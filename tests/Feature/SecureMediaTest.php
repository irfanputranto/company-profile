<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function grantSecureMediaAccess(User $user): void
{
    $user->givePermissionTo(Permission::findOrCreate('access adminpanel', 'web'));
}

it('melarang pengunjung membuka avatar privat', function (): void {
    Storage::fake('local');
    $user = User::factory()->create(['avatar_path' => 'users/avatars/'.Str::uuid().'.webp']);
    Storage::disk('local')->put($user->avatar_path, 'private-image');

    $this->get(route('secure-media.users.avatar', $user->uuid))
        ->assertRedirect(route('login'));
});

it('mengirim avatar hanya kepada pengguna berizin dengan header privat', function (): void {
    Storage::fake('local');
    $viewer = User::factory()->create();
    grantSecureMediaAccess($viewer);
    $owner = User::factory()->create(['avatar_path' => 'users/avatars/'.Str::uuid().'.webp']);
    Storage::disk('local')->put($owner->avatar_path, 'private-image');

    $response = $this->actingAs($viewer)->get(route('secure-media.users.avatar', $owner->uuid));

    $response->assertSuccessful()
        ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noimageindex, noarchive, nosnippet')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
    expect($response->streamedContent())->toBe('private-image');
});

it('menolak path avatar di luar direktori yang diizinkan', function (): void {
    Storage::fake('local');
    $viewer = User::factory()->create();
    grantSecureMediaAccess($viewer);
    $owner = User::factory()->create(['avatar_path' => 'documents/'.Str::uuid().'.webp']);
    Storage::disk('local')->put($owner->avatar_path, 'private-image');

    $this->actingAs($viewer)
        ->get(route('secure-media.users.avatar', $owner->uuid))
        ->assertNotFound();
});

it('menolak pengguna tanpa akses adminpanel', function (): void {
    Storage::fake('local');
    $viewer = User::factory()->create();
    $owner = User::factory()->create(['avatar_path' => 'users/avatars/'.Str::uuid().'.webp']);
    Storage::disk('local')->put($owner->avatar_path, 'private-image');

    $this->actingAs($viewer)
        ->get(route('secure-media.users.avatar', $owner->uuid))
        ->assertForbidden();
});
