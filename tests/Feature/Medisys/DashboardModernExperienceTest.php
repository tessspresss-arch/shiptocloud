<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardModernExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_premium_sections_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Cockpit SaaS')
            ->assertSeeText('Pilotage du cabinet')
            ->assertSeeText('Performance financiere')
            ->assertSeeText("Flux clinique du jour")
            ->assertSeeText("Agenda a venir")
            ->assertSeeText("Consultations urgentes")
            ->assertSeeText("Snapshot financier");
    }
}
