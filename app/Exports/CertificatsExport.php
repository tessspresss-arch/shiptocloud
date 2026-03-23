<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CertificatsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $certificats;

    public function __construct($certificats)
    {
        $this->certificats = $certificats;
    }

    public function collection()
    {
        return $this->certificats->map(function ($cert) {
            return [
                'patient' => $cert->patient->nom_complet ?? '',
                'type' => $cert->type,
                'medecin' => $cert->medecin->nom ?? '-',
                'date_emission' => $cert->date_emission->format('d/m/Y'),
                'date_debut' => $cert->date_debut->format('d/m/Y'),
                'date_fin' => $cert->date_fin->format('d/m/Y'),
                'nombre_jours' => $cert->nombre_jours,
                'transmis' => $cert->est_transmis ? 'Oui' : 'Non',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Patient',
            'Type',
            'Médecin',
            'Date Émission',
            'Date Début',
            'Date Fin',
            'Nombre Jours',
            'Transmis'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '3B82F6'],
                ],
            ],
        ];
    }
}
