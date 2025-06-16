# Todo App with Enhanced User Profile Management

This Laravel-based Todo application features comprehensive user profile management capabilities.

## Profile Management Features

### User Profile Fields
- **Nickname**: Editable display name shown in the top-right menu
- **Avatar**: Uploadable profile picture (max 2MB, stored in `storage/app/public/avatars/`)
- **Email**: Editable email address with unique validation
- **Password**: Secure password change functionality
- **Phone**: Optional contact number
- **City**: Optional location information

### Implementation Details

#### Key Files
- `database/migrations/2025_06_16_000000_add_profile_fields_to_users_table.php`: Profile fields schema
- `app/Models/User.php`: User model with fillable fields
- `app/Http/Controllers/ProfileController.php`: Profile management logic
- `resources/views/profile/show.blade.php`: Profile edit interface

#### Example Code Snippets

User Model Fields:
```php
protected $fillable = [
    'name',
    'nickname',
    'avatar',
    'email',
    'password',
    'phone',
    'city'
];
```

Avatar Upload Method:
```php
public function updateAvatar(Request $request)
{
    $request->validate(['avatar' => 'required|image|max:2048']);
    $avatarPath = $request->file('avatar')->store('avatars', 'public');
    auth()->user()->update(['avatar' => $avatarPath]);
}
```

### Directory Structure
```
app/
├── Http/Controllers/
│   └── ProfileController.php     # Profile management
├── Models/
│   └── User.php                  # User model
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php         # Main layout with user menu
    └── profile/
        └── show.blade.php        # Profile edit form
```

## TL;DR
Full user profile management system with editable nickname, avatar upload, contact details, and account deletion. Uses Laravel's built-in authentication with enhanced profile features.


## Input Validation for Registration Login Pages

The application uses Form Request classes to validate user input, implementing Laravel regex patterns as whitelists for required inputs.

### Form Request Classes
- `app/Http/Requests/RegisterRequest.php`: Validates registration data with strict regex patterns
- `app/Http/Requests/LoginRequest.php`: Validates login credentials
- `app/Http/Requests/TwoFactorChallengeRequest.php`: Validates 2FA verification codes

### Implementation
The validation is implemented in Form Request classes that are used by the Auth controllers:

```php
// app/Http/Requests/RegisterRequest.php
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

// app/Http/Requests/LoginRequest.php
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
```

### Controller Integration
The Form Requests are used in the controllers to handle validation:

```php
// app/Http/Controllers/Auth/LoginController.php
public function login(LoginRequest $request)
{
    // Request is already validated by LoginRequest
    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');
    
    if (Auth::attempt($credentials, $remember)) {
        // Authentication successful
    }
}

// app/Http/Controllers/Auth/RegisterController.php
public function register(\Illuminate\Http\Request $request)
{
    // Validation using rules from RegisterRequest
    $validator = \Illuminate\Support\Facades\Validator::make(
        $request->all(), 
        (new \App\Http\Requests\RegisterRequest)->rules(),
        (new \App\Http\Requests\RegisterRequest)->messages()
    );
    
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
    
    // Create user if validation passes
}
```

### Key Features
- **Name**: Only allows letters and spaces (`regex:/^[A-Za-z\s]+$/`)
- **Nickname**: Only allows letters, numbers, underscores, and dashes (`regex:/^[A-Za-z0-9_\-]+$/`)
- **Email**: Validates proper email format with specific pattern
- **Password**: Requires minimum 8 characters with uppercase, lowercase, number, and special character
- **2FA Code**: Must be exactly 6 digits (`regex:/^\d{6}$/`)

### Benefits
- Controllers remain lean with validation logic moved to Form Request classes
- Custom error messages provide clear feedback to users
- Consistent validation rules across the application
- Enhanced security through strict input whitelist validation
- Improved maintainability through separation of concerns

### Showcase
https://github.com/user-attachments/assets/b24cdf46-be45-4c18-93c5-2c5a3aebb825

