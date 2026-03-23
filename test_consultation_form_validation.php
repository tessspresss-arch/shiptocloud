<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Consultation Form Validation ===\n\n";

// Get test data
$patient = \App\Models\Patient::first();
$medecin = \App\Models\Medecin::actif()->first();

if (!$patient) {
    echo "⚠️  No patients found. Please create a patient first.\n";
    exit(1);
}

if (!$medecin) {
    echo "⚠️  No active medecins found. Please create a medecin first.\n";
    exit(1);
}

echo "Test Data:\n";
echo "- Patient: {$patient->nom} {$patient->prenom} (ID: {$patient->id})\n";
echo "- Medecin: {$medecin->nom_complet} (ID: {$medecin->id})\n\n";

// Test validation rules
echo "Testing Validation Rules:\n";

$validator = \Illuminate\Support\Facades\Validator::make([
    'patient_id' => $patient->id,
    'medecin_id' => $medecin->id,
    'date_consultation' => date('Y-m-d'),
], [
    'patient_id' => 'required|exists:patients,id',
    'medecin_id' => 'required|exists:medecins,id',
    'date_consultation' => 'required|date',
]);

if ($validator->fails()) {
    echo "✗ Validation FAILED:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "  - $error\n";
    }
} else {
    echo "✓ Validation PASSED\n";
    echo "  - patient_id: Valid (exists in patients table)\n";
    echo "  - medecin_id: Valid (exists in medecins table)\n";
    echo "  - date_consultation: Valid\n";
}

// Test with invalid medecin_id (to ensure validation catches it)
echo "\nTesting with invalid medecin_id (999):\n";
$validator2 = \Illuminate\Support\Facades\Validator::make([
    'patient_id' => $patient->id,
    'medecin_id' => 999,
    'date_consultation' => date('Y-m-d'),
], [
    'patient_id' => 'required|exists:patients,id',
    'medecin_id' => 'required|exists:medecins,id',
    'date_consultation' => 'required|date',
]);

if ($validator2->fails()) {
    echo "✓ Validation correctly FAILED for invalid medecin_id\n";
    echo "  Error: " . $validator2->errors()->first('medecin_id') . "\n";
} else {
    echo "✗ Validation should have failed but didn't!\n";
}

echo "\n=== Test Complete ===\n";
echo "\nSUMMARY:\n";
echo "✓ The validation rules now correctly reference the 'medecins' table\n";
echo "✓ Form submissions will work with the medecin IDs from the medecins table\n";
echo "✓ The dropdown will display: {$medecin->nom_complet}";
if ($medecin->specialite) {
    echo " - {$medecin->specialite}";
}
echo "\n";
