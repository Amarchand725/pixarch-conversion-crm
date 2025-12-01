<?php

namespace App\Modules\Lead\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Repositories\Eloquent\LeadRepository;
use App\Modules\Lead\Http\Requests\LeadRequest;
use App\Modules\Lead\Http\Requests\LeadStatusRequest;
use App\Modules\Lead\Models\Lead;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    protected $leadRepo;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected $permissionPrefix;
    protected $prefix;
    protected $leadStatus;
    protected $sourceRepo;
    protected $userRepo;

    public function __construct(LeadRepository $leadRepo)
    {
        $this->leadRepo = $leadRepo;
        $this->leadStatus = new Status();
        $this->sourceRepo = new Source();
        $this->userRepo = new User();

        $this->prefix = Str::kebab('Lead');
        $this->routePrefix = 'back-office.'. Str::plural($this->prefix);
        $this->pathInitialize = $this->routePrefix;
        $this->permissionPrefix = Str::snake($this->prefix);
        $this->singularLabel = Str::ucfirst($this->prefix);
        $this->pluralLabel = Str::ucfirst(Str::plural($this->prefix)).' List';
    }

    public function index(Request $request)
    {
        $title = $this->pluralLabel;
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize = $this->routePrefix;
        $singularLabel = $this->singularLabel;

        $statusLeads = $this->leadRepo->getAllCollection();

        $columns = [
            'assigned_to' => ['label' => 'Assigned Agent', 'html' => true, 'searchable' => false],
            'name' => ['label' => 'Lead Name', 'searchable' => 'name'],
            'status_name' => ['label' => 'Status', 'html' => true, 'searchable' => 'lastStatusLog.status.name'],
            'value' => ['label' => 'Value', 'searchable' => 'value'],
            'created_at' => ['label' => 'Created', 'searchable' => 'created_at'],
            'action' => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        // query builder from repository or your service
        $query = $this->leadRepo->getAll();
        $total_leads = $query->count();
        
        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function($row) use ($routeInitialize, $permissionPrefix, $singularLabel){
                $value = $row->value > 0 ? number_format($row->value, 2) : '0.00';
                $row->value = $value;
                
                $row->assigned_to = view('back-office.partials.avatar', ['user' => $row->assignees->first()])->render();
                
                $status = strtolower($row->lastStatusLog?->status?->name ?? '');
                $row->status_name = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                                    . strtoupper($status) .
                                    '</span>';
                $row->action = view('back-office.partials.action-buttons', [
                    'model' => $row,
                    'routeInitialize' => $routeInitialize,
                    'permissionPrefix' => $permissionPrefix,
                    'singularLabel' => $singularLabel,
                ])->render();

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), get_defined_vars());
    }

    public function create()
    {
        $stages = $this->leadStatus->where('model', 'lead')->get();
        $sources = $this->sourceRepo->get();
        $agents = $this->userRepo->role('agent')
                ->whereHas('status', fn($q) => $q->where('name', 'active'))
                ->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(LeadRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->leadRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' added successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Lead $lead)
    {
        $stages = $this->leadStatus->where('model', 'lead')->get();
        $sources = $this->sourceRepo->get();
        $agents = $this->userRepo->role('agent')
                ->whereHas('status', fn($q) => $q->where('name', 'active'))
                ->get();
        $model = $this->leadRepo->showModel($lead);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $payload = $request->validated();
        try {
            $this->leadRepo->updateModel($lead, $payload);
            return successResponse([], $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Lead $lead)
    {
        $model = $this->leadRepo->showModel($lead);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Lead $lead)
    {
        try {
            if($this->leadRepo->softDeleteModel($lead)) {
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

    public function restore(Lead $lead)
    {
        try {
            if($this->leadRepo->restoreModel($lead)) {
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

    public function forceDelete(Lead $lead)
    {
        try {
            if($this->leadRepo->permanentlyDeleteModel($lead)) {
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

    public function bulkDelete()
    {
        try {
            $this->leadRepo->bulkDelete();
            return redirect()->route(strtolower('leads.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadRepo->bulkRestore();
            return redirect()->route(strtolower('leads.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(LeadStatusRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $this->leadRepo->statusModel($payload);
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500); // optional HTTP 500
        }
    }
}