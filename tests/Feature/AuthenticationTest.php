<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menampilkan halaman login kepada tamu', function (): void {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertViewIs('auth.login');
});

it('masuk menggunakan username atau email', function (string $login): void {
    $user = User::factory()->create([
        'username' => 'login-user',
        'email' => 'login@example.test',
        'password' => 'password-rahasia',
    ]);

    $this->post(route('login'), [
        'login' => "  {$login}  ",
        'password' => 'password-rahasia',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
})->with([
    'username' => 'login-user',
    'email' => 'login@example.test',
]);

it('menolak kredensial yang salah', function (): void {
    User::factory()->create([
        'username' => 'login-user',
        'password' => 'password-rahasia',
    ]);

    $this->from(route('login'))->post(route('login'), [
        'login' => 'login-user',
        'password' => 'password-salah',
    ])->assertRedirect(route('login'))
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

it('menolak pengguna yang dinonaktifkan', function (): void {
    User::factory()->create([
        'username' => 'user-nonaktif',
        'password' => 'password-rahasia',
        'is_active' => false,
    ]);

    $this->from(route('login'))->post(route('login'), [
        'login' => 'user-nonaktif',
        'password' => 'password-rahasia',
    ])->assertRedirect(route('login'))
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

it('mewajibkan login dan password', function (): void {
    $this->from(route('login'))->post(route('login'), [])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['login', 'password']);
});

it('mengakhiri sesi pengguna saat logout', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
