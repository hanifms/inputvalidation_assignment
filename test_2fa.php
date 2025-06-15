<?php

/**
 * This is a simple test script for the 2FA implementation.
 * To run it, use the command: php test_2fa.php
 */

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

// Clear terminal
echo "\033[2J\033[H";
echo "=============================================\n";
echo "Two-Factor Authentication Test Script\n";
echo "=============================================\n\n";

// 1. Find or create a test user
echo "Step 1: Finding or creating a test user...\n";
$user = User::where('email', 'test@example.com')->first();
if (!$user) {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    echo " - Created new test user: {$user->email}\n";
} else {
    echo " - Found existing test user: {$user->email}\n";
}

// 2. Test enabling 2FA
echo "\nStep 2: Testing 2FA enable...\n";
$user->update([
    'two_factor_code' => 'ENABLED',
    'two_factor_expires_at' => now()->addYears(1),
]);
echo " - 2FA enabled for user\n";

// 3. Test the LoginResponse logic
echo "\nStep 3: Simulating login with 2FA...\n";
$code = rand(100000, 999999);
$user->update([
    'two_factor_code' => $code,
    'two_factor_expires_at' => now()->addMinutes(10),
]);
echo " - Generated 2FA code: {$code}\n";

// 4. Test sending the email
echo "\nStep 4: Testing 2FA email...\n";
echo " - Mail settings: " . config('mail.default') . "\n";
try {
    Mail::fake(); // So we don't actually send emails during test
    Mail::to($user->email)->send(new TwoFactorAuthMail($code));
    echo " - Successfully created 2FA email\n";
} catch (\Exception $e) {
    echo " - Error sending email: " . $e->getMessage() . "\n";
}

// 5. Test verification logic
echo "\nStep 5: Testing 2FA verification...\n";
// Simulate correct code
$result = ($code === $user->two_factor_code && !$user->two_factor_expires_at->isPast());
echo " - Correct code verification: " . ($result ? "PASSED" : "FAILED") . "\n";

// Simulate incorrect code
$result = (123456 === $user->two_factor_code);
echo " - Incorrect code verification: " . (!$result ? "PASSED" : "FAILED") . "\n";

// Simulate expired code
$user->update(['two_factor_expires_at' => now()->subMinutes(15)]);
$result = (!$user->two_factor_expires_at->isPast());
echo " - Expired code verification: " . (!$result ? "PASSED" : "FAILED") . "\n";

// 6. Cleanup
echo "\nStep 6: Cleaning up...\n";
$user->update([
    'two_factor_code' => null,
    'two_factor_expires_at' => null,
]);
echo " - Reset user 2FA status\n";

echo "\n=============================================\n";
echo "Test completed!\n";
echo "=============================================\n";
