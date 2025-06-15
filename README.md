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
