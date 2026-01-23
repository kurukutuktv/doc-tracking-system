<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{

    // public function __construct()
    // {
    //     $this->authorizeResource(User::class, 'user');
    // }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['department', 'roles'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('last_name', 'like', "%{$request->search}%")
                        ->orWhere('first_name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->when($request->role, function ($q) use ($request) {
                $q->whereHas('roles', function ($q) use ($request) {
                    $q->where('id', $request->role);
                });
            })
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', [
            'departments' => Department::all(),
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'password'      => 'required|min:8',
        ]);

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'department_id' => $request->department_id,
            'password'      => Hash::make($request->password),
        ]);

        // ✅ AUTO-ASSIGN ROLE
        $user->assignRole('office');

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created and assigned as Office.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', User::class);

        return view('admin.users.edit', [
            'user' => $user,
            'departments' => Department::all(),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('viewAny', User::class);

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:8',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'password' => $request->password
                ? Hash::make($request->password)
                : $user->password,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);

        if ($user->id === Auth::user()->id) {
            return back()->withErrors('You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
