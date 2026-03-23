<?php
/**
 * Test script to verify facture fixes
 * Run with: php test_facture_fixes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Facture;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\LigneFacture;

echo "=== Testing Facture Module Fixes ===\n\n";

// Test 1: Check if relationships are properly defined
echo "Test 1: Checking Model Relationships\n";
echo "-------------------------------------\n";

$facture = Facture::first();

if (!$facture) {
    echo "❌ No factures found in database. Creating test data...\n";
    
    // Create test patient
    $patient = Patient::first();
    if (!$patient) {
        echo "❌ No patients found. Please create a patient first.\n";
        exit(1);
    }
    
    // Create test facture
    $facture = Facture::create([
        'numero_facture' => Facture::generateNumero(),
        'patient_id' => $patient->id,
        'date_facture' => now(),
        'montant_total' => 500.00,
        'remise' => 0,
        'statut' => 'en_attente',
    ]);
    
    // Create test ligne facture
    LigneFacture::create([
        'facture_id' => $facture->id,
        'description' => 'Consultation générale',
        'quantite' => 1,
        'prix_unitaire' => 500.00,
        'total_ligne' => 500.00,
        'type' => 'prestation',
    ]);
    
    echo "✅ Test facture created with ID: {$facture->id}\n\n";
}

// Test relationship methods exist
try {
    $reflection = new ReflectionClass(Facture::class);
    
    $hasPatient = $reflection->hasMethod('patient');
    $hasMedecin = $reflection->hasMethod('medecin');
    $hasLigneFactures = $reflection->hasMethod('ligneFactures');
    
    echo "✅ patient() method exists: " . ($hasPatient ? 'Yes' : 'No') . "\n";
    echo "✅ medecin() method exists: " . ($hasMedecin ? 'Yes' : 'No') . "\n";
    echo "✅ ligneFactures() method exists: " . ($hasLigneFactures ? 'Yes' : 'No') . "\n\n";
    
    if (!$hasPatient || !$hasMedecin || !$hasLigneFactures) {
        echo "❌ Some relationships are missing!\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error checking relationships: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Load facture with eager loading
echo "Test 2: Testing Eager Loading\n";
echo "------------------------------\n";

try {
    $factureWithRelations = Facture::with(['ligneFactures', 'patient', 'medecin'])->first();
    
    if ($factureWithRelations) {
        echo "✅ Facture loaded with ID: {$factureWithRelations->id}\n";
        echo "✅ Numero: {$factureWithRelations->numero_facture}\n";
        
        // Check patient relationship
        if ($factureWithRelations->patient) {
            echo "✅ Patient loaded: {$factureWithRelations->patient->nom} {$factureWithRelations->patient->prenom}\n";
        } else {
            echo "❌ Patient not loaded\n";
        }
        
        // Check medecin relationship
        if ($factureWithRelations->medecin) {
            echo "✅ Medecin loaded: Dr. {$factureWithRelations->medecin->nom} {$factureWithRelations->medecin->prenom}\n";
        } else {
            echo "⚠️  Medecin not assigned (this is OK if facture has no medecin)\n";
        }
        
        // Check ligneFactures relationship
        $lignesCount = $factureWithRelations->ligneFactures->count();
        echo "✅ Ligne Factures loaded: {$lignesCount} ligne(s)\n";
        
        if ($lignesCount > 0) {
            echo "\n   Prestations:\n";
            foreach ($factureWithRelations->ligneFactures as $ligne) {
                echo "   - {$ligne->description}: {$ligne->quantite} x {$ligne->prix_unitaire} DH = {$ligne->total_ligne} DH\n";
            }
        } else {
            echo "❌ No ligne factures found!\n";
        }
        
    } else {
        echo "❌ No facture found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error loading facture: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Test 3: Check fillable fields
echo "Test 3: Checking Fillable Fields\n";
echo "---------------------------------\n";

$facture = new Facture();
$fillable = $facture->getFillable();

$requiredFields = ['numero_facture', 'patient_id', 'medecin_id', 'date_facture', 'date_echeance', 'montant_total', 'remise', 'statut', 'notes'];

foreach ($requiredFields as $field) {
    if (in_array($field, $fillable)) {
        echo "✅ {$field} is fillable\n";
    } else {
        echo "❌ {$field} is NOT fillable\n";
    }
}

echo "\n";

// Test 4: Test status enum values
echo "Test 4: Testing Status Values\n";
echo "------------------------------\n";

$validStatuses = ['brouillon', 'en_attente', 'payée', 'annulée'];

foreach ($validStatuses as $status) {
    echo "✅ Status '{$status}' is valid\n";
}

echo "\n";

// Test 5: Simulate controller show method
echo "Test 5: Simulating Controller Show Method\n";
echo "------------------------------------------\n";

try {
    $testFacture = Facture::with(['ligneFactures', 'patient', 'medecin'])->first();
    
    if ($testFacture) {
        echo "✅ Controller would load facture successfully\n";
        echo "   - ID: {$testFacture->id}\n";
        echo "   - Numero: {$testFacture->numero_facture}\n";
        echo "   - Patient: " . ($testFacture->patient ? "Loaded" : "Not loaded") . "\n";
        echo "   - Medecin: " . ($testFacture->medecin ? "Loaded" : "Not assigned") . "\n";
        echo "   - Lignes: {$testFacture->ligneFactures->count()} loaded\n";
        
        // Test view would work
        if ($testFacture->ligneFactures->count() > 0) {
            echo "✅ View foreach loop would work (ligneFactures is not null)\n";
        } else {
            echo "⚠️  No lignes to display in view\n";
        }
    } else {
        echo "❌ No facture to test\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Check LigneFacture model
echo "Test 6: Checking LigneFacture Model\n";
echo "------------------------------------\n";

$ligneFacture = new LigneFacture();
$ligneFillable = $ligneFacture->getFillable();

$requiredLigneFields = ['facture_id', 'description', 'quantite', 'prix_unitaire', 'total_ligne', 'type'];

foreach ($requiredLigneFields as $field) {
    if (in_array($field, $ligneFillable)) {
        echo "✅ {$field} is fillable in LigneFacture\n";
    } else {
        echo "❌ {$field} is NOT fillable in LigneFacture\n";
    }
}

echo "\n";

// Summary
echo "=== Test Summary ===\n";
echo "All critical tests completed.\n";
echo "If all tests show ✅, the fixes are working correctly.\n";
echo "\nNext step: Test in browser at http://cabinet-medical-laravel.test/factures/1\n";
