<?php

namespace App\Modules\Lead\Repositories\Eloquent;

use App\Models\LogEntityStatus;
use App\Models\Status;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Modules\Lead\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LeadRepository extends BaseRepository implements LeadContract
{
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

    public function statusModel($payload){
        $model = Lead::where('id', $payload['lead_id'])->firstOrFail();

        LogEntityStatus::create([
            'author_id'   => auth()->id(), // logged-in user
            'model_id'    => $model->id,
            'model_type'  => $model->getMorphClass(),
            'status_id'   => $payload['status_id'],
            'assignee_id' => $model->currentAssignee?->user_id ?? null,
            'description' => 'Status updated via drag & drop',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => true]);
    }
}