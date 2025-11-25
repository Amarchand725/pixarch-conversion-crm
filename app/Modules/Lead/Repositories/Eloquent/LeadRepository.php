<?php

namespace App\Modules\Lead\Repositories\Eloquent;

use App\Models\LogEntityStatus;
use App\Models\Status;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Modules\Lead\Models\Lead;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class LeadRepository extends BaseRepository implements LeadContract
{
    public function __construct(Lead $model)
    {
        parent::__construct($model);
    }

    public function getAll(array $columns = ['*']): EloquentCollection
    {
        $statuses = Status::where('model', 'Lead')->get();

        $leads = Lead::with(['lastStatusLog.status', 'currentAssignee'])->get(); // EloquentCollection

        $statusLeads = new EloquentCollection();

        foreach ($statuses as $status) {
            $filtered = $leads->filter(function ($lead) use ($status) {
                return $lead->lastStatusLog?->status_id === $status->id;
            })->values(); // still EloquentCollection

            // Store both status_id and leads
            $statusLeads[] = [
                'status_id' => $status->uuid,
                'status_name' => $status->name,
                'leads' => $filtered,
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