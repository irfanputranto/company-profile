<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('adminpanel.pages.dashboard.index', [
            'activeUsersCount' => User::query()->where('is_active', true)->count(),
            'rolesCount' => Role::query()->count(),
            'permissionsCount' => Permission::query()->count(),
            'greeting' => $this->greeting(),
        ]);
    }

    private function greeting(): string
    {
        return match (true) {
            now()->hour < 11 => __('admin.dashboard.morning'),
            now()->hour < 15 => __('admin.dashboard.afternoon'),
            now()->hour < 18 => __('admin.dashboard.evening'),
            default => __('admin.dashboard.night'),
        };
    }
}
