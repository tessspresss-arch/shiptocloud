<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Facture Routes ===\n\n";

// Test 1: Check if routes exist
echo "Test 1: Verifying route registration...\n";
$routes = ['factures.pdf', 'factures.envoyer', 'factures.update-statut'];
foreach ($routes as $routeName) {
    try {
        $url = route($routeName, ['facture' => 1]);
        echo "✅ Route '$routeName' exists: $url\n";
    } catch (Exception $e) {
        echo "❌ Route '$routeName' NOT FOUND: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Test 2: Check if facture exists with relations
echo "Test 2: Checking facture data...\n";
try {
    $facture = App\Models\Facture::with(['ligneFactures', 'patient', 'medecin'])->first();
    if ($facture) {
        echo "✅ Facture found: {$facture->numero_facture}\n";
        echo "   - Patient: {$facture->patient->nom} {$facture->patient->prenom}\n";
        echo "   - Status: {$facture->statut}\n";
        echo "   - Ligne factures count: " . $facture->ligneFactures->count() . "\n";
        echo "   - Montant total: {$facture->montant_total} €\n";
    } else {
        echo "❌ No facture found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading facture: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Test PDF generation (simulate)
echo "Test 3: Testing PDF generation capability...\n";
try {
    $facture = App\Models\Facture::with(['ligneFactures', 'patient', 'medecin'])->first();
    
    // Check if view exists
    if (view()->exists('factures.pdf')) {
        echo "✅ PDF view template exists\n";
        
        // Try to render the view
        $html = view('factures.pdf', compact('facture'))->render();
        echo "✅ PDF view renders successfully (" . strlen($html) . " bytes)\n";
    } else {
        echo "❌ PDF view template NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error rendering PDF view: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test email template
echo "Test 4: Testing email template...\n";
try {
    $facture = App\Models\Facture::with(['ligneFactures', 'patient', 'medecin'])->first();
    
    if (view()->exists('emails.facture')) {
        echo "✅ Email view template exists\n";
        
        // Try to render the view
        $html = view('emails.facture', compact('facture'))->render();
        echo "✅ Email view renders successfully (" . strlen($html) . " bytes)\n";
    } else {
        echo "❌ Email view template NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error rendering email view: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Check dompdf package
echo "Test 5: Checking dompdf package...\n";
try {
    if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
        echo "✅ DomPDF package is installed\n";
    } else {
        echo "❌ DomPDF package NOT FOUND\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking DomPDF: " . $e->getMessage() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "All critical path tests completed!\n";
echo "The RouteNotFoundException should now be resolved.\n";
