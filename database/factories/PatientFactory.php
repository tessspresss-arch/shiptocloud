<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Patient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Moroccan cities
        $moroccanCities = [
            'Casablanca', 'Rabat', 'Marrakech', 'Fès', 'Tanger', 'Agadir', 'Meknès', 'Oujda',
            'Kenitra', 'Tétouan', 'Salé', 'Nador', 'Beni Mellal', 'Mohammedia', 'Khémisset',
            'El Jadida', 'Berkane', 'Safi', 'Taroudant', 'Ksar el Kebir'
        ];

        // Generate local Moroccan phone number: 8 digits
        $phoneNumber = '6' . str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);

        // Email with occasional .ma domain
        $email = $this->faker->unique()->userName . '@' . ($this->faker->boolean(30) ? 'gmail.com' : 'yahoo.fr');

        return [
            'numero_dossier' => 'PAT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'date_naissance' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'genre' => $this->faker->randomElement(['M', 'F']),
            'telephone' => $phoneNumber,
            'email' => $email,
            'cin' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'adresse' => $this->faker->streetAddress,
            'ville' => $this->faker->randomElement($moroccanCities),
            'code_postal' => $this->faker->numerify('#####'),
            'groupe_sanguin' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'assurance' => $this->faker->randomElement(['CNSS', 'CNOPS', 'Privé', 'Aucune']),
            'antecedents' => $this->faker->optional(0.3)->sentence,
            'notes' => $this->faker->optional(0.2)->paragraph,
            'is_draft' => false,
        ];
    }
}
