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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('prenom', 150)->nullable();
            $table->enum('type', ['patient', 'laboratoire', 'fournisseur', 'hopital', 'assurance', 'autre'])->default('autre');
            $table->string('email', 150)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('telephone_secondaire', 20)->nullable();
            $table->string('adresse', 255)->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('codepostal', 10)->nullable();
            $table->string('entreprise', 255)->nullable();
            $table->string('fonction', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_actif')->default(true);
            $table->boolean('is_favorite')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('type');
            $table->index('is_actif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
