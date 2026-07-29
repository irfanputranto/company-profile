<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsModelActivity, Notifiable, SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'username',
        'name',
        'avatar_path',
        'email',
        'password',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? route('secure-media.users.avatar', ['user' => $this->uuid, 'v' => $this->updated_at?->timestamp])
            : null;
    }
}
