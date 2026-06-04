<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingAdminController extends Controller
{
    public function index()
    {
        $shippingMethods = ShippingMethod::all();

        return view('admin.shipping.index', compact('shippingMethods'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:shipping_methods,name',
            'delivery_time' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'required|integer|min:0',
        ]);

        ShippingMethod::create($data);

        return back()->with('success', "Shipping method \"{$data['name']}\" created.");

    }

    public function update(ShippingMethod $shipping, Request $request)
    {
        if ($request->boolean('toggle_active')) {

            $shipping->update(['is_active' => ! $shipping->is_active]);

            return back()->with('success', 'Shipping method status updated.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:shipping_methods,name,'.$shipping->id,
            'delivery_time' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $shipping->update($data);

        return back()->with('success', 'Shipping method updated!.');
    }

    public function destroy(ShippingMethod $shipping)
    {
        $shipping->delete();

        return back()->with('success', 'Shipping method deleted!.');
    }
}
