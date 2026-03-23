use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Facture;
use App\Models\LigneFacture;
use Carbon\Carbon;

// Doctors
$doc1 = Medecin::firstOrCreate(['matricule' => 'DOC001'], ['civilite' => 'Dr.', 'nom' => 'Bennani', 'prenom' => 'Ahmed', 'specialite' => 'Généraliste', 'telephone' => '0612345678', 'email' => 'ahmed@cabinet.ma', 'numero_ordre' => 'O001', 'adresse_cabinet' => '123 Avenue Hassan', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 300, 'statut' => 'actif']);
$doc2 = Medecin::firstOrCreate(['matricule' => 'DOC002'], ['civilite' => 'Dr.', 'nom' => 'Alaoui', 'prenom' => 'Fatima', 'specialite' => 'Cardiologue', 'telephone' => '0623456789', 'email' => 'fatima@cabinet.ma', 'numero_ordre' => 'O002', 'adresse_cabinet' => '456 Boulevard Zaid', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 400, 'statut' => 'actif']);
$doc3 = Medecin::firstOrCreate(['matricule' => 'DOC003'], ['civilite' => 'Dr.', 'nom' => 'Houria', 'prenom' => 'Mohamed', 'specialite' => 'Dermatologue', 'telephone' => '0634567890', 'email' => 'mohamed@cabinet.ma', 'numero_ordre' => 'O003', 'adresse_cabinet' => '789 Rue Paix', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 350, 'statut' => 'actif']);

echo "✓ 3 doctors créés\n";

// Patients
$p1 = Patient::firstOrCreate(['numero_dossier' => 'PAT0001'], ['nom' => 'Martin', 'prenom' => 'Jean', 'genre' => 'M', 'date_naissance' => '1975-05-15', 'telephone' => '0611223344', 'email' => 'jean@email.com', 'adresse' => '42 Rue A', 'ville' => 'Casablanca']);
$p2 = Patient::firstOrCreate(['numero_dossier' => 'PAT0002'], ['nom' => 'Dupont', 'prenom' => 'Marie', 'genre' => 'F', 'date_naissance' => '1982-08-22', 'telephone' => '0622334455', 'email' => 'marie@email.com', 'adresse' => '42 Rue B', 'ville' => 'Casablanca']);
$p3 = Patient::firstOrCreate(['numero_dossier' => 'PAT0003'], ['nom' => 'Bernard', 'prenom' => 'Pierre', 'genre' => 'M', 'date_naissance' => '1968-03-10', 'telephone' => '0633445566', 'email' => 'pierre@email.com', 'adresse' => '42 Rue C', 'ville' => 'Casablanca']);
$p4 = Patient::firstOrCreate(['numero_dossier' => 'PAT0004'], ['nom' => 'Thomas', 'prenom' => 'Sophie', 'genre' => 'F', 'date_naissance' => '1990-11-30', 'telephone' => '0644556677', 'email' => 'sophie@email.com', 'adresse' => '42 Rue D', 'ville' => 'Casablanca']);
$p5 = Patient::firstOrCreate(['numero_dossier' => 'PAT0005'], ['nom' => 'Robert', 'prenom' => 'Luc', 'genre' => 'M', 'date_naissance' => '1960-07-05', 'telephone' => '0655667788', 'email' => 'luc@email.com', 'adresse' => '42 Rue E', 'ville' => 'Casablanca']);

echo "✓ 5 patients créés\n";

// Appointments
RendezVous::firstOrCreate(['patient_id' => $p1->id, 'medecin_id' => $doc1->id, 'date_rdv' => Carbon::now()->addDays(2)->setTime(10, 0)], ['motif' => 'Consultation générale', 'statut' => 'programmé']);
RendezVous::firstOrCreate(['patient_id' => $p2->id, 'medecin_id' => $doc2->id, 'date_rdv' => Carbon::now()->addDays(1)->setTime(14, 30)], ['motif' => 'Visite cardiaque', 'statut' => 'programmé']);
RendezVous::firstOrCreate(['patient_id' => $p3->id, 'medecin_id' => $doc1->id, 'date_rdv' => Carbon::now()->subDays(1)->setTime(11, 0)], ['motif' => 'Suivi médical', 'statut' => 'complété']);
RendezVous::firstOrCreate(['patient_id' => $p4->id, 'medecin_id' => $doc3->id, 'date_rdv' => Carbon::now()->subDays(5)->setTime(15, 0)], ['motif' => 'Bilan dermatologique', 'statut' => 'complété']);
RendezVous::firstOrCreate(['patient_id' => $p5->id, 'medecin_id' => $doc2->id, 'date_rdv' => Carbon::now()->addDays(3)->setTime(9, 30)], ['motif' => 'Consultation routine', 'statut' => 'programmé']);

echo "✓ 5 rendez-vous créés\n";

// Invoices
$f1 = Facture::firstOrCreate(['numero_facture' => 'FAC-2026-0001'], ['patient_id' => $p1->id, 'medecin_id' => $doc1->id, 'date_facture' => Carbon::now()->subMonths(1), 'date_echeance' => Carbon::now()->addDays(15), 'statut' => 'payée', 'montant_total' => 300]);
LigneFacture::firstOrCreate(['facture_id' => $f1->id, 'description' => 'Consultation'], ['type' => 'consultation', 'montant_unitaire' => 300, 'quantite' => 1, 'montant_total' => 300]);

$f2 = Facture::firstOrCreate(['numero_facture' => 'FAC-2026-0002'], ['patient_id' => $p2->id, 'medecin_id' => $doc2->id, 'date_facture' => Carbon::now()->subMonths(1), 'date_echeance' => Carbon::now()->addDays(10), 'statut' => 'en attente', 'montant_total' => 400]);
LigneFacture::firstOrCreate(['facture_id' => $f2->id, 'description' => 'Consultation cardiaque'], ['type' => 'consultation', 'montant_unitaire' => 400, 'quantite' => 1, 'montant_total' => 400]);

$f3 = Facture::firstOrCreate(['numero_facture' => 'FAC-2026-0003'], ['patient_id' => $p3->id, 'medecin_id' => $doc1->id, 'date_facture' => Carbon::now()->subDays(15), 'date_echeance' => Carbon::now()->addDays(20), 'statut' => 'payée', 'montant_total' => 300]);
LigneFacture::firstOrCreate(['facture_id' => $f3->id, 'description' => 'Suivi médical'], ['type' => 'consultation', 'montant_unitaire' => 300, 'quantite' => 1, 'montant_total' => 300]);

$f4 = Facture::firstOrCreate(['numero_facture' => 'FAC-2026-0004'], ['patient_id' => $p4->id, 'medecin_id' => $doc3->id, 'date_facture' => Carbon::now(), 'date_echeance' => Carbon::now()->addDays(30), 'statut' => 'en attente', 'montant_total' => 350]);
LigneFacture::firstOrCreate(['facture_id' => $f4->id, 'description' => 'Consultation dermatologie'], ['type' => 'consultation', 'montant_unitaire' => 350, 'quantite' => 1, 'montant_total' => 350]);

$f5 = Facture::firstOrCreate(['numero_facture' => 'FAC-2026-0005'], ['patient_id' => $p5->id, 'medecin_id' => $doc2->id, 'date_facture' => Carbon::now()->subDays(30), 'date_echeance' => Carbon::now(), 'statut' => 'partiellement payée', 'montant_total' => 400]);
LigneFacture::firstOrCreate(['facture_id' => $f5->id, 'description' => 'Consultation cardio'], ['type' => 'consultation', 'montant_unitaire' => 400, 'quantite' => 1, 'montant_total' => 400]);

echo "✓ 5 factures créées\n";

echo "\n✅ Données de démonstration créées!\n";
echo "   • 3 médecins\n";
echo "   • 5 patients\n";
echo "   • 5 rendez-vous\n";
echo "   • 5 factures\n";
echo "   Revenus: 1650 DH (2 payées + 1 partiellement payée)\n";
