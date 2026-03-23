<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;

class MedisysTestingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MedisysTestUsersSeeder::class,
            MedisysTestDataSeeder::class,
        ]);
    }
}
