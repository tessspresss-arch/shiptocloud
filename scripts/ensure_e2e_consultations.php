<?php

declare(strict_types=1);

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$patient = Patient::query()->updateOrCreate(
    ['email' => 'e2e.consultations@medisys.test'],
    [
        'numero_dossier' => 'E2E-CONSULT-001',
        'nom' => 'Consultation',
        'prenom' => 'E2E',
        'date_naissance' => '1991-06-18',
        'genre' => 'F',
        'telephone' => '0600000301',
        'adresse' => 'Fixture consultations premium',
        'ville' => 'Rabat',
        'code_postal' => '10000',
        'etat_civil' => 'celibataire',
        'contact_urgence' => 'Contact E2E Consult',
        'telephone_urgence' => '0600000302',
        'allergies' => 'Aucune',
        'traitements' => 'Aucun',
        'notes' => 'Patient de fixture pour les tests UI consultations.',
    ]
);

$medecin = Medecin::withTrashed()->updateOrCreate(
    ['email' => 'e2e.consult.medecin@medisys.test'],
    [
        'matricule' => 'MED-E2E-CONS-0001',
        'civilite' => 'Dr.',
        'nom' => 'Consult',
        'prenom' => 'E2E',
        'specialite' => 'Medecine generale',
        'telephone' => '+212600000302',
        'adresse_cabinet' => 'Cabinet E2E Consultations',
        'ville' => 'Rabat',
        'code_postal' => '10000',
        'numero_ordre' => 'ORD-E2E-CONS-1',
        'tarif_consultation' => 250.00,
        'statut' => 'actif',
        'notes' => 'Medecin de fixture pour les tests UI consultations.',
        'date_embauche' => '2019-01-01',
    ]
);

if (method_exists($medecin, 'restore') && $medecin->trashed()) {
    $medecin->restore();
}

$consultation = Consultation::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'diagnostic' => 'Suivi clinique E2E stable',
    ],
    [
        'rendez_vous_id' => null,
        'medecin_id' => $medecin->id,
        'date_consultation' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
        'symptomes' => 'Fatigue legere et controle de routine',
        'poids' => 68.5,
        'taille' => 1.68,
        'tension_arterielle_systolique' => 120,
        'tension_arterielle_diastolique' => 80,
        'temperature' => 36.8,
        'examen_clinique' => 'Examen clinique normal',
        'traitement_prescrit' => 'Hydratation et repos',
        'recommandations' => 'Controle dans trois mois',
        'date_prochaine_visite' => Carbon::now()->addMonths(3)->toDateString(),
    ]
);

fwrite(STDOUT, json_encode([
    'consultation_id' => $consultation->id,
], JSON_THROW_ON_ERROR));
