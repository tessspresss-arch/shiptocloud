<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rendez_vous_status_histories')) {
            return;
        }

        Schema::create('rendez_vous_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendez_vous_id')->constrained('rendez_vous')->onDelete('cascade');
            $table->string('old_status', 40)->nullable();
            $table->string('new_status', 40);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 40)->default('manual');
            $table->string('notes', 255)->nullable();
            $table->timestamp('transitioned_at')->useCurrent();
            $table->timestamps();

            $table->index(['rendez_vous_id', 'transitioned_at'], 'idx_rdv_status_histories_rdv_time');
            $table->index('new_status', 'idx_rdv_status_histories_new_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous_status_histories');
    }
};
