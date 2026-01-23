@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Documents</h1>
        {{-- Create (Office only) --}}
        @can('create', App\Models\Document::class)
            <a href="{{ route('documents.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + New Document
            </a>
        @endcan
    </div>

    @if($documents->isEmpty())
        <div class="text-gray-500 text-center py-6">
            No documents found.
        </div>
    @else
        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">Tracking #</th>
                    <th class="p-2">Title</th>
                    <th class="p-2">Status</th>
                    <th class="p-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr class="border-t">
                        <td class="p-2">{{ $document->tracking_number }}</td>
                        <td class="p-2">{{ $document->title }}</td>
                        <td class="p-2">
                            @include('documents.status', ['status' => $document->status])
                        </td>
                        <td class="p-2 flex justify-center gap-2">

                            {{-- View --}}
                            @can('view', $document)
                                <a href="{{ route('documents.show', $document) }}"
                                   class="text-blue-600">View</a>
                            @endcan

                            {{-- Delete (Admin only) --}}
                            @can('delete', $document)
                                <form method="POST"
                                      action="{{ route('documents.destroy', $document) }}"
                                      onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    @endif
</div>
@endsection
