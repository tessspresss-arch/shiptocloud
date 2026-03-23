<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['email', 'sms']);
            $table->string('code')->unique();
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->string('event')->index();
            $table->string('url');
            $table->string('secret')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->unsignedSmallInteger('retry_max_attempts')->default(3);
            $table->unsignedInteger('timeout_ms')->default(5000);
            $table->timestamps();

            $table->index(['direction', 'event']);
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('webhook_subscriptions')->cascadeOnDelete();
            $table->string('status')->index();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->json('payload')->nullable();
            $table->longText('response')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['subscription_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_subscriptions');
        Schema::dropIfExists('notification_templates');
    }
};
