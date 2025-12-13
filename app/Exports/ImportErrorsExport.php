<?php


namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class ImportErrorsExport implements FromCollection
{
    protected Collection $rows;

    public function __construct(array $failures, array $exceptions)
    {
        $grouped = [];

        // 1) Group validation failures by their row number
        foreach ($failures as $failure) {
            $rowNum = $failure->row();       // Excel row number
            // initialize group if first time
            if (!isset($grouped[$rowNum])) {
                $grouped[$rowNum] = [
                    'values' => $failure->values(), // the raw row
                    'errors' => [],
                ];
            }
            // merge in this failure's messages
            $grouped[$rowNum]['errors'] = array_merge(
                $grouped[$rowNum]['errors'],
                $failure->errors()
            );
        }

        // 2) Also capture any caught-exception rows
        foreach ($exceptions as $ex) {
            // we stored ['row'=>..., 'message'=>...] in your import
            $values = $ex['row'];
            // determine a unique key—either use the row number if you stored it,
            // or serialize the row data itself
            $key = $values['__row_index'] ?? md5(json_encode($values));

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'values' => $values,
                    'errors' => [],
                ];
            }
            $grouped[$key]['errors'][] = $ex['message'];
        }

        // 3) Build the final collection: one entry per grouped record
        $out = [];
        foreach ($grouped as $group) {
            // unique-ify error messages
            $errors = array_unique($group['errors']);
            $arr = $group['values'];
            foreach ($arr as $key => $value) {
                // if the key is an integer or a string consisting of digits…
                if (is_int($key) || ctype_digit((string) $key)) {
                    unset($arr[$key]);
                }
            }
            $row    = $arr;
            // inject the JSON-encoded errors column
            $row['errors'] = json_encode(array_values($errors), JSON_UNESCAPED_UNICODE);
            $out[] = $row;

        }
        $this->rows = collect($out);

    }

    public function collection()
    {
        return $this->rows;
    }

    /**
     * Define the header row: auto-pull keys from the first row.
     */
    public function headings(): array
    {
        // If no rows, return an empty array to avoid errors
        if ($this->rows->isEmpty()) {
            return [];
        }

        // Grab the keys of the first row, in order:
        return array_keys($this->rows->first());
    }
}
