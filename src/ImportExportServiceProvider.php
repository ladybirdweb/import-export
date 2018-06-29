<?php

namespace Ladybirdweb\ImportExport;

use Illuminate\Support\ServiceProvider;

class ImportExportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register routes
        $this->loadRoutesFrom( __DIR__ . '/routes.php' );

        // Register database migrations
        $this->loadMigrationsFrom( __DIR__ . '/../database/migrations' );

        // Load views
        $this->loadViewsFrom( __DIR__ . '/../resources/views', 'importexport' );


        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'ladybirdweb');

        // Publishing is only necessary when using the CLI.
        if ( $this->app->runningInConsole() ) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__ . '/../config/import.php' => config_path('ladybirdweb/import-export/import.php'),
            ], 'import-export.config');
            
            $this->publishes([
                __DIR__ . '/../config/export.php' => config_path('ladybirdweb/import-export/export.php'),
            ], 'import-export.config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/ladybirdweb/import-export'),
            ], 'import-export.views');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../public/assets' => public_path('vendor/ladybirdweb/import-export'),
            ], 'import-export.assets');
            
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom( __DIR__ . '/../config/import.php', 'import' );
        $this->mergeConfigFrom( __DIR__ . '/../config/export.php', 'export' );

        // Register fecades
        $this->app->singleton( 'import', 'Ladybirdweb\ImportExport\Import' );
        $this->app->singleton( 'importhandler', 'Ladybirdweb\ImportExport\ImportHandler' );
        $this->app->singleton( 'importexportlog', 'Ladybirdweb\ImportExport\ImportExportLog' );

        $this->app->singleton( 'export', 'Ladybirdweb\ImportExport\Export' );

        // Register import controller
        $this->app->singleton( 'Ladybirdweb\ImportExport\Import' );

        // Register import handler
        $this->app->singleton( 'Ladybirdweb\ImportExport\ImportHandler' );
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ImportExport'];
    }
}
