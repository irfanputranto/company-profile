<?php

namespace App\Modules\Master\Role\Requests;

class UpdateRoleRequest extends StoreRoleRequest
{
    public function rules(): array
    {
        return $this->roleRules((int) $this->route('role'));
    }
}
