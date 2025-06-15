<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        authenticated as protected baseAuthenticated;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     * Override the method from AuthenticatesUsers trait
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user has 2FA enabled (indicated by non-null two_factor_code value)
        if ($user->two_factor_code === 'ENABLED') {
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

        // If no 2FA, use the standard redirect
        return $this->baseAuthenticated($request, $user);
    }
}
