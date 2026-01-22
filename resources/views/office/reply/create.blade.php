@extends('layouts.app')

@section('content')
<h2 class="text-xl font-bold mb-4">
    Reply to: {{ $document->title }}
</h2>

<form method="POST"
      action="{{ route('office.documents.reply', $document) }}"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow space-y-4">

    @csrf

    <div>
        <label class="block">Title</label>
        <input name="title" class="w-full border p-2" required>
    </div>

    <div>
        <label class="block">Attachments</label>
        <input type="file" name="files[]" multiple>
    </div>

    <button class="bg-blue-700 text-white px-4 py-2 rounded">
        Submit Reply
    </button>
</form>
@endsection
