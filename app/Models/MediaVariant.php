<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaVariant extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'media_id',
        'name',
        'disk',
        'path',
        'mime_type',
        'byte_size',
        'width',
        'height',
    ];

    /** @var array<string, string> */
    protected $attributes = [
        'mime_type' => 'image/webp',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'byte_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /** @return BelongsTo<Media, $this> */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
