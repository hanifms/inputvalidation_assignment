<?php

/**
 * Test script for verifying database and mail configuration
 * To run, use: php test_config.php
 */

// Load the Laravel framework
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Clear terminal
echo "\033[2J\033[H";
echo "=============================================\n";
echo "Configuration Test Script\n";
echo "=============================================\n\n";

// Test Database Connection
echo "Testing Database Connection:\n";
try {
    // Try to get a user count
    $userCount = DB::table('users')->count();
    echo " - SUCCESS: Database connection working. Found $userCount users.\n";

    // Test creation of a user (if none exists)
    if ($userCount === 0) {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo " - SUCCESS: Created a test user with ID: $userId\n";
    } else {
        echo " - SKIPPED: Users already exist in database\n";
    }

    // Test 2FA fields
    $user = DB::table('users')->first();
    if ($user) {
        echo " - Testing 2FA fields on user record:\n";
        echo "   - two_factor_code column: " . (property_exists($user, 'two_factor_code') ? "EXISTS" : "MISSING") . "\n";
        echo "   - two_factor_expires_at column: " . (property_exists($user, 'two_factor_expires_at') ? "EXISTS" : "MISSING") . "\n";
    }

    echo " - Overall database status: SUCCESS\n";
} catch (\Exception $e) {
    echo " - ERROR: " . $e->getMessage() . "\n";
}

echo "\nTesting Mail Configuration:\n";
try {
    // Get mail config
    echo " - Mail Driver: " . config('mail.default') . "\n";
    echo " - Mail Host: " . config('mail.mailers.smtp.host') . "\n";
    echo " - Mail Port: " . config('mail.mailers.smtp.port') . "\n";
    echo " - Mail Username: " . (config('mail.mailers.smtp.username') ? "CONFIGURED" : "NOT CONFIGURED") . "\n";
    echo " - Mail Password: " . (config('mail.mailers.smtp.password') ? "CONFIGURED" : "NOT CONFIGURED") . "\n";
    echo " - Mail Encryption: " . (config('mail.mailers.smtp.encryption') ? config('mail.mailers.smtp.encryption') : "NONE") . "\n";
    echo " - Mail From Address: " . config('mail.from.address') . "\n";

    // Test mail creation (without actually sending)
    $user = DB::table('users')->first();
    if ($user) {
        $code = '123456';
        $mailData = new \App\Mail\TwoFactorAuthMail($code);
        $mailData->to($user->email);
        echo " - Mail object creation: SUCCESS\n";
        echo " - Would send email to: " . $user->email . "\n";

        // To actually test sending, uncomment this:
        // \Illuminate\Support\Facades\Mail::to($user->email)->send($mailData);
        // echo " - Mail sent successfully\n";
    }

    echo " - Overall mail configuration: SUCCESS\n";
} catch (\Exception $e) {
    echo " - ERROR: " . $e->getMessage() . "\n";
}

echo "\n=============================================\n";
echo "Test complete!\n";
echo "=============================================\n";
