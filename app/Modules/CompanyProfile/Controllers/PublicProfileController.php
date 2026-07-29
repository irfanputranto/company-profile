<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Service;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function __invoke(): View
    {
        $profile = Profile::query()
            ->with('contentTranslations.language')
            ->where('is_active', true)
            ->first();
        $services = $profile
            ? Service::query()
                ->with('contentTranslations.language')
                ->where('profile_id', $profile->getKey())
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
            : collect();

        return view('welcome', compact('profile', 'services'));
    }
}
