@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Documents</h1>

        <a href="{{ route('documents.create') }}"
           class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded">
            + New Document
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Tracking #</th>
                    <th class="px-3 py-2 text-left">Title</th>
                    <th class="px-3 py-2 text-left">Type</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Current Approver</th>
                    <th class="px-3 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $doc->tracking_number }}</td>
                    <td class="px-3 py-2">{{ $doc->title }}</td>
                    <td class="px-3 py-2">
                        {{ $doc->is_memo ? 'Memo' : 'For Approval' }}
                    </td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-1 rounded text-xs
                            @if($doc->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($doc->status === 'in_review') bg-blue-100 text-blue-700
                            @elseif($doc->status === 'completed') bg-green-100 text-green-700
                            @elseif($doc->status === 'rejected') bg-red-100 text-red-700
                            @endif">
                            {{ ucfirst(str_replace('_',' ', $doc->status)) }}
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        {{ $doc->currentApprover?->full_name ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-center">
                        <a href="{{ route('documents.show', $doc->id) }}"
                           class="text-blue-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">
                        No documents found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
