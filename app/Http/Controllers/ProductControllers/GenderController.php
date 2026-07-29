<?php

namespace App\Http\Controllers\ProductControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShopSetting;

class GenderController extends Controller
{
    public function index()
    {
        $genders = Category::where('parent_id', null)
            ->get();

        $free_shipping_threshold = ShopSetting::get('free_shipping_threshold');

        return view('welcome', compact('genders', 'free_shipping_threshold'));
    }

    public function show(string $gender)
    {
        $genders = Category::where('parent_id', null)
            ->with('children')
            ->get();

        $currentGender = $genders->where('slug', $gender)->first();

        $categories = $currentGender->children;

        $products = Product::whereHas('category.parent.parent', function ($q) use ($gender) {
            $q->where('slug', $gender);
        })
            // ->latest()
            // ->take(12)
            ->get();

        return view('product.gender.index', compact('currentGender', 'categories', 'products'));
    }
}
