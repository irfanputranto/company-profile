<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menampilkan ringkasan fondasi aplikasi', function (): void {
    $user = User::factory()->create(['name' => 'Admin Skeleton']);
    Role::findOrCreate('administrator', 'web');
    Permission::findOrCreate('access adminpanel', 'web');

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSeeText('Admin Skeleton')
        ->assertSeeText('Skeleton siap dikembangkan')
        ->assertSeeText('Pengguna aktif')
        ->assertSeeText('Permission');
});

it('mengarahkan tamu dari dashboard ke halaman login', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
