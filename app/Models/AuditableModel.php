<?php

namespace App\Models;

use App\Models\Concerns\AuditsUserActions;
use App\Services\BaseCrud\Traits\HasBaseOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class AuditableModel extends Model
{
    use AuditsUserActions, HasBaseOwner, LogsModelActivity, SoftDeletes;
}
