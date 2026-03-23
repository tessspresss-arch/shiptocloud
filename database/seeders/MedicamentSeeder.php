<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('data/medicaments_base.csv');

        if (!file_exists($csvPath)) {
            $this->command?->warn("Fichier CSV introuvable: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            $this->command?->error("Impossible de lire le CSV: {$csvPath}");
            return;
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            $this->command?->warn('CSV vide.');
            return;
        }

        $rows = [];
        $now = now();
        $index = 1;

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 4) {
                continue;
            }

            [$nom, $classe, $forme, $dosage] = array_map(
                static fn ($value) => trim((string) $value),
                $data
            );

            if ($nom === '') {
                continue;
            }

            $rows[] = [
                'nom_commercial' => $nom,
                'dci' => $nom,
                'code_cip' => sprintf('CSV%09d', $index),
                'code_medicament' => sprintf('MED%06d', $index),
                'categorie' => 'Médicament',
                'classe_therapeutique' => $classe ?: null,
                'type' => 'prescription',
                'quantite_stock' => 0,
                'quantite_seuil' => 10,
                'quantite_ideale' => 50,
                'prix_achat' => 0,
                'prix_vente' => 0,
                'prix_remboursement' => null,
                'taux_remboursement' => null,
                'presentation' => $forme ?: null,
                'dosage_standard' => $dosage ?: null,
                'statut' => 'actif',
                'generique' => false,
                'remboursable' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $index++;
        }

        fclose($handle);

        if (empty($rows)) {
            $this->command?->warn('Aucune ligne exploitable dans le CSV.');
            return;
        }

        DB::table('medicaments')->upsert(
            $rows,
            ['code_medicament'],
            [
                'nom_commercial',
                'dci',
                'code_cip',
                'categorie',
                'classe_therapeutique',
                'type',
                'quantite_stock',
                'quantite_seuil',
                'quantite_ideale',
                'prix_achat',
                'prix_vente',
                'prix_remboursement',
                'taux_remboursement',
                'presentation',
                'dosage_standard',
                'statut',
                'generique',
                'remboursable',
                'updated_at',
            ]
        );

        $this->command?->info(count($rows) . ' médicaments importés/mis à jour depuis le CSV.');
    }
}
