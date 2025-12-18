<?php

namespace App\Modules\Campaign\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Status;
use App\Models\User;
use App\Modules\Campaign\Http\Requests\CampaignRequest;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\Campaign\Repositories\Contracts\CampaignContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CampaignController extends BaseModuleController
{
    protected $status;
    protected $agent;

    public function __construct(
        protected CampaignContract $campaignRepo
    ){
        $this->status = new Status();
        $this->agent = new User();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name'      => ['label' => 'Name', 'searchable' => 'name'],
            'type'      => ['label' => 'Type', 'searchable' => 'type'],
            'budget_value'      => ['label' => 'Budget', 'html' => true, 'searchable' => 'budget'],
            'start_date'      => ['label' => 'Start Date', 'searchable' => 'start_date'],
            'end_date'      => ['label' => 'End Date', 'searchable' => 'end_date'],
            'status_label'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->campaignRepo->getAll();
        $total_count = $query->count();

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
        // Keep numeric value untouched for DataTables
        $amount = floatval($row->budget ?? 0);
        $symbol = config('app.currency_symbol');
        // New property for display
        $row->budget_value = '<span class="text-success">'.$symbol . number_format($amount, 2) . '</span>';

        $status = $row->status?->name ?? 'de-active';
        $row->status_label = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                    . strtoupper($status) .
                    '</span>';
        $row->action = view('back-office.partials.actions', [
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
        $agents = $this->agent->where('status_id',$agent_status_id)->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(CampaignRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->campaignRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Campaign $campaign)
    {
        $statuses = $this->status->where('model', 'Campaign')->get();
        $agent_status_id = $this->status->where('model', 'User')->where('name', 'active')->value('id');
        $agents = $this->agent->where('status_id',$agent_status_id)->get();
        $model = $this->campaignRepo->showModel($campaign);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(CampaignRequest $request, Campaign $campaign)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $campaign) {
                $this->campaignRepo->updateModel($campaign, $payload);
            });
            return successResponse($response, $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Campaign $campaign)
    {
        $model = $this->campaignRepo->showModel($campaign);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Campaign $campaign)
    {
        try {
            if($this->campaignRepo->softDeleteModel($campaign)) {
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

    public function restore(Campaign $campaign)
    {
        try {
            if($this->campaignRepo->restoreModel($campaign)) {
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

    public function forceDelete(Campaign $campaign)
    {
        try {
            if ($this->campaignRepo->permanentlyDeleteModel($campaign)) {
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
            $this->campaignRepo->bulkDelete();
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->campaignRepo->bulkRestore();
            return redirect()->route(strtolower('campaigns.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}