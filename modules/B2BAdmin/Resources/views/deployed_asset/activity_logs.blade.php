@if($logs->count())
    <div class="timeline-wrapper position-relative">
        @foreach($logs->sortByDesc('created_at')->values() as $index => $log)
            <div class="timeline-step animate__animated animate__fadeInUp" 
                 style="animation-delay: {{ $index * 0.3 }}s;">
                 
                <!-- Animated Circle -->
                <div class="timeline-icon d-flex align-items-center justify-content-center step-{{ $index % 4 }}">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <!-- Log Card -->
                <div class="timeline-content ms-4 p-3 rounded shadow-sm bg-white">
                    <div class="fw-bold text-dark fs-6">
                        {{ $log->remarks ?? 'No remarks' }}
                    </div>
                    <small class="text-muted">
                        {{ $log->created_at->format('d M Y, h:i A') }}
                    </small>
                </div>
            </div>
        @endforeach
    </div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
    No activity logs found.
</div>
@endif
