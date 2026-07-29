<?php

use App\Services\OptimizedImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('mengonversi gambar konten menjadi webp responsif beserta metadata', function () {
    Storage::fake('content_media');

    $media = app(OptimizedImageService::class)->storeResponsiveContent(
        image: UploadedFile::fake()->image('portfolio.jpg', 2400, 1600),
        directory: 'company-profile/projects',
        altText: 'Tampilan proyek Irfan Putranto Pratama',
    );

    expect($media->path)
        ->toEndWith('.webp')
        ->and($media->mime_type)->toBe('image/webp')
        ->and($media->variants)->toHaveCount(3)
        ->and($media->variants->pluck('name')->all())
        ->toEqualCanonicalizing(['thumbnail', 'card', 'hero']);

    Storage::disk('content_media')->assertExists($media->path);

    foreach ($media->variants as $variant) {
        Storage::disk('content_media')->assertExists($variant->path);
        expect($variant->mime_type)->toBe('image/webp');
    }
});
