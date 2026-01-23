@php
    $user = Auth::user();
    $isAdmin = $user->department && $user->department->code === 'ADM';
@endphp

@extends('layouts.app')
@section('content')

<h1 class="text-2xl font-bold mb-6">{{ $user->name }}</h1>
<div class="grid grid-cols-5 gap-4 mb-6">
    @foreach([
    'Incoming Today' => $stats['incoming_today'],
    'Unlogged' => $stats['unlogged'],
    'Forwarded' => $stats['forwarded'],
    'Outgoing Pending' => $stats['outgoing_pending'],
    'Archived' => $stats['archived']
    ] as $label => $count)

    <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-2xl font-bold">{{ $count }}</div>
        <div class="text-sm text-gray-600">{{ $label }}</div>
    </div>

    @endforeach
</div>

<h2 class="text-xl font-semibold mb-3">Action Queue</h2>

<table class="w-full bg-white rounded shadow">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-2">Title</th>
            <th class="p-2">Status</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>

        @foreach($actionQueue as $doc)
        <tr class="border-t">
            <td class="p-2">{{ $doc->title }}</td>
            <td class="p-2">{{ $doc->status->name }}</td>
            <td class="p-2">
                <a href="{{ route('admin.documents.log', $doc) }}"
                    class="text-blue-600 underline">
                    Process
                </a>
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
@endsection