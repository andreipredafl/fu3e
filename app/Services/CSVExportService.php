<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CSVExportService
{
    /**
     * Streams a table to the browser as a CSV file download.
     *
     * @param  array<int, string>  $headings
     * @param  iterable<array<int, string>>  $rows
     */
    public function download(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headings, $rows) {
            $output = fopen('php://output', 'w');

            $this->write($output, $headings, $rows);

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Writes a table to an open stream as UTF-8 CSV, one line at a time, so
     * the CSV text is never held in memory.
     *
     * @param  resource  $stream
     * @param  array<int, string>  $headings
     * @param  iterable<array<int, string>>  $rows
     */
    public function write($stream, array $headings, iterable $rows): void
    {
        // Byte order mark, so Excel reads the file as UTF-8.
        fwrite($stream, "\xEF\xBB\xBF");

        fputcsv($stream, $headings, escape: '', eol: "\r\n");

        foreach ($rows as $row) {
            fputcsv($stream, $row, escape: '', eol: "\r\n");
        }
    }
}
