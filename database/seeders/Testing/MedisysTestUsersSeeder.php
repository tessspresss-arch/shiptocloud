<?php

namespace Database\Seeders\Testing;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MedisysTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        User::updateOrCreate(
            ['email' => 'admin@medisys.test'],
            [
                'name' => 'Admin Test',
                'password' => $password,
                'role' => 'admin',
                'module_permissions' => [],
                'email_verified_at' => now(),
                'account_status' => 'actif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'medecin@medisys.test'],
            [
                'name' => 'Medecin Test',
                'password' => $password,
                'role' => 'medecin',
                'module_permissions' => [
                    'dashboard' => true,
                    'patients' => true,
                    'consultations' => true,
                    'planning' => true,
                    'medecins' => true,
                    'facturation' => false,
                    'sms' => false,
                ],
                'email_verified_at' => now(),
                'account_status' => 'actif',
            ]
        );

        User::updateOrCreate(
            ['email' => 'reception@medisys.test'],
            [
                'name' => 'Reception Test',
                'password' => $password,
                'role' => 'secretaire',
                'module_permissions' => [
                    'dashboard' => true,
                    'patients' => true,
                    'consultations' => false,
                    'planning' => true,
                    'facturation' => true,
                    'sms' => true,
                    'medecins' => true,
                ],
                'email_verified_at' => now(),
                'account_status' => 'actif',
            ]
        );
    }
}
