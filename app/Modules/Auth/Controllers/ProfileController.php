<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\UpdateProfilePhotoRequest;
use App\Services\OptimizedImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('adminpanel.pages.profile.show');
    }

    public function updatePhoto(UpdateProfilePhotoRequest $request, OptimizedImageService $optimizedImageService): RedirectResponse
    {
        $user = $request->user();
        $oldAvatarPath = $user->avatar_path;
        $newAvatarPath = null;

        try {
            if ($request->hasFile('photo')) {
                $newAvatarPath = $optimizedImageService->store($request->file('photo'), 'users/avatars');
            }

            $avatarPath = $newAvatarPath ?: ($request->boolean('remove_photo') ? null : $oldAvatarPath);
            DB::transaction(function () use ($user, $avatarPath): void {
                $user->forceFill(['avatar_path' => $avatarPath, 'updated_by' => $user->id])->save();
            });
        } catch (Throwable $throwable) {
            if ($newAvatarPath) {
                Storage::disk(config('filesystems.private_media_disk'))->delete($newAvatarPath);
            }

            throw $throwable;
        }

        if ($oldAvatarPath && $oldAvatarPath !== $newAvatarPath && ($newAvatarPath || $request->boolean('remove_photo'))) {
            Storage::disk(config('filesystems.private_media_disk'))->delete($oldAvatarPath);
        }

        return back()->with('success_message', 'Foto profil berhasil diperbarui.');
    }
}
