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
use App\Modules\Campaign\Models\Campaign;

class LeadCapture extends Model
{
    use SoftDeletes, LogsActivity, HasFactory, ModelTrait;

    protected $fillable = ['status_id', 'campaign_id', 'name', 'description'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'LeadCapture')
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

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function fields()
    {
        return $this->hasMany(CaptureFormField::class, 'lead_capture_id');
    }
}