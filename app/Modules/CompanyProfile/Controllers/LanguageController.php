<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Models\Language;
use App\Modules\CompanyProfile\Requests\StoreLanguageRequest;
use App\Modules\CompanyProfile\Requests\UpdateLanguageRequest;
use App\Modules\CompanyProfile\Services\LanguageManager;
use App\Services\BaseCrud\BaseWebCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LanguageController extends BaseWebCrud
{
    public function __construct(private LanguageManager $manager) {}

    public function index(Request $request)
    {
        Gate::authorize('view_languages');

        return view('adminpanel.pages.company-profile.languages.list', [
            'languages' => Language::query()->orderByDesc('is_default')->orderBy('sort_order')->paginate(15),
        ]);
    }

    public function create()
    {
        Gate::authorize('create_languages');

        return view('adminpanel.pages.company-profile.languages.create');
    }

    public function store(Request $request)
    {
        $request = app(StoreLanguageRequest::class);
        $this->manager->save(new Language, $request->validated());

        return redirect()->route('company-profile.languages.index')
            ->with('success_message', __('company-profile.languages.created'));
    }

    public function edit($id)
    {
        Gate::authorize('update_languages');
        $language = $id instanceof Language ? $id : Language::query()->findOrFail($id);

        return view('adminpanel.pages.company-profile.languages.edit', compact('language'));
    }

    public function update(Request $request, $id)
    {
        $request = app(UpdateLanguageRequest::class);
        $language = $id instanceof Language ? $id : Language::query()->findOrFail($id);
        $this->manager->save($language, $request->validated());

        return redirect()->route('company-profile.languages.index')
            ->with('success_message', __('company-profile.languages.updated'));
    }

    public function destroy(Request $request, $id)
    {
        Gate::authorize('delete_languages');
        $language = $id instanceof Language ? $id : Language::query()->findOrFail($id);
        $this->manager->delete($language);

        return redirect()->route('company-profile.languages.index')
            ->with('success_message', __('company-profile.languages.deleted'));
    }
}
