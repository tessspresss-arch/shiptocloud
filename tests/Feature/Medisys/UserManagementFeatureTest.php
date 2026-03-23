<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_users_by_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        User::factory()->create([
            'name' => 'Utilisateur Actif',
            'role' => 'secretaire',
            'account_status' => 'actif',
        ]);

        User::factory()->create([
            'name' => 'Utilisateur Desactive',
            'role' => 'medecin',
            'account_status' => 'desactive',
        ]);

        $response = $this->actingAs($admin)->get(route('utilisateurs.index', [
            'status' => 'desactive',
        ]));

        $response
            ->assertOk()
            ->assertSee('Utilisateur Desactive')
            ->assertDontSee('Utilisateur Actif');
    }

    public function test_admin_can_toggle_a_user_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $target = User::factory()->create([
            'role' => 'secretaire',
            'account_status' => 'actif',
        ]);

        $response = $this->actingAs($admin)->post(route('utilisateurs.toggle-status', $target), [
            'status' => 'desactive',
        ]);

        $response
            ->assertRedirect(route('utilisateurs.index'))
            ->assertSessionHas('success');

        $this->assertSame('desactive', $target->fresh()->account_status);
    }

    public function test_admin_can_reset_a_user_password_and_force_rotation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $target = User::factory()->create([
            'role' => 'medecin',
            'account_status' => 'actif',
            'password' => 'AncienMotDePasse!9',
            'force_password_change' => false,
        ]);

        $previousHash = $target->password;

        $response = $this->actingAs($admin)->post(route('utilisateurs.reset-password', $target));

        $response
            ->assertRedirect(route('utilisateurs.edit', $target))
            ->assertSessionHas('generated_password');

        $generatedPassword = session('generated_password');

        $target->refresh();

        $this->assertTrue($target->force_password_change);
        $this->assertNotSame($previousHash, $target->password);
        $this->assertTrue(Hash::check($generatedPassword, $target->password));
    }

    public function test_admin_can_create_a_user_with_last_name_and_first_name(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $response = $this->actingAs($admin)->post(route('utilisateurs.store'), [
            'name' => 'ZARRIK',
            'first_name' => 'Mohamed',
            'email' => 'mohamed.zarrik@example.test',
            'role' => 'medecin',
            'password' => 'MotDePasse!234',
            'password_confirmation' => 'MotDePasse!234',
            'account_status' => 'actif',
            'ui_language' => 'fr',
            'timezone' => 'Africa/Casablanca',
            'notification_channel' => 'email',
        ]);

        $response
            ->assertRedirect(route('utilisateurs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'mohamed.zarrik@example.test',
            'name' => 'ZARRIK Mohamed',
        ]);
    }

    public function test_user_creation_password_errors_are_returned_in_french(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'actif',
        ]);

        $response = $this->from(route('utilisateurs.create'))
            ->actingAs($admin)
            ->post(route('utilisateurs.store'), [
                'name' => 'ZARRIK',
                'first_name' => 'Mohamed',
                'email' => 'short-pass@example.test',
                'role' => 'medecin',
                'password' => 'abc',
                'password_confirmation' => 'abc',
                'account_status' => 'actif',
                'ui_language' => 'fr',
                'timezone' => 'Africa/Casablanca',
                'notification_channel' => 'email',
            ]);

        $response
            ->assertRedirect(route('utilisateurs.create'))
            ->assertSessionHasErrors([
                'password' => 'Le champ mot de passe doit contenir au moins 12 caracteres.',
            ]);
    }
}



