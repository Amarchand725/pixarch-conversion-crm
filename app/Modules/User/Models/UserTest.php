<?php

namespace App\Modules\User\Models;

use App\Models\Attachment;
use App\Models\Role;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use SoftDeletes, LogsActivity, HasRoles;

    protected $fillable = ['id:increments', 'uuid:uuid', 'name:string', 'status:boolean'];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('User'))
            ->logFillable()
            ->logOnlyDirty();
    }

    public function avatar()
    {
        return $this->belongsTo(Attachment::class, 'avatar_id');
    }

    public function statusInfo()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
}