<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        $addArrivedAt = !Schema::hasColumn('rendez_vous', 'arrived_at');
        $addConsultationStartedAt = !Schema::hasColumn('rendez_vous', 'consultation_started_at');
        $addConsultationFinishedAt = !Schema::hasColumn('rendez_vous', 'consultation_finished_at');
        $addAbsentMarkedAt = !Schema::hasColumn('rendez_vous', 'absent_marked_at');

        if (!$addArrivedAt && !$addConsultationStartedAt && !$addConsultationFinishedAt && !$addAbsentMarkedAt) {
            return;
        }

        Schema::table('rendez_vous', function (Blueprint $table) use (
            $addArrivedAt,
            $addConsultationStartedAt,
            $addConsultationFinishedAt,
            $addAbsentMarkedAt
        ) {
            if ($addArrivedAt) {
                $table->timestamp('arrived_at')->nullable()->after('statut');
            }
            if ($addConsultationStartedAt) {
                $table->timestamp('consultation_started_at')->nullable()->after('arrived_at');
            }
            if ($addConsultationFinishedAt) {
                $table->timestamp('consultation_finished_at')->nullable()->after('consultation_started_at');
            }
            if ($addAbsentMarkedAt) {
                $table->timestamp('absent_marked_at')->nullable()->after('consultation_finished_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        $dropArrivedAt = Schema::hasColumn('rendez_vous', 'arrived_at');
        $dropConsultationStartedAt = Schema::hasColumn('rendez_vous', 'consultation_started_at');
        $dropConsultationFinishedAt = Schema::hasColumn('rendez_vous', 'consultation_finished_at');
        $dropAbsentMarkedAt = Schema::hasColumn('rendez_vous', 'absent_marked_at');

        if (!$dropArrivedAt && !$dropConsultationStartedAt && !$dropConsultationFinishedAt && !$dropAbsentMarkedAt) {
            return;
        }

        Schema::table('rendez_vous', function (Blueprint $table) use (
            $dropArrivedAt,
            $dropConsultationStartedAt,
            $dropConsultationFinishedAt,
            $dropAbsentMarkedAt
        ) {
            $toDrop = [];
            if ($dropArrivedAt) {
                $toDrop[] = 'arrived_at';
            }
            if ($dropConsultationStartedAt) {
                $toDrop[] = 'consultation_started_at';
            }
            if ($dropConsultationFinishedAt) {
                $toDrop[] = 'consultation_finished_at';
            }
            if ($dropAbsentMarkedAt) {
                $toDrop[] = 'absent_marked_at';
            }

            if ($toDrop !== []) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
