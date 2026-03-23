<?php

namespace App\Services;

use App\Models\Patient;
use Carbon\CarbonInterface;

class PatientsCsvExportService
{
    public function delimiter(?string $requestedDelimiter = null): string
    {
        $delimiter = $requestedDelimiter ?? (string) env('CSV_DELIMITER', ';');
        $delimiter = trim($delimiter);

        if ($delimiter === '\\t') {
            return "\t";
        }

        $allowed = [';', ',', "\t", '|'];

        return in_array($delimiter, $allowed, true) ? $delimiter : ';';
    }

    public function headersRow(): array
    {
        return [
            'ID / DOSSIER',
            'PATIENT',
            'CONTACT',
            'CIN',
            'DATE NAISSANCE',
            'GENRE',
        ];
    }

    public function patientRow(Patient $patient): array
    {
        $fullName = trim((string) $patient->nom . ' ' . (string) $patient->prenom);
        $patientLabel = mb_strtoupper($fullName !== '' ? $fullName : 'PATIENT', 'UTF-8');

        $contactParts = array_values(array_filter([
            trim((string) ($patient->telephone ?? '')),
            trim((string) ($patient->email ?? '')),
        ], static fn ($value) => $value !== ''));

        $contact = implode(' | ', $contactParts);

        $dateNaissance = '';
        if ($patient->date_naissance instanceof CarbonInterface) {
            $dateNaissance = $patient->date_naissance->format('d/m/Y');
        } elseif (! empty($patient->date_naissance)) {
            $dateNaissance = date('d/m/Y', strtotime((string) $patient->date_naissance));
        }

        $genreRaw = strtoupper(trim((string) ($patient->genre ?? '')));
        $genre = match ($genreRaw) {
            'M', 'MALE', 'MASCULIN' => 'Masculin',
            'F', 'FEMALE', 'FEMININ', 'FÉMININ' => 'Féminin',
            default => '',
        };

        $dossier = trim((string) ($patient->numero_dossier ?? ''));
        if ($dossier === '') {
            $dossier = 'PAT-' . (string) $patient->id;
        }

        return [
            $dossier,
            $patientLabel,
            $contact,
            (string) ($patient->cin ?? ''),
            $dateNaissance,
            $genre,
        ];
    }

    public function writeRows($handle, iterable $patients, string $delimiter): void
    {
        fwrite($handle, implode($delimiter, $this->headersRow()) . "\r\n");

        foreach ($patients as $patient) {
            if ($patient instanceof Patient) {
                fputcsv($handle, $this->patientRow($patient), $delimiter);
            }
        }
    }
}

