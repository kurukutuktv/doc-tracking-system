@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <div class="bg-white shadow rounded p-5 space-y-4 max-w-3xl mx-auto">

        <h1 class="text-2xl font-bold">{{ $document->title }}</h1>

        <p class="text-gray-600">{{ $document->description }}</p>

        <div class="flex gap-4">
            <p><strong>Status:</strong>
                @include('documents._status', ['status' => $document->status])
            </p>

            <p><strong>Tracking #:</strong> {{ $document->tracking_number }}</p>
        </div>

        @if($document->file_path)
        <a href="{{ Storage::url($document->file_path) }}" 
           target="_blank"
           class="text-blue-600 underline">
            View Attached File
        </a>
        @endif


        {{-- APPROVAL BUTTONS --}}
        @php
            $role = optional(auth()->user()->roles->first())->name;
        @endphp

        @if($role === 'approver'.$document->approver_level && $document->status !== 'completed' && $document->status !== 'rejected')
            <div class="flex gap-3 mt-4">

                {{-- Approve --}}
                <form action="{{ route('documents.approve', $document->id) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">
                        Approve
                    </button>
                </form>

                {{-- Reject --}}
                <form action="{{ route('documents.reject', $document->id) }}" method="POST">
                    @csrf
                    <button class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded shadow">
                        Reject
                    </button>
                </form>

            </div>
        @endif

        <hr class="my-4">

        <h2 class="text-xl font-bold mb-2">Timeline</h2>
        @include('documents._timeline', ['logs' => $document->logs ?? []])

    </div>

</div>
@endsection
