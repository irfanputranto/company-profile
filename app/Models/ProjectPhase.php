<?php

namespace App\Models;

use Database\Factories\ProjectPhaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPhase extends AuditableModel
{
    /** @use HasFactory<ProjectPhaseFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'managed_project_id', 'name', 'description', 'deliverables', 'status', 'progress',
        'started_at', 'due_at', 'completed_at', 'sort_order',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'started_at' => 'date',
            'due_at' => 'date',
            'completed_at' => 'date',
            'sort_order' => 'integer',
        ];
    }

    public function managedProject(): BelongsTo
    {
        return $this->belongsTo(ManagedProject::class);
    }

    /** @return HasMany<ProjectFeature, $this> */
    public function features(): HasMany
    {
        return $this->hasMany(ProjectFeature::class)->orderBy('sort_order');
    }
}
