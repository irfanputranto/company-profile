<?php

namespace App\Modules\CompanyProfile\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitRecorder
{
    /** @return array<string, bool|int|string|null> */
    public function context(Request $request): array
    {
        $userAgent = (string) $request->userAgent();
        $applicationKey = (string) config('app.key');
        $sessionId = $request->hasSession() ? $request->session()->getId() : '';
        $referrerHost = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
        $countryCode = Str::upper((string) $request->headers->get('cf-ipcountry'));

        return [
            'route_name' => $request->route()?->getName(),
            'path' => '/'.ltrim($request->path(), '/'),
            'visitor_hash' => hash_hmac('sha256', "{$request->ip()}|{$userAgent}", $applicationKey),
            'session_hash' => $sessionId !== ''
                ? hash_hmac('sha256', $sessionId, $applicationKey)
                : null,
            'referrer_host' => is_string($referrerHost) ? Str::limit($referrerHost, 255, '') : null,
            'device_type' => $this->deviceType($userAgent),
            'country_code' => preg_match('/^[A-Z]{2}$/', $countryCode) === 1 ? $countryCode : null,
            'is_bot' => preg_match('/bot|crawl|spider|slurp|preview|headless/i', $userAgent) === 1,
            'occurred_at' => now(),
        ];
    }

    /** @param array<string, bool|int|string|null> $context */
    public function record(array $context, string $scopeType = 'site', int $scopeId = 0): void
    {
        $occurredAt = CarbonImmutable::parse((string) $context['occurred_at']);

        DB::transaction(function () use ($context, $occurredAt, $scopeId, $scopeType): void {
            DB::table('page_visits')->insert([
                ...$context,
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
            ]);

            if ($context['is_bot'] === true) {
                return;
            }

            foreach ($this->periods($occurredAt) as $periodType => $periodStart) {
                $this->incrementAggregate(
                    periodType: $periodType,
                    periodStart: $periodStart,
                    scopeType: $scopeType,
                    scopeId: $scopeId,
                    visitorHash: is_string($context['visitor_hash']) ? $context['visitor_hash'] : null,
                    sessionHash: is_string($context['session_hash']) ? $context['session_hash'] : null,
                );
            }
        });
    }

    /**
     * @return array{day: string, week: string, month: string, year: string}
     */
    private function periods(CarbonImmutable $occurredAt): array
    {
        return [
            'day' => $occurredAt->startOfDay()->toDateString(),
            'week' => $occurredAt->startOfWeek()->toDateString(),
            'month' => $occurredAt->startOfMonth()->toDateString(),
            'year' => $occurredAt->startOfYear()->toDateString(),
        ];
    }

    private function incrementAggregate(
        string $periodType,
        string $periodStart,
        string $scopeType,
        int $scopeId,
        ?string $visitorHash,
        ?string $sessionHash,
    ): void {
        $key = [
            'period_type' => $periodType,
            'period_start' => $periodStart,
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
        ];
        $now = now();

        DB::table('visit_aggregates')->insertOrIgnore([
            ...$key,
            'page_views' => 0,
            'unique_visitors' => 0,
            'sessions' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('visit_aggregates')->where($key)->increment('page_views', 1, ['updated_at' => $now]);

        $this->incrementUniqueMetric($key, 'visitor', $visitorHash, 'unique_visitors', $now);
        $this->incrementUniqueMetric($key, 'session', $sessionHash, 'sessions', $now);
    }

    /**
     * @param  array{period_type: string, period_start: string, scope_type: string, scope_id: int}  $aggregateKey
     */
    private function incrementUniqueMetric(
        array $aggregateKey,
        string $identityType,
        ?string $identityHash,
        string $column,
        \DateTimeInterface $now,
    ): void {
        if ($identityHash === null) {
            return;
        }

        $inserted = DB::table('visit_aggregate_identities')->insertOrIgnore([
            ...$aggregateKey,
            'identity_type' => $identityType,
            'identity_hash' => $identityHash,
            'created_at' => $now,
        ]);

        if ($inserted === 1) {
            DB::table('visit_aggregates')->where($aggregateKey)->increment($column, 1, ['updated_at' => $now]);
        }
    }

    private function deviceType(string $userAgent): string
    {
        return match (true) {
            preg_match('/tablet|ipad/i', $userAgent) === 1 => 'tablet',
            preg_match('/mobile|android|iphone/i', $userAgent) === 1 => 'mobile',
            default => 'desktop',
        };
    }
}
