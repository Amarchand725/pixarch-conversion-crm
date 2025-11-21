<?php
namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()              // log all attributes
            ->logOnlyDirty()        // log only changed attributes
            ->dontSubmitEmptyLogs() // skip if nothing changed
            ->useLogName(class_basename($this)); // log name = model name
    }
}
