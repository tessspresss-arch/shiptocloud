<?php

declare(strict_types=1);

use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\SMSReminder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$patient = Patient::query()->updateOrCreate(
    ['email' => 'e2e.sms@medisys.test'],
    [
        'numero_dossier' => 'E2E-SMS-001',
        'nom' => 'SMS',
        'prenom' => 'E2E',
        'date_naissance' => '1993-02-10',
        'genre' => 'M',
        'telephone' => '0600000401',
        'adresse' => 'Fixture sms premium',
        'ville' => 'Casablanca',
        'code_postal' => '20000',
        'etat_civil' => 'marie',
        'contact_urgence' => 'Contact E2E SMS',
        'telephone_urgence' => '0600000402',
        'allergies' => 'Aucune',
        'traitements' => 'Aucun',
        'notes' => 'Patient de fixture pour les tests UI SMS.',
    ]
);

$medecin = Medecin::withTrashed()->updateOrCreate(
    ['email' => 'e2e.sms.medecin@medisys.test'],
    [
        'matricule' => 'MED-E2E-SMS-0001',
        'civilite' => 'Dr.',
        'nom' => 'SMS',
        'prenom' => 'E2E',
        'specialite' => 'Medecine generale',
        'telephone' => '+212600000402',
        'adresse_cabinet' => 'Cabinet E2E SMS',
        'ville' => 'Casablanca',
        'code_postal' => '20000',
        'numero_ordre' => 'ORD-E2E-SMS-1',
        'tarif_consultation' => 260.00,
        'statut' => 'actif',
        'notes' => 'Medecin de fixture pour les tests UI SMS.',
        'date_embauche' => '2018-01-01',
    ]
);

if (method_exists($medecin, 'restore') && $medecin->trashed()) {
    $medecin->restore();
}

$rendezVous = RendezVous::query()->updateOrCreate(
    [
        'patient_id' => $patient->id,
        'motif' => 'Rendez-vous E2E SMS',
    ],
    [
        'medecin_id' => $medecin->id,
        'date_heure' => Carbon::now()->addDay()->setTime(14, 0),
        'duree' => 30,
        'type' => 'consultation',
        'notes' => 'Rendez-vous de fixture pour rappels SMS.',
        'statut' => 'confirmé',
    ]
);

$reminder = SMSReminder::query()->updateOrCreate(
    [
        'rendezvous_id' => $rendezVous->id,
        'message_template' => 'Bonjour, rappel de votre rendez-vous E2E demain a 14h00.',
    ],
    [
        'patient_id' => $patient->id,
        'telephone' => '0600000401',
        'heures_avant' => 24,
        'statut' => 'planifie',
        'date_envoi_prevue' => Carbon::parse($rendezVous->date_heure)->copy()->subHours(24),
        'date_envoi_reelle' => null,
        'code_erreur' => null,
        'erreur_message' => null,
        'provider' => 'twilio',
        'provider_id' => 'sms-e2e-0001',
    ]
);

fwrite(STDOUT, json_encode([
    'reminder_id' => $reminder->id,
], JSON_THROW_ON_ERROR));
