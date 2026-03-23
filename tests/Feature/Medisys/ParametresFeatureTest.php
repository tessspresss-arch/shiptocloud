<?php

namespace Tests\Feature\Medisys;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ParametresFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_cabinet_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $response = $this->actingAs($admin)->put(route('parametres.update'), [
            'section' => 'cabinet',
            'cabinet_address' => '123 Avenue Hassan II, Casablanca',
            'cabinet_city' => 'Casablanca',
            'cabinet_zip' => '20000',
            'cabinet_phone' => '+212600000000',
            'cabinet_email' => 'contact@cabinet.test',
            'cabinet_website' => 'https://cabinet.test',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('123 Avenue Hassan II, Casablanca', Setting::where('key', 'cabinet_address')->value('value'));
        $this->assertSame('+212600000000', Setting::where('key', 'cabinet_phone')->value('value'));
        $this->assertSame('https://cabinet.test', Setting::where('key', 'cabinet_website')->value('value'));
    }

    public function test_admin_can_download_an_existing_backup(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $directory = storage_path('app/backups/database/2026/03');
        File::ensureDirectoryExists($directory);
        $backupPath = $directory . DIRECTORY_SEPARATOR . 'backup_test_20260313.sql';
        File::put($backupPath, '-- test backup');

        try {
            $response = $this->actingAs($admin)->get(route('parametres.backup-download', [
                'file' => 'backups/database/2026/03/backup_test_20260313.sql',
            ]));

            $response
                ->assertOk()
                ->assertDownload('backup_test_20260313.sql');
        } finally {
            File::delete($backupPath);
        }
    }
}



