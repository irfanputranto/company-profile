<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientCompany;
use App\Models\ManagedProject;
use App\Modules\ProjectManagement\Requests\ManagedProjectRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ManagedProjectController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_managed_projects');

        $projects = ManagedProject::query()
            ->with('clientCompany:id,name')
            ->withCount(['documents', 'phases'])
            ->when($request->filled('q'), fn ($query) => $query->where(function ($search) use ($request): void {
                $keyword = '%'.$request->string('q')->trim()->toString().'%';
                $search->where('name', 'like', $keyword)->orWhere('code', 'like', $keyword);
            }))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('company'), fn ($query) => $query->where('client_company_id', $request->integer('company')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('adminpanel.pages.project-management.projects.index', [
            'projects' => $projects,
            'companies' => ClientCompany::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create_managed_projects');

        return view('adminpanel.pages.project-management.projects.create', ['companies' => $this->companies()]);
    }

    public function store(ManagedProjectRequest $request): RedirectResponse
    {
        $project = ManagedProject::query()->create($request->validated());

        return to_route('project-management.projects.show', $project)->with('success_message', __('project-management.projects.created'));
    }

    public function show(ManagedProject $managedProject): View
    {
        Gate::authorize('show_managed_projects');

        $managedProject->load([
            'clientCompany:id,name,contact_person,email,phone',
            'documents' => fn ($query) => $query->latest(),
            'phases.features',
            'technologies',
            'servers',
        ]);

        return view('adminpanel.pages.project-management.projects.show', ['project' => $managedProject]);
    }

    public function board(ManagedProject $managedProject): View
    {
        Gate::authorize('show_managed_projects');

        $managedProject->load([
            'clientCompany:id,name',
            'phases.features',
        ]);

        return view('adminpanel.pages.project-management.projects.board', ['project' => $managedProject]);
    }

    public function edit(ManagedProject $managedProject): View
    {
        Gate::authorize('update_managed_projects');

        return view('adminpanel.pages.project-management.projects.edit', [
            'project' => $managedProject,
            'companies' => $this->companies(),
        ]);
    }

    public function update(ManagedProjectRequest $request, ManagedProject $managedProject): RedirectResponse
    {
        $managedProject->update($request->validated());

        return to_route('project-management.projects.show', $managedProject)->with('success_message', __('project-management.projects.updated'));
    }

    public function destroy(ManagedProject $managedProject): RedirectResponse
    {
        Gate::authorize('delete_managed_projects');
        $managedProject->delete();

        return to_route('project-management.projects.index')->with('success_message', __('project-management.projects.deleted'));
    }

    /** @return Collection<int, ClientCompany> */
    private function companies(): Collection
    {
        return ClientCompany::query()->orderBy('name')->get(['id', 'name']);
    }
}
