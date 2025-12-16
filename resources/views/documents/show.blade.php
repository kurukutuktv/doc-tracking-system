@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-3xl">
    <div class="bg-white p-4 rounded shadow space-y-4">

        <h1 class="text-2xl font-bold">{{ $document->title }}</h1>

        <p class="text-gray-600">{{ $document->description }}</p>

        <div class="text-sm space-y-1">
            <div>Status: <strong>{{ ucfirst($document->status) }}</strong></div>
            <div>Tracking #: {{ $document->tracking_number }}</div>
            <div>Current Approver:
                <strong>{{ $document->currentApprover?->full_name ?? '—' }}</strong>
            </div>
        </div>

        @if($document->file_path)
        <a href="{{ Storage::url($document->file_path) }}"
            target="_blank" class="text-blue-600 underline">
            Open Attachment
        </a>
        @endif

        {{-- APPROVAL ACTIONS --}}
        @if($document->current_approver_id === auth()->id())
        <div class="flex gap-2 mt-4">
            <form method="POST" action="{{ route('documents.approve', $document->id) }}">
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded">Approve</button>
            </form>

            <form method="POST" action="{{ route('documents.reject', $document->id) }}">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded">Reject</button>
            </form>
        </div>
        @endif
        @if(
        $document->is_memo &&
        !$document->isAcknowledgedBy(auth()->id())
        )
        <hr>
        <form method="POST" action="{{ route('documents.acknowledge', $document->id) }}">
            @csrf
            <button
                class="mt-3 inline-flex items-center gap-2
               bg-emerald-50 text-emerald-700
               border border-emerald-200
               px-4 py-2 rounded-lg
               hover:bg-emerald-100 transition">
                ✔ Acknowledge
            </button>
        </form>
        @endif
        <hr>
        <h2 class="font-semibold">Timeline</h2>
        @foreach($document->logs as $log)
        <div class="text-sm text-gray-600">
            {{ $log->created_at->format('M d, Y H:i') }} —
            {{ $log->action }}
        </div>
        @endforeach

    </div>
</div>
@endsection