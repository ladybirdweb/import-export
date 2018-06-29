<?php

namespace Tests;

use Tests\TestCase;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Ladybirdweb\ImportExport\Facades\Import;
use Ladybirdweb\ImportExport\Models\Import as ModelImport;

class ImportTest extends TestCase
{
	use RefreshDatabase;

	protected $import;

	protected function setUp ()
	{
	    parent::setUp();

	    Route::middleware('web')->group(function() {

		    Route::get('ticket/import', ['as' => 'ticket.import', 'uses' => 'Ladybirdweb\ImportExport\Import@showImportForm'] );
		    Route::post('ticket/import', ['as' => 'ticket.import', 'uses' => 'Ladybirdweb\ImportExport\Import@uploadImportFile'] );

			Route::get('/ticket/import/{id}/map', [ 'as' => 'ticket.import.show.map', 'uses' => 'Ladybirdweb\ImportExport\Import@showColumnsMapForm']);
			Route::post('/ticket/import/{id}', [ 'as' => 'ticket.import.map', 'uses' => 'Ladybirdweb\ImportExport\Import@storeColumnsMap']);

			Route::get( '/import/{id}/progress', [ 'as' => 'ladybirdweb.import.ajax.progress', 'uses' => 'Ladybirdweb\ImportExport\Import@returnImportProgress']);

		});

		Storage::putFileAs('imports', new File( storage_path( 'test/test.csv' ) ), 'test.csv');

		$this->import = ModelImport::create([
			'file' => 'imports/test.csv',
			'file_rows' => 104,
			'db_cols' => [ 'name', 'email', 'password'],
			'model_map' => ['email', 'name', 'password']
		]);
	}

	/**
	* @test
	*/
	public function see_import_form()
	{
		Import::setUploadRoute('ticket.import');

		$response = $this->get('ticket/import');

		$response->assertSee( 'importer' );
	}

	/**
	* @test
	*/
	public function try_file_upload()
	{
		Storage::fake('file');

        $response = $this->json( 'POST', '/ticket/import', [
            'file' => UploadedFile::fake()->create('file.csv', 100)
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment( ['status' => 'ready'] );
	}

	/**
	* @test
	*/
	public function fail_file_upload()
	{
		Storage::fake('file');

        $response = $this->json( 'POST', '/ticket/import', [
            'file' => UploadedFile::fake()->image('file.jpg')
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment( ['status' => 'failed'] );

        $this->assertArrayHasKey( 'errors', Import::getImportErrors() );
	}

	/**
	* @test
	*/
	public function map_data_with_csv_cols()
	{
		Import::setImportMapRoute('ticket.import.map');

		$id = $this->import->id;

		$response = $this->get('/ticket/import/' . $id . '/map');

		$response->assertStatus(200);

		$response->assertSee( 'Confirm Import' );
	}

	/**
	* @test
	*/
	public function store_data_map_with_csv_cols()
	{
		$id = $this->import->id;

		$response = $this->post( '/ticket/import/' . $id, [
			'db_column' => ['email', 'name', 'password']
		] );

		$response->assertStatus(200);

		$response->assertSessionMissing( 'errors' );
	}

	/**
	* @test
	*/
	public function store_data_map_with_csv_cols_failed_validation()
	{
		$id = $this->import->id;

		$response = $this->post( '/ticket/import/' . $id, [
			'db_column' => ['name', 'name', 'name']
		] );

		$response->assertStatus(302);

		$response->assertSessionHas( 'errors' );
	}

	/**
	* @test
	*/
	public function sucess_to_dispatch_given_job_class()
	{
		$import = $id = $this->import;

		Queue::fake();

		Import::dispatchImportJob( FakeJob::class, $import );

		Queue::assertPushedOn('importing', FakeJob::class);
	}

	/**
	* @test
	*/
	public function check_import_progress()
	{
		$response = $this->json( 'GET', '/import/1/progress' );

		$response->assertStatus(200);

		$response->assertJsonFragment( ['status' => 200] );

		$response->assertJsonFragment( ['progress'] );
	}
}
