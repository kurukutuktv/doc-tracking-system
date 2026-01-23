@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-2">{{ $document->title }}</h1>

    <div class="mb-4">
        @include('documents.status', ['status' => $document->status])
    </div>

    <p class="mb-4 text-gray-700">
        {{ $document->description }}
    </p>

    @if($document->file_path)
        <a href="{{ Storage::url($document->file_path) }}"
           target="_blank"
           class="text-blue-600 underline">
            View Attachment
        </a>
    @endif

    <div class="flex gap-3 mt-6">

        {{-- Acknowledge (Admin + Memo) --}}
        @can('acknowledge', $document)
            <form method="POST" action="{{ route('documents.acknowledge', $document) }}">
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    Acknowledge
                </button>
            </form>
        @endcan

        {{-- Reject (Admin only) --}}
        @can('reject', $document)
            <form method="POST" action="{{ route('documents.reject', $document) }}">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Reject
                </button>
            </form>
        @endcan
    </div>

    {{-- Timeline --}}
    @include('documents._timeline', ['logs' => $document->logs])

</div>
@endsection
