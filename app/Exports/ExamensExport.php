<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamensExport implements FromCollection, WithHeadings, WithStyles
{
    protected $examens;

    public function __construct($examens)
    {
        $this->examens = $examens;
    }

    public function collection()
    {
        return $this->examens->map(function ($examen) {
            return [
                'patient' => $examen->patient->nom_complet ?? '',
                'nom_examen' => $examen->nom_examen,
                'type' => $examen->type,
                'date_demande' => $examen->date_demande->format('d/m/Y'),
                'date_realisation' => $examen->date_realisation ? $examen->date_realisation->format('d/m/Y') : '-',
                'statut' => $examen->statut,
                'medecin' => $examen->medecin->nom ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Patient',
            'Examen',
            'Type',
            'Date Demande',
            'Date Réalisation',
            'Statut',
            'Médecin'
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
