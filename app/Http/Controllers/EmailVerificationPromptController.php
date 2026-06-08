<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request)
    {
        // 💡 THE FIX: If the user is already verified, bounce them to the dashboard!
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : view('auth.verify-email');
    }

    public function sendEmailVerificaitonLink(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect(route('home'))
                ->with('success', 'You already verified your email');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
