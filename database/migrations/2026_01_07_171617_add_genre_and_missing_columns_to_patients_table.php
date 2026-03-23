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
        // Renommer 'sexe' en 'genre' si elle existe
        if (Schema::hasColumn('patients', 'sexe') && !Schema::hasColumn('patients', 'genre')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->renameColumn('sexe', 'genre');
            });
        }

        Schema::table('patients', function (Blueprint $table) {
            // Ajouter 'genre' si elle n'existe pas
            if (!Schema::hasColumn('patients', 'genre')) {
                $table->enum('genre', ['M', 'F'])->nullable();
            }

            // Ajouter les colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('patients', 'cin')) {
                $table->string('cin', 20)->nullable();
            }
            if (!Schema::hasColumn('patients', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }
            if (!Schema::hasColumn('patients', 'telephone')) {
                $table->string('telephone', 20)->nullable();
            }
            if (!Schema::hasColumn('patients', 'email')) {
                $table->string('email')->nullable()->unique();
            }
            if (!Schema::hasColumn('patients', 'adresse')) {
                $table->string('adresse')->nullable();
            }
            if (!Schema::hasColumn('patients', 'ville')) {
                $table->string('ville', 100)->nullable();
            }
            if (!Schema::hasColumn('patients', 'groupe_sanguin')) {
                $table->enum('groupe_sanguin', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            }
            if (!Schema::hasColumn('patients', 'assurance')) {
                $table->string('assurance')->nullable();
            }
            if (!Schema::hasColumn('patients', 'antecedents')) {
                $table->text('antecedents')->nullable();
            }
            if (!Schema::hasColumn('patients', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('patients', 'photo')) {
                $table->string('photo')->nullable();
            }
            if (!Schema::hasColumn('patients', 'is_draft')) {
                $table->boolean('is_draft')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Renommer 'genre' en 'sexe' si elle existe
            if (Schema::hasColumn('patients', 'genre')) {
                $table->renameColumn('genre', 'sexe');
            }

            // Supprimer les colonnes ajoutées si elles existent
            if (Schema::hasColumn('patients', 'cin')) {
                $table->dropColumn('cin');
            }
            if (Schema::hasColumn('patients', 'date_naissance')) {
                $table->dropColumn('date_naissance');
            }
            if (Schema::hasColumn('patients', 'telephone')) {
                $table->dropColumn('telephone');
            }
            if (Schema::hasColumn('patients', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('patients', 'adresse')) {
                $table->dropColumn('adresse');
            }
            if (Schema::hasColumn('patients', 'ville')) {
                $table->dropColumn('ville');
            }
            if (Schema::hasColumn('patients', 'groupe_sanguin')) {
                $table->dropColumn('groupe_sanguin');
            }
            if (Schema::hasColumn('patients', 'assurance')) {
                $table->dropColumn('assurance');
            }
            if (Schema::hasColumn('patients', 'antecedents')) {
                $table->dropColumn('antecedents');
            }
            if (Schema::hasColumn('patients', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('patients', 'photo')) {
                $table->dropColumn('photo');
            }
            if (Schema::hasColumn('patients', 'is_draft')) {
                $table->dropColumn('is_draft');
            }
        });
    }
};
