<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultation;
use App\Models\RendezVous;
use App\Models\Patient;
use App\Models\Medecin;
use Carbon\Carbon;

class ConsultationSeeder extends Seeder
{
    public function run()
    {
        // Get completed appointments from the past
        $completedAppointments = RendezVous::where('statut', 'terminé')
            ->where('date_heure', '<', now())
            ->get();

        if ($completedAppointments->isEmpty()) {
            return; // Skip if no completed appointments
        }

        $symptomes = [
            'Douleurs abdominales',
            'Maux de tête persistants',
            'Fièvre et fatigue',
            'Toux chronique',
            'Douleurs articulaires',
            'Problèmes digestifs',
            'Infections respiratoires',
            'Hypertension',
            'Diabète de type 2',
            'Anémie'
        ];

        $diagnostics = [
            'Gastrite chronique',
            'Migraine',
            'Infection virale',
            'Bronchite',
            'Arthrose',
            'Colopathie fonctionnelle',
            'Pneumonie',
            'Hypertension artérielle',
            'Diabète',
            'Anémie ferriprive'
        ];

        $traitements = [
            'Antibiotiques 7 jours',
            'Anti-inflammatoires',
            'Antidépresseurs',
            'Insuline',
            'Diurétiques',
            'Antihypertenseurs',
            'Vitamines',
            'Fer oral',
            'Corticoïdes',
            'Analgésiques'
        ];

        $consultations = [];

        foreach ($completedAppointments as $appointment) {
            // Create consultation for about 80% of completed appointments
            if (rand(1, 10) <= 8) {
                $consultations[] = [
                    'rendez_vous_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'medecin_id' => $appointment->medecin_id,
                    'date_consultation' => $appointment->date_heure->toDateString(),
                    'symptomes' => $symptomes[array_rand($symptomes)],
                    'diagnostic' => $diagnostics[array_rand($diagnostics)],
                    'poids' => rand(50, 100) + (rand(0, 9) / 10), // 50.0 to 100.9
                    'taille' => rand(150, 200) / 100, // 1.50 to 2.00
                    'tension_arterielle_systolique' => rand(110, 180),
                    'tension_arterielle_diastolique' => rand(70, 110),
                    'temperature' => 36.0 + (rand(0, 50) / 10), // 36.0 to 41.0
                    'examen_clinique' => 'Examen clinique normal',
                    'traitement_prescrit' => $traitements[array_rand($traitements)],
                    'recommandations' => 'Régime alimentaire équilibré, activité physique régulière',
                    'date_prochaine_visite' => rand(0, 1) ? $appointment->date_heure->copy()->addDays(rand(30, 90)) : null,
                    'created_at' => $appointment->date_heure,
                    'updated_at' => $appointment->date_heure->copy()->addMinutes(rand(30, 120)),
                ];
            }
        }

        // Insert in chunks
        foreach (array_chunk($consultations, 50) as $chunk) {
            Consultation::insert($chunk);
        }
    }
}
