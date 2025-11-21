<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
     * Retrieve all records.
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a specific model by ID.
     */
    public function showModel(int|string $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Store a new model.
     */
    public function storeModel(array $data): Model
    {
        try {
            $model = $this->model->create($data);
            Log::info('Created model', ['id' => $model->id]);
        } catch (\Throwable $e) {
            Log::error('Model creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $model;

    }

    /**
     * Update an existing model.
     */
    public function updateModel($model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    /**
     * Soft delete a model.
     */
    public function softDeleteModel(int|string $id): bool
    {
        $model = $this->model->findOrFail($id);
        return (bool) $model->delete();
    }

    /**
     * Restore a soft-deleted model.
     */
    public function restoreModel(int|string $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return (bool) $model->restore();
    }

    /**
     * Permanently delete a model.
     */
    public function permanentlyDeleteModel(int|string $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        return (bool) $model->forceDelete();
    }
}
