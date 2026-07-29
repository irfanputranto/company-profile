<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menerapkan tema dari config pada seluruh layout utama', function (): void {
    config(['theme.default' => 'luxury']);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('data-theme="luxury"', false);

    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('data-theme="luxury"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('data-theme="luxury"', false);
});

it('menampilkan seluruh pilihan tema FlyonUI pada header admin', function (): void {
    $response = $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSuccessful();

    foreach (config('theme.themes') as $theme => $label) {
        $response
            ->assertSee('data-theme-value="'.$theme.'"', false)
            ->assertSee($label);
    }

    expect(config('theme.default'))->toBeIn(array_keys(config('theme.themes')));
});
