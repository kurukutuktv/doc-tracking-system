@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-md">
    <h1 class="font-bold mb-4">Create Role</h1>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <input name="name" placeholder="Role name"
               class="border px-3 py-2 w-full mb-3">

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</div>
@endsection
