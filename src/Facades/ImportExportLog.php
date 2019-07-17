<?php

namespace LWS\ImportExport\Facades;

use Illuminate\Support\Facades\Facade;

class ImportExportLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'importexportlog';
    }
}
