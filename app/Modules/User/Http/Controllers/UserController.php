<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Repositories\Eloquent\UserRepository;
use App\Modules\User\Http\Requests\UserRequest;
use App\Modules\User\Models\User;
use Exception;

class UserController extends Controller
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        $title = 'Users List';
        $users = $this->userRepo->getAll();
        return view(strtolower('backOffice.users.index'), get_defined_vars());
    }

    public function create()
    {
        return view('backOffice.users.create');
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
        return view('backOffice.users.edit', compact('user'));
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