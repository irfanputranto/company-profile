<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagedProject;
use App\Models\ProjectPhase;
use App\Modules\ProjectManagement\Requests\ProjectPhaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProjectPhaseController extends Controller
{
    public function store(ProjectPhaseRequest $request, ManagedProject $managedProject): RedirectResponse
    {
        $managedProject->phases()->create($request->validated());

        return back()->with('success_message', __('project-management.phases.created'));
    }

    public function update(ProjectPhaseRequest $request, ManagedProject $managedProject, ProjectPhase $phase): RedirectResponse
    {
        $phase->update($request->validated());

        return back()->with('success_message', __('project-management.phases.updated'));
    }

    public function destroy(ManagedProject $managedProject, ProjectPhase $phase): RedirectResponse
    {
        Gate::authorize('update_managed_projects');
        $phase->delete();

        return back()->with('success_message', __('project-management.phases.deleted'));
    }
}
