<?php

namespace App\Modules\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\ModelTrait;

class LeadCapture extends Model
{
    use SoftDeletes, LogsActivity, HasFactory, ModelTrait;

    protected $fillable = ['status_id', 'name', 'fields'];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('LeadCapture'))
            ->logFillable()
            ->logOnlyDirty();
    }

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\LeadCaptureFactory::new();
    }
}