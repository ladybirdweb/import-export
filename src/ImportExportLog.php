<?php

namespace Ladybirdweb\ImportExport;

use Ladybirdweb\ImportExport\Models\Import;

class ImportExportLog
{
    public function logImportError(Import $import, $data, $message)
    {
        // Create new log
        return $import->importLogs()->create([
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function getLogs($id)
    {
        // Get all logs of a import or export process
        return Import::findOrFail($id)->importLogs()->get(['data', 'message'])->toArray();
    }
}
