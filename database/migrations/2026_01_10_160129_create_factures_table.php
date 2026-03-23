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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('medecins')->onDelete('set null');
            $table->date('date_facture');
            $table->decimal('montant_total', 10, 2);
            $table->decimal('remise', 10, 2)->default(0);
            $table->enum('statut', ['brouillon', 'en_attente', 'payée', 'annulée'])->default('brouillon');
            $table->enum('mode_paiement', ['espèces', 'carte', 'chèque', 'virement'])->nullable();
            $table->date('date_paiement')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
