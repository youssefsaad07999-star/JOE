<?php

namespace App\Http\Controllers;

use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;

class ProductController extends Controller
{
    public function home()
    {
        $genders = Category::genders()->active()->orderBy('sort_order')->get();

        return view('welcome', compact('genders'));
    }

    public function genderIndex(Category $gender)
    {
        // Direct category children (depth = 'category')
        $categories = $gender->children()->with('children')->get();

        // All products sitting under this gender
        $products = Product::with(['variants', 'images', 'primaryImage'])
            ->isActive()
            ->whereHas('category.parent.parent', fn ($q) => $q->where('id', $gender->id))
            ->with('category.parent.parent')
            ->latest()
            ->paginate(16);

        return view('product.gender.index', compact('gender', 'categories', 'products'));
    }

    public function categoryShow(Category $gender, Category $category)
    {
        $subcategories = $category->children()->get();

        $products = Product::with(['variants.color', 'variants.size', 'images', 'primaryImage'])
            ->isActive()
            ->whereHas('category', fn ($q) => $q->where('parent_id', $category->id))
            ->latest()
            ->paginate(16);

        return view('product.category.show', compact('gender', 'category', 'subcategories', 'products'));

    }

    public function subcategoryShow(Category $gender, Category $category, Category $subcategory)
    {
        $subcategories = $category->children()->get();

        $products = Product::with(['variants.color', 'variants.size', 'images', 'primaryImage'])
            ->isActive()
            ->where('category_id', $subcategory->id)
            ->latest()
            ->paginate(16);

        return view('product.subcategory.show', compact('gender', 'category', 'products', 'subcategory', 'subcategories'));
    }

    public function productShow(Category $gender, Category $category, Category $subcategory, Product $product)
    {
        return view('product.show', compact('gender', 'category', 'subcategory', 'product'));
    }

    public function byCategory(Category $category)
    {

        $products = Product::inCategory($category)
            ->with('variants')
            ->get();

        // dd($products[0]->variants[0]->sku);

        // return view('products.index', compact('products', 'category'));
    }
}
