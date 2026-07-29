<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('model')
            ->logAll()
            ->logExcept(['password', 'remember_token'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName): string => match ($eventName) {
                'created' => 'Menambahkan '.class_basename($this),
                'updated' => 'Mengubah '.class_basename($this),
                'deleted' => 'Menghapus '.class_basename($this),
                'restored' => 'Memulihkan '.class_basename($this),
                default => $eventName.' '.class_basename($this),
            });
    }
}
