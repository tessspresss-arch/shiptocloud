<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dossier_medicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->text('antecedents')->nullable();
            $table->text('allergies')->nullable();
            $table->text('traitements_courants')->nullable();
            $table->text('vaccinations')->nullable();
            $table->text('examens_complementaires')->nullable();
            $table->text('comptes_rendus')->nullable();
            $table->text('observations')->nullable();
            $table->enum('statut', ['actif', 'archive'])->default('actif');
            $table->date('date_creation')->nullable();
            $table->date('date_archive')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Index pour améliorer les performances
            $table->index('patient_id');
            $table->index('statut');
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dossier_medicals');
    }
};
