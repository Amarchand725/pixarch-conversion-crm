<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Status;
use Spatie\Permission\Models\Role;
use App\Modules\User\Http\Requests\UserRequest;
use App\Models\User;
use App\Modules\User\Repositories\Contracts\UserContract;
use App\Services\PhoneNumberService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $columns = [
            'agent'     => ['label' => 'Agent', 'html' => true, 'searchable' => 'name'],
            'role'       => ['label' => 'Role', 'searchable' => 'roles.name'],
            'phone'      => ['label' => 'Phone', 'searchable' => 'phone'],
            'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        // Get query builder from repository (perfect for DataTables)
        $query = $this->userRepo->getAll()->with(['avatar', 'roles', 'statusInfo']); // eager-load status
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
        $extraActions = [];
        $status = $row->statusInfo?->name ?? 'de-active';
        // pass $row as 'user' for the partial
        $row->agent = view('back-office.partials.avatar', ['user' => $row])->render();
        $row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass($status) .'">'
                            . strtoupper($status) .
                            '</span>';

        //Adding extra custom actions
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

        // Role - first role name from Spatie roles
        $row->role = $row->getRoleNames()[0] ?? 'N/A';

        return $row;
    }

    public function create()
    {
        $roles = $this->roleRepo->get();
        return (string) view($this->pathInitialize.'.create_content', get_defined_vars());
    }

    public function store(UserRequest $request, PhoneNumberService $phoneService)
    {
        $payload = $request->validated();
        $parsed = $phoneService->parse($request->phone);
        $payload['numeric_code'] = $parsed['numeric_code'];
        $payload['iso_code'] = $parsed['iso_code'];

        try {
            $payload['role'] = 'Agent';
            $response = null;
            DB::transaction(function () use (&$response, $payload) {
                $this->userRepo->storeModel($payload);
            });
            return successResponse($response, module_message('created', $this->singularLabel));
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

    public function update(UserRequest $request, User $user, PhoneNumberService $phoneService)
    {
        $payload = $request->validated();
        $parsed = $phoneService->parse($request->phone);
        $payload['numeric_code'] = $parsed['numeric_code'];
        $payload['iso_code'] = $parsed['iso_code'];

        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $user) {
                $this->userRepo->updateModel($user, $payload);
            });
            return successResponse($response, module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show(User $user)
    {
        $model = $this->userRepo->showModel($user, ['avatar', 'roles', 'statusInfo']);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }

    public function destroy(User $user)
    {
        try {
            if($this->userRepo->softDeleteModel($user)) {
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

    public function restore(User $user)
    {
        try {
            if($this->userRepo->restoreModel($user)) {
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

    public function forceDelete(User $user)
    {
        try {
            if ($this->userRepo->permanentlyDeleteModel($user)) {
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
            $this->userRepo->bulkDelete();
            return redirect()->route('back-office.users.index')->with('success', value: module_message('bulk-deleted', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->userRepo->bulkRestore();
            return redirect()->route('back-office.users.index')->with('success', module_message('bulk-restored', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function editPassword(?User $user)
    {
        $model = $user ? $this->userRepo->showModel($user) : null;
        return (string) view($this->pathInitialize.'.change_password_content', get_defined_vars());
    }

    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8',
        ]);
        
        try {
            $this->userRepo->updateModel($user, ['password' => $request->password]);
            return successResponse([], module_message('changed_password', $this->singularLabel));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}