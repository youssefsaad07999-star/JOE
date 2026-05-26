<?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->with('variants.product')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show()
    {
        //
    }
}
