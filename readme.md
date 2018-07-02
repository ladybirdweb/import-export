# import-export

[![Build Status](https://travis-ci.org/ladybirdweb/import-export.svg?branch=master)](https://travis-ci.org/ladybirdweb/import-export)
[![Build Status](https://scrutinizer-ci.com/g/ladybirdweb/import-export/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ladybirdweb/import-export/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ladybirdweb/import-export/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ladybirdweb/import-export/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/ladybirdweb/import-export/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![StyleCI](https://github.styleci.io/repos/138305416/shield?branch=master)](https://github.styleci.io/repos/138305416)

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
