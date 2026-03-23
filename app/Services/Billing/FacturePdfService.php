<?php

namespace App\Services\Billing;

use App\Models\Facture;
use App\Services\Pdf\PdfBuilder;
use Symfony\Component\HttpFoundation\Response;

class FacturePdfService
{
    public function __construct(private readonly PdfBuilder $pdfBuilder)
    {
    }

    public function download(Facture $facture): Response
    {
        return $this->pdfBuilder
            ->fromView('factures.pdf', ['facture' => $facture])
            ->download('facture-' . $facture->numero_facture . '.pdf');
    }

    public function output(Facture $facture): string
    {
        return $this->pdfBuilder
            ->fromView('factures.pdf', ['facture' => $facture])
            ->output();
    }
}