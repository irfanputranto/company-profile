<?php

namespace App\Modules\CompanyProfile\Controllers;

use App\Modules\CompanyProfile\Requests\ContentCrudRequest;
use App\Modules\CompanyProfile\Support\ContentResourceRegistry;
use App\Services\BaseCrud\BaseWebCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends BaseWebCrud
{
    private string $resourceKey;

    /** @var array<string, mixed> */
    private array $definition;

    /** @var array<string, list<int>> */
    private array $relationValues = [];

    public function __construct(Request $request)
    {
        $this->resourceKey = (string) $request->route('resource');
        $this->definition = ContentResourceRegistry::get($this->resourceKey);
        $permissionResource = str($this->resourceKey)->replace('-', '_')->toString();

        $this->model = $this->definition['model'];
        $this->searchAble = $this->definition['searchable'];
        $this->storeValidator = ContentCrudRequest::class;
        $this->updateValidator = ContentCrudRequest::class;
        $this->viewPath = 'adminpanel.pages.company-profile.content';
        $this->redirectSuccessStore = 'company-profile.content.index';
        $this->redirectSuccessUpdate = 'company-profile.content.index';
        $this->successStoreMsg = __('admin.crud.created', ['resource' => $this->definition['singular']]);
        $this->successUpdateMsg = __('admin.crud.updated', ['resource' => $this->definition['singular']]);
        $this->successDestroyMsg = __('admin.crud.deleted', ['resource' => $this->definition['singular']]);
        $this->defaultOrder = $this->definition['order'];
        $this->defaultSort = 'desc';
        $this->relationShow = collect($this->definition['fields'])
            ->pluck('relation')
            ->filter()
            ->values()
            ->all();
        $this->abilityPolicyIndex = "view_{$permissionResource}";
        $this->abilityPolicyShow = "show_{$permissionResource}";
        $this->abilityPolicyStore = "create_{$permissionResource}";
        $this->abilityPolicyUpdate = "update_{$permissionResource}";
        $this->abilityPolicyDelete = "delete_{$permissionResource}";
        $this->enableBulkDelete = false;
        $this->lockRelationParam = true;
    }

    public function editRecord(string $resource, int $record): mixed
    {
        return parent::edit($record);
    }

    public function updateRecord(Request $request, string $resource, int $record): mixed
    {
        return parent::update($request, $record);
    }

    public function destroyRecord(Request $request, string $resource, int $record): mixed
    {
        return parent::destroy($request, $record);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function __prepareDataStore($data): array
    {
        return $this->prepareData($data);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function __prepareDataUpdate($data): array
    {
        return $this->prepareData($data);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function __extraData($data): array
    {
        $data['resourceKey'] = $this->resourceKey;
        $data['resourceDefinition'] = ContentResourceRegistry::formData($this->resourceKey);

        return $data;
    }

    public function __redirectSuccess()
    {
        return redirect()->route('company-profile.content.index', [
            'resource' => $this->resourceKey,
        ]);
    }

    protected function afterStore(): void
    {
        $this->syncConfiguredRelations();
    }

    protected function afterUpdate(): void
    {
        $this->syncConfiguredRelations();
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        foreach ($this->definition['fields'] as $field) {
            if (! isset($field['relation'])) {
                continue;
            }

            $this->relationValues[$field['relation']] = array_map('intval', $data[$field['name']] ?? []);
            unset($data[$field['name']]);
        }

        $slugSource = $this->definition['slugSource'];

        if ($slugSource && blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug((string) $data[$slugSource]);
        }

        if (in_array($this->resourceKey, ['articles', 'content-pages'], true) && blank($data['author_id'] ?? null)) {
            $data['author_id'] = auth()->id();
        }

        return ContentResourceRegistry::normalize($this->resourceKey, $data);
    }

    private function syncConfiguredRelations(): void
    {
        foreach ($this->relationValues as $relationName => $ids) {
            $relation = $this->row->{$relationName}();
            $oldIds = $relation->pluck($relation->getRelated()->getQualifiedKeyName())->map(fn ($id): int => (int) $id)->sort()->values()->all();
            $relation->sync($ids);
            $newIds = $relation->pluck($relation->getRelated()->getQualifiedKeyName())->map(fn ($id): int => (int) $id)->sort()->values()->all();

            if ($oldIds === $newIds) {
                continue;
            }

            activity('model')
                ->performedOn($this->row)
                ->event('updated')
                ->withProperties([
                    'old' => [$relationName => $oldIds],
                    'attributes' => [$relationName => $newIds],
                ])
                ->log("Mengubah relasi {$relationName} ".class_basename($this->row));
        }
    }
}
