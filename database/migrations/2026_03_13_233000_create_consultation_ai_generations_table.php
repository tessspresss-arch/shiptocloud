<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action_type', 40);
            $table->string('suggested_target', 60)->nullable();
            $table->longText('source_text');
            $table->longText('generated_text');
            $table->json('context_payload')->nullable();
            $table->timestamps();

            $table->index(['consultation_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_ai_generations');
    }
};
