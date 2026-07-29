<?php

namespace App\Services\BaseCrud\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait HasBaseTable
{
    use HasPublicRelation;

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            self::__setupBaseTable($model);
        });

        self::saving(function ($model) {
            self::__setupBaseTable($model);
        });

        self::deleting(function ($model) {
            self::__setupDeleteBaseTable($model);
        });
    }

    public static function __setupBaseTable($model): void
    {
        if (empty($model->uuid)) {
            $model->uuid = Str::uuid()->toString();
        }
        if (empty($model->id)) {
            if ($model->created_by === null) {
                $model->created_by = Auth::id();
            }
        }

        if (empty($model->updated_by)) {
            $model->updated_by = Auth::id();
        }
    }

    public static function __setupDeleteBaseTable($model): void
    {
        $attr = $model->getAttributes();
        if (array_key_exists('deleted_by', $attr)) {
            $model->deleted_by = Auth::id();
            $model->save();
        }
    }

    public function __get($key)
    {
        // if (
        //     is_string($this->getAttribute($key)) and $key != 'url' and $key != 'email' and $key != 'notes' and $key != 'remarks_received' and $key != 'pic_email' and $key != 'contact_email' and $key != 'description' and $key != 'uuid' and $key != 'remarks'
        //     and $key != 'slug'
        //     and $key != 'overview'
        //     and $key != 'filename'
        //     and $key != 'attachment2_remarks'
        //     and $key != 'attachment_remarks'
        //     and $key != 'attachment3_remarks'
        // ) {
        //     return strtoupper($this->getAttribute($key));
        // } else {
        return $this->getAttribute($key);
        // }
    }

    public static function getId($uuid, $field = 'uuid'): ?int
    {
        if (empty($uuid)) {
            return null;
        }

        return self::where($field, $uuid)->value('id');
    }

    public static function getFirst($uuid, $field = 'uuid')
    {
        if (empty($uuid)) {
            return null;
        }

        return self::where($field, $uuid)->first();
    }

    public static function getOrFail($uuid, $field = 'uuid')
    {
        return self::where($field, $uuid)->firstOrFail();
    }
}
