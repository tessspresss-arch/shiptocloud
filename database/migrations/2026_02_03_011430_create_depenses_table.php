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
        if (!Schema::hasTable('depenses')) {
            Schema::create('depenses', function (Blueprint $table) {
                $table->id();
                $table->string('description');
                $table->text('details')->nullable();
                $table->decimal('montant', 10, 2);
                $table->date('date_depense');
                $table->enum('categorie', ['fournitures', 'medicaments', 'loyer', 'personnel', 'utilites', 'maintenance', 'formation', 'autre'])->default('autre');
                $table->string('beneficiaire')->nullable();
                $table->enum('statut', ['enregistre', 'payee', 'en_attente'])->default('enregistre');
                $table->string('facture_numero')->nullable();
                $table->string('mode_paiement')->nullable();
                $table->dateTime('date_paiement')->nullable();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index('date_depense');
                $table->index('categorie');
                $table->index('statut');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
