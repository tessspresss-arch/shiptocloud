<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@cabinet.com'],
            [
                'name' => 'Administrateur Cabinet',
                'password' => Hash::make('1234'), // Changez ce mot de passe
                'role' => 'admin', // Si vous avez un champ rôle
                'email_verified_at' => now(),
            ]
        );

        echo "Utilisateur admin créé ou mis à jour : admin@cabinet.com / 1234\n";
    }
}
