<?php

namespace App\Modules\Lead\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Modules\Lead\Models\Lead;

class LeadRepository extends BaseRepository implements LeadContract
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }
}