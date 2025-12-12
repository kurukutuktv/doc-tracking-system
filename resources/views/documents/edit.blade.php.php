@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-xl">

    <h1 class="text-xl font-bold mb-4">Edit Document</h1>

    <form action="{{ route('documents.update', $document->id) }}" 
          method="POST" enctype="multipart/form-data"
          class="bg-white shadow rounded p-4 space-y-4">

        @csrf
        @method('PUT')

        <div>
            <label class="block mb-1 font-semibold">Title</label>
            <input type="text" name="title" class="border rounded px-3 py-2 w-full"
                value="{{ old('title', $document->title) }}" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Description</label>
            <textarea name="description" rows="3" class="border rounded px-3 py-2 w-full">
                {{ old('description', $document->description) }}
            </textarea>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Replace File (optional)</label>
            <input type="file" name="file" class="border rounded w-full">
        </div>

        <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Update Document
        </button>

    </form>

</div>
@endsection
