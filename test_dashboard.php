<?php
// Test 1 : Vérifier l'autoload
require __DIR__ . '/vendor/autoload.php';
echo "Autoload OK\n";

// Test 2 : Vérifier la classe
if (class_exists('App\Http\Controllers\DashboardController')) {
    echo "DashboardController existe\n";
} else {
    echo "DashboardController N'EXISTE PAS\n";

    // Vérifier le chemin
    $path = __DIR__ . '/app/Http/Controllers/DashboardController.php';
    if (file_exists($path)) {
        echo "Fichier trouvé à: $path\n";
        echo "Contenu:\n" . file_get_contents($path) . "\n";
    } else {
        echo "Fichier NON trouvé à: $path\n";
    }
}
