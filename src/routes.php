<?php

Route::middleware('web')->group(function () {

// Ajax GET import progress
    Route::get(config('import.import_progress.url'), ['as' => config('import.import_progress.name'), 'uses' => 'Ladybirdweb\ImportExport\Import@returnImportProgress']);

    // Ajax GET export progress
    Route::get(config('export.export_progress.url'), ['as' => config('export.export_progress.name'), 'uses' => 'Ladybirdweb\ImportExport\Export@returnExportProgress']);

    // GET export download
    Route::get(config('export.export_download.url'), ['as' => config('export.export_download.name'), 'uses' => 'Ladybirdweb\ImportExport\Export@downloadExportedFile']);
});
