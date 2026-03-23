<?php

namespace App\Console\Commands;

use App\Models\RendezVous;
use App\Models\RendezVousStatusHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoMarkAbsentRendezVousCommand extends Command
{
    protected $signature = 'waiting-room:auto-absent 
        {--dry-run : Affiche seulement les rendez-vous concernes sans modifier la base}
        {--include-past-days : Inclut aussi les jours precedents (sinon: seulement aujourd\'hui)}';

    protected $description = "Marque automatiquement absents les patients non arrives 1h apres l'heure du rendez-vous";

    public function handle(): int
    {
        $timezone = config('app.timezone', 'UTC');
        $now = now($timezone);
        $threshold = $now->copy()->subHour();
        $dryRun = (bool) $this->option('dry-run');
        $includePastDays = (bool) $this->option('include-past-days');

        $query = RendezVous::query()
            ->where('statut', RendezVous::normalizeStatus('a_venir'))
            ->where('date_heure', '<=', $threshold)
            ->when(!$includePastDays, function ($q) use ($now) {
                $q->whereDate('date_heure', $now->toDateString());
            })
            ->orderBy('date_heure');

        $count = (clone $query)->count();
        if ($count === 0) {
            $this->info('Aucun rendez-vous a marquer absent.');
            return self::SUCCESS;
        }

        $this->info("Rendez-vous eligibles: {$count}");

        if ($dryRun) {
            $query->with(['patient:id,nom,prenom'])->limit(25)->get()->each(function (RendezVous $rdv) {
                $patientName = trim((string) (($rdv->patient->prenom ?? '') . ' ' . ($rdv->patient->nom ?? '')));
                $this->line(sprintf(
                    '#%d | %s | %s',
                    $rdv->id,
                    $patientName !== '' ? $patientName : 'Patient',
                    optional($rdv->date_heure)->format('d/m/Y H:i')
                ));
            });
            return self::SUCCESS;
        }

        $updatedCount = 0;
        $query->chunkById(100, function ($items) use (&$updatedCount, $now, $timezone) {
            /** @var RendezVous $rdv */
            foreach ($items as $rdv) {
                $oldStatus = RendezVous::normalizeStatus($rdv->statut) ?? RendezVous::normalizeStatus('a_venir');
                $newStatus = RendezVous::normalizeStatus('absent');
                if ($oldStatus === null || $newStatus === null || $oldStatus === $newStatus) {
                    continue;
                }

                $rdv->statut = $newStatus;
                if ($rdv->absent_marked_at === null) {
                    $rdv->absent_marked_at = Carbon::now($timezone);
                }
                $rdv->save();

                RendezVousStatusHistory::create([
                    'rendez_vous_id' => $rdv->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_by' => null,
                    'source' => 'scheduler_auto_absent',
                    'notes' => 'Auto absent apres 1 heure sans arrivee.',
                    'transitioned_at' => $now,
                ]);

                $updatedCount++;
            }
        });

        $this->info("Patients marques absents: {$updatedCount}");
        return self::SUCCESS;
    }
}
