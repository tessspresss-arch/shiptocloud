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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('rendezvous')) {
            return;
        }

        DB::statement("UPDATE rendezvous SET statut = 'à_venir' WHERE statut IN ('programmé', 'confirmé')");
        DB::statement("UPDATE rendezvous SET statut = 'vu' WHERE statut = 'terminé'");

        if ($this->supportsEnumAlter()) {
            DB::statement("ALTER TABLE rendezvous MODIFY COLUMN statut ENUM('programmé', 'confirmé', 'annulé', 'terminé') NOT NULL DEFAULT 'programmé'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('rendezvous')) {
            return;
        }

        DB::statement("UPDATE rendezvous SET statut = 'programmé' WHERE statut = 'à_venir'");
        DB::statement("UPDATE rendezvous SET statut = 'terminé' WHERE statut = 'vu'");

        if ($this->supportsEnumAlter()) {
            DB::statement("ALTER TABLE rendezvous MODIFY COLUMN statut ENUM('programmé', 'confirmé', 'annulé', 'terminé') NOT NULL DEFAULT 'programmé'");
        }
    }
};

