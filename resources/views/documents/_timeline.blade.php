@props(['logs'])
<div class="space-y-3">
    @foreach($logs as $log)
        <div class="flex gap-3">
            <div class="w-2 h-2 mt-2 bg-sky-400 rounded-full"></div>
            <div>
                <p class="text-sm text-gray-700 font-medium">{{ $log->action }}</p>
                <p class="text-xs text-gray-400">
                    {{ $log->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
    @endforeach
</div>
