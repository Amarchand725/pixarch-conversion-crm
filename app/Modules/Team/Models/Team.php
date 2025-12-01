<?php

namespace App\Modules\Team\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Traits\NotifiesUsers;
use App\Models\User;

class Team extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, NotifiesUsers;

    protected $fillable = [
        'name',
        'status_id'
    ];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Team'))
            ->logFillable()
            ->logOnlyDirty();
    }

    public function members()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}