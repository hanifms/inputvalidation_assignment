<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Todo App with Enhanced Authentication

This Laravel application demonstrates a secure Todo application with advanced authentication features including Multi-Factor Authentication (MFA), strong password hashing, rate limiting, and password salting.

## Authentication Features

### 1. Two-Factor Authentication (2FA)
Email-based two-factor authentication is implemented using Laravel Fortify:
- When a user with 2FA enabled logs in, a 6-digit verification code is sent to their email
- The code is valid for 10 minutes and is required to complete the login process
- Users can enable/disable 2FA from their profile page

### 2. Secure Password Hashing
- Password hashing uses Laravel's built-in Bcrypt algorithm, ensuring strong encryption
- Laravel automatically selects secure default settings for the hashing algorithm

### 3. Rate Limiting
- Implemented using Laravel RateLimiter
- Login attempts are limited to 3 failed attempts per minute per IP address
- Two-factor authentication verification is limited to 5 attempts per minute per user

## Authentication Flow

### Registration Process
1. User registers with name, email, and password
2. Password is securely hashed using Bcrypt
3. User is created in the database
4. User is automatically logged in

### Enabling 2FA
1. User navigates to their profile page
2. Under the "Two Factor Authentication" section, user clicks "Enable Two-Factor"
3. If configured, user must confirm their password
4. 2FA is enabled for the account

### Login with 2FA
1. User enters email and password on login page
2. If credentials are correct and 2FA is enabled:
   - A random 6-digit code is generated
   - Code is stored in the user record with an expiration time
   - Code is sent to the user's email address
   - User is redirected to the 2FA verification page
3. User checks their email and enters the code
4. If the code is correct and hasn't expired, the user is logged in
5. The code is cleared from the database after successful verification

## Code Structure Overview

### Key Components

#### 1. Custom Login Response
```php
// app/Http/Responses/LoginResponse.php
public function toResponse($request)
{
    $user = Auth::user();
    
    // Generate a 6-digit code
    $code = rand(100000, 999999);
    
    // Save code and expiry to the user
    $user->update([
        'two_factor_code' => $code,
        'two_factor_expires_at' => now()->addMinutes(10),
    ]);
    
    // Send the code via email
    Mail::to($user->email)->send(new TwoFactorAuthMail($code));
    
    // Log the user out
    Auth::logout();
    
    // Store user's ID in session
    $request->session()->put('login.id', $user->id);
    
    // Redirect to the 2FA verification page
    return redirect()->route('2fa.challenge');
}
```

#### 2. Two Factor Challenge Controller
```php
// app/Http/Controllers/Auth/TwoFactorChallengeController.php
public function store(Request $request)
{
    $request->validate([
        'code' => 'required|string',
    ]);

    $userId = $request->session()->get('login.id');
    $user = User::find($userId);

    if (!$user || $user->two_factor_code !== $request->code || $user->two_factor_expires_at->isPast()) {
        return back()->withErrors(['code' => 'The code is invalid or has expired.']);
    }

    // Clear the 2FA data
    $user->update([
        'two_factor_code' => null,
        'two_factor_expires_at' => null,
    ]);

    // Log the user in
    Auth::login($user);
    $request->session()->forget('login.id');

    return redirect()->intended(config('fortify.home'));
}
```

#### 3. Rate Limiting Configuration
```php
// app/Providers/FortifyServiceProvider.php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip())->response(function (Request $request, array $headers) {
        return response('Too many login attempts. Please try again in a minute.', 429)
            ->withHeaders($headers);
    });
});

RateLimiter::for('two-factor', function (Request $request) {
    return Limit::perMinute(5)->by($request->session()->get('login.id'));
});
```

#### 4. User Model & Database Structure
```php
// app/Models/User.php
protected $fillable = [
    'name',
    'email',
    'password',
    'two_factor_code',
    'two_factor_expires_at',
];

// Database migration added 2FA columns
// database/migrations/2025_06_15_171141_replace_two_factor_auth_columns.php
$table->string('two_factor_code')->nullable();
$table->dateTime('two_factor_expires_at')->nullable();
```

## TL;DR

This Laravel Todo app implements email-based two-factor authentication using Laravel Fortify. When a user with 2FA enabled attempts to log in, they must enter a time-limited verification code sent to their email. The system includes strong password hashing with Bcrypt, rate limiting to prevent brute force attacks (3 attempts/minute), and automatic password salting. Users can easily enable or disable 2FA from their profile page.
