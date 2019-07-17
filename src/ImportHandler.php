<?php

namespace LWS\ImportExport;

use Carbon\Carbon;
use LWS\ImportExport\Models\Import;

class ImportHandler
{
    public function process(Import $import, callable $callback)
    {

        // CSV header row show be excluded
        $csv_header = true;

        // Read csv
        $file = fopen(storage_path('app/'.$import->file), 'r');

        // Processed csv rows
        $processed_row = 1;

        // Go over csv data line by line
        while ($csv_line = fgetcsv($file)) {
            if ($csv_header) {

                // Skip csv header
                $csv_header = false;
            } else {

                // Drop ignore columns
                $data = array_combine($import->model_map, $csv_line);
                unset($data['']);

                // Call user callback with data
                if ($callback($data)) {

                    // If successful -> update imported rows
                    $import->row_imported = $import->row_imported + 1;
                }

                // Update porcessed rows
                $import->row_processed = $processed_row;
                $import->save();

                $processed_row++;
            }
        }

        // Close csv file
        fclose($file);

        // Update import as done
        $import->completed_at = Carbon::now();
        $import->save();
    }
}
