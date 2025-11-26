<?php

namespace App\Modules\Role\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Role extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = ['id:increments', 'uuid:uuid', 'name:string', 'status:boolean'];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Role'))
            ->logFillable()
            ->logOnlyDirty();
    }
}