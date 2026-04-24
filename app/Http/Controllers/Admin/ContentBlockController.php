<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentBlockController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());

        $defaults = [
            'home_banner' => 'Homepage banner',
            'promotional_section' => 'Promotional section',
            'featured_collections' => 'Featured collections',
            'about_page_content' => 'About page content',
            'contact_details' => 'Contact details',
        ];

        foreach ($defaults as $key => $title) {
            ContentBlock::firstOrCreate(['key' => $key], ['title' => $title, 'is_active' => true]);
        }

        return view('admin.content.index', [
            'blocks' => ContentBlock::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('key', 'like', "%{$search}%")
                            ->orWhere('content', 'like', "%{$search}%");
                    });
                })
                ->orderBy('title')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function edit(ContentBlock $content): View
    {
        return view('admin.content.edit', ['block' => $content]);
    }

    public function update(Request $request, ContentBlock $content): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $content->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'meta' => $validated['meta'] ? ['notes' => $validated['meta']] : null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Content block updated successfully.');
    }
}
