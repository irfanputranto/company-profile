<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = (string) config('seeding.default_user_password');

        if (mb_strlen($defaultPassword) < 12) {
            throw new RuntimeException('SEED_DEFAULT_PASSWORD wajib diisi minimal 12 karakter.');
        }

        $role = Role::findOrCreate('administrator', 'web');
        $user = User::withTrashed()->firstOrNew(['email' => 'admin@example.test']);
        $user->fill([
            'username' => 'administrator',
            'name' => 'Administrator',
            'is_active' => true,
        ]);

        if (! $user->exists) {
            $user->password = $defaultPassword;
        }

        $user->forceFill([
            'uuid' => $user->uuid ?: (string) Str::uuid(),
            'deleted_by' => null,
            'deleted_at' => null,
        ])->saveQuietly();

        $user->forceFill([
            'created_by' => $user->created_by ?? $user->id,
            'updated_by' => $user->id,
        ])->saveQuietly();
        $user->syncRoles([$role]);
    }
}
