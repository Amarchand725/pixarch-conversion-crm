<?php

namespace App\Modules\Meeting\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Meeting\Repositories\Contracts\MeetingContract;
use App\Modules\Meeting\Http\Requests\MeetingRequest;
use App\Modules\Meeting\Models\Meeting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;

class MeetingController extends BaseModuleController
{
    protected $status;
    protected $lead;
    protected $agent;
    
    public function __construct(
        protected MeetingContract $meetingRepo
    ){
        $this->status = new Status();
        $this->lead = new Lead();
        $this->agent = new User();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'attendee'     => ['label' => 'Attendee', 'html' => true, 'searchable' => false],
            'lead_name'      => ['label' => 'Lead Name', 'searchable' => false],
            'status_lab'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'meeting_date_time'     => ['label' => 'Date & Time', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->meetingRepo->getAll();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: [$this, 'formatRow']
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    public function formatRow($row)
    {
        $status = $row->status?->name ?? 'de-active';
        $row->status_lab = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                    . strtoupper($status) .
                    '</span>';

        $lead = $row?->lead ?? '-';
        if($lead){
            $row->lead_name = $lead?->name.' ('.$lead?->phone.')';
        }else{
            $row->lead_name = '-';
        }

        $row->meeting_date_time = $row?->start_date_time.' | '.$row?->end_date_time;
                    
        $attendee = $row->attendees()->first() ?? '';
        $row->attendee = $attendee
                ? view('back-office.partials.avatar', ['user' => $attendee])->render()
                : '-';

        $row->action = view('back-office.partials.action-buttons', [
            'model'            => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
        ])->render();

        return $row;
    }

    public function create()
    {
        $agent_status_id = $this->status->where('model', 'User')->where('name', 'active')->value('id');
        $lead_status_id = $this->status->where('model', 'Lead')->where('name', 'active')->value('id');
        $agents = $this->agent->where('status_id',$agent_status_id)->get();
        $leads = $this->lead->where('status_id',$lead_status_id)->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(MeetingRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->meetingRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' scheduled successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Meeting $meeting)
    {
        $statuses = $this->status->where('model', 'Meeting')->get();
        $agent_status_id = $this->status->where('model', 'User')->where('name', 'active')->value('id');
        $lead_status_id = $this->status->where('model', 'Lead')->where('name', 'active')->value('id');
        $agents = $this->agent->where('status_id',$agent_status_id)->get();
        $leads = $this->lead->where('status_id',$lead_status_id)->get();
        $model = $this->meetingRepo->showModel($meeting);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(MeetingRequest $request, Meeting $meeting)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $meeting) {
                $this->meetingRepo->updateModel($meeting, $payload);
            });
            return successResponse([], $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Meeting $meeting)
    {
        $model = $this->meetingRepo->showModel($meeting);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Meeting $meeting)
    {
        try {
            if($this->meetingRepo->softDeleteModel($meeting)) {
                return response()->json([
                    'status' => true,
                    'message' => $this->singularLabel.' Deleted Successfully'
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function restore(Meeting $meeting)
    {
        try {
            if($this->meetingRepo->restoreModel($meeting)) {
                return redirect()->back()->with('message', 'Record Restored Successfully.');
            } else {
                return false;
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function forceDelete(Meeting $meeting)
    {
        try {
            if ($this->meetingRepo->permanentlyDeleteModel($meeting)) {
                return response()->json([
                    'status' => true,
                    'message' => $this->singularLabel.' Deleted Successfully'
                ]);
            } else{
                return response()->json([
                    'status' => true,
                    'error' => $this->singularLabel.' not deleted try again.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->meetingRepo->bulkDelete();
            return redirect()->route(strtolower('meetings.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->meetingRepo->bulkRestore();
            return redirect()->route(strtolower('meetings.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}