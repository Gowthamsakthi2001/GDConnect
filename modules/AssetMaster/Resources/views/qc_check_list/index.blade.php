<x-app-layout>

<style>
    .form-check-input:checked {
        background-color: #0f62fe !important;
        border-color: #0f62fe !important;
    }
    table thead th{
        background: white !important;
        color: #4b5563 !important;
    }
     .custom-dropdown-toggle::after {
        display: none !important;
      }

    .form-check-input[type="checkbox"] {
        width: 2.3rem;
        height: 1.2rem;
    }
    .dropdown-menu {
      max-width: 250px;
      word-wrap: break-word;
    }

body {
  overflow-x: hidden !important;
}


</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0">Quality Check Label Lists<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{count($lists)}}</span></div>
                            
                           
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                           
                            <div class="text-center d-flex gap-2">
                                
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="window.location.href='{{route('admin.asset_management.quality_check_list.export',['status'=>$status,'from_date'=>$from_date,'to_date'=>$to_date,'fill_vehicle_type'=>$fill_vehicle_type])}}'"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="AddQCLModal()"
                                    style="cursor: pointer;">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        


        <div class="table-responsive">
                    <table class="table custom-table text-center" style="width: 100%;">
                        <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                                <th scope="col" class="custom-dark text-center">#</th>
                                <th scope="col" class="custom-dark text-center">Vehicle Type</th>
                                <th scope="col" class="custom-dark text-center">Label Name</th>
                                <th scope="col" class="custom-dark text-center">Created At</th>
                                <th scope="col" class="custom-dark text-center">Status</th>
                                <th scope="col" class="custom-dark text-center">Active/In Active</th>
                                <th scope="col" class="custom-dark text-center">Action</th>
                            </tr>
                        </thead>
                    

                        <tbody class="bg-white border border-white">
                        @if(isset($lists))
                            @foreach($lists as $val)
                                <?php
                                    $status = $val->status;
                                    $colorClass = match ($val->status) {
                                        1 => 'text-success',
                                        0 => 'text-danger',
                                        default => 'text-secondary',
                                    };
                                     
                                    $statusName = $val->status == 1 ? 'Active' : 'Inactive';
                                ?>
                    
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td >{{ $val->vehicle_type->name ?? '' }}</td>
                                    
                                    <td>{{ \Illuminate\Support\Str::limit($val->label_name, 50, '...') }}</td>
                                    <td >{{ date('d M Y h:i:s A',strtotime($val->created_at)) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                                            <span class="text-capitalize">{{ $statusName }}</span>
                                        </div>
                                    </td>
                    
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                            <input class="form-check-input toggle-status" data-id="{{ $val->id }}" type="checkbox" role="switch" id="toggleSwitch{{ $loop->index }}" {{$status == 1 ? 'checked' : ''}}>
                                        </div>
                                    </td>
                
                                    
                                   <td>
                                      <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                          <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                          <li>
                                            <a href="javascript:void(0);" 
                                               data-vehicle_type="{{ $val->vehicle_type_id }}" 
                                               data-label_name="{{ $val->label_name }}"
                                               data-status="{{ $val->status }}"
                                               onclick="EditQCLModal('{{ $val->id }}', this)" 
                                               class="dropdown-item d-flex align-items-center">
                                                <i class="bi bi-pencil-square me-2"></i> Edit
                                            </a>

                                          </li>
                                          <li>
                                            <a href="javascript:void(0);" 
                                               class="dropdown-item d-flex align-items-center" onclick="DeleteRecord('{{$val->id}}')">
                                              <i class="bi bi-trash me-2"></i> Delete
                                            </a>
                                          </li>
                                        </ul>
                                      </div>
                                    </td>

                                </tr>
                            @endforeach
                         @endif
                        </tbody>
                    </table>

                </div>
    </div>
      
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Quality Check Label Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearLocationFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyLocationFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
              <?php
                $checked_status = request('status');
                $from_date = request('from_date');
                $to_date = request('to_date');
              ?>
               <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status" value="all" {{$ch_status == 'all' ? 'checked': ''}}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{$ch_status == 1 ? 'checked': ''}}>
                      <label class="form-check-label" for="status1">
                       Active
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status2" value="0" {{$ch_status == 0 ? 'checked': ''}}>
                      <label class="form-check-label" for="status2">
                        Inactive
                      </label>
                    </div>
                    
               </div>
           </div>
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Vehcle Type</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                       <select class="form-control basic-single" id="Fil_VehicleType">
                            <option value="">Select</option>
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                    <option value="{{$val->id}}" {{$fill_vehicle_type == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
  
               </div>
            </div>

            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Date Between</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearLocationFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyLocationFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
        <div class="modal fade" id="AddQCL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="AddQCLLabel" aria-hidden="true">
        <form id="Add_QCL_Form" action="javascript:void(0);" method="POST">
            @csrf
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enter a Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            
                             <div class="col-12 mb-3">
                                <label for="vehicle_type" class="mb-2 ms-1">Vehicle Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="vehicletype" name="vehicle_type">
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                        @foreach($vehicle_types as $val)
                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                                <div class="col-12 mb-3">
                                    <label class="input-label mb-2 ms-1">Label Names <span class="text-danger">*</span></label>
                                
                                    <div id="label-container">
                                        <!-- Initial Label -->
                                        <div class="label-group mb-2 d-flex align-items-start gap-2">
                                            <textarea class="form-control" rows="2" name="label_names[]" placeholder="Enter label name 1"></textarea>
                                        </div>
                                    </div>
                                
                                    <button type="button" class="btn btn-success mt-2" id="add-label-btn">
                                         Add Label
                                    </button>
                                </div>


                            
                            <div class="col-12">
                                <label for="statusSelect" class="mb-2 ms-1">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="status_value" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success addsubmitBtn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
     <div class="modal fade" id="EditQCL" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="EditQCLLabel" aria-hidden="true">
        <form id="Edit_QCL_Form" action="javascript:void(0);" method="POST">
            @csrf
            <input type="hidden" name="edit_qcl_id" id="Edit_QCL_ID" value="">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="EditQCLLabel">Enter a Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            
                             <div class="col-12 mb-3">
                                <label for="vehicle_type" class="mb-2 ms-1">Vehicle Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="vehicle_type" name="vehicle_type">
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                        @foreach($vehicle_types as $val)
                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                             <div class="col-12 mb-3">
                                <label class="input-label mb-2 ms-1" for="name">Label Name <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" rows="5" id="name" name="name"></textarea>
                                
                            </div>
                            
                            <div class="col-12">
                                <label for="statusSelect" class="mb-2 ms-1">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success submitBtn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@section('script_js')


<script>
    
    
    
    function applyLocationFilter() {
        const selectedStatus = document.querySelector('input[name="status"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        const fil_vehicle = document.getElementById('Fil_VehicleType').value;
        
        if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
                toastr.error("From Date and To Date is must be required");
                return;
            }
            
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('vehicle_type', fil_vehicle);
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
    
        window.location.href = url.toString();
    }


    
    function clearLocationFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        url.searchParams.delete('vehicle_type');
        window.location.href = url.toString();
    }



  $(document).ready(function () {
    $('#CSelectAllBtn').on('change', function () {
      $('.sr_checkbox').prop('checked', this.checked);
    });

    $('.sr_checkbox').on('change', function () {
      if (!this.checked) {
        $('#CSelectAllBtn').prop('checked', false);
      } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
        $('#CSelectAllBtn').prop('checked', true);
      }
    });
  });
  
  function AddQCLModal() {
    $('#Add_QCL_Form')[0].reset();
    $('#AddQCL').modal('show');
}
  
  function EditQCLModal(id, el) {
        if (id == 0) {
            $("#EditQCL").modal('show');
            $('#EditQCL form').trigger('reset');
        } else {
            $("#EditQCLLabel").text("Update a Details");
            $(".submitBtn").text('Update');
    
            var vehicle_type = $(el).data('vehicle_type');
            var label_name = $(el).data('label_name');
            var status = $(el).data('status');
    
            $("#EditQCL").modal('show');
            $("#Edit_QCL_ID").val(id);
            $("#vehicle_type").val(vehicle_type);
            $('#name').val(label_name);
            $("#status").val(status);
        }
    }
    

  
  $("#Edit_QCL_Form").submit(function(){
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token","{{csrf_token()}}");
        var redirect = "{{ route('admin.asset_management.quality_check_list.index') }}";
        $.ajax({
            url: "{{ route('admin.asset_management.quality_check_list.store') }}",
            type: "POST",
            data:formData,
           contentType: false, // Required for FormData
            processData: false, // Required for FormData
            success: function(response) {
                console.log(response);
                if (response.success == true) {
                   toastr.success(response.message);
                    $("#EditQCL").modal('hide');
                    form.reset();
                   setTimeout(function(){
                       window.location.href = redirect;
                   },1000);
                } else {
                    toastr.error(response.message);
                }
            },
           error: function(xhr) {
                if (xhr.status === 422) { 
                    var errors = xhr.responseJSON.errors; 
                    $.each(errors, function(key, value) { 
                        toastr.error(value[0]); 
                    });
                } else {
                    toastr.error("Please try again.");
                }
            },
        });
    });
</script>


<script>
      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('field1');
        const checkboxes = document.querySelectorAll('.form-check-input');
    
        selectAll.addEventListener('change', function () {
          checkboxes.forEach(checkbox => {
            if (checkbox !== selectAll) {
              checkbox.checked = selectAll.checked;
            }
          });
        });
    
        // Optional: Update "Select All" if any individual checkbox is unchecked
        checkboxes.forEach(checkbox => {
          if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function () {
              const allChecked = Array.from(checkboxes)
                .filter(cb => cb !== selectAll)
                .every(cb => cb.checked);
              selectAll.checked = allChecked;
            });
          }
        });
      });
</script>

<script>
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
  
</script>

<script>
    $(document).ready(function () {
    $('.toggle-status').change(function (e) {
        e.preventDefault();

        var checkbox = $(this);
        var brandId = checkbox.data('id');
        var intendedStatus = checkbox.is(':checked') ? 1 : 0;

        // Temporarily revert the checkbox until confirmed
        checkbox.prop('checked', !intendedStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to ${intendedStatus ? 'Active' : 'Inactive'} this Label Name.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Now update checkbox state immediately after confirmation
                checkbox.prop('checked', intendedStatus);

                // Proceed with AJAX
                $.ajax({
                    url: "{{ route('admin.asset_management.quality_check_list.status_update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: brandId,
                        status: intendedStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            } else {
                // Cancelled - keep checkbox in original state
                checkbox.prop('checked', !intendedStatus);
            }
        });
    });
});

function DeleteRecord(id){
    Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete this Label Name?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: "{{ route('admin.asset_management.quality_check_list.destroy') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            } else {
                console.log("else");
            }
        });
}

</script>

<script>
    $(document).ready(function () {
        
    let labelIndex = 2;

    // Add new label textarea
    $('#add-label-btn').on('click', function () {
        const newLabel = `
            <div class="label-group mb-2 d-flex align-items-center gap-2">
                <textarea class="form-control" rows="2" name="label_names[]" placeholder="Enter label name ${labelIndex}"></textarea>
                <button type="button" class="btn btn-danger btn-sm remove-label-btn">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        $('#label-container').append(newLabel);
        labelIndex++;
    });

    // Delete a specific label textarea
    $('#label-container').on('click', '.remove-label-btn', function () {
        $(this).closest('.label-group').remove();
    });
    
    
    
        // Submit handler
    $("#Add_QCL_Form").submit(function (e) {
        e.preventDefault();

        let hasError = false;

        // Validate all label fields
        $('textarea[name="label_names[]"]').each(function () {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                hasError = true;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (hasError) {
            toastr.error("Please fill all label names.");
            return false;
        }

        // Proceed if valid
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");

        var redirect = "{{ route('admin.asset_management.quality_check_list.index') }}";
        var $submitBtn = $('.addsubmitBtn');

        $submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i> Submitting...');

        $.ajax({
            url: "{{ route('admin.asset_management.quality_check_list.create') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                if (response.success == true) {
                    toastr.success(response.message);
                    $("#EditQCL").modal('hide');
                    form.reset();
                    setTimeout(() => window.location.href = redirect, 1000);
                } else {
                    toastr.error(response.message);
                    $submitBtn.prop('disabled', false).html('Submit');
                }
            },
            error: function (xhr) {
                $submitBtn.prop('disabled', false).html('Submit');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Please try again.");
                }
            },
        });
    });
});
</script>

@endsection
</x-app-layout>
