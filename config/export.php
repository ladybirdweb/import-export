<?php

return [
    // Route for export process ajax
    'export_progress' => [
        'url' => '/export/{id}/progress',
        'name' => 'ladybirdweb.export.ajax.progress',
    ],

    // Route for download exported file
    'export_download' => [
        'url' => '/export/{id}/download',
        'name' => 'ladybirdweb.export.download',
    ],
];
