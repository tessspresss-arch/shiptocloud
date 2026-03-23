<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $contacts;

    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection()
    {
        return $this->contacts->map(function ($contact) {
            return [
                'nom' => $contact->nom_complet,
                'type' => $contact->type_formate,
                'email' => $contact->email ?? '-',
                'telephone' => $contact->telephone ?? '-',
                'adresse' => $contact->adresse ?? '-',
                'entreprise' => $contact->entreprise ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nom Complet',
            'Type',
            'Email',
            'Téléphone',
            'Adresse',
            'Entreprise'
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
