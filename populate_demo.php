<?php
require 'bootstrap/app.php';

$app = make(Illuminate\Contracts\Foundation\Application::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Facture;
use App\Models\LigneFacture;
use Carbon\Carbon;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
Medecin::truncate();
Patient::truncate();
RendezVous::truncate();
Facture::truncate();
LigneFacture::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Médecins
echo "✓ Médecins\n";
$doc1 = Medecin::create(['matricule' => 'D001', 'civilite' => 'Dr.', 'nom' => 'Bennani', 'prenom' => 'Ahmed', 'specialite' => 'Généraliste', 'telephone' => '0612345678', 'email' => 'ahmed@cabinet.ma', 'numero_ordre' => 'O001', 'adresse_cabinet' => '123 Ave', 'ville' => 'Casa', 'code_postal' => '20000', 'tarif_consultation' => 300, 'statut' => 'actif', 'date_embauche' => '2020-01-01']);
$doc2 = Medecin::create(['matricule' => 'D002', 'civilite' => 'Dr.', 'nom' => 'Alaoui', 'prenom' => 'Fatima', 'specialite' => 'Cardio', 'telephone' => '0623456789', 'email' => 'fatima@cabinet.ma', 'numero_ordre' => 'O002', 'adresse_cabinet' => '456 Blvd', 'ville' => 'Casa', 'code_postal' => '20000', 'tarif_consultation' => 400, 'statut' => 'actif', 'date_embauche' => '2021-01-01']);

// Patients
echo "✓ Patients\n";
$p1 = Patient::create(['nom' => 'Martin', 'prenom' => 'Jean', 'genre' => 'M', 'date_naissance' => '1975-05-15', 'telephone' => '0611223344', 'email' => 'jean@email.com', 'numero_dossier' => 'PAT001', 'adresse' => 'Rue A', 'ville' => 'Casa']);
$p2 = Patient::create(['nom' => 'Dupont', 'prenom' => 'Marie', 'genre' => 'F', 'date_naissance' => '1982-08-22', 'telephone' => '0622334455', 'email' => 'marie@email.com', 'numero_dossier' => 'PAT002', 'adresse' => 'Rue B', 'ville' => 'Casa']);
$p3 = Patient::create(['nom' => 'Bernard', 'prenom' => 'Pierre', 'genre' => 'M', 'date_naissance' => '1968-03-10', 'telephone' => '0633445566', 'email' => 'pierre@email.com', 'numero_dossier' => 'PAT003', 'adresse' => 'Rue C', 'ville' => 'Casa']);
$p4 = Patient::create(['nom' => 'Thomas', 'prenom' => 'Sophie', 'genre' => 'F', 'date_naissance' => '1990-11-30', 'telephone' => '0644556677', 'email' => 'sophie@email.com', 'numero_dossier' => 'PAT004', 'adresse' => 'Rue D', 'ville' => 'Casa']);

// RDV
echo "✓ Rendez-vous\n";
RendezVous::create(['patient_id' => $p1->id, 'medecin_id' => $doc1->id, 'date_rdv' => Carbon::now()->addDays(2), 'motif' => 'Consultation', 'statut' => 'programmé']);
RendezVous::create(['patient_id' => $p2->id, 'medecin_id' => $doc2->id, 'date_rdv' => Carbon::now()->addDays(1), 'motif' => 'Visite', 'statut' => 'programmé']);
RendezVous::create(['patient_id' => $p3->id, 'medecin_id' => $doc1->id, 'date_rdv' => Carbon::now()->subDays(1), 'motif' => 'Suivi', 'statut' => 'complété']);
RendezVous::create(['patient_id' => $p4->id, 'medecin_id' => $doc2->id, 'date_rdv' => Carbon::now()->subDays(5), 'motif' => 'Bilan', 'statut' => 'complété']);

// Factures
echo "✓ Factures\n";
$f1 = Facture::create(['patient_id' => $p1->id, 'medecin_id' => $doc1->id, 'numero_facture' => 'FAC-2026-0001', 'date_facture' => Carbon::now()->subMonths(1), 'date_echeance' => Carbon::now()->addDays(15), 'statut' => 'payée', 'montant_total' => 300]);
LigneFacture::create(['facture_id' => $f1->id, 'description' => 'Consultation', 'type' => 'consultation', 'montant_unitaire' => 300, 'quantite' => 1, 'montant_total' => 300]);

$f2 = Facture::create(['patient_id' => $p2->id, 'medecin_id' => $doc2->id, 'numero_facture' => 'FAC-2026-0002', 'date_facture' => Carbon::now()->subMonths(2), 'date_echeance' => Carbon::now()->addDays(10), 'statut' => 'en attente', 'montant_total' => 400]);
LigneFacture::create(['facture_id' => $f2->id, 'description' => 'Consultation', 'type' => 'consultation', 'montant_unitaire' => 400, 'quantite' => 1, 'montant_total' => 400]);

$f3 = Facture::create(['patient_id' => $p3->id, 'medecin_id' => $doc1->id, 'numero_facture' => 'FAC-2026-0003', 'date_facture' => Carbon::now(), 'date_echeance' => Carbon::now()->addDays(30), 'statut' => 'en attente', 'montant_total' => 300]);
LigneFacture::create(['facture_id' => $f3->id, 'description' => 'Consultation', 'type' => 'consultation', 'montant_unitaire' => 300, 'quantite' => 1, 'montant_total' => 300]);

echo "\n✅ Données créées:\n";
echo "   • 2 médecins\n";
echo "   • 4 patients\n";
echo "   • 4 rendez-vous\n";
echo "   • 3 factures\n";
