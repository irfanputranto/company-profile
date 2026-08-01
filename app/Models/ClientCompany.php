<?php

namespace App\Models;

use Database\Factories\ClientCompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientCompany extends AuditableModel
{
    /** @use HasFactory<ClientCompanyFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address', 'notes'];

    /** @return HasMany<ManagedProject, $this> */
    public function managedProjects(): HasMany
    {
        return $this->hasMany(ManagedProject::class);
    }
}
