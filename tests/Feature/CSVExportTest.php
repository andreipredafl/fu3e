<?php

namespace Tests\Feature;

use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CSVExportTest extends TestCase
{
    public function test_it_downloads_the_table_as_a_csv_file(): void
    {
        $response = $this->export(
            ['project', 'contractor', 'notes'],
            [
                ['Riverside', 'North & Co', 'Phase 1'],
                ['Station Yard', 'West Build', ''],
                ['Orchard House', 'Green Works', 'Review, then approve'],
            ],
        );

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertDownload('project-register.csv');

        $this->assertCsv($response, [
            'project,contractor,notes',
            'Riverside,"North & Co","Phase 1"',
            '"Station Yard","West Build",',
            '"Orchard House","Green Works","Review, then approve"',
        ]);
    }

    public function test_it_escapes_commas_quotes_and_line_breaks(): void
    {
        $response = $this->export(
            ['a,b', 'quote'],
            [['say "hi"', "line one\nline two"]],
        );

        $this->assertCsv($response, [
            '"a,b",quote',
            '"say ""hi""","line one'."\n".'line two"',
        ]);
    }

    public function test_it_keeps_empty_cells_and_surrounding_spaces(): void
    {
        $response = $this->export(
            ['first', 'second', 'third'],
            [['', '  padded  ', '']],
        );

        $this->assertCsv($response, [
            'first,second,third',
            ',"  padded  ",',
        ]);
    }

    public function test_it_keeps_accents_and_symbols(): void
    {
        $response = $this->export(
            ['șantier', 'cost'],
            [['Café Gară', '£1200']],
        );

        $this->assertCsv($response, [
            'șantier,cost',
            '"Café Gară",£1200',
        ]);
    }

    public function test_it_exports_only_the_headings_when_there_are_no_rows(): void
    {
        $response = $this->export(['project', 'contractor'], []);

        $response->assertOk();
        $this->assertCsv($response, ['project,contractor']);
    }

    public function test_it_allows_empty_and_duplicate_headings(): void
    {
        $response = $this->export(
            ['notes', '', 'notes'],
            [['one', 'two', 'three']],
        );

        $this->assertCsv($response, [
            'notes,,notes',
            'one,two,three',
        ]);
    }

    public function test_it_rejects_a_table_without_headings(): void
    {
        $this->export([], [])->assertUnprocessable()->assertJsonValidationErrors('headings');

        $this->postJson('/api/csv-export', ['rows' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('headings');
    }

    public function test_it_rejects_a_row_with_the_wrong_number_of_cells(): void
    {
        $response = $this->export(
            ['project', 'contractor'],
            [['Riverside', 'North & Co'], ['Station Yard']],
        );

        $response->assertUnprocessable()->assertJsonValidationErrors([
            'rows.1' => 'Row 2 must contain 2 text values.',
        ]);
    }

    public function test_it_rejects_a_cell_that_is_not_text(): void
    {
        $this->export(['project'], [[null]])->assertUnprocessable()->assertJsonValidationErrors('rows.0');
        $this->export(['project'], [[5]])->assertUnprocessable()->assertJsonValidationErrors('rows.0');
        $this->export(['project'], [[['nested']]])->assertUnprocessable()->assertJsonValidationErrors('rows.0');
    }

    public function test_it_rejects_rows_that_are_not_a_list(): void
    {
        $this->export(['project'], 'not a list')->assertUnprocessable()->assertJsonValidationErrors('rows');
        $this->export(['project'], ['first' => ['Riverside']])->assertUnprocessable()->assertJsonValidationErrors('rows');

        $this->postJson('/api/csv-export', ['headings' => ['project']])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rows');
    }

    public function test_it_rejects_a_heading_that_is_not_text(): void
    {
        $this->export(['project', 5], [])->assertUnprocessable()->assertJsonValidationErrors('headings.1');
        $this->export(['project', null], [])->assertUnprocessable()->assertJsonValidationErrors('headings.1');
    }

    private function export(mixed $headings, mixed $rows): TestResponse
    {
        return $this->postJson('/api/csv-export', ['headings' => $headings, 'rows' => $rows]);
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function assertCsv(TestResponse $response, array $lines): void
    {
        $byteOrderMark = "\xEF\xBB\xBF";

        $this->assertSame(
            $byteOrderMark.implode("\r\n", $lines)."\r\n",
            $response->streamedContent(),
        );
    }
}
