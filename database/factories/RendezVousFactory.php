<?php

namespace Database\Factories;

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RendezVous>
 */
class RendezVousFactory extends Factory
{
    protected $model = RendezVous::class;

    public function definition(): array
    {
        $dateHeure = now()->addDay()->setTime(10, 0)->addMinutes($this->faker->numberBetween(0, 6) * 30);

        return [
            'patient_id' => Patient::factory(),
            'medecin_id' => Medecin::factory(),
            'date_heure' => $dateHeure,
            'duree' => 30,
            'type' => $this->faker->randomElement([
                'Consultation generale',
                'Controle',
                'Urgence',
                'Vaccination',
            ]),
            'motif' => $this->faker->sentence(4),
            'notes' => $this->faker->optional()->sentence(),
            'statut' => 'a_venir',
        ];
    }
}
