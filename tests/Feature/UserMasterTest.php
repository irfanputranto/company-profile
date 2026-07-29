<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->role = Role::create(['name' => 'administrator', 'guard_name' => 'web']);
    $this->user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'user-admin', 'is_active' => true]);
    grantMasterPermissions($this->user, 'users');
});

it('menampilkan daftar pengguna', function () {
    $this->actingAs($this->user)->get(route('master.users.index'))->assertSuccessful()->assertSee('user-admin');
});

it('menyimpan pengguna beserta role', function () {
    $this->actingAs($this->user)->post(route('master.users.store'), ['name' => 'Udin Sanjaya', 'username' => 'udin', 'email' => 'udin@example.test', 'password' => 'Password123!', 'password_confirmation' => 'Password123!', 'role_id' => $this->role->id, 'is_active' => true])->assertRedirect(route('master.users.index'));
    expect(User::query()->where('username', 'udin')->first()?->hasRole('administrator'))->toBeTrue();
});

it('menolak email pengguna duplikat', function () {
    $this->actingAs($this->user)->post(route('master.users.store'), ['name' => 'Duplikat', 'username' => 'duplikat', 'email' => $this->user->email, 'password' => 'Password123!', 'password_confirmation' => 'Password123!', 'role_id' => $this->role->id, 'is_active' => true])->assertSessionHasErrors('email');
});

it('mengunggah dan mengoptimalkan foto pengguna menjadi webp', function () {
    Storage::fake('local');
    $photo = UploadedFile::fake()->image('avatar.jpg', 1200, 1000)->size(1500);

    $this->actingAs($this->user)->post(route('master.users.store'), [
        'name' => 'Udin Sanjaya',
        'username' => 'udin-photo',
        'email' => 'udin.photo@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'photo' => $photo,
        'role_id' => $this->role->id,
        'is_active' => true,
    ])->assertRedirect(route('master.users.index'));

    $user = User::query()->where('username', 'udin-photo')->firstOrFail();
    expect($user->avatar_path)->toEndWith('.webp');
    Storage::disk('local')->assertExists($user->avatar_path);
});

it('menolak foto pengguna lebih dari dua megabita', function () {
    Storage::fake('local');

    $this->actingAs($this->user)->post(route('master.users.store'), [
        'name' => 'Foto Besar',
        'username' => 'foto-besar',
        'email' => 'foto.besar@example.test',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'photo' => UploadedFile::fake()->image('besar.png')->size(2049),
        'role_id' => $this->role->id,
        'is_active' => true,
    ])->assertSessionHasErrors('photo');
});

it('menolak pengguna tanpa izin', function () {
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'no-access', 'is_active' => true]);
    $this->actingAs($user)->get(route('master.users.index'))->assertForbidden();
});
