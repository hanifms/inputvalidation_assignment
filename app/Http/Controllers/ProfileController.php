<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'nickname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        auth()->user()->update([
            'name' => $request->name,
            'nickname' => $request->nickname,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
        ]);

        return back()->with('status', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // 2MB Max
        ]);

        $user = auth()->user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store the new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update([
            'avatar' => $avatarPath
        ]);

        return back()->with('status', 'Avatar updated successfully.');
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

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();

        // Delete avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Account deleted successfully.');
    }
}
