@props(['logs'])

<div class="space-y-3">
    @forelse($logs as $log)
        <div class="border-l-4 border-blue-400 pl-3">
            <p class="text-sm font-semibold text-gray-700">{{ $log->action }}</p>
            <p class="text-xs text-gray-500">
                {{ $log->created_at->format('M d, Y h:i A') }}
                • by {{ $log->user->name ?? 'System' }}
            </p>
        </div>
    @empty
        <p class="text-gray-500 text-sm italic">No activity logs yet.</p>
    @endforelse
</div>
