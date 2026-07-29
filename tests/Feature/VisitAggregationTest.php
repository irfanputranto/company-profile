<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('mencatat visit halaman publik tanpa menyimpan alamat ip mentah', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
        ->withHeader('User-Agent', 'Company Profile Browser')
        ->get(route('home'))
        ->assertSuccessful();

    $visit = DB::table('page_visits')->first();

    expect($visit)
        ->not->toBeNull()
        ->and($visit->route_name)->toBe('home')
        ->and($visit->path)->toBe('/')
        ->and($visit->visitor_hash)->toHaveLength(64)
        ->and($visit->visitor_hash)->not->toContain('203.0.113.10');
});

it('meringkas visit tanpa menghitung tabel mentah saat dashboard dibuka', function () {
    DB::table('page_visits')->delete();

    DB::table('page_visits')->insert([
        visitRow('visitor-a', 'session-a', '2026-07-29 08:00:00'),
        visitRow('visitor-a', 'session-a', '2026-07-29 08:05:00'),
        visitRow('visitor-b', 'session-b', '2026-07-29 09:00:00'),
        visitRow('bot', 'bot-session', '2026-07-29 10:00:00', true),
    ]);

    $this->artisan('visits:aggregate', ['--date' => '2026-07-29'])
        ->expectsOutput('Ringkasan visit berhasil diperbarui.')
        ->assertSuccessful();

    $this->assertDatabaseHas('visit_aggregates', [
        'period_type' => 'day',
        'period_start' => '2026-07-29',
        'scope_type' => 'site',
        'scope_id' => 0,
        'page_views' => 3,
        'unique_visitors' => 2,
        'sessions' => 2,
    ]);
    $this->assertDatabaseHas('visit_aggregates', [
        'period_type' => 'week',
        'period_start' => '2026-07-27',
        'page_views' => 3,
    ]);
    $this->assertDatabaseHas('visit_aggregates', [
        'period_type' => 'month',
        'period_start' => '2026-07-01',
        'page_views' => 3,
    ]);
    $this->assertDatabaseHas('visit_aggregates', [
        'period_type' => 'year',
        'period_start' => '2026-01-01',
        'page_views' => 3,
    ]);
});

/**
 * @return array<string, bool|int|string|null>
 */
function visitRow(string $visitorHash, string $sessionHash, string $occurredAt, bool $isBot = false): array
{
    return [
        'scope_type' => 'site',
        'scope_id' => 0,
        'route_name' => 'home',
        'path' => '/',
        'visitor_hash' => hash('sha256', $visitorHash),
        'session_hash' => hash('sha256', $sessionHash),
        'referrer_host' => null,
        'device_type' => 'desktop',
        'country_code' => 'ID',
        'is_bot' => $isBot,
        'occurred_at' => $occurredAt,
    ];
}
