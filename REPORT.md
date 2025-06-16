# Laravel Authentication Enhancement Report

This report documents the enhancements made to the Laravel authentication module of our Todo application.

## 1. Strong Password Hashing

- Laravel's default Bcrypt algorithm is being used for password hashing
- Configuration set in `config/hashing.php` with `bcrypt` as the default driver
- Automatic salting is handled internally by Laravel's hashing mechanism

## 2. Multi-Factor Authentication (MFA)

### 2.1 Email-Based MFA Implementation

Created a custom email Mailable for sending 2FA codes:
```php
// app/Mail/TwoFactorAuthMail.php
class TwoFactorAuthMail extends Mailable
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Two-Factor Authentication Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.2fa-code',
        );
    }
}
```

### 2.2 Custom Login Response

```php
// app/Http/Responses/LoginResponse.php
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
    Mail::to($user->email)->send(new TwoFactorAuthMail($code));

    // 4. Log the user out and redirect to verification page
    Auth::logout();
    $request->session()->put('login.id', $user->id);
    return redirect()->route('2fa.challenge');
}
```

### 2.3 Two-Factor Challenge Controller

```php
// app/Http/Controllers/Auth/TwoFactorChallengeController.php
public function store(Request $request)
{
    $request->validate(['code' => 'required|string']);

    $userId = $request->session()->get('login.id');
    $user = User::find($userId);

    if (!$user || $user->two_factor_code !== $request->code || 
        $user->two_factor_expires_at->isPast()) {
        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

    // Clear the 2FA data and log the user in
    $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
    Auth::login($user);
    $request->session()->forget('login.id');

    return redirect()->intended(config('fortify.home'));
}
```

### 2.4 Two-Factor Management

```php
// app/Http/Controllers/TwoFactorController.php
public function enableTwoFactor(Request $request)
{
    $user = $request->user();

    // Validate password if needed
    if (Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')) {
        $request->validate(['password' => ['required', 'string', 'current_password']]);
    }

    // Set placeholder values to indicate 2FA is enabled
    $user->forceFill([
        'two_factor_code' => 'ENABLED',
        'two_factor_expires_at' => now()->addYears(10),
    ])->save();

    return back()->with('status', 'Two-factor authentication enabled successfully.');
}
```

## 3. Rate Limiting for Login Attempts

Implemented in `FortifyServiceProvider.php`:
```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip())->response(function ($request, $headers) {
        return response('Too many login attempts. Please try again in a minute.', 429)
            ->withHeaders($headers);
    });
});
```

## 3. User Profile Management System

This section details the comprehensive user profile management system implemented in the application.

### 3.1 Enhanced User Model

The User model has been extended with additional fields to support rich profile information:

```php
// database/migrations/2025_06_16_000000_add_profile_fields_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('nickname')->nullable()->after('name');
    $table->string('avatar')->nullable()->after('nickname');
    $table->string('phone')->nullable()->after('email');
    $table->string('city')->nullable()->after('phone');
});
```

### 3.2 Profile Management Implementation

#### 3.2.1 Profile Controller Overview

```php
// app/Http/Controllers/ProfileController.php
class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        auth()->user()->update($request->only(['name', 'nickname', 'email', 'phone', 'city']));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'max:2048']]);
        
        $user = auth()->user();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);
        
        $user = auth()->user();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        auth()->logout();
        $user->delete();
    }
}
```

### 3.3 Profile View Implementation

The profile view (`resources/views/profile/show.blade.php`) implements several key features:

#### 3.3.1 Avatar Management
- Displays current avatar or fallback to name initial
- Supports file upload with size validation
- Automatically removes old avatar when updating

#### 3.3.2 Profile Information Form
- Fields: name, nickname, email, phone, city
- Real-time validation feedback
- Success/error message handling

#### 3.3.3 Security Features
- Password confirmation for sensitive operations
- CSRF protection on all forms
- File upload validation and sanitization

### 3.4 Data Storage and Security

#### 3.4.1 Avatar Storage
- Stored in `storage/app/public/avatars/`
- Public disk configuration for accessibility
- Automatic cleanup of old files

#### 3.4.2 Form Validation Rules
```php
$rules = [
    'name' => ['required', 'string', 'max:255'],
    'nickname' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
    'phone' => ['nullable', 'string', 'max:20'],
    'city' => ['nullable', 'string', 'max:100'],
    'avatar' => ['required', 'image', 'max:2048'], // 2MB limit
];
```

### 3.5 User Interface Integration

The user interface has been enhanced to display profile information in strategic locations:

#### 3.5.1 Navigation Bar Integration
```blade
<!-- resources/views/layouts/app.blade.php -->
<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button">
    @if(Auth::user()->avatar)
        <img src="{{ Storage::url(Auth::user()->avatar) }}" 
             alt="Profile" 
             class="rounded-circle me-1" 
             width="24" height="24">
    @endif
    {{ Auth::user()->nickname ?? Auth::user()->name }}
</a>
```

### 3.6 Account Management

#### 3.6.1 Account Deletion Process
1. Password confirmation required
2. Cleanup of associated files (avatar)
3. Session invalidation
4. Database record removal

### 3.7 Security Considerations

- All forms protected with CSRF tokens
- File upload validation and sanitization
- Password confirmation for sensitive operations
- Proper file storage permissions
- Input validation and sanitization
- Unique email constraints
- Secure password handling

## 4. Input Validation for Registration and Login Pages

Form Request classes are used to implement robust input validation for registration and login forms, enhancing security and user experience.

### 4.1 Form Request Implementation

#### 4.1.1 Registration Form Validation

```php
// app/Http/Requests/RegisterRequest.php
class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Open to public
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'nickname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_\-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Name can only contain letters and spaces.',
            'nickname.regex' => 'Nickname can only contain letters, numbers, underscores, and dashes.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }
}
```

#### 4.1.2 Login Form Validation

```php
// app/Http/Requests/LoginRequest.php
class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Anyone can attempt to login
    }

    public function rules()
    {
        return [
            'email' => [
                'required', 'string', 'email', 'max:255', 
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => 'Please enter a valid email address.',
        ];
    }
}
```

#### 4.1.3 Two-Factor Authentication Validation

```php
// app/Http/Requests/TwoFactorChallengeRequest.php
class TwoFactorChallengeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Anyone with session ID can attempt 2FA
    }

    public function rules()
    {
        return [
            'code' => ['required', 'string', 'regex:/^\d{6}$/'], // Exactly 6 digits
        ];
    }

    public function messages()
    {
        return [
            'code.regex' => 'The verification code must be exactly 6 digits.',
        ];
    }
}
```

### 4.2 Controller Integration

Form Request classes are integrated with controllers to provide validation:

#### 4.2.1 Login Controller

```php
// app/Http/Controllers/Auth/LoginController.php (excerpt)
public function login(LoginRequest $request)
{
    // Request is already validated by the LoginRequest class
    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');
    
    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return $this->sendLoginResponse($request);
    }

    return $this->sendFailedLoginResponse($request);
}
```

#### 4.2.2 Two-Factor Challenge Controller

```php
// app/Http/Controllers/Auth/TwoFactorChallengeController.php (excerpt)
public function store(TwoFactorChallengeRequest $request)
{
    // Request is already validated by TwoFactorChallengeRequest
    $userId = $request->session()->get('login.id');
    $user = User::find($userId);

    if (!$user || $user->two_factor_code !== $request->code || 
        $user->two_factor_expires_at->isPast()) {
        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

    // Process valid 2FA code...
}
```

### 4.3 Security Benefits

1. **Input Whitelisting**: Using regex patterns ensures only permitted character formats can be submitted
2. **Data Integrity**: Strict validation ensures database consistency
3. **User Experience**: Clear, specific error messages help users correct their input
4. **Security**: Prevents potential injection attacks through input validation
5. **Maintainability**: Separating validation logic from controllers creates cleaner, more focused code

### 4.4 Validation Strategy

- **Whitelist Approach**: Only accepting known good input patterns
- **Strict Type Checking**: Enforcing data types for all inputs
- **Custom Error Messages**: Providing helpful feedback to users
- **Dedicated Classes**: Separating validation logic from business logic

This approach to input validation improves both security and user experience while maintaining clean, maintainable code architecture by keeping controllers focused on their primary responsibilities.

## TL;DR

This Laravel Todo app features email-based two-factor authentication using Laravel Fortify. When users with 2FA enabled log in, they receive a time-limited verification code by email. The system includes Bcrypt password hashing, rate limiting (3 attempts/minute), and a simple user interface for enabling/disabling 2FA from the profile page. Additionally, a comprehensive user profile management system allows users to update their profile information and avatar, with strict validation and security measures in place.
