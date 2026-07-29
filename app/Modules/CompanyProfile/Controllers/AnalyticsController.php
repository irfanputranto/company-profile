<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VisitAggregate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_analytics');

        $filters = $request->validate([
            'period' => ['nullable', Rule::in(['day', 'week', 'month', 'year'])],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $period = $filters['period'] ?? 'day';
        $query = VisitAggregate::query()
            ->where('scope_type', 'site')
            ->where('scope_id', 0)
            ->where('period_type', $period)
            ->when($filters['from'] ?? null, fn ($builder, string $from) => $builder->whereDate('period_start', '>=', $from))
            ->when($filters['to'] ?? null, fn ($builder, string $to) => $builder->whereDate('period_start', '<=', $to));

        $totals = (clone $query)
            ->selectRaw('COALESCE(SUM(page_views), 0) as page_views')
            ->selectRaw('COALESCE(SUM(unique_visitors), 0) as unique_visitors')
            ->selectRaw('COALESCE(SUM(sessions), 0) as sessions')
            ->first();

        return view('adminpanel.pages.company-profile.analytics.index', [
            'menuStats' => $this->eventStats('menu', $period, $filters),
            'period' => $period,
            'sectionStats' => $this->eventStats('section', $period, $filters),
            'totals' => $totals,
            'list' => $query->latest('period_start')->paginate(30)->withQueryString(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array{key: string, label: string, page_views: int, unique_visitors: int}>
     */
    private function eventStats(string $scopeType, string $period, array $filters): Collection
    {
        $configuredEvents = config("analytics.events.{$scopeType}", []);
        $eventNames = array_flip(is_array($configuredEvents) ? $configuredEvents : []);

        return VisitAggregate::query()
            ->where('scope_type', $scopeType)
            ->where('period_type', $period)
            ->when($filters['from'] ?? null, fn ($builder, string $from) => $builder->whereDate('period_start', '>=', $from))
            ->when($filters['to'] ?? null, fn ($builder, string $to) => $builder->whereDate('period_start', '<=', $to))
            ->select('scope_id')
            ->selectRaw('SUM(page_views) as page_views')
            ->selectRaw('SUM(unique_visitors) as unique_visitors')
            ->groupBy('scope_id')
            ->orderByDesc('page_views')
            ->limit(10)
            ->get()
            ->map(function (VisitAggregate $aggregate) use ($eventNames, $scopeType): array {
                $key = (string) ($eventNames[$aggregate->scope_id] ?? $aggregate->scope_id);

                return [
                    'key' => $key,
                    'label' => __("admin.analytics.events.{$scopeType}.{$key}"),
                    'page_views' => $aggregate->page_views,
                    'unique_visitors' => $aggregate->unique_visitors,
                ];
            });
    }
}
