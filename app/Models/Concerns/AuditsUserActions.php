<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait AuditsUserActions
{
    public static function bootAuditsUserActions(): void
    {
        static::creating(function (Model $model): void {
            $model->setAttribute('created_by', $model->getAttribute('created_by') ?? auth()->id());
            $model->setAttribute('updated_by', $model->getAttribute('updated_by') ?? auth()->id());
        });

        static::updating(function (Model $model): void {
            $model->setAttribute('updated_by', auth()->id());
        });

        static::deleting(function (Model $model): void {
            $actorId = auth()->id();
            $model->setAttribute('deleted_by', $actorId);
            $model->setAttribute('updated_by', $actorId);

            if (! method_exists($model, 'isForceDeleting') || ! $model->isForceDeleting()) {
                $model->saveQuietly();
            }
        });

        static::restoring(function (Model $model): void {
            $model->setAttribute('deleted_by', null);
            $model->setAttribute('updated_by', auth()->id());
        });
    }
}
