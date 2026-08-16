<?php

use App\Support\MetaDescription;
use Illuminate\Support\Str;

it('menggunakan kandidat pertama yang memiliki isi', function (): void {
    expect(MetaDescription::make(null, '   ', 'Deskripsi artikel.'))
        ->toBe('Deskripsi artikel.');
});

it('membersihkan html entitas dan spasi berlebih', function (): void {
    $description = '<p>Jasa &amp; solusi</p>   <strong>aplikasi bisnis</strong>';

    expect(MetaDescription::make($description))
        ->toBe('Jasa & solusi aplikasi bisnis');
});

it('meringkas deskripsi panjang menjadi maksimal 160 karakter', function (): void {
    $description = str_repeat('Pengembangan aplikasi bisnis yang aman dan scalable. ', 8);
    $result = MetaDescription::make($description);

    expect(Str::length($result))
        ->toBeGreaterThanOrEqual(150)
        ->toBeLessThanOrEqual(160)
        ->and($result)->toEndWith('...');
});

it('tidak memotong deskripsi yang sudah singkat', function (): void {
    $description = 'Solusi aplikasi bisnis yang dirancang sesuai kebutuhan perusahaan.';

    expect(MetaDescription::make($description))->toBe($description);
});
