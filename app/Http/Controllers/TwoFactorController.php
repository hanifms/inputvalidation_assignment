<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();

        // Validate password if needed
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $request->validate([
                'password' => ['required', 'string', 'current_password'],
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

        // Validate password if needed
        if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
            $request->validate([
                'password' => ['required', 'string', 'current_password'],
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
