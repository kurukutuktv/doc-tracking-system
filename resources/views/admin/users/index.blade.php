@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Users Management</h1>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 transition-opacity duration-500">
        {{ session('success') }}
    </div>
    @endif

    {{-- Header + Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-3 w-full">

        {{-- Filters --}}
        <form id="filter-form" method="GET" class="flex flex-col md:flex-row md:items-center gap-3 w-full md:w-auto">

            {{-- Search --}}
            <input type="text" name="search" id="search"
                placeholder="Search by name or email"
                value="{{ request('search') }}"
                class="border border-gray-300 rounded-md px-3 py-2 w-full md:w-64
                   focus:outline-none focus:ring-2 focus:ring-blue-400 transition">

            {{-- Role Filter --}}
            <select name="role" id="role"
                class="border border-gray-300 rounded-md px-3 py-2 w-full md:w-40
                   focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>

        </form>

        {{-- Add User Button --}}
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex justify-center items-center gap-2 bg-blue-400 hover:bg-blue-300
           text-white font-medium px-4 py-2 rounded-md shadow-sm transition
           text-sm md:text-base whitespace-nowrap">
            Add User
            {{-- White Circular Icon --}}
            <span class="w-6 h-6 flex items-center justify-center bg-white rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" class="icon-tabler icon-tabler-plus  hover:bg-gray-100"
                    stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </span>
        </a>
    </div>
    {{-- Users Table --}}
    <div class="bg-white shadow rounded p-4 overflow-x-auto">
        <table class="min-w-full border-collapse w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-3 py-2 text-left">ID</th>
                    <th class="border px-3 py-2 text-left">Name</th>
                    <th class="border px-3 py-2 text-left">Email</th>
                    <th class="border px-3 py-2 text-left">Role</th>
                    <th class="border px-3 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2">{{ $user->id }}</td>
                    <td class="border px-3 py-2">{{ $user->full_name }}</td>
                    <td class="border px-3 py-2">{{ $user->email }}</td>
                    <td class="border px-3 py-2">
                        @if($user->roles->count() > 0)
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">
                            {{ $user->roles->first()->name }}
                        </span>
                        @else
                        <span class="text-gray-500 italic">No role</span>
                        @endif
                    </td>
                    <td class="border px-3 py-2 flex justify-center gap-2 items-center">
                        {{-- Edit --}}
                        <a href="{{ route('admin.users.edit', $user->id) }}" title="Edit"
                            class="text-gray-400 hover:text-gray-600 transition flex items-center justify-center w-8 h-8 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5"
                                viewBox="0 0 24 24">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.42l-2.34-2.34a1 1 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                            </svg>
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button title="Delete"
                                class="text-rose-400 hover:text-rose-600 transition flex items-center justify-center w-8 h-8 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M9 3a1 1 0 0 0-1 1v1H4.5a.75.75 0 0 0 0 1.5h.82l.93 12.04A2.25 2.25 0 0 0 8.49 21h7.02a2.25 2.25 0 0 0 2.24-2.46l-.93-12.04h.82a.75.75 0 0 0 0-1.5H16V4a1 1 0 0 0-1-1H9Zm1 2V4h4v1H10Zm-.75 4.25a.75.75 0 0 1 .75.75v7a.75.75 0 0 1-1.5 0v-7a.75.75 0 0 1 .75-.75Zm6 0a.75.75 0 0 1 .75.75v7a.75.75 0 0 1-1.5 0v-7a.75.75 0 0 1 .75-.75Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4 flex justify-center justify-items-center">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Auto Filter Script --}}
<script>
    const filterForm = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const roleSelect = document.getElementById('role');

    let typingTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => filterForm.submit(), 500);
    });

    roleSelect.addEventListener('change', () => filterForm.submit());
</script>
@endsection