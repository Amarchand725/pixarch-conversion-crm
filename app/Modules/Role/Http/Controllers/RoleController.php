<?php

namespace App\Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Role\Repositories\Eloquent\RoleRepository;
use App\Modules\Role\Http\Requests\RoleRequest;
use App\Modules\Role\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    public function index(Request $request)
    {
        $title = 'Roles List';
        $columns = [
            'name'       => ['label' => 'Role Name'],
            'guard_name'      => ['label' => 'Guard Name'],
            'created_at' => ['label' => 'Created'],
            'action'     => ['label' => 'Action', 'html' => true],
        ];

        // Get query builder from repository (perfect for DataTables)
        $query = $this->roleRepo->getAll();

        $dataTable = new \App\Services\DataTableService(
            model: $query,
            columns: $columns,
            rowFormatter: function($row){
                // pass $row as 'model' for the partial
                $row->action = view('back-office.partials.action-buttons', ['model' => $row])->render();

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower('back-office.roles.index'), get_defined_vars());
    }

    public function create()
    {
        return view('backOffice.roles.create');
    }

    public function store(RoleRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->roleRepo->storeModel($payload);
            return redirect()->route(strtolower('Role.index'))->with('success', 'Role created successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $role = $this->roleRepo->showModel($id);
        return view('backOffice.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $payload = $request->validated();
        try {
            $this->roleRepo->updateModel($role, $payload);
            return redirect()->route(strtolower('Role.index'))->with('success', 'Role updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $role = $this->roleRepo->showModel($id);
        return view('roles.show', compact('role'));
    }

    public function destroy($id)
    {
        try {
            $this->roleRepo->softDeleteModel($id);
            return redirect()->route(strtolower('roles.index'))->with('success', 'Role deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $this->roleRepo->restoreModel($id);
            return redirect()->route(strtolower('roles.index'))->with('success', 'Role restored successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->roleRepo->permanentlyDeleteModel($id);
            return redirect()->route(strtolower('roles.index'))->with('success', 'Role permanently deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkDelete()
    {
        try {
            $this->roleRepo->bulkDelete();
            return redirect()->route(strtolower('roles.index'))->with('success', 'Bulk delete successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function bulkRestore()
    {
        try {
            $this->roleRepo->bulkRestore();
            return redirect()->route(strtolower('roles.index'))->with('success', 'Bulk restore successful.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}