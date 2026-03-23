<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function legacy(string $escaped): string
    {
        return json_decode('"' . $escaped . '"', true, 512, JSON_THROW_ON_ERROR);
    }

    private function supportsEnumAlter(): bool
    {
        $driver = DB::connection()->getDriverName();

        return in_array($driver, ['mysql', 'mariadb'], true);
    }

    /**
     * @return array<string>
     */
    private function canonicalStatuses(): array
    {
        return ['a_venir', 'en_attente', 'en_soins', 'vu', 'absent', 'annule'];
    }

    public function up(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            return;
        }

        if ($this->supportsEnumAlter()) {
            DB::statement("ALTER TABLE rendez_vous MODIFY COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'a_venir'");
        }

        $variants = [
            'a_venir' => [
                'a_venir',
                $this->legacy('\u00c3\u00a0_venir'),
                $this->legacy('\u00c3\u0192\u00c2\u00a0_venir'),
                'programme',
                $this->legacy('programm\u00c3\u00a9'),
                $this->legacy('programm\u00c3\u0192\u00c2\u00a9'),
                'confirme',
                $this->legacy('confirm\u00c3\u00a9'),
                $this->legacy('confirm\u00c3\u0192\u00c2\u00a9'),
            ],
            'en_attente' => ['en_attente', 'attente', 'salle_attente', 'salle_d_attente'],
            'en_soins' => ['en_soins', 'consultation', 'en_consultation', 'salle_soin', 'salle_de_soin'],
            'vu' => [
                'vu',
                'termine',
                $this->legacy('termin\u00c3\u00a9'),
                'terminee',
                $this->legacy('termin\u00c3\u00a9e'),
                $this->legacy('termin\u00c3\u0192\u00c2\u00a9'),
                $this->legacy('termin\u00c3\u0192\u00c2\u00a9e'),
            ],
            'absent' => ['absent'],
            'annule' => [
                'annule',
                $this->legacy('annul\u00c3\u00a9'),
                $this->legacy('annul\u00c3\u0192\u00c2\u00a9'),
            ],
        ];

        foreach ($variants as $target => $values) {
            DB::table('rendez_vous')->whereIn('statut', $values)->update(['statut' => $target]);
        }

        DB::table('rendez_vous')->whereNull('statut')->update(['statut' => 'a_venir']);
        DB::table('rendez_vous')
            ->whereNotIn('statut', $this->canonicalStatuses())
            ->update(['statut' => 'a_venir']);

        if ($this->supportsEnumAlter()) {
            DB::statement("ALTER TABLE rendez_vous MODIFY COLUMN statut ENUM('a_venir', 'en_attente', 'en_soins', 'vu', 'absent', 'annule') NOT NULL DEFAULT 'a_venir'");
        }
    }

    public function down(): void
    {
        // Keep canonical statuses, no destructive rollback.
    }
};
