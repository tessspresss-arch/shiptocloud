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
        if (!Schema::hasTable('examens')) {
            Schema::create('examens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
                $table->foreignId('medecin_id')->nullable()->constrained('users')->cascadeOnDelete();
                $table->string('type_examen');
                $table->text('description')->nullable();
                $table->date('date_examen');
                $table->text('resultats')->nullable();
                $table->text('observations')->nullable();
                $table->enum('statut', ['demande', 'en_cours', 'termine', 'annule'])->default('demande');
                $table->string('localisation')->nullable();
                $table->string('appareil')->nullable();
                $table->decimal('cout', 10, 2)->nullable();
                $table->boolean('payee')->default(false);
                $table->string('fichier_examen')->nullable();
                $table->text('recommandations')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index('patient_id');
                $table->index('medecin_id');
                $table->index('date_examen');
                $table->index('statut');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};
