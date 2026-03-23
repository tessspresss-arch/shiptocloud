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
        Schema::create('categorie_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->string('couleur', 7)->default('#6b7280'); // Code couleur hex
            $table->string('icone', 50)->default('fas fa-file'); // Classe FontAwesome
            $table->integer('duree_conservation_ans')->default(20); // Durée de conservation en années
            $table->boolean('confidentiel')->default(false); // Documents nécessitant chiffrement
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0); // Ordre d'affichage
            $table->timestamps();

            $table->index(['actif', 'ordre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie_documents');
    }
};
