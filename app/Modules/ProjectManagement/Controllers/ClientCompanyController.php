<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientCompany;
use App\Modules\ProjectManagement\Requests\ClientCompanyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientCompanyController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view_client_companies');

        $companies = ClientCompany::query()
            ->withCount('managedProjects')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($search) use ($request): void {
                $keyword = '%'.$request->string('q')->trim()->toString().'%';
                $search->where('name', 'like', $keyword)->orWhere('contact_person', 'like', $keyword);
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('adminpanel.pages.project-management.companies.index', compact('companies'));
    }

    public function create(): View
    {
        Gate::authorize('create_client_companies');

        return view('adminpanel.pages.project-management.companies.create');
    }

    public function store(ClientCompanyRequest $request): RedirectResponse
    {
        ClientCompany::query()->create($request->validated());

        return to_route('project-management.companies.index')->with('success_message', __('project-management.companies.created'));
    }

    public function edit(ClientCompany $company): View
    {
        Gate::authorize('update_client_companies');

        return view('adminpanel.pages.project-management.companies.edit', compact('company'));
    }

    public function update(ClientCompanyRequest $request, ClientCompany $company): RedirectResponse
    {
        $company->update($request->validated());

        return to_route('project-management.companies.index')->with('success_message', __('project-management.companies.updated'));
    }

    public function destroy(ClientCompany $company): RedirectResponse
    {
        Gate::authorize('delete_client_companies');

        if ($company->managedProjects()->exists()) {
            return back()->with('error_message', __('project-management.companies.has_projects'));
        }

        $company->delete();

        return to_route('project-management.companies.index')->with('success_message', __('project-management.companies.deleted'));
    }
}
