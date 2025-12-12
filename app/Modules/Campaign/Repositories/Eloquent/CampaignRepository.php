<?php

namespace App\Modules\Campaign\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Campaign\Repositories\Contracts\CampaignContract;
use App\Modules\Campaign\Models\Campaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CampaignRepository extends BaseRepository implements CampaignContract
{
    public function __construct(Campaign $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model;
        $model->toFill($payload, ['user_ids']);
        $model->save();

        if (isset($payload['user_ids'])){
            $model->agents()->sync($payload['user_ids']);
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        $model->toFill($payload, ['user_ids']);
        $model->save();

        if (isset($payload['user_ids'])) {
            $model->agents()->sync($payload['user_ids']); // assign/update
        } else {
            $model->agents()->sync([]); // remove all if user_ids not set
        }

        return $model;
    }
}