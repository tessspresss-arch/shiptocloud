<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Medecin;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facture>
 */
class FactureFactory extends Factory
{
    protected $model = Facture::class;

    public function definition(): array
    {
        $montant = $this->faker->randomFloat(2, 120, 1200);

        return [
            'numero_facture' => 'FAC-' . now()->format('Y') . '-' . $this->faker->unique()->numerify('######'),
            'patient_id' => Patient::factory(),
            'medecin_id' => Medecin::factory(),
            'date_facture' => now()->toDateString(),
            'date_echeance' => now()->addDays(15)->toDateString(),
            'montant_total' => $montant,
            'remise' => $this->faker->randomFloat(2, 0, 50),
            'statut' => 'en_attente',
            'mode_paiement' => null,
            'date_paiement' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
