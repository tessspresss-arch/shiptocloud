<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
                            {email=admin@cabinet.com : Email de l\'administrateur}
                            {password=Admin123 : Mot de passe}
                            {name=Administrateur : Nom complet}';

    protected $description = 'Créer un compte administrateur';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name');

        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Mettre à jour le mot de passe
            $existingUser->password = Hash::make($password);
            $existingUser->name = $name;
            $existingUser->role = 'admin';
            $existingUser->save();

            $this->info("✅ Compte administrateur MIS À JOUR !");
        } else {
            // Créer un nouvel utilisateur
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);

            $this->info("✅ Compte administrateur CRÉÉ !");
        }

        $this->line("=================================");
        $this->line("📋 INFORMATIONS DE CONNEXION :");
        $this->line("=================================");
        $this->line("Email: " . $email);
        $this->line("Mot de passe: " . $password);
        $this->line("=================================");
        $this->warn("⚠️  Gardez ces informations en sécurité !");

        return 0;
    }
}
