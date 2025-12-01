<?php

namespace App\Modules\Team\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Team extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait;

    protected $fillable = ['name', 'status'];

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
}