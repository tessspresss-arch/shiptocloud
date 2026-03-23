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
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'etat_civil')) {
                $table->string('etat_civil', 50)->nullable();
            }
            if (!Schema::hasColumn('patients', 'contact_urgence')) {
                $table->string('contact_urgence', 100)->nullable();
            }
            if (!Schema::hasColumn('patients', 'telephone_urgence')) {
                $table->string('telephone_urgence', 20)->nullable();
            }
            if (!Schema::hasColumn('patients', 'allergies')) {
                $table->text('allergies')->nullable();
            }
            if (!Schema::hasColumn('patients', 'traitements')) {
                $table->text('traitements')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'etat_civil')) {
                $table->dropColumn('etat_civil');
            }
            if (Schema::hasColumn('patients', 'contact_urgence')) {
                $table->dropColumn('contact_urgence');
            }
            if (Schema::hasColumn('patients', 'telephone_urgence')) {
                $table->dropColumn('telephone_urgence');
            }
            if (Schema::hasColumn('patients', 'allergies')) {
                $table->dropColumn('allergies');
            }
            if (Schema::hasColumn('patients', 'traitements')) {
                $table->dropColumn('traitements');
            }
        });
    }
};
