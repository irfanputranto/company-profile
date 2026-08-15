<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

it('hanya menampilkan latar utama pada fallback avatar', function (): void {
    $user = new class
    {
        public string $avatar_path = 'users/avatars/profile.webp';

        public string $name = 'Transparent Avatar';

        public function avatarUrl(): string
        {
            return '/avatars/profile.webp';
        }
    };

    $avatar = Blade::render('<x-adminpanel::components.user-avatar :user="$user" />', [
        'user' => $user,
    ]);
    View::share('errors', new ViewErrorBag);
    $photoUpload = Blade::render('<x-adminpanel::components.master.users.photo-upload :user="$user" />', [
        'user' => $user,
    ]);

    expect($avatar)
        ->toContain('class="relative size-full overflow-hidden rounded-full font-semibold"')
        ->toContain('class="absolute inset-0 size-full rounded-full object-cover"')
        ->toContain('class="bg-primary text-primary-content absolute inset-0 flex size-full items-center justify-center rounded-full"')
        ->not->toContain('class="bg-primary text-primary-content size-full rounded-full font-semibold"')
        ->and($photoUpload)->toContain('x-show="!preview"');
});
