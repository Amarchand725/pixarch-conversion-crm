<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseContract
{
    public function getAllCollection(array $columns = ['*']);
    public function getAll(array $columns = ['*']);

    public function showModel(Model $model, array $relations = []);

    public function storeModel(array $data);

    public function updateModel(Model $model, array $data);

    public function softDeleteModel(Model $model);

    public function restoreModel(Model $model);

    public function permanentlyDeleteModel(Model $model);
}
