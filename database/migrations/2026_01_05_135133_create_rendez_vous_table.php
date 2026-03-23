<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            $table->datetime('date_heure');
            $table->integer('duree'); // en minutes
            $table->string('type', 50); // consultation, contrôle, urgence, etc.
            $table->string('motif', 1000)->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['programmé', 'confirmé', 'annulé', 'terminé'])->default('programmé');
            $table->timestamps();

            // Index pour les performances
            $table->index('date_heure');
            $table->index('statut');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rendez_vous');
    }
};
