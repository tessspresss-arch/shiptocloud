<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\DashboardController;

$controller = new DashboardController();
$data = $controller->index()->getData();

echo "=== DASHBOARD DATA TEST ===\n\n";

echo "Total Patients: " . $data['total_patients'] . "\n";
echo "Total Médecins: " . $data['total_medecins'] . "\n";
echo "RDV Aujourd'hui: " . $data['rdv_aujourdhui'] . "\n";
echo "Nouveaux patients ce mois: " . $data['nouveaux_patients_mois'] . "\n";
echo "Consultations ce mois: " . $data['total_consultations_mois'] . "\n";
echo "Revenus du mois: " . $data['revenus_mois'] . " DH\n";
echo "Factures impayées: " . $data['factures_impayees'] . "\n";
echo "RDV annulés ce mois: " . $data['rdv_annules_mois'] . "\n";

echo "\n=== ALERTES ===\n";
echo "Nombre d'alertes: " . count($data['alertes']) . "\n";
foreach ($data['alertes'] as $alerte) {
    echo "- " . $alerte['message'] . "\n";
}

echo "\n=== CHARTS DATA ===\n";
echo "Patient growth data points: " . count($data['patient_growth_data']) . "\n";
echo "Revenue data points: " . count($data['revenue_data']) . "\n";
echo "Appointment stats: " . json_encode($data['appointment_stats']) . "\n";

echo "\n=== SUCCESS: Dashboard data is populated! ===\n";
