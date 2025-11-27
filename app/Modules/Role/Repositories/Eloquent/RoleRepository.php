<?php

namespace App\Modules\Role\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Role\Repositories\Contracts\RoleContract;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleContract
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}