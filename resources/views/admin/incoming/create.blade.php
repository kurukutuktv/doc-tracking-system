@extends('layouts.app')

@section('content')
<h2 class="text-xl font-bold mb-4">Receive Incoming Document</h2>

<form method="POST"
      action="{{ route('admin.documents.incoming.store') }}"
      enctype="multipart/form-data"
      class="bg-white p-6 rounded shadow space-y-4">

    @csrf

    <div>
        <label class="block">Document Type</label>
        <select name="document_type_id" class="w-full border p-2">
            @foreach($documentTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block">Title</label>
        <input type="text" name="title" class="w-full border p-2" required>
    </div>

    <div>
        <label class="block">Sender</label>
        <input type="text" name="sender_name" class="w-full border p-2" required>
    </div>

    <div>
        <label class="block">Attachments</label>
        <input type="file" name="files[]" multiple class="w-full">
    </div>

    <button class="bg-blue-700 text-white px-4 py-2 rounded">
        Receive Document
    </button>
</form>
@endsection
