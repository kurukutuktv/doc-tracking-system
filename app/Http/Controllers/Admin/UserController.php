<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']); // only admins
    }

    public function index(Request $request)
    {
        $roles = Role::all();

        $users = User::with('roles')
            // Apply search filter
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            // Apply role filter
            ->when($request->role, function ($query, $roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('id', $roleId);
                });
            })
            ->latest()
            ->paginate(10) // paginate AFTER filtering
            ->withQueryString(); // keep query string on pagination links

        return view('admin.users.index', compact('users', 'roles'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roleName = Role::findOrFail($request->role)->name;
        $user->assignRole($roleName);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    // Update
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $roleName = Role::findOrFail($request->role)->name;
        $user->syncRoles([$roleName]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }


    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
