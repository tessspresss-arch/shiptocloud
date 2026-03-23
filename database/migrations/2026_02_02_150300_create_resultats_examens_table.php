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
        Schema::create('resultats_examens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examen_id')->constrained('examens')->onDelete('cascade');
            $table->string('parametre', 255);
            $table->string('valeur', 100);
            $table->string('unite', 50)->nullable();
            $table->string('valeur_normale', 100)->nullable(); // Intervalle de normalité
            $table->enum('interpretation', ['normal', 'anormal', 'critique'])->default('normal');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('examen_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultats_examens');
    }
};
