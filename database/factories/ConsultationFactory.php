<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    public function definition(): array
    {
        return [
            'rendez_vous_id' => RendezVous::factory(),
            'patient_id' => Patient::factory(),
            'medecin_id' => Medecin::factory(),
            'date_consultation' => now()->toDateString(),
            'symptomes' => $this->faker->sentence(),
            'diagnostic' => $this->faker->sentence(),
            'poids' => $this->faker->randomFloat(2, 45, 120),
            'taille' => $this->faker->randomFloat(2, 1.45, 1.95),
            'tension_arterielle_systolique' => $this->faker->numberBetween(100, 150),
            'tension_arterielle_diastolique' => $this->faker->numberBetween(60, 95),
            'temperature' => $this->faker->randomFloat(1, 36, 39),
            'examen_clinique' => $this->faker->paragraph(),
            'traitement_prescrit' => $this->faker->sentence(),
            'recommandations' => $this->faker->sentence(),
            'date_prochaine_visite' => now()->addMonth()->toDateString(),
        ];
    }
}
