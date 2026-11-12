<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CSVExportRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'headings' => ['required', 'array', 'list'],
            'headings.*' => ['string'],
        ];
    }

    /**
     * Only the headings go through Laravel's validator. It copies every array
     * it is given, which costs too much memory for a large table, so the rows
     * are checked in a plain loop in after() instead.
     *
     * @return array<string, mixed>
     */
    public function validationData(): array
    {
        return $this->only('headings');
    }

    /**
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $rows = $this->input('rows');

                if (! is_array($rows) || ! array_is_list($rows)) {
                    $validator->errors()->add('rows', 'The rows field must be a list.');

                    return;
                }

                $columnCount = count($this->input('headings'));

                foreach ($rows as $index => $row) {
                    if (! $this->isRowOfText($row, $columnCount)) {
                        $validator->errors()->add(
                            "rows.$index",
                            'Row '.($index + 1)." must contain $columnCount text values.",
                        );

                        return;
                    }
                }
            },
        ];
    }

    private function isRowOfText(mixed $row, int $columnCount): bool
    {
        return is_array($row)
            && array_is_list($row)
            && count($row) === $columnCount
            && count(array_filter($row, is_string(...))) === $columnCount;
    }
}
