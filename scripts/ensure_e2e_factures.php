<?php

declare(strict_types=1);

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Patient;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$patient = Patient::query()->updateOrCreate(
    ['email' => 'e2e.factures@medisys.test'],
    [
        'numero_dossier' => 'E2E-FACTURES-001',
        'nom' => 'Factures',
        'prenom' => 'E2E',
        'date_naissance' => '1988-04-12',
        'genre' => 'M',
        'telephone' => '0600000101',
        'adresse' => 'Fixture factures premium',
        'ville' => 'Casablanca',
        'code_postal' => '20000',
        'etat_civil' => 'marie',
        'contact_urgence' => 'Contact E2E Factures',
        'telephone_urgence' => '0600000102',
        'allergies' => 'Aucune',
        'traitements' => 'Aucun',
        'notes' => 'Patient de fixture pour les tests Playwright factures.',
    ]
);

$facturePayload = [
    'patient_id' => $patient->id,
];

if (Schema::hasColumn('factures', 'numero_facture')) {
    $facturePayload['numero_facture'] = 'FAC-E2E-0001';
}

if (Schema::hasColumn('factures', 'medecin_id')) {
    $facturePayload['medecin_id'] = null;
}

if (Schema::hasColumn('factures', 'date_facture')) {
    $facturePayload['date_facture'] = Carbon::now()->toDateString();
}

if (Schema::hasColumn('factures', 'date_echeance')) {
    $facturePayload['date_echeance'] = Carbon::now()->addDays(15)->toDateString();
}

if (Schema::hasColumn('factures', 'montant_total')) {
    $facturePayload['montant_total'] = 350.00;
}

if (Schema::hasColumn('factures', 'remise')) {
    $facturePayload['remise'] = 20.00;
}

if (Schema::hasColumn('factures', 'statut')) {
    $facturePayload['statut'] = 'en_attente';
}

if (Schema::hasColumn('factures', 'mode_paiement')) {
    $facturePayload['mode_paiement'] = 'carte';
}

if (Schema::hasColumn('factures', 'date_paiement')) {
    $facturePayload['date_paiement'] = null;
}

if (Schema::hasColumn('factures', 'notes')) {
    $facturePayload['notes'] = 'Fixture E2E factures premium';
}

$facture = Facture::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'notes' => 'Fixture E2E factures premium',
    ],
    $facturePayload
);

$lignePayload = [
    'facture_id' => $facture->id,
    'description' => 'Consultation premium E2E',
    'quantite' => 1,
    'prix_unitaire' => 350.00,
    'total_ligne' => 350.00,
];

if (Schema::hasColumn('ligne_factures', 'type')) {
    $lignePayload['type'] = 'prestation';
}

LigneFacture::query()->updateOrCreate(
    [
        'facture_id' => $facture->id,
        'description' => 'Consultation premium E2E',
    ],
    $lignePayload
);

fwrite(STDOUT, json_encode([
    'patient_id' => $patient->id,
    'facture_id' => $facture->id,
], JSON_THROW_ON_ERROR));
