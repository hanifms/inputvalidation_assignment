<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Facades\Mail;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // The user is already authenticated here by Fortify
        $user = Auth::user();

        // 1. Generate a 6-digit code
        $code = rand(100000, 999999);

        // 2. Save code and expiry to the user
        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        // 3. Send the code via email
        try {
             Mail::to($user->email)->send(new TwoFactorAuthMail($code));
        } catch (\Exception $e) {
            // Handle mail sending failure if necessary
        }

        // 4. Log the user out
        Auth::logout();

        // 5. Store user's ID in session to identify them on the verification page
        $request->session()->put('login.id', $user->id);

        // 6. Redirect to the 2FA verification page
        return redirect()->route('2fa.challenge');
    }
}
