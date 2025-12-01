<?php

namespace App\Modules\Team\Repositories\Eloquent;

use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Team\Repositories\Contracts\TeamContract;
use App\Modules\Team\Models\Team;
use Illuminate\Database\Eloquent\Model;

class TeamRepository extends BaseRepository implements TeamContract
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {   
        $model = $this->model->make();
        $model->toFill($payload);
        $model->save();

        if (!empty($payload['members'])) {
            $model->members()->sync($payload['members']);

            // ✅ Notify each member after sync
            $members = $model->members; // Assuming relation: hasMany/belongsToMany
            if ($members && $members->count()) {
                foreach ($members as $member) {
                    $role = $member->pivot->role ?? 'member'; // if using a pivot table with a 'role' field
                    $teamName = $model->name??'N/A';
                    if ($role === 'lead') {
                        $title = 'Assigned as Team Lead';
                        $message = "You’ve been assigned as the team lead for team '{$teamName}'.";
                        $type = 'assigned_as_lead';
                    } else {
                        $title = 'Added as Team Member';
                        $message = "You’ve been added as a team member under team '{$teamName}'.";
                        $type = 'added_to_team';
                    }

                    $link = rtrim(env('FULL_APP_URL'), '/') . '/teams/' . $model->uuid;

                    $model->notifyUser(
                        $member,
                        $title,
                        $message,
                        $link,
                        $type
                    );
                }
            }
        }
        
        return $model; 
    }

    public function updateModel(Model $model, array $payload): Model
    {   
        $model->toFill($payload); 
        $model->save();

        if (!empty($payload['members'])) {
            $model->members()->detach();
            $model->members()->attach($payload['members']);
     
            // ✅ Notify each member after sync
            $members = $model->members; // Assuming relation: hasMany/belongsToMany
            if ($members && $members->count()) {
                foreach ($members as $member) {
                    $role = $member->pivot->role ?? 'member'; // if using a pivot table with a 'role' field
                    $teamName = $model->name??'N/A';
                    if ($role === 'lead') {
                        $title = 'Assigned as Team Lead';
                        $message = "You’ve been assigned as the team lead for team '{$teamName}'.";
                        $type = 'assigned_as_lead';
                    } else {
                        $title = 'Added as Team Member';
                        $message = "You’ve been added as a team member under team '{$teamName}'.";
                        $type = 'added_to_team';
                    }

                    $link = rtrim(env('FULL_APP_URL'), '/') . '/teams/' . $model->uuid;

                    $model->notifyUser(
                        $member,
                        $title,
                        $message,
                        $link,
                        $type
                    );
                }
            }
        }
        
        return $model;
    }
}