<?php

namespace Ladybirdweb\ImportExport;

use Illuminate\Database\Eloquent\Builder;
use Ladybirdweb\ImportExport\Jobs\ExportJob;
use Ladybirdweb\ImportExport\Models\Export as ModelExport;

class Export
{
	
	public function export(Builder $query, $ext = 'xls')
	{
		// Create new export
		$export = new ModelExport;

		$export->type = $ext;
		$export->query = $query->getModel();

		$export->save();

		ExportJob::dispatch( $export )->onQueue( 'exporting' );

		return $export;
	}


    /**
     * Return export progress
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response json
     */
    public function returnExportProgress($id)
    {
        // Map export instance
        $export = ModelExport::findOrFail( $id );

        $data['status'] = 200;

        if ( $export->result_rows > 0 ) {

        	$data['progress'] = round( ( $export->row_processed / $export->result_rows ) * 100 );

        } else {

        	$data['progress'] = 0;

        }

        // If progress completed return successful imported rows count
        if ( $data['progress'] == 100 ) {
            $data['exported'] = route( 'ladybirdweb.export.download', $export->id );
        }

        return response()->json( $data );
    }


    public function downloadExportedFile($id)
    {
        // Map export instance
        $export = ModelExport::findOrFail( $id );

        if ( ! file_exists( storage_path( 'app/exports' ) . '/' . $export->file ) || empty( $export->file ) ) {
            abort(404);
        }

        return response()->download( storage_path( 'app/exports/' . $export->file ) );
    }
}
