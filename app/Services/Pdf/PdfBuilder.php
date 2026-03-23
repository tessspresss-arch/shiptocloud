<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf as DomPdfFacade;
use Barryvdh\DomPDF\PDF;

class PdfBuilder
{
    public function fromView(string $view, array $data = [], string $paper = 'a4'): PDF
    {
        return DomPdfFacade::loadView($view, $data)
            ->setOption(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
            ->setPaper($paper);
    }

    public function fromHtml(string $html, string $paper = 'a4'): PDF
    {
        return DomPdfFacade::loadHTML($html)
            ->setOption(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
            ->setPaper($paper);
    }
}