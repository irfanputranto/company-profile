<?php

namespace App\Console\Commands;

use App\Models\VisitAggregate;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AggregateVisits extends Command
{
    /** @var string */
    protected $signature = 'visits:aggregate {--date= : Tanggal harian dalam format Y-m-d}';

    /** @var string */
    protected $description = 'Memperbarui ringkasan visit harian, mingguan, bulanan, dan tahunan';

    public function handle(): int
    {
        try {
            $dates = $this->datesToAggregate();

            foreach ($dates as $date) {
                $this->aggregatePeriod('day', $date->startOfDay(), $date->endOfDay());
            }

            if (is_string($this->option('date')) && $this->option('date') !== '') {
                $this->aggregateParentPeriods($dates);
            }
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Ringkasan visit berhasil diperbarui.');

        return self::SUCCESS;
    }

    /** @return list<CarbonImmutable> */
    private function datesToAggregate(): array
    {
        $dateOption = $this->option('date');

        if (is_string($dateOption) && $dateOption !== '') {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $dateOption);

            if ($date === false || $date->format('Y-m-d') !== $dateOption) {
                throw new \InvalidArgumentException('Opsi --date wajib menggunakan format Y-m-d.');
            }

            return [$date];
        }

        $today = CarbonImmutable::today();

        return [$today->subDay(), $today];
    }

    private function aggregatePeriod(
        string $periodType,
        CarbonImmutable $periodStart,
        CarbonImmutable $periodEnd,
    ): void {
        $rows = DB::table('page_visits')
            ->select(['scope_type', 'scope_id'])
            ->selectRaw('COUNT(*) as page_views')
            ->selectRaw('COUNT(DISTINCT visitor_hash) as unique_visitors')
            ->selectRaw('COUNT(DISTINCT session_hash) as sessions')
            ->where('is_bot', false)
            ->where('occurred_at', '>=', $periodStart->startOfDay())
            ->where('occurred_at', '<', $periodEnd->addDay()->startOfDay())
            ->groupBy(['scope_type', 'scope_id'])
            ->get();

        foreach ($rows as $row) {
            $this->upsertAggregate(
                periodType: $periodType,
                periodStart: $periodStart,
                scopeType: $row->scope_type,
                scopeId: (int) $row->scope_id,
                pageViews: (int) $row->page_views,
                uniqueVisitors: (int) $row->unique_visitors,
                sessions: (int) $row->sessions,
            );
        }
    }

    /** @param list<CarbonImmutable> $dates */
    private function aggregateParentPeriods(array $dates): void
    {
        $periods = collect($dates)
            ->flatMap(fn (CarbonImmutable $date): array => [
                ['type' => 'week', 'start' => $date->startOfWeek(), 'end' => $date->endOfWeek()],
                ['type' => 'month', 'start' => $date->startOfMonth(), 'end' => $date->endOfMonth()],
                ['type' => 'year', 'start' => $date->startOfYear(), 'end' => $date->endOfYear()],
            ])
            ->unique(fn (array $period): string => "{$period['type']}:{$period['start']->format('Y-m-d')}");

        foreach ($periods as $period) {
            $this->aggregatePeriod(
                periodType: $period['type'],
                periodStart: $period['start'],
                periodEnd: $period['end'],
            );
        }
    }

    private function upsertAggregate(
        string $periodType,
        CarbonImmutable $periodStart,
        string $scopeType,
        int $scopeId,
        int $pageViews,
        int $uniqueVisitors,
        int $sessions,
    ): void {
        VisitAggregate::query()->updateOrCreate(
            [
                'period_type' => $periodType,
                'period_start' => $periodStart->format('Y-m-d'),
                'scope_type' => $scopeType,
                'scope_id' => $scopeId,
            ],
            [
                'page_views' => $pageViews,
                'unique_visitors' => $uniqueVisitors,
                'sessions' => $sessions,
            ],
        );
    }
}
