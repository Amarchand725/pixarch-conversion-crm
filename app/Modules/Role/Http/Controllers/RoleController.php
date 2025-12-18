<?php

namespace App\Modules\Role\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Permission;
use App\Modules\Role\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Modules\Role\Repositories\Contracts\RoleContract;
use Exception;
use Illuminate\Http\Request;

class RoleController extends BaseModuleController
{
    protected $permissionModel;

    public function __construct(
        protected RoleContract $roleRepo
    ){
        $this->permissionModel = new Permission();
        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function index(Request $request)
    {
        $columns = [
            'name'       => ['label' => 'Role Name', 'searchable' => 'name'],
            'guard_name'      => ['label' => 'Guard Name', 'searchable' => 'guard_name'],
            'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
            'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
        ];

        // Get query builder from repository (perfect for DataTables)
        $query = $this->roleRepo->getAll();
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
        // pass $row as 'model' for the partial
        $row->action = view('back-office.partials.actions', data: 
        [
            'model' => $row,
            'permissionPrefix' => $this->permissionPrefix,
            'routeInitialize'  => $this->routePrefix,
            'singularLabel'    => $this->singularLabel,
        ])->render(); 

        return $row;
    }

    public function create()
    {
        $title = $this->pluralLabel;
        $models = $this->permissionModel->orderby('id','DESC')->groupBy('label')->get();
        return view($this->pathInitialize.'.create', get_defined_vars());
    }

    public function store(RoleRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->roleRepo->storeModel($payload);
            return redirect()->route('back-offices.roles.index')->with('success', value: module_message('created', $this->singularLabel));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Role $role)
    {
        $title = 'Edit '.$this->singularLabel;
        $role = $this->roleRepo->showModel($role);
        $permissions = $this->permissionModel->orderby('id','DESC')->groupBy('label')->get();
        return view($this->pathInitialize.'.edit', get_defined_vars());
    }

    public function update(RoleRequest $request, Role $role)
    {
        $payload = $request->validated();
        
        try {
            $this->roleRepo->updateModel($role, $payload);
            return redirect()->route('back-office.roles.index')->with('message', value: module_message('updated', $this->singularLabel));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage()); 
        }
    }

    public function show(Role $role)
    {
        $model = $this->roleRepo->showModel($role);
        $permissions = $model->permissions()->pluck('name')->toArray();
        $groupedPermissions = groupPermissions($permissions);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }
}