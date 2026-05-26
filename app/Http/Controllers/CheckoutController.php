<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\ProductModels\CartItem;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartTotal = 0;

        $cartItems = CartItem::forUser(auth()->id())
            ->get();

        foreach ($cartItems as $cartItem) {
            $cartTotal += $cartItem->line_total;
        }

        $shipping_methods = ShippingMethod::all();

        $countries = Country::where('is_active', true)->get();

        $free_shipping_threshold = ShopSetting::get('free_shipping_threshold');

        return view('checkout.index', compact(
            'cartItems',
            'cartTotal',
            'shipping_methods',
            'countries',
            'free_shipping_threshold'
        ));
    }

    public function store()
    {
        //
    }
}
