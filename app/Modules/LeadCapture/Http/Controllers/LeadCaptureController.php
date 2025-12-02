<?php

namespace App\Modules\LeadCapture\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\LeadCapture\Http\Requests\LeadCaptureRequest;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeadCaptureController extends BaseModuleController
{
    public function __construct(
        protected LeadCaptureContract $leadCaptureRepo
    ){
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize = $this->routePrefix;
        $singularLabel = $this->singularLabel;

        $columns = [
            'name'      => ['label' => 'name', 'searchable' => 'name'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->leadCaptureRepo->getAll();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function ($row) use ($routeInitialize, $permissionPrefix, $singularLabel) {
                $status = $row->status?->name ?? 'de-active';
                $row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                            . strtoupper($status) .
                            '</span>';

                $row->action = view('back-office.partials.action-buttons', [
                    'model'            => $row,
                    'permissionPrefix' => $permissionPrefix,
                    'routeInitialize'  => $routeInitialize,
                    'singularLabel'    => $singularLabel,
                ])->render();

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), $this->viewWithVars(get_defined_vars()));
    }

    public function create()
    {
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(LeadCaptureRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->leadCaptureRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(LeadCapture $leadCapture)
    {
        $model = $this->leadCaptureRepo->showModel($leadCapture);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(LeadCaptureRequest $request, LeadCapture $leadCapture)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $leadCapture) {
                $this->leadCaptureRepo->updateModel($leadCapture, $payload);
            });
            return successResponse($response, $this->singularLabel. ' updated successfully.');
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

    public function restore(LeadCapture $leadCapture)
    {
        try {
            if($this->leadCaptureRepo->restoreModel($leadCapture)) {
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

    public function forceDelete(LeadCapture $leadCapture)
    {
        try {
            if ($this->leadCaptureRepo->permanentlyDeleteModel($leadCapture)) {
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
            $this->leadCaptureRepo->bulkDelete();
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->leadCaptureRepo->bulkRestore();
            return redirect()->route(strtolower('lead_captures.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}