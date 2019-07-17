<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use LWS\ImportExport\Models\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LWS\ImportExport\Facades\ImportExportLog;
use LWS\ImportExport\Models\ImportExportLog as ModelImportExportLog;

class ImportExportLogTest extends TestCase
{
    use RefreshDatabase;

    protected $import;

    protected function getPackageProviders($app)
    {
        return ['LWS\ImportExport\ImportExportServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function setUp():void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->import = Import::create([
            'file' => 'imports/import-1530262997.csv',
            'file_rows' => 104,
            'db_cols' => ['name', 'email', 'password'],
            'model_map' => ['email', 'name', 'password'],
        ]);
    }

    /**
     * @test
     */
    public function save_new_log()
    {
        $import = $this->import;

        $result = ImportExportLog::logImportError($import, ['data' => 'this is test data'], 'This is not expected');

        $this->assertInstanceOf(ModelImportExportLog::class, $result);
    }

    /**
     * @test
     */
    public function get_saved_logs()
    {
        $import = $this->import;

        $log = ImportExportLog::logImportError($import, ['data' => 'this is test data'], 'This is not expected');

        $log_in_db = ImportExportLog::getLogs($log->id);

        $this->assertInternalType('array', $log_in_db);

        $this->assertArrayHasKey('data', $log_in_db[0]);

        $this->assertArrayHasKey('message', $log_in_db[0]);
    }
}
