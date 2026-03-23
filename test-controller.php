<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "<h2>Test du contrôleur FactureController</h2>";

// Chemin du fichier
$controllerPath = __DIR__.'/app/Http/Controllers/FactureController.php';

// 1. Vérifier l'existence du fichier
echo "1. Fichier contrôleur : ";
if (file_exists($controllerPath)) {
    echo "<span style='color:green'>✅ EXISTE</span><br>";
    echo "   Taille : " . filesize($controllerPath) . " octets<br>";
} else {
    echo "<span style='color:red'>❌ MANQUANT</span><br>";
    echo "   Cherché à : " . $controllerPath . "<br>";
    exit;
}

// 2. Vérifier le contenu
echo "2. Contenu du fichier :<br>";
$content = file_get_contents($controllerPath);
if (strpos($content, 'namespace App\Http\Controllers') !== false) {
    echo "   ✅ Namespace correct<br>";
} else {
    echo "   ❌ Problème de namespace<br>";
}

if (strpos($content, 'class FactureController') !== false) {
    echo "   ✅ Classe définie<br>";
} else {
    echo "   ❌ Classe non trouvée<br>";
}

// 3. Vérifier l'autoload
echo "3. Vérification autoload : ";
if (class_exists('App\Http\Controllers\FactureController')) {
    echo "<span style='color:green'>✅ CLASSE CHARGEABLE</span><br>";
} else {
    echo "<span style='color:red'>❌ CLASSE NON TROUVÉE</span><br>";

    // Essayer de charger manuellement
    require_once $controllerPath;
    if (class_exists('App\Http\Controllers\FactureController')) {
        echo "   ✅ Chargée manuellement avec succès<br>";
    }
}

// 4. Créer une instance
echo "4. Test d'instanciation : ";
try {
    $controller = new \App\Http\Controllers\FactureController();
    echo "<span style='color:green'>✅ INSTANCIATION RÉUSSIE</span><br>";
} catch (Exception $e) {
    echo "<span style='color:red'>❌ ERREUR : " . $e->getMessage() . "</span><br>";
}
