<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function supportsEnumAlter(): bool
    {
        $driver = DB::connection()->getDriverName();

        return in_array($driver, ['mysql', 'mariadb'], true);
    }

    public function up(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        // Keep datetime columns synchronized for legacy data.
        DB::statement("UPDATE rendez_vous SET date_heure = date_rdv WHERE date_heure IS NULL AND date_rdv IS NOT NULL");
        DB::statement("UPDATE rendez_vous SET date_rdv = date_heure WHERE date_rdv IS NULL AND date_heure IS NOT NULL");

        // Normalize legacy statuses before enforcing enum values.
        DB::statement("UPDATE rendez_vous SET statut = 'à_venir' WHERE statut IN ('programmé', 'programme', 'confirmé', 'confirme')");
        DB::statement("UPDATE rendez_vous SET statut = 'vu' WHERE statut IN ('terminé', 'termine')");
        DB::statement("UPDATE rendez_vous SET statut = 'annulé' WHERE statut = 'annule'");

        if ($this->supportsEnumAlter()) {
            DB::statement("ALTER TABLE rendez_vous MODIFY COLUMN statut ENUM('à_venir', 'en_attente', 'en_soins', 'vu', 'absent', 'annulé') NOT NULL DEFAULT 'à_venir'");
        }
    }

    public function down(): void
    {
        // No destructive rollback: this migration only hardens data consistency.
    }
};

