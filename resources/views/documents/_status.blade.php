@php
$map = [
    'pending' => 'bg-yellow-100 text-yellow-700',
    'in_review' => 'bg-blue-100 text-blue-700',
    'completed' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
    'information' => 'bg-gray-100 text-gray-600',
];
@endphp

<span class="px-2 py-1 rounded text-xs font-medium {{ $map[$status] ?? 'bg-gray-100 text-gray-600' }}">
    {{ ucfirst(str_replace('_',' ', $status)) }}
</span>