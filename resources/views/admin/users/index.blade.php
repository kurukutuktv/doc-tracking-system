<x-app-layout>
    <div class="container mx-auto p-4">

        <h1 class="text-2xl font-bold mb-4">Users Management</h1>

        @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="flex gap-3 mb-4">
            <input name="search" value="{{ request('search') }}"
                placeholder="Search name or email"
                class="border px-3 py-2 rounded w-64">

            <select name="role" onchange="this.form.submit()"
                class="border px-3 py-2 rounded">
                <option value="">All Roles</option>
                @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    @selected(request('role')==$role->id)>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>

            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-500 text-white px-4 py-2 rounded">
                Add User
            </a>
        </form>

        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">ID</th>
                    <th class="border px-3 py-2">Name</th>
                    <th class="border px-3 py-2">Email</th>
                    <th class="border px-3 py-2">Role</th>
                    <th class="border px-3 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="border px-3 py-2">{{ $user->id }}</td>
                    <td class="border px-3 py-2">{{ $user->name }}</td>
                    <td class="border px-3 py-2">{{ $user->email }}</td>
                    <td class="border px-3 py-2">
                        @if ($user->roles->isNotEmpty())
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">
                            {{ $user->roles->first()->name }}
                        </span>
                        @else
                        <span class="text-gray-500 italic">No role</span>
                        @endif
                    </td>
                    <td class="border px-3 py-2 flex justify-center gap-2">
                        @can('update', $user)
                        <a href="{{ route('admin.users.edit', $user) }}"
                            class="text-blue-600">Edit</a>
                        @endcan
                        @can('delete', $user)
                        <form method="POST"
                            action="{{ route('admin.users.destroy', $user) }}"
                            onsubmit="return confirm('Delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->withQueryString()->links() }}
        </div>

    </div>
</x-app-layout>