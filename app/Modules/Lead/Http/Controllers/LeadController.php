<?php

namespace App\Modules\Lead\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Http\Requests\LeadRequest;
use App\Modules\Lead\Http\Requests\LeadStatusRequest;
use App\Modules\Lead\Models\Lead;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Services\PhoneNumberService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends BaseModuleController
{  
    protected $leadStatus;
    protected $sourceRepo;
    protected $userRepo;
    protected $importService;

    public function __construct(
        protected LeadContract $leadRepo
    ){
        $this->leadStatus = new Status();
        $this->sourceRepo = new Source();
        $this->userRepo = new User();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $statusLeads = $this->leadRepo->getAllCollection();
        $columns = [
            'name' => ['label' => 'Lead Name', 'html' => true, 'searchable' => 'name'],
            'assigned_to' => ['label' => 'Assignee', 'html' => true, 'searchable' => false],
            'status_name' => ['label' => 'Status', 'html' => true, 'searchable' => 'lastStatusLog.status.name'],
            'pipeline' => ['label' => 'Pipeline', 'searchable' => 'pipeline'],
            'budget_amount' => ['label' => 'Budget', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action' => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $user = auth()->user();

        // If Admin → fetch all meetings
        if ($user->hasRole('Admin')) {
            $query = $this->leadRepo->getAll(); // builder
        } 
        // Else → fetch only meetings where user is attendee
        else {
            $query = $user->leads(); // builder
        }

        $total_leads = $query->count();
        
        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: [$this, 'formatRow']
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view($this->pathInitialize.'.index', $this->viewWithVars(get_defined_vars()));
    }

    public function formatRow($row)
    {   
        $extraActions = [];

        $row->show_url = route($this->routePrefix . '.show', $row->uuid);

        // Keep numeric value untouched for DataTables
        $amount = floatval($row->budget ?? 0);
        $symbol = config('app.currency_symbol');
        // New property for display
        $row->budget_amount = '<span class="text-success">'.$symbol . number_format($amount, 2) . '</span>';
        if($row->assignees->first()){
            $row->assigned_to = view('back-office.partials.avatar', [
                    'user' => $row->assignees->first()
                ]
            )->render();
        }else{
            $row->assignee_to = '-';
        }

        $label = $this->singularLabel.' Details';
        $row->name = '
            <a href="#" class="show fw-semibold cursor-pointer"
                data-show-url="'.$row->show_url.'"
                data-bs-toggle="modal"
                data-bs-target="#details-modal"
                title="'.e($label).'"
                label="'.e($label).'"
                >
                '.e($row->name).'
            </a>
        ';
        
        $status = strtolower($row->lastStatusLog?->status?->name ?? '');
        $row->status_name = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                            . strtoupper($status) .
                            '</span>';

        //Adding extra custom actions
        $extraActions = [];
        $extraActions[] = view($this->pathInitialize.'.custom-actions', [
            'model' => $row,
            'routeInitialize' => $this->routePrefix,
            'singularLabel' => $this->singularLabel,
            'permissionPrefix' => $this->permissionPrefix,
        ])->render();

        $row->action = view('back-office.partials.actions', [
            'model' => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
            'extraActions' => $extraActions, // Pass the extra buttons
        ])->render();

        $row->show_url = route($this->routePrefix.'.show', $row->uuid);

        return $row;
    }

    public function create()
    {
        $stages = $this->leadStatus->where('model', 'lead')->get();
        $sources = $this->sourceRepo->get();
        $agents = $this->userRepo
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->whereHas('status', fn($q) => $q->where('name', 'active'))
            ->get();
            
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(LeadRequest $request, PhoneNumberService $phoneService)
    {
        $payload = $request->validated();
        $parsed = $phoneService->parse($request->phone);
        $payload['numeric_code'] = $parsed['numeric_code'];
        $payload['iso_code'] = $parsed['iso_code'];
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $response = $this->leadRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
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
        $agents = $this->userRepo
                    ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
                    ->whereHas('status', fn($q) => $q->where('name', 'active'))
                    ->get();

        $model = $this->leadRepo->showModel($lead);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(LeadRequest $request, Lead $lead, PhoneNumberService $phoneService)
    {
        $payload = $request->validated();
        $parsed = $phoneService->parse($request->phone);
        $payload['numeric_code'] = $parsed['numeric_code'];
        $payload['iso_code'] = $parsed['iso_code'];
        try {
            $response = $this->leadRepo->updateModel($lead, $payload);
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Lead $lead)
    {
        $symbol = config('app.currency_symbol');
        $model = $this->leadRepo->showModel($lead, [
            'assignees', 'statusLogs', 'lastStatusLog', 'source', 'currentAssignee', 'meeting', 'author', 'meetings'
        ]);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Lead $lead)
    {
        try {
            if($this->leadRepo->softDeleteModel($lead)) {
                return response()->json([
                    'status' => true,
                    'message' => module_message('deleted', $this->singularLabel)
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
                return redirect()->back()->with('message', module_message('restored', $this->singularLabel));
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
                    'message' => module_message('permanently-deleted', $this->singularLabel)
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
            return redirect()->route('back-office.leads.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadRepo->bulkRestore();
            return redirect()->route('back-office.leads.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function updateStatus(LeadStatusRequest $request, Lead $lead)
    {
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $lead) {        
                $response = $this->leadRepo->statusModel($lead, $payload);
            });

            return successResponse($response, module_message('status_changed', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500); // optional HTTP 500
        }
    }

     /**
     * Upload Excel and import leads
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $this->importService->import($request->file('file'));
        return response()->json([
            'status' => true,
            'message' => module_message('imported', $this->singularLabel)
        ]);
    }

    public function actionEdit($action, Lead $lead)
    {
        $stages = $this->leadStatus->where('model', 'lead')->get();
        $agents = $this->userRepo
        ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
        ->whereHas('status', fn($q) => $q->where('name', 'active'))
        ->get();

        return (string) view($this->pathInitialize.'.action_content', get_defined_vars());
    }
}