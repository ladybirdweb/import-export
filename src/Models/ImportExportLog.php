<?php

namespace LWS\ImportExport\Models;

use Illuminate\Database\Eloquent\Model;

class ImportExportLog extends Model
{
    protected $fillable = ['op_id', 'data', 'message'];

    /**
     * Relationship with import model.
     *
     * @return void
     */
    public function import()
    {
        return $this->belongsTo(\Ladybirdweb\ImportExport\Models\Import::class);
    }

    /**
     * Model Map accessor.
     *
     * Unserialize data while retriving.
     * @param $value
     * @return string
     */
    public function getDataAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Model Map mulator.
     *
     * Serialize data while storing.
     * @param $value
     * @return void
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = serialize($value);
    }

    /**
     * Model Map accessor.
     *
     * Unserialize message while retriving.
     * @param $value
     * @return string
     */
    public function getMessageAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Model Map mulator.
     *
     * Serialize message while storing.
     * @param $value
     * @return void
     */
    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = serialize($value);
    }
}
