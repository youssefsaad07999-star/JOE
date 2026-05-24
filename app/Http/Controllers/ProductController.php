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
        $products = Product::with(['variants', 'images'])
            ->whereHas('category.parent.parent', fn ($q) => $q->where('id', $gender->id)
            )->with('category.parent.parent')
            ->get();

        return view('product.gender.index', compact('gender', 'categories', 'products'));
    }

    public function categoryShow(Category $gender, Category $category)
    {
        $subcategories = $category->children()->get();

        $products = Product::with(['variants.color', 'variants.size', 'images'])
            ->whereHas('category', fn ($q) => $q->where('parent_id', $category->id)
            )->get();

        return view('product.category.show', compact('gender', 'category', 'subcategories', 'products'));

    }

    public function subcategoryShow(Category $gender, Category $category, Category $subcategory)
    {
        // if (
        //     $category->parent?->slug !== $gender->slug ||
        //     $subcategory->parent?->id !== $category->id ||
        //     $subcategory->parent?->parent?->slug !== $gender->slug
        // ) {
        //     abort(404);
        // }

        $subcategories = $category->children()->get();

        $products = Product::with(['variants.color', 'variants.size', 'images'])
            ->where('category_id', $subcategory->id)
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
