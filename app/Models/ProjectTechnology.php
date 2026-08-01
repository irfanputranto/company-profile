<?php

namespace App\Models;

use Database\Factories\ProjectTechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTechnology extends AuditableModel
{
    /** @use HasFactory<ProjectTechnologyFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['managed_project_id', 'name', 'category', 'version', 'notes'];

    public function managedProject(): BelongsTo
    {
        return $this->belongsTo(ManagedProject::class);
    }
}
