<?php

declare(strict_types=1);

use App\Models\Medecin;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$payload = [
    'civilite' => 'Dr.',
    'nom' => 'Medecin',
    'prenom' => 'E2E',
    'specialite' => 'Medecine generale',
    'telephone' => '+212600000301',
    'email' => 'e2e.medecins@medisys.test',
    'adresse_cabinet' => 'Fixture medecins premium',
    'ville' => 'Casablanca',
    'code_postal' => '20000',
    'numero_ordre' => 'ORD-E2E-0001',
    'tarif_consultation' => 300.00,
    'statut' => 'actif',
    'notes' => 'Medecin de fixture pour les tests UI.',
    'date_embauche' => '2020-01-01',
];

if (Schema::hasColumn('medecins', 'matricule')) {
    $payload['matricule'] = 'MED-E2E-0001';
}

$medecin = Medecin::withTrashed()->updateOrCreate(
    ['email' => 'e2e.medecins@medisys.test'],
    $payload
);

if (method_exists($medecin, 'restore') && $medecin->trashed()) {
    $medecin->restore();
}

fwrite(STDOUT, json_encode([
    'medecin_id' => $medecin->id,
], JSON_THROW_ON_ERROR));
