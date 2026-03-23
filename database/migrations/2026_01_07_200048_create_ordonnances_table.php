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
        Schema::create('ordonnances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('consultation_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date_prescription');
            $table->text('diagnostic')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('statut', ['active', 'terminée', 'annulée'])->default('active');
            $table->boolean('imprimee')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordonnances');
    }
};
