<?php

namespace App\Modules\Meeting\Models;

use App\Models\LogEntityStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;

class Meeting extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = ['name', 'status_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->status_id)) {
                $model->status_id = Status::where('model', 'Meeting')
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
            ->useLogName(strtolower('Meeting'))
            ->logFillable()
            ->logOnlyDirty();
    }
        
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\MeetingFactory::new();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'meeting_users', 'meeting_id', 'user_id');
    }

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function statusLogs()
    {
        return $this->morphMany(LogEntityStatus::class, 'model');
    }

    public function lastStatusLog()
    {
        return $this->morphOne(LogEntityStatus::class, 'model')->latestOfMany();
    }
}