@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">Create Document</h1>

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="block mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="border px-3 py-2 w-full" required>
        </div>

        <div>
            <label class="block mb-1">Description</label>
            <textarea name="description" rows="3" class="border px-3 py-2 w-full">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block mb-1">File (optional)</label>
            <input type="file" name="file" class="w-full">
        </div>

        <div>
            <label class="block mb-1">Document Type</label>
            <select name="doc_type" id="doc_type" class="border px-3 py-2 w-full" required>
                <option value="">Choose type</option>
                <option value="memo" {{ old('doc_type') === 'memo' ? 'selected' : '' }}>Memo / Announcement</option>
                <option value="approval" {{ old('doc_type') === 'approval' ? 'selected' : '' }}>For Approval</option>
            </select>
        </div>

        {{-- Memo options --}}
        <div id="memo-options" class="space-y-2 hidden">
            <label class="block mb-1">Audience</label>
            <select name="audience_type" id="audience_type" class="border px-3 py-2 w-full">
                <option value="all">All</option>
                <option value="users">Specific Users</option>
            </select>

            <div id="audience-users" class="hidden">
                <label class="block mb-1">Select users</label>
                <select name="audience_users[]" multiple class="border px-3 py-2 w-full">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Approval options --}}
        <div id="approval-options" class="space-y-2 hidden">
            <label class="block mb-1">Approval chain (select in order)</label>
            <select name="approval_chain[]" multiple class="border px-3 py-2 w-full">
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500">Select approvers in sequence. First selected is the first approver.</p>
        </div>

        <div class="flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('doc_type').addEventListener('change', function(){
    const value = this.value;
    document.getElementById('memo-options').classList.toggle('hidden', value !== 'memo');
    document.getElementById('approval-options').classList.toggle('hidden', value !== 'approval');
});
document.getElementById('audience_type')?.addEventListener('change', function(){
    document.getElementById('audience-users').classList.toggle('hidden', this.value !== 'users');
});
</script>
@endsection
