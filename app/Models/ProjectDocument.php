<?php

namespace App\Models;

use Database\Factories\ProjectDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends AuditableModel
{
    /** @use HasFactory<ProjectDocumentFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'managed_project_id', 'uploaded_by', 'uuid', 'category', 'title', 'disk', 'path',
        'original_name', 'mime_type', 'byte_size', 'notes',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['byte_size' => 'integer'];
    }

    public function managedProject(): BelongsTo
    {
        return $this->belongsTo(ManagedProject::class);
    }
}
