<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        return view('admin.tags.index', [
            'tags' => Tag::query()
                ->withCount('products')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
                })
                ->orderBy('name')
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug'],
        ]);

        Tag::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name,'.$tag->id],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug,'.$tag->id],
        ]);

        $tag->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->products()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully.');
    }
}
