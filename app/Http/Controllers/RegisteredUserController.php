<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisteredUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisteredUserRequest $request)
    {
        // dd($request->validated());
        $user = User::create($request->validated());

        Auth::login($user);

        return redirect('/')->with('success', 'Your account has been created!');
    }
}
