<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Modules\User\Repositories\Eloquent\UserRepository;
use App\Modules\User\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepo;
    protected $roleRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = new Role();
    }

    public function index(Request $request)
    {
        $title = 'Agents List';
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
            rowFormatter: function($row){
                // pass $row as 'user' for the partial
                $row->agent = view('back-office.partials.avatar', ['user' => $row])->render();
                $row->action = view('back-office.partials.action-buttons', ['module' => 'users', 'model' => $row])->render();
                $row->status = view('back-office.partials.status-badge', ['status' => $row->statusInfo?->name])->render();

                // Role - first role name from Spatie roles
                $row->role = $row->getRoleNames()[0] ?? 'N/A';

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view('back-office.users.index', get_defined_vars());
    }

    public function create()
    {
        $title = 'Add Agent';
        $roles = $this->roleRepo->get();
        return (string) view('back-office.users.create_content', get_defined_vars());
    }

    public function store(UserRequest $request)
    {
        $payload = $request->validated();
        
        try {
            $payload['role'] = 'Agent';
            $response = $this->userRepo->storeModel($payload);

            return successResponse($response, 'Agent registered successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = $this->userRepo->showModel($id);
        return view('back-office.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $payload = $request->validated();
        try {
            $this->userRepo->updateModel($user, $payload);
            return redirect()->route(strtolower('User.index'))->with('success', 'User updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $model = $this->userRepo->showModel($id);
        return (string) view('back-office.users.show_content', get_defined_vars());
    }

    public function destroy($id)
    {
        try {
            $this->userRepo->softDeleteModel($id);
            return redirect()->route(strtolower('users.index'))->with('success', 'User deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $this->userRepo->restoreModel($id);
            return redirect()->route(strtolower('users.index'))->with('success', 'User restored successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->userRepo->permanentlyDeleteModel($id);
            return redirect()->route(strtolower('users.index'))->with('success', 'User permanently deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
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