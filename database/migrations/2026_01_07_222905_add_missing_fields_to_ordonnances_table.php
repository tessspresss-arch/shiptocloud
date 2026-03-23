<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ordonnances', function (Blueprint $table) {
            if (!Schema::hasColumn('ordonnances', 'numero_ordonnance')) {
                $table->string('numero_ordonnance')->unique()->after('id');
            }
            if (!Schema::hasColumn('ordonnances', 'observations')) {
                $table->text('observations')->nullable()->after('diagnostic');
            }
            if (!Schema::hasColumn('ordonnances', 'date_expiration')) {
                $table->date('date_expiration')->nullable()->after('date_prescription');
            }
            // Handle medecin_id foreign key change - drop and recreate properly
            if (Schema::hasColumn('ordonnances', 'medecin_id')) {
                // Drop existing foreign key constraint if it exists
                try {
                    $table->dropForeign(['medecin_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                // Drop the column and recreate it with proper foreign key
                $table->dropColumn('medecin_id');
            }
            // Always add the medecin_id column with proper foreign key
            if (!Schema::hasColumn('ordonnances', 'medecin_id')) {
                $table->foreignId('medecin_id')->nullable()->constrained('medecins')->onDelete('set null');
            }
            if (Schema::hasColumn('ordonnances', 'statut')) {
                $table->dropColumn('statut'); // Supprimer l'ancien statut
            }
            if (!Schema::hasColumn('ordonnances', 'statut')) {
                $table->enum('statut', ['active', 'expiree', 'annulee'])->default('active')->after('observations');
            }
            if (Schema::hasColumn('ordonnances', 'imprimee')) {
                $table->dropColumn('imprimee'); // Supprimer l'ancienne colonne
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordonnances', function (Blueprint $table) {
            $table->dropColumn(['numero_ordonnance', 'observations', 'date_expiration']);
            $table->dropForeign(['medecin_id']);
            $table->foreignId('medecin_id')->nullable()->constrained('users')->onDelete('set null')->change();
            // Note: statut column remains as is
            $table->boolean('imprimee')->default(false);
        });
    }
};
