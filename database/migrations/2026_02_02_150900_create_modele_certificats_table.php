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
        Schema::create('modele_certificats', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->text('contenu_html');
            $table->string('type', 100);
            $table->foreignId('medecin_id')->nullable()->constrained('medecins')->onDelete('cascade');
            $table->boolean('est_template_general')->default(false);
            $table->boolean('is_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('medecin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modele_certificats');
    }
};
