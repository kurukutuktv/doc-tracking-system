<!-- @extends('layouts.app') -->
<x-app-layout>
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
</x-app-layout>

@section('content')
<h1 class="text-2xl font-bold mb-6">Office Dashboard</h1>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-2xl font-bold">{{ $stats['new'] }}</div>
        <div class="text-sm text-gray-600">New Documents</div>
    </div>

    <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-2xl font-bold">{{ $stats['for_reply'] }}</div>
        <div class="text-sm text-gray-600">For Reply</div>
    </div>

    <div class="bg-white p-4 rounded shadow text-center">
        <div class="text-2xl font-bold">{{ $stats['replied'] }}</div>
        <div class="text-sm text-gray-600">Replies Sent</div>
    </div>
</div>

<h2 class="text-xl font-semibold mb-3">Inbox</h2>

<table class="w-full bg-white rounded shadow">
<thead class="bg-gray-200">
<tr>
    <th class="p-2">Title</th>
    <th class="p-2">From</th>
    <th class="p-2">Action</th>
</tr>
</thead>
<tbody>

@foreach($inbox as $doc)
<tr class="border-t">
    <td class="p-2">{{ $doc->title }}</td>
    <td class="p-2">{{ $doc->sender_name }}</td>
    <td class="p-2">
        <a href="{{ route('office.documents.acknowledge', $doc) }}"
           class="text-blue-600 underline">
            Open
        </a>
    </td>
</tr>
@endforeach

</tbody>
</table>
@endsection
