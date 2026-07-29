<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\View\View;

class PublicPricingController extends Controller
{
    public function __invoke(): View
    {
        $profile = Profile::query()
            ->with([
                'contentTranslations.language',
                'services' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'socialLinks' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'pricingPlans' => fn ($query) => $query
                    ->with([
                        'contentTranslations.language',
                        'features' => fn ($featureQuery) => $featureQuery
                            ->with('contentTranslations.language')
                            ->where('features.is_active', true),
                    ])
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->where('is_active', true)
            ->first();

        return view('public.pricing', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'pricingPlans' => $profile?->pricingPlans ?? collect(),
        ]);
    }
}
