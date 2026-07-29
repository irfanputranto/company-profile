<?php

namespace App\Modules\Master\User\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Modules\Master\User\Requests\StoreUserRequest;
use App\Modules\Master\User\Requests\UpdateUserRequest;
use App\Services\BaseCrud\BaseWebCrud;
use App\Services\BaseCrud\Traits\HasDynamicFilters;
use App\Services\OptimizedImageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends BaseWebCrud
{
    use HasDynamicFilters;

    private ?string $newAvatarPath = null;

    private ?string $oldAvatarPath = null;

    public function __construct(private readonly OptimizedImageService $optimizedImageService) {}

    public $model = User::class;

    public $modelKey = 'uuid';

    public $searchAble = ['name', 'username', 'email'];

    public $storeValidator = StoreUserRequest::class;

    public $updateValidator = UpdateUserRequest::class;

    public $viewPath = 'adminpanel.pages.master.users';

    public $redirectSuccessStore = 'master.users.index';

    public $redirectSuccessUpdate = 'master.users.index';

    public $successStoreMsg = 'Pengguna berhasil ditambahkan.';

    public $successUpdateMsg = 'Pengguna berhasil diperbarui.';

    public $successDestroyMsg = 'Pengguna berhasil dihapus.';

    public $defaultOrder = 'name';

    public $defaultSort = 'asc';

    public $relationList = ['roles:id,name'];

    public $relationShow = ['roles:id,name'];

    public $abilityPolicyIndex = 'view_users';

    public $abilityPolicyShow = 'show_users';

    public $abilityPolicyStore = 'create_users';

    public $abilityPolicyUpdate = 'update_users';

    public $abilityPolicyDelete = 'delete_users';

    public $enableBulkDelete = false;

    public $lockRelationParam = true;

    protected array $filterFields = [
        ['type' => 'select', 'name' => 'role_id', 'label' => 'Role', 'placeholder' => 'Semua role', 'options' => []],
        ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'Semua status', 'options' => ['active' => 'Aktif', 'inactive' => 'Tidak aktif']],
    ];

    public function __prepareDataStore($data): array
    {
        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }

    public function __prepareDataUpdate($data): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function beforeList(): void
    {
        $this->query
            ->when($this->requestData->integer('role_id'), fn ($query, int $roleId) => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->whereKey($roleId)))
            ->when(in_array($this->requestData->query('status'), ['active', 'inactive'], true), fn ($query) => $query->where('is_active', $this->requestData->query('status') === 'active'));
    }

    protected function afterStore(): void
    {
        $this->syncRole();
        $this->storeUploadedPhoto();
    }

    protected function afterUpdate(): void
    {
        $this->syncRole();

        if ($this->requestData->boolean('remove_photo') && ! $this->requestData->hasFile('photo')) {
            $this->removeCurrentPhoto();
        }

        $this->storeUploadedPhoto();
    }

    protected function beforeUpdate(): mixed
    {
        if (auth()->id() === $this->row->id && ! $this->requestData->boolean('is_active')) {
            return back()->withInput()->withErrors(['is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        return null;
    }

    protected function beforeDestroy(): mixed
    {
        return auth()->id() === $this->row->id
            ? back()->with('error_message', 'Anda tidak dapat menghapus akun sendiri.')
            : null;
    }

    private function syncRole(): void
    {
        User::query()->whereKey($this->row->id)->lockForUpdate()->firstOrFail();

        $oldRoles = $this->row->roles()->pluck('name')->sort()->values()->all();
        $role = Role::query()->findOrFail($this->requestData->integer('role_id'));
        $this->row->syncRoles([$role]);
        $newRoles = $this->row->roles()->pluck('name')->sort()->values()->all();

        if ($oldRoles !== $newRoles) {
            activity('model')
                ->performedOn($this->row)
                ->event('updated')
                ->withProperties(['old' => ['roles' => $oldRoles], 'attributes' => ['roles' => $newRoles]])
                ->log('Mengubah role User');
        }
    }

    private function storeUploadedPhoto(): void
    {
        if (! $this->requestData->hasFile('photo')) {
            return;
        }

        $this->oldAvatarPath = $this->row->avatar_path;
        $this->newAvatarPath = $this->optimizedImageService->store($this->requestData->file('photo'), 'users/avatars');
        $this->row->forceFill(['avatar_path' => $this->newAvatarPath])->save();
    }

    private function removeCurrentPhoto(): void
    {
        if (! $this->row->avatar_path) {
            return;
        }

        $this->oldAvatarPath = $this->row->avatar_path;
        $this->row->forceFill(['avatar_path' => null])->save();
    }

    public function __afterCommit(): void
    {
        if ($this->oldAvatarPath) {
            Storage::disk(config('filesystems.private_media_disk'))->delete($this->oldAvatarPath);
        }
    }

    public function __errorDBSafe(mixed $throwable): mixed
    {
        if ($this->newAvatarPath) {
            Storage::disk(config('filesystems.private_media_disk'))->delete($this->newAvatarPath);
        }

        return parent::__errorDBSafe($throwable);
    }

    public function __extraData($data): array
    {
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);
        $this->filterFields[0]['options'] = $roles->pluck('name', 'id')->all();
        $data['roles'] = $roles;

        return $data;
    }
}
