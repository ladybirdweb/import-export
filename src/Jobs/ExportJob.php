<?php

namespace Ladybirdweb\ImportExport\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Ladybirdweb\ImportExport\Models\Export;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Custom file path
        $file_path = date('Y/m');

        // Export instance assign to variable
        $export = $this->export;

        // Store total row count
        $export->result_rows = $export->query->count();
        $export->save();

        // Create export file
        $excel = Excel::create('export-'.date('dmYhis'), function ($excel) use ($export) {

            // Create new sheet
            $excel->sheet('export', function ($sheet) use ($export) {

                // Retrive data in chunk
                $export->query->chunk(10, function ($data) use ($export, $sheet) {

                    // Process chunk data
                    foreach ($data as $row) {

                        // Append row to sheet
                        $sheet->appendRow($row->toArray());
                    }

                    // Store processed row count
                    $export->row_processed += 10;
                    $export->save();
                });
            });
        })->store($this->export->type, storage_path('app/exports/'.$file_path), true);

        // Update export data
        $export->file = $file_path.'/'.$excel['file'];
        $export->completed_at = Carbon::now();
        $export->save();
    }
}
