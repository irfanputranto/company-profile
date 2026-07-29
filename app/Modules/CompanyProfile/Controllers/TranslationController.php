<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Modules\CompanyProfile\Requests\UpdateTranslationsRequest;
use App\Modules\CompanyProfile\Services\TranslationManager;
use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use App\Modules\CompanyProfile\Support\TranslatableContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TranslationController extends Controller
{
    public function __construct(private TranslationManager $manager) {}

    public function edit(string $resource, int $record): View
    {
        $model = $this->resolveRecord($resource, $record);
        Gate::authorize('update_'.str($resource)->replace('-', '_'));

        return view('adminpanel.pages.company-profile.translations.edit', [
            'languages' => Language::query()->where('is_active', true)->orderByDesc('is_default')->orderBy('sort_order')->get(),
            'model' => $model->load('contentTranslations.language'),
            'resource' => $resource,
            'definition' => ContentResourceRegistry::get($resource),
            'fields' => $this->translationFields($resource),
        ]);
    }

    public function update(
        UpdateTranslationsRequest $request,
        string $resource,
        int $record,
    ): RedirectResponse {
        $model = $this->resolveRecord($resource, $record);
        $this->manager->save($model, $request->validated('translations'));

        return redirect()->route('company-profile.translations.edit', compact('resource', 'record'))
            ->with('success_message', __('company-profile.translations.updated'));
    }

    private function resolveRecord(string $resource, int $record): Model
    {
        abort_unless(TranslatableContentRegistry::supports($resource), 404);
        $model = ContentResourceRegistry::get($resource)['model'];

        return $model::query()->findOrFail($record);
    }

    /** @return list<array<string, mixed>> */
    private function translationFields(string $resource): array
    {
        return collect(ContentResourceRegistry::get($resource)['fields'])
            ->whereIn('name', TranslatableContentRegistry::fields($resource))
            ->values()
            ->all();
    }
}
