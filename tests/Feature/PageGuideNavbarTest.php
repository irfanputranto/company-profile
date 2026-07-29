<?php

use Illuminate\Support\Facades\Route;

it('menampilkan panduan akses pada navbar', function (string $slug, string $routeName, string $guideTitle): void {
    $path = "/_test/page-guide/{$slug}";

    Route::get($path, fn () => view('adminpanel.layouts.header'))
        ->name($routeName);

    $this->get($path)
        ->assertSuccessful()
        ->assertSeeText('Panduan')
        ->assertSeeText($guideTitle)
        ->assertSee('aria-controls="global-page-guide-dialog"', false);
})->with([
    'pengguna' => ['users', 'master.users.navbar-test', 'Panduan Pengguna'],
    'role' => ['roles', 'master.roles.navbar-test', 'Panduan Hak Akses'],
    'permission' => ['permissions', 'master.permissions.navbar-test', 'Panduan Hak Akses'],
    'activity log' => ['activity-logs', 'system.activity-logs.navbar-test', 'Panduan Activity Log'],
]);
