<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Database\Factories\PricingPlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PricingPlan extends AuditableModel
{
    /** @use HasFactory<PricingPlanFactory> */
    use HasContentTranslations, HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'slug', 'title', 'tagline', 'description', 'price', 'currency',
        'billing_period', 'call_to_action_label', 'call_to_action_url', 'sort_order',
        'is_featured', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Profile, $this> */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /** @return BelongsToMany<Feature, $this> */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class)->orderBy('features.sort_order');
    }
}
