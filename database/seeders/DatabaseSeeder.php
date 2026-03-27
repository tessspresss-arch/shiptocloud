<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RbacFoundationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
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