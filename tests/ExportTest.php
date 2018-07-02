<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Ladybirdweb\ImportExport\Facades\Export;
use Ladybirdweb\ImportExport\Jobs\ExportJob;
use Ladybirdweb\ImportExport\Models\Export as ModelExport;

class ExportTest extends TestCase
{
	use RefreshDatabase;

	protected $export;

	protected function setUp ()
	{
	    parent::setUp();

	    Route::middleware('web')->group(function() {

			Route::get('/ticket/export/{id}', [ 'as' => 'ticket.export.progress', 'uses' => 'Ladybirdweb\ImportExport\Export@showExportStatus']);

			Route::get( '/export/{id}/download',  [ 'as' => 'ladybirdweb.export.download', 'uses' => 'Ladybirdweb\ImportExport\Export@downloadExportedFile']);

		});

		Storage::putFileAs('exports', new File( storage_path( 'test/test.csv' ) ), 'test.xls');

		$this->export = ModelExport::create([
			'file' => 'test.xls',
			'query' => \App\Models\User::select([ 'name', 'email', 'created_at' ])->getModel(),
			'type' => 'xls'
		]);
	}

	/**
	* @test
	*/
	public function data_export_initiated_and_dispatched()
	{
		Queue::fake();

		$export = Export::export( \App\Models\User::select([ 'name', 'email', 'created_at' ]), 'xls' );

		$this->assertInstanceOf( ModelExport::class, $export );

		Queue::assertPushedOn('exporting', ExportJob::class);
	}

	/**
	* @test
	*/
	public function see_export_progress_page()
	{
		$response =  $this->get('/ticket/export/' . $this->export->id);

		$response->assertStatus(200);

		$response->assertSee('Export');
	}

	/**
	* @test
	*/
	public function check_export_progress_ajax()
	{
		$response = $this->json( 'GET', '/export/' . $this->export->id . '/progress');

		$response->assertStatus(200);

		$response->assertJsonFragment( ['status' => 200] );

		$response->assertJsonFragment( ['progress'] );
	}

	/**
	* @test
	*/
	public function try_download_exported_file()
	{
		$response = $this->get('/export/' . $this->export->id . '/download');

		$response->assertHeader( 'content-disposition', 'attachment; filename="test.xls"');

		$response->assertStatus(200);
	}

	/**
	* @test
	*/
	public function fail_download_exported_file()
	{
		$response = $this->get('/export/987654321/download');

		$response->assertStatus(404);
	}
}
