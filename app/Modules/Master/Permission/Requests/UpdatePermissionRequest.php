<?php

namespace App\Modules\Master\Permission\Requests;

class UpdatePermissionRequest extends StorePermissionRequest
{
    public function rules(): array
    {
        return $this->permissionRules((int) $this->route('permission'));
    }
}
