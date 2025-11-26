<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Repositories\Eloquent\UserRepository;
use App\Modules\User\Http\Requests\UserRequest;
use App\Modules\User\Models\User;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index(Request $request)
    {
        $title = 'Users List';
        $models = $this->userRepo->getAll();

        // if($request->ajax() && $request->loaddata == "yes") {
        //     return DataTables::of($models)
        //         ->addIndexColumn()
        //         ->addColumn('avatar', function($model){
        //             $src = $model->avatar && $model->avatar->path
        //                 ? asset('back-office/assets/' . $model->avatar->path)
        //                 : asset('back-office/assets/img/avatars/' . rand(1, 10) . '.png');

        //             return '<img class="rounded-circle" src="' . $src . '" width="36" height="36" alt="Avatar">';
        //         })
        //         ->addColumn('name', function($model){
        //             return $model->name;
        //         })
        //         ->addColumn('email', function($model){
        //             return $model->email;
        //         })
        //         ->addColumn('phone', function($model){
        //             return $model->phone;
        //         })
        //         ->addColumn('status', function($model){
        //             return $model->status;
        //         })
        //         ->addColumn('created_at', function($model){
        //             return getDateTimeFormat($model->created_at);
        //         })
        //         ->addColumn('action', function($model){
        //             return 'action';
        //         })
        //         ->rawColumns(['action', 'avatar'])
        //         ->make(true);
        // }

        $columns = [
            'avatar' => ['label'=>'Avatar', 'orderable'=>false, 'searchable'=>false],
            'name' => ['label'=>'Name'],
            'email' => ['label'=>'Email'],
            'phone' => ['label'=>'Phone'],
            'status' => ['label'=>'Status'],
            'created_at' => ['label'=>'Created'],
            'action' => ['label'=>'Action', 'orderable'=>false, 'searchable'=>false],
        ];

        $dataTableService = (new \App\Services\DataTableService($models))
            ->setColumns($columns)
            ->setRawColumns(['avatar','action']);

        if($request->ajax() && $request->loaddata == "yes") {
            return $dataTableService->handle($request, function($user){
                // Use optional() to safely access relationship
                $src = optional($user->avatar)->path
                    ? asset('back-office/assets/' . $user->avatar->path)
                    : asset('back-office/assets/img/avatars/' . rand(1,10) . '.png');

                $user->avatar = '<img class="rounded-circle" width="36" height="36" src="'.$src.'">';
                $user->created_at = $user->created_at;
                $user->action = '<button class="btn btn-sm btn-primary">Edit</button>';
                return $user;
            });
        }
        return view(strtolower('back-office.users.index'), get_defined_vars());
    }

    public function create()
    {
        return view('back-office.users.create');
    }

    public function store(UserRequest $request)
    {
        $payload = $request->validated();
        try {
            $this->userRepo->storeModel($payload);
            return redirect()->route(strtolower('User.index'))->with('success', 'User created successfully.');
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
        $user = $this->userRepo->showModel($id);
        return view('users.show', compact('user'));
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