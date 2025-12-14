@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    {{-- Page Header --}}
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

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

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" class="border px-2 py-1 w-full" value="{{ old('name', $user->name) }}">
        </div> -->
        {{-- Name Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-1">Last Name</label>
                <input type="text" name="last_name"
                    class="border px-2 py-1 w-full"
                    value="{{ old('last_name', $user->last_name) }}" required>
            </div>

            <div>
                <label class="block mb-1">First Name</label>
                <input type="text" name="first_name"
                    class="border px-2 py-1 w-full"
                    value="{{ old('first_name', $user->first_name) }}" required>
            </div>

            <div>
                <label class="block mb-1">Middle Name</label>
                <input type="text" name="middle_name"
                    class="border px-2 py-1 w-full"
                    value="{{ old('middle_name', $user->middle_name) }}">
            </div>

            <div>
                <label class="block mb-1">Name Extension</label>
                <select name="name_extension" class="border px-2 py-1 w-full">
                    <option value="">None</option>
                    @foreach(['Jr.', 'Sr.', 'II', 'III', 'IV'] as $ext)
                    <option value="{{ $ext }}"
                        {{ old('name_extension', $user->name_extension) === $ext ? 'selected' : '' }}>
                        {{ $ext }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-1">Post-nominal Titles</label>
                <input type="text" name="post_nominals"
                    class="border px-2 py-1 w-full"
                    value="{{ old('post_nominals', $user->post_nominals) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="border px-2 py-1 w-full" value="{{ old('email', $user->email) }}">
        </div>

        <div class="mb-4">
            <label class="block mb-1">Password (leave blank to keep current)</label>
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
                <option value="{{ $role->id }}"
                    @if(old('role', $user->roles->first()?->id) == $role->id) selected @endif>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow">
            Update User
        </button>
    </form>
</div>
@endsection