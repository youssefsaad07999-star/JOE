<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {

        $attributes = $request->validate([
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        if (! Auth::attempt($attributes)) {
            return back()->withErrors([
                'password' => 'Invalid credentials',
            ]
            )->withInput();
        }

        $request->session()->regenerate();
        // generate session

        return redirect()->intended('/')->with('success', 'You are now logged in!');
    }

    public function destroy()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/')->with('success', 'You are now logged out!');
    }
}
