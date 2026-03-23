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
        Schema::table('ligne_ordonnances', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ligne_ordonnances', function (Blueprint $table) {
            $table->dropForeign(['ordonnance_id']);
            $table->dropForeign(['medicament_id']);
            $table->dropColumn(['ordonnance_id', 'medicament_id', 'posologie', 'duree', 'quantite', 'instructions']);
        });
    }
};
