<?php

declare(strict_types=1);

use App\Models\DossierMedical;
use App\Models\Patient;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$patient = Patient::query()->updateOrCreate(
    ['email' => 'e2e.dossiers@medisys.test'],
    [
        'numero_dossier' => 'E2E-DOSSIERS-001',
        'nom' => 'Dossiers',
        'prenom' => 'E2E',
        'date_naissance' => '1992-09-20',
        'genre' => 'F',
        'telephone' => '0600000201',
        'adresse' => 'Fixture dossiers premium',
        'ville' => 'Rabat',
        'code_postal' => '10000',
        'etat_civil' => 'celibataire',
        'contact_urgence' => 'Contact E2E Dossiers',
        'telephone_urgence' => '0600000202',
        'allergies' => 'Aucune',
        'traitements' => 'Aucun',
        'notes' => 'Patient de fixture pour les tests Playwright dossiers.',
    ]
);

$baseDossierPayload = [
    'patient_id' => $patient->id,
];

if (Schema::hasColumn('dossiers_medicaux', 'type')) {
    $baseDossierPayload['type'] = 'general';
}

if (Schema::hasColumn('dossiers_medicaux', 'date_ouverture')) {
    $baseDossierPayload['date_ouverture'] = Carbon::now()->subDays(10)->toDateString();
}

if (Schema::hasColumn('dossiers_medicaux', 'observations')) {
    $baseDossierPayload['observations'] = 'Fixture E2E dossiers premium.';
}

if (Schema::hasColumn('dossiers_medicaux', 'diagnostic')) {
    $baseDossierPayload['diagnostic'] = 'Suivi clinique stable.';
}

if (Schema::hasColumn('dossiers_medicaux', 'traitement')) {
    $baseDossierPayload['traitement'] = 'Surveillance simple.';
}

if (Schema::hasColumn('dossiers_medicaux', 'prescriptions')) {
    $baseDossierPayload['prescriptions'] = 'Bilan a 3 mois.';
}

if (Schema::hasColumn('dossiers_medicaux', 'documents')) {
    $baseDossierPayload['documents'] = [];
}

$activePayload = $baseDossierPayload;
if (Schema::hasColumn('dossiers_medicaux', 'numero_dossier')) {
    $activePayload['numero_dossier'] = 'DOS-E2E-ACT-001';
}
if (Schema::hasColumn('dossiers_medicaux', 'statut')) {
    $activePayload['statut'] = 'actif';
}

$archivedPayload = $baseDossierPayload;
if (Schema::hasColumn('dossiers_medicaux', 'numero_dossier')) {
    $archivedPayload['numero_dossier'] = 'DOS-E2E-ARC-001';
}
if (Schema::hasColumn('dossiers_medicaux', 'statut')) {
    $archivedPayload['statut'] = 'archive';
}
if (Schema::hasColumn('dossiers_medicaux', 'observations')) {
    $archivedPayload['observations'] = 'Fixture E2E dossiers archivee.';
}

$activeDossier = DossierMedical::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'numero_dossier' => 'DOS-E2E-ACT-001',
    ],
    $activePayload
);

$archivedDossier = DossierMedical::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'numero_dossier' => 'DOS-E2E-ARC-001',
    ],
    $archivedPayload
);

fwrite(STDOUT, json_encode([
    'patient_id' => $patient->id,
    'active_dossier_id' => $activeDossier->id,
    'archived_dossier_id' => $archivedDossier->id,
], JSON_THROW_ON_ERROR));
