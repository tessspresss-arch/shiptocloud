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
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id();
            $table->string('nom_commercial');
            $table->string('dci')->nullable(); // Dénomination Commune Internationale
            $table->string('code_cip')->unique(); // Code Identifiant de Présentation
            $table->string('code_medicament')->unique();
            $table->string('categorie')->nullable();
            $table->string('classe_therapeutique')->nullable(); // Classe thérapeutique ATC
            $table->string('laboratoire')->nullable(); // Laboratoire pharmaceutique
            $table->enum('type', ['prescription', 'otc', 'controlled'])->default('prescription');
            $table->integer('quantite_stock')->default(0); // Stock actuel
            $table->integer('quantite_seuil')->default(10); // Seuil d'alerte stock
            $table->integer('quantite_ideale')->default(50); // Stock idéal
            $table->decimal('prix_achat', 8, 2)->default(0); // Prix d'achat
            $table->decimal('prix_vente', 8, 2)->default(0); // Prix de vente
            $table->decimal('prix_remboursement', 8, 2)->nullable(); // Prix de remboursement sécurité sociale
            $table->integer('taux_remboursement')->default(0); // Taux de remboursement (%)
            $table->date('date_peremption')->nullable();
            $table->date('date_fabrication')->nullable();
            $table->string('numero_lot')->nullable();
            $table->string('fournisseur')->nullable();
            $table->string('presentation')->nullable(); // Forme galénique
            $table->string('voie_administration')->nullable();
            $table->text('posologie')->nullable();
            $table->text('contre_indications')->nullable();
            $table->text('effets_secondaires')->nullable();
            $table->text('interactions')->nullable(); // Interactions médicamenteuses
            $table->text('precautions')->nullable(); // Précautions d'emploi
            $table->text('conservation')->nullable(); // Conditions de conservation
            $table->enum('statut', ['actif', 'inactif', 'rupture', 'expired'])->default('actif');
            $table->boolean('generique')->default(false); // Médicament générique
            $table->boolean('remboursable')->default(true); // Remboursable par sécurité sociale
            $table->json('composants')->nullable(); // Composition détaillée
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes pour les performances
            $table->index(['statut', 'date_peremption']);
            $table->index(['quantite_stock', 'quantite_seuil']);
            $table->index('dci');
            $table->index('laboratoire');
            $table->index('classe_therapeutique');

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
