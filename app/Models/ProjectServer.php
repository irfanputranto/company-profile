<?php

namespace App\Models;

use Database\Factories\ProjectServerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;

class ProjectServer extends AuditableModel
{
    /** @use HasFactory<ProjectServerFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'managed_project_id', 'name', 'provider', 'environment', 'host', 'port', 'username',
        'password', 'api_secret', 'credentials_note', 'billing_cycle', 'base_price', 'selling_price',
        'currency', 'purchased_at', 'expires_at', 'reminder_days', 'last_notified_at', 'status', 'notes',
    ];

    /** @var list<string> */
    protected $hidden = ['username', 'password', 'api_secret', 'credentials_note'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'username' => 'encrypted',
            'password' => 'encrypted',
            'api_secret' => 'encrypted',
            'credentials_note' => 'encrypted',
            'base_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'purchased_at' => 'date',
            'expires_at' => 'date',
            'last_notified_at' => 'datetime',
            'reminder_days' => 'integer',
            'port' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('model')
            ->logOnly([
                'managed_project_id', 'name', 'provider', 'environment', 'host', 'port',
                'billing_cycle', 'base_price', 'selling_price', 'currency', 'purchased_at',
                'expires_at', 'reminder_days', 'status', 'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /** @return Attribute<float, never> */
    protected function profitPercentage(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->base_price > 0
            ? (((float) $this->selling_price - (float) $this->base_price) / (float) $this->base_price) * 100
            : 0);
    }

    public function managedProject(): BelongsTo
    {
        return $this->belongsTo(ManagedProject::class);
    }
}
