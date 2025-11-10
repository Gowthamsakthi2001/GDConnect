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

</style>

    <div class="main-content">
        <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                       <div class="col-md-4 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0">List of Zones <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" >
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>

                                    </div>
         
                                
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="window.location.href='{{route('admin.Green-Drive-Ev.zone.zone')}}'"
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
        
            <table id="ZoneTable_List" class="table text-start" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark">
                            <div class="form-check">
                                <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value=""
                                    id="CSelectAllBtn"
                                    title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                <label class="form-check-label" for="CSelectAllBtn"></label>
                            </div>
                        </th>
                        <th scope="col" class="custom-dark">Zone Name</th>
                        <th scope="col" class="custom-dark">State Name</th>
                        <th scope="col" class="custom-dark">City Name</th>
                        <th scope="col" class="custom-dark">Status</th>
                        <th scope="col" class="custom-dark">Status Action</th>
                        <th scope="col" class="custom-dark">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white border border-white">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
    @section('script_js')

    <script>
        $(document).ready(function () { 
            // Show loading overlay initially
            $('#loadingOverlay').show();
        
            var table = $('#ZoneTable_List').DataTable({
                pageLength: 15,
                pagingType: "simple",
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.Green-Drive-Ev.zone.render.list') }}",
                    type: 'GET',
                    data: function (d) {
                        d.status = $('input[name="status"]:checked').val();
                        d.from_date = $('#FromDate').val();
                        d.to_date = $('#ToDate').val();
                        d.timeline = $('input[name="STtimeLine"]:checked').val();
                        d.location = $('#location_id').val();
                    },
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
                    { data: 'zoneName', name: 'zoneName' },
                    { data: 'stateName', name: 'stateName' },
                    { data: 'cityName', name: 'cityName' },
                    { data: 'status', name: 'status' },
                    { data: 'statusToggleAction', name: 'statusToggleAction', orderable: false, searchable: false },
                    { data: 'ActionBtns', name: 'ActionBtns', orderable: false, searchable: false }
                ],
                lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
                responsive: false,
                scrollX: true,
                dom: '<"top"lf>rt<"bottom"ip>',
                initComplete: function() {
                    $('#loadingOverlay').hide();
                    
                    // Checkbox handling
                    $('#ZoneTable_List').on('change', '.sr_checkbox', function() {
                        if (!this.checked) {
                            $('#CSelectAllBtn').prop('checked', false);
                        } else {
                            var allChecked = $('.sr_checkbox:checked').length === $('.sr_checkbox').length;
                            $('#CSelectAllBtn').prop('checked', allChecked);
                        }
                    });
        
                    $('#CSelectAllBtn').on('change', function() {
                        $('.sr_checkbox').prop('checked', this.checked);
                    });
                    
                    // Improved search with validation
                    let searchDelay;
                    let lastNotification;
                    let lastSearchTerm = '';
                    
                    $('#ZoneTable_List_filter input')
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
                            
                            if (searchTerm.length > 0 && searchTerm.length < 4) {
                                searchDelay = setTimeout(() => {
                                    lastNotification = toastr.info(
                                        "Please enter at least 4 characters for better results",
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
        
            // Error handling
            $.fn.dataTable.ext.errMode = 'none';
            $('#ZoneTable_List').on('error.dt', function(e, settings, techNote, message) {
                console.error('DataTables Error:', message);
                $('#loadingOverlay').hide();
                toastr.error('Error loading data. Please try again.');
            });
        
            // Show loading when table is being redrawn
            $('#ZoneTable_List').on('preDraw.dt', function() {
                $('#loadingOverlay').show();
            });
        
            // Hide loading when table draw is complete
            $('#ZoneTable_List').on('draw.dt', function() {
                $('#loadingOverlay').hide();
            });
        });

    </script>
    @endsection
    
</x-app-layout>