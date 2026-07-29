<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->viewArticles = Permission::create(['name' => 'view_articles', 'guard_name' => 'web']);
    $this->createArticles = Permission::create(['name' => 'create_articles', 'guard_name' => 'web']);
    $this->user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'role-admin', 'is_active' => true]);
    grantMasterPermissions($this->user, 'roles');
});

it('menampilkan role beserta permission', function () {
    $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $role->givePermissionTo($this->createArticles);

    $this->actingAs($this->user)->get(route('master.roles.index'))
        ->assertSuccessful()
        ->assertSee('Editor')
        ->assertSee('create_articles');
});

it('menyimpan satu role dengan banyak permission', function () {
    $this->actingAs($this->user)->post(route('master.roles.store'), [
        'name' => 'Content Manager',
        'permission_ids' => [(string) $this->viewArticles->id, (string) $this->createArticles->id],
    ])->assertRedirect(route('master.roles.index'))->assertSessionMissing('alert');

    $role = Role::findByName('content manager');
    expect($role->permissions()->pluck('permissions.id')->sort()->values()->all())
        ->toBe(collect([$this->viewArticles->id, $this->createArticles->id])->sort()->values()->all());
});

it('menyinkronkan ulang permission role', function () {
    $role = Role::create(['name' => 'operator', 'guard_name' => 'web']);
    $role->givePermissionTo($this->viewArticles);

    $this->actingAs($this->user)->put(route('master.roles.update', $role->id), [
        'name' => 'operator',
        'permission_ids' => [(string) $this->createArticles->id],
    ])->assertRedirect(route('master.roles.index'))->assertSessionMissing('alert');

    expect($role->fresh()->permissions()->pluck('permissions.id')->all())->toBe([$this->createArticles->id]);
});

it('melindungi role sistem dan role yang digunakan pengguna', function () {
    $systemRole = Role::create(['name' => 'administrator', 'guard_name' => 'web']);
    $assignedRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
    $assignedRole->givePermissionTo($this->createArticles);
    $assignedUser = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'assigned-role', 'is_active' => true]);
    $assignedUser->assignRole($assignedRole);

    $this->actingAs($this->user)->delete(route('master.roles.destroy', $systemRole->id))->assertSessionHas('error_message');
    $this->actingAs($this->user)->delete(route('master.roles.destroy', $assignedRole->id))->assertSessionHas('error_message');

    expect($systemRole->fresh())->not->toBeNull()->and($assignedRole->fresh())->not->toBeNull();
});

it('menolak pengguna tanpa izin mengelola role', function () {
    $user = User::factory()->create(['uuid' => (string) Str::uuid(), 'username' => 'role-no-access', 'is_active' => true]);

    $this->actingAs($user)->get(route('master.roles.index'))->assertForbidden();
});
