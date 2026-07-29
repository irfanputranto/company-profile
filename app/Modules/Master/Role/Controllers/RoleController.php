<?php

namespace App\Modules\Master\Role\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Modules\Master\Role\Requests\StoreRoleRequest;
use App\Modules\Master\Role\Requests\UpdateRoleRequest;
use App\Services\BaseCrud\BaseWebCrud;
use App\Services\BaseCrud\Traits\HasDynamicFilters;

class RoleController extends BaseWebCrud
{
    use HasDynamicFilters;

    public const SYSTEM_ROLES = ['administrator', 'superadmin'];

    public function __construct()
    {
        $this->successStoreMsg = __('admin.roles.created');
        $this->successUpdateMsg = __('admin.roles.updated');
        $this->successDestroyMsg = __('admin.roles.deleted');
        $this->filterFields = [
            ['type' => 'select', 'name' => 'usage', 'label' => __('admin.roles.usage'), 'placeholder' => __('admin.roles.all_roles'), 'options' => ['assigned' => __('admin.roles.assigned'), 'unused' => __('admin.roles.unused')]],
            ['type' => 'select', 'name' => 'kind', 'label' => __('admin.roles.kind'), 'placeholder' => __('admin.roles.all_types'), 'options' => ['system' => __('admin.roles.system_role'), 'custom' => __('admin.roles.operational_role')]],
        ];
    }

    public $model = Role::class;

    public $modelKey = 'id';

    public $searchAble = ['name', 'guard_name', 'permissions.name'];

    public $storeValidator = StoreRoleRequest::class;

    public $updateValidator = UpdateRoleRequest::class;

    public $viewPath = 'adminpanel.pages.master.roles';

    public $redirectSuccessStore = 'master.roles.index';

    public $redirectSuccessUpdate = 'master.roles.index';

    public $successStoreMsg;

    public $successUpdateMsg;

    public $successDestroyMsg;

    public $defaultOrder = 'name';

    public $defaultSort = 'asc';

    public $relationList = ['permissions:id,name,guard_name'];

    public $relationShow = ['permissions:id,name,guard_name'];

    public $abilityPolicyIndex = 'view_roles';

    public $abilityPolicyShow = 'show_roles';

    public $abilityPolicyStore = 'create_roles';

    public $abilityPolicyUpdate = 'update_roles';

    public $abilityPolicyDelete = 'delete_roles';

    public $enableBulkDelete = false;

    public $lockRelationParam = true;

    protected array $filterFields = [];

    public function __prepareDataStore($data): array
    {
        unset($data['permission_ids']);

        return $data;
    }

    public function __prepareDataUpdate($data): array
    {
        unset($data['permission_ids']);

        return $data;
    }

    protected function beforeList(): void
    {
        $this->query
            ->withCount(['permissions', 'users'])
            ->when($this->requestData->query('usage') === 'assigned', fn ($query) => $query->has('users'))
            ->when($this->requestData->query('usage') === 'unused', fn ($query) => $query->doesntHave('users'))
            ->when($this->requestData->query('kind') === 'system', fn ($query) => $query->whereIn('name', self::SYSTEM_ROLES))
            ->when($this->requestData->query('kind') === 'custom', fn ($query) => $query->whereNotIn('name', self::SYSTEM_ROLES));
    }

    protected function beforeUpdate(): mixed
    {
        return $this->isSystemRole($this->row)
            ? back()->with('error_message', __('admin.roles.cannot_update_system'))
            : null;
    }

    protected function afterStore(): void
    {
        $this->syncPermissions();
    }

    protected function afterUpdate(): void
    {
        $this->syncPermissions();
    }

    protected function beforeDestroy(): mixed
    {
        if ($this->isSystemRole($this->row)) {
            return back()->with('error_message', __('admin.roles.cannot_delete_system'));
        }

        if ($this->row->users()->exists()) {
            return back()->with('error_message', __('admin.roles.cannot_delete_used'));
        }

        return null;
    }

    private function syncPermissions(): void
    {
        $oldPermissions = $this->row->permissions()->pluck('name')->sort()->values()->all();
        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereKey($this->requestData->validated('permission_ids', []))
            ->get();

        $this->row->syncPermissions($permissions);
        $newPermissions = $this->row->permissions()->pluck('name')->sort()->values()->all();

        if ($oldPermissions !== $newPermissions) {
            activity('model')
                ->performedOn($this->row)
                ->event('updated')
                ->withProperties(['old' => ['permissions' => $oldPermissions], 'attributes' => ['permissions' => $newPermissions]])
                ->log(__('admin.roles.permissions_activity'));
        }
    }

    private function isSystemRole(Role $role): bool
    {
        return in_array($role->name, self::SYSTEM_ROLES, true);
    }

    public function __extraData($data): array
    {
        $permissions = Permission::query()->where('guard_name', 'web')->orderBy('name')->get(['id', 'name', 'guard_name']);
        $data['permissions'] = $permissions;
        $data['permissionGroups'] = $permissions->groupBy(function (Permission $permission): string {
            foreach (['view_', 'create_', 'show_', 'update_', 'delete_'] as $actionPrefix) {
                if (str($permission->name)->startsWith($actionPrefix)) {
                    return str($permission->name)->after($actionPrefix)->replace('_', ' ')->title()->toString();
                }
            }

            return __('admin.roles.system_access_group');
        });

        return $data;
    }
}
