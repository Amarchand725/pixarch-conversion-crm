<?php

namespace App\Modules\BusinessSetting\Models;

use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BusinessSetting extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait;

    protected $fillable = ['category', 'key', 'value', 'input_type', 'status'];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('BusinessSetting'))
            ->logFillable()
            ->logOnlyDirty();
    }
}