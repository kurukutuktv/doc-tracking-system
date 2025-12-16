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
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </span>
            New Document
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <form method="GET" class="flex flex-wrap gap-3 mb-4">
            <select name="department_id"
                class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}"
                    @selected(request('department_id')==$dept->id)>
                    {{ $dept->name }}
                </option>
                @endforeach
            </select>

            <select name="status"
                class="border rounded-lg px-3 py-2 text-sm">
                <option value="">All Status</option>
                @foreach(['pending','in_review','completed','rejected','information'] as $st)
                <option value="{{ $st }}" @selected(request('status')==$st)>
                    {{ ucfirst(str_replace('_',' ',$st)) }}
                </option>
                @endforeach
            </select>

            <button class="px-4 py-2 bg-slate-100 rounded-lg text-sm">
                Filter
            </button>
        </form>

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Tracking #</th>
                    <th class="px-4 py-3 text-left">Title</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Department</th>
                    <th class="px-4 py-3 text-left">Next Approver</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date Created</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($documents as $doc)
                <tr class="hover:bg-slate-50 transition">
                    {{-- Tracking --}}
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                        {{ $doc->tracking_number }}
                    </td>

                    {{-- Title --}}
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $doc->title }}
                    </td>

                    {{-- Type --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ $doc->is_memo ? 'Memo' : 'For Approval' }}
                    </td>

                    {{-- Department --}}
                    <td class="px-4 py-3 text-gray-600">
                        {{ $doc->department?->name ?? 'All Departments' }}
                    </td>

                    {{-- Next Approver --}}
                    <td class="px-4 py-3 text-gray-600">
                        @if($doc->is_for_approval && $doc->currentApprover)
                        {{ $doc->currentApprover->full_name }}
                        @elseif($doc->is_for_approval)
                        <span class="text-gray-400 italic">Completed</span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3">
                        @include('documents._status', ['status' => $doc->status])
                    </td>
                
                    {{-- Date Created --}}
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $doc->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-400">
                            {{ $doc->created_at->format('h:i A') }}
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('documents.show', $doc->id) }}"
                            class="text-sky-600 hover:text-sky-800 text-sm font-medium">
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