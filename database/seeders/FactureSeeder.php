<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facture;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Medecin;
use Carbon\Carbon;

class FactureSeeder extends Seeder
{
    public function run()
    {
        $consultations = Consultation::all();

        if ($consultations->isEmpty()) {
            return; // Skip if no consultations
        }

        $modesPaiement = ['espèces', 'carte', 'chèque', 'virement'];
        $statuts = ['payée', 'en_attente', 'annulée'];

        $factures = [];
        $numeroCounter = 1;

        foreach ($consultations as $consultation) {
            // Create invoice for about 90% of consultations
            if (rand(1, 10) <= 9) {
                $montant = rand(200, 1500); // 200 to 1500 DH
                $remise = rand(0, 5) === 0 ? rand(10, 50) : 0; // 10% chance of discount
                $dateFacture = Carbon::parse($consultation->date_consultation)->addDays(rand(0, 7));

                $statut = $statuts[array_rand($statuts)];
                $datePaiement = null;
                if ($statut === 'payée') {
                    $datePaiement = $dateFacture->copy()->addDays(rand(0, 30));
                }

                $factures[] = [
                    'numero_facture' => 'FAC-' . date('Y') . '-' . str_pad($numeroCounter++, 6, '0', STR_PAD_LEFT),
                    'patient_id' => $consultation->patient_id,
                    'medecin_id' => $consultation->medecin_id,
                    'date_facture' => $dateFacture,
                    'montant_total' => $montant,
                    'remise' => $remise,
                    'statut' => $statut,
                    'mode_paiement' => $statut === 'payée' ? $modesPaiement[array_rand($modesPaiement)] : null,
                    'date_paiement' => $datePaiement,
                    'notes' => rand(0, 1) ? 'Consultation médicale' : null,
                    'created_at' => $dateFacture,
                    'updated_at' => $datePaiement ?: $dateFacture,
                ];
            }
        }

        // Insert in chunks
        foreach (array_chunk($factures, 50) as $chunk) {
            Facture::insert($chunk);
        }
    }
}
