<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository implements BaseContract
{
    /**
     * The model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $model;

    /**
     * Inject the model into the repository.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve all builder.
     */

    public function getAll(array $columns = ['*']): Builder
    {
        return $this->model->orderby('id','desc')->select($columns); // returns query builder
    }

    /**
     * Retrieve all records collection.
     */
    public function getAllCollection(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a specific model by ID.
     */
    public function showModel(Model $model, array $relations = []): Model
    {
        if (!empty($relations)) {
            $model->load($relations);
        }

        return $model;
    }

    /**
     * Store a new model.
     */
    public function storeModel(array $data): Model
    {
        try {
            $model = $this->model->create($data);
        } catch (\Throwable $e) {
            Log::error('Model creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $model;

    }

    /**
     * Update an existing model.
     */
    public function updateModel(Model $model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a model.
     */
    public function softDeleteModel(Model $model): bool
    {
        return (bool) $model->delete();
    }

    /**
     * Restore a soft-deleted model.
     */
    public function restoreModel(Model $model): bool
    {
        return (bool) $model->restore();
    }

    /**
     * Permanently delete a model.
     */
    public function permanentlyDeleteModel(Model $model): bool
    {
        return (bool) $model->forceDelete();
    }
}
