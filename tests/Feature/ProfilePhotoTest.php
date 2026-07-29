<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('memperbarui foto profil pengguna yang sedang login', function () {
    Storage::fake('local');
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'profile-photo', 'is_active' => true]);

    $this->actingAs($user)->patch(route('profile.photo.update'), [
        'photo' => UploadedFile::fake()->image('profile.jpg', 1200, 900)->size(1500),
    ])->assertRedirect();

    $avatarPath = $user->fresh()->avatar_path;
    expect($avatarPath)->toEndWith('.webp');
    Storage::disk('local')->assertExists($avatarPath);
});

it('menghapus foto profil pengguna yang sedang login', function () {
    Storage::fake('local');
    Storage::disk('local')->put('users/avatars/old.webp', 'old-photo');
    $user = User::factory()->create([
        'uuid' => (string) Str::uuid(),
        'username' => 'profile-remove-photo',
        'avatar_path' => 'users/avatars/old.webp',
        'is_active' => true,
    ]);

    $this->actingAs($user)->patch(route('profile.photo.update'), [
        'remove_photo' => true,
    ])->assertRedirect();

    expect($user->fresh()->avatar_path)->toBeNull();
    Storage::disk('local')->assertMissing('users/avatars/old.webp');
});

it('menolak foto profil lebih dari dua megabita', function () {
    Storage::fake('local');
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'profile-large-photo', 'is_active' => true]);

    $this->actingAs($user)->patch(route('profile.photo.update'), [
        'photo' => UploadedFile::fake()->image('large.png')->size(2049),
    ])->assertSessionHasErrors('photo');
});
