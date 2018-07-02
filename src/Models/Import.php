<?php

namespace Ladybirdweb\ImportExport\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = ['file', 'file_rows', 'db_cols', 'row_processed'];

    /**
     * Relationship with import export log model.
     *
     * @return void
     */
    public function importLogs()
    {
        return $this->hasMany(\Ladybirdweb\ImportExport\Models\ImportExportLog::class, 'op_id');
    }

    /**
     * Model Map accessor.
     *
     * Unserialize model map while retriving.
     * @param $value
     * @return string
     */
    public function getModelMapAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Model Map mulator.
     *
     * Serialize model map while storing.
     * @param $value
     * @return void
     */
    public function setModelMapAttribute($value)
    {
        $this->attributes['model_map'] = serialize($value);
    }

    /**
     * DB cols accessor.
     *
     * Unserialize model map while retriving.
     * @param $value
     * @return string
     */
    public function getDbColsAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Model Map mulator.
     *
     * Serialize model map while storing.
     * @param $value
     * @return void
     */
    public function setDbColsAttribute($value)
    {
        $this->attributes['db_cols'] = serialize($value);
    }
}
