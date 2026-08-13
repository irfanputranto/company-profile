<?php

use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    config([
        'app.debug' => false,
        'theme.default' => 'ghibli',
    ]);

    Route::get('/__error-page-test/{status}', function (int $status): never {
        abort($status);
    })->whereNumber('status');
});

it('menampilkan tema dan ilustrasi vector pada halaman error khusus', function (int $status, string $title): void {
    $this->get("/__error-page-test/{$status}")
        ->assertStatus($status)
        ->assertSee('data-theme="ghibli"', false)
        ->assertSee("Status {$status}")
        ->assertSee($title)
        ->assertSee('assets/img/illustrations/error-anime.svg', false)
        ->assertSee('Kembali ke beranda')
        ->assertSee('content="noindex, nofollow"', false);
})->with([
    'unauthorized' => [401, 'Aksesmu perlu dikenali'],
    'payment required' => [402, 'Langkah ini memerlukan pembayaran'],
    'forbidden' => [403, 'Area ini sedang terkunci'],
    'not found' => [404, 'Halaman tersesat di dimensi lain'],
    'page expired' => [419, 'Sesi ini sudah tertidur'],
    'too many requests' => [429, 'Permintaan datang terlalu cepat'],
    'server error' => [500, 'Ada gangguan di pusat kendali'],
    'maintenance' => [503, 'Layanan sedang mengisi energi'],
]);

it('menggunakan tema fallback untuk status error lainnya', function (int $status, string $title): void {
    $this->get("/__error-page-test/{$status}")
        ->assertStatus($status)
        ->assertSee("Status {$status}")
        ->assertSee($title)
        ->assertSee('assets/img/illustrations/error-anime.svg', false);
})->with([
    'client error fallback' => [418, 'Permintaan belum dapat dilanjutkan'],
    'server error fallback' => [501, 'Sistem sedang memulihkan diri'],
]);

it('tetap mengembalikan json ketika klien meminta json', function (): void {
    $this->getJson('/__error-page-test/404')
        ->assertNotFound()
        ->assertJsonStructure(['message'])
        ->assertDontSee('error-anime.svg', false);
});
