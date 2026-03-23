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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories_depenses')->onDelete('restrict');
            $table->string('description', 255);
            $table->decimal('montant', 10, 2);
            $table->dateTime('date_depense');
            $table->string('methode_paiement', 50)->default('especes'); // especes, cheque, carte, virement
            $table->string('reference_paiement', 100)->nullable(); // N° cheque, N° transaction
            $table->text('notes')->nullable();
            $table->string('piece_jointe')->nullable(); // URL du reçu/facture
            $table->boolean('is_documentee')->default(false);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['categorie_id', 'date_depense']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
