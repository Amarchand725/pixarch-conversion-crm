<?php

namespace App\Modules\LeadCapture\Models;

use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\ModelTrait;
use App\Models\User;

class LeadCapture extends Model
{
    use SoftDeletes, LogsActivity, HasFactory, ModelTrait;

    protected $fillable = ['status_id', 'name', 'fields'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'Campaign')
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
            ->useLogName(strtolower('LeadCapture'))
            ->logFillable()
            ->logOnlyDirty();
    }

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\LeadCaptureFactory::new();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}