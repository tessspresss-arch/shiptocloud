<?php

namespace Database\Factories;

use App\Models\Medecin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medecin>
 */
class MedecinFactory extends Factory
{
    protected $model = Medecin::class;

    public function definition(): array
    {
        $nom = $this->faker->lastName();
        $prenom = $this->faker->firstName();

        return [
            'matricule' => 'MED-' . now()->format('Y') . '-' . $this->faker->unique()->numerify('####'),
            'civilite' => 'Dr.',
            'nom' => $nom,
            'prenom' => $prenom,
            'specialite' => $this->faker->randomElement([
                'medecine generale',
                'cardiologie',
                'pediatrie',
                'dermatologie',
            ]),
            'telephone' => '+2126' . $this->faker->numerify('########'),
            'email' => strtolower($prenom . '.' . $nom) . $this->faker->unique()->numerify('##') . '@medisys.test',
            'adresse_cabinet' => $this->faker->streetAddress(),
            'ville' => $this->faker->randomElement(['Casablanca', 'Rabat', 'Marrakech', 'Fes']),
            'code_postal' => $this->faker->numerify('#####'),
            'numero_ordre' => 'ORD-' . $this->faker->numerify('######'),
            'tarif_consultation' => $this->faker->randomFloat(2, 150, 500),
            'statut' => 'actif',
            'date_embauche' => $this->faker->dateTimeBetween('-10 years', '-1 month'),
        ];
    }
}
