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
        Schema::table('medicaments', function (Blueprint $table) {
            $table->string('dosage_standard')->nullable()->after('posologie');
            $table->string('frequence_standard')->nullable()->after('dosage_standard');
            $table->string('duree_standard')->nullable()->after('frequence_standard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicaments', function (Blueprint $table) {
            $table->dropColumn(['dosage_standard', 'frequence_standard', 'duree_standard']);
        });
    }
};
