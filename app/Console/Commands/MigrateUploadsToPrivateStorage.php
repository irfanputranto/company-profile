<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MigrateUploadsToPrivateStorage extends Command
{
    protected $signature = 'media:migrate-private
                            {--dry-run : Tampilkan file tanpa memindahkannya}
                            {--keep-public : Salin file tetapi pertahankan salinan publik}
                            {--keep-link : Pertahankan symlink public/storage}';

    protected $description = 'Pindahkan upload lama dari penyimpanan publik ke penyimpanan lokal privat';

    public function handle(): int
    {
        /** @var FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('legacy_public');
        /** @var FilesystemAdapter $privateDisk */
        $privateDisk = Storage::disk(config('filesystems.private_media_disk'));

        $files = collect([
            ...$publicDisk->allFiles('users/avatars'),
        ])->unique()->values();

        if ($files->isEmpty()) {
            $this->removePublicStorageLink();
            $this->components->info('Tidak ada upload publik yang perlu dipindahkan.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $files->each(fn (string $path) => $this->line($path));
            $this->components->info($files->count().' file siap dipindahkan ke storage privat.');

            return self::SUCCESS;
        }

        $moved = 0;

        foreach ($files as $path) {
            if ($privateDisk->exists($path)) {
                $sourceChecksum = hash_file('sha256', $publicDisk->path($path));
                $destinationChecksum = hash_file('sha256', $privateDisk->path($path));

                if ($sourceChecksum === false || ! hash_equals($sourceChecksum, (string) $destinationChecksum)) {
                    throw new RuntimeException("File privat sudah ada tetapi isinya berbeda: {$path}");
                }

                if (! $this->option('keep-public') && ! $publicDisk->delete($path)) {
                    throw new RuntimeException("Salinan publik gagal dihapus setelah diverifikasi: {$path}");
                }

                $moved++;
                $this->line("Sudah terverifikasi: {$path}");

                continue;
            }

            $sourceStream = $publicDisk->readStream($path);

            if (! is_resource($sourceStream)) {
                throw new RuntimeException("File publik gagal dibaca: {$path}");
            }

            try {
                if (! $privateDisk->writeStream($path, $sourceStream)) {
                    throw new RuntimeException("File privat gagal ditulis: {$path}");
                }
            } finally {
                fclose($sourceStream);
            }

            $sourceChecksum = hash_file('sha256', $publicDisk->path($path));
            $destinationChecksum = hash_file('sha256', $privateDisk->path($path));

            if ($sourceChecksum === false || ! hash_equals($sourceChecksum, (string) $destinationChecksum)) {
                $privateDisk->delete($path);

                throw new RuntimeException("Verifikasi salinan privat gagal: {$path}");
            }

            if (! $this->option('keep-public') && ! $publicDisk->delete($path)) {
                throw new RuntimeException("Salinan publik gagal dihapus setelah diverifikasi: {$path}");
            }

            $moved++;
            $verb = $this->option('keep-public') ? 'Disalin' : 'Dipindahkan';
            $this->line("{$verb}: {$path}");
        }

        $action = $this->option('keep-public') ? 'disalin' : 'dipindahkan';
        $this->components->info("{$moved} file berhasil {$action} ke storage privat dan telah diverifikasi.");
        $this->removePublicStorageLink();

        return self::SUCCESS;
    }

    private function removePublicStorageLink(): void
    {
        if ($this->option('dry-run') || $this->option('keep-public') || $this->option('keep-link')) {
            return;
        }

        $link = public_path('storage');

        if (! is_link($link)) {
            return;
        }

        $target = realpath($link);
        $expectedTarget = realpath(storage_path('app/public'));

        if ($target === false || $expectedTarget === false || $target !== $expectedTarget) {
            throw new RuntimeException('Symlink public/storage tidak mengarah ke storage publik yang dikenal.');
        }

        if (! unlink($link)) {
            throw new RuntimeException('Symlink public/storage gagal dinonaktifkan.');
        }

        $this->components->info('Symlink public/storage telah dinonaktifkan.');
    }
}
