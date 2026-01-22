<!-- @extends('layouts.app') -->
<x-app-layout>
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
</x-app-layout>

@section('content')
<h2 class="text-xl font-bold mb-4">Create User</h2>

<form method="POST" class="bg-white p-6 rounded shadow w-1/2">
    @csrf

    <input name="name" placeholder="Full Name"
           class="w-full border p-2 mb-3" required>

    <input name="email" type="email" placeholder="Email"
           class="w-full border p-2 mb-3" required>

    <select name="department_id" class="w-full border p-2 mb-3" required>
        <option value="">Select Department</option>
        @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
        @endforeach
    </select>

    <input name="password" type="password"
           placeholder="Temporary Password"
           class="w-full border p-2 mb-3" required>

    <button class="bg-blue-700 text-white px-4 py-2 rounded">
        Create User
    </button>
</form>
@endsection
