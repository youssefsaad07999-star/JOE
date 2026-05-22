<?php

namespace App\Http\Controllers\ProductControllers;

use App\Http\Controllers\Controller;
use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;

class SubcategoryController extends Controller
{
    public function show(Category $category, Category $subcategory, string $gender)
    {
        if (
            $category->parent?->slug !== $gender ||
            $subcategory->parent?->id !== $category->id ||
            $subcategory->parent?->parent?->slug !== $gender
        ) {
            abort(404);
        }

        $subcategories = $category->children()->get();

        $products = Product::whereHas('category',
            fn ($q) => $q->where('id', $subcategory->id)
        )->get();

        return view('product.subcategory.show', compact('gender', 'category', 'products', 'subcategories'));
    }
}
