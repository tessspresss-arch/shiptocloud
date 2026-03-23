<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@cabinet.com')->first();

if ($user) {
    echo "Admin user found:\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Email Verified At: " . $user->email_verified_at . "\n";
    echo "Password Hash: " . substr($user->password, 0, 10) . "...\n";
} else {
    echo "Admin user not found.\n";
}
