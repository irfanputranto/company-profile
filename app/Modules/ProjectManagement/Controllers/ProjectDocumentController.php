<?php

namespace App\Modules\ProjectManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagedProject;
use App\Models\ProjectDocument;
use App\Modules\ProjectManagement\Requests\ProjectDocumentRequest;
use App\Modules\ProjectManagement\Services\ProjectDocumentStorage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectDocumentController extends Controller
{
    public function __construct(private readonly ProjectDocumentStorage $storage) {}

    public function store(ProjectDocumentRequest $request, ManagedProject $managedProject): RedirectResponse
    {
        $this->storage->storeMany(
            $managedProject,
            $request->file('documents', []),
            $request->string('category')->toString(),
            $request->string('notes')->toString() ?: null,
        );

        return back()->with('success_message', __('project-management.documents.created'));
    }

    public function download(ManagedProject $managedProject, ProjectDocument $document): StreamedResponse
    {
        Gate::authorize('show_managed_projects');
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($document->disk);
        abort_unless($disk->exists($document->path), 404);

        return $disk->download($document->path, $document->original_name, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(ManagedProject $managedProject, ProjectDocument $document): RedirectResponse
    {
        Gate::authorize('update_managed_projects');
        $this->storage->delete($document);

        return back()->with('success_message', __('project-management.documents.deleted'));
    }
}
