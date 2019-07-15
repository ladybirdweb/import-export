<?php

namespace Tests;

use Illuminate\Http\File;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Ladybirdweb\ImportExport\Facades\Import;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ladybirdweb\ImportExport\Models\Import as ModelImport;

class ImportTest extends TestCase
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

    protected function setUp():void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing']);

        Route::middleware('web')->group(function () {
            Route::get('/import/{id}/progress', ['as' => 'ladybirdweb.import.ajax.progress', 'uses' => 'Ladybirdweb\ImportExport\Import@returnImportProgress']);
        });

        Storage::putFileAs('imports', new File(__DIR__.'/storage/test/test.csv'), 'test.csv');

        $this->import = ModelImport::create([
            'file' => 'imports/test.csv',
            'file_rows' => 104,
            'db_cols' => ['name', 'email', 'password'],
            'model_map' => ['email', 'name', 'password'],
        ]);
    }

    /**
     * @test
     */
    public function create_new_import_success()
    {
        $import = Import::createImport('imports/test.csv', ['name', 'email', 'password']);

        $this->assertInstanceOf(ModelImport::class, $import);
    }

    /**
     * @test
     */
    public function fetch_import()
    {
        $import = Import::getImport($this->import->id);

        $this->assertInstanceOf(ModelImport::class, $import);
    }

    /**
     * @test
     */
    public function get_few_rows_from_uploaded_file()
    {
        $csv_data = Import::getImportFileData($this->import->id);

        $this->assertInternalType('array', $csv_data);
    }

    /**
     * @test
     */
    public function store_data_map_with_csv_cols()
    {
        $import = Import::setDataMap(['email', 'name', 'password'], $this->import->id);

        $this->assertInstanceOf(ModelImport::class, $import);
    }

    /**
     * @test
     */
    public function sucess_to_dispatch_given_job_class()
    {
        $import = $id = $this->import;

        Queue::fake();

        Import::dispatchImportJob(FakeJob::class, $import);

        Queue::assertPushedOn('importing', FakeJob::class);
    }

    /**
     * @test
     */
    public function check_import_progress()
    {
        $response = $this->json('GET', '/import/1/progress');

        $response->assertStatus(200);

        $response->assertJsonFragment(['status' => 200]);

        $response->assertJsonFragment(['progress'=>0]);
    }

    /**
     * @test
     */
    public function remove_import()
    {
        $this->assertTrue(Import::removeImport($this->import->id));
    }
}
