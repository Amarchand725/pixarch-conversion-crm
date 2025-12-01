<?php

namespace App\Modules\Team\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Team\Repositories\Contracts\TeamContract;
use App\Modules\Team\Models\Team;

class TeamRepository extends BaseRepository implements TeamContract
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }
}