<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('document_medicals', ['created_at'], 'idx_document_medicals_created_at');
        $this->addIndexIfMissing('factures', ['statut'], 'idx_factures_statut');
        $this->addIndexIfMissing('factures', ['statut', 'date_facture'], 'idx_factures_statut_date_facture');
        $this->addIndexIfMissing('consultations', ['date_consultation'], 'idx_consultations_date_consultation');
        $this->addIndexIfMissing('depenses', ['date_depense'], 'idx_depenses_date_depense');
        $this->addIndexIfMissing('rendez_vous', ['medecin_id', 'date_heure'], 'idx_rendez_vous_medecin_date_heure');
        $this->addIndexIfMissing('patients', ['nom', 'prenom'], 'idx_patients_nom_prenom');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('document_medicals', 'idx_document_medicals_created_at');
        $this->dropIndexIfExists('factures', 'idx_factures_statut');
        $this->dropIndexIfExists('factures', 'idx_factures_statut_date_facture');
        $this->dropIndexIfExists('consultations', 'idx_consultations_date_consultation');
        $this->dropIndexIfExists('depenses', 'idx_depenses_date_depense');
        $this->dropIndexIfExists('rendez_vous', 'idx_rendez_vous_medecin_date_heure');
        $this->dropIndexIfExists('patients', 'idx_patients_nom_prenom');
    }

    private function addIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!Schema::hasTable($table) || $this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
            $tableBlueprint->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
            $tableBlueprint->dropIndex($indexName);
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?',
            [$table, $indexName]
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }
};
