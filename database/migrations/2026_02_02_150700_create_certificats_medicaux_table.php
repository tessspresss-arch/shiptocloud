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
        Schema::create('certificats_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('medecins')->onDelete('restrict');
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->onDelete('set null');
            $table->string('type', 100); // Arrêt de travail, Justificatif, Incapacité, etc
            $table->dateTime('date_emission');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->integer('nombre_jours')->nullable(); // Durée en jours
            $table->text('motif');
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('est_transmis')->default(false);
            $table->dateTime('date_transmission')->nullable();
            $table->string('fichier_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['patient_id', 'medecin_id']);
            $table->index('date_emission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificats_medicaux');
    }
};
