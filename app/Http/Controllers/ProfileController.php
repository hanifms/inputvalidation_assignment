<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Features;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        auth()->user()->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated successfully.');
    }

    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
            ]);
        }

        // We don't need any special flags now - when the user logs in,
        // our LoginResponse will generate codes regardless
        // Just indicate that 2FA is enabled by setting temporary code values
        $user->forceFill([
            'two_factor_code' => 'ENABLED',
            'two_factor_expires_at' => now()->addYears(10), // Just a placeholder
        ])->save();

        return back()->with('status', 'Two-factor authentication enabled successfully.');
    }

    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();

        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
            ]);
        }

        // Clear 2FA data
        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        return back()->with('status', 'Two-factor authentication disabled successfully.');
    }
}
