@extends('layouts.app')

@section('content')
<h2 class="text-xl font-bold mb-4">
    {{ $document->title }}
</h2>

<p><strong>Status:</strong> {{ $document->status->name }}</p>
<p><strong>Control #:</strong> {{ $document->control_number ?? '—' }}</p>

<hr class="my-4">

<h3 class="font-semibold mb-2">Attachments</h3>

<div class="grid grid-cols-3 gap-4">
@foreach($document->attachments as $attachment)

    <div class="border p-2 rounded">

        <p class="text-sm">{{ $attachment->original_name }}</p>

        @if(Str::startsWith($attachment->mime_type, 'image/'))
            <img src="{{ asset('storage/'.$attachment->file_path) }}"
                 class="w-full h-32 object-cover mt-2">
        @else
            <a href="{{ route('attachments.preview', $attachment) }}"
               target="_blank"
               class="text-blue-600 underline">
               Preview PDF
            </a>
        @endif

        <div class="flex justify-between mt-2">
            <a href="{{ route('documents.download', $document) }}"
               class="text-sm text-green-600">
               Download
            </a>

            <form method="POST"
                  action="{{ route('attachments.destroy', $attachment) }}">
                @csrf
                @method('DELETE')
                <button class="text-red-600 text-sm">Delete</button>
            </form>
        </div>
    </div>

@endforeach
</div>

@if(!$document->control_number)
<form method="POST"
      action="{{ route('admin.documents.log', $document) }}"
      class="mt-6">
    @csrf
    <button class="bg-green-700 text-white px-4 py-2 rounded">
        Log & Stamp
    </button>
</form>
@endif

@endsection
