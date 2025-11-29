<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Modules\User\Repositories\Eloquent\UserRepository;
use App\Modules\User\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class UserController extends Controller
{
    protected $userRepo;
    protected $roleRepo;

    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected $permissionPrefix;
    protected $prefix;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = new Role();

        $this->prefix = Str::kebab('User');
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

        $columns = [
            'agent'     => ['label' => 'Agent', 'html' => true],
            'role'       => ['label' => 'Role'],
            'phone'      => ['label' => 'Phone'],
            'status'     => ['label' => 'Status', 'html' => true],
            'created_at' => ['label' => 'Created'],
            'action'     => ['label' => 'Action', 'html' => true],
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

        return view($this->pathInitialize.'.index', get_defined_vars());
    }

    public function create()
    {
        $title = $this->pluralLabel;
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

    public function edit($id)
    {
        $title = $this->pluralLabel;
        $model = $this->userRepo->showModel($id);
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

    public function show($id)
    {
        $model = $this->userRepo->showModel($id);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy($id)
    {
        try {
            if($this->userRepo->softDeleteModel($id)) {
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

    public function restore($id)
    {
        try {
            if($this->userRepo->restoreModel($id)) {
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

    public function forceDelete($id)
    {
        try {
            if ($this->userRepo->permanentlyDeleteModel($id)) {
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