<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendez_vous_id')->nullable()->constrained('rendez_vous')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->date('date_consultation');
            $table->text('symptomes');
            $table->text('diagnostic');
            $table->decimal('poids', 5, 2)->nullable();
            $table->decimal('taille', 5, 2)->nullable();
            $table->integer('tension_arterielle_systolique')->nullable();
            $table->integer('tension_arterielle_diastolique')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->text('examen_clinique')->nullable();
            $table->text('traitement_prescrit');
            $table->text('recommandations')->nullable();
            $table->date('date_prochaine_visite')->nullable();
            $table->timestamps();
            
            // Index
            $table->index(['patient_id', 'date_consultation']);
            $table->index('medecin_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('consultations');
    }
};
