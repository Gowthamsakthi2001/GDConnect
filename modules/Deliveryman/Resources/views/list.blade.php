<x-app-layout>
       <style>
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 25px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-switch-label {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 50px;
            transition: background-color 0.3s ease;
        }

        .toggle-switch-indicator {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        input:checked + .toggle-switch-label {
            background-color: #4CAF50; /* Green when active */
        }

        input:checked + .toggle-switch-label .toggle-switch-indicator {
            transform: translateX(26px); /* Move the indicator to the right */
        }

    </style>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/deliveryman.jpg')}}" class="img-fluid rounded"><span class="ps-2">List of Rider</span>
                    </div>
                    <a href="{{ route('admin.Green-Drive-Ev.delivery-man.create') }}" class="btn custom-btn-primary btn-sm">
                            <i class="fa fa-plus-circle"></i>&nbsp;
                            Add Rider
                        </a>
               </div>
            </h2>
        </div>
        <!-- End Page Header -->
        
        <div class="tile">
                <div class="card mb-4">
                    <div class="card-header p-0 m-0"></div>
                    <div class="card-body">
                        <div class="row mb-3">
                             <div class="col-md-4">
                                 <label>Select City</label>
                                <select id="current_city_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{$city->id == $city_id ? 'selected' : ''}}>{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           <div class="col-md-4">
                               <label>Select Zone</label>
                                <select id="zone_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select Zone</option>
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}" {{$zone->id == $zone_id ? 'selected' : ''}}>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="col-md-4">
                                <label>Select Client</label>
                                <select id="client_id" class="form-control custom-select2-field" onchange="DmExportFiler()">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{$client->id == $client_id ? 'selected' : ''}}>{{ $client->client_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                        </div>
                        <div class="row mb-3 ">
                            <div class="col-12 d-flex justify-content-end">
                                   <a class="btn btn-round me-1 btn-md px-4 btn-dark all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'all']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'all', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> All
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'pending']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'pending', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Pending
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'approve']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'approve', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Approved
                                    </a>
                                    
                                    <a class="btn btn-dark btn-round btn-md me-1 all-filter-export-btn"
                                       data-baseurl="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'deny']) }}"
                                       href="{{ route('admin.Green-Drive-Ev.delivery-man.export_deliveryman_verify_list', ['type' => 'deny', 'city_id' => $city_id, 'zone_id' => $zone_id, 'client_id' => $client_id]) }}">
                                        <i class="bi bi-download"></i> Rejected
                                    </a>

                             </div>
                                  
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table custom-table text-center" style="width: 100%;">
                                <thead class="bg-success rounded">
                                    <tr>
                                        <th scope="col" class="text-white">#</th>
                                        <th scope="col" class="text-white">Image</th>
                                        <th scope="col" class="text-white">Deliveryman Name</th>
                                        <th scope="col" class="text-white">GDM ID</th>
                                        <th scope="col" class="text-white">Email ID</th>
                                        <th scope="col" class="text-white">Mobile Number</th>
                                        <th scope="col" class="text-white">Role</th>
                                        <th scope="col" class="text-white">City</th>
                                        <th scope="col" class="text-white">Zone</th>
                                        <th scope="col" class="text-white">Client Name</th>
                                        <th scope="col" class="text-white">Hub Name</th>
                                        <th scope="col" class="text-white">Rider Status</th>
                                        <th scope="col" class="text-white">Last Login Date</th>
                                        <th scope="col" class="text-white">Adhar Verified</th>
                                        <th scope="col" class="text-white">Pan Verified</th>
                                        <th scope="col" class="text-white">Bank Verified</th>
                                        <th scope="col" class="text-white">License Verified</th>
                                        <th scope="col" class="text-white">Action</th>
                                    </tr>
                                </thead>
                
                                <tbody class="bg-white border border-white">
                                    @if(isset($lists))
                                        @foreach($lists as $key => $val)
                                         <?php
                                         $full_name = ($val->first_name ?? '').' '.($val->last_name ?? '');
                                         $roll_type = '';
                                         if($val->work_type == 'deliveryman'){
                                             $roll_type = 'Rider';
                                         }
                                         else if($val->work_type == 'in-house'){
                                             $roll_type = 'Employee';
                                         }
                                         else if($val->work_type == 'adhoc'){
                                             $roll_type = 'Adhoc';
                                         }
                                         
                                         $image = $val->photo ? asset('public/EV/images/photos/'.$val->photo) : asset('public/admin-assets/img/person.png');
                                         
                                         $hub = \Modules\Clients\Entities\ClientHub::where('id',$val->hub_id)->where('client_id',$val->client_id)->first();
                                            $hub_name = '';
                                            if($hub){
                                                $hub_name = $hub->hub_name;
                                            }else{
                                                $hub_name = '-';
                                            }
                                       ?>
                                         <tr>
                                            <td>{{$key+1}}</td>
                                            <td>
                                                <div onclick="Profile_Image_View('{{$image}}')">
                                                    <img src="{{$image}}" alt="Image" class="profile-image">
                                                </div>
                                            </td>
                                             <td>{{$full_name}}</td>
                                             <td>{{$val->emp_id ?? '-'}}</td>
                                             <td>{{$val->email ?? ''}}</td>
                                             <td>{{$val->mobile_number}}</td>
                                             <td>{{$roll_type}}</td>
                                             <td>{{$val->current_city->city_name ?? ''}}</td>
                                             <td>{{$val->zone->zone_name ?? '-'}}</td>
                                             <td>{{$val->client->client_name ?? '-'}}</td>
                                             <td>{{$hub_name}}</td>
                                             
                                             <?php
                                                $isChecked = $val->rider_status ? 'checked' : '';
                                                $toggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$val->id, $val->rider_status ? 0 : 1]);
                                                $toggleText = $val->rider_status ? 'Deactivate' : 'Activate';
                                             ?>
                                            
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label class="toggle-switch" for="statusCheckbox_{{ $val->id }}">
                                                        <input type="checkbox"
                                                               onclick="status_change_alert('{{ $toggleStatusUrl }}', '{{ $toggleText }} this Deliveryman?', event)"
                                                               class="form-check-input toggle-btn"
                                                               id="statusCheckbox_{{ $val->id }}"
                                                               {{ $isChecked }}>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <?php
                                               $lastPunchIn = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                                                    ->where('user_id', $val->id)
                                                    ->orderBy('punched_in', 'desc')
                                                    ->first();
                                                
                                                $last_login = '<span class="badge bg-secondary">No Login</span>'; // Default
                                                
                                                $city = '';
                                                
                                                if ($lastPunchIn) {
                                                    if (!empty($lastPunchIn->punchin_latitude) && !empty($lastPunchIn->punchin_longitude)) {
                                                        $city = \App\Helpers\CustomHandler::get_punchin_city($lastPunchIn->punchin_latitude, $lastPunchIn->punchin_longitude);
                                                    }
                                                
                                                    $lastPunchInFormatted = \Carbon\Carbon::parse($lastPunchIn->punched_in)->format('d-m-Y H:i:s');
                                                    $daysSinceLastPunch = now()->diffInDays(\Carbon\Carbon::parse($lastPunchIn->punched_in));
                                                
                                                    if ($daysSinceLastPunch >= 3) {
                                                        $last_login = '<span class="badge bg-danger">' . $lastPunchInFormatted . '</span> <span style="font-size: 10px;">' . e($city) . '</span>';
                                                    } else {
                                                        $last_login = '<span class="badge bg-success">' . $lastPunchInFormatted . '</span> <span style="font-size: 10px;">' . e($city) . '</span>';
                                                    }
                                                }
                                            ?>

                                             <td>{!! $last_login !!}</td>
                                             
                                            <?php
                                                $aisChecked = $val->aadhar_verify ? 'checked' : ''; //adhar card
                                                $atoggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$val->id, $val->aadhar_verify ? 0 : 1]); 
                                                $atoggleText = $val->aadhar_verify ? 'Deactivate' : 'Activate';
                                            ?>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label class="toggle-switch" for="adhaar_statusCheckbox_{{ $val->id }}">
                                                        <input type="checkbox"
                                                               class="form-check-input toggle-btn"
                                                               id="adhaar_statusCheckbox_{{ $val->id }}"
                                                               <?= $aisChecked ?>>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            
                                            
                                            <?php
                                                $pisChecked = $val->pan_verify ? 'checked' : ''; //pan card
                                                $ptoggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$val->id, $val->pan_verify ? 0 : 1]); 
                                                $ptoggleText = $val->pan_verify ? 'Deactivate' : 'Activate';
                                            ?>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label class="toggle-switch" for="pan_statusCheckbox_{{ $val->id }}">
                                                        <input type="checkbox"
                                                               class="form-check-input toggle-btn"
                                                               id="pan_statusCheckbox_{{ $val->id }}"
                                                               <?= $pisChecked ?>>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            
                                             <?php
                                                $bisChecked = $val->bank_verify ? 'checked' : ''; //bank card
                                                $btoggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$val->id, $val->bank_verify ? 0 : 1]); 
                                                $btoggleText = $val->bank_verify ? 'Deactivate' : 'Activate';
                                            ?>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label class="toggle-switch" for="bank_statusCheckbox_{{ $val->id }}">
                                                        <input type="checkbox"
                                                               class="form-check-input toggle-btn"
                                                               id="bank_statusCheckbox_{{ $val->id }}"
                                                               <?= $bisChecked ?>>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            
                                             <?php
                                                $dlisChecked = $val->lisence_verify ? 'checked' : ''; //DL card
                                                $dltoggleStatusUrl = route('admin.Green-Drive-Ev.delivery-man.status', [$val->id, $val->lisence_verify ? 0 : 1]); 
                                                $dltoggleText = $val->lisence_verify ? 'Deactivate' : 'Activate';
                                            ?>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <label class="toggle-switch" for="dl_statusCheckbox_{{ $val->id }}">
                                                        <input type="checkbox"
                                                               class="form-check-input toggle-btn"
                                                               id="dl_statusCheckbox_{{ $val->id }}"
                                                               <?= $dlisChecked ?>>
                                                        <span class="toggle-switch-label">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            
                                            <?php
                                                if ($val->delete_status == 1) {
                                                    $dicon = '<i class="fas fa-undo"></i>';
                                                    $dbtnClass = 'btn-dark-soft btn-outline-dark';
                                                    $dbtnText = 'Restore';
                                                } else {
                                                    $dicon = '<i class="fas fa-trash"></i>';
                                                    $dbtnClass = 'btn-danger-soft';
                                                    $dbtnText = 'Delete';
                                                }
                                            ?>
                                            
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('admin.Green-Drive-Ev.delivery-man.preview', $val->id) }}" class="btn btn-warning-soft btn-sm me-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                            
                                                    <a onclick="route_alert_with_input('{{ route('admin.Green-Drive-Ev.delivery-man.whatsapp-message') }}', '{{ $val->mobile_number }}')" class="btn btn-success btn-sm me-1">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                            
                                                    <a href="{{ route('admin.Green-Drive-Ev.delivery-man.edit', $val->id) }}" class="btn btn-success-soft btn-sm me-1">
                                                        <i class="fas fa-pen-to-square"></i>
                                                    </a>
                                            
                                                    <button onclick="route_alert('{{ route('admin.Green-Drive-Ev.delivery-man.delete', $val->id) }}', '{{ $dbtnText }} this Deliveryman')" class="btn {{ $dbtnClass }} btn-sm me-1" title="{{ $dbtnText }}">
                                                        {!! $dicon !!}
                                                    </button>
                                            
                                                    <a href="{{ route('admin.Green-Drive-Ev.delivery-man.zone-asset', $val->id) }}" class="btn btn-primary-soft btn-sm me-1">
                                                        <i class="fas fa-bicycle"></i>
                                                    </a>
                                                </div>
                                            </td>

                                         </tr>
                                        @endforeach
                                    @endif
                
                                </tbody>
                
                            </table>
                        </div>
                        
                    </div>
            </div>
        </div>
        
        <div class="modal fade" id="ProfileImage_srcModal" tabindex="-1" aria-labelledby="ProfileImage_srcModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-end">
                  <button type="button" class="btn btn-dark btn-sm btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="d-flex justify-content-center">
                      <img class="img-fluid" id="profile_image_src" src="">
                  </div>
                </div>
               
              </div>
            </form>
          </div>
        </div>
@section('script_js')
    <script>
    //   document.querySelectorAll('#zone_id, #client_id, #current_city_id').forEach(function(filter) {
    //         filter.addEventListener('change', function() {
    //             let filterName = filter.id;
    //             let filterValue = filter.value;

    //             applyFilter(filterName, filterValue);
    //         });
    //     });


        
    //     function applyFilter() {
    //         const city_id = document.getElementById('current_city_id').value;
    //         const zone_id = document.getElementById('zone_id').value;
    //         const client_id = document.getElementById('client_id').value;
        
    //         const queryParams = new URLSearchParams();
    //         if (city_id) queryParams.append('city_id', city_id);
    //         if (zone_id) queryParams.append('zone_id', zone_id);
    //         if (client_id) queryParams.append('client_id', client_id);
        
    //         const queryString = queryParams.toString();
    //         const url = "{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" + (queryString ? `?${queryString}` : '');
        
    //         console.log("DataTable URL:", url);
        
    //         $('#delivery-man-table').DataTable().ajax.url(url).load();
        
    //         document.querySelectorAll('.all-filter-export-btn').forEach(function (btn) {
    //             const baseUrl = btn.getAttribute('data-baseurl');
    //             const fullUrl = baseUrl + (queryString ? `?${queryString}` : '');
    //             btn.href = fullUrl;
    //             console.log("Updated export URL:", fullUrl);
    //         });
    //     }



    function DmExportFiler(){
       var city_id = $("#current_city_id").val();
       var zone_id = $("#zone_id").val();
       var client_id = $("#client_id").val();
       var url = new URL(window.location.href);
       url.searchParams.set('city_id',city_id);
       url.searchParams.set('zone_id',zone_id);
       url.searchParams.set('client_id',client_id);
       window.location.href = url.toString();
   }
   
    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
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
                location.href = url;
            }
        });    }

     function route_alert(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
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
                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            setTimeout(function() {
                                // location.reload(); 
                                $('#delivery-man-table').DataTable().ajax.reload(null, false); 
                            }, 1000);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }

                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                    }
                });
            }
        });
    }
    
    function route_alert_approve(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
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
                location.href = route;
            }
        });
    }

    
    function route_deny(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            input: 'text', // Input field for remarks
            inputPlaceholder: 'Enter remarks here...',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true,
            preConfirm: (remarks) => {
                if (!remarks) {
                    Swal.showValidationMessage('Remarks are required');
                }
                return remarks;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect with remarks as query parameter
                const encodedRemarks = encodeURIComponent(result.value);
                location.href = `${route}?remarks=${encodedRemarks}`;
            }
        });
    }
    
    function route_alert_with_input(route, number) {
        Swal.fire({
            text: "Please enter your message below:",
            input: 'text', // Adds an input field
            inputPlaceholder: 'Type your message here...',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Send",
            reverseButtons: true,
            preConfirm: (inputValue) => {
                if (!inputValue) {
                    Swal.showValidationMessage('Message cannot be empty!');
                }
                return inputValue;
            }
        }).then((result) => {
            if (result.isConfirmed) {   
                // Send the input value to the backend using AJAX
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        message: result.value,
                        number: number
                    },
                    success: function(response) {
                        console.log(response)
                        if (response.status) {
                            Swal.fire('Success!', 'Message sent successfully!', 'success');
                        } else {
                            Swal.fire('Error!', 'Failed to send the message.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }

    function Profile_Image_View(src){
        $("#ProfileImage_srcModal").modal("show");
        $("#profile_image_src").attr("src",src);
        
    }

</script>
@endsection
</x-app-layout>
