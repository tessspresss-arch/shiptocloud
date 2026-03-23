<?php

namespace Database\Seeders\Testing;

use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Database\Seeder;

class MedisysTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $medecin = Medecin::query()->first()
            ?? Medecin::query()->updateOrCreate(
                ['email' => 'dr.zarrik@medisys.test'],
                [
                    'nom' => 'Zarrik',
                    'prenom' => 'Mohammed',
                    'specialite' => 'cardiologie',
                    'telephone' => '+212612345678',
                    'numero_ordre' => 'ORD-MEDISYS-001',
                    'statut' => 'actif',
                ]
            );

        $patientA = Patient::query()->first()
            ?? Patient::query()->updateOrCreate(
                ['cin' => 'AA123456'],
                [
                    'numero_dossier' => 'PAT-AHMED-TEST',
                    'nom' => 'Bennani',
                    'prenom' => 'Ahmed',
                    'telephone' => '+212611111111',
                    'email' => 'ahmed.bennani@medisys.test',
                    'date_naissance' => '1990-01-01',
                    'genre' => 'M',
                    'adresse' => 'Casablanca',
                    'ville' => 'Casablanca',
                    'assurance' => 'CNSS',
                    'is_draft' => false,
                ]
            );

        $patientB = Patient::query()->updateOrCreate(
            ['cin' => 'BB123456'],
            [
                'numero_dossier' => 'PAT-FATIMA-TEST',
                'nom' => 'Idrissi',
                'prenom' => 'Fatima',
                'telephone' => '+212622222222',
                'email' => 'fatima.idrissi@medisys.test',
                'date_naissance' => '1992-02-02',
                'genre' => 'F',
                'adresse' => 'Rabat',
                'ville' => 'Rabat',
                'assurance' => 'CNOPS',
                'is_draft' => false,
            ]
        );

        $today = now()->startOfDay();

        RendezVous::query()->updateOrCreate([
            'patient_id' => $patientA->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(9, 0),
        ], [
            'date_rdv' => $today->copy()->setTime(9, 0),
            'motif' => 'Consultation generale',
            'type' => 'Consultation generale',
            'duree' => 30,
            'statut' => 'a_venir',
        ]);

        RendezVous::query()->updateOrCreate([
            'patient_id' => $patientB->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(10, 0),
        ], [
            'date_rdv' => $today->copy()->setTime(10, 0),
            'motif' => 'Controle',
            'type' => 'Controle',
            'duree' => 30,
            'statut' => 'en_attente',
        ]);

        RendezVous::query()->updateOrCreate([
            'patient_id' => $patientA->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(11, 0),
        ], [
            'date_rdv' => $today->copy()->setTime(11, 0),
            'motif' => 'Urgence',
            'type' => 'Urgence',
            'duree' => 30,
            'statut' => 'en_soins',
        ]);

        RendezVous::query()->updateOrCreate([
            'patient_id' => $patientB->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(12, 0),
        ], [
            'date_rdv' => $today->copy()->setTime(12, 0),
            'motif' => 'Suivi',
            'type' => 'Suivi',
            'duree' => 30,
            'statut' => 'vu',
        ]);

        RendezVous::query()->updateOrCreate([
            'patient_id' => $patientA->id,
            'medecin_id' => $medecin->id,
            'date_heure' => $today->copy()->setTime(13, 0),
        ], [
            'date_rdv' => $today->copy()->setTime(13, 0),
            'motif' => 'Vaccination',
            'type' => 'Vaccination',
            'duree' => 30,
            'statut' => 'absent',
        ]);

        Facture::query()->updateOrCreate(
            ['numero_facture' => 'FAC-' . now()->format('Y') . '-900001'],
            [
                'patient_id' => $patientA->id,
                'medecin_id' => $medecin->id,
                'statut' => 'en_attente',
                'montant_total' => 300,
                'remise' => 0,
                'date_facture' => now()->toDateString(),
            ]
        );
    }
}
