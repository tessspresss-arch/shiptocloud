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
        if (!Schema::hasTable('ligne_factures') || !$this->supportsEnumAlter()) {
            return;
        }

        DB::statement("ALTER TABLE ligne_factures MODIFY COLUMN type ENUM('consultation', 'médicament', 'analyse', 'autre', 'prestation') DEFAULT 'consultation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ligne_factures') || !$this->supportsEnumAlter()) {
            return;
        }

        DB::statement("ALTER TABLE ligne_factures MODIFY COLUMN type ENUM('consultation', 'médicament', 'analyse', 'autre') DEFAULT 'consultation'");
    }
};

