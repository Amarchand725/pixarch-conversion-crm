<?php

namespace App\Modules\Meeting\Repositories\Eloquent;

use App\Models\Status;
use App\Modules\Lead\Models\Lead;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Meeting\Repositories\Contracts\MeetingContract;
use App\Modules\Meeting\Models\Meeting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingRepository extends BaseRepository implements MeetingContract
{
    public function __construct(Meeting $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $lead = Lead::where('id', $payload['lead_id'])->firstOrFail();
        
        $model = $this->model;

        //attendee user
        $attendee_id = $payload['attendee_id'] ?? auth()->user()->id;

        $serverTz = $payload['time_zone'] ?? config('app.timezone');
        // Convert payload to server timezone before comparing
        $payload['start_date_time'] = Carbon::parse($payload['start_date_time'])->setTimezone($serverTz);
        $payload['end_date_time'] = Carbon::parse($payload['end_date_time'])->setTimezone($serverTz);

        $conflict = $model->whereHas('attendees', function ($q) use ($attendee_id) {
                $q->where('user_id', $attendee_id);
            })
            ->where(function ($q) use ($payload) {
                $q->whereBetween('start_date_time', [$payload['start_date_time'], $payload['end_date_time']])
                ->orWhereBetween('end_date_time', [$payload['start_date_time'], $payload['end_date_time']]);
            })
            ->where(function ($q) use ($payload) {
                $q->where(function ($q2) use ($payload) {
                    $q2->where('start_date_time', '<', $payload['end_date_time'])
                    ->where('end_date_time', '>', $payload['start_date_time']);
                });
            })
            ->exists();

        if ($conflict) {
            throw new \Exception('Attendee already has a meeting scheduled during this time.');
        }

        //adding meeting status
        $payload['time_zone'] = config('app.timezone');
        
        // 🗓️ 2. Create or update meeting for this lead
        $model->toFill($payload, ['attendee_id']);
        $model->save();
        
        // 👥 3. Attach attendee(s) via pivot table in model_users
        $model->attendees()->sync([$attendee_id]);

        // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
        $attendees = $model->attendees; // belongsTo

        if ($attendees && $attendees->count()) {
            foreach ($attendees as $attendee) {
                $link = rtrim(env('FULL_APP_URL'), '/') . '/leads/' . $lead->uuid;                     
                $assigner = auth()->user();
                $assigner_avatar = $assigner?->avatar?->path ?? asset('back-office/assets/img/avatars/default-avatar.png');
                $title = ucfirst($assigner->name).' has rescheduled your meeting';
                $model->notifyUser(
                    $attendee,
                    $assigner_avatar,
                    $title,
                    "Your meeting for '{$lead->name}' lead on {$model->start_date_time}",
                    $link,
                    'meeting_scheduled'
                );
            }
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        $lead = Lead::where('id', $payload['lead_id'])->firstOrFail();
        
        //attendee user
        $attendee_id = $payload['attendee_id'] ?? auth()->user()->id;

        $serverTz = $payload['time_zone'] ?? config('app.timezone');
        // Convert payload to server timezone before comparing
        $payload['start_date_time'] = Carbon::parse($payload['start_date_time'])->setTimezone($serverTz);
        $payload['end_date_time'] = Carbon::parse($payload['end_date_time'])->setTimezone($serverTz);

        $conflict = $model->whereHas('attendees', function ($q) use ($attendee_id) {
                $q->where('user_id', $attendee_id);
            })
            ->where(function ($q) use ($payload) {
                $q->whereBetween('start_date_time', [$payload['start_date_time'], $payload['end_date_time']])
                ->orWhereBetween('end_date_time', [$payload['start_date_time'], $payload['end_date_time']]);
            })
            ->where(function ($q) use ($payload) {
                $q->where(function ($q2) use ($payload) {
                    $q2->where('start_date_time', '<', $payload['end_date_time'])
                    ->where('end_date_time', '>', $payload['start_date_time']);
                });
            })
            ->exists();

        if ($conflict) {
            throw new \Exception('Attendee already has a meeting scheduled during this time.');
        }

        //adding meeting status
        $payload['time_zone'] = config('app.timezone');
        
        // 🗓️ 2. Create or update meeting for this lead
        $model->toFill($payload, ['attendee_id']);
        $model->save();
        
        // 👥 3. Attach attendee(s) via pivot table in model_users
        $model->attendees()->sync([$attendee_id]);

        // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
        $attendees = $model->attendees; // belongsTo

        if ($attendees && $attendees->count()) {
            foreach ($attendees as $attendee) {
                $link = rtrim(env('FULL_APP_URL'), '/') . '/leads/' . $lead->uuid;                     
                $assigner = auth()->user();
                $assigner_avatar = $assigner?->avatar?->path ?? asset('back-office/assets/img/avatars/default-avatar.png');
                $title = ucfirst($assigner->name).' has rescheduled your meeting';
                $model->notifyUser(
                    $attendee,
                    $assigner_avatar,
                    $title,
                    "Your meeting for '{$lead->name}' lead on {$model->start_date_time}",
                    $link,
                    'meeting_scheduled'
                );
            }
        }

        return $model;
    }

    public function statusModel(Model $model, array $payload){
        if($payload['action']=='status'){
            $model->toFill($payload, ['attendee_id']);
            $model->save();
        }else{
            $payload['attendee_id'] = $payload['attendee_id'] ?? auth()->user()->id;
            if (!empty($payload['start_date_time']) && !empty($payload['attendee_id'])) {
                //attendee user
                $attendee_id = $payload['attendee_id'] ?? auth()->user()->id;

                $serverTz = $payload['time_zone'] ?? config('app.timezone');
                // Convert payload to server timezone before comparing
                $payload['start_date_time'] = Carbon::parse($payload['start_date_time'])->setTimezone($serverTz);
                $payload['end_date_time'] = Carbon::parse($payload['end_date_time'])->setTimezone($serverTz);

                $conflict = $model->whereHas('attendees', function ($q) use ($attendee_id) {
                        $q->where('user_id', $attendee_id);
                    })
                    ->where(function ($q) use ($payload) {
                        $q->whereBetween('start_date_time', [$payload['start_date_time'], $payload['end_date_time']])
                        ->orWhereBetween('end_date_time', [$payload['start_date_time'], $payload['end_date_time']]);
                    })
                    ->where(function ($q) use ($payload) {
                        $q->where(function ($q2) use ($payload) {
                            $q2->where('start_date_time', '<', $payload['end_date_time'])
                            ->where('end_date_time', '>', $payload['start_date_time']);
                        });
                    })
                    ->exists();

                if ($conflict) {
                    throw new \Exception('Attendee already has a meeting scheduled during this time.');
                }

                //adding meeting status
                $payload['time_zone'] = config('app.timezone');

                $model->status_id = Status::where('model', "Meeting")->where('name', 'Rescheduled')->value('id');
                $model->save();
                
                // 🗓️ 2. Create or update meeting for this lead
                $model = Meeting::make();
                $model->toFill($payload, ['attendee_id']);
                $model->save();
                
                // 👥 3. Attach attendee(s) via pivot table in meeting_users
                $model->attendees()->sync([$attendee_id]);
                
                DB::commit();

                // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
                $attendees = $model->attendees; // belongsTo

                if ($attendees && $attendees->count()) {
                    foreach ($attendees as $attendee) {
                        $link = rtrim(env('FULL_APP_URL'), '/') . '/leads/' . $model->uuid;                     
                        $lead = $model ?? 'N/A';
                        $assigner = auth()->user();
                        $assigner_avatar = $assigner?->avatar?->path ?? asset('back-office/assets/img/avatars/default-avatar.png');
                        $title = ucfirst($assigner->name).' has rescheduled your meeting';
                        $model->notifyUser(
                            $attendee,
                            $assigner_avatar,
                            $title,
                            "Your meeting for '{$lead->name}' lead on {$model->start_date_time}",
                            $link,
                            'meeting_scheduled'
                        );
                    }
                }
            }
        }

        return response()->json(['success' => true]);
    }
}