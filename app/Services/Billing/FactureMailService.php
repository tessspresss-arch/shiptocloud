<?php

namespace App\Services\Billing;

use App\Models\Facture;
use Illuminate\Support\Facades\Mail;

class FactureMailService
{
    public function __construct(private readonly FacturePdfService $pdfService)
    {
    }

    public function sendToPatient(Facture $facture): void
    {
        $pdfOutput = $this->pdfService->output($facture);

        Mail::send('emails.facture', ['facture' => $facture], function ($message) use ($facture, $pdfOutput) {
            $message->to($facture->patient->email, $facture->patient->nom . ' ' . $facture->patient->prenom)
                ->subject('Facture ' . $facture->numero_facture)
                ->attachData($pdfOutput, 'facture-' . $facture->numero_facture . '.pdf');
        });
    }
}