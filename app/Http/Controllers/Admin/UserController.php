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
        $this->middleware(['auth', 'role:admin']);
    }

    /* ---------- LIST ---------- */

    public function index(Request $request)
    {
        $roles = Role::all(); // ✅ THIS IS MISSING RIGHT NOW

        $users = User::with('role')
            ->when($request->role, function ($query) use ($request) {
                $query->where('role_id', $request->role);
            })
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles, // ✅ MUST BE PASSED
        ]);
    }


    /* ---------- CREATE ---------- */

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:20',
            'post_nominals' => 'nullable|string|max:255',

            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);
        $fullName = trim(
            "{$request->last_name}, {$request->first_name} {$request->middle_name} {$request->name_extension} {$request->post_nominals}"
        );

        $user = User::create([
            'name' => $fullName,
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'post_nominals' => $validated['post_nominals'] ?? null,

            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $role = Role::find($validated['role']);
        $user->assignRole($role);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /* ---------- EDIT ---------- */

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'name_extension' => 'nullable|string|max:20',
            'post_nominals' => 'nullable|string|max:255',

            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user->update([
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'post_nominals' => $validated['post_nominals'] ?? null,
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->name = trim(
                "{$request->last_name}, {$request->first_name} {$request->middle_name} {$request->name_extension} {$request->post_nominals}"
            );

            $user->save();
        }

        $role = Role::find($validated['role']);
        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /* ---------- DELETE ---------- */

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
