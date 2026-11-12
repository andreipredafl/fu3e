<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVExportRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVExport
{
    public function __invoke(CSVExportRequest $request): StreamedResponse
    {
        $headings = $request->validated('headings');
        $rows = $request->input('rows');

        // The table is already in memory from the request. Writing each line
        // straight to the response avoids holding the CSV text as a second copy
        return response()->streamDownload(function () use ($headings, $rows) {
            $output = fopen('php://output', 'w');

            // Byte order mark, so Excel reads the file as UTF-8.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, $headings, escape: '', eol: "\r\n");

            foreach ($rows as $row) {
                fputcsv($output, $row, escape: '', eol: "\r\n");
            }

            fclose($output);
        }, 'project-register.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
