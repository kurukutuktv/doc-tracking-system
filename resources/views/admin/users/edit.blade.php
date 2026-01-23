<x-app-layout>
    <h2 class="text-xl font-bold mb-4">Edit User</h2>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            <div>
                <label>Name</label>
                <input name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label>Email</label>
                <input name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label>Department</label>
                <select name="department_id" class="w-full border rounded px-3 py-2">
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}"
                            @selected($user->department_id == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Role</label>
                <select name="role_id" class="w-full border rounded px-3 py-2">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            @selected($user->role_id == $role->id)>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>New Password (optional)</label>
                <input type="password" name="password"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Update
                </button>

                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 border rounded">
                    Cancel
                </a>
            </div>

        </div>
    </form>
</x-app-layout>
