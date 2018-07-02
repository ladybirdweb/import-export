<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Ladybirdweb\ImportExport\Facades\ImportExportLog;
use Ladybirdweb\ImportExport\Models\ImportExportLog as ModelImportExportLog;
use Ladybirdweb\ImportExport\Models\Import;

class ImportExportLogTest extends TestCase
{
	use RefreshDatabase;

	protected $import;

	protected function getPackageProviders($app)
	{
	    return ['Ladybirdweb\ImportExport\ImportExportServiceProvider'];
	}

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

	protected function setUp ()
	{
	    parent::setUp();

	    $this->artisan('migrate', ['--database' => 'testing']);

		$this->import = Import::create([
			'file' => 'imports/import-1530262997.csv',
			'file_rows' => 104,
			'db_cols' => [ 'name', 'email', 'password'],
			'model_map' => ['email', 'name', 'password']
		]);
	}

	/**
	* @test
	*/
	public function save_new_log()
	{
		$import = $this->import;

		$result = ImportExportLog::logImportError( $import, ['data' => 'this is test data'], 'This is not expected' );

		$this->assertInstanceOf( ModelImportExportLog::class, $result );
	}

	/**
	* @test
	*/
	public function get_saved_logs()
	{
		$import = $this->import;

		$log = ImportExportLog::logImportError( $import, ['data' => 'this is test data'], 'This is not expected' );

		$log_in_db = ImportExportLog::getLogs( $log->id );

		$this->assertInternalType( 'array', $log_in_db );

		$this->assertArrayHasKey( 'data', $log_in_db[0] );

		$this->assertArrayHasKey( 'message', $log_in_db[0] );
	}
}
