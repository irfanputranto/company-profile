<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagedProject;
use App\Models\ProjectFeature;
use App\Models\ProjectPhase;
use App\Modules\ProjectManagement\Requests\ProjectFeatureMoveRequest;
use App\Modules\ProjectManagement\Requests\ProjectFeatureRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectFeatureController extends Controller
{
    public function store(ProjectFeatureRequest $request, ManagedProject $managedProject, ProjectPhase $phase): RedirectResponse
    {
        $phase->features()->create($request->validated());

        return back()->with('success_message', __('project-management.features.created'));
    }

    public function update(ProjectFeatureRequest $request, ManagedProject $managedProject, ProjectPhase $phase, ProjectFeature $feature): RedirectResponse
    {
        $feature->update($request->validated());

        return back()->with('success_message', __('project-management.features.updated'));
    }

    public function move(ProjectFeatureMoveRequest $request, ManagedProject $managedProject, ProjectPhase $phase, ProjectFeature $feature): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($feature, $phase, $validated): void {
            $feature->update($validated);
            $this->synchronizePhaseProgress($phase);
        });

        return response()->json([
            'message' => __('project-management.board.moved'),
            'feature' => [
                'id' => $feature->id,
                'status' => $feature->status,
                'sort_order' => $feature->sort_order,
            ],
            'phase' => [
                'id' => $phase->id,
                'status' => $phase->status,
                'progress' => $phase->progress,
            ],
        ]);
    }

    public function destroy(ManagedProject $managedProject, ProjectPhase $phase, ProjectFeature $feature): RedirectResponse
    {
        Gate::authorize('update_managed_projects');
        $feature->delete();

        return back()->with('success_message', __('project-management.features.deleted'));
    }

    private function synchronizePhaseProgress(ProjectPhase $phase): void
    {
        /** @var Collection<int, string> $statuses */
        $statuses = $phase->features()->pluck('status');
        $featureCount = $statuses->count();
        $progress = $featureCount === 0
            ? 0
            : (int) round(($statuses->filter(fn (string $status): bool => $status === 'done')->count() / $featureCount) * 100);

        $status = match (true) {
            $featureCount === 0 => 'pending',
            $progress === 100 => 'completed',
            $statuses->contains('blocked') => 'blocked',
            $statuses->contains('in_progress') => 'in_progress',
            $statuses->contains('review') => 'review',
            default => 'pending',
        };

        $phase->update([
            'progress' => $progress,
            'status' => $status,
            'completed_at' => $status === 'completed' ? today() : null,
        ]);
    }
}
