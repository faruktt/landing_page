<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', ['category' => new Category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $category = new Category;
        $this->applyData($category, $data, $request);
        $category->save();

        return redirect()->route('admin.categories.index')->with('status', 'ক্যাটাগরি সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validateData($request, $category->id);
        $this->applyData($category, $data, $request);
        $category->save();

        return redirect()->route('admin.categories.index')->with('status', 'ক্যাটাগরি সফলভাবে আপডেট হয়েছে।');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'ক্যাটাগরি মুছে ফেলা হয়েছে।');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'.($ignoreId ? ",$ignoreId" : '')],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function applyData(Category $category, array $data, Request $request): void
    {
        $category->name = $data['name'];
        $category->slug = ($data['slug'] ?? null) ?: $this->uniqueSlug($data['name'], $category->id);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $category->image = $request->file('image')->store('categories', 'public');
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'category-'.Str::random(6);
        $original = $slug;
        $i = 2;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
