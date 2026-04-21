<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['categoryRelation', 'tags', 'images'])
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create($this->productPayload($validated));
            $this->syncProductRelations($product, $request);
        });

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load(['categoryRelation', 'tags', 'images', 'specifications']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($product, $request, $validated) {
            $product->update($this->productPayload($validated, $product));
            $this->syncProductRelations($product, $request);
        });

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
            Storage::disk('public')->delete($product->image_url);
        }

        foreach ($product->images as $image) {
            if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function destroyImage(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        return redirect()->route('admin.products.edit', $product)->with('success', 'Gallery image removed.');
    }

    protected function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.($product?->id ?? 'NULL').',id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'size' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'category_label' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'is_new' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:4096'],
            'colors' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
        ]);
    }

    protected function productPayload(array $validated, ?Product $product = null): array
    {
        $category = null;

        if (!empty($validated['category_id'])) {
            $category = Category::find($validated['category_id']);
        }

        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'discount' => $validated['discount'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'size' => $validated['size'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'category' => $category?->name ?? ($validated['category_label'] ?? $product?->category ?? 'General'),
            'is_new' => (bool) ($validated['is_new'] ?? false),
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
        ];
    }

    protected function syncProductRelations(Product $product, Request $request): void
    {
        if ($request->hasFile('image')) {
            if ($product->image_url && Storage::disk('public')->exists($product->image_url)) {
                Storage::disk('public')->delete($product->image_url);
            }

            $product->update([
                'image_url' => $request->file('image')->store('products', 'public'),
            ]);
        }

        if ($request->filled('tags')) {
            $product->tags()->sync($request->input('tags', []));
        } else {
            $product->tags()->sync([]);
        }

        if ($request->hasFile('gallery_images')) {
            $sortOrder = (int) $product->images()->max('sort_order');

            foreach ($request->file('gallery_images') as $index => $imageFile) {
                $product->images()->create([
                    'image_url' => $imageFile->store('products/gallery', 'public'),
                    'sort_order' => $sortOrder + $index + 1,
                ]);
            }
        }

        $product->specifications()->delete();

        $specifications = [];

        if ($request->filled('colors')) {
            $specifications[] = ['label' => 'Color', 'value' => $request->string('colors')->toString()];
        }

        if ($request->filled('materials')) {
            $specifications[] = ['label' => 'Material', 'value' => $request->string('materials')->toString()];
        }

        foreach (preg_split('/\r\n|\r|\n/', (string) $request->input('specifications')) as $line) {
            $line = trim($line);

            if ($line === '' || !str_contains($line, ':')) {
                continue;
            }

            [$label, $value] = array_map('trim', explode(':', $line, 2));

            if ($label !== '' && $value !== '') {
                $specifications[] = ['label' => $label, 'value' => $value];
            }
        }

        foreach ($specifications as $specification) {
            ProductSpecification::create([
                'product_id' => $product->id,
                'label' => $specification['label'],
                'value' => $specification['value'],
            ]);
        }
    }
}
