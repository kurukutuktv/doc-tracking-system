@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">New Document</h1>

    <form method="POST"
          action="{{ route('documents.store') }}"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf

        <input name="title" placeholder="Title" class="w-full border p-2" required>

        <textarea name="description" placeholder="Description"
                  class="w-full border p-2"></textarea>

        <select name="doc_type" class="w-full border p-2">
            <option value="document">Document</option>
            <option value="memo">Memo</option>
        </select>

        <input type="file" name="file">

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Submit
        </button>
    </form>
</div>
@endsection
