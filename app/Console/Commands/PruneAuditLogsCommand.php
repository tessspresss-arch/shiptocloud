<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneAuditLogsCommand extends Command
{
    protected $signature = 'audit:prune {--days= : Nombre de jours de rétention des logs d\'audit (prioritaire sur la configuration)}';

    protected $description = 'Supprime les logs d\'audit plus anciens que la période de rétention configurée';

    public function handle(): int
    {
        $configuredDays = (int) Setting::get('audit.retention_days', 365);
        $optionDays = $this->option('days');
        $days = $optionDays !== null ? (int) $optionDays : $configuredDays;
        $days = max(1, $days);
        $threshold = Carbon::now()->subDays($days);

        $deleted = AuditLog::query()
            ->where('created_at', '<', $threshold)
            ->delete();

        $this->info("Audit prune terminé: {$deleted} ligne(s) supprimée(s), rétention={$days} jours.");

        return self::SUCCESS;
    }
}
