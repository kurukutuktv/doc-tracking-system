@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Documents</h1>
        <a href="{{ route('documents.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Add Document</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow rounded p-4 overflow-auto">
        <table class="min-w-full w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-3 py-2 text-left">Tracking #</th>
                    <th class="border px-3 py-2 text-left">Title</th>
                    <th class="border px-3 py-2 text-left">Type</th>
                    <th class="border px-3 py-2 text-left">Status</th>
                    <th class="border px-3 py-2 text-left">Creator</th>
                    <th class="border px-3 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2">{{ $doc->tracking_number }}</td>
                    <td class="border px-3 py-2">{{ $doc->title }}</td>
                    <td class="border px-3 py-2">{{ $doc->is_memo ? 'Memo' : 'Approval' }}</td>
                    <td class="border px-3 py-2">@include('documents._status', ['status' => $doc->status])</td>
                    <td class="border px-3 py-2">{{ $doc->creator->name ?? 'Unknown'}}</td>
                    <td class="border px-3 py-2 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('documents.show', $doc->id) }}" class="text-blue-500">View</a>
                            @can('update', $doc)
                                <a href="{{ route('documents.edit', $doc->id) }}" class="text-gray-600">Edit</a>
                            @endcan
                            @can('delete', $doc)
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-500">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">No documents found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
