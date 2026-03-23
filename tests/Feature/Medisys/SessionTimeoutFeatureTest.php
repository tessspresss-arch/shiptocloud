<?php

namespace Tests\Feature\Medisys;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dynamic_session_lifetime_is_applied_from_settings(): void
    {
        Setting::create([
            'key' => 'session_timeout',
            'value' => '2',
            'type' => 'integer',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $this->actingAs($user)
            ->withSession(['last_activity_at' => now()->timestamp])
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertSame(2, config('session.lifetime'));
    }

    public function test_user_is_logged_out_after_configured_inactivity_timeout(): void
    {
        Setting::create([
            'key' => 'session_timeout',
            'value' => '2',
            'type' => 'integer',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['last_activity_at' => now()->subMinutes(3)->timestamp])
            ->get(route('dashboard'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', "Votre session a expire apres 2 minute(s) d'inactivite.");

        $this->assertGuest();
    }

    public function test_user_remains_logged_in_when_still_within_timeout_window(): void
    {
        Setting::create([
            'key' => 'session_timeout',
            'value' => '2',
            'type' => 'integer',
        ]);

        $user = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $this->actingAs($user)
            ->withSession(['last_activity_at' => now()->subMinute()->timestamp])
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
    }
}



