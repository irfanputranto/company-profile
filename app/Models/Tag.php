<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class Tag extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];
}
