#!/usr/bin/env php
<?php

use App\Models\Patient;
use App\Models\Medecin;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\LigneFacture;
use Carbon\Carbon;

// Créer les médecins
echo "📌 Création des médecins...\n";
$med1 = Medecin::create([
    'matricule' => 'MED-TEST-001',
    'civilite' => 'Dr.',
    'nom' => 'Bennani',
    'prenom' => 'Ahmed',
    'specialite' => 'Médecin Généraliste',
    'telephone' => '06 12 34 56 78',
    'email' => 'ahmed@cabinet.ma',
    'numero_ordre' => 'ORD-001',
    'adresse_cabinet' => '123 Avenue Hassan II',
    'ville' => 'Casablanca',
    'code_postal' => '20000',
    'tarif_consultation' => 300,
    'statut' => 'actif',
    'date_embauche' => '2020-01-15',
]);

$med2 = Medecin::create([
    'matricule' => 'MED-TEST-002',
    'civilite' => 'Dr.',
    'nom' => 'Alaoui',
    'prenom' => 'Fatima',
    'specialite' => 'Cardiologue',
    'telephone' => '06 23 45 67 89',
    'email' => 'fatima@cabinet.ma',
    'numero_ordre' => 'ORD-002',
    'adresse_cabinet' => '456 Boulevard Zaid',
    'ville' => 'Casablanca',
    'code_postal' => '20000',
    'tarif_consultation' => 400,
    'statut' => 'actif',
    'date_embauche' => '2021-03-20',
]);

// Créer les patients
echo "👥 Création des patients...\n";
$patients = [];
$names = [
    ['nom' => 'Martin', 'prenom' => 'Jean'],
    ['nom' => 'Dupont', 'prenom' => 'Marie'],
    ['nom' => 'Bernard', 'prenom' => 'Pierre'],
    ['nom' => 'Thomas', 'prenom' => 'Sophie'],
];

foreach ($names as $i => $name) {
    $patients[] = Patient::create([
        'nom' => $name['nom'],
        'prenom' => $name['prenom'],
        'genre' => ($i % 2 == 0) ? 'M' : 'F',
        'date_naissance' => Carbon::now()->subYears(random_int(20, 70))->format('Y-m-d'),
        'telephone' => '06 ' . str_pad(random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
        'email' => strtolower($name['prenom'] . '.' . $name['nom'] . '@email.com'),
        'numero_dossier' => 'PAT-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
        'adresse' => 'Rue ' . fake()->lastName(),
        'ville' => 'Casablanca',
    ]);
}

// Créer les rendez-vous
echo "📅 Création des rendez-vous...\n";
$motifs = ['Visite routine', 'Consultation', 'Suivi', 'Bilan'];
foreach ($patients as $patient) {
    for ($i = 0; $i < 2; $i++) {
        $date = Carbon::now()->addDays(random_int(-15, 15))->setTime(random_int(9, 17), [0, 30][random_int(0, 1)]);
        RendezVous::create([
            'patient_id' => $patient->id,
            'medecin_id' => ($i % 2 == 0) ? $med1->id : $med2->id,
            'date_rdv' => $date,
            'motif' => $motifs[array_rand($motifs)],
            'statut' => $date->isPast() ? 'complété' : 'programmé',
        ]);
    }
}

// Créer les factures
echo "💰 Création des factures...\n";
foreach ($patients as $patient) {
    for ($i = 0; $i < 2; $i++) {
        $montant = random_int(300, 500);
        $fact = Facture::create([
            'patient_id' => $patient->id,
            'medecin_id' => ($i % 2 == 0) ? $med1->id : $med2->id,
            'numero_facture' => 'FAC-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'date_facture' => Carbon::now()->addMonths(-random_int(1, 3)),
            'date_echeance' => Carbon::now()->addDays(random_int(5, 30)),
            'statut' => ['payée', 'en attente', 'partiellement payée'][random_int(0, 2)],
            'montant_total' => $montant,
        ]);
        
        LigneFacture::create([
            'facture_id' => $fact->id,
            'description' => 'Consultation médicale',
            'type' => 'consultation',
            'montant_unitaire' => $montant,
            'quantite' => 1,
            'montant_total' => $montant,
        ]);
    }
}

echo "✅ Données créées avec succès!\n";
echo "   • 2 médecins\n";
echo "   • 4 patients\n";
echo "   • 8 rendez-vous\n";
echo "   • 8 factures\n";
