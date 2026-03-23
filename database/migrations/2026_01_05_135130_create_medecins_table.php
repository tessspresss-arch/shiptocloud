<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medecins', function (Blueprint $table) {
            $table->id();
            $table->string('matricule')->unique()->nullable();
            $table->string('civilite')->default('Dr.');
            $table->string('nom');
            $table->string('prenom');
            $table->string('specialite')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('adresse_cabinet')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('numero_ordre')->nullable(); // Numéro d'ordre des médecins
            $table->string('signature_path')->nullable(); // Chemin vers la signature numérique
            $table->string('photo_path')->nullable(); // Photo du médecin
            $table->json('horaires_travail')->nullable(); // Horaires en JSON
            $table->json('jours_conges')->nullable(); // Jours de congés
            $table->decimal('tarif_consultation', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'en_conge', 'retraite'])->default('actif');
            $table->date('date_embauche')->nullable();
            $table->date('date_depart')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medecins');
    }
};
