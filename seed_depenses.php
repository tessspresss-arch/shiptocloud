<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Depense;

$user = User::first();

if (!$user) {
    echo "Créons d'abord un utilisateur";
    exit;
}

// Créer quelques dépenses de test
Depense::create([
    'user_id' => $user->id,
    'description' => 'Fournitures médicales',
    'details' => 'Masques, gants, tenues stériles',
    'montant' => 500.00,
    'date_depense' => now(),
    'categorie' => 'fournitures',
    'beneficiaire' => 'Fournisseur ABC',
    'statut' => 'payee',
    'facture_numero' => 'FAC001'
]);

Depense::create([
    'user_id' => $user->id,
    'description' => 'Médicaments',
    'details' => 'Stock initial d\'urgence',
    'montant' => 1200.00,
    'date_depense' => now(),
    'categorie' => 'medicaments',
    'beneficiaire' => 'Pharmacie XYZ',
    'statut' => 'payee',
    'facture_numero' => 'FAC002'
]);

Depense::create([
    'user_id' => $user->id,
    'description' => 'Loyer du cabinet',
    'details' => 'Paiement mensuel',
    'montant' => 3000.00,
    'date_depense' => now()->startOfMonth(),
    'categorie' => 'loyer',
    'beneficiaire' => 'Propriétaire',
    'statut' => 'payee',
    'facture_numero' => 'FAC003'
]);

Depense::create([
    'user_id' => $user->id,
    'description' => 'Facture électricité',
    'details' => 'Consommation du mois',
    'montant' => 450.00,
    'date_depense' => now(),
    'categorie' => 'utilites',
    'beneficiaire' => 'Fournisseur électricité',
    'statut' => 'en_attente',
    'facture_numero' => null
]);

echo "✅ 4 dépenses créées avec succès!";
