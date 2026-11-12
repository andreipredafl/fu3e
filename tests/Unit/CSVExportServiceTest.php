<?php

namespace Tests\Unit;

use App\Services\CSVExportService;
use Generator;
use PHPUnit\Framework\TestCase;

class CSVExportServiceTest extends TestCase
{
    private const BYTE_ORDER_MARK = "\xEF\xBB\xBF";

    public function test_it_writes_headings_and_rows_as_csv(): void
    {
        $csv = $this->write(['project', 'notes'], [
            ['Riverside', 'Review, then approve'],
            ['Station Yard', ''],
        ]);

        $this->assertSame(
            self::BYTE_ORDER_MARK
                ."project,notes\r\n"
                ."Riverside,\"Review, then approve\"\r\n"
                ."\"Station Yard\",\r\n",
            $csv,
        );
    }

    public function test_it_accepts_rows_from_a_generator(): void
    {
        $rows = (function (): Generator {
            yield ['one'];
            yield ['two'];
        })();

        $this->assertSame(
            self::BYTE_ORDER_MARK."number\r\none\r\ntwo\r\n",
            $this->write(['number'], $rows),
        );
    }

    /**
     * @param  array<int, string>  $headings
     * @param  iterable<array<int, string>>  $rows
     */
    private function write(array $headings, iterable $rows): string
    {
        $stream = fopen('php://memory', 'w+');

        (new CSVExportService)->write($stream, $headings, $rows);

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $csv;
    }
}
