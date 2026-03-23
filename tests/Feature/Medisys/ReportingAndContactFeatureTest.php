<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingAndContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_monthly_report_excel_export_returns_a_download(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => ['rapports' => true],
        ]);

        $response = $this->actingAs($user)->post(route('rapports.monthly'), [
            'date_debut' => now()->startOfMonth()->toDateString(),
            'date_fin' => now()->endOfMonth()->toDateString(),
            'format' => 'excel',
        ]);

        $response->assertOk();
        $this->assertStringContainsString(
            'attachment; filename=rapport_monthly_',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_contacts_index_ignores_invalid_sort_parameters(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['contacts' => true],
        ]);

        $this->actingAs($user)
            ->get(route('contacts.index', [
                'sort_by' => 'drop table contacts',
                'sort_order' => 'sideways',
            ]))
            ->assertOk();
    }
}
