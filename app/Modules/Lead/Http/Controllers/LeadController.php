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
use App\Services\LeadImportService;
use App\Services\PhoneNumberService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends BaseModuleController
{  
    protected $leadStatus;
    protected $sourceRepo; 
    protected $userRepo;

    public function __construct(
        protected LeadContract $leadRepo,
        protected LeadImportService $importService
    ){
        $this->leadStatus = new Status();
        $this->sourceRepo = new Source();
        $this->userRepo = new User();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name' => ['label' => 'Lead Name', 'html' => true, 'searchable' => 'name'],
            'assigned_to' => ['label' => 'Assignee', 'html' => true],
            'status_name' => ['label' => 'Status', 'html' => true],
            'budget_amount' => ['label' => 'Budget', 'html' => true],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action' => ['label' => 'Action', 'html' => true],
        ];

        $user = auth()->user();

        // LIST VIEW QUERY
        $query = Lead::query()
            ->with([
                'lastStatusLog.status:id,name',
                'currentAssignee:id,name'
            ])
            ->select([
                'id',
                'uuid',
                'name',
                'email',
                'budget',
                'created_at'
            ]);

        if (!$user->hasRole('Admin')) {
            $query->whereHas('assignees', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $total_leads = (clone $query)->count();

        // ONLY LOAD CARD VIEW DATA IF NEEDED
        $statusLeads = [];

        if (request('view') != 'list') {
            $statusLeads = $this->leadRepo->getKanbanLeads();
        }

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
        // if($row->assignees->first()){
        //     $row->assigned_to = view('back-office.partials.avatar', [
        //             'user' => $row->assignees->first()
        //         ]
        //     )->render();
        // }else{
        //     $row->assignee_to = '-';
        // }

        $user = $row->assignees->first();

        $row->assigned_to = $user
            ? view('back-office.partials.avatar', ['user' => $user])->render()
            : '-';

        $label = module_label('show', $this->singularLabel);
        $shortName = \Illuminate\Support\Str::limit($row->name, 30);

        $row->name = '
            <a href="#" class="show fw-semibold cursor-pointer"
                data-show-url="'.$row->show_url.'"
                data-bs-toggle="modal"
                data-bs-target="#details-modal"
                title="'.e($label).' - '.e($row->name).'"
                label="'.e($label).'">
                '.e($shortName).'
            </a>
        ';

        // $row->name = '
        //     <a href="#" class="show fw-semibold cursor-pointer"
        //         data-show-url="'.$row->show_url.'"
        //         data-bs-toggle="modal"
        //         data-bs-target="#details-modal"
        //         title="'.e($label).'"
        //         label="'.e($label).'"
        //         >
        //         '.e($row->name).'
        //     </a>
        // ';
        
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

    public function loadMore(Request $request, $statusId)
    {
        $offset = $request->offset ?? 0;

        $user = auth()->user();

        $query = Lead::query()
            ->with([
                'lastStatusLog.status:id,name',
                'source:id,name',
                'assignees:id,name',
                'assignees.avatar:id,path',
            ])
            ->whereHas('lastStatusLog', function ($q) use ($statusId) {
                $q->where('status_id', $statusId);
            });

        if (!$user->hasRole('Admin')) {

            $query->where(function ($q) use ($user) {

                $q->whereHas('assignees', function ($qq) use ($user) {
                    $qq->where('user_id', $user->id);
                });

                $q->orWhereHas('lastStatusLog.status', function ($qq) {
                    $qq->where('name', 'Pool');
                });
            });
        }

        $leads = $query
            ->latest('id')
            ->skip($offset)
            ->take(20)
            ->get();

        return view(
            'back-office.leads.partials.lead-cards',
            $this->viewWithVars(get_defined_vars())
        )->render();
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
        $payload['phone'] = $parsed['e164'];
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
        $payload['phone'] = $parsed['e164'];
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
            $response['route'] = route($this->routePrefix . '.index');
            
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
    public function importForm(Request $request)
    {
        return (string) view($this->pathInitialize.'.import_content', get_defined_vars());
    }
     /**
     * Upload Excel and import leads
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        
        $response = [];
        $this->importService->importFromCsv($request->file('file'));

        $response['route'] = route($this->routePrefix . '.index');
        $response['status'] = true; 
        return successResponse($response, module_message('created', $this->singularLabel));
    }

    public function actionEdit($action, Lead $lead)
    {
        $stages = $this->leadStatus->where('model', 'lead')->get();
        $agents = $this->userRepo
        ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
        ->whereHas('status', fn($q) => $q->where('name', 'active'))
        ->get();

        $notes = $lead->statusLogs()
        ->whereNotNull('description')
        ->get();

        return (string) view($this->pathInitialize.'.action_content', get_defined_vars());
    }
}