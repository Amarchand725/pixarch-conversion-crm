<?php

namespace App\Modules\Faq\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Faq\Repositories\Contracts\FaqContract;
use App\Modules\Faq\Models\Faq;

class FaqRepository extends BaseRepository implements FaqContract
{
    public function __construct(Faq $model)
    {
        parent::__construct($model);
    }
}