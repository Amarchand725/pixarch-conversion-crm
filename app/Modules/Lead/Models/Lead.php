<?php

namespace App\Modules\Lead\Models;

use App\Models\EntityRelationship;
use App\Models\LogEntityStatus;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\ModelTrait;
use App\Models\User;

class Lead extends Model
{
    use SoftDeletes, LogsActivity, HasFactory, ModelTrait;

    protected $fillable = [
        'lead_capture_id', 
        'source_id', 
        'name', 
        'email', 
        'phone', 
        'value', 
        'status', 
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

    // Tell Laravel where to find the factory
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\LeadFactory::new();
    }

    public function assignees()
    {
        return $this->morphToMany(User::class, 'model', 'entity_relationships');
    }

    public function statusLogs()
    {
        return $this->morphMany(LogEntityStatus::class, 'model');
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
}