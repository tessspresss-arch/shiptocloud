<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sensitive_routes_require_authentication(): void
    {
        $this->get('/agenda')->assertRedirect(route('login'));
        $this->get('/urgence')->assertRedirect(route('login'));
    }

    public function test_api_rendezvous_requires_authentication_for_json_requests(): void
    {
        $this->getJson('/api/rendezvous')->assertUnauthorized();
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_role_middleware_accepts_multiple_roles(): void
    {
        Route::middleware(['web', 'auth', 'role:admin,medecin,secretaire'])
            ->get('/_test-role-multi', fn () => response()->json(['ok' => true]));

        $user = User::factory()->create([
            'role' => 'medecin',
        ]);

        $this->actingAs($user)
            ->getJson('/_test-role-multi')
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_module_access_middleware_returns_json_403_when_access_denied(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['planning' => false],
        ]);

        $this->actingAs($user)
            ->getJson('/api/rendezvous')
            ->assertForbidden()
            ->assertJson([
                'message' => 'Acces refuse au module: planning',
            ]);
    }

    public function test_non_admin_cannot_manage_document_categories(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['documents' => true],
        ]);

        $this->actingAs($user)
            ->get('/documents/categories')
            ->assertForbidden();
    }

    public function test_routes_now_guarded_by_role_or_module_permissions(): void
    {
        $secretaire = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => [
                'planning' => false,
                'examens' => false,
                'patients' => false,
            ],
        ]);

        $this->actingAs($secretaire)->get('/infirmiers')->assertForbidden();
        $this->actingAs($secretaire)->get('/specialites')->assertForbidden();
        $this->actingAs($secretaire)->get('/gardes')->assertForbidden();
        $this->actingAs($secretaire)->get('/salles')->assertForbidden();
        $this->actingAs($secretaire)->get('/examen')->assertForbidden();
        $this->actingAs($secretaire)->get('/archives')->assertForbidden();
    }
}



