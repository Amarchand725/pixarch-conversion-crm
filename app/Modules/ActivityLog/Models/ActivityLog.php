<?php

namespace App\Modules\ActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;

class ActivityLog extends Model
{
    use LogsActivity, ModelTrait, HasFactory;

    protected $table = 'activity_log';
    protected $fillable = ['name', 'status_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'ActivityLog')
                    ->where('name', 'active')
                    ->value('id');
            }
        });
    }

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('ActivityLog'))
            ->logFillable()
            ->logOnlyDirty();
    }
        
    // protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    // {
    //     return \Database\Factories\{ActivityLog}Factory::new();
    // }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}