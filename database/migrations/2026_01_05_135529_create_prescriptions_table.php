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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->foreignId('consultation_id')->constrained('consultations')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('medecins')->onDelete('cascade');

            // Informations prescription
            $table->date('date_prescription');
            $table->string('numero_prescription')->unique();
            $table->enum('type_prescription', ['medicament', 'examens', 'soins', 'kinesitherapie', 'orthophonie']);

            // Médicaments (stockage JSON pour flexibilité)
            $table->json('medicaments')->nullable()->comment('[{nom: "", dosage: "", posologie: "", duree: "", quantite: ""}]');

            // Examens complémentaires
            $table->json('examens_demandes')->nullable()->comment('[{type: "", raison: "", urgence: ""}]');

            // Soins et recommandations
            $table->text('soins_prescrits')->nullable();
            $table->text('recommandations')->nullable();

            // Renouvellement
            $table->boolean('est_renouvelable')->default(false);
            $table->integer('nombre_renouvellements')->default(0);
            $table->integer('duree_validite_jours')->default(30);

            // Statut
            $table->enum('statut', ['active', 'expiree', 'annulee', 'terminee'])->default('active');

            // Signature numérique
            $table->string('signature_medecin')->nullable();
            $table->timestamp('date_signature')->nullable();

            // Informations pharmacie
            $table->string('pharmacie_nom')->nullable();
            $table->string('pharmacie_adresse')->nullable();
            $table->date('date_delivrance')->nullable();

            $table->timestamps();

            // Index pour performances
            $table->index(['patient_id', 'date_prescription']);
            $table->index(['medecin_id', 'statut']);
            $table->index('numero_prescription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
