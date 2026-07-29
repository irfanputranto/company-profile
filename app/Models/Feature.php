<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends AuditableModel
{
    /** @use HasFactory<FeatureFactory> */
    use HasContentTranslations, HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'profile_id', 'slug', 'title', 'description', 'icon', 'sort_order',
        'is_featured', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
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

    /** @return BelongsToMany<PricingPlan, $this> */
    public function pricingPlans(): BelongsToMany
    {
        return $this->belongsToMany(PricingPlan::class);
    }
}
