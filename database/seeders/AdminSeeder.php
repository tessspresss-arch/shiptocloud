<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Créer l'administrateur principal du cabinet médical
     */
    public function run()
    {
        // Créer l'admin si il n'existe pas
        $admin = User::firstOrCreate(
            ['email' => 'admin@cabinet.com'],
            [
                'name' => 'Administrateur Cabinet Médical',
                'password' => Hash::make('AdminCabinet2024!'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // Toujours mettre à jour le mot de passe et le rôle (au cas où)
        $admin->password = Hash::make('1234');
        $admin->role = 'admin';
        $admin->save();

        $this->command->info('==========================================');
        $this->command->info('✅ ADMINISTRATEUR CRÉÉ/MIS À JOUR');
        $this->command->info('==========================================');
        $this->command->info('Email: admin@cabinet.com');
        $this->command->info('Mot de passe: 1234');
        $this->command->info('==========================================');
    }
}
