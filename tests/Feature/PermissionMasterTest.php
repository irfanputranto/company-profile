<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'permission-admin', 'is_active' => true]);
    grantMasterPermissions($this->user, 'permissions');
});

it('menampilkan dan mencari permission', function () {
    Permission::create(['name' => 'view_articles', 'guard_name' => 'web']);

    $this->actingAs($this->user)->get(route('master.permissions.index', ['q' => 'articles']))
        ->assertSuccessful()
        ->assertSee('view_articles');
});

it('menyimpan permission baru dengan guard web', function () {
    $this->actingAs($this->user)->post(route('master.permissions.store'), [
        'name' => '  create_articles  ',
    ])->assertRedirect(route('master.permissions.index'));

    expect(Permission::query()->where('name', 'create_articles')->first())
        ->guard_name->toBe('web');
});

it('menolak permission duplikat', function () {
    Permission::create(['name' => 'view_articles', 'guard_name' => 'web']);

    $this->actingAs($this->user)->post(route('master.permissions.store'), [
        'name' => 'view_articles',
    ])->assertSessionHasErrors('name');
});

it('melindungi permission yang sudah digunakan role', function () {
    $permission = Permission::create(['name' => 'update_articles', 'guard_name' => 'web']);
    $role = Role::create(['name' => 'content-manager', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);

    $this->actingAs($this->user)->put(route('master.permissions.update', $permission->id), [
        'name' => 'delete_articles',
    ])->assertSessionHas('error_message');

    $this->actingAs($this->user)->delete(route('master.permissions.destroy', $permission->id))
        ->assertSessionHas('error_message');

    expect($permission->fresh()->name)->toBe('update_articles');
});

it('menolak pengguna tanpa izin mengelola permission', function () {
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'permission-no-access', 'is_active' => true]);

    $this->actingAs($user)->get(route('master.permissions.index'))->assertForbidden();
});
