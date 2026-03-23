<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('patients', 'code_postal')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->string('code_postal', 20)->nullable()->after('ville');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'code_postal')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('code_postal');
            });
        }
    }
};
