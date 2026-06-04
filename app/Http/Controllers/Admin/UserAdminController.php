<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withSum(['orders' => function ($query) {
            $query->where('status', '!=', 'cancelled');
        }], 'total_price')
            ->when(
                $request->search, fn ($q, $s) => $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$s}%"])
                    ->orWhere('email', 'like', "%{$s}%")
            )
            ->paginate(6);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $orders = $user->orders()->latest()->paginate(6);

        return view('admin.users.show', compact('user', 'orders'));
    }

    public function updateRole(User $user, Request $request)
    {
        $validated = $request->validate(
            ['role' => ['required', Rule::in(['admin', 'customer'])]]
        );

        $user->update($validated);

        return back()->with('success', 'User role changed successfully!');
    }
}
