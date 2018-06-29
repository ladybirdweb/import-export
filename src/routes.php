<?php

Route::middleware('web')->group(function() {

// Post import form
Route::post( config('import.import_upload'), [ 'as' => 'ladybirdweb.import', 'uses' => 'Ladybirdweb\ImportExport\Import@uploadImportFile']);

// Ajax GET import progress
Route::get( config( 'import.import_progress' ), [ 'as' => 'ladybirdweb.import.ajax.progress', 'uses' => 'Ladybirdweb\ImportExport\Import@returnImportProgress']);

// Ajax GET export progress
Route::get( config( 'export.export_progress' ), [ 'as' => 'ladybirdweb.export.ajax.progress', 'uses' => 'Ladybirdweb\ImportExport\Export@returnExportProgress']);

// GET export download
Route::get( config( 'export.export_download' ),  [ 'as' => 'ladybirdweb.export.download', 'uses' => 'Ladybirdweb\ImportExport\Export@downloadExportedFile']);

});
