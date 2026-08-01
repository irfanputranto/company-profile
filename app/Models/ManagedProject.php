<?php

namespace App\Models;

use Database\Factories\ManagedProjectFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagedProject extends AuditableModel
{
    /** @use HasFactory<ManagedProjectFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'client_company_id', 'code', 'name', 'description', 'status', 'started_at', 'due_at',
        'completed_at', 'contract_value', 'estimated_cost', 'currency',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'due_at' => 'date',
            'completed_at' => 'date',
            'contract_value' => 'decimal:2',
            'estimated_cost' => 'decimal:2',
        ];
    }

    /** @return Attribute<float, never> */
    protected function profitAmount(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->contract_value - (float) $this->estimated_cost);
    }

    /** @return Attribute<float, never> */
    protected function profitPercentage(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->estimated_cost > 0
            ? ($this->profit_amount / (float) $this->estimated_cost) * 100
            : 0);
    }

    public function clientCompany(): BelongsTo
    {
        return $this->belongsTo(ClientCompany::class);
    }

    /** @return HasMany<ProjectDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    /** @return HasMany<ProjectPhase, $this> */
    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('sort_order');
    }

    /** @return HasMany<ProjectTechnology, $this> */
    public function technologies(): HasMany
    {
        return $this->hasMany(ProjectTechnology::class)->orderBy('name');
    }

    /** @return HasMany<ProjectServer, $this> */
    public function servers(): HasMany
    {
        return $this->hasMany(ProjectServer::class)->orderBy('name');
    }
}
