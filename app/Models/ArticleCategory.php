<?php

namespace App\Models;

use App\Models\Concerns\HasContentTranslations;

class ArticleCategory extends AuditableModel
{
    use HasContentTranslations;

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'description'];
}
