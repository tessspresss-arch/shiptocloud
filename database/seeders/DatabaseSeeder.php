<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingSeeder::class,
            RbacFoundationSeeder::class,
            PatientSeeder::class,
            MedecinSeeder::class,
            MedicamentSeeder::class,
            RendezVousSeeder::class,
            ConsultationSeeder::class,
            FactureSeeder::class,
        ]);
    }
}