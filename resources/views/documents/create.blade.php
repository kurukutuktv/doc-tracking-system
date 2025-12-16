@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">Create Document</h1>

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white p-4 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="block mb-1">Title</label>
            <input name="title" class="border px-3 py-2 w-full" required>
        </div>

        <div>
            <label class="block mb-1">Description</label>
            <textarea name="description" rows="3" class="border px-3 py-2 w-full"></textarea>
        </div>

        <div>
            <label class="block mb-1">Attachment</label>
            <input type="file" name="file">
        </div>

        <div>
            <label class="block mb-1">Document Type</label>
            <select name="doc_type" id="doc_type" class="border px-3 py-2 w-full" required>
                <option value="">Select</option>
                <option value="memo">Memo / Announcement</option>
                <option value="approval">For Approval</option>
            </select>
        </div>

        {{-- MEMO --}}
        <div id="memo-options" class="hidden space-y-2">
            <label>Audience</label>
            <select name="audience_type" class="border px-3 py-2 w-full">
                <option value="all">All Users</option>
                <option value="users">Specific Users</option>
            </select>
        </div>

        {{-- APPROVAL --}}
        <div id="approval-options" class="hidden space-y-2">
            <label>Target Department</label>
            <select name="target_department_id" class="border px-3 py-2 w-full">
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}"
                        @selected($dept->id == $userDepartmentId)>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>

            <p class="text-xs text-gray-500">
                Approval will follow the department’s predefined hierarchy.
            </p>
        </div>

        <div class="flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('documents.index') }}" class="border px-4 py-2 rounded">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('doc_type').addEventListener('change', e => {
    document.getElementById('memo-options').classList.toggle('hidden', e.target.value !== 'memo');
    document.getElementById('approval-options').classList.toggle('hidden', e.target.value !== 'approval');
});
</script>
@endsection
