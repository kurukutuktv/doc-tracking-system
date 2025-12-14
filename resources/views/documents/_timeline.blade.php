@props(['logs'])
<div class="space-y-2">
    @forelse($logs as $log)
        <div class="border-l-4 border-blue-400 pl-3">
            <p class="text-sm font-semibold">{{ $log->action }}</p>
            <p class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }} • {{ $log->user->name ?? 'System' }}</p>
        </div>
    @empty
        <p class="text-gray-500 italic">No activity yet.</p>
    @endforelse
</div>
