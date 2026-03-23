<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// ========== TEST COMPLET DES MODÈLES ==========

echo str_repeat("=", 50) . "\n";
echo "TEST COMPLET DES MODÈLES CABINET MÉDICAL\n";
echo str_repeat("=", 50) . "\n\n";

// NETTOYAGE PRÉALABLE
DB::table('prescriptions')->where('medicaments', 'LIKE', '%Test%')->delete();
DB::table('consultations')->where('symptomes', 'LIKE', '%Test%')->delete();
DB::table('rendez_vous')->where('motif', 'LIKE', '%Test%')->delete();
DB::table('patients')->where('email', 'LIKE', '%test%')->delete();
DB::table('medecins')->where('email', 'LIKE', '%test%')->delete();

// 1. TEST PATIENT
echo "1. TEST PATIENT:\n";
$patient = App\Models\Patient::create([
    'numero_dossier' => 'TEST' . time(),
    'nom' => 'TEST',
    'prenom' => 'Patient',
    'date_naissance' => '1990-05-20',
    'sexe' => 'F',
    'telephone' => '0111111111',
    'email' => 'patient.test@example.com',
    'numero_securite_sociale' => '1' . rand(10000000000000, 99999999999999),
    'adresse' => '1 Rue Test, Ville'
]);

echo "   ✓ Créé - ID: {$patient->id}\n";
echo "   ✓ Nom complet: {$patient->nom_complet}\n";
echo "   ✓ Âge: {$patient->age} ans\n";
echo "   ✓ Date Carbon: " . (get_class($patient->date_naissance) === 'Carbon\Carbon' ? 'OK' : 'ERREUR') . "\n";

// 2. TEST MEDECIN
echo "\n2. TEST MEDECIN:\n";
$medecin = App\Models\Medecin::create([
    'matricule' => 'MED' . rand(100, 999),
    'nom' => 'TEST',
    'prenom' => 'Docteur',
    'specialite' => 'Généraliste',
    'telephone' => '0222222222',
    'email' => 'medecin.test@example.com',
    'adresse_cabinet' => '2 Avenue Test, Ville',
    'heure_debut' => '08:30',
    'heure_fin' => '19:00',
    'jours_travail' => json_encode(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi']),
    'duree_consultation' => 20,
    'est_actif' => true
]);

$medecin->refresh();

echo "   ✓ Créé - ID: {$medecin->id}\n";
echo "   ✓ Nom complet: {$medecin->nom_complet}\n";
echo "   ✓ Horaires: {$medecin->horaires}\n";
echo "   ✓ Jours travail: " . implode(', ', $medecin->jours_travail) . "\n";
echo "   ✓ Actif: " . ($medecin->est_actif ? 'OUI' : 'NON') . "\n";

// 3. TEST RENDEZ-VOUS
echo "\n3. TEST RENDEZ-VOUS:\n";
$rdv = App\Models\RendezVous::create([
    'patient_id' => $patient->id,
    'medecin_id' => $medecin->id,
    'date_heure' => now()->addDays(3)->setTime(14, 0),
    'motif' => 'Consultation de test',
    'type' => 'consultation_suivi',
    'statut' => 'confirme',
    'duree' => 30
]);

echo "   ✓ Créé - ID: {$rdv->id}\n";
echo "   ✓ Date: {$rdv->date}\n";
echo "   ✓ Heure: {$rdv->heure}\n";
echo "   ✓ Passé: " . ($rdv->est_passe ? 'OUI' : 'NON') . "\n";

// 4. TEST RELATIONS RDV
echo "\n4. TEST RELATIONS RDV:\n";
echo "   ✓ Patient RDV count: " . $patient->rendezvous->count() . "\n";
echo "   ✓ Médecin RDV count: " . $medecin->rendezvous->count() . "\n";
echo "   ✓ RDV → Patient: " . ($rdv->patient->id === $patient->id ? 'OK' : 'ERREUR') . "\n";
echo "   ✓ RDV → Médecin: " . ($rdv->medecin->id === $medecin->id ? 'OK' : 'ERREUR') . "\n";

// 5. TEST CONSULTATION
echo "\n5. TEST CONSULTATION:\n";
$consultation = App\Models\Consultation::create([
    'rendez_vous_id' => $rdv->id,
    'patient_id' => $patient->id,
    'medecin_id' => $medecin->id,
    'date_consultation' => today(),
    'symptomes' => 'Fièvre, fatigue',
    'diagnostic' => 'Grippe',
    'poids' => 65.5,
    'taille' => 1.68,
    'tension_arterielle_systolique' => 125,
    'tension_arterielle_diastolique' => 82,
    'temperature' => 38.2,
    'traitement_prescrit' => 'Repos, hydratation, paracétamol'
]);

echo "   ✓ Créée - ID: {$consultation->id}\n";
echo "   ✓ IMC: " . ($consultation->imc ?? 'N/A') . "\n";
echo "   ✓ Tension: " . ($consultation->tension ?? 'N/A') . "\n";
echo "   ✓ Consultation → Patient: " . ($consultation->patient->id === $patient->id ? 'OK' : 'ERREUR') . "\n";
echo "   ✓ Consultation → Médecin: " . ($consultation->medecin->id === $medecin->id ? 'OK' : 'ERREUR') . "\n";

// 6. TEST PRESCRIPTION
echo "\n6. TEST PRESCRIPTION:\n";
$prescription = App\Models\Prescription::create([
    'consultation_id' => $consultation->id,
    'patient_id' => $patient->id,
    'medecin_id' => $medecin->id,
    'date_prescription' => today(),
    'numero_prescription' => 'PRE' . time(),
    'type_prescription' => 'medicament',
    'medicaments' => json_encode([
        ['nom' => 'Paracétamol', 'dosage' => '500mg', 'posologie' => '1g 3x/jour', 'duree' => '5 jours', 'quantite' => '15'],
        ['nom' => 'Vitamine C', 'dosage' => '1000mg', 'posologie' => '1x/jour', 'duree' => '7 jours', 'quantite' => '7']
    ]),
    'est_renouvelable' => true,
    'nombre_renouvellements' => 1,
    'duree_validite_jours' => 30,
    'statut' => 'active'
]);

echo "   ✓ Créée - ID: {$prescription->id}\n";
echo "   ✓ Valide: " . ($prescription->est_valide ? 'OUI' : 'NON') . "\n";
echo "   ✓ Médicaments count: " . count($prescription->medicaments) . "\n";
echo "   ✓ Renouvelable: " . ($prescription->est_renouvelable ? 'OUI' : 'NON') . "\n";

// 7. TEST SCOPES
echo "\n7. TEST SCOPES:\n";
echo "   ✓ RDV aujourd'hui: " . App\Models\RendezVous::today()->count() . "\n";
echo "   ✓ RDV confirmés: " . App\Models\RendezVous::confirmed()->count() . "\n";
echo "   ✓ RDV futurs: " . App\Models\RendezVous::future()->count() . "\n";

// 8. TEST RELATIONS FINALES
echo "\n8. TEST RELATIONS FINALES:\n";
echo "   ✓ Patient consultations: " . $patient->consultations->count() . "\n";
echo "   ✓ Patient prescriptions: " . $patient->prescriptions->count() . "\n";
echo "   ✓ Médecin consultations: " . $medecin->consultations->count() . "\n";
echo "   ✓ Médecin prescriptions: " . $medecin->prescriptions->count() . "\n";
echo "   ✓ RDV a consultation: " . ($rdv->consultation ? 'OUI' : 'NON') . "\n";

// 9. NETTOYAGE
echo "\n9. NETTOYAGE:\n";
$deleted = DB::table('prescriptions')->where('id', $prescription->id)->delete();
$deleted += DB::table('consultations')->where('id', $consultation->id)->delete();
$deleted += DB::table('rendez_vous')->where('id', $rdv->id)->delete();
$deleted += DB::table('patients')->where('id', $patient->id)->delete();
$deleted += DB::table('medecins')->where('id', $medecin->id)->delete();
echo "   ✓ {$deleted} enregistrements de test supprimés\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ TEST TERMINÉ AVEC SUCCÈS\n";
echo str_repeat("=", 50) . "\n";

