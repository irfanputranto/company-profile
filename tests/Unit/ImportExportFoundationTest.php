<?php

use App\Exports\GenericExport;
use App\Imports\Base\BaseImport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

it('menyediakan export generik dengan heading dari data', function (): void {
    $export = new GenericExport([
        ['name' => 'Artikel Pertama', 'status' => 'published'],
    ]);

    expect($export)
        ->toBeInstanceOf(FromArray::class)
        ->toBeInstanceOf(WithHeadings::class)
        ->and($export->headings())->toBe(['name', 'status'])
        ->and($export->array())->toBe([
            ['name' => 'Artikel Pertama', 'status' => 'published'],
        ]);
});

it('menyediakan import generik berbasis heading row', function (): void {
    $import = new BaseImport(['validation' => ['*.name' => ['required']]]);

    expect($import)
        ->toBeInstanceOf(ToArray::class)
        ->toBeInstanceOf(WithHeadingRow::class)
        ->and($import->validation)->toHaveKey('validation');
});
