@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    {{-- Page Header --}}
    <h1 class="text-2xl font-bold mb-4">Create User</h1>

    {{-- Success / Error Alerts --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 transition-opacity duration-500">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" class="border px-2 py-1 w-full" value="{{ old('name') }}">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="border px-2 py-1 w-full" value="{{ old('email') }}">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="border px-2 py-1 w-full">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Password Confirmation</label>
            <input type="password" name="password_confirmation" class="border px-2 py-1 w-full">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Role</label>
            <select name="role" class="border px-2 py-1 w-full">
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded shadow">
            Save User
        </button>
    </form>
</div>
@endsection
