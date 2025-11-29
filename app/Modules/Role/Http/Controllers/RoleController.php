<?php

namespace App\Modules\Role\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Modules\Role\Repositories\Eloquent\RoleRepository;
use App\Modules\Role\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class RoleController extends Controller
{
    protected $roleRepo;
    protected $permissionModel;

    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;
    protected $permissionPrefix;
    protected $prefix;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
        $this->permissionModel = new Permission();

        $this->prefix = Str::kebab('Role');
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
            rowFormatter: function($row) use ($routeInitialize, $permissionPrefix, $singularLabel){
                // pass $row as 'model' for the partial
                $row->action = view('back-office.partials.action-buttons', data: 
                [
                    'model' => $row,
                    'permissionPrefix' => $permissionPrefix,
                    'routeInitialize' => $routeInitialize,
                    'singularLabel' => $singularLabel,
                ])->render();

                return $row;
            }
        );

        if ($request->ajax() && $request->loaddata == "yes") {
            return $dataTable->ajax();
        }

        return view(strtolower($this->pathInitialize.'.index'), get_defined_vars());
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
            return redirect()->route(strtolower('Role.index'))->with('success', 'Role created successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $title = $this->pluralLabel;
        $role = $this->roleRepo->showModel($id);
        $permissions = $this->permissionModel->orderby('id','DESC')->groupBy('label')->get();
        return view($this->pathInitialize.'.edit', get_defined_vars());
    }

    public function update(RoleRequest $request, Role $role)
    {
        $payload = $request->validated();
        
        try {
            $this->roleRepo->updateModel($role, $payload);
            return redirect()->route(strtolower('role.index'))->with('success', 'Role updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $model = $this->roleRepo->showModel($id);
        $permissions = $model->permissions()->pluck('name')->toArray();
        $groupedPermissions = groupPermissions($permissions);
        return (string) view($this->pathInitialize.'.show_content', get_defined_vars());
    }
}