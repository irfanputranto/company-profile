<?php

namespace App\Modules\Master\Permission\Controllers;

use App\Models\Permission;
use App\Modules\Master\Permission\Requests\StorePermissionRequest;
use App\Modules\Master\Permission\Requests\UpdatePermissionRequest;
use App\Services\BaseCrud\BaseWebCrud;
use App\Services\BaseCrud\Traits\HasDynamicFilters;
use App\Support\MasterPermission;

class PermissionController extends BaseWebCrud
{
    use HasDynamicFilters;

    public function __construct()
    {
        $this->successStoreMsg = __('admin.permissions.created');
        $this->successUpdateMsg = __('admin.permissions.updated');
        $this->successDestroyMsg = __('admin.permissions.deleted');
        $this->filterFields = [
            ['type' => 'select', 'name' => 'usage', 'label' => __('admin.permissions.usage'), 'placeholder' => __('admin.permissions.all_permissions'), 'options' => ['assigned' => __('admin.permissions.assigned'), 'unused' => __('admin.permissions.unused')]],
            ['type' => 'select', 'name' => 'kind', 'label' => __('admin.permissions.kind'), 'placeholder' => __('admin.permissions.all_types'), 'options' => ['system' => __('admin.permissions.system_permission'), 'custom' => __('admin.permissions.custom_permission')]],
        ];
    }

    public $model = Permission::class;

    public $modelKey = 'id';

    public $searchAble = ['name', 'guard_name'];

    public $storeValidator = StorePermissionRequest::class;

    public $updateValidator = UpdatePermissionRequest::class;

    public $viewPath = 'adminpanel.pages.master.permissions';

    public $redirectSuccessStore = 'master.permissions.index';

    public $redirectSuccessUpdate = 'master.permissions.index';

    public $successStoreMsg;

    public $successUpdateMsg;

    public $successDestroyMsg;

    public $defaultOrder = 'name';

    public $defaultSort = 'asc';

    public $abilityPolicyIndex = 'view_permissions';

    public $abilityPolicyShow = 'show_permissions';

    public $abilityPolicyStore = 'create_permissions';

    public $abilityPolicyUpdate = 'update_permissions';

    public $abilityPolicyDelete = 'delete_permissions';

    public $enableBulkDelete = false;

    public $lockRelationParam = true;

    protected array $filterFields = [];

    protected function beforeList(): void
    {
        $this->query
            ->withCount(['roles', 'users'])
            ->when($this->requestData->query('usage') === 'assigned', fn ($query) => $query->where(fn ($usageQuery) => $usageQuery->whereHas('roles')->orWhereHas('users')))
            ->when($this->requestData->query('usage') === 'unused', fn ($query) => $query->whereDoesntHave('roles')->whereDoesntHave('users'))
            ->when($this->requestData->query('kind') === 'system', fn ($query) => $query->whereIn('name', self::systemPermissions()))
            ->when($this->requestData->query('kind') === 'custom', fn ($query) => $query->whereNotIn('name', self::systemPermissions()));
    }

    protected function beforeUpdate(): mixed
    {
        if (($this->isSystemPermission($this->row) || $this->isAssigned($this->row)) && $this->row->name !== $this->requestData->string('name')->toString()) {
            return back()->withInput()->with('error_message', __('admin.permissions.cannot_update_used'));
        }

        return null;
    }

    protected function beforeDestroy(): mixed
    {
        if ($this->isSystemPermission($this->row)) {
            return back()->with('error_message', __('admin.permissions.cannot_delete_system'));
        }

        if ($this->row->roles()->exists() || $this->row->users()->exists()) {
            return back()->with('error_message', __('admin.permissions.cannot_delete_used'));
        }

        return null;
    }

    private function isSystemPermission(Permission $permission): bool
    {
        return in_array($permission->name, self::systemPermissions(), true);
    }

    private function isAssigned(Permission $permission): bool
    {
        return $permission->roles()->exists() || $permission->users()->exists();
    }

    /** @return list<string> */
    public static function systemPermissions(): array
    {
        return ['access adminpanel', 'view_activity_logs', 'view_analytics', ...MasterPermission::all()];
    }
}
