<?php

namespace App\Modules\Lead\Repositories\Eloquent;

use App\Models\Meeting;
use App\Models\Status;
use App\Models\User;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Modules\Lead\Models\Lead;
use App\Services\LeadAssigner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SendsModelNotifications;

class LeadRepository extends BaseRepository implements LeadContract
{
    use SendsModelNotifications;

    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }

    public function getAllCollection(array $columns = ['*']): Collection
    {
        $user = auth()->user();
        $statuses = Status::where('model', 'Lead')->get();

        // Get “Pool” status
        $poolStatus = Status::where('model', 'Lead')->where('name', 'Pool')->first();

        // -----------------------------------
        // ADMIN: Get all leads
        // -----------------------------------
        if ($user->hasRole('Admin')) {
            $leads = Lead::with(['lastStatusLog.status', 'currentAssignee'])->get();
        }

        // -----------------------------------
        // AGENT: Assigned leads + Pool leads
        // -----------------------------------
        
        else {

            // Leads assigned to this agent
            $assignedLeadIds = DB::table('entity_relationships')
                ->where('model_type', 'Lead')
                ->where('user_id', $user->id)
                ->pluck('model_id');

            // Pool leads = leads with pool status (visible to all)
            $poolLeadIds = $poolStatus
                ? Lead::whereHas('lastStatusLog', function ($q) use ($poolStatus) {
                    $q->where('status_id', $poolStatus->id);
                })
                ->pluck('id')
                : collect();

            // Merge assigned + pool leads
            $leadIds = $assignedLeadIds->merge($poolLeadIds)->unique();

            $leads = Lead::whereIn('id', $leadIds)
                ->with(['lastStatusLog.status', 'currentAssignee'])
                ->get();
        }

        // -----------------------------------
        // GROUP BY STATUS
        // -----------------------------------
        $statusLeads = new Collection();

        foreach ($statuses as $status) {
            $filtered = $leads->filter(function ($lead) use ($status) {
                return $lead->lastStatusLog?->status_id === $status->id;
            })->values();

            $statusLeads[] = [
                'status_id'   => $status->uuid,
                'status_name' => $status->name,
                'leads'       => $filtered,
            ];
        }

        return $statusLeads;
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model;
        $model->toFill($payload, ['status_id', 'assignee_id']);
        $model->save();

        $logStatus['amount'] = $payload['budget'];
        $logStatus['status_id'] = $payload['status_id'];
        $logStatus['assignee_id'] = $payload['assignee_id'];
        $logStatus['model_id'] = $model->id;
        $logStatus['model_type'] = $model->getMorphClass();

        $log = $model->statusLogs()->firstOrNew();
        $log->toFill($logStatus);
        $log->save();

        if(empty($payload['assignee_id'])){
            $payload['assignee_id'] = LeadAssigner::getNextAgent($payload['iso_code']); //assignee rol-robbin agent id
        }

        if (!empty($payload['assignee_id'])) {
            $model->assignees()->sync([$payload['assignee_id']]);

            // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
            $assignees = $model->assignees; // belongsTo
            $assigner = null;
            if(auth()->user()){
                $assigner = auth()->user();
            }else{
                $assigner = User::role('Admin')->firstOrFail();
            }

            if ($assignees && $assignees->count()) {
                $this->sendNotification(
                    $model,
                    $model->assignees,
                    ucfirst(auth()->user()->name) . ' has assigned you a lead',
                    "{$model->name} ({$model->email}) - {$model->pipeline}",
                    'lead_assigned'
                );
            }
        }else{
            $model->assignees()->sync([auth()->id()]);
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        $model->toFill($payload, ['status_id', 'assignee_id']);
        $model->save();

        $logStatus['amount'] = $payload['budget'];
        $logStatus['status_id'] = $payload['status_id'];
        $logStatus['assignee_id'] = $payload['assignee_id'];
        $logStatus['model_id'] = $model->id;
        $logStatus['model_type'] = $model->getMorphClass();

        $log = $model->statusLogs()->firstOrNew();
        $log->toFill($logStatus);
        $log->save();

        if(empty($payload['assignee_id'])){
            $payload['assignee_id'] = LeadAssigner::getNextAgent($payload['iso_code']); //assignee rol-robbin agent id
        }

        if (!empty($payload['assignee_id'])) {
            $model->assignees()->sync([$payload['assignee_id']]);

            // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
            $assignees = $model->assignees; // belongsTo
            if ($assignees && $assignees->count()) {
                $this->sendNotification(
                    $model,
                    $model->assignees,
                    ucfirst(auth()->user()->name) . ' has assigned you a lead',
                    "{$model->name} ({$model->email}) - {$model->pipeline}",
                    'lead_assigned'
                );
            }
        }else{
            $model->assignees()->sync([auth()->id()]);
        }

        return $model;
    }

    public function statusModel(Model $model, array $payload){
        $assignee_id = $payload['assignee_id'] ?? $model->lastStatusLog?->assignee_id;
        $payload['attendee_id'] = $payload['attendee_id'] ?? auth()->user()->id;

        if($model?->lastStatusLog?->status?->name == 'pool' && !auth()->user()->hasRole('Admin')){
            $model->assignees()->attach(auth()->id());
            $assignee_id = auth()->id();
        }

        // 🟢 If status is "meeting"
        if (!empty($payload['start_date_time']) && !empty($payload['attendee_id'])) {
            $meeting = $this->meetingSchedule($model, $payload);
            $logStatus['meeting_id'] = $meeting->id;
        }

        $amount = $model->budget;
        if(isset($payload['amount'])){
            $amount = $payload['amount'] ?? 0;
        }

        $logStatus['description'] = $payload['description'] ?? $model?->lastStatusLog?->description ??  'Status updated via drag & drop';
        $logStatus['amount'] = $amount;
        $logStatus['status_id'] = $payload['status_id'];
        $logStatus['assignee_id'] = $assignee_id;
        $logStatus['model_id'] = $model->id;
        $logStatus['model_type'] = $model->getMorphClass();

        $log = $model->statusLogs()->make();
        $log->toFill($logStatus);
        $log->save();

        if($model->budget != $amount){
            $model->budget = $amount;
            $model->save();
        }

        //notify assignee only if changed
        if($log && $model->lastStatusLog?->assignee_id != $assignee_id){
            //assign new agent lead.
            $model->assignees()->sync([$assignee_id]);

            // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
            $assignees = $model->assignees; // belongsTo

            if ($assignees && $assignees->count() && auth()->user()->id != $assignee_id) {
                $this->sendNotification(
                $model,
                $model->assignees,
                ucfirst(auth()->user()->name) . ' has assigned you a lead',
                "{$model->name} ({$model->email}) - {$model->pipeline}",
                'lead_status_updated'
                );
            }
        }elseif($log && $model->lastStatusLog?->status_id != $payload['status_id'] && auth()->user()->id != $model->lastStatusLog?->assignee_id && statusName('Lead', $payload['status_id']) != 'pool'){
            // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
            $assignees = $model->assignees; // belongsTo

            if ($assignees && $assignees->count()) {
                $this->sendNotification(
                    $model,
                    $model->assignees,
                    ucfirst(auth()->user()->name) . ' has updated status of your lead',
                    "{$model->name} ({$model->email}) - {$model->pipeline}",
                    'lead_status_updated'
                );
            }
        }elseif(statusName('Lead', $payload['status_id']) == 'pool'){
            $status_id = Status::where('model', 'User')->where('name', 'active')->value('id');
            $agents = User::where('id', '!=', auth()->user()->id)->where('status_id', $status_id)->get();
            if ($agents && $agents->count() > 0) {
                $this->sendNotification(
                    $model,
                    $agents,
                    ucfirst(auth()->user()->name) . ' has added a lead to the pool',
                    "{$model->name} ({$model->email}) - {$model->pipeline}",
                    'lead_added_to_pool'
                );
            }
        }

        return ['status'=>true];
    }

    public function meetingSchedule(Lead $model, array $payload)
    {
        //attendee user
        $attendee_id = $payload['attendee_id'];

        $serverTz = $payload['time_zone'] ?? config('app.timezone');
        // Convert payload to server timezone before comparing
        $payload['start_date_time'] = Carbon::parse($payload['start_date_time'])->setTimezone($serverTz);
        $payload['end_date_time'] = Carbon::parse($payload['end_date_time'])->setTimezone($serverTz);

        $conflict = Meeting::whereHas('attendees', function ($q) use ($attendee_id) {
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
        $meeting = $model->meetings()->make();
        $meeting->toFill($payload, ['attendee_id']);
        $meeting->save();

        $meeting->status_id = Status::where('model', "Meeting")->where('name', 'Upcoming')->first()->id ?? null;

        $meeting->status_id = Status::where('model', 'Meeting')
            ->where('name', 'Upcoming')
            ->value('id');

        $meeting->save();
        
        // 👥 3. Attach attendee(s) via pivot table in meeting_users
        $meeting->attendees()->sync([$attendee_id]);
        
        // $logStatus['meeting_id'] = $meeting->id;

        // ✅ MANUAL NOTIFICATION RIGHT AFTER SAVE
        $attendees = $meeting->attendees; // belongsTo

        if ($attendees && $attendees->count()) {
            if(auth()->user()->id==$attendee_id){ //self-meeting
                $admin = User::role('Admin')->first();
                $this->sendNotification(
                    $model,
                    [$admin],
                    ucfirst(auth()->user()->name) . ' has scheduled a meeting',
                    "Meeting scheduled for '{$model->name}' lead on {$meeting->start_date_time}",
                    'meeting_scheduled'
                );
            }else{ //meeting for another user
                $this->sendNotification(
                    $model,
                    $attendees,
                    ucfirst(auth()->user()->name) . ' has scheduled a meeting for you',
                    "Your meeting for '{$model->name}' lead on {$meeting->start_date_time}",
                    'meeting_scheduled'
                );
            }
        }

        return $meeting;
    }
}