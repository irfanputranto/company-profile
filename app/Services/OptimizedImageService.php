<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

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

    /**
     * Convert a public content image into WebP and persist responsive variants.
     *
     * @param  array<string, array{width: int, height: int, quality: int}>|null  $variants
     */
    public function storeResponsiveContent(
        UploadedFile $image,
        string $directory,
        ?string $altText = null,
        ?int $uploadedBy = null,
        ?array $variants = null,
    ): Media {
        $directory = $this->validatedDirectory($directory);
        $realPath = $this->validatedRealPath($image);
        $disk = (string) config('filesystems.public_media_disk');
        $uuid = (string) Str::uuid();
        $storedPaths = [];
        $variantDefinitions = $variants ?? [
            'thumbnail' => ['width' => 320, 'height' => 320, 'quality' => 72],
            'card' => ['width' => 768, 'height' => 768, 'quality' => 78],
            'hero' => ['width' => 1600, 'height' => 1200, 'quality' => 82],
        ];

        try {
            $primaryImage = ImageManager::gd()
                ->read($realPath)
                ->orient()
                ->scaleDown(width: 2000, height: 1600);
            $primaryContents = $primaryImage->toWebp(quality: 84, strip: true)->toString();
            $primaryPath = "{$directory}/{$uuid}.webp";

            $this->putOrFail($disk, $primaryPath, $primaryContents);
            $storedPaths[] = $primaryPath;

            return DB::transaction(function () use (
                $altText,
                $disk,
                $image,
                $primaryContents,
                $primaryImage,
                $primaryPath,
                $realPath,
                &$storedPaths,
                $uploadedBy,
                $uuid,
                $variantDefinitions,
            ): Media {
                $media = Media::query()->create([
                    'uuid' => $uuid,
                    'uploaded_by' => $uploadedBy,
                    'disk' => $disk,
                    'path' => $primaryPath,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => 'image/webp',
                    'extension' => 'webp',
                    'byte_size' => mb_strlen($primaryContents, '8bit'),
                    'width' => $primaryImage->width(),
                    'height' => $primaryImage->height(),
                    'alt_text' => $altText,
                    'created_by' => $uploadedBy,
                    'updated_by' => $uploadedBy,
                ]);

                foreach ($variantDefinitions as $name => $definition) {
                    $variantImage = ImageManager::gd()
                        ->read($realPath)
                        ->orient()
                        ->scaleDown(width: $definition['width'], height: $definition['height']);
                    $variantContents = $variantImage
                        ->toWebp(quality: $definition['quality'], strip: true)
                        ->toString();
                    $variantPath = "{$media->pathWithoutExtension()}-{$name}.webp";

                    $this->putOrFail($disk, $variantPath, $variantContents);
                    $storedPaths[] = $variantPath;
                    $media->variants()->create([
                        'name' => $name,
                        'disk' => $disk,
                        'path' => $variantPath,
                        'byte_size' => mb_strlen($variantContents, '8bit'),
                        'width' => $variantImage->width(),
                        'height' => $variantImage->height(),
                    ]);
                }

                return $media->load('variants');
            });
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($storedPaths);

            throw $exception;
        }
    }

    private function validatedRealPath(UploadedFile $image): string
    {
        $realPath = $image->getRealPath();

        if (! $image->isValid() || $realPath === false || ! is_file($realPath)) {
            throw new InvalidArgumentException('File gambar tidak valid.');
        }

        return $realPath;
    }

    private function validatedDirectory(string $directory): string
    {
        $directory = trim($directory, '/');

        if ($directory === '' || str_contains($directory, '..') || preg_match('/^[A-Za-z0-9_\/-]+$/', $directory) !== 1) {
            throw new InvalidArgumentException('Direktori penyimpanan gambar tidak valid.');
        }

        return $directory;
    }

    private function putOrFail(string $disk, string $path, string $contents): void
    {
        if (! Storage::disk($disk)->put($path, $contents)) {
            throw new RuntimeException('Gambar gagal disimpan.');
        }
    }
}
