@if($logs->isNotEmpty())
    <div class="mt-6">
        <h2 class="font-semibold mb-2">Timeline</h2>
        <ul class="space-y-2">
            @foreach($logs as $log)
                <li class="text-sm text-gray-600">
                    {{ $log->created_at->format('M d, Y H:i') }} —
                    <strong>{{ $log->user->name }}</strong>:
                    {{ $log->action }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
