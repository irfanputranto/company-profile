<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagedProject;
use App\Models\ProjectTechnology;
use App\Modules\ProjectManagement\Requests\ProjectTechnologyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ProjectTechnologyController extends Controller
{
    public function store(ProjectTechnologyRequest $request, ManagedProject $managedProject): RedirectResponse
    {
        $managedProject->technologies()->create($request->validated());

        return back()->with('success_message', __('project-management.technologies.created'));
    }

    public function destroy(ManagedProject $managedProject, ProjectTechnology $technology): RedirectResponse
    {
        Gate::authorize('update_managed_projects');
        $technology->delete();

        return back()->with('success_message', __('project-management.technologies.deleted'));
    }
}
