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
        Schema::table('consultations', function (Blueprint $table) {
            $table->text('symptomes')->nullable()->change();
            $table->text('diagnostic')->nullable()->change();
            $table->text('traitement_prescrit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->text('symptomes')->nullable(false)->change();
            $table->text('diagnostic')->nullable(false)->change();
            $table->text('traitement_prescrit')->nullable(false)->change();
        });
    }
};
