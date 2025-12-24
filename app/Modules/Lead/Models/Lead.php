<?php

namespace App\Modules\Lead\Models;

use App\Models\EntityRelationship;
use App\Models\LogEntityStatus;
use App\Models\Meeting;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\ModelTrait;
use App\Models\Traits\NotifiesUsers;
use App\Models\User;
use App\Services\PhoneNumberService;

class Lead extends Model
{
    use SoftDeletes, LogsActivity, HasFactory, ModelTrait, NotifiesUsers;

    protected $fillable = [
        'lead_capture_id', 
        'source_id', 
        'name', 
        'email', 
        'phone', 
        'numeric_code', 
        'iso_code', 
        'budget', 
        'pipeline',
        'status', 
        'faq_status',
        'created_at',
        'updated_at',
        'fields'
    ];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Lead'))
            ->logFillable()
            ->logOnlyDirty();
    }

    protected $casts = [
        'fields' => 'array',
    ];

    public function setPhoneAttribute($value)
    {
        $data = PhoneNumberService::parse($value); // returns array

        $this->attributes['phone'] = $data['e164'];         // string
        $this->attributes['numeric_code'] = $data['numeric_code']; // string
        $this->attributes['iso_code'] = $data['iso_code'];        // string
    }

    // Tell Laravel where to find the factory
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\LeadFactory::new();
    }

    public function assignees()
    {
        return $this->morphToMany(User::class, 'model', 'entity_relationships')
                ->withTimestamps();
    }

    public function statusLogs()
    {
        return $this->morphMany(LogEntityStatus::class, 'model')->orderby('id', 'desc');
    }

    public function lastStatusLog()
    {
        return $this->morphOne(LogEntityStatus::class, 'model')->latestOfMany();
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function currentAssignee()
    {
        return $this->hasOne(EntityRelationship::class, 'model_id');
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'lead_id');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class)->orderby('id', 'desc');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}