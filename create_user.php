<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Supprimer l'ancien utilisateur s'il existe
User::where('email', 'admin@example.com')->delete();

// Créer le nouvel utilisateur admin
$user = User::create([
    'name' => 'Admin Cabinet',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'email_verified_at' => now()
]);

echo "✅ Utilisateur créé avec succès!\n";
echo "📧 Email: admin@example.com\n";
echo "🔐 Mot de passe: password123\n";
echo "\n👉 Accédez à: http://cabinet-medical-laravel.test/admin\n";
