<?php

/**
 * This is a simple test script to verify mail functionality.
 * To run it, use the command: php test_mail.php
 */

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Mail\TwoFactorAuthMail;
use Illuminate\Support\Facades\Mail;

echo "Testing email functionality...\n";

// Get mail config
$mailer = config('mail.mailer');
$host = config('mail.host');
$port = config('mail.port');
$username = config('mail.username');

echo "Mail Configuration:\n";
echo "- Mailer: {$mailer}\n";
echo "- Host: {$host}\n";
echo "- Port: {$port}\n";
echo "- Username: {$username}\n\n";

// Test email sending
try {
    echo "Sending test email...\n";
    Mail::to('test@example.com')->send(new TwoFactorAuthMail('123456'));
    echo "Email sent successfully!\n";
} catch (\Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
