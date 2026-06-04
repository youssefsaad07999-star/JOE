<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModels\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $genders = Category::genders()
            ->with([
                'children' => fn ($q) => $q->withCount('products')->with([
                    'children' => fn ($q) => $q->withCount('products'),
                ]),
            ])
            ->orderBy('sort_order')
            ->get();

        // For the modal parent selector
        $allCategories = Category::select(['id', 'name', 'depth', 'parent_id'])
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('genders', 'allCategories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'depth' => 'required|in:gender,category,subcategory',
            'sort_order' => 'integer|min:0',
        ]);

        // Enforce unique slug per parent
        $slug = $data['slug'] ?? Str::slug($data['name']);

        $exists = Category::where('slug', $slug)
            ->where('parent_id', $data['parent_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A category with this slug already exists under this parent.']);
        }

        Category::create([
            'name' => ucfirst($data['name']),
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?: null,
            'depth' => $data['depth'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', "Category \"{$data['name']}\" created.");
    }

    public function update(Request $request, Category $adminCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $adminCategory->update([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
            'sort_order' => $data['sort_order'] ?? $adminCategory->sort_order,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function toggle(Category $adminCategory)
    {
        $adminCategory->update(['is_active' => ! $adminCategory->is_active]);

        // Hide children too when hiding a parent
        if (! $adminCategory->is_active) {
            Category::where('parent_id', $adminCategory->id)
                ->update(['is_active' => false]);
        }

        return back()->with('success',
            $adminCategory->is_active
                ? "Category \"{$adminCategory->name}\" is now visible."
                : "Category \"{$adminCategory->name}\" is now hidden."
        );
    }

    public function destroy(Category $adminCategory)
    {
        // Block deletion if children or products exist
        if ($adminCategory->children()->exists()) {
            return back()->with('error',
                "Cannot delete \"{$adminCategory->name}\" — remove its subcategories first.");
        }

        if ($adminCategory->products()->exists()) {
            return back()->with('error',
                "Cannot delete \"{$adminCategory->name}\" — it has products assigned to it.");
        }

        $name = $adminCategory->name;
        $adminCategory->delete();

        return back()->with('success', "\"{$name}\" deleted.");
    }
}
