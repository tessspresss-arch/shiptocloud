<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('account_status', 'suspendu')
            ->update(['account_status' => 'desactive']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('account_status', 'desactive')
            ->update(['account_status' => 'suspendu']);
    }
};
