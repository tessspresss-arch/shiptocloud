<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CategorieDocument;

// Vérifier si les catégories existent
$count = CategorieDocument::count();

if ($count == 0) {
    $categories = [
        ['nom' => 'Certificats Médicaux', 'description' => 'Certificats et attestations médicales', 'actif' => true],
        ['nom' => 'Prescriptions', 'description' => 'Prescriptions de médicaments', 'actif' => true],
        ['nom' => 'Résultats de Tests', 'description' => 'Résultats d\'analyses et tests médicaux', 'actif' => true],
        ['nom' => 'Ordonnances', 'description' => 'Ordonnances médicales', 'actif' => true],
        ['nom' => 'Rapports Médicaux', 'description' => 'Rapports et comptes rendus', 'actif' => true],
        ['nom' => 'Imagerie Médicale', 'description' => 'Radiographies, IRM, Scanner', 'actif' => true],
        ['nom' => 'Dossiers Patient', 'description' => 'Dossiers médicaux complets', 'actif' => true],
        ['nom' => 'Factures', 'description' => 'Factures et devis médicaux', 'actif' => true],
        ['nom' => 'Correspondances', 'description' => 'Courriers et correspondances', 'actif' => true],
        ['nom' => 'Autres Documents', 'description' => 'Documents divers', 'actif' => true],
    ];

    foreach ($categories as $cat) {
        CategorieDocument::create($cat);
    }

    echo "✅ " . count($categories) . " catégories créées avec succès!\n";
} else {
    echo "✅ Les catégories existent déjà (" . $count . " catégories).\n";
}
