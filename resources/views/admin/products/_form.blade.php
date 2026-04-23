@php
    $colorSpec = $product->specifications->firstWhere('label', 'Color')?->value ?? '';
    $materialSpec = $product->specifications->firstWhere('label', 'Material')?->value ?? '';
    $customSpecifications = $product->specifications
        ->reject(fn ($spec) => in_array($spec->label, ['Color', 'Material']))
        ->map(fn ($spec) => "{$spec->label}: {$spec->value}")
        ->implode(PHP_EOL);
@endphp

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="title">Product title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $product->title) }}" required>
    </div>
    <div class="admin-field">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug) }}">
    </div>
    <div class="admin-field">
        <label for="price">Price</label>
        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
    </div>
    <div class="admin-field">
        <label for="sale_price">Discount price</label>
        <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price) }}">
    </div>
    <div class="admin-field">
        <label for="discount">Discount percentage</label>
        <input id="discount" name="discount" type="number" min="0" max="100" value="{{ old('discount', $product->discount) }}">
    </div>
    <div class="admin-field">
        <label for="stock_quantity">Stock quantity</label>
        <input id="stock_quantity" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
    </div>
    <div class="admin-field">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id">
            <option value="">Select a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="admin-field">
        <label for="category_label">Fallback category label</label>
        <input id="category_label" name="category_label" type="text" value="{{ old('category_label', $product->category) }}" placeholder="Used if no category is selected">
    </div>
    <div class="admin-field">
        <label for="size">Size</label>
        <input id="size" name="size" type="text" value="{{ old('size', $product->size) }}" placeholder="small, medium, large">
    </div>
    <div class="admin-field">
        <label for="tags">Tags</label>
        <select id="tags" name="tags[]" multiple size="5">
            @foreach ($tags as $tag)
                <option value="{{ $tag->id }}" @selected(collect(old('tags', $product->tags->pluck('id')->all()))->contains($tag->id))>{{ $tag->name }}</option>
            @endforeach
        </select>
        <div class="helper-text">Hold Ctrl or Command to select multiple tags.</div>
    </div>
    <div class="admin-field">
        <label for="image">Primary image</label>
        <div class="admin-file-field">
            <input id="image" name="image" type="file" accept="image/*" class="admin-file-input">
            <label for="image" class="admin-file-label">
                <span class="admin-file-title">Choose primary image</span>
                <span class="admin-file-meta">PNG, JPG, WEBP</span>
            </label>
        </div>
    </div>
    <div class="admin-field">
        <label for="gallery_images">Gallery images</label>
        <div class="admin-file-field">
            <input id="gallery_images" name="gallery_images[]" type="file" accept="image/*" multiple class="admin-file-input">
            <label for="gallery_images" class="admin-file-label">
                <span class="admin-file-title">Choose gallery images</span>
                <span class="admin-file-meta">Multiple files allowed</span>
            </label>
        </div>
    </div>
    <div class="admin-field">
        <label><input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new))> Mark as new</label>
    </div>
    <div class="admin-field">
        <label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Mark as featured</label>
    </div>
</div>

<div class="admin-field">
    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
</div>

<div class="admin-form-grid">
    <div class="admin-field">
        <label for="colors">Colors</label>
        <input id="colors" name="colors" type="text" value="{{ old('colors', $colorSpec) }}" placeholder="Walnut, Stone, Olive">
    </div>
    <div class="admin-field">
        <label for="materials">Materials</label>
        <input id="materials" name="materials" type="text" value="{{ old('materials', $materialSpec) }}" placeholder="Oak frame, boucle fabric">
    </div>
</div>

<div class="admin-field">
    <label for="specifications">Custom specifications</label>
    <textarea id="specifications" name="specifications" placeholder="Dimensions: 80 x 90 x 70 cm&#10;Warranty: 2 years">{{ old('specifications', $customSpecifications) }}</textarea>
    <div class="helper-text">One specification per line using the format `Label: Value`.</div>
</div>

@if ($product->exists)
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Existing media</h3>
        </div>
        <div class="thumb-grid">
            <div>
                <img class="thumb" src="{{ $product->display_image_url }}" alt="{{ $product->title }}">
                <div class="helper-text">Primary image</div>
            </div>
            @foreach ($product->images as $image)
                <div>
                    <img class="thumb" src="{{ $image->resolved_url }}" alt="">
                    <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-link">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="admin-actions">
    <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to products</a>
</div>
