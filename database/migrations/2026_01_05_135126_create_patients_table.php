<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dossier')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['M', 'F']);
            $table->string('lieu_naissance')->nullable();
            $table->string('adresse');
            $table->string('telephone');
            $table->string('email')->nullable()->unique();
            $table->string('cin')->nullable();
            $table->string('assurance')->nullable();
            $table->string('numero_mutuelle')->nullable();
            $table->text('antecedents')->nullable();
            $table->text('allergies')->nullable();
            $table->text('traitements_en_cours')->nullable();
            $table->string('personne_urgence')->nullable();
            $table->string('tel_urgence')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
