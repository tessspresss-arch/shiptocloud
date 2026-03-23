<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Facture;
use App\Models\LigneFacture;

echo "Adding test ligne factures...\n\n";

$facture = Facture::first();

if (!$facture) {
    echo "❌ No facture found!\n";
    exit(1);
}

echo "Found facture: {$facture->numero_facture}\n";

// Add test lignes
LigneFacture::create([
    'facture_id' => $facture->id,
    'description' => 'Consultation générale',
    'quantite' => 1,
    'prix_unitaire' => 300.00,
    'total_ligne' => 300.00,
    'type' => 'prestation',
]);

LigneFacture::create([
    'facture_id' => $facture->id,
    'description' => 'Radiographie',
    'quantite' => 2,
    'prix_unitaire' => 150.00,
    'total_ligne' => 300.00,
    'type' => 'prestation',
]);

echo "✅ Lignes factures created successfully!\n";
echo "\nNow run: php test_facture_fixes.php\n";
