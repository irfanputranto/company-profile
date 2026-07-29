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

    public $model = Role::class;

    public $modelKey = 'id';

    public $searchAble = ['name', 'guard_name', 'permissions.name'];

    public $storeValidator = StoreRoleRequest::class;

    public $updateValidator = UpdateRoleRequest::class;

    public $viewPath = 'adminpanel.pages.master.roles';

    public $redirectSuccessStore = 'master.roles.index';

    public $redirectSuccessUpdate = 'master.roles.index';

    public $successStoreMsg = 'Role dan permission berhasil ditambahkan.';

    public $successUpdateMsg = 'Role dan permission berhasil diperbarui.';

    public $successDestroyMsg = 'Role berhasil dihapus.';

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

    protected array $filterFields = [
        ['type' => 'select', 'name' => 'usage', 'label' => 'Penggunaan', 'placeholder' => 'Semua role', 'options' => ['assigned' => 'Sudah digunakan', 'unused' => 'Belum digunakan']],
        ['type' => 'select', 'name' => 'kind', 'label' => 'Jenis', 'placeholder' => 'Semua jenis', 'options' => ['system' => 'Role sistem', 'custom' => 'Role operasional']],
    ];

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
            ? back()->with('error_message', 'Role sistem tidak dapat diubah.')
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
            return back()->with('error_message', 'Role sistem tidak dapat dihapus.');
        }

        if ($this->row->users()->exists()) {
            return back()->with('error_message', 'Role masih digunakan oleh pengguna.');
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
                ->log('Mengubah permission Role');
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

            return 'Akses Sistem';
        });

        return $data;
    }
}
