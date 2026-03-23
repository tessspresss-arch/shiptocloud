<?php

namespace Database\Seeders;

use App\Models\Examen;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        // Récupère un patient et un médecin existants
        $patient = Patient::first();
        $medecin = User::where('role', 'admin')->first();
        
        if (!$patient || !$medecin) {
            return; // Ne crée rien s'il n'y a pas de données de base
        }

        $examens = [
            [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type_examen' => 'Analyse de sang',
                'description' => 'Analyse complète avec test sérologique',
                'date_examen' => now()->addDays(2),
                'resultats' => 'Résultats normaux - Globules rouges: 4.8M, Globules blancs: 7.2K',
                'observations' => 'Patient en bonne santé générale',
                'statut' => 'termine',
                'localisation' => 'Bras droit',
                'appareil' => 'Analyseur biochimique auto',
                'cout' => 45.50,
                'payee' => true,
                'recommandations' => 'Contrôle recommandé dans 6 mois',
                'created_by' => $medecin->id,
            ],
            [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type_examen' => 'Radiographie',
                'description' => 'Radiographie thoracique de contrôle',
                'date_examen' => now()->addDays(5),
                'resultats' => null,
                'observations' => 'Examen demandé pour suspicion infection pulmonaire',
                'statut' => 'en_cours',
                'localisation' => 'Thorax',
                'appareil' => 'Radiographe numérique GE',
                'cout' => 85.00,
                'payee' => false,
                'recommandations' => null,
                'created_by' => $medecin->id,
            ],
            [
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'type_examen' => 'Échographie',
                'description' => 'Échographie abdominale de contrôle',
                'date_examen' => now()->addDays(10),
                'resultats' => null,
                'observations' => 'Suivi post-opératoire',
                'statut' => 'demande',
                'localisation' => 'Abdomen',
                'appareil' => 'Échographe Philips EPIQ',
                'cout' => 120.00,
                'payee' => false,
                'recommandations' => 'À jeun avant l\'examen',
                'created_by' => $medecin->id,
            ],
        ];

        foreach ($examens as $examen) {
            Examen::create($examen);
        }
    }
}
