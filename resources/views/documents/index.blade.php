@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-7xl">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Documents</h1>
            <p class="text-sm text-gray-500">Manage uploaded documents and approvals</p>
        </div>

        <a href="{{ route('documents.create') }}"
           class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600
                  text-white px-4 py-2 rounded-md text-sm shadow">
            <span class="w-6 h-6 flex items-center justify-center bg-white rounded-full">
                <svg class="w-3 h-3" stroke="black" fill="none" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
            </span>
            New Document
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Tracking #</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Created By</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                        {{ $doc->tracking_number }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $doc->title }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $doc->is_memo ? 'Memo' : 'For Approval' }}
                    </td>
                    <td class="px-4 py-3">
                        @include('documents._status', ['status' => $doc->status])
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $doc->creator?->full_name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('documents.show', $doc->id) }}"
                           class="text-blue-500 hover:text-blue-700 text-sm">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-gray-400">
                        No documents found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(method_exists($documents, 'links'))
    <div class="mt-6 flex justify-center">
        {{ $documents->links() }}
    </div>
    @endif

</div>
@endsection
