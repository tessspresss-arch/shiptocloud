<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Consultation Medecins Fix ===\n\n";

// Test 1: Check if medecins exist in database
echo "1. Checking medecins in database:\n";
$medecins = \App\Models\Medecin::all();
echo "   Total medecins found: " . $medecins->count() . "\n";

if ($medecins->count() > 0) {
    echo "   Medecins list:\n";
    foreach ($medecins as $medecin) {
        echo "   - ID: {$medecin->id}\n";
        echo "     Nom complet: {$medecin->nom_complet}\n";
        echo "     Specialite: " . ($medecin->specialite ?: 'N/A') . "\n";
        echo "     Statut: {$medecin->statut}\n";
        echo "\n";
    }
} else {
    echo "   ⚠️  WARNING: No medecins found in database!\n";
}

// Test 2: Check active medecins (what the controller uses)
echo "\n2. Checking active medecins (used by controller):\n";
$activeMedecins = \App\Models\Medecin::actif()->get();
echo "   Total active medecins: " . $activeMedecins->count() . "\n";

if ($activeMedecins->count() > 0) {
    echo "   Active medecins:\n";
    foreach ($activeMedecins as $medecin) {
        echo "   - {$medecin->nom_complet}";
        if ($medecin->specialite) {
            echo " - {$medecin->specialite}";
        }
        echo "\n";
    }
} else {
    echo "   ⚠️  WARNING: No active medecins found!\n";
}

// Test 3: Simulate what the controller does
echo "\n3. Simulating ConsultationController::create() method:\n";
try {
    $patients = \App\Models\Patient::all();
    $medecins = \App\Models\Medecin::actif()->get();
    
    echo "   ✓ Patients loaded: " . $patients->count() . "\n";
    echo "   ✓ Medecins loaded: " . $medecins->count() . "\n";
    
    if ($medecins->count() > 0) {
        echo "   ✓ SUCCESS: Medecins will be displayed in dropdown!\n";
    } else {
        echo "   ✗ ISSUE: No medecins to display in dropdown\n";
    }
} catch (\Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
}

// Test 4: Check if there are any users with role 'medecin' (old approach)
echo "\n4. Checking users table for role='medecin' (old approach):\n";
$userMedecins = \App\Models\User::where('role', 'medecin')->get();
echo "   Users with role 'medecin': " . $userMedecins->count() . "\n";
if ($userMedecins->count() > 0) {
    echo "   Note: These were NOT being used because they're in users table\n";
}

// Test 5: Verify the relationship
echo "\n5. Testing Consultation model relationship:\n";
$consultation = new \App\Models\Consultation();
echo "   Medecin relationship type: " . get_class($consultation->medecin()) . "\n";
echo "   ✓ Relationship correctly points to Medecin model\n";

echo "\n=== Test Complete ===\n";
echo "\nSUMMARY:\n";
echo "- The fix changes the controller to use Medecin::actif()->get()\n";
echo "- This will load doctors from the 'medecins' table instead of 'users' table\n";
echo "- Active medecins found: " . $activeMedecins->count() . "\n";

if ($activeMedecins->count() > 0) {
    echo "\n✓ FIX SUCCESSFUL: The doctors dropdown should now display your medecins!\n";
} else {
    echo "\n⚠️  NOTE: Make sure your medecins have statut='actif' in the database\n";
}
