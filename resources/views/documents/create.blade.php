@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-xl">

    <h1 class="text-xl font-bold mb-4">Create Document</h1>

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white shadow rounded p-4 space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-semibold">Title</label>
            <input type="text" name="title" class="border rounded px-3 py-2 w-full"
                value="{{ old('title') }}" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" rows="3" class="border rounded px-3 py-2 w-full">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block mb-1 font-semibold">File Upload</label>
            <input type="file" name="file" class="border rounded w-full">
        </div>

        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
            Save Document
        </button>
    </form>

</div>
@endsection
