<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_medicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_archive_id')->constrained()->onDelete('cascade');
            $table->foreignId('categorie_document_id')->constrained()->onDelete('cascade');
            $table->string('nom_fichier', 255);
            $table->string('nom_original', 255);
            $table->string('chemin_fichier', 500);
            $table->string('mime_type', 100);
            $table->bigInteger('taille_fichier'); // Taille en octets
            $table->string('extension', 10);
            $table->text('description')->nullable();
            $table->date('date_document')->nullable(); // Date du document médical
            $table->string('auteur', 100)->nullable(); // Médecin ou professionnel
            $table->json('tags')->nullable(); // Mots-clés pour recherche
            $table->boolean('chiffre')->default(false); // Fichier chiffré
            $table->string('hash_fichier', 128)->nullable(); // Hash pour intégrité
            $table->integer('version')->default(1);
            $table->foreignId('document_parent_id')->nullable()->constrained('document_medicals')->onDelete('set null'); // Version précédente
            $table->boolean('supprime')->default(false);
            $table->timestamp('date_suppression')->nullable();
            $table->timestamps();

            $table->index(['patient_archive_id', 'categorie_document_id']);
            $table->index(['date_document']);
            $table->index(['supprime']);
            $table->index(['mime_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_medicals');
    }
};
