<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\View\View;

class PublicProjectController extends Controller
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
                'socialLinks' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order'),
                'projects' => fn ($query) => $query
                    ->with([
                        'contentTranslations.language',
                        'skills.contentTranslations.language',
                    ])
                    ->where('is_active', true)
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order'),
            ])
            ->where('is_active', true)
            ->first();

        return view('public.projects', [
            'profile' => $profile,
            'services' => $profile?->services ?? collect(),
            'socialLinks' => $profile?->socialLinks ?? collect(),
            'projects' => $profile?->projects ?? collect(),
        ]);
    }
}
