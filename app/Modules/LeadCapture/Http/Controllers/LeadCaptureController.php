<?php

namespace App\Modules\LeadCapture\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Status;
use App\Modules\Campaign\Models\Campaign;
use App\Modules\LeadCapture\Http\Requests\LeadCaptureRequest;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeadCaptureController extends BaseModuleController
{
    protected $status;
    protected $campaigns;

    public function __construct(
        protected LeadCaptureContract $leadCaptureRepo
    ){
        $this->status = new Status();
        $this->campaigns = new Campaign();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'campaign_id' => ['label' => 'Campaign Name', 'html' => true, 'searchable' => false],
            'name'      => ['label' => 'Form Name', 'searchable' => 'name'],
            'status_id'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->leadCaptureRepo->getAll()->with(['campaign', 'status', 'author']);
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
        $row->campaign_id = $row->campaign?->name ?? '-';
        $status = $row->status?->name ?? 'de-active';
        $row->status_id = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                        . strtoupper($status) .
                        '</span>';

        $row->author_id = $row->author
            ? view('back-office.partials.avatar', ['user' => $row->author])->render()
            : '-';

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
        $flag = false;
        $status_id = $this->status->where('model', 'Campaign')->where('name', 'active')->value('id');
        $campaigns = $this->campaigns->where('status_id', $status_id)->get();
        $formHtml = (string) view($this->pathInitialize.'.create_content', get_defined_vars());
        return response()->json(['html' => $formHtml, 'flag' => $flag]);
    }

    public function store(LeadCaptureRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->leadCaptureRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(LeadCapture $leadCapture)
    {
        $flag = true;
        $status_id = $this->status->where('model', 'Campaign')->where('name', 'active')->value('id');
        $campaigns = $this->campaigns->where('status_id', $status_id)->get();
        $statuses = $this->status->where('model', 'LeadCapture')->get();
        $model = $this->leadCaptureRepo->showModel($leadCapture, ['campaign', 'status', 'author', 'fields']);
        $formHtml = (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
        return response()->json(['html' => $formHtml, 'flag' => $flag]);
    }

    public function update(LeadCaptureRequest $request, LeadCapture $leadCapture)
    {
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $leadCapture) {
                $this->leadCaptureRepo->updateModel($leadCapture, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(LeadCapture $leadCapture)
    {
        $model = $this->leadCaptureRepo->showModel($leadCapture);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(LeadCapture $leadCapture)
    {
        try {
            if($this->leadCaptureRepo->softDeleteModel($leadCapture)) {
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

    public function restore(LeadCapture $leadCapture)
    {
        try {
            if($this->leadCaptureRepo->restoreModel($leadCapture)) {
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

    public function forceDelete(LeadCapture $leadCapture)
    {
        try {
            if ($this->leadCaptureRepo->permanentlyDeleteModel($leadCapture)) {
                return response()->json([
                    'status' => true,
                    'message' => module_message('permanently-deleted', $this->singularLabel)
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
            $this->leadCaptureRepo->bulkDelete();
            return redirect()->route('back-office.lead-captures.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadCaptureRepo->bulkRestore();
            return redirect()->route('back-office.lead-captures.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}