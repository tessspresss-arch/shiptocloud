<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            // Vérifier si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('mouvement_stocks', 'medicament_id')) {
                $table->foreignId('medicament_id')->constrained('medicaments')->onDelete('cascade');
            }
            if (!Schema::hasColumn('mouvement_stocks', 'type_mouvement')) {
                $table->enum('type_mouvement', ['entree', 'sortie', 'ajustement', 'retour']);
            }
            if (!Schema::hasColumn('mouvement_stocks', 'quantite')) {
                $table->integer('quantite');
            }
            if (!Schema::hasColumn('mouvement_stocks', 'stock_avant')) {
                $table->integer('stock_avant')->nullable();
            }
            if (!Schema::hasColumn('mouvement_stocks', 'stock_apres')) {
                $table->integer('stock_apres')->nullable();
            }
            if (!Schema::hasColumn('mouvement_stocks', 'motif')) {
                $table->text('motif')->nullable();
            }
            if (!Schema::hasColumn('mouvement_stocks', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            $table->dropForeign(['medicament_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['medicament_id', 'type_mouvement', 'quantite', 'stock_avant', 'stock_apres', 'motif', 'user_id']);
        });
    }
};
