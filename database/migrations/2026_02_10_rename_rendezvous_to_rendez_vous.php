<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommer la table sans perte de données
        if (Schema::hasTable('rendezvous') && !Schema::hasTable('rendez_vous')) {
            Schema::rename('rendezvous', 'rendez_vous');
        }
    }

    public function down(): void
    {
        // Revenir en arrière si besoin
        if (Schema::hasTable('rendez_vous') && !Schema::hasTable('rendezvous')) {
            Schema::rename('rendez_vous', 'rendezvous');
        }
    }
};
