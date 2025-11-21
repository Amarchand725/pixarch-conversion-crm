<?php
namespace App\Core\Traits;

use Spatie\Activitylog\LogOptions;

trait ModuleActivityLog
{
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->setDescriptionForEvent(fn(string $eventName) =>
                class_basename($this) . " has been {$eventName}"
            );
    }
}
