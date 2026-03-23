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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // monthly, financial, patient, medicament
            $table->string('periode'); // e.g., "2024-01", "Q1 2024"
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('format')->default('pdf'); // pdf, excel, csv
            $table->string('file_path')->nullable(); // path to generated file
            $table->unsignedBigInteger('generated_by');
            $table->json('parameters')->nullable(); // additional parameters
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users');
            $table->index(['type', 'date_debut', 'date_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
