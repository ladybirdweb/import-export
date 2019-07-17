<?php

namespace Tests;

use Illuminate\Http\File;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use LWS\ImportExport\Facades\Export;
use LWS\ImportExport\Jobs\ExportJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LWS\ImportExport\Models\Export as ModelExport;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected $export;

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

        Route::middleware('web')->group(function () {
            Route::get('/ticket/export/{id}', ['as' => 'ticket.export.progress', 'uses' => 'LWS\ImportExport\Export@showExportStatus']);

            Route::get('/export/{id}/download', ['as' => 'ladybirdweb.export.download', 'uses' => 'LWS\ImportExport\Export@downloadExportedFile']);
        });

        Storage::putFileAs('exports', new File(__DIR__.'/storage/test/test.csv'), 'test.xls');

        $this->export = ModelExport::create([
            'file' => 'test.xls',
            'query' => User::select(['name', 'email', 'created_at'])->getModel(),
            'type' => 'xls',
        ]);
    }

    /**
     * @test
     */
    public function data_export_initiated_and_dispatched()
    {
        Queue::fake();

        $export = Export::export(User::select(['name', 'email', 'created_at']), 'xls');

        $this->assertInstanceOf(ModelExport::class, $export);

        Queue::assertPushedOn('exporting', ExportJob::class);
    }

    /**
     * @test
     */
    public function check_export_progress_ajax()
    {
        $response = $this->json('GET', '/export/'.$this->export->id.'/progress');

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 200]);

        $response->assertJsonFragment(['progress'=>0]);
    }

    /**
     * @test
     */
    public function try_download_exported_file()
    {
        $response = $this->get('/export/'.$this->export->id.'/download');

        $response->assertHeader('content-disposition', 'attachment; filename=test.xls');

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
