<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modele_ordonnances', function (Blueprint $table) {
            if (!Schema::hasColumn('modele_ordonnances', 'categorie')) {
                $table->string('categorie', 120)->nullable()->after('nom');
            }

            if (!Schema::hasColumn('modele_ordonnances', 'diagnostic_contexte')) {
                $table->text('diagnostic_contexte')->nullable()->after('categorie');
            }

            if (!Schema::hasColumn('modele_ordonnances', 'instructions_generales')) {
                $table->text('instructions_generales')->nullable()->after('diagnostic_contexte');
            }

            if (!Schema::hasColumn('modele_ordonnances', 'medicaments_template')) {
                $table->json('medicaments_template')->nullable()->after('instructions_generales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('modele_ordonnances', function (Blueprint $table) {
            $columns = [
                'medicaments_template',
                'instructions_generales',
                'diagnostic_contexte',
                'categorie',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('modele_ordonnances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
