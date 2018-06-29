<?php

namespace Ladybirdweb\ImportExport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JildertMiedema\LaravelPlupload\Facades\Plupload;
use Ladybirdweb\ImportExport\Models\Import as ModelImport;

class Import
{
    protected $import_errors = [];

    protected $upload_route;

    protected $import_map_route;

    /**
     * Show the form for creating a new import.
     *
     * @return \Illuminate\Http\Response
     */
    public function showImportForm()
    {
        $route = $this->upload_route;

        return view( 'importexport::import.import', compact( 'route' ) );
    }


    /**
     * Store a newly created import in storage.
     *
     * @param  $path
     * @param  $columns
     * @return integer Import id
     */
    public function createImport($path, $columns)
    {
        // Store file path and model class to db
        $import = ModelImport::create([
            'file' => $path,
            'file_rows' => count( file( storage_path( 'app/' . $path ) ) ) - 1,
            'db_cols' => $columns
        ]);

        return $import->id;
    }


    /**
     * Upload import file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImportFile(Request $request)
    {
        // Validate file
        $validation = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt'
        ]);

        if ( $validation->fails() ) {
            // Set validator errors
            $this->import_errors = $validation->errors();

            return ['status' => 'failed'];
        }

        return Plupload::receive('file', function ($file) {

            // Upload file to storage/app/imports
            $path = Storage::putFileAs( 'imports', $file,
                'import-' . time() . '.' . $file->getClientOriginalExtension() );           

            return ['status' => 'ready', 'path' => $path];
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showImportStatus($id)
    {
        // Map import instance
        $import = ModelImport::findOrFail( $id );

        return view( 'importexport::import.progress', compact( 'id' ) );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showColumnsMapForm($id)
    {
        // Map import instance
        $import = ModelImport::findOrFail( $id );
        
        // Read 5 rows from csv
        $read_line = 1;

        $file = fopen( storage_path( 'app/' . $import->file ), 'r' );

        while ( $csv_line = fgetcsv( $file ) ) {
            $csv_data[] = $csv_line;

            if ( $read_line > 5 ) break;

            $read_line++;
        }
        
        fclose( $file );

        // Get fillable columns
        $db_columns = $import->db_cols;

        // Set post route
        $route = $this->import_map_route;

        return view( 'importexport::import.data_map', compact( 'db_columns', 'csv_data', 'id', 'route' ) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeColumnsMap(Request $request, $id)
    {
        // Map import instance
        $import = ModelImport::findOrFail( $id );

        // Get fillable columns
        $db_columns = $import->db_cols;

        // Validate data
        $validator = Validator::make($request->all(), [
            'db_column' => [
                'required',
                'array',
                Rule::in( $db_columns ),
                'size:' . count( $db_columns ),
            ],
            'db_column.*' => 'distinct',
            'ignore_col' => 'sometimes|array',
        ],
        [
            'required' => implode(', ', $db_columns) . ' are the mandatory columns',
            'in' => implode(', ', $db_columns) . ' are the mandatory columns',
            'size' => implode(', ', $db_columns) . ' are the mandatory columns',
            'distinct' => 'You can not select one column more than one time',
        ])->validate();

        $db_column = $request->db_column;

        // Push ignore column to db_column if exists
        if ( ! is_null( $request->ignore_col ) ) {
            foreach ( $request->ignore_col as $col_no ) {
                array_splice( $db_column, $col_no, 0, '');
            }
        }

        // Store column map in DB
        $import->model_map = $db_column;
        $import->save();

        // Return import instance
        return $import;
    }

    public function dispatchImportJob( $job, ModelImport $import)
    {
        // Dispatch import corn job
        $job::dispatch( $import )->onQueue( 'importing' );
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeImport($id)
    {
        // Remove a import from db
        return ModelImport::findOrFail( $id )->delete();
    }

    /**
     * Return import progress
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response json
     */
    public function returnImportProgress($id)
    {
        // Map import instance
        $import = ModelImport::findOrFail( $id );

        $data['status'] = 200;
        $data['progress'] = round( ( $import->row_processed / $import->file_rows ) * 100 );

        // If progress completed return successful imported rows count
        if ( $data['progress'] == 100 ) {
            $data['imported'] = $import->row_imported;
        }

        return response()->json( $data );
    }

    /**
     * Get import errors
     *
     * @return json
     */
    public function getImportErrors()
    {
        return [ 'errors' => $this->import_errors ];
    }

    /**
     * Set import route
     *
     * @param  string  $route
     */
    public function setImportMapRoute($route)
    {
        $this->import_map_route = $route;
    }

    /**
     * Set upload route
     *
     * @param  string  $route
     */
    public function setUploadRoute($route)
    {
        $this->upload_route = $route;
    }
}
