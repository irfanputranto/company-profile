<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\MasterPermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionNames = [
            'access adminpanel',
            'view_activity_logs',
            ...MasterPermission::all(),
        ];

        foreach ($permissionNames as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', MasterPermission::LEGACY_PERMISSIONS)
            ->delete();

        $allPermissions = Permission::query()->where('guard_name', 'web')->get();

        Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', ['administrator', 'superadmin'])
            ->each(fn (Role $role) => $role->syncPermissions($allPermissions));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
