<?php

namespace App\Modules\Master\User\Requests;

use App\Models\User;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        $userId = User::query()->where('uuid', $this->route('user'))->value('id');

        return $this->userRules($userId, false);
    }
}
