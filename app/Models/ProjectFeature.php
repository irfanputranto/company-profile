<?php

namespace App\Models;

use Database\Factories\ProjectFeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFeature extends AuditableModel
{
    /** @use HasFactory<ProjectFeatureFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['project_phase_id', 'name', 'description', 'acceptance_criteria', 'status', 'sort_order'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function projectPhase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class);
    }
}
