<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureMediaController extends Controller
{
    public function userAvatar(string $user): StreamedResponse
    {
        $model = User::query()
            ->where('uuid', $user)
            ->whereNotNull('avatar_path')
            ->firstOrFail();

        return $this->respond(
            path: $model->avatar_path,
            expectedDirectory: 'users/avatars',
            filename: 'avatar-'.$model->uuid.'.webp',
        );
    }

    private function respond(string $path, string $expectedDirectory, string $filename): StreamedResponse
    {
        abort_unless(
            preg_match('#^'.preg_quote($expectedDirectory, '#').'/[0-9a-f-]+\.webp$#i', $path) === 1,
            404,
        );

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk(config('filesystems.private_media_disk'));
        abort_unless($disk->exists($path), 404);

        return $disk->response($path, $filename, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow, noimageindex, noarchive, nosnippet',
        ]);
    }
}
