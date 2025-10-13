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

    input:checked+.toggle-switch-label {
        background-color: #4CAF50;
        /* Green when active */
    }

    input:checked+.toggle-switch-label .toggle-switch-indicator {
        transform: translateX(26px);
        /* Move the indicator to the right */
    }
</style>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/list_of_adhoc.png')}}" class="img-fluid rounded"><span class="ps-2">List of Adhoc</span>
                    </div>
                    <a href="{{route('admin.Green-Drive-Ev.adhocmanagement.create_adhoc')}}" class="btn custom-btn-primary btn-sm">
                            <i class="fa fa-plus-circle"></i>&nbsp;
                            Add Adhoc
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
                                    <select id="zone_id" class="form-control">
                                        <option value="">Select Zone</option>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                        
                                <div class="col-md-4">
                                    <select id="client_id" class="form-control">
                                        <option value="">Select Client</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select id="current_city_id" class="form-control">
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="row mb-3 ">
                            <div class="col-12 d-flex justify-content-end">
                                   <a class="btn btn-round me-1 btn-sm px-4 custom-btn" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'all'])}}">
                                        <i class="bi bi-download"></i> All
                                    </a>
                                    <a class="btn custom-btn btn-round btn-sm me-1" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'pending'])}}">
                                        <i class="bi bi-download"></i> Pending
                                    </a>
                                   <a class="btn custom-btn btn-round btn-sm me-1" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'approve'])}}">
                                       <i class="bi bi-download"></i> Approved
                                    </a>
                                  <a class="btn custom-btn btn-round btn-sm me-1" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'deny'])}}">
                                      <i class="bi bi-download"></i> Rejected
                                   </a>
                             </div>
                                  
                                
                                <!-- <div class="col-6 col-md-3 mb-2">-->
                                <!--    <a class="btn btn-success btn-round w-100" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'pending'])}}"><i class="bi bi-download"></i> Pending</a>-->
                                <!--</div>-->
                                <!--<div class="col-6 col-md-3 mb-2">-->
                                <!--    <a class="btn btn-success btn-round w-100" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'approve'])}}"><i class="bi bi-download"></i> Approved</a>-->
                                <!--</div>-->
                                <!-- <div class="col-6 col-md-3 mb-2">-->
                                <!--    <a class="btn btn-success btn-round w-100" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'deny'])}}"><i class="bi bi-download"></i> Rejected</a>-->
                                <!--</div>-->
                                <!--  <div class="col-6 col-md-3 mb-2">-->
                                <!--    <a class="btn btn-success btn-round w-100" href="{{route('admin.Green-Drive-Ev.adhocmanagement.export_adhoc_verify_list',['type'=>'deny'])}}"><i class="bi bi-download"></i> Rejected</a>-->
                                <!--</div>-->
                        </div>
                        <div class="table-responsive">
                            <x-data-table :dataTable="$dataTable" />
                        </div>
                    </div>
            </div>
        </div>
@section('script_js')
<script>
    document.querySelectorAll('#zone_id, #client_id, #current_city_id').forEach(function(filter) {
            filter.addEventListener('change', function() {
                let filterName = filter.id;
                let filterValue = filter.value;
        
                // Clear other fields based on the selected filter
                if (filterName === 'zone_id') {
                    // If zone is selected, clear client_id and current_city_id
                    if (filterValue !== '') {
                        document.getElementById('client_id').value = '';
                        document.getElementById('current_city_id').value = '';
                    }
                } else if (filterName === 'client_id') {
                    // If client is selected, clear zone_id and current_city_id
                    if (filterValue !== '') {
                        document.getElementById('zone_id').value = '';
                        document.getElementById('current_city_id').value = '';
                    }
                } else if (filterName === 'current_city_id') {
                    // If current city is selected, clear zone_id and client_id
                    if (filterValue !== '') {
                        document.getElementById('zone_id').value = '';
                        document.getElementById('client_id').value = '';
                    }
                }
        
                // Apply the filter based on the selected value
                applyFilter(filterName, filterValue);
            });
        });


        
        function applyFilter(filterName, filterValue) {
            // Reload the DataTable with the corresponding filter applied
            let url = "{{ route('admin.Green-Drive-Ev.adhocmanagement.list_of_adhoc') }}?" + filterName + "=" + filterValue;
            $('#supervisor-list-table').DataTable().ajax.url(url).load();
        }
    
    function ApproveOrRejectStatus(route, id, message, status, title = "Are you sure?") {
            if (status == 1) {
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
                            type: "POST",
                            data: {
                                id: id,
                                status: status,
                                _token: $('meta[name="csrf-token"]').attr('content') 
                            },
                            success: function (response) {
                              if(response.success){
                                    Swal.fire("Approved!",response.message, "success");
                                    setTimeout(function(){
                                        location.reload(); 
                                    },1000);
                              }else{
                                    Swal.fire("Warning!",response.message, "error");
                              }
                            },
                            error: function () {
                                Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    input: 'textarea', 
                    inputPlaceholder: 'Enter remarks here...',
                    inputAttributes: {
                        rows: 4 
                    },
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#FC6A57',
                    cancelButtonText: "No",
                    confirmButtonText: "Yes",
                    reverseButtons: true,
                    preConfirm: (remarks) => {
                        if (!remarks) {
                            Swal.showValidationMessage('Reject Reason are required');
                        }
                        return remarks;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const remarks = result.value;
                        $.ajax({
                            url: route,
                            type: "POST",
                            data: {
                                id: id,
                                status: status,
                                remarks: remarks,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if(response.success){
                                    Swal.fire("Rejected!",response.message, "success");
                                    setTimeout(function(){
                                        location.reload(); 
                                    },1000);
                              }else{
                                    Swal.fire("Warning!",response.message, "error");
                              }
                            },
                            error: function () {
                                Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                            }
                        });
                    }
                });
            }
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
                                $('#supervisor-list-table').DataTable().ajax.reload(null, false); 
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

</script>
@endsection
</x-app-layout>
