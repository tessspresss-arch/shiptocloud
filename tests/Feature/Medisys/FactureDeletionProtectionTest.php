<?php

namespace Tests\Feature\Medisys;

use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactureDeletionProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_invoice_cannot_be_deleted(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['facturation' => true],
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();

        $facture = Facture::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'statut' => "pay\u{00E9}e",
        ]);

        $response = $this->actingAs($user)->delete(route('factures.destroy', $facture->id));

        $response->assertRedirect(route('factures.index'));
        $this->assertDatabaseHas('factures', ['id' => $facture->id]);
    }

    public function test_unpaid_invoice_can_be_deleted(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['facturation' => true],
        ]);

        $patient = Patient::factory()->create();
        $medecin = Medecin::factory()->create();

        $facture = Facture::factory()->create([
            'patient_id' => $patient->id,
            'medecin_id' => $medecin->id,
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($user)->delete(route('factures.destroy', $facture->id));

        $response->assertRedirect(route('factures.index'));
        $this->assertDatabaseMissing('factures', ['id' => $facture->id]);
    }
}
