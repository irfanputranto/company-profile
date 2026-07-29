<?php

namespace App\Modules\System\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'event' => ['nullable', 'in:created,updated,deleted,restored'],
            'causer_id' => ['nullable', 'integer'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'in:10,25,50,100'],
        ]);

        $activities = Activity::query()
            ->with('causer')
            ->when($filters['q'] ?? null, function (Builder $query, string $keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('description', 'like', "%{$keyword}%")
                        ->orWhere('subject_type', 'like', "%{$keyword}%")
                        ->orWhereHasMorph('causer', [User::class], function (Builder $query) use ($keyword): void {
                            $query->where('name', 'like', "%{$keyword}%")
                                ->orWhere('username', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filters['event'] ?? null, fn (Builder $query, string $event): Builder => $query->where('event', $event))
            ->when($filters['causer_id'] ?? null, fn (Builder $query, int $causerId): Builder => $query->where('causer_id', $causerId)->where('causer_type', User::class))
            ->when($filters['subject_type'] ?? null, fn (Builder $query, string $subjectType): Builder => $query->where('subject_type', $subjectType))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate($filters['limit'] ?? 25)
            ->withQueryString();

        $causers = User::query()
            ->whereIn('id', Activity::query()->select('causer_id')->where('causer_type', User::class))
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        $subjectTypes = Activity::query()
            ->whereNotNull('subject_type')
            ->distinct()
            ->orderBy('subject_type')
            ->pluck('subject_type');

        return view('adminpanel.pages.system.activity-logs.index', compact('activities', 'causers', 'subjectTypes'));
    }

    public function show(Activity $activity): View
    {
        $activity->load('causer');

        $newValues = $activity->properties->get('attributes', []);
        $oldValues = $activity->properties->get('old', []);
        $changedAttributes = collect(array_keys($oldValues))
            ->merge(array_keys($newValues))
            ->unique()
            ->map(fn (string $attribute): array => [
                'attribute' => $attribute,
                'old' => $oldValues[$attribute] ?? null,
                'new' => $newValues[$attribute] ?? null,
            ]);

        return view('adminpanel.pages.system.activity-logs.show', compact('activity', 'changedAttributes'));
    }
}
