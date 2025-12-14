@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Role Management</h1>
        <a href="{{ route('admin.roles.create') }}" class="text-sm text-green-600">
            + New Role
        </a>
    </div>

    @foreach($roles as $role)
        <div class="flex justify-between items-center border-b py-2">
            <span class="capitalize">{{ $role->name }}</span>

            <div class="flex gap-2 text-sm">
                <a href="{{ route('admin.roles.edit', $role) }}" class="text-gray-500">Edit</a>

                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}">
                    @csrf @method('DELETE')
                    <button class="text-rose-500">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
