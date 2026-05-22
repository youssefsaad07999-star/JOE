<?php

namespace App\Http\Controllers\ProductControllers;

use App\Http\Controllers\Controller;
use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;

class CategoryController extends Controller
{
    public function show(Category $category, string $gender)
    {

        if ($category->parent?->slug !== $gender) {
            abort(404);
        }
        $subcategories = $category->children()->get();
        $products = Product::whereHas('category',
            fn ($q) => $q->where('parent_id', $category->id)
        )->get();

        return view('product.category.show', compact('gender', 'category', 'subcategories', 'products'));
    }
}
