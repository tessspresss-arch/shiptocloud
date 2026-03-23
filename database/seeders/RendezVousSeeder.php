<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RendezVous;
use App\Models\Patient;
use App\Models\Medecin;
use Carbon\Carbon;

class RendezVousSeeder extends Seeder
{
    public function run()
    {
        // Update existing appointments to have dates in 2024
        $appointments = RendezVous::all();

        foreach ($appointments as $appointment) {
            // Generate a random date in 2024
            $randomDay = rand(1, 365);
            $baseDate = Carbon::create(2024, 1, 1)->addDays($randomDay - 1);

            // Skip weekends
            while ($baseDate->isWeekend()) {
                $baseDate->addDay();
            }

            // Random time between 8:00 and 17:00
            $hour = rand(8, 16);
            $minute = rand(0, 3) * 15; // 0, 15, 30, 45
            $appointmentTime = $baseDate->copy()->setTime($hour, $minute);

            $appointment->update([
                'date_heure' => $appointmentTime,
                'statut' => $appointmentTime->isPast() ? 'terminé' : 'programmé'
            ]);
        }
    }
}
