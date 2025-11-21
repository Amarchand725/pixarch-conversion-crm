<?php

namespace App\Modules\BusinessSetting\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract;
use App\Modules\BusinessSetting\Models\BusinessSetting;

class BusinessSettingRepository extends BaseRepository implements BusinessSettingContract
{
    public function __construct(BusinessSetting $model)
    {
        parent::__construct($model);
    }
}