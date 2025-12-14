@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-lg">

    <h1 class="text-xl font-bold mb-4">Edit User</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label class="block text-sm mb-1">Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   class="border px-3 py-2 w-full rounded"
                   required>
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="border px-3 py-2 w-full rounded"
                   required>
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm mb-1">
                Password <span class="text-xs text-gray-500">(leave blank to keep current)</span>
            </label>
            <input type="password"
                   name="password"
                   class="border px-3 py-2 w-full rounded">
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm mb-1">Confirm Password</label>
            <input type="password"
                   name="password_confirmation"
                   class="border px-3 py-2 w-full rounded">
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm mb-1">Role</label>
            <select name="role"
                    class="border px-3 py-2 w-full rounded"
                    required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ $user->roles->first()?->id === $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 pt-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update
            </button>

            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 border rounded text-gray-600">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
