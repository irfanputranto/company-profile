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

    public $model = Permission::class;

    public $modelKey = 'id';

    public $searchAble = ['name', 'guard_name'];

    public $storeValidator = StorePermissionRequest::class;

    public $updateValidator = UpdatePermissionRequest::class;

    public $viewPath = 'adminpanel.pages.master.permissions';

    public $redirectSuccessStore = 'master.permissions.index';

    public $redirectSuccessUpdate = 'master.permissions.index';

    public $successStoreMsg = 'Permission berhasil ditambahkan.';

    public $successUpdateMsg = 'Permission berhasil diperbarui.';

    public $successDestroyMsg = 'Permission berhasil dihapus.';

    public $defaultOrder = 'name';

    public $defaultSort = 'asc';

    public $abilityPolicyIndex = 'view_permissions';

    public $abilityPolicyShow = 'show_permissions';

    public $abilityPolicyStore = 'create_permissions';

    public $abilityPolicyUpdate = 'update_permissions';

    public $abilityPolicyDelete = 'delete_permissions';

    public $enableBulkDelete = false;

    public $lockRelationParam = true;

    protected array $filterFields = [
        ['type' => 'select', 'name' => 'usage', 'label' => 'Penggunaan', 'placeholder' => 'Semua permission', 'options' => ['assigned' => 'Sudah digunakan', 'unused' => 'Belum digunakan']],
        ['type' => 'select', 'name' => 'kind', 'label' => 'Jenis', 'placeholder' => 'Semua jenis', 'options' => ['system' => 'Permission sistem', 'custom' => 'Permission tambahan']],
    ];

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
            return back()->withInput()->with('error_message', 'Permission yang digunakan sistem, role, atau pengguna tidak dapat diubah.');
        }

        return null;
    }

    protected function beforeDestroy(): mixed
    {
        if ($this->isSystemPermission($this->row)) {
            return back()->with('error_message', 'Permission sistem tidak dapat dihapus.');
        }

        if ($this->row->roles()->exists() || $this->row->users()->exists()) {
            return back()->with('error_message', 'Permission masih digunakan oleh role atau pengguna.');
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
        return ['access adminpanel', 'view_activity_logs', ...MasterPermission::all()];
    }
}
