<?php

namespace App\Http\Controllers;

use App\Http\Requests\CSVExportRequest;
use App\Services\CSVExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVExport
{
    public function __invoke(CSVExportRequest $request, CSVExportService $csv): StreamedResponse
    {
        return $csv->download(
            'project-register.csv',
            $request->validated('headings'),
            $request->input('rows'),
        );
    }
}
