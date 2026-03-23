<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('secret_hash');
            $table->json('metadata')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('api_clients')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('token_hash')->unique();
            $table->json('scopes')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();

            $table->index(['client_id', 'revoked_at']);
        });

        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('api_clients')->cascadeOnDelete();
            $table->unsignedInteger('limit_per_minute')->default(60);
            $table->unsignedInteger('limit_per_day')->default(5000);
            $table->timestamps();

            $table->unique('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
        Schema::dropIfExists('api_tokens');
        Schema::dropIfExists('api_clients');
    }
};
