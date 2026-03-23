<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('factures') || Schema::hasColumn('factures', 'consultation_id')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            $table->foreignId('consultation_id')
                ->nullable()
                ->after('patient_id')
                ->constrained('consultations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('factures') || !Schema::hasColumn('factures', 'consultation_id')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('consultation_id');
        });
    }
};