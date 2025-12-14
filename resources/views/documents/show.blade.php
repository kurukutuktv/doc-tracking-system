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
        {{-- Approval Flow Info --}}
        @if($document->is_for_approval)
        <div class="mt-4">
            <h3 class="font-semibold text-lg mb-2">Approval Flow</h3>

            @php
            $steps = [];
            if ($document->current_approver_id) {
            $steps[] = $document->current_approver;
            }
            if (is_array($document->next_approver_ids)) {
            foreach ($document->next_approver_ids as $uid) {
            $steps[] = \App\Models\User::find($uid);
            }
            }
            @endphp

            <ol class="space-y-2 text-sm">
                @foreach($steps as $index => $approver)
                <li class="flex items-center gap-2">
                    <span class="w-5 h-5 flex items-center justify-center rounded-full
                        {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        {{ $index + 1 }}
                    </span>

                    <span>
                        {{ $index === 0 ? 'Current Approver:' : 'Next Approver:' }}
                        <strong>{{ $approver->name ?? 'Unknown' }}</strong>
                    </span>
                </li>
                @endforeach
            </ol>

            @if($document->status === 'completed')
            <div class="text-green-600 text-sm mt-2">
                ✔ Fully approved
            </div>
            @endif

            @if($document->status === 'rejected')
            <div class="text-rose-600 text-sm mt-2">
                ✖ Document rejected
            </div>
            @endif
        </div>
        @endif

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