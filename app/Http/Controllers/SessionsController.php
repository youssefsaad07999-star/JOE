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

        $oldSessionId = $request->session()->getId();

        session(['guest_session_id' => $oldSessionId]);

        $remember = $request->has('remember');
    
        if (! Auth::attempt($attributes, $remember)) {
            session()->forget('guest_session_id');

            return back()->withErrors([
                'password' => 'Invalid credentials',
            ]
            )->withInput();
        }

        $request->session()->regenerate();

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
