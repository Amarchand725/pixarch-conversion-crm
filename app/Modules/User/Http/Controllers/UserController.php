<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Status;
use Spatie\Permission\Models\Role;
use App\Modules\User\Http\Requests\UserRequest;
use App\Models\User;
use App\Modules\User\Repositories\Contracts\UserContract;
use Exception;
use Illuminate\Http\Request;

class UserController extends BaseModuleController
{
    protected $roleRepo;
    protected $status;

    public function __construct(
        protected UserContract $userRepo
    ){
        $this->roleRepo = new Role();
        $this->status = new Status();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $permissionPrefix = $this->permissionPrefix;
        $routeInitialize = $this->routePrefix;
        $singularLabel = $this->singularLabel;

        $columns = [
            'agent'     => ['label' => 'Agent', 'html' => true, 'searchable' => 'name'],
            'role'       => ['label' => 'Role', 'searchable' => 'roles.name'],
            'phone'      => ['label' => 'Phone', 'searchable' => 'phone'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        // Get query builder from repository (perfect for DataTables)
        $query = $this->userRepo->getAll()->with(['avatar', 'roles', 'statusInfo']); // eager-load status

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function($row) use ($routeInitialize, $permissionPrefix, $singularLabel){
                $status = $row->statusInfo?->name ?? 'de-active';
                // pass $row as 'user' for the partial
                $row->agent = view('back-office.partials.avatar', ['user' => $row])->render();
                $row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                                    . strtoupper($status) .
                                    '</span>';

                $author = $row->author ?? '';
                $row->author_id = view('back-office.partials.avatar', ['user' => $author])->render();
                $row->action = view('back-office.partials.action-buttons', [
                    'model' => $row,
                    'permissionPrefix' => $permissionPrefix,
                    'routeInitialize' => $routeInitialize,
                    'singularLabel' => $singularLabel,
                ])->render();

                // Role - first role name from Spatie roles
                $row->role = $row->getRoleNames()[0] ?? 'N/A';

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
        $statuses = $this->status->where('model', 'User')->get();
        $roles = $this->roleRepo->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(UserRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $payload['role'] = 'Agent';
            $response = $this->userRepo->storeModel($payload);

            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit(User $user)
    {
        $statuses = $this->status->where('model', 'User')->get();
        $model = $this->userRepo->showModel($user);
        $roles = $this->roleRepo->get();
        return (string) view($this->pathInitialize.'.edit_content', get_defined_vars());
    }

    public function update(UserRequest $request, User $user)
    {
        $payload = $request->validated();
        try {
            $this->userRepo->updateModel($user, $payload);
            return successResponse([], $this->singularLabel. ' updated successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(User $user)
    {
        $model = $this->userRepo->showModel($user);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(User $user)
    {
        try {
            if($this->userRepo->softDeleteModel($user)) {
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

    public function restore(User $user)
    {
        try {
            if($this->userRepo->restoreModel($user)) {
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

    public function forceDelete(User $user)
    {
        try {
            if ($this->userRepo->permanentlyDeleteModel($user)) {
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
            $this->userRepo->bulkDelete();
            return redirect()->route(strtolower('users.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->userRepo->bulkRestore();
            return redirect()->route(strtolower('users.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}