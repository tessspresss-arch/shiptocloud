<?php

namespace Tests\Feature\Medisys;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientCreationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_reception_can_create_patient_from_http_form(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'date_naissance' => '1990-05-20',
            'genre' => 'M',
            'cin' => 'QA123456',
            'telephone' => '63334455',
            'email' => 'ahmed.bennani@medisys.test',
            'adresse' => 'Boulevard Hassan II',
            'ville' => 'Casablanca',
            'groupe_sanguin' => 'A+',
            'assurance_medicale' => 'CNSS',
            'antecedents_medicaux' => 'RAS',
            'allergies' => 'Aucune',
        ]);

        $response->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', [
            'nom' => 'Bennani',
            'prenom' => 'Ahmed',
            'telephone' => '63334455',
            'cin' => 'QA123456',
        ]);
    }

    public function test_creation_persists_medical_history_and_postal_code(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'nom' => 'El Amrani',
            'prenom' => 'Nadia',
            'date_naissance' => '1988-11-21',
            'genre' => 'F',
            'cin' => 'ZA654321',
            'telephone' => '69988776',
            'email' => 'nadia.elamrani@medisys.test',
            'adresse' => '14 Rue Atlas',
            'ville' => 'Rabat',
            'code_postal' => '10000',
            'groupe_sanguin' => 'B+',
            'assurance_medicale' => 'Autre',
            'assurance_autre' => 'AssurTest',
            'antecedents_medicaux' => 'Hypertension',
            'allergies' => 'Aucune',
        ]);

        $response->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', [
            'nom' => 'El Amrani',
            'prenom' => 'Nadia',
            'code_postal' => '10000',
            'assurance' => 'AssurTest',
            'antecedents' => 'Hypertension',
        ]);
    }

    public function test_creation_supports_city_dropdown_with_manual_other_value(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'nom' => 'Bouziane',
            'prenom' => 'Salma',
            'date_naissance' => '1995-02-11',
            'genre' => 'F',
            'cin' => 'MC987654',
            'telephone' => '64444555',
            'email' => 'salma.bouziane@medisys.test',
            'adresse' => '10 Avenue Atlas',
            'ville_selection' => 'Autre',
            'ville_autre' => 'Azrou',
            'groupe_sanguin' => 'O+',
            'assurance_medicale' => 'CNSS',
        ]);

        $response->assertRedirect(route('patients.index'));

        $this->assertDatabaseHas('patients', [
            'nom' => 'Bouziane',
            'prenom' => 'Salma',
            'ville' => 'Azrou',
            'telephone' => '64444555',
        ]);
    }

    public function test_creation_rejects_phone_that_is_not_exactly_eight_digits(): void
    {
        $user = User::factory()->create([
            'role' => 'secretaire',
            'module_permissions' => ['patients' => true],
        ]);

        $response = $this->actingAs($user)
            ->from(route('patients.create'))
            ->post(route('patients.store'), [
                'nom' => 'Berrada',
                'prenom' => 'Nina',
                'date_naissance' => '1994-01-09',
                'genre' => 'F',
                'telephone' => '1234567',
            ]);

        $response
            ->assertRedirect(route('patients.create'))
            ->assertSessionHasErrors(['telephone']);

        $this->actingAs($user)
            ->followingRedirects()
            ->from(route('patients.create'))
            ->post(route('patients.store'), [
                'nom' => 'Berrada',
                'prenom' => 'Nina',
                'date_naissance' => '1994-01-09',
                'genre' => 'F',
                'telephone' => '1234567',
            ])
            ->assertSeeText('Le numero de telephone doit contenir exactement 8 chiffres.');
    }
}



