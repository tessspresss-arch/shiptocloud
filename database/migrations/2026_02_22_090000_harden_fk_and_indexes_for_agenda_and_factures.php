<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureRendezVousForeignKeys();
        $this->ensureFacturesForeignKeys();
        $this->ensureRendezVousIndexes();
        $this->ensureFacturesIndexes();
    }

    public function down(): void
    {
        if (Schema::hasTable('rendez_vous')) {
            Schema::table('rendez_vous', function (Blueprint $table) {
                if ($this->constraintExists('rendez_vous', 'fk_rendez_vous_patient_id')) {
                    $table->dropForeign('fk_rendez_vous_patient_id');
                }

                if ($this->constraintExists('rendez_vous', 'fk_rendez_vous_medecin_id')) {
                    $table->dropForeign('fk_rendez_vous_medecin_id');
                }

                if ($this->indexExists('rendez_vous', 'idx_rendez_vous_patient_id')) {
                    $table->dropIndex('idx_rendez_vous_patient_id');
                }

                if ($this->indexExists('rendez_vous', 'idx_rendez_vous_medecin_id')) {
                    $table->dropIndex('idx_rendez_vous_medecin_id');
                }

                if ($this->indexExists('rendez_vous', 'idx_rendez_vous_date_heure')) {
                    $table->dropIndex('idx_rendez_vous_date_heure');
                }

                if ($this->indexExists('rendez_vous', 'idx_rendez_vous_date_rdv')) {
                    $table->dropIndex('idx_rendez_vous_date_rdv');
                }
            });
        }

        if (Schema::hasTable('factures')) {
            Schema::table('factures', function (Blueprint $table) {
                if ($this->constraintExists('factures', 'fk_factures_patient_id')) {
                    $table->dropForeign('fk_factures_patient_id');
                }

                if ($this->indexExists('factures', 'idx_factures_patient_id')) {
                    $table->dropIndex('idx_factures_patient_id');
                }

                if ($this->indexExists('factures', 'idx_factures_medecin_id')) {
                    $table->dropIndex('idx_factures_medecin_id');
                }

                if ($this->indexExists('factures', 'idx_factures_date_facture')) {
                    $table->dropIndex('idx_factures_date_facture');
                }
            });
        }
    }

    private function ensureRendezVousForeignKeys(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        if (!$this->foreignKeyExistsByColumn('rendez_vous', 'patient_id', 'patients')) {
            $orphans = (int) ($this->scalar(
                'SELECT COUNT(*) FROM rendez_vous rv LEFT JOIN patients p ON p.id = rv.patient_id WHERE p.id IS NULL'
            ) ?? 0);

            if ($orphans > 0) {
                throw new RuntimeException("Impossible d'ajouter FK rendez_vous.patient_id -> patients.id: {$orphans} enregistrements orphelins.");
            }

            Schema::table('rendez_vous', function (Blueprint $table) {
                $table->foreign('patient_id', 'fk_rendez_vous_patient_id')
                    ->references('id')
                    ->on('patients')
                    ->cascadeOnDelete();
            });
        }

        if (!$this->foreignKeyExistsByColumn('rendez_vous', 'medecin_id', 'medecins')) {
            $orphans = (int) ($this->scalar(
                'SELECT COUNT(*) FROM rendez_vous rv LEFT JOIN medecins m ON m.id = rv.medecin_id WHERE m.id IS NULL'
            ) ?? 0);

            if ($orphans > 0) {
                throw new RuntimeException("Impossible d'ajouter FK rendez_vous.medecin_id -> medecins.id: {$orphans} enregistrements orphelins.");
            }

            Schema::table('rendez_vous', function (Blueprint $table) {
                $table->foreign('medecin_id', 'fk_rendez_vous_medecin_id')
                    ->references('id')
                    ->on('medecins')
                    ->cascadeOnDelete();
            });
        }
    }

    private function ensureFacturesForeignKeys(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        if (!$this->foreignKeyExistsByColumn('factures', 'patient_id', 'patients')) {
            $orphans = (int) ($this->scalar(
                'SELECT COUNT(*) FROM factures f LEFT JOIN patients p ON p.id = f.patient_id WHERE p.id IS NULL'
            ) ?? 0);

            if ($orphans > 0) {
                throw new RuntimeException("Impossible d'ajouter FK factures.patient_id -> patients.id: {$orphans} enregistrements orphelins.");
            }

            Schema::table('factures', function (Blueprint $table) {
                $table->foreign('patient_id', 'fk_factures_patient_id')
                    ->references('id')
                    ->on('patients')
                    ->cascadeOnDelete();
            });
        }
    }

    private function ensureRendezVousIndexes(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        Schema::table('rendez_vous', function (Blueprint $table) {
            if (!$this->columnHasIndex('rendez_vous', 'patient_id')) {
                $table->index('patient_id', 'idx_rendez_vous_patient_id');
            }

            if (!$this->columnHasIndex('rendez_vous', 'medecin_id')) {
                $table->index('medecin_id', 'idx_rendez_vous_medecin_id');
            }

            if (!$this->columnHasIndex('rendez_vous', 'date_heure')) {
                $table->index('date_heure', 'idx_rendez_vous_date_heure');
            }

            if (Schema::hasColumn('rendez_vous', 'date_rdv') && !$this->columnHasIndex('rendez_vous', 'date_rdv')) {
                $table->index('date_rdv', 'idx_rendez_vous_date_rdv');
            }
        });
    }

    private function ensureFacturesIndexes(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            if (!$this->columnHasIndex('factures', 'patient_id')) {
                $table->index('patient_id', 'idx_factures_patient_id');
            }

            if (!$this->columnHasIndex('factures', 'medecin_id')) {
                $table->index('medecin_id', 'idx_factures_medecin_id');
            }

            if (!$this->columnHasIndex('factures', 'date_facture')) {
                $table->index('date_facture', 'idx_factures_date_facture');
            }
        });
    }

    private function foreignKeyExistsByColumn(string $table, string $column, string $referencedTable): bool
    {
        $count = $this->scalar(
            'SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME = ?',
            [$table, $column, $referencedTable]
        );

        return (int) $count > 0;
    }

    private function columnHasIndex(string $table, string $column): bool
    {
        $count = $this->scalar(
            'SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) $count > 0;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $count = $this->scalar(
            'SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?',
            [$table, $indexName]
        );

        return (int) $count > 0;
    }

    private function constraintExists(string $table, string $constraintName): bool
    {
        $count = $this->scalar(
            'SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = "FOREIGN KEY"',
            [$table, $constraintName]
        );

        return (int) $count > 0;
    }

    private function scalar(string $sql, array $bindings = []): mixed
    {
        $row = DB::selectOne($sql, $bindings);

        if (!$row) {
            return null;
        }

        $values = array_values((array) $row);

        return $values[0] ?? null;
    }
};

