<?php

namespace App\Modules\Campaign\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Campaign\Repositories\Contracts\CampaignContract;
use App\Modules\Campaign\Models\Campaign;

class CampaignRepository extends BaseRepository implements CampaignContract
{
    public function __construct(Campaign $model)
    {
        parent::__construct($model);
    }
}