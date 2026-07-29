<?php

namespace App\Support;

class SecureUploadRules
{
    /** @return array<int, string> */
    public static function image(string $presence = 'nullable'): array
    {
        return [
            $presence,
            'image',
            'mimes:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:'.config('uploads.max_file_size_kb'),
            'dimensions:max_width=6000,max_height=6000',
        ];
    }

    /** @return array<int, string> */
    public static function imageConstraints(): array
    {
        return array_slice(self::image(), 1);
    }

    /** @return array<int, string> */
    public static function spreadsheet(): array
    {
        return [
            'required',
            'file',
            'mimes:xlsx,xls,csv',
            'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain',
            'max:'.config('uploads.max_file_size_kb'),
        ];
    }
}
