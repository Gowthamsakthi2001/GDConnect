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
      
      .datatable-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .loading-spinner {
        width: 3rem;
        height: 3rem;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #0f62fe;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .table-container {
        position: relative;
        min-height: 200px;
    }
    
    /* Style DataTables Prev/Next buttons */
    .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next {
        background-color: #0d6efd; /* Bootstrap primary color */
        color: white !important;
        border-radius: 6px;
        padding: 6px 12px;
        border: none;
    }
    
    /* Hover effect */
    .dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
        background-color: #0b5ed7; /* Darker primary */
        color: white !important;
    }
    
    /* Disabled state */
    .dataTables_wrapper .dataTables_paginate .paginate_button.previous.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.next.disabled {
        background-color: #ccc;
        color: #666 !important;
        cursor: not-allowed;
    }
    
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
    .swal2-actions {
        gap: 20px !important;  /* adjust value as needed */
    }


</style>

    <div class="main-content">
        <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                       <div class="col-md-4 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0">List of Recovery Reason Status <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                               <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RR_Excel_Export()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                               <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RR_RightSideFilerOpen()"><i class="bi bi-download fs-17 me-1"></i> Filter</div>
                                 <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="AddorEditRRModal('0',this)"
                                    style="cursor: pointer;">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            
        <div class="table-responsive table-container">
            <div id="loadingOverlay" class="datatable-loading-overlay">
                <div class="loading-spinner"></div>
            </div>
        
            <table id="RecoveryReasonTable_list" class="table text-start" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark">
                            S.No
                        </th>
                        <th scope="col" class="custom-dark">Status Name</th>
                        <th scope="col" class="custom-dark">Created At</th>
                        <th scope="col" class="custom-dark">Updated At</th>
                        <th scope="col" class="custom-dark">Status</th>
                        <th scope="col" class="custom-dark">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white border border-white">
                </tbody>
            </table>
        </div>
    </div>
    
    
    <!--filter ui code -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="RRoffcanvasFilter" aria-labelledby="offcanvasFilterLabel">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasFilterLabel">Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
              
              <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearRecoveryreasonFilters()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyRecoveryreasonFilters()">Apply</button>
                </div>
              
                <!-- Status -->
                <div class="card mb-3">
                   <div class="card-header p-2"><h6 class="custom-dark">Status</h6></div>
                   <div class="card-body">
                       <div class="form-check">
                         <input class="form-check-input" type="radio" name="status" value="all" checked>
                         <label class="form-check-label">All</label>
                       </div>
                       <div class="form-check">
                         <input class="form-check-input" type="radio" name="status" value="1">
                         <label class="form-check-label">Active</label>
                       </div>
                       <div class="form-check">
                         <input class="form-check-input" type="radio" name="status" value="0">
                         <label class="form-check-label">Inactive</label>
                       </div>
                   </div>
                </div>
            
     
           
            
            <!-- Timeline -->
                <div class="card mb-3">
                   <div class="card-header p-2"><h6 class="custom-dark">Select Timeline</h6></div>
                   <div class="card-body">
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="timeline" value="today">
                        <label class="form-check-label">Today</label>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="timeline" value="this_week">
                        <label class="form-check-label">This Week</label>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="timeline" value="this_month">
                        <label class="form-check-label">This Month</label>
                      </div>
                      <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="timeline" value="this_year">
                        <label class="form-check-label">This Year</label>
                      </div>
                   </div>
                </div>
            
                <!-- Date Between -->
                <div class="card mb-3">
                   <div class="card-header p-2"><h6 class="custom-dark">Date Between</h6></div>
                   <div class="card-body">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" id="FromDate" class="form-control mb-2" max="{{date('Y-m-d')}}">
                         <label class="form-label" for="FromDate">To Date</label>
                        <input type="date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}">
                   </div>
                </div>
            
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearRecoveryreasonFilters()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyRecoveryreasonFilters()">Apply</button>
                </div>
        
            <!--<button class="btn btn-success w-100" onclick="applyRecoveryreasonFilters()">Apply</button>-->
            <!--<button class="btn btn-outline-secondary w-100 mt-2" onclick="clearRecoveryreasonFilters()">Clear</button>-->
          </div>
        </div>
        
    <div class="modal fade" id="AddorEditRecoveryReason" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"  aria-labelledby="AddorEditRecoveryReasonLabel" aria-hidden="true">
        <form id="AddorEdit_RR_Form" action="javascript:void(0);" method="POST">
            @csrf
            <input type="hidden" name="edit_id" id="Edit_RR_ID" value="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AddorEditRecoveryReasonLabel">Enter a Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                             <div class="col-12 mb-3">
                                <label class="input-label mb-2 ms-1" for="name">Reason Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="reason_name" id="reason_name" class="form-control" placeholder="Enter a Reason">
                                <!--<textarea class="form-control" rows="5" id="name" name="name"></textarea>-->
                                
                            </div>
                            
                            <div class="col-12">
                                <label for="statusSelect" class="mb-2 ms-1">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control basic-single" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success submitBtn">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @section('script_js')

    <script>
     function RR_RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#RRoffcanvasFilter');
        bsOffcanvas.show();
    }
    
    
    function applyRecoveryreasonFilters() {
        const status = $('input[name="assetType"]:checked').val(); 
        const timeline = $('input[name="STtimeLine"]:checked').val();
        const from_date = $('#FromDate').val();
        const to_date = $('#ToDate').val();
        const city = $('#city_id').val(); 
    
        var table = $('#RecoveryReasonTable_list').DataTable();
        
        table.ajax.reload();
        
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('RRoffcanvasFilter'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }

    
    $(document).ready(function () {
        // Show loading overlay initially
        $('#loadingOverlay').show();
    
        var table = $('#RecoveryReasonTable_list').DataTable({
            pageLength: 15,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.index') }}",
                type: 'GET',
                beforeSend: function() {
                    $('#loadingOverlay').show();
                },
                complete: function() {
                    $('#loadingOverlay').hide();
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    $('#loadingOverlay').hide();
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        toastr.error(xhr.responseJSON.error);
                    } else {
                        toastr.error('Failed to load data. Please try again.');
                    }
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'label_name', name: 'label_name', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
            responsive: false,
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            initComplete: function() {
                $('#loadingOverlay').hide();
    
                // ✅ Checkbox handling
                $('#RecoveryReasonTable_list').on('change', '.row-checkbox', function() {
                    if (!this.checked) {
                        $('#CSelectAllBtn').prop('checked', false);
                    } else {
                        var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                        $('#CSelectAllBtn').prop('checked', allChecked);
                    }
                });
    
                $('#CSelectAllBtn').on('change', function() {
                    $('.row-checkbox').prop('checked', this.checked);
                });
    
                // ✅ Improved search with delay + notification
                let searchDelay;
                let lastNotification;
                let lastSearchTerm = '';
    
                $('#RecoveryReasonTable_list_filter input')
                    .off('keyup')
                    .on('keyup', function() {
                        const searchTerm = this.value.trim();
    
                        clearTimeout(searchDelay);
                        if (lastNotification) {
                            toastr.clear(lastNotification);
                        }
    
                        if (searchTerm === lastSearchTerm) {
                            return;
                        }
    
                        if (searchTerm.length > 0 && searchTerm.length < 3) {
                            searchDelay = setTimeout(() => {
                                lastNotification = toastr.info(
                                    "Please enter at least 3 characters for better results",
                                    { timeOut: 2000 }
                                );
                            }, 500);
                            return;
                        }
    
                        searchDelay = setTimeout(() => {
                            lastSearchTerm = searchTerm;
                            table.search(searchTerm).draw();
                        }, 400);
                    });
            }
        });
    
        // ✅ Error handling
        $.fn.dataTable.ext.errMode = 'none';
        $('#RecoveryReasonTable_list').on('error.dt', function(e, settings, techNote, message) {
            console.error('DataTables Error:', message);
            $('#loadingOverlay').hide();
            toastr.error('Error loading data. Please try again.');
        });
    
        // ✅ Show loading when table redraws
        $('#RecoveryReasonTable_list').on('preDraw.dt', function() {
            $('#loadingOverlay').show();
        });
    
        // ✅ Hide loading + show total count when draw is complete
        $('#RecoveryReasonTable_list').on('draw.dt', function() {
            $('#loadingOverlay').hide();
            var recordsTotal = table.page.info().recordsTotal;
            $('.badge').text(recordsTotal); // optional badge count update
        });
    
        // ✅ Export button
        $('#exportBtn').on('click', function() {
            let search = $('#RecoveryReasonTable_list_filter input').val();
    
            let params = new URLSearchParams({
                search: search
            });
    
            window.location.href = "{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.index') }}?" + params.toString();
        });
    });

    $(document).on('change', '.toggle-status', function (e) {
    e.preventDefault();

    var checkbox = $(this);
    var brandId = checkbox.data('id');
    var intendedStatus = checkbox.is(':checked') ? 1 : 0;

    // Temporarily revert
    checkbox.prop('checked', !intendedStatus);

    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to ${intendedStatus ? 'Activate' : 'Deactivate'} this Reason.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, confirm it!'
    }).then((result) => {
        if (result.isConfirmed) {
            checkbox.prop('checked', intendedStatus);

            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.status_update') }}",
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
                            // Instead of full reload, reload only DataTable
                            // $('#RecoveryReasonTable_list').DataTable().ajax.reload(null, false);
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
            // Cancelled — revert to original
            checkbox.prop('checked', !intendedStatus);
        }
    });
});

    function AddorEditRRModal(id, el) {
        if (id == 0) {
            $("#AddorEditRecoveryReasonLabel").text("Enter a Details");
            $("#AddorEditRecoveryReason").modal('show');
            $("#Edit_RR_ID").val("");
            $('#AddorEditRecoveryReason form').trigger('reset');
            
        } else {
            $("#AddorEditRecoveryReasonLabel").text("Update a Details");
            $(".submitBtn").text('Update');
            console.log(id);
            var reason_name = $(el).data('labelname');
            var status = $(el).data('status');
            console.log(reason_name);
            console.log(status);
            $("#AddorEditRecoveryReason").modal('show');
            $("#Edit_RR_ID").val(id);
            $('#reason_name').val(reason_name);
            $("#edit_status").val(status);
        }
    }
    
    $("#AddorEdit_RR_Form").submit(function(e){
     e.preventDefault();
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token","{{csrf_token()}}");
        var edit_id = $("#Edit_RR_ID").val();
        var submit_url = '';
        if(edit_id != ""){
            submit_url = "{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.data.update',':id') }}";
            submit_url = submit_url.replace(':id',edit_id);
        }else{
            submit_url = "{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.store') }}";
        }
        
        $.ajax({
            url: submit_url,
            type: "POST",
            data:formData,
           contentType: false,
            processData: false, 
            success: function(response) {
                console.log(response);
                if (response.success == true) {
                    $("#AddorEditRecoveryReason").modal('hide');
                    toastr.success(response.message);
                    form.reset();
                    $('#RecoveryReasonTable_list').DataTable().ajax.reload(null, false); 
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
    
     function RR_Excel_Export(){
        let selected = [];
        document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
          selected.push(cb.value);
        });
    
        let params = new URLSearchParams();
        params.append('status', '');
        params.append('from_date', '');
        params.append('to_date', '');
        
        if (selected.length > 0) {
          params.append('selected_ids', JSON.stringify(selected));
        }
    
        const url = `{{ route('admin.Green-Drive-Ev.master_management.recovery_reason.export') }}?${params.toString()}`;
        window.location.href = url;
    }
   

    </script>
    @endsection
    
</x-app-layout>