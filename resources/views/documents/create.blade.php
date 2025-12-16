@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-2xl">

    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Create Document</h1>

    <form method="POST" action="{{ route('documents.store') }}"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg shadow space-y-5">
        @csrf

        {{-- Basic Info --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" required
                class="mt-1 w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3"
                class="mt-1 w-full border rounded px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Attachment</label>
            <input type="file" name="file" class="mt-1">
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Document Type</label>
            <select name="doc_type" id="doc_type" required
                class="mt-1 w-full border rounded px-3 py-2">
                <option value="">Select type</option>
                <option value="memo">Memo / Announcement</option>
                <option value="approval">For Approval</option>
            </select>
        </div>

        {{-- Memo Options --}}
        <div id="memo-options" class="hidden space-y-3 border-t pt-4">
            <h3 class="font-medium text-gray-700">Memo Audience</h3>

            <select name="audience_type" id="audience_type"
                class="w-full border rounded px-3 py-2">
                <option value="all">All Users</option>
                <option value="users">Specific Users</option>
            </select>

            <div id="audience-users" class="hidden">
                <select name="audience_users[]" multiple
                    class="w-full border rounded px-3 py-2">
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->full_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Department Selection --}}
        <select name="target_department_id"
            class="border px-3 py-2 w-full rounded" required>

            <option value="">All Departments</option>

            @foreach($departments as $dept)
            <option value="{{ $dept->id }}"
                {{ $dept->id == $userDepartmentId ? 'selected' : '' }}>
                {{ $dept->name }}
            </option>
            @endforeach
        </select>


        {{-- Approval Info --}}
        <div id="approval-options" class=" space-y-3 border-t pt-4">
            <h3 class="font-medium text-gray-700">Approval Routing</h3>

            <div class="text-sm text-gray-600 leading-relaxed">
                This document will follow the <strong>predefined approval hierarchy</strong>
                of the selected department.
            </div>

            <ul class="text-sm text-gray-500 list-disc pl-5">
                <li>Level 1 – Immediate Supervisor</li>
                <li>Level 2 – Department Head</li>
                <li>Level 3 – Final Approver</li>
            </ul>

            <p class="text-xs text-gray-400">
                Approvers are assigned automatically. No manual selection required.
            </p>
        </div>


        {{-- Actions --}}
        <div class="flex justify-end gap-2 pt-4 border-t">
            <a href="{{ route('documents.index') }}"
                class="px-4 py-2 border rounded text-gray-600">
                Cancel
            </a>
            <button class="px-4 py-2 bg-blue-600 text-white rounded">
                Save
            </button>
        </div>
        {{-- Success Message --}}
        @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
    </form>
</div>

{{-- Scripts unchanged --}}
@endsection