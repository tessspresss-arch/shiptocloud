<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'professional_phone')) {
                $table->string('professional_phone', 30)->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title', 120)->nullable()->after('professional_phone');
            }

            if (!Schema::hasColumn('users', 'speciality')) {
                $table->string('speciality', 120)->nullable()->after('job_title');
            }

            if (!Schema::hasColumn('users', 'order_number')) {
                $table->string('order_number', 120)->nullable()->after('speciality');
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 120)->nullable()->after('order_number');
            }

            if (!Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status', 20)->default('actif')->after('department');
            }

            if (!Schema::hasColumn('users', 'account_expires_at')) {
                $table->date('account_expires_at')->nullable()->after('account_status');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('account_expires_at');
            }

            if (!Schema::hasColumn('users', 'ui_language')) {
                $table->string('ui_language', 10)->default('fr')->after('avatar');
            }

            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone', 80)->default('Africa/Casablanca')->after('ui_language');
            }

            if (!Schema::hasColumn('users', 'notification_channel')) {
                $table->string('notification_channel', 20)->default('email')->after('timezone');
            }

            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('notification_channel');
            }

            if (!Schema::hasColumn('users', 'force_password_change')) {
                $table->boolean('force_password_change')->default(false)->after('two_factor_enabled');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('force_password_change');
            }

            if (!Schema::hasColumn('users', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'professional_phone',
                'job_title',
                'speciality',
                'order_number',
                'department',
                'account_status',
                'account_expires_at',
                'avatar',
                'ui_language',
                'timezone',
                'notification_channel',
                'two_factor_enabled',
                'force_password_change',
                'last_login_at',
                'last_activity_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

