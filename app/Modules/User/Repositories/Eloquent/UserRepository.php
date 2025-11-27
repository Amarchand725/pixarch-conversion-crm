<?php

namespace App\Modules\User\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\User\Repositories\Contracts\UserContract;
use App\Models\User;

class UserRepository extends BaseRepository implements UserContract
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}