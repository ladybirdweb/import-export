<?php

namespace Ladybirdweb\ImportExport;

use Ladybirdweb\ImportExport\Models\Import as ModelImport;

class Import
{
    /**
     * Store a newly created import in storage.
     *
     * @param  $path
     * @param  $columns
     * @return instance Import
     */
    public function createImport($path, $columns)
    {
        // Store file path and model class to db
        $import = ModelImport::create([
            'file' => $path,
            'file_rows' => count(file(storage_path('app/'.$path))) - 1,
            'db_cols' => $columns,
        ]);

        return $import;
    }

    /**
     * Get import instance.
     *
     * @param  $id
     * @return instance Import
     */
    public function getImport($id)
    {
        return ModelImport::findOrFail($id);
    }

    public function getImportFileData($id, $rows = 5)
    {
        // Get import instance
        $import = $this->getImport($id);

        // Read 5 rows from csv
        $read_line = 1;

        $file = fopen(storage_path('app/'.$import->file), 'r');

        while ($csv_line = fgetcsv($file)) {
            $csv_data[] = $csv_line;

            if ($read_line > $rows) {
                break;
            }

            $read_line++;
        }

        fclose($file);

        return $csv_data;
    }

    /**
     * Update column map in DB.
     *
     * @param  mixed  $column
     * @param  int  $id
     * @return instance Import
     */
    public function setDataMap($column, $id)
    {
        // Get import instance
        $import = $this->getImport($id);

        // Store column map in DB
        $import->model_map = $column;
        $import->save();

        // Return import instance
        return $import;
    }

    /**
     * Dispatch import job.
     *
     * @param  $job Job class to dispatch
     * @param  $import Instance of import
     * @return void
     */
    public function dispatchImportJob($job, ModelImport $import)
    {
        // Dispatch import corn job
        $job::dispatch($import)->onQueue('importing');
    }

    /**
     * Return import progress.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response json
     */
    public function returnImportProgress($id)
    {
        // GEt import instance
        $import = $this->getImport($id);

        $data['status'] = 200;
        $data['progress'] = round(($import->row_processed / $import->file_rows) * 100);

        // If progress completed return successful imported rows count
        if ($data['progress'] == 100) {
            $data['imported'] = $import->row_imported;
        }

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImport($id)
    {
        // Get import instance
        $import = $this->getImport($id);

        // Remove a import from db
        return $import->delete();
    }
}
