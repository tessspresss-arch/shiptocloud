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
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('medecins')->onDelete('set null');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('set null');
            $table->string('nom_examen', 255);
            $table->text('description')->nullable();
            $table->enum('type', ['biologie', 'imagerie', 'endoscopie', 'autre'])->default('biologie');
            $table->enum('statut', ['demande', 'en_attente', 'termine', 'annule'])->default('demande');
            $table->dateTime('date_demande');
            $table->dateTime('date_realisation')->nullable();
            $table->string('lieu_realisation', 255)->nullable(); // Nom du labo/hôpital
            $table->text('observations')->nullable();
            $table->string('document_resultat')->nullable(); // URL du fichier PDF
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['patient_id', 'statut']);
            $table->index('medecin_id');
            $table->index('date_demande');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};
