@extends('layouts.app')

@section('content')
<h2 class="text-xl font-bold mb-4">Office Inbox</h2>

<table class="w-full bg-white shadow rounded">
<thead class="bg-gray-200">
<tr>
    <th class="p-2">Title</th>
    <th class="p-2">From</th>
    <th class="p-2">Action</th>
</tr>
</thead>
<tbody>

@foreach($documents as $document)
<tr class="border-t">
    <td class="p-2">{{ $document->title }}</td>
    <td class="p-2">{{ $document->sender_name }}</td>
    <td class="p-2">
        <form method="POST"
              action="{{ route('office.documents.acknowledge', $document) }}">
            @csrf
            <button class="text-blue-600 underline">
                Acknowledge
            </button>
        </form>
    </td>
</tr>
@endforeach

</tbody>
</table>
@endsection
