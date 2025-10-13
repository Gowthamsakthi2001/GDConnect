<x-app-layout>
<style>
.form-check-input {
    width: 20px;
    height: 20px;
    accent-color: #007bff; /* blue when checked */
    margin-top: 0.3rem;
}

.form-check-label {
    font-size: 1rem;
    margin-left: 0.4rem;
}
    /* Main single selection style */
.select2-container--default .select2-selection--single {
    border: none !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    height: 38px !important; /* match Bootstrap */
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
    background-color: #fff !important;
    display: flex;
    align-items: center;
}

/* Arrow alignment */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 10px;
}

/* Text alignment */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    padding-left: 0 !important;
}

/* On focus border color (optional) */
.select2-container--default.select2-container--focus .select2-selection--single {
    border-bottom: 2px solid #3b82f6 !important; /* blue on focus */
}
table, tbody, tfoot, thead, tr, th, td {
    border: none !important;
}

table thead th {
    text-align: center !important;
    background: white !important;
    color: black !important;
}

.form-check-input[type="checkbox"]{
    width: 2.3rem;
    height:1.2rem;
}
</style>


  
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Customer Login Credential - {{$customerData->trade_name ?? ''}}
                              </div> <!-- updated by Gowtham.s -->
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>

            <div class="card p-2">
                <div class="card-body pb-4">
                    <form id="CreateCustomerLoginForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                
                        <input type="hidden" id="customerId" value="{{ $decode_id }}">
                        <div class="col-12 mb-4">
                            <div class="form-group d-flex align-items-center flex-wrap">
                                <label class="me-3 mb-0 fw-semibold text-nowrap">
                                    Login Type <span class="text-danger">*</span>
                                </label>
                                
                                <div class="form-check form-check-inline d-flex align-items-center me-4">
                                    <input class="form-check-input me-1" type="radio" name="login_type" id="Masterlogin" value="master" required>
                                    <label class="form-check-label" for="Masterlogin">Master Login</label>
                                </div>
                        
                                <div class="form-check form-check-inline d-flex align-items-center">
                                    <input class="form-check-input me-1" type="radio" name="login_type" id="Zonelogin" value="zone" required>
                                    <label class="form-check-label" for="Zonelogin">Zone Login</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3 d-none" id="MasterSection">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="city_id">City <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="city_id" name="city_id" onchange="getZones(this.value)">
                                        <option value="">Select City</option>
                                        @if(isset($cities))
                                           @foreach($cities as $data)
                                              <option value="{{$data->id}}">{{$data->city_name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-none" id="ZoneSection">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="zone_id">Zone <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="zone_id" name="zone_id">
                                        <option value="">Select a city first</option>
                                      
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="Email" class="col-12 col-md-4 col-form-label text-start "> Email ID <span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8">
                                    <input type="email" class="form-control border-0 border-bottom rounded-0 shadow-none" name="email" id="Email" placeholder="Enter Your Email ID" required>
                                </div>
                            </div>
                        </div>
                        
                       <!--<div class="col-md-6 mb-3">-->
                       <!--     <div class="form-group row">-->
                       <!--         <label for="passowrd" class="col-12 col-md-4 col-form-label text-start ">Password<span class="text-danger">*</span></label>-->
                       <!--         <div class="col-12 col-md-8">-->
                       <!--             <input type="password" class="form-control border-0 border-bottom rounded-0 shadow-none" name="password" id="password" placeholder="Enter Your Password" autocomplete="off" required>-->
                       <!--         </div>-->
                       <!--     </div>-->
                       <!-- </div>-->
                        
                        <!-- Password field with toggle -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group row align-items-center">
                                <label for="password" class="col-12 col-md-4 col-form-label text-start">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="col-12 col-md-8 position-relative">
                                    <input type="password" 
                                           class="form-control border-0 border-bottom rounded-0 shadow-none" 
                                           name="password" 
                                           id="password" 
                                           placeholder="Enter Your Password" 
                                           autocomplete="off" 
                                           required>
                                    <!-- Eye toggle button -->
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 " 
                                          style="cursor:pointer;" 
                                          onclick="togglePassword()">
                                        <i id="eyeIcon" class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>


                       <div class="col-md-6 mb-3">
                            <div class="form-group row align-items-center">
                                <label for="password_confirmation" class="col-12 col-md-4 col-form-label text-start ">Confirm Password<span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8 position-relative">
                                    <!--<input type="password" class="form-control border-0 border-bottom rounded-0 shadow-none" name="password_confirmation" id="password_confirmation" placeholder="Enter Your Confirm Password" autocomplete="off" required>-->
                                    <input type="password" 
                                           class="form-control border-0 border-bottom rounded-0 shadow-none" 
                                           name="password_confirmation" 
                                           id="password_confirmation" 
                                           placeholder="Enter Your Confirm Password" 
                                           autocomplete="off" 
                                           required>
                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3 " 
                                          style="cursor:pointer;" 
                                          onclick="toggleConfirmPassword()">
                                        <i id="eyeIcon1" class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="status">Status<span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none" id="status" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end gap-2">
                            <button type="button" class="btn btn-danger px-3">Reset</button>
                            <button type="submit" id="addBtn" class="btn btn-success px-3">Add Login</button>
                        </div>
               
                    </div>
                    </form>
                </div>
                <hr>
                


                
                <!-- Table Layout -->
                <div class="table-responsive position-relative p-10">
                    
                    <div id="loadingSpinner" class="text-center my-3" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                
                    <table id="LoginCredentialTable_List" class="table text-center" style="width: 100%;">
                        <thead class="bg-white rounded">
                            <tr>
                                <th>S.NO</th>
                                <th>Email ID</th>
                                <th>Login Type</th>
                                <th>City</th>
                                <th>Zone</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white border border-white">
                            <!-- Rows will be appended dynamically -->
                        </tbody>
                    </table>
                </div>
                    
                                         
            </div>
    </div>
    
    
            <div class="modal fade" id="EditLoginDetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="EditLoginDetails" aria-hidden="true">
            <form id="EditLoginDetails_From" action="javascript:void(0);" method="POST">
                @csrf
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" >Update Login Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                    <div class="row">
                        <input id="edit_id" name="id" type="hidden">
                        <input type="hidden" id="Get_LT">
            
                        <!--<div class="col-12 mb-4">-->
                        <!--    <div class="form-group d-flex align-items-center flex-wrap">-->
                        <!--        <label class="me-3 mb-0 fw-semibold text-nowrap">-->
                        <!--            Login Type <span class="text-danger">*</span>-->
                        <!--        </label>-->
                                
                        <!--        <div class="form-check form-check-inline d-flex align-items-center me-4">-->
                        <!--            <input class="form-check-input me-1" type="radio" name="logintype" id="masterlogin" value="master" required>-->
                        <!--            <label class="form-check-label" for="masterlogin">Master Login</label>-->
                        <!--        </div>-->
                        
                        <!--        <div class="form-check form-check-inline d-flex align-items-center">-->
                        <!--            <input class="form-check-input me-1" type="radio" name="logintype" id="zonelogin" value="zone" required>-->
                        <!--            <label class="form-check-label" for="zonelogin">Zone Login</label>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <div class="col-md-6 mb-3" id="MasterEditSection">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="city_id">City <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none EditFetchZones" id="edit_city_id" name="edit_city_id">
                                        <option value="">Select City</option>
                                        @if(isset($cities))
                                           @foreach($cities as $data)
                                              <option value="{{$data->id}}">{{$data->city_name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-none" id="ZoneEditSection">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="zone_id">Zone <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none" id="edit_zone_id" name="edit_zone_id">
                                        <option value="">Select a city first</option>
                                      
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="email_id" class="col-12 col-md-4 col-form-label text-start "> Email ID <span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8">
                                    <input type="email" class="form-control border-0 border-bottom rounded-0 shadow-none" name="email_id" id="email_id" placeholder="Enter Your Email ID" required>
                                </div>
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="edit_password" class="col-12 col-md-4 col-form-label text-start ">Password<span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8">
                                    <input type="password" class="form-control border-0 border-bottom rounded-0 shadow-none" name="edit_password" id="edit_password" placeholder="Enter Your Password" autocomplete="off">
                                </div>
                            </div>
                        </div>


                       <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="passwordconfirmation" class="col-12 col-md-4 col-form-label text-start ">Confirm Password<span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8">
                                    <input type="password" class="form-control border-0 border-bottom rounded-0 shadow-none" name="passwordconfirmation" id="passwordconfirmation" placeholder="Enter Your Confirm Password" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="status_value">Status<span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none" id="status_value" name="status_value">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success updateBtn">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    
   
@section('script_js')



<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.remove("bi-eye");
        eyeIcon.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon.classList.remove("bi-eye-slash");
        eyeIcon.classList.add("bi-eye");
    }
}

function toggleConfirmPassword() {
    const passwordField = document.getElementById("password_confirmation");
    const eyeIcon1 = document.getElementById("eyeIcon1");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon1.classList.remove("bi-eye");
        eyeIcon1.classList.add("bi-eye-slash");
    } else {
        passwordField.type = "password";
        eyeIcon1.classList.remove("bi-eye-slash");
        eyeIcon1.classList.add("bi-eye");
    }
}
</script>

<script>
    $(document).ready(function () {
        $('input[name="login_type"]').on('change', function () {
            $("#city_id").val('').trigger('change');
            $("#zone_id").val('').trigger('change');
            if ($(this).val() === 'zone') {
                // Show Zone section for Zone login
                $("#MasterSection").removeClass('d-none');
                $('#ZoneSection').removeClass('d-none');
            } else {
                // Hide Zone section and reset dropdown for Master login
                $("#MasterSection").removeClass('d-none');
                $('#ZoneSection').addClass('d-none');
                $('#zone_id').val('');
            }
        });
    });
    
    $(document).ready(function () {
        $('input[name="logintype"]').on('change', function () {
            if ($(this).val() === 'zone') {
                // Show Zone section for Zone login
                $('#ZoneEditSection').removeClass('d-none');
            } else {
                // Hide Zone section and reset dropdown for Master login
                $('#ZoneEditSection').addClass('d-none');
                $('#zoneid').val('');
            }
        });
    });

        
    
        
    $("#CreateCustomerLoginForm").submit(function(e) {
        e.preventDefault();
    
        const customer_id = "{{ $decode_id }}";
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("customer_id", customer_id);
    
        var $submitBtn = $("#addBtn");
        var originalText = $submitBtn.html();
        $submitBtn.prop("disabled", true).html("⏳ Submitting...");
    
        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.create_login') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $submitBtn.prop("disabled", false).html(originalText);
    
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $submitBtn.prop("disabled", false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Please try again.");
                }
            }
        });
    });
    
    
        
    $("#EditLoginDetails_From").submit(function(e) {
        e.preventDefault();
    
        const customer_id = "{{ $decode_id }}";
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("customer_id", customer_id);
    
    
    
        var $submitBtn = $(".updateBtn");
        var originalText = $submitBtn.html();
        $submitBtn.prop("disabled", true).html("⏳ Updating...");
    
        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.login_update') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $submitBtn.prop("disabled", false).html(originalText);
    
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $submitBtn.prop("disabled", false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Please try again.");
                }
            }
        });
    });
    
    
    
    let loginDataList = [];
    
    $(document).ready(function () {
        let customerId = $("#customerId").val();
        let $spinner = $('#loadingSpinner');
        let $tbody = $('#LoginCredentialTable_List tbody');
    
        // Initial fetch
        fetchCustomerLogins(customerId);
    
         
        // Function to fetch login data
        function fetchCustomerLogins(customerId) {
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.get_customer_logins') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF protection
                    customer_id: customerId
                },
                beforeSend: function () {
                    $spinner.show();
                },
                success: function (response) {
                    $spinner.hide();
                    $tbody.empty();
    
                    if (response.success && response.data.length > 0) {
                        
                    loginDataList = response.data; // Store globally for edit usage
                        
                        response.data.forEach((login, index) => {
                        let statusToggle = `
                            <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                <input class="form-check-input toggle-status" 
                                       type="checkbox" role="switch" 
                                       id="toggleSwitch${index}" 
                                       data-id="${login.id}" 
                                       ${login.status == 1 ? 'checked' : ''}>
                            </div>`;
    
                            let createdAt = new Date(login.created_at).toLocaleString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true // this gives AM/PM
                            });
    
                            let row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${login.email}</td>
                                    <td>${login.type.charAt(0).toUpperCase() + login.type.slice(1)}</td>
                                    <td>${login.city}</td>
                                    <td>${login.zone}</td>
                                    <td>${createdAt}</td>
                                    <td>${statusToggle}</td>
                                    <td>
                                        <a href="javascript:void(0)" 
                                           class="d-flex align-items-center justify-content-center border-0 text-warning edit-login" 
                                           data-id="${login.id}" data-get_city_id="${login.city_id}" data-get_zone_id="${login.zone_id}" data-get_login_type="${login.type}">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                    </td>
                                </tr>`;
                            $tbody.append(row);
                            
                            
                              $(`#toggleSwitch${index}`).on('change', function () {
                            let loginId = $(this).data('id');
                            let status = $(this).is(':checked') ? 1 : 0;

                            Swal.fire({
                                title: 'Are you sure?',
                                text: `You want to ${status ? 'activate' : 'deactivate'} this login?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.login_status_update') }}",
                                        type: 'POST',
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            id: loginId,
                                            status: status
                                        },
                                        success: function (res) {
                                            if (res.success) {
                                                Swal.fire('Updated!', 'Login status updated.', 'success');
                                            } else {
                                                Swal.fire('Failed!', 'Status update failed.', 'error');
                                                // Revert toggle
                                                $(`#toggleSwitch${index}`).prop('checked', !status);
                                            }
                                        },
                                        error: function () {
                                            Swal.fire('Error!', 'Something went wrong.', 'error');
                                            // Revert toggle
                                            $(`#toggleSwitch${index}`).prop('checked', !status);
                                        }
                                    });
                                } else {
                                    // Revert toggle if canceled
                                    $(`#toggleSwitch${index}`).prop('checked', !status);
                                }
                            });
                        });
                        
                        });
                        
                    $('#LoginCredentialTable_List').DataTable({
                        columnDefs: [
                            { orderable: false, targets: '_all' }
                        ],
                        lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
                        responsive: true,
                        scrollX: true,
                        destroy: true  // important if you reload table multiple times
                    });


                    } else {
                        
                        
                    $('#LoginCredentialTable_List').DataTable({
                        columnDefs: [
                            { orderable: false, targets: '_all' }
                        ],
                        lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
                        responsive: true,
                        scrollX: true,
                        destroy: true  // important if you reload table multiple times
                    });
                    
                        $tbody.html(`<tr><td colspan="6" class="text-center text-muted">No login records found.</td></tr>`);
                    }
                },
                error: function () {
                    $spinner.hide();
                    alert('Failed to fetch login details. Please try again.');
                }
            });
        }
    });
    
  
    
    function getZones(CityID) {
        let ZoneDropdown = $('#zone_id');
        var login_type = $('input[name="login_type"]:checked').val();
        console.log(login_type);
        ZoneDropdown.empty().append('<option value="">Loading...</option>');

       if(login_type == 'zone'){
           
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">--Select Zone--</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                        });
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
            // ZoneWrapper.hide();
        }
       }
       
    }
    
    
    
  $(document).on('click', '.edit-login', function () {
        $('#edit_city_id').select2({
            dropdownParent: $('#EditLoginDetails')
        });
        $('#edit_zone_id').select2({
            dropdownParent: $('#EditLoginDetails')
        });
    
        let loginId = $(this).data('id');
        let type    = $(this).data('get_login_type');
        let cityId  = $(this).data('get_city_id');
        let zoneId  = $(this).data('get_zone_id');
        let login   = loginDataList.find(item => item.id == loginId);
    
        if (!login) {
            alert('Login details not found!');
            return;
        }
    
        // master / zone toggle
        if (login.type === 'master') {
            $('#EditLoginDetails #masterlogin').prop('checked', true);
            $('#EditLoginDetails #ZoneEditSection').addClass('d-none');
        } else if (login.type === 'zone') {
            $('#EditLoginDetails #zonelogin').prop('checked', true);
            $('#EditLoginDetails #ZoneEditSection').removeClass('d-none');
        }
    
        // assign values
        $('#EditLoginDetails #edit_id').val(login.id); 
        $('#EditLoginDetails #email_id').val(login.email);     
        $('#EditLoginDetails #status_value').val(login.status);
    
        $('#edit_city_id').val(cityId).trigger('change'); 
        $("#Get_LT").val(type);
        // load zones if type is zone
        if (type === 'zone') {
            fetchZones(type, cityId, zoneId);
        }
    
        // show modal
        let modal = new bootstrap.Modal(document.getElementById('EditLoginDetails'));
        modal.show();
    });

    function fetchZones(type, CityID, ZoneID) { 
        let ZoneDropdown = $('#edit_zone_id');
    
        if (type !== 'zone') {
            return;
        }
    
        ZoneDropdown.empty().append('<option value="">Loading...</option>');
    
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">--Select Zone--</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append(
                                '<option value="' + zone.id + '">' + zone.name + '</option>'
                            );
                        });
    
                        // ✅ set zone if provided
                        if (ZoneID) {
                            ZoneDropdown.val(ZoneID).trigger('change.select2'); 
                        }
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
        }
    }

    
   $(".EditFetchZones").on('change', function() {
        var CityID     = $('#edit_city_id').val();
        // var login_type = $('input[name="logintype"]:checked').val();
        
        var login_type = $("#Get_LT").val();
    
        if (login_type === 'zone') {
            fetchZones(login_type, CityID, null); 
        }
    });

    
</script>
@endsection
</x-app-layout>
