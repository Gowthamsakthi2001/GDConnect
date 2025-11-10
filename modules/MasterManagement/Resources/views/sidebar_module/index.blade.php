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
                            <div class="card-title h5 custom-dark m-0">List of Modules <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="window.location.href='{{route('admin.Green-Drive-Ev.master_management.sidebar_module.create')}}'"
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
        
            <table id="ModuleTable_list" class="table text-start" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark">
                            S.No
                        </th>
                        <th scope="col" class="custom-dark">Image</th>
                        <th scope="col" class="custom-dark">Module Name</th>
                        <th scope="col" class="custom-dark">Assigned Roles</th>
                        <th scope="col" class="custom-dark">Status</th>
                        <th scope="col" class="custom-dark">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white border border-white">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
    
    
    <!--filter ui code -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFilter" aria-labelledby="offcanvasFilterLabel">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasFilterLabel">Filter Zones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
              
              <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearZoneFilters()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyZoneFilters()">Apply</button>
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
                    <button class="btn btn-outline-secondary w-50" onclick="clearZoneFilters()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyZoneFilters()">Apply</button>
                </div>
        
            <!--<button class="btn btn-success w-100" onclick="applyZoneFilters()">Apply</button>-->
            <!--<button class="btn btn-outline-secondary w-100 mt-2" onclick="clearZoneFilters()">Clear</button>-->
          </div>
        </div>
        
            
    
    @section('script_js')

    <script>
$(document).ready(function () {
    // Show loading overlay initially
    $('#loadingOverlay').show();

    var table = $('#ModuleTable_list').DataTable({
        pageLength: 15,
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.Green-Drive-Ev.master_management.sidebar_module.index') }}",
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
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'module_name', name: 'module_name' },
            { data: 'view_roles_id', name: 'view_roles_id' },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    if (type === 'display') {
                        return row.status == "1" ? "<span class='text-success'>Active</span>" : "<span class='text-danger'>Inactive</span>";
                    }
                    return data; 
                }
            },

            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
        responsive: false,
        scrollX: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        initComplete: function() {
            $('#loadingOverlay').hide();

            // ✅ Checkbox handling
            $('#ModuleTable_list').on('change', '.row-checkbox', function() {
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

            $('#ModuleTable_list_filter input')
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
    $('#ModuleTable_list').on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

    // ✅ Show loading when table redraws
    $('#ModuleTable_list').on('preDraw.dt', function() {
        $('#loadingOverlay').show();
    });

    // ✅ Hide loading + show total count when draw is complete
    $('#ModuleTable_list').on('draw.dt', function() {
        $('#loadingOverlay').hide();
        var recordsTotal = table.page.info().recordsTotal;
        $('.badge').text(recordsTotal); // optional badge count update
    });

    // ✅ Export button
    $('#exportBtn').on('click', function() {
        let search = $('#ModuleTable_list_filter input').val();

        let params = new URLSearchParams({
            search: search
        });

        window.location.href = "{{ route('admin.Green-Drive-Ev.master_management.sidebar_module.index') }}?" + params.toString();
    });
});


    </script>
    @endsection
    
</x-app-layout>