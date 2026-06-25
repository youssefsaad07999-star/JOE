<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->with(['variants.product.images', 'payment'])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize($order);

        return view('orders.show', compact('order'));
    }

    public function authorize($order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }
    }
}
