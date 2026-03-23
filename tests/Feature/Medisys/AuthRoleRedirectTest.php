<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'module_permissions' => [],
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_medecin_is_redirected_to_consultations_when_module_is_allowed(): void
    {
        $user = User::factory()->create([
            'role' => 'medecin',
            'module_permissions' => [
                'consultations' => true,
                'planning' => false,
                'patients' => false,
                'dashboard' => true,
            ],
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('consultations.index'));
    }
}



