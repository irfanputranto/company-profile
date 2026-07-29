<?php

if (! function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        if ($bytes < 1) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];

        $base = floor(log($bytes, 1024));
        $base = min($base, count($units) - 1);

        $value = $bytes / pow(1024, $base);

        return round($value, $precision) . ' ' . $units[$base];
    }
}
