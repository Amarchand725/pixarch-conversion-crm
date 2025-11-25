<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use App\Models\Traits\LogsModelActivity;
use App\Modules\Lead\Models\Lead;

class LogEntityStatus extends Model
{
    use ModelTrait, LogsModelActivity; 

    protected $fillable = [
        'status_id',
        'model_type', 
        'model_id',
        'assignee_id',
        'meeting_id',
        'description',
    ];

    // Polymorphic relation to any entity
    public function model()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class);
    }
}
