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



</style>


    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0">Insurer Name Master <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$data->count()}}</span></div>
                            
                           
                          
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                           
                            <div class="text-center d-flex gap-2">
                                 <div class="m-2 bg-white p-2 px-3 border-gray">
                                    
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>
                                    
                                 </div>
                                 
                                 
                                 
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    style="cursor: pointer;"      data-bs-toggle="modal" data-bs-target="#AddInsurerNameMaster">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        



        <div class="table-responsive">
                    <table id="InsurerNameMasterTable_List" class="table text-left" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Insurer Name</th>
                              <th scope="col" class="custom-dark">Created At</th>
                              <th scope="col" class="custom-dark">Status</th>
                              <th scope="col" class="custom-dark">Active/In Active</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>

                         


                          
                        <tbody class="bg-white border border-white">
                                  
                          @if(isset($data))
                            @foreach($data as $val)
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
                                  <td>
                                    <div class="form-check">
                                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="{{ $val->id }}">
                                    </div>
                                </td>
                                    <td >{{ $val->name ?? '' }}</td>
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
                
                                {{-- Action --}}
                                <td class="text-start">
                                    <a href="javascript:void(0);" class="text-success editInsurerBtn" data-id="{{ $val->id }}"  style="font-size: 1.2rem;">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Insurer Name Master</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearInsurerNameFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyInsurerFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status_value" id="status" value="all"  {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status_value" id="status1" value="1" {{ request('status') === '1' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status1">
                       Active
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status_value" id="status2" value="0" {{ request('status') === '0' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status2">
                        Inactive
                      </label>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value={{$to_date}}>
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearInsurerNameFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyInsurerFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
        
        
        
        <div class="modal fade" id="AddInsurerNameMaster" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="AddInsurerNameMaster" aria-hidden="true">
            <form id="AddInsurerNameMaster_From" action="javascript:void(0);" method="POST">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" >Create Insurer Name Master</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                
                                 <div class="col-12 mb-3">
                                    <label for="vehicle_type" class="mb-2 ms-1">Insurer Name
                                    <span class="text-danger">*</span></label>
                                        
                                    <input class="form-control basic-single" placeholder="Enter Insurer Name" name="insurer_name" required>
                                </div>
                                
                                
                                <div class="col-12">
                                    <label for="statusSelect" class="mb-2 ms-1">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control basic-single"  name="status" required>
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
        
        
            <div class="modal fade" id="EditTelematricMaster" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="EditTelematricMaster" aria-hidden="true">
            <form id="EditInsurerNameMaster_Form" action="javascript:void(0);" method="POST">
                @csrf
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" >Update Insurer Name</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                 <input type="hidden" name="id" id="edit_id">
                                 <div class="col-12 mb-3">
                                    <label for="vehicle_type" class="mb-2 ms-1">Insurer Name
                                    <span class="text-danger">*</span></label>
                                        
                                    <input class="form-control basic-single" placeholder="Enter Insurer Name" name="insurer_name" id="insurer_name" required>
                                </div>
                                
                                
                                
                                <div class="col-12">
                                    <label for="statusSelect" class="mb-2 ms-1">Status <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control basic-single" id="edit_status" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
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
    
        function applyInsurerFilter() {
        const selectedStatus = document.querySelector('input[name="status_value"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
                if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
                toastr.error("From Date and To Date is must be required");
                return;
            }
            
        }
        
             
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
    
        window.location.href = url.toString();
    }


    
    function clearInsurerNameFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
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
    
    
        
        $(document).ready(function () {
       $('#InsurerNameMasterTable_List').DataTable({
            // dom: 'Blfrtip',
            // dom: 'frtip',
            // buttons: ['excel', 'pdf', 'print'],
            // order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
            responsive: false,
            scrollX: true,
        });
    });
    
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
  
</script>
<script>
    $("#AddInsurerNameMaster_From").submit(function (e) {
        e.preventDefault();

        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");

        var redirect = "{{ route('admin.Green-Drive-Ev.master_management.insurer_name.index') }}";
        var $submitBtn = $(this).find('.submitBtn');

        // ✅ Disable button and show loading
        $submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Submitting...'
        );

        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.insurer_name.store') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $("#AddInsurerNameMaster").modal('hide');
                    form.reset();
                    setTimeout(function () {
                        window.location.href = redirect;
                    }, 1000);
                } else {
                    toastr.error("Unexpected error. Please try again.");
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
                    toastr.error("Something went wrong. Please try again.");
                }
            }
        });
    });
    
      $(document).on('click', '.editInsurerBtn', function () {
        var id = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.insurer_name.get_data', ':id') }}".replace(':id', id),
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                if (response.success) {
                    $('#edit_id').val(response.data.id);
                    $('#insurer_name').val(response.data.name);
                     $('#edit_status').val(String(response.data.status)).trigger('change'); // 🔥 fixed here

                    $('#EditTelematricMaster').modal('show');
                } else {
                    toastr.error('Failed to fetch data.');
                }
            },
            error: function () {
                toastr.error('Error loading data.');
            }
        });
    });
    
        $("#EditInsurerNameMaster_Form").submit(function (e) {
        e.preventDefault();

        var form = $(this)[0];
        var formData = new FormData(form);
        var $submitBtn = $(this).find('.updateBtn');

        // Spinner loading
        $submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Submitting...'
        );

        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.insurer_name.update') }}", // Update route
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $submitBtn.prop('disabled', false).html('Submit');
                if (response.success) {
                    toastr.success(response.message);
                    $('#EditTelematricMaster').modal('hide');
                    form.reset();
                    setTimeout(function () {
                        window.location.reload(); // or redirect if needed
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Something went wrong.');
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
                    toastr.error("Unexpected error. Try again.");
                }
            }
        });
    });



 $(document).ready(function () {
    $('.toggle-status').change(function (e) {
        e.preventDefault();

        var checkbox = $(this);
        var Id = checkbox.data('id');
        var intendedStatus = checkbox.is(':checked') ? 1 : 0;

        // Temporarily revert the checkbox until confirmed
        checkbox.prop('checked', !intendedStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to ${intendedStatus ? 'activate' : 'deactivate'} this item.`,
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
                    url: "{{ route('admin.Green-Drive-Ev.master_management.insurer_name.status_update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: Id,
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







</script>


<script>
  document.getElementById('exportBtn').addEventListener('click', function () {
    const selected = [];
    document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
      selected.push(cb.value);
    });



    const params = new URLSearchParams();
    params.append('status', '{{ request()->status }}');
    params.append('from_date', '{{ $from_date }}');
    params.append('to_date', '{{ $to_date }}');
    
    if (selected.length > 0) {
      params.append('selected_ids', JSON.stringify(selected));
    }

    const url = `{{ route('admin.Green-Drive-Ev.master_management.insurer_name.export_insurer_name_master') }}?${params.toString()}`;
    window.location.href = url;
  });
</script>

@endsection
</x-app-layout>
