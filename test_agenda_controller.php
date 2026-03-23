<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $controller = new App\Http\Controllers\RendezVousController();
    $request = new Illuminate\Http\Request();
    $result = $controller->agenda($request);
    echo "Controller executed successfully\n";
    echo "Result type: " . get_class($result) . "\n";

    if (method_exists($result, 'getData')) {
        $data = $result->getData();
        echo "Data keys: " . implode(', ', array_keys($data)) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
