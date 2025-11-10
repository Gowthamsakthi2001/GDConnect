
<x-app-layout>
    <style>
      .form-switch {
            padding-left: 2.5em !important;
        }
    </style>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
                <img src="{{asset('admin-assets/icons/custom/api_settings.jpg')}}" class="img-fluid rounded"><span class="ps-2">Mobitra API Settings</span>
            </h2>
        </div>
        <!-- Content Row -->
        @php
            $settings = \App\Models\EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
            $bg_color = isset($settings['API_CLUB_MODE']) && $settings['API_CLUB_MODE'] == 1 ? 'bg-success' : 'bg-warning';
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row">
                            <div class="border border-light-subtle p-2 col-md-4 col-6">
                               <div class="form-check form-switch">
                                    <label class="toggle-switch" for="api_club_mode">
                                        <input type="checkbox" class="form-check-input toggle-btn fs-6"
                                            id="api_club_mode" name="api_club_mode"
                                            {{ isset($settings['API_CLUB_MODE']) && $settings['API_CLUB_MODE'] == '1' ? 'checked' : '' }} onchange="updateApiClubMode(this,event)">
                                            API Club Mode <span class="text-danger">*</span>
                                        <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                                    </label>
                                </div>
                           </div>
                           <div class="col-md-4 col-6">
                               <span class="badge {{$bg_color}} p-2 mt-2">{{ isset($settings['API_CLUB_MODE']) && $settings['API_CLUB_MODE'] == 1 ? 'Production Mode Enabled' : 'Test Environment Enabled' }}</span>
                             </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.mobitra_api.mobitra_api_settings_update')}}" method="post" class="row g-3 p-3">
                            @csrf
                                
                                <div class="col-md-4 col-12">
                                    <label class="input-label mb-2 ms-1" for="base_url">{{ __('User Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="user_name" id="user_name" class="form-control" 
                                            value="{{ $settings['USER_NAME'] ?? '' }}" 
                                            @if(isset($settings['USER_NAME'])) readonly @endif>
                                </div>
                                <div class="col-md-4 col-12">
                                    <label class="input-label mb-2 ms-1" for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                               value="{{ $settings['PASSWORD'] ?? '' }}" 
                                               @if(isset($settings['PASSWORD'])) readonly @endif>
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="base_url">{{ __('Base Url') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="base_url" id="base_url" class="form-control"  value="{{ $settings['BASE_URL'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="production_url">{{ __('Production Url') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="production_url" id="production_url" class="form-control" value="{{ $settings['API_MOBITRA_PRODUCTION'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="test_url">{{ __('Test Url') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="test_url" id="test_url" class="form-control" value="{{ $settings['API_MOBITRA_TEST'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="authenticate_endpoint">{{ __('Authenticate Endpoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="authenticate_endpoint" id="authenticate_endpoint" class="form-control" 
                                        value="{{ $settings['AUTHENTICATE_ENDPOINT'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="get_user_by_id_endpoint">{{ __('Get User by Id Endpoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="get_user_by_id_endpoint" id="get_user_by_id_endpoint" class="form-control"
                                        value="{{ $settings['GET_USER_BY_ID_ENDPOINT'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="get_user_list_endpoint">{{ __('Get User List EndPoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="get_user_list_endpoint" id="get_user_list_endpoint" class="form-control" value="{{ $settings['GET_USER_LIST_ENDPOINT'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="vw_role_based_imei_endpoint">{{ __('VW Role Based IMEI EndPoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="vw_role_based_imei_endpoint" id="vw_role_based_imei_endpoint" class="form-control" 
                                        value="{{ $settings['VW_ROLE_BASED_IMEI_ENDPOINT'] ?? '' }}">
                                </div>
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="fleet_tracking_endpoint">{{ __('Fleet Tracking EndPoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="fleet_tracking_endpoint" id="fleet_tracking_endpoint" class="form-control"
                                        value="{{ $settings['FLEET_TRACKING_ENDPOINT'] ?? '' }}">
                                </div>
                                
                                <div class="col-md-4 col-12">
                                        <label class="input-label mb-2 ms-1" for="fleet_notification_endpoint">{{ __('Fleet Notification EndPoint') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="fleet_notification_endpoint" id="fleet_notification_endpoint" class="form-control"
                                        value="{{ $settings['FLEET_NOTIFICATION_ENDPOINT'] ?? '' }}">
                                </div>
    
                            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
        
                                <button type="submit" class="btn btn-success btn-round">Save Changes</button>
                            </div>
                            
                        </form>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('script_js')
<script>
   function updateApiClubMode(checkbox) {
        let isChecked = checkbox.checked ? 1 : 0;
        let confirmation = confirm("Are you sure you want to update API Mode?");
        
        if (!confirmation) {
            // Revert checkbox if user cancels
            checkbox.checked = !checkbox.checked;
            return;
        }

        fetch("", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ api_club_mode: isChecked })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("API Club Mode updated successfully!");
            } else {
                alert("Failed to update API Club Mode.");
                checkbox.checked = !checkbox.checked; // Revert if update fails
            }
        })
        .catch(error => {
            console.error("Error:", error);
            // alert("Something went wrong!");
            checkbox.checked = !checkbox.checked;
        });
    }
    
     function updateApiClubMode(checkbox,e) {
        e.preventDefault();
        if (checkbox.checked) {
            var message = "Are you sure you want to enable Production?";
        } else {
            var message = "Are you sure you want to enable Test Environment?";
        }

        Swal.fire({
            title: '',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
               let isChecked = checkbox.checked ? 1 : 0;
               let token = "{{csrf_token()}}";
               $.ajax({
                    url: "{{route('admin.mobitra_api.mobitra_api_mode_update')}}",
                    type: 'POST',
                    data: {api_log_mode:isChecked,_token:token},
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload(); 
                            }, 1000);
                        } else {
                            toastr.error(response.message); 
                        }
                    },
                    error: function(xhr, status, error) {
                         toastr.error("The network connection has failed. Please try again.");
                    }
                });
            }else{
                checkbox.checked = !checkbox.checked;
            }
        });
    }
</script>
@endsection
</x-app-layout>
