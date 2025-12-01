<?php

namespace App\Modules\Team\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Team\Repositories\Eloquent\TeamRepository;
use App\Modules\Team\Http\Requests\TeamRequest;
use App\Modules\Team\Models\Team;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class TeamController extends Controller
{
    protected $teamRepo;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected $permissionPrefix;
    protected $prefix;

    public function __construct(TeamRepository $teamRepo)
    {
        $this->prefix = Str::kebab('Team');
        $this->routePrefix = 'back-office.'. Str::plural($this->prefix);
        $this->pathInitialize = $this->routePrefix;
        $this->permissionPrefix = Str::snake($this->prefix);
        $this->singularLabel = Str::ucfirst($this->prefix);
        $this->pluralLabel = Str::ucfirst(Str::plural($this->prefix)).' List';
    }

    public function index(Request $request)
    {
        $title            = $this->pluralLabel;
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize  = $this->routePrefix;
        $singularLabel    = $this->singularLabel;

        $columns = [
            'name'      => ['label' => 'name', 'searchable' => 'name'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        $query = $this->{{variable}}Repo
            ->getAll()
            ->with(['avatar', 'roles', 'statusInfo']);

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

                $row->role = $row->getRoleNames()[0] ?? 'N/A';

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view($this->pathInitialize.'.index', get_defined_vars());
    }


    public function create()
    {
        $title = $this->pluralLabel;
        $roles = $this->roleRepo->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(TeamRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->teamRepo->storeModel($payload);
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
        $title = $this->pluralLabel;
        $model = $this->userRepo->showModel($team);
        $roles = $this->roleRepo->get();
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(TeamRequest $request, Team $team)
    {
        $payload = $request->validated();
        try {
            $this->teamRepo->updateModel($team, $payload);
            return successResponse([], $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(Team $team)
    {
        $model = $this->userRepo->showModel($team);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(Team $team)
    {
        try {
            if($this->userRepo->softDeleteModel($team)) {
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

    public function restore({Team $team)
    {
        try {
            if($this->userRepo->restoreModel($team)) {
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
            if ($this->userRepo->permanentlyDeleteModel($team)) {
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