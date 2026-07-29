<?php

use App\Services\PageGuideResolver;
use Illuminate\Config\Repository;

it('memberikan panduan sesuai kelompok halaman skeleton', function (string $routeName, string $expectedTitle): void {
    $resolver = new PageGuideResolver(new Repository([
        'page_guides' => require dirname(__DIR__, 2).'/config/page_guides.php',
    ]));

    expect($resolver->resolve($routeName))
        ->toHaveKey('title', $expectedTitle)
        ->toHaveKey('description')
        ->toHaveKey('steps');
})->with([
    'dashboard' => ['dashboard', 'Panduan Dashboard'],
    'profil' => ['profile', 'Panduan Profil'],
    'pengguna' => ['master.users.edit', 'Panduan Pengguna'],
    'role' => ['master.roles.index', 'Panduan Hak Akses'],
    'permission' => ['master.permissions.create', 'Panduan Hak Akses'],
    'activity log' => ['system.activity-logs.show', 'Panduan Activity Log'],
]);

it('menggunakan panduan aman untuk halaman baru', function (): void {
    $resolver = new PageGuideResolver(new Repository([
        'page_guides' => require dirname(__DIR__, 2).'/config/page_guides.php',
    ]));

    expect($resolver->resolve('feature.baru.index'))
        ->toHaveKey('title', 'Panduan Halaman')
        ->toHaveKey('steps');
});
