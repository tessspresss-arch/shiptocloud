<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepensesExport implements FromCollection, WithHeadings, WithStyles
{
    protected $depenses;

    public function __construct($depenses)
    {
        $this->depenses = $depenses;
    }

    public function collection()
    {
        return $this->depenses->map(function ($depense) {
            return [
                'date' => $depense->date_depense->format('d/m/Y'),
                'categorie' => $depense->categorie->nom ?? '',
                'description' => $depense->description,
                'montant' => number_format($depense->montant, 2, ',', ' '),
                'methode' => $depense->methode_paiement,
                'reference' => $depense->reference_paiement ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Catégorie',
            'Description',
            'Montant',
            'Méthode',
            'Référence'
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
