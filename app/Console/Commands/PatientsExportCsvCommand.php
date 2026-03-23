<?php

namespace App\Console\Commands;

use App\Models\Patient;
use App\Services\PatientsCsvExportService;
use Illuminate\Console\Command;

class PatientsExportCsvCommand extends Command
{
    protected $signature = 'patients:export:csv
                            {--dry-run : Affiche un aperçu CSV en console sans HTTP}
                            {--delimiter= : Délimiteur CSV (ex: ; , | \\t)}
                            {--limit=3 : Nombre de lignes d\'aperçu en dry-run}';

    protected $description = 'Génère un aperçu du format CSV Patients conforme UI';

    public function handle(PatientsCsvExportService $csvService): int
    {
        $delimiter = $csvService->delimiter($this->option('delimiter'));
        $limit = max(1, (int) $this->option('limit'));

        $patients = Patient::query()
            ->select(['id', 'numero_dossier', 'nom', 'prenom', 'telephone', 'email', 'cin', 'date_naissance', 'genre'])
            ->latest('id')
            ->limit($limit)
            ->get();

        if (! $this->option('dry-run')) {
            $this->info('Commande prête. Utilisez --dry-run pour afficher un aperçu CSV.');
            return self::SUCCESS;
        }

        $stream = fopen('php://temp', 'w+');
        if ($stream === false) {
            $this->error('Impossible d\'ouvrir un flux temporaire.');
            return self::FAILURE;
        }

        fwrite($stream, "\xEF\xBB\xBF");
        $csvService->writeRows($stream, $patients, $delimiter);
        rewind($stream);

        $content = stream_get_contents($stream) ?: '';
        fclose($stream);

        $this->line('--- CSV PREVIEW START ---');
        $this->line($content);
        $this->line('--- CSV PREVIEW END ---');

        return self::SUCCESS;
    }
}
