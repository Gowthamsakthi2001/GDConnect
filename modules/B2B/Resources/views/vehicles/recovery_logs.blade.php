@if($logs->count())
    <div class="timeline-wrapper position-relative">
        @foreach($logs->sortByDesc('created_at')->values() as $index => $log)
            @php
                // Default values
                $userType = 'Agent';
                $sender = 'Unknown';
                $image = asset('b2b/img/default_profile_img.png');
                $updatesText = $updates->firstWhere('id',  $log->updates_id)->label_name ?? 'No Updates';
                 
                // Identify user type and image based on log->type
                switch ($log->type) {
                    case 'recovery-manager-dashboard':
                        if ($log->recovery_user->role  == 1) {
                            $userType = 'Super Admin';
                        } elseif ($log->recovery_user->role == 13) {
                            $userType = 'Admin';
                        }elseif ($log->recovery_user->role == 23) {
                            $userType = 'Recovery Manager';
                        }
                        else {
                           $roleName = $roles->firstWhere('id', $log->recovery_user->role)->name ?? 'Unknown Role';
                            // Capitalize properly (optional)
                            $userType = ucwords($roleName);
                        }
                        $sender = $log->recovery_user->name ?? 'Unknown';
                        if (!empty($log->recovery_user->profile_photo_path)) {
                            $image = asset('uploads/users/' . $log->recovery_user->profile_photo_path);
                        }
                        break;
                    case 'b2b-admin-dashboard':
                        if ($log->recovery_user->role == 1) {
                            $userType = 'Super Admin';
                        } elseif ($log->recovery_user->role == 13) {
                            $userType = 'Admin';
                        } else {
                            $roleName = $roles->firstWhere('id', $log->recovery_user->role)->name ?? 'Unknown Role';
                            // Capitalize properly (optional)
                            $userType = ucwords($roleName);
                        }
                        $sender = $log->recovery_user->name ?? 'Unknown';
                        $image = 'uploads/users/' . ($log->recovery_user->profile_photo_path ?? '');
                        break;
                    case 'b2b-web-dashboard':
                        $userType = 'Customer';
                        $image = '';
                        $sender = $customers->firstWhere('id', $log->recovery_user->customer_id)->name ?? 'Unknown';
                        // no image defined for this type
                        break;
                        
                    case 'b2b-customer':
                        $userType = 'Customer';
                        $image = '';
                        $sender = $customers->firstWhere('id', $log->recovery_user->customer_id)->name ?? 'Unknown';
                        // no image defined for this type
                        break;
                        
                    case 'recovery-agent':
                        $userType = 'Agent';
                        $image = 'EV/images/photos/' . ($log->recovery_user->photo ?? '');
                        $sender = $log->recovery_user->first_name .' '. $log->recovery_user->last_name?? 'Unknown';
                        break;
                    default:
                        $userType = 'Unknown';
                        $sender = 'Unknown';
                        $image = '';
                        
                        break;
                }

                // Status color mapping
                $statusColors = [
                    'opened' => '#dc3545',
                    'assigned' => '#6610f2',
                    'in_progress' => '#ffc107',
                    'rider_contacted' => '#007bff',
                    'reached_location' => '#17a2b8',
                    'revisited_location' => '#0dcaf0',
                    'recovered' => '#28a745',
                    'not_recovered' => '#dc3545',
                    'vehicle_handovered' => '#6f42c1',
                    'hold' => '#fd7e14',
                    'closed' => '#343a40',
                ];

                $statusLabel = [
                    'opened' => 'Opened',
                    'assigned' => 'Assigned',
                    'in_progress' => 'In Progress',
                    'rider_contacted' => 'Follow-up Call',
                    'reached_location' => 'Reached Location',
                    'revisited_location' => 'Revisited Location',
                    'recovered' => 'Recovered',
                    'not_recovered' => 'Not Recovered',
                    'vehicle_handovered' => 'Vehicle Handovered',
                    'hold' => 'Hold',
                    'closed' => 'Closed',
                ];

                $status = $log->status ?? null;
                $statusColor = $statusColors[$status] ?? '#6c757d';
                $statusText = $statusLabel[$status] ?? ucfirst(str_replace('_', ' ', $status));
            @endphp

            <div class="timeline-step animate__animated animate__fadeInUp" 
                 style="animation-delay: {{ $index * 0.3 }}s;">

                <!-- Animated Circle -->
                <div class="timeline-icon d-flex align-items-center justify-content-center step-{{ $index % 4 }}">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <!-- Log Card -->
                <div class="timeline-content ms-4 p-3 rounded shadow-sm bg-white">
                    <div class="d-flex justify-content-between">
                        <!-- Left: User Info -->
                        <div class="d-flex align-items-center">
                            @if(!empty($image))
                                    <img src="{{ asset($image) }}" 
                                         alt="Profile Image" 
                                         class="rounded-circle" 
                                         style="width:50px; height:50px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('b2b/img/default_profile_img.png') }}" 
                                         alt="Profile Image" 
                                         class="rounded-circle" 
                                         style="width:50px; height:50px; object-fit:cover;">
                                @endif
                            <div class="ms-3">
                                <div class="fw-bold text-dark">{{ $sender }}</div>
                                <div class="text-muted small">{{ $userType }}</div>
                            </div>
                        </div>

                        <!-- Right: Status & Date -->
                        <div class="text-end">
                            @if($status)
                                <div class="fw-bold" style="color: {{ $statusColor }}">
                                    {{ $statusText }}
                                </div>
                            @endif
                            <small class="text-muted">
                                {{ $log->created_at->format('d M Y, h:i A') }}
                            </small>
                        </div>
                    </div>

                    <!-- Comment Section -->
                    <table class="table table-sm table-borderless mt-3 w-auto">
                        <tr>
                            <th class="text-secondary">Updates:</th>
                            <td class="text-dark">{{ $updatesText ?: 'No updates' }}</td>
                        </tr>
                        <tr>
                            <th class="text-secondary">Comments:</th>
                            <td class="text-dark">{{ $log->remarks ?: 'No Comments' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
        No logs found.
    </div>
@endif
