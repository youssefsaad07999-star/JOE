<?php

namespace App\Http\Controllers;

use App\Models\ProductModels\Category;
use App\Models\ProductModels\Product;

class ProductController extends Controller
{
    public function Index()
    {
        //
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
