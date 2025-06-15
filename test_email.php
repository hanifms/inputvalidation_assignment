<?php

/**
 * Test script for sending an actual email to Mailtrap
 * To run, use: php test_email.php
 */

// Load the Laravel framework
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Facades\Mail;

// Clear terminal
echo "\033[2J\033[H";
echo "=============================================\n";
echo "Email Sending Test Script\n";
echo "=============================================\n\n";

// Get mail config
echo "Mail Configuration:\n";
echo " - Mail Driver: " . config('mail.default') . "\n";
echo " - Mail Host: " . config('mail.mailers.smtp.host') . "\n";
echo " - Mail Port: " . config('mail.mailers.smtp.port') . "\n";
echo " - Mail Username: " . (config('mail.mailers.smtp.username') ? "CONFIGURED" : "NOT CONFIGURED") . "\n";
echo " - Mail Password: " . (config('mail.mailers.smtp.password') ? "CONFIGURED" : "NOT CONFIGURED") . "\n";
echo " - Mail Encryption: " . (config('mail.mailers.smtp.encryption') ? config('mail.mailers.smtp.encryption') : "NONE") . "\n";
echo " - Mail From Address: " . config('mail.from.address') . "\n\n";

// Find a user
$user = User::first();
if (!$user) {
    echo "ERROR: No user found to send email to.\n";
    exit(1);
}

// Generate a test code
$code = rand(100000, 999999);
echo "Generated 2FA code: {$code}\n\n";

try {
    echo "Sending test email to: {$user->email}...\n";

    // Send an actual email (will appear in Mailtrap)
    Mail::to($user->email)->send(new TwoFactorAuthMail($code));

    echo "SUCCESS: Email sent! Check your Mailtrap inbox.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=============================================\n";
echo "Test complete!\n";
echo "=============================================\n";
