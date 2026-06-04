<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisteredUserRequest;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
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

        $oldSessionId = $request->session()->getId();

        session(['guest_session_id' => $oldSessionId]);

        Auth::login($user);

        event(new Registered($user));

        // $user->notify(new WelcomeNotification($user));
        return redirect()->route('verification.notice');
        // return redirect('/')->with('success', 'Your account has been created!');
    }
}
