# import-export

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This package will help to import and export huge data using cron jobs.

## Installation

Via Composer

``` bash
$ composer require ladybirdweb/import-export
```

## Usage

After installation you need to add the following line to config/app.php -

```
'providers' => [
	/*
     * Package Service Providers...
     */
    Ladybirdweb\ImportExport\ImportExportServiceProvider::class,
]
```

If you want to use alias add the following -

```
'aliases' => [
	/*
     * Package Facades...
     */
    'Import' => Ladybirdweb\ImportExport\Facades\Import::class,
    'Export' => Ladybirdweb\ImportExport\Facades\Export::class,
    'ImportHandler' => Ladybirdweb\ImportExport\Facades\ImportHandler::class,
    'ImportExportLog' => Ladybirdweb\ImportExport\Facades\ImportExportLog::class,
]
```

After setup you need migrate the database using

``` bash
$ php artisan migrate
```

Publish configs, views, assets using -

``` bash
$ php artisan vendor:publish
```

## License

MIT. Please see the [license file](license.md) for more information.
