<?php

namespace App\Modules\ActivityLog\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract;
use App\Modules\ActivityLog\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository implements ActivityLogContract
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }
}