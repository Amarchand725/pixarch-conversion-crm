<?php

namespace App\Modules\User\Repositories\Eloquent;

use App\Helpers\FileUploader;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\User\Repositories\Contracts\UserContract;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserContract
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model;
        $model->toFill($payload, ['avatar', 'role']);
        $model->password = Hash::make($payload['password']);

        $model->save();
        //uploading avatar image
        if (isset($payload['avatar']) && $payload['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            $model->avatar_id = FileUploader::uploadFile($payload['avatar'], $model, 'avatars', size: 64)?->id;
            $model->save();
        }

        if (!empty($payload['role'])) {
            // You can pass role name or role ID, depending on how you send it
            $model->syncRoles([$payload['role']]); 
        }
        
        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        $model->toFill($payload, ['avatar', 'role']);

        // Handle avatar upload
        if (!empty($payload['avatar']) && $payload['avatar'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete existing avatar if exists
            if ($model?->avatar?->path) {
                FileUploader::deleteFile($model?->avatar?->path);
            }

            // Upload new avatar
            $model->avatar_id = FileUploader::uploadFile(
                $payload['avatar'], 
                $model, 
                'avatars', 
                size: 64
            )?->id;
        }

        $model->save();

        if (!empty($payload['role'])) {
            // You can pass role name or role ID, depending on how you send it
            $model->syncRoles([$payload['role']]); 
        }
        
        return $model;
    }
}