@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-3xl">
    <div class="bg-white p-4 rounded shadow space-y-4">
        <h1 class="text-2xl font-bold">{{ $document->title }}</h1>
        <p class="text-gray-600">{{ $document->description }}</p>

        <div class="flex gap-4 items-center">
            <div>Status: @include('documents._status', ['status' => $document->status])</div>
            <div>Tracking #: <strong>{{ $document->tracking_number }}</strong></div>
            <div>Type: {{ $document->is_memo ? 'Memo' : 'For Approval' }}</div>
        </div>

        @if($document->file_path)
            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-600 underline">Open attachment</a>
        @endif

        {{-- Memo: Acknowledge --}}
        @if($document->is_memo)
            @php $ack = $document->acknowledged_by ?? []; @endphp
            @if(!in_array(auth()->id(), $ack))
                <form action="{{ route('documents.acknowledge', $document->id) }}" method="POST" class="inline-block mt-3">
                    @csrf
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Acknowledge</button>
                </form>
            @else
                <div class="text-sm text-gray-600 mt-2">You have acknowledged this memo.</div>
            @endif
        @endif

        {{-- Approval: Approve / Reject --}}
        @if($document->is_for_approval && $document->current_approver_id == auth()->id() && $document->status !== 'completed' && $document->status !== 'rejected')
            <div class="flex gap-2 mt-3">
                <form action="{{ route('documents.approve', $document->id) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Approve</button>
                </form>
                <form action="{{ route('documents.reject', $document->id) }}" method="POST">
                    @csrf
                    <button class="bg-rose-600 text-white px-4 py-2 rounded">Reject</button>
                </form>
            </div>
        @endif

        <hr>
        <h2 class="text-xl font-semibold">Timeline</h2>
        @include('documents._timeline', ['logs' => $document->logs])
    </div>
</div>
@endsection
