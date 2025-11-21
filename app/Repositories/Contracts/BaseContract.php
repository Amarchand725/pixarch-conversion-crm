<?php

namespace App\Repositories\Contracts;

interface BaseContract
{
    public function getAll(array $columns = ['*']);

    public function showModel(int|string $id);

    public function storeModel(array $data);

    public function updateModel(int|string $id, array $data);

    public function softDeleteModel(int|string $id);

    public function restoreModel(int|string $id);

    public function permanentlyDeleteModel(int|string $id);
}
