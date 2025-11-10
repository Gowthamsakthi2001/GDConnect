@if($logs->count())
    <div class="timeline-wrapper position-relative">
        @foreach($logs->sortByDesc('created_at')->values() as $index => $log)
        
        @php
            $isManager = false;
            $userType = '';
            $userLabel = '';
            $sender ='Unknown';
            $updatesText = $updates->firstWhere('id',  $log->updates_id)->label_name ?? 'No Updates';
            $image = 'b2b/img/default_profile_img.png';
            if ($log->user_type === 'recovery-manager-dashboard') {
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
                $image = 'uploads/users/' . ($log->recovery_user->profile_photo_path ?? ''); 
                $isManager = ($log->user_id == $manager->id);
                $sender = $log->recovery_user->name ?? 'Unknown';
            }elseif($log->user_type === 'b2b-admin-dashboard'){
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
            }elseif($log->user_type === 'b2b-web-dashboard'){
                $userType = 'Customer';
                $image = '';
                $sender = $customers->firstWhere('id', $log->recovery_user->customer_id)->name ?? 'Unknown';
                
            }elseif($log->user_type === 'b2b-customer'){
                $userType = 'Customer';
                $image = '';
                $sender = $customers->firstWhere('id', $log->recovery_user->customer_id)->name ?? 'Unknown';
                
            }
            elseif($log->user_type === 'recovery-agent'){
                $userType = 'Agent';
                $image = 'EV/images/photos/' . ($log->recovery_user->photo ?? '');
                $sender = $log->recovery_user->first_name .' '. $log->recovery_user->last_name?? 'Unknown';
            }
            else{
                $sender = 'Unknown';
                $userType = 'Unknown';
                $image = '';
            }

            // Status color mapping
            $statusColors = [
                 'assigned' => '#6610f2',
                'in_progress' => '#ffc107',        // Yellow
                'reached_location' => '#17a2b8',   // Teal / Info Blue
                'revisited_location' => '#0dcaf0', // Light Blue
                'recovered' => '#28a745',          // Green
                'not_recovered' => '#dc3545',      // Red
                'vehicle_handovered' => '#6f42c1', // Purple
                'hold' => '#fd7e14',               // Orange
                'closed' => '#343a40',             // Dark Gray / Neutral
            ];
            
            $statusLabel = [
                 'assigned' => 'Assigned',
                'in_progress' => 'In Progress',
                'reached_location' => 'Location Reached',
                'revisited_location' => 'Location Revisited',
                'recovered' => 'Recovered',
                'not_recovered' => 'Not Recovered',
                'vehicle_handovered' => 'Vehicle Handovered',
                'hold' => 'Hold',
                'closed' => 'Closed',
            ];
            
            $status =$log->status ?? null;
            $statusColor = $statusColors[$log->status] ?? null;
        @endphp
        
            <div class="timeline-step animate__animated animate__fadeInUp" 
                 style="animation-delay: {{ $index * 0.3 }}s;">
                 
                <!-- Animated Circle -->
                <div class="timeline-icon d-flex align-items-center justify-content-center step-{{ $index % 4 }}">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <!-- Log Card -->
                <div class="timeline-content ms-4 p-3 rounded shadow-sm bg-white">
                    <div class="d-flex justify-content-between ">
                        <!-- Left side: Image + sender + user type -->
                        <div class="d-flex align-items-center">
                            <!-- Profile Image -->
                            <div>
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
                            </div>
                
                            <!-- Sender & User Type -->
                            <div class="ms-3">
                                
                                <div class="fw-bold text-dark">{{ $sender }}</div>
                                <div class="text-muted small">{{ $userType }}</div>
                            </div>
                        </div>
                
                        <!-- Right side: Created At -->
                        <div class="text-end">
                            @if($status)
                            <div class="fw-bold" style="color:{{$statusColors[$status] ?? '#ffc107'}}">{{ $statusLabel[$status] ?? '' }}</div>
                            @endif
                            <small class="text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</small>
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
                            <td class="text-dark">{{ $log->comments ?: 'No Comments' }}</td>
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
