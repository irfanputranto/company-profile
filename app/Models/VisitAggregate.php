<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitAggregate extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'period_type',
        'period_start',
        'scope_type',
        'scope_id',
        'page_views',
        'unique_visitors',
        'sessions',
    ];

    /** @var array<string, int|string> */
    protected $attributes = [
        'scope_type' => 'site',
        'scope_id' => 0,
        'page_views' => 0,
        'unique_visitors' => 0,
        'sessions' => 0,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'scope_id' => 'integer',
            'page_views' => 'integer',
            'unique_visitors' => 'integer',
            'sessions' => 'integer',
        ];
    }
}
