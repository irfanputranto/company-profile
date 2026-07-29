<?php

namespace App\Models;

use Database\Factories\PageVisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    /** @use HasFactory<PageVisitFactory> */
    use HasFactory;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'scope_type',
        'scope_id',
        'route_name',
        'path',
        'visitor_hash',
        'session_hash',
        'referrer_host',
        'device_type',
        'country_code',
        'is_bot',
        'occurred_at',
    ];

    /** @var array<string, int|string|bool> */
    protected $attributes = [
        'scope_type' => 'site',
        'scope_id' => 0,
        'is_bot' => false,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'scope_id' => 'integer',
            'is_bot' => 'boolean',
            'occurred_at' => 'datetime',
        ];
    }
}
