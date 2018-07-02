<?php

namespace Ladybirdweb\ImportExport\Models;

use Illuminate\Database\Eloquent\Model;

class Export extends Model
{
    protected $fillable = ['file', 'type', 'query', 'result_rows'];

    /**
     * Query accessor.
     *
     * Unserialize query while retriving.
     * @param $value
     * @return string
     */
    public function getQueryAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Query mulator.
     *
     * Serialize query while storing.
     * @param $value
     * @return void
     */
    public function setQueryAttribute($value)
    {
        $this->attributes['query'] = serialize($value);
    }
}
