<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = [
        'user_id', 'logo_media_id', 'favicon_media_id', 'slug', 'public_name', 'headline', 'short_bio', 'about', 'email', 'phone',
        'location', 'timezone', 'availability_status', 'years_experience', 'resume_path', 'is_active',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function whatsappUrl(string $message): ?string
    {
        $phone = str($this->phone)->replaceMatches('/\D+/', '')->toString();

        if ($phone === '') {
            return null;
        }

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    /** @return BelongsTo<Media, $this> */
    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    /** @return BelongsTo<Media, $this> */
    public function faviconMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'favicon_media_id');
    }

    /** @return HasMany<Service, $this> */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /** @return HasMany<Feature, $this> */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    /** @return HasMany<PricingPlan, $this> */
    public function pricingPlans(): HasMany
    {
        return $this->hasMany(PricingPlan::class);
    }

    /** @return HasMany<Skill, $this> */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    /** @return HasMany<Project, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /** @return HasMany<Experience, $this> */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /** @return HasMany<SocialLink, $this> */
    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class);
    }

    /** @return HasMany<Testimonial, $this> */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /** @return HasMany<Faq, $this> */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }
}
