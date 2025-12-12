@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Documents</h1>

        <a href="{{ route('documents.create') }}"
            class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded shadow flex items-center gap-2">

            <div class="w-6 h-6 bg-white rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </div>

            Add Document
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded p-4 overflow-auto">
        <table class="min-w-full border-collapse w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-3 py-2 text-left">Tracking #</th>
                    <th class="border px-3 py-2 text-left">Title</th>
                    <th class="border px-3 py-2 text-left">Status</th>
                    <th class="border px-3 py-2 text-left">Created By</th>
                    <th class="border px-3 py-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2">{{ $doc->tracking_number }}</td>
                    <td class="border px-3 py-2">{{ $doc->title }}</td>
                    <td class="border px-3 py-2">
                        @include('documents._status', ['status' => $doc->status])
                    </td>
                    <td class="border px-3 py-2">{{ $doc->creator->name ?? 'Unknown' }}</td>

                    <td class="border px-3 py-2">
                        <div class="flex justify-center gap-2">

                            {{-- Show --}}
                            <a href="{{ route('documents.show', $doc->id) }}"
                                class="text-blue-500 hover:text-blue-700 w-8 h-8 flex items-center justify-center rounded">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Edit (Creator/Admin only) --}}
                            @can('update', $doc)
                            <a href="{{ route('documents.edit', $doc->id) }}"
                                class="text-gray-500 hover:text-gray-700 w-8 h-8 flex items-center justify-center rounded">
                                <svg class="w-5 h-5" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                                </svg>
                            </a>
                            @endcan

                            {{-- Delete --}}
                            @can('delete', $doc)
                            <form action="{{ route('documents.destroy', $doc->id) }}" method="POST"
                                onsubmit="return confirm('Delete this document?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    class="text-rose-400 hover:text-rose-600 w-8 h-8 flex items-center justify-center rounded">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M9 3a1 1 0 0 0-1 1v1H4.5a.75.75 0 0 0 0 1.5h.82l.93 
                                            12.04A2.25 2.25 0 0 0 8.49 21h7.02a2.25 2.25 
                                            0 0 0 2.24-2.46l-.93-12.04h.82a.75.75 
                                            0 0 0 0-1.5H16V4a1 1 0 0 
                                            0-1-1H9Z" />
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>

                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-4">No documents found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
