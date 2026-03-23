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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('telephone', 20);
            $table->text('message');
            $table->string('type', 50)->default('reminder'); // reminder, confirmation, notification, etc
            $table->enum('statut', ['envoye', 'echec', 'delivre'])->default('envoye');
            $table->string('provider', 50)->default('twilio');
            $table->string('provider_message_id')->nullable();
            $table->string('code_erreur')->nullable();
            $table->text('erreur_details')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['patient_id', 'statut']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
