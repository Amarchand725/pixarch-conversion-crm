<?php

namespace App\Models;

use App\Models\Traits\LogsModelActivity;
use App\Models\Traits\ModelTrait;
use App\Modules\Lead\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use LogsModelActivity, ModelTrait, NotifiesUsers, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'status_id',
        'time_zone',
        'start_date_time',
        'end_date_time',
        'description',
    ];   

    protected static function booted()
    {
        static::creating(function ($meeting) {
            if (empty($meeting->status_id)) {
                $meeting->status_id = Status::where('model', 'Meeting')
                    ->where('name', 'Upcoming')
                    ->value('id');
            }
        });
    }
    
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'meeting_users', 'meeting_id', 'user_id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function lead(){
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function statusLogs()
    {
        return $this->morphMany(LogEntityStatus::class, 'model');
    }
}
