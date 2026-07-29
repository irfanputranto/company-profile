<?php

use App\Services\PageGuideResolver;
use Illuminate\Config\Repository;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

function pageGuideResolver(string $locale = 'en'): PageGuideResolver
{
    $loader = new ArrayLoader;
    $loader->addMessages($locale, 'admin', require dirname(__DIR__, 2)."/lang/{$locale}/admin.php");

    return new PageGuideResolver(
        new Repository([
            'page_guides' => require dirname(__DIR__, 2).'/config/page_guides.php',
        ]),
        new Translator($loader, $locale),
    );
}

it('memberikan panduan sesuai kelompok halaman skeleton', function (string $routeName, string $expectedTitle): void {
    $resolver = pageGuideResolver();

    expect($resolver->resolve($routeName))
        ->toHaveKey('title', $expectedTitle)
        ->toHaveKey('description')
        ->toHaveKey('steps');
})->with([
    'dashboard' => ['dashboard', 'Dashboard Guide'],
    'profil' => ['profile', 'Profile Guide'],
    'pengguna' => ['master.users.edit', 'User Guide'],
    'role' => ['master.roles.index', 'Access Management Guide'],
    'permission' => ['master.permissions.create', 'Access Management Guide'],
    'activity log' => ['system.activity-logs.show', 'Activity Log Guide'],
]);

it('menggunakan panduan aman untuk halaman baru', function (): void {
    $resolver = pageGuideResolver('id');

    expect($resolver->resolve('feature.baru.index'))
        ->toHaveKey('title', 'Panduan Halaman')
        ->toHaveKey('steps');
});
