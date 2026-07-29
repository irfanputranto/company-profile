<?php

namespace App\Imports\Base;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BaseImport implements ToArray, WithHeadingRow
{
    use Importable;

    /** @param array<string, mixed> $validation */
    public function __construct(public readonly array $validation = []) {}

    public function array(array $array): void
    {
        //
    }
}
