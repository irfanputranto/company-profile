<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VisitAggregate;
use Illuminate\Http\Request;
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
            'period' => $period,
            'totals' => $totals,
            'list' => $query->latest('period_start')->paginate(30)->withQueryString(),
        ]);
    }
}
