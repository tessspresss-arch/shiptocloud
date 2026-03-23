<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dossiers_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('numero_dossier', 50)->unique();
            $table->string('type', 50)->default('général');
            $table->date('date_ouverture')->nullable();
            $table->text('observations')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('traitement')->nullable();
            $table->text('prescriptions')->nullable();
            $table->string('statut', 20)->default('actif');
            $table->json('documents')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'statut']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dossiers_medicaux');
    }
};
