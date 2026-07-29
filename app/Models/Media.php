<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends AuditableModel
{
    /** @var list<string> */
    protected $fillable = [
        'uuid',
        'uploaded_by',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'extension',
        'byte_size',
        'width',
        'height',
        'alt_text',
        'caption',
        'created_by',
        'updated_by',
        'deleted_by',
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

    /** @return HasMany<MediaVariant, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    public function pathWithoutExtension(): string
    {
        return Str::beforeLast($this->path, '.');
    }

    public function publicUrl(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
