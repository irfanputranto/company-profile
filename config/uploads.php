<?php

$maxFileSizeMb = max(1, (int) env('UPLOAD_MAX_FILE_SIZE_MB', 100));
$maxRequestSizeMb = max($maxFileSizeMb, (int) env('UPLOAD_MAX_REQUEST_SIZE_MB', 512));

return [
    'max_file_size_mb' => $maxFileSizeMb,
    'max_file_size_kb' => $maxFileSizeMb * 1024,
    'max_file_size_bytes' => $maxFileSizeMb * 1024 * 1024,
    'max_request_size_mb' => $maxRequestSizeMb,
];
