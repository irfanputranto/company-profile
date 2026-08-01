<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagedProject;
use App\Models\ProjectServer;
use App\Modules\ProjectManagement\Requests\ProjectServerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectServerController extends Controller
{
    public function store(ProjectServerRequest $request, ManagedProject $managedProject): RedirectResponse
    {
        $managedProject->servers()->create($request->validated());

        return back()->with('success_message', __('project-management.servers.created'));
    }

    public function update(ProjectServerRequest $request, ManagedProject $managedProject, ProjectServer $server): RedirectResponse
    {
        $data = $request->validated();
        if ($server->expires_at?->toDateString() !== ($data['expires_at'] ?? null)
            || $server->reminder_days !== (int) $data['reminder_days']) {
            $data['last_notified_at'] = null;
        }
        foreach (['username', 'password', 'api_secret', 'credentials_note'] as $secret) {
            if (blank($data[$secret] ?? null)) {
                Arr::forget($data, $secret);
            }
        }
        $server->update($data);

        return back()->with('success_message', __('project-management.servers.updated'));
    }

    public function credentials(ManagedProject $managedProject, ProjectServer $server): View
    {
        Gate::authorize('show_project_credentials');

        activity('security')
            ->causedBy(auth()->user())
            ->performedOn($server)
            ->withProperties(['project_id' => $managedProject->id, 'server_id' => $server->id])
            ->log('Melihat kredensial server proyek');

        return view('adminpanel.pages.project-management.projects.credentials', compact('managedProject', 'server'));
    }

    public function destroy(ManagedProject $managedProject, ProjectServer $server): RedirectResponse
    {
        Gate::authorize('update_managed_projects');
        $server->delete();

        return back()->with('success_message', __('project-management.servers.deleted'));
    }
}
