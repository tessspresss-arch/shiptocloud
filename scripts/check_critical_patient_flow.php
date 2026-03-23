<?php

declare(strict_types=1);

use App\Models\Consultation;
use App\Models\Facture;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$token = trim((string) ($argv[1] ?? ''));

if ($token === '') {
    fwrite(STDERR, "Token obligatoire.\n");
    exit(1);
}

$patientQuery = Patient::query()->where(function ($query) use ($token) {
    $query->where('cin', 'CRIT-' . $token)
        ->orWhere('email', 'critical.' . $token . '@medisys.test');
});

$patientCount = (clone $patientQuery)->count();
$patient = (clone $patientQuery)->latest('id')->first();

$consultation = null;
$facture = null;
$rendezVous = null;

if ($patient) {
    $consultation = Consultation::query()
        ->where('patient_id', $patient->id)
        ->where('diagnostic', 'like', '%' . $token . '%')
        ->latest('id')
        ->first();

    $factureQuery = Facture::query()
        ->where('patient_id', $patient->id)
        ->where('notes', 'like', '%' . $token . '%');

    if ($consultation && Schema::hasColumn('factures', 'consultation_id')) {
        $factureQuery->orWhere(function ($query) use ($patient, $consultation) {
            $query->where('patient_id', $patient->id)
                ->where('consultation_id', $consultation->id);
        });
    }

    $facture = $factureQuery->latest('id')->first();

    $rendezVousQuery = RendezVous::query()
        ->where('patient_id', $patient->id);

    $rendezVous = (clone $rendezVousQuery)
        ->where('notes', 'like', '%' . $token . '%')
        ->latest('id')
        ->first();

    if (!$rendezVous) {
        $rendezVous = $rendezVousQuery
            ->latest('id')
            ->first();
    }
}

$consultationStatus = null;
if ($consultation && $consultation->date_consultation) {
    $consultationDate = Carbon::parse($consultation->date_consultation);
    $consultationStatus = $consultationDate->isPast() && !empty($consultation->diagnostic)
        ? 'terminee'
        : ($consultationDate->isFuture() ? 'planifiee' : 'en_attente');
}

fwrite(STDOUT, json_encode([
    'token' => $token,
    'patient_count' => $patientCount,
    'patient_id' => $patient?->id,
    'consultation_id' => $consultation?->id,
    'consultation_patient_id' => $consultation?->patient_id,
    'consultation_status' => $consultationStatus,
    'facture_id' => $facture?->id,
    'facture_patient_id' => $facture?->patient_id,
    'facture_consultation_id' => Schema::hasColumn('factures', 'consultation_id') ? $facture?->consultation_id : null,
    'facture_statut' => $facture?->statut,
    'facture_date_paiement' => $facture?->date_paiement ? Carbon::parse((string) $facture->date_paiement)->format('Y-m-d') : null,
    'rendezvous_id' => $rendezVous?->id,
    'rendezvous_patient_id' => $rendezVous?->patient_id,
    'rendezvous_statut' => $rendezVous?->statut,
], JSON_THROW_ON_ERROR));