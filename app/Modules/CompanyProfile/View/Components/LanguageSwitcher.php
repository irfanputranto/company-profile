<?php

namespace App\Modules\CompanyProfile\View\Components;

use App\Modules\CompanyProfile\Services\LanguageResolver;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LanguageSwitcher extends Component
{
    public function __construct(
        private LanguageResolver $resolver,
        public bool $dark = false,
        public bool $withFlags = false,
    ) {}

    public function render(): View
    {
        return view('components.language-switcher', [
            'languages' => $this->resolver->activeLanguages(),
        ]);
    }
}
