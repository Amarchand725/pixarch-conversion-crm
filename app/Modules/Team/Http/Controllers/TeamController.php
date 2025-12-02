<?php

namespace App\Modules\Team\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Modules\Team\Http\Requests\TeamRequest;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Repositories\Contracts\TeamContract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamController extends BaseModuleController
{
    public function __construct(
        protected TeamContract $teamRepo
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

        $query = $this->teamRepo->getAll();

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

    public function store(TeamRequest $request)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->teamRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(Team $team)
    {
        $model = $this->teamRepo->showModel($team);
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(TeamRequest $request, Team $team)
    {
        $payload = $request->validated();
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $team) {
                $this->teamRepo->updateModel($team, $payload);
            });
            return successResponse($response, $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Team $team)
    {
        $model = $this->teamRepo->showModel($team);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Team $team)
    {
        try {
            if($this->teamRepo->softDeleteModel($team)) {
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

    public function restore(Team $team)
    {
        try {
            if($this->teamRepo->restoreModel($team)) {
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

    public function forceDelete(Team $team)
    {
        try {
            if ($this->teamRepo->permanentlyDeleteModel($team)) {
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
            $this->teamRepo->bulkDelete();
            return redirect()->route(strtolower('teams.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->teamRepo->bulkRestore();
            return redirect()->route(strtolower('teams.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}