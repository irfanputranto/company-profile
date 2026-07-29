<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;

class SiteSetting extends AuditableModel
{
    /** @var list<string> */
    protected $fillable = ['key', 'group', 'type', 'value', 'is_public'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['value' => 'array', 'is_public' => 'boolean'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return parent::getActivitylogOptions()->logExcept(['value']);
    }
}
