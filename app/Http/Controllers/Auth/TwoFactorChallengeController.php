<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the two-factor authentication challenge view.
     */
    public function create(Request $request)
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = $request->session()->get('login.id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Your session has expired. Please try logging in again.']);
        }

        $user = User::find($userId);

        if (!$user || $user->two_factor_code !== $request->code || $user->two_factor_expires_at->isPast()) {
            return back()->withErrors(['code' => 'The code you provided is invalid or has expired.']);
        }

        // Clear the 2FA data
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        // Log the user in
        Auth::login($user);

        // Forget the session key
        $request->session()->forget('login.id');

        // Redirect to the intended dashboard
        return redirect()->intended(config('fortify.home'));
    }
}
