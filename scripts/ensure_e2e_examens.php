<?php

declare(strict_types=1);

use App\Models\Examen;
use App\Models\Patient;
use App\Models\ResultatExamen;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$patient = Patient::query()->updateOrCreate(
    ['email' => 'e2e.examens@medisys.test'],
    [
        'numero_dossier' => 'E2E-EXAMENS-001',
        'nom' => 'Examens',
        'prenom' => 'E2E',
        'date_naissance' => '1990-01-15',
        'genre' => 'F',
        'telephone' => '0600000001',
        'adresse' => 'Fixture examens premium',
        'ville' => 'Casablanca',
        'code_postal' => '20000',
        'etat_civil' => 'celibataire',
        'contact_urgence' => 'Contact E2E',
        'telephone_urgence' => '0600000002',
        'allergies' => 'Aucune',
        'traitements' => 'Aucun',
        'notes' => 'Patient de fixture pour les tests Playwright examens.',
    ]
);

$examPayload = [
    'patient_id' => $patient->id,
    'description' => 'Fixture E2E examens premium',
];

if (Schema::hasColumn('examens', 'type')) {
    $examPayload['type'] = 'imagerie';
}

if (Schema::hasColumn('examens', 'statut')) {
    $examPayload['statut'] = 'termine';
}

if (Schema::hasColumn('examens', 'observations')) {
    $examPayload['observations'] = 'Fixture stable pour valider les ecrans show et edit.';
}

if (Schema::hasColumn('examens', 'cout')) {
    $examPayload['cout'] = 350.00;
}

if (Schema::hasColumn('examens', 'payee')) {
    $examPayload['payee'] = true;
}

if (Schema::hasColumn('examens', 'medecin_id')) {
    $examPayload['medecin_id'] = null;
}

if (Schema::hasColumn('examens', 'consultation_id')) {
    $examPayload['consultation_id'] = null;
}

if (Schema::hasColumn('examens', 'nom_examen')) {
    $examPayload['nom_examen'] = 'IRM de controle E2E';
}

if (Schema::hasColumn('examens', 'type_examen')) {
    $examPayload['type_examen'] = 'IRM de controle E2E';
}

if (Schema::hasColumn('examens', 'date_demande')) {
    $examPayload['date_demande'] = Carbon::now()->addDay()->startOfDay();
}

if (Schema::hasColumn('examens', 'date_realisation')) {
    $examPayload['date_realisation'] = Carbon::now()->addDay()->setTime(10, 30);
}

if (Schema::hasColumn('examens', 'date_examen')) {
    $examPayload['date_examen'] = Carbon::now()->addDay()->toDateString();
}

if (Schema::hasColumn('examens', 'lieu_realisation')) {
    $examPayload['lieu_realisation'] = 'Imagerie - Salle 2';
}

if (Schema::hasColumn('examens', 'localisation')) {
    $examPayload['localisation'] = 'Imagerie - Salle 2';
}

if (Schema::hasColumn('examens', 'resultats')) {
    $examPayload['resultats'] = 'Aucune anomalie visible. Controle satisfaisant.';
}

if (Schema::hasColumn('examens', 'recommandations')) {
    $examPayload['recommandations'] = 'Revoir le patient dans 3 mois.';
}

if (Schema::hasColumn('examens', 'appareil')) {
    $examPayload['appareil'] = 'IRM Siemens E2E';
}

if (Schema::hasColumn('examens', 'created_by')) {
    $examPayload['created_by'] = null;
}

$examen = Examen::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'description' => 'Fixture E2E examens premium',
    ],
    $examPayload
);

ResultatExamen::query()->updateOrCreate(
    [
        'examen_id' => $examen->id,
        'parametre' => 'Contraste',
    ],
    [
        'valeur' => 'Normal',
        'unite' => '',
        'valeur_normale' => 'Normal',
        'interpretation' => 'normal',
        'notes' => 'Resultat genere automatiquement pour les tests E2E.',
    ]
);

fwrite(STDOUT, json_encode([
    'patient_id' => $patient->id,
    'examen_id' => $examen->id,
], JSON_THROW_ON_ERROR));
