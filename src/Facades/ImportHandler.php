<?php

namespace LWS\ImportExport\Facades;

use Illuminate\Support\Facades\Facade;

class ImportHandler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'importhandler';
    }
}
