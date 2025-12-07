<?php

namespace App\Modules\Meeting\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Meeting\Repositories\Contracts\MeetingContract;
use App\Modules\Meeting\Models\Meeting;

class MeetingRepository extends BaseRepository implements MeetingContract
{
    public function __construct(Meeting $model)
    {
        parent::__construct($model);
    }
}