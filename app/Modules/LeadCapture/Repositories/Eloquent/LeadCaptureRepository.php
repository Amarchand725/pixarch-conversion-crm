<?php

namespace App\Modules\LeadCapture\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract;
use App\Modules\LeadCapture\Models\LeadCapture;

class LeadCaptureRepository extends BaseRepository implements LeadCaptureContract
{
    public function __construct(LeadCapture $model)
    {
        parent::__construct($model);
    }
}