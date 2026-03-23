<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medecin;

class MedecinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medecins = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'specialite' => 'Médecine générale',
                'telephone' => '01 23 45 67 89',
                'email' => 'jean.dupont@cabinet-medical.fr',
                'adresse_cabinet' => '123 Rue de la Santé, Paris',
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'specialite' => 'Pédiatrie',
                'telephone' => '01 23 45 67 90',
                'email' => 'marie.martin@cabinet-medical.fr',
                'adresse_cabinet' => '456 Avenue des Enfants, Paris',
            ],
            [
                'nom' => 'Dubois',
                'prenom' => 'Pierre',
                'specialite' => 'Cardiologie',
                'telephone' => '01 23 45 67 91',
                'email' => 'pierre.dubois@cabinet-medical.fr',
                'adresse_cabinet' => '789 Boulevard du Cœur, Paris',
            ],
            [
                'nom' => 'Garcia',
                'prenom' => 'Sophie',
                'specialite' => 'Dermatologie',
                'telephone' => '01 23 45 67 92',
                'email' => 'sophie.garcia@cabinet-medical.fr',
                'adresse_cabinet' => '321 Rue de la Peau, Paris',
            ],
            [
                'nom' => 'Leroy',
                'prenom' => 'Michel',
                'specialite' => 'Ophtalmologie',
                'telephone' => '01 23 45 67 93',
                'email' => 'michel.leroy@cabinet-medical.fr',
                'adresse_cabinet' => '654 Avenue des Yeux, Paris',
            ],
        ];

        foreach ($medecins as $medecin) {
            Medecin::create($medecin);
        }
    }
}
