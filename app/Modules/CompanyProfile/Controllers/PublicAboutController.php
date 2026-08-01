<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\View\View;

class PublicAboutController extends Controller
{
    public function __invoke(): View
    {
        $profile = Profile::query()
            ->with([
                'contentTranslations.language',
                'logoMedia',
                'faviconMedia',
                'services' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'experiences' => fn ($query) => $query
                    ->with('contentTranslations.language')
                    ->where('is_active', true)
                    ->latest('started_at'),
                'socialLinks' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
            ])
            ->where('is_active', true)
            ->first();

        return view('public.about', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'experiences' => $profile?->experiences ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
        ]);
    }
}
