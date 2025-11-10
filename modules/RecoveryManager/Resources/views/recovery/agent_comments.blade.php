@if($logs->count())
    @foreach($logs as $log)
        @php
            $isManager = false;
            $userType = '';
            $userLabel = '';

            if ($log->user_type === 'recovery-manager-dashboard') {
                if ($manager->role == 1) {
                    $userType = 'Super Admin';
                } elseif ($manager->role == 13) {
                    $userType = 'Admin';
                } else {
                    $userType = 'Recovery Manager';
                }
                $isManager = ($log->user_id == $manager->id);
            }

            // Sender name
            if ($log->user_type === 'recovery-manager-dashboard') {
                $sender = ($manager->id == $log->user_id) ? 'You' : ($log->user->name ?? 'Unknown');
            } else {
                $sender = $log->user->first_name ?? 'Unknown';
            }

            // Status color mapping
            $statusColors = [
                'in_progress' => '#ffc107',
                'pickup_reached' => '#17a2b8',
                'recovered' => '#28a745',
                'not_recovered' => '#dc3545',
                'vehicle_handovered' => '#6f42c1'
            ];
            $statusColor = $statusColors[$log->status] ?? null;

            // Bubble style (alignment and colors)
            $bubbleAlign = $isManager
                ? 'text-end ms-auto bg-primary text-white'
                : 'text-start me-auto bg-light text-dark';
        @endphp

        <div class="d-flex flex-column mb-3 {{ $isManager ? 'align-items-end' : 'align-items-start' }}">
            <div class="p-3 rounded chat-bubble shadow-sm position-relative {{ $bubbleAlign }}" style="max-width: 80%;">

                {{-- Sender Name + (User Type for others only) --}}
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="fw-bold small">{{ $sender }}</div>
                    @if(!$isManager && $userType)
                        <div class="small text-muted fst-italic">{{ $userType }}</div>
                    @endif
                </div>

                {{-- Message --}}
                <div>{{ $log->comments ?? 'No Comments' }}</div>

                {{-- Status (optional) --}}
                @if($log->status)
                    <div class="small mt-2 fw-bold" style="color: {{ $statusColor }}">
                        {{ ucwords(str_replace('_', ' ', $log->status)) }}
                    </div>
                @endif
            </div>

            {{-- Timestamp --}}
            <small class="text-muted mt-1">
                {{ $log->created_at->format('d M Y, h:i A') }}
            </small>
        </div>
    @endforeach
@else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
        No Comments Added Yet.
    </div>
@endif

