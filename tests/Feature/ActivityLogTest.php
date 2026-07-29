<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->administrator = User::factory()->create(['username' => 'activity-admin']);
    $this->administrator->givePermissionTo(Permission::findOrCreate('view_activity_logs', 'web'));
    Activity::query()->delete();
});

it('mencatat tambah ubah dan hapus pengguna beserta nilai detail', function (): void {
    $this->actingAs($this->administrator);

    $user = User::factory()->create(['name' => 'Pengguna Awal']);
    $user->update(['name' => 'Pengguna Baru']);
    $user->delete();

    $activities = Activity::query()->whereMorphedTo('subject', $user)->oldest()->get();

    expect($activities)->toHaveCount(3)
        ->and($activities->pluck('event')->all())->toBe(['created', 'updated', 'deleted'])
        ->and($activities->every(fn (Activity $activity): bool => $activity->causer_id === $this->administrator->id))->toBeTrue()
        ->and(data_get($activities[1]->properties, 'old.name'))->toBe('Pengguna Awal')
        ->and(data_get($activities[1]->properties, 'attributes.name'))->toBe('Pengguna Baru');
});

it('tidak menyimpan password atau token pengguna', function (): void {
    $this->actingAs($this->administrator);

    $this->administrator->update([
        'password' => 'password-baru',
        'remember_token' => 'token-rahasia',
    ]);

    expect(Activity::query()->whereMorphedTo('subject', $this->administrator)->exists())->toBeFalse();
});

it('hanya menampilkan halaman activity log kepada pengguna berizin', function (): void {
    $this->actingAs($this->administrator)
        ->get(route('system.activity-logs.index'))
        ->assertSuccessful()
        ->assertSee('Activity Log');

    $user = User::factory()->create();
    $this->actingAs($user)->get(route('system.activity-logs.index'))->assertForbidden();
});

it('tetap menampilkan log historis saat model subject sudah tidak tersedia', function (): void {
    $activity = Activity::query()->create([
        'log_name' => 'model',
        'description' => 'Menghapus ProductCategory',
        'subject_type' => 'App\Models\ProductCategory',
        'subject_id' => 123,
        'causer_type' => User::class,
        'causer_id' => $this->administrator->id,
        'event' => 'deleted',
        'properties' => [
            'old' => ['name' => 'Kategori Lama'],
        ],
    ]);

    $this->actingAs($this->administrator)
        ->get(route('system.activity-logs.index'))
        ->assertSuccessful()
        ->assertSee('ProductCategory')
        ->assertSee('Menghapus ProductCategory');

    $this->get(route('system.activity-logs.show', $activity))
        ->assertSuccessful()
        ->assertSee('ProductCategory #123')
        ->assertSee('Kategori Lama');
});
