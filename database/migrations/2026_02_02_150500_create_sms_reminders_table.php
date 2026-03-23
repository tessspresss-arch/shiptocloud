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
        if (!Schema::hasTable('sms_reminders')) {
            Schema::create('sms_reminders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rendezvous_id')->constrained('rendez_vous')->onDelete('cascade');
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->string('telephone', 20);
                $table->integer('heures_avant')->default(24); // Nb d'heures avant le RDV
                $table->enum('statut', ['planifie', 'envoye', 'echec', 'desactive'])->default('planifie');
                $table->dateTime('date_envoi_prevue')->nullable();
                $table->dateTime('date_envoi_reelle')->nullable();
                $table->text('message_template')->nullable();
                $table->string('code_erreur')->nullable();
                $table->text('erreur_message')->nullable();
                $table->string('provider', 50)->default('twilio'); // twilio, aws-sns, etc
                $table->string('provider_id')->nullable(); // ID du message chez le provider
                $table->timestamps();
                
                $table->index(['rendezvous_id', 'statut']);
                $table->index('patient_id');
                $table->index('date_envoi_prevue');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_reminders');
    }
};
