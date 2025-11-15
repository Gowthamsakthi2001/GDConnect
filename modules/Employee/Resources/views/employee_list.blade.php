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
                        <img src="{{asset('admin-assets/icons/custom/employees-icon.webp')}}" class="img-fluid rounded"><span class="ps-2">List of Employee</span>
                    </div>
                    <!--<a href="{{ route('admin.Green-Drive-Ev.employee_management.employee_create') }}" class="btn custom-btn-primary btn-sm">-->
                    <!--        <i class="fa fa-plus-circle"></i>&nbsp;-->
                    <!--        Add Employee-->
                    <!--    </a>-->
 
               </div>
            </h2>
        </div>
        <!-- End Page Header -->
        
        <div class="tile">
                <div class="card mb-4">
                    <div class="card-header p-0 m-0"></div>
                    <div class="card-body">
                       <div class="row mb-3 align-items-center">
                        <div class="col-md-4 col-12 mb-2 mb-md-0">
                            <select id="current_city_id" class="form-control">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 col-12">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <a class="btn btn-sm btn-round custom-btn city-export-btn" data-baseurl="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'all']) }}" href="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'all']) }}">
                                    <i class="bi bi-download"></i> All
                                </a>
                                <a class="btn btn-sm btn-round custom-btn city-export-btn" data-baseurl="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'pending']) }}" href="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'pending']) }}">
                                    <i class="bi bi-download"></i> Pending
                                </a>
                                <a class="btn btn-sm btn-round custom-btn city-export-btn" data-baseurl="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'approve']) }}" href="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'approve']) }}">
                                    <i class="bi bi-download"></i> Approved
                                </a>
                                <a class="btn btn-sm btn-round custom-btn city-export-btn" data-baseurl="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'deny']) }}" href="{{ route('admin.Green-Drive-Ev.employee_management.export_employee_verify_list', ['type'=>'deny']) }}">
                                    <i class="bi bi-download"></i> Rejected
                                </a>
                            </div>
                        </div>
                    </div>

                    
                        <div class="table-responsive">
                            <x-data-table :dataTable="$dataTable" />
                        </div>
                    </div>
            </div>
        </div>
    </div>
@section('script_js')
   <script>
   document.querySelectorAll('#current_city_id').forEach(function(filter) {
            filter.addEventListener('change', function() {
                let filterName = filter.id;
                let filterValue = filter.value;
                console.log(filterName);
                console.log(filterValue);
                if (filterName === 'current_city_id') {
                    if (filterValue !== '') {
                        // document.getElementById('client_id').value = '';
                    }
                    
                     document.querySelectorAll('.city-export-btn').forEach(function(btn) {
                        const baseUrl = btn.dataset.baseurl;
                        const newUrl = filterValue ? `${baseUrl}?city_id=${filterValue}` : baseUrl;
                        btn.setAttribute('href', newUrl);
                    });
                }

                // Apply the filter based on the selected value
                applyFilter(filterName, filterValue);
            });
        });



        // function applyFilter(filterName, filterValue) {
        //     console.log(filterName+' '+filterValue);
        //     // Reload the DataTable with the corresponding filter applied
        //     let url = "{{ route('admin.Green-Drive-Ev.delivery-man.list') }}?" + filterName + "=" + filterValue;
        //     $('#employess-list-table').DataTable().ajax.url(url).load();
        // }
        
        function applyFilter(filterName, filterValue) {
            let url = "{{ route('admin.Green-Drive-Ev.employee_management.employee_list') }}?" + 
                      filterName + "=" + filterValue + 
                      "&work_type=in-house";  // Explicitly adding work_type
        
            console.log("Generated URL:", url);
            $('#employess-list-table').DataTable().ajax.url(url).load();
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
                                $('#employess-list-table').DataTable().ajax.reload(null, false); 
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
    
 
 function ChangeJobStatus(id, value, element, previousVal = '') {
    if (!id) {
        toastr.error("Employee ID field is required");
        return;
    }

    if (!value) {
        toastr.error("Job Status field cannot be empty");
        return;
    }

    if (value === 'resigned') {
        Swal.fire({
            title: 'Resignation Remarks',
            input: 'textarea',
            inputLabel: 'Please provide remarks for resignation',
            inputPlaceholder: 'Type remarks here...',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Cancel',
        }).then((inputResult) => {
            if (inputResult.isConfirmed) {
                let remarks = inputResult.value || '';
                sendStatusUpdate(id, value, remarks, element, previousVal);
            } else {
                // Restore previous value
                $(element).val(previousVal);
            }
        });
    } else {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to update the job status to " + value + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }).then((inputResult) => {
            if (inputResult.isConfirmed) {
                sendStatusUpdate(id, value, '', element, previousVal);
            } else {
                $(element).val(previousVal);
            }
        });
    }
}

function sendStatusUpdate(id, value, remarks, element, previousVal) {
    $.ajax({
        url: "{{ route('admin.Green-Drive-Ev.employee_management.job_status_update') }}",
        type: 'GET', // Recommend POST
        data: {
            id: id,
            job_status: value,
            remarks: remarks
        },
        success: function (response) {
            if (response.success) {
                Swal.fire('Success!', response.message, 'success');
                setTimeout(function () {
                    $('#employess-list-table').DataTable().ajax.reload(null, false);
                }, 1000);
            } else {
                Swal.fire('Error!', response.message, 'error');
                $(element).val(previousVal); // Rollback
            }
        },
        error: function () {
            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
            $(element).val(previousVal); // Rollback
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
                        $.ajax({
                            url: url,
                            type: 'GET',
                            beforeSend: function () {
                                Swal.fire({
                                    title: 'Please wait...',
                                    text: 'Updating status...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                           success: function (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message || 'Status updated successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            
                                window.location.reload();
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: xhr.responseJSON?.message || 'Something went wrong!',
                                });
                                checkboxElement.checked = previousState;
                            }
                        });
                    }

            });
        }
        
        
//         function status_change_alert(url, message, e, checkboxElement = null) {
//     e.preventDefault();

//     if (!checkboxElement) return;

//     // ✅ Save the initial state before showing confirmation
//     const previousState = checkboxElement.checked;

//     // Temporarily disable toggle to prevent double-click during SweetAlert
//     checkboxElement.disabled = true;

//     Swal.fire({
//         title: "Are you sure?",
//         text: message,
//         icon: 'warning',
//         showCancelButton: true,
//         cancelButtonColor: 'default',
//         confirmButtonColor: '#FC6A57',
//         cancelButtonText: "No",
//         confirmButtonText: "Yes",
//         reverseButtons: true
//     }).then((result) => {
//         checkboxElement.disabled = false; // Re-enable after response

        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Please wait...',
                        text: 'Updating status...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Status updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // ✅ Keep toggle as it is (new state confirmed)
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON?.message || 'Something went wrong!',
                    });
                    // ❌ Revert back to original state on error
                    checkboxElement.checked = previousState;
                }
            });
        } else {
            // ❌ Cancelled — revert to original state
            checkboxElement.checked = previousState;
        }
//     });
// }



</script>
@endsection
</x-app-layout>
