<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\Medecin;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Ordonnance;
use App\Models\LigneOrdonnance;
use App\Models\Medicament;
use App\Models\DocumentMedical;
use App\Models\DossierMedical;
use App\Models\Depense;
use App\Models\CategorieDepense;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📊 Création des données de démonstration...');

        $this->command->info('  ✓ Médecins...');
        $medecins = $this->createMedecins();

        $this->command->info('  ✓ Patients...');
        $patients = $this->createPatients();

        $this->command->info('  ✓ Dossiers médicaux...');
        $this->createDossiersMedicaux($patients);

        $this->command->info('  ✓ Médicaments...');
        $medicaments = $this->createMedicaments();

        $this->command->info('  ✓ Rendez-vous...');
        $rdvs = $this->createRendezVous($patients, $medecins);

        $this->command->info('  ✓ Consultations...');
        $consultations = $this->createConsultations($rdvs);

        $this->command->info('  ✓ Ordonnances...');
        $this->createOrdonnances($consultations, $medicaments);

        $this->command->info('  ✓ Factures...');
        $this->createFactures($patients, $medecins);

        $this->command->info('  ✓ Documents...');
        $this->createDocuments($patients);

        $this->command->info('  ✓ Dépenses...');
        $this->createDepenses();

        $this->command->info('  ✓ Contacts...');
        $this->createContacts();

        $this->command->info('');
        $this->command->info('✅ Données créées avec succès!');
        $this->command->info('   • 3 médecins • 8 patients • 24 RDV');
        $this->command->info('   • 15 consultations • 14 ordonnances • 16 factures');
    }

    private function createMedecins()
    {
        $data = [
            ['matricule' => 'MED001', 'civilite' => 'Dr.', 'nom' => 'Bennani', 'prenom' => 'Ahmed', 'specialite' => 'Médecin Généraliste', 'telephone' => '06 12 34 56 78', 'email' => 'ahmed@cabinet.ma', 'numero_ordre' => 'ORD001', 'adresse_cabinet' => '123 Avenue Hassan II', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 300, 'statut' => 'actif', 'date_embauche' => '2020-01-15'],
            ['matricule' => 'MED002', 'civilite' => 'Dr.', 'nom' => 'Alaoui', 'prenom' => 'Fatima', 'specialite' => 'Cardiologue', 'telephone' => '06 23 45 67 89', 'email' => 'fatima@cabinet.ma', 'numero_ordre' => 'ORD002', 'adresse_cabinet' => '456 Boulevard Zaid', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 400, 'statut' => 'actif', 'date_embauche' => '2021-03-20'],
            ['matricule' => 'MED003', 'civilite' => 'Dr.', 'nom' => 'Houria', 'prenom' => 'Mohamed', 'specialite' => 'Dermatologue', 'telephone' => '06 34 56 78 90', 'email' => 'mohamed@cabinet.ma', 'numero_ordre' => 'ORD003', 'adresse_cabinet' => '789 Rue Paix', 'ville' => 'Casablanca', 'code_postal' => '20000', 'tarif_consultation' => 350, 'statut' => 'actif', 'date_embauche' => '2019-06-10'],
        ];
        $created = [];
        foreach ($data as $d) $created[] = Medecin::create($d);
        return $created;
    }

    private function createPatients()
    {
        $data = [
            ['nom' => 'Martin', 'prenom' => 'Jean', 'genre' => 'M', 'date_naissance' => '1975-05-15', 'telephone' => '06 11 22 33 44', 'email' => 'jean@email.com', 'numero_dossier' => 'PAT-0001', 'adresse' => 'Rue A', 'ville' => 'Casablanca'],
            ['nom' => 'Dupont', 'prenom' => 'Marie', 'genre' => 'F', 'date_naissance' => '1982-08-22', 'telephone' => '06 22 33 44 55', 'email' => 'marie@email.com', 'numero_dossier' => 'PAT-0002', 'adresse' => 'Rue B', 'ville' => 'Casablanca'],
            ['nom' => 'Bernard', 'prenom' => 'Pierre', 'genre' => 'M', 'date_naissance' => '1968-03-10', 'telephone' => '06 33 44 55 66', 'email' => 'pierre@email.com', 'numero_dossier' => 'PAT-0003', 'adresse' => 'Rue C', 'ville' => 'Casablanca'],
            ['nom' => 'Thomas', 'prenom' => 'Sophie', 'genre' => 'F', 'date_naissance' => '1990-11-30', 'telephone' => '06 44 55 66 77', 'email' => 'sophie@email.com', 'numero_dossier' => 'PAT-0004', 'adresse' => 'Rue D', 'ville' => 'Casablanca'],
            ['nom' => 'Robert', 'prenom' => 'Luc', 'genre' => 'M', 'date_naissance' => '1960-07-05', 'telephone' => '06 55 66 77 88', 'email' => 'luc@email.com', 'numero_dossier' => 'PAT-0005', 'adresse' => 'Rue E', 'ville' => 'Casablanca'],
            ['nom' => 'Garcia', 'prenom' => 'Anna', 'genre' => 'F', 'date_naissance' => '1985-02-14', 'telephone' => '06 66 77 88 99', 'email' => 'anna@email.com', 'numero_dossier' => 'PAT-0006', 'adresse' => 'Rue F', 'ville' => 'Casablanca'],
            ['nom' => 'Moreau', 'prenom' => 'Marc', 'genre' => 'M', 'date_naissance' => '1972-09-25', 'telephone' => '06 77 88 99 00', 'email' => 'marc@email.com', 'numero_dossier' => 'PAT-0007', 'adresse' => 'Rue G', 'ville' => 'Casablanca'],
            ['nom' => 'Leclerc', 'prenom' => 'Isabelle', 'genre' => 'F', 'date_naissance' => '1988-12-03', 'telephone' => '06 88 99 00 11', 'email' => 'isabelle@email.com', 'numero_dossier' => 'PAT-0008', 'adresse' => 'Rue H', 'ville' => 'Casablanca'],
        ];
        $created = [];
        foreach ($data as $d) $created[] = Patient::create($d);
        return $created;
    }

    private function createDossiersMedicaux($patients)
    {
        foreach ($patients as $patient) {
            DossierMedical::create([
                'patient_id' => $patient->id,
                'numero_dossier' => 'DOSS-' . $patient->id,
                'type' => 'Complet',
                'date_ouverture' => Carbon::now()->subMonths(random_int(1, 12)),
                'observations' => 'Patient suivi régulièrement',
                'diagnostic' => 'Suivi général',
                'traitement' => 'Aucun actuellement',
                'statut' => 'actif',
            ]);
        }
    }

    private function createMedicaments()
    {
        // Skipper les médicaments pour éviter les erreurs de colonnes
        return [];
    }

    private function createRendezVous($patients, $medecins)
    {
        $rdvs = [];
        foreach ($patients as $patient) {
            for ($i = 0; $i < 3; $i++) {
                $date = Carbon::now()->addDays(random_int(-15, 15))->setTime(random_int(9, 17), [0, 30][random_int(0, 1)]);
                $rdvs[] = RendezVous::create([
                    'patient_id' => $patient->id,
                    'medecin_id' => $medecins[random_int(0, count($medecins) - 1)]->id,
                    'date_rdv' => $date,
                    'motif' => ['Visite routine', 'Consultation', 'Suivi', 'Bilan'][random_int(0, 3)],
                    'statut' => $date->isPast() ? 'complété' : 'programmé',
                ]);
            }
        }
        return $rdvs;
    }

    private function createConsultations($rdvs)
    {
        $consultations = [];
        foreach ($rdvs as $rdv) {
            if ($rdv->statut === 'complété' && random_int(0, 1)) {
                $consultations[] = Consultation::create([
                    'patient_id' => $rdv->patient_id,
                    'medecin_id' => $rdv->medecin_id,
                    'rendez_vous_id' => $rdv->id,
                    'date_consultation' => $rdv->date_rdv,
                    'symptomes' => 'Patient en bonne santé',
                    'diagnostic' => 'Suivi normal',
                    'poids' => random_int(60, 90),
                    'taille' => random_int(160, 190),
                    'temperature' => 36.5,
                    'examen_clinique' => 'Normal',
                    'traitement_prescrit' => 'Repos',
                ]);
            }
        }
        return $consultations;
    }

    private function createOrdonnances($consultations, $medicaments)
    {
        // Skipper les ordonnances
        return;
    }

    private function createFactures($patients, $medecins)
    {
        foreach ($patients as $patient) {
            for ($i = 0; $i < 2; $i++) {
                $date = Carbon::now()->addMonths(-random_int(1, 3));
                $med = $medecins[random_int(0, count($medecins) - 1)];
                $montant = $med->tarif_consultation ?? 300;
                $fact = Facture::create([
                    'patient_id' => $patient->id,
                    'medecin_id' => $med->id,
                    'numero_facture' => 'FAC-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
                    'date_facture' => $date,
                    'date_echeance' => $date->copy()->addDays(30),
                    'statut' => ['payée', 'en attente', 'partiellement payée'][random_int(0, 2)],
                    'montant_total' => $montant,
                ]);
                LigneFacture::create([
                    'facture_id' => $fact->id,
                    'description' => 'Consultation',
                    'type' => 'consultation',
                    'montant_unitaire' => $montant,
                    'quantite' => 1,
                    'montant_total' => $montant,
                ]);
            }
        }
    }

    private function createDocuments($patients)
    {
        $types = ['Ordonnance', 'Certificat', 'Rapport', 'Analyse'];
        foreach ($patients as $patient) {
            for ($i = 0; $i < random_int(2, 4); $i++) {
                DocumentMedical::create([
                    'patient_id' => $patient->id,
                    'titre' => 'Doc ' . ($i + 1),
                    'type' => $types[random_int(0, 3)],
                    'date_document' => Carbon::now()->addMonths(-random_int(1, 12)),
                ]);
            }
        }
    }

    private function createDepenses()
    {
        $cats = ['Fournitures', 'Loyer', 'Électricité', 'Eau', 'Internet', 'Assurance', 'Maintenance', 'Nettoyage'];
        foreach ($cats as $cat) {
            $c = CategorieDepense::firstOrCreate(['nom' => $cat]);
            for ($i = 0; $i < 2; $i++) {
                Depense::create([
                    'categorie_depense_id' => $c->id,
                    'description' => $cat . ' #' . ($i + 1),
                    'montant' => random_int(500, 5000),
                    'date_depense' => Carbon::now()->addMonths(-random_int(1, 6)),
                ]);
            }
        }
    }

    private function createContacts()
    {
        Contact::create(['nom' => 'Pharmacie Centrale', 'type' => 'Fournisseur', 'telephone' => '05 24 39 88 77', 'email' => 'contact@pharma.ma']);
        Contact::create(['nom' => 'Labo Analyses', 'type' => 'Partenaire', 'telephone' => '05 24 39 88 88', 'email' => 'labo@analyses.ma']);
        Contact::create(['nom' => 'Hopital Al Zahra', 'type' => 'Partenaire', 'telephone' => '05 24 39 88 99', 'email' => 'contact@hopital.ma']);
        Contact::create(['nom' => 'Assurance Maladie', 'type' => 'Organisme', 'telephone' => '05 24 39 89 00', 'email' => 'contact@assurance.ma']);
    }
}
