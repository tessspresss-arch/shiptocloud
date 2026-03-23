<?php

namespace App\Services\Exports;

use Symfony\Component\HttpFoundation\StreamedResponse;

class Utf8CsvExporter
{
    /**
     * @param iterable<int, array<int, mixed>> $rows
     * @param array<int, string> $headers
     */
    public function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}