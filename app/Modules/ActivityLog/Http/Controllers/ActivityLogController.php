<?php

namespace App\Modules\ActivityLog\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract;
use Spatie\Activitylog\Models\Activity as ActivityLog;
use Exception;
use Illuminate\Http\Request;
use App\Models\Status;

class ActivityLogController extends BaseModuleController
{
    protected $status;
    
    public function __construct(
        protected ActivityLogContract $activityLogRepo
    ){
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'log_name'      => ['label' => 'Log Name', 'html' => true, 'searchable' => 'log_name'], // e.g., default, authentication
            'event'         => ['label' => 'Event', 'html' => true, 'searchable' => 'event'],       // created, updated, deleted
            'causer'        => ['label' => 'Performed By', 'html' => true, 'searchable' => false], // user who performed action
            'created_at'    => ['label' => 'Date & Time', 'searchable' => 'created_at'],
            'action'        => ['label' => 'Action', 'html' => true, 'searchable' => false], // buttons if needed
        ];

        $query = $this->activityLogRepo->getAll();

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
        $row['uuid'] = $row->id;
        $row->log_name = ucwords(str_replace('_', ' ', $row->log_name));

        $eventName = $row->getRawOriginal('event'); // raw value: created, updated, deleted

        $row->event = '<span class="badge rounded-pill px-3 py-2 '
                    . activityEventBadgeClass($eventName)
                    . '">'
                    . ucfirst($eventName)
                    . '</span>';
  
        // Who did the action
        $row->causer = optional($row->causer)->id
            ? view('back-office.partials.avatar', ['user' => $row->causer])->render()
            : '-';

        $row->action = view('back-office.partials.actions', [
            'model'            => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
        ])->render();

        return $row;
    }

    public function show(ActivityLog $activityLog)
    {
        $model = $this->activityLogRepo->showModel($activityLog);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(ActivityLog $activityLog)
    {
        try {
            if($this->activityLogRepo->softDeleteModel($activityLog)) {
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
}