<?php

namespace App\Modules\ProjectManagement\Services;

use App\Models\ManagedProject;
use App\Models\ProjectDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ProjectDocumentStorage
{
    /** @param list<UploadedFile> $files */
    public function storeMany(ManagedProject $project, array $files, string $category, ?string $notes): void
    {
        $storedDocuments = collect();

        try {
            DB::transaction(function () use ($project, $files, $category, $notes, $storedDocuments): void {
                foreach ($files as $file) {
                    $storedDocuments->push($this->store($project, $file, $category, $notes));
                }
            });
        } catch (Throwable $exception) {
            $storedDocuments->each(fn (ProjectDocument $document) => Storage::disk($document->disk)->delete($document->path));

            throw $exception;
        }
    }

    public function store(ManagedProject $project, UploadedFile $file, string $category, ?string $notes): ProjectDocument
    {
        $uuid = (string) Str::uuid();
        $extension = Str::lower($file->extension() ?: 'bin');
        $path = $file->storeAs("project-documents/{$project->id}", "{$uuid}.{$extension}", config('filesystems.private_media_disk'));

        if (! $path) {
            throw new RuntimeException('Project document could not be stored.');
        }

        return $project->documents()->create([
            'uploaded_by' => auth()->id(),
            'uuid' => $uuid,
            'category' => $category,
            'title' => Str::limit(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 255, ''),
            'disk' => config('filesystems.private_media_disk'),
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'byte_size' => (int) $file->getSize(),
            'notes' => $notes,
        ]);
    }

    public function delete(ProjectDocument $document): void
    {
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();
    }
}
