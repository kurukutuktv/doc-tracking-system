@props(['type' => 'info'])

@php
$styles = [
    'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'error' => 'bg-rose-50 text-rose-700 border-rose-200',
    'info' => 'bg-sky-50 text-sky-700 border-sky-200',
];
@endphp

<div class="border rounded-lg px-4 py-3 {{ $styles[$type] }}">
    {{ $slot }}
</div>
