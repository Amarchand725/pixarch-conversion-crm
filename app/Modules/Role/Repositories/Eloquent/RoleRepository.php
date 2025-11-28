<?php

namespace App\Modules\Role\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Role\Repositories\Contracts\RoleContract;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleContract
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function updateModel(Model $model, array $payload): Model
    {
        if (!empty($payload['permissions'])) {
            $model->syncPermissions($payload['permissions']);
        }

        $model->save();

        return $model;
    }
}