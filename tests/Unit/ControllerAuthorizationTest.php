<?php

use App\Modules\Master\User\Controllers\UserController;

it('menyediakan method authorize untuk controller aplikasi', function (): void {
    expect(method_exists(UserController::class, 'authorize'))->toBeTrue();
});
