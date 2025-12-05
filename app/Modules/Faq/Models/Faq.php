<?php

namespace App\Modules\Faq\Models;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

    protected $fillable = ['status_id', 'question', 'answer', 'order'];

    /**
     * Configure Spatie Activity Log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(strtolower('Faq'))
            ->logFillable()
            ->logOnlyDirty();
    }

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\FaqFactory::new();
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