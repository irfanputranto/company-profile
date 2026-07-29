<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(
        Request $request,
        Language $language,
        LanguageResolver $resolver,
    ): RedirectResponse {
        abort_unless($language->is_active, 404);

        $request->session()->put('site_locale', $language->code);
        $resolver->forget();

        return redirect()->back();
    }
}
