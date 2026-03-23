<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('examens')) {
            return;
        }

        Schema::table('examens', function (Blueprint $table) {
            if (!Schema::hasColumn('examens', 'resultats')) {
                $table->text('resultats')->nullable()->after('observations');
            }

            if (!Schema::hasColumn('examens', 'recommandations')) {
                $table->text('recommandations')->nullable()->after('resultats');
            }

            if (!Schema::hasColumn('examens', 'appareil')) {
                $table->string('appareil')->nullable()->after('lieu_realisation');
            }

            if (!Schema::hasColumn('examens', 'cout')) {
                $table->decimal('cout', 10, 2)->nullable()->after('recommandations');
            }

            if (!Schema::hasColumn('examens', 'payee')) {
                $table->boolean('payee')->default(false)->after('cout');
            }

            if (!Schema::hasColumn('examens', 'fichier_examen')) {
                $table->string('fichier_examen')->nullable()->after('document_resultat');
            }

            if (!Schema::hasColumn('examens', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('fichier_examen')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('examens', 'document_resultat') && Schema::hasColumn('examens', 'fichier_examen')) {
            DB::table('examens')
                ->whereNull('fichier_examen')
                ->whereNotNull('document_resultat')
                ->update(['fichier_examen' => DB::raw('document_resultat')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('examens')) {
            return;
        }

        Schema::table('examens', function (Blueprint $table) {
            if (Schema::hasColumn('examens', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            $columns = [];
            foreach (['fichier_examen', 'payee', 'cout', 'appareil', 'recommandations', 'resultats'] as $column) {
                if (Schema::hasColumn('examens', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
