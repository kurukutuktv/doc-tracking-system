@php
$colors = [
    'submitted' => 'bg-blue-100 text-blue-700',
    'information' => 'bg-green-100 text-green-700',
    'rejected' => 'bg-red-100 text-red-700',
];
@endphp

<span class="px-2 py-1 rounded text-sm {{ $colors[$status] ?? 'bg-gray-100' }}">
    {{ ucfirst($status) }}
</span>
