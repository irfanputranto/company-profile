<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use RuntimeException;

class OptimizedImageService
{
    public function store(UploadedFile $image, string $directory, int $maxWidth = 800, int $maxHeight = 800, int $quality = 50): string
    {
        $directory = trim($directory, '/');
        $realPath = $image->getRealPath();

        if (! $image->isValid() || $realPath === false || ! is_file($realPath)) {
            throw new InvalidArgumentException('File gambar tidak valid.');
        }

        if ($directory === '' || str_contains($directory, '..') || preg_match('/^[A-Za-z0-9_\/-]+$/', $directory) !== 1) {
            throw new InvalidArgumentException('Direktori penyimpanan gambar tidak valid.');
        }

        $encodedImage = ImageManager::gd()
            ->read($realPath)
            ->orient()
            ->scaleDown(width: $maxWidth, height: $maxHeight)
            ->toWebp(quality: $quality, strip: true);

        $path = $directory.'/'.Str::uuid().'.webp';

        if (! Storage::disk(config('filesystems.private_media_disk'))->put($path, $encodedImage->toString())) {
            throw new RuntimeException('Gambar gagal disimpan.');
        }

        return $path;
    }
}
