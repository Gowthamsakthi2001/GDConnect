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
      
    textarea {
        text-align: left !important;
        direction: ltr !important;
    }


.select2-container {
    width: 100% !important;
}

/* Scrollable dropdown */
.select2-results__options {
    max-height: 200px; /* adjust as needed */
    overflow-y: auto;
}
/* Style the visible Select2 container */
.select2-container--bootstrap-5 .select2-selection {
    border: 1px solid #ced4da !important; /* default Bootstrap input border */
    border-radius: 0.375rem !important;   /* match Bootstrap's rounded corners */

}

/* Optional: on focus */
.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #0d6efd !important; /* Bootstrap primary */
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
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


</style>

    
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-8 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> 
                                 Vehicle Transfer Table 
                                 <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$recordsTotal ?? 0}}</span>
                              </div>
                        </div>

                        <div class="col-md-4 d-flex gap-2 align-items-center justify-content-end"> 
                            <div class="col-12 d-flex gap-2 align-items-center justify-content-end">
                                <div class="text-center d-flex gap-2">
                                    <div class="m-2 bg-white p-2 px-3 border-gray" onclick="SelectExportFields()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                                    <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <!-- End Page Header -->

        <div class="table-responsive table-container">
            <div id="loadingOverlay" class="datatable-loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                    <table id="AssetMasterTable_List" class="table text-center" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Transfer ID</th>
                              <th scope="col" class="custom-dark">Transfer Type</th>
                              <th scope="col" class="custom-dark">Total Vehicles</th>
                              <th scope="col" class="custom-dark">Return Vehicles</th>
                              <th scope="col" class="custom-dark">Running Vehicles</th>
                              <th scope="col" class="custom-dark">Transfer Date</th>
                              <th scope="col" class="custom-dark">Return Date</th>
                              <th scope="col" class="custom-dark">Transfer Status</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                          
                        <tbody class="bg-white border border-white" id="vehicle-transfer-tbody">
                        
                           
                        </tbody>
                        </table>
                </div>
    </div>
    
    
    
      <div class="modal fade" id="export_select_fields_modal" tabindex="-1" aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-between">
                  <div>
                    <h1 class="h3 fs-5 text-center custom-dark" id="export_select_fields_modalLabel">Select Fields</h1>
                  </div>
                  <div>
                      <button type="button" class="btn text-white" style="background:#26c360;" onclick="ExportAssetMasterData()">Download</button>
                  </div>
                </div>
                <div class="modal-body p-md-3">
                  <div class="row px-4">
                      <div class="col-md-3 col-12 mb-3">
                          <div class="d-flex justify-content-between align-items-center">
                            <label class="form-check-label mb-0 text-dark fw-bold h6" for="field1">Select All</label>
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input get-export-label" type="checkbox" id="field1" value="">
                            </div>
                          </div>
                        </div>
                  </div>
                  <div class="row p-4">
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">Transfer ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field2" value="transfer_id">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Transfer Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field3" value="transfer_type">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Total Vehicles</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field4" value="total_vehicles">
                        </div>
                      </div>
                    </div>
                                        
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Return Vehicles</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field6" value="return_vehicles">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Running Vehicles</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field5" value="running_vehicles">
                        </div>
                      </div>
                    </div>
                    

                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Transfer Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field8" value="transfer_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Return Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field9" value="return_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">Transfer Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field10" value="transfer_status">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Vehicle Transfer Detail View</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field11" value="vehicle_transfer_detail_view">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Transfer Logs</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field12" value="vehicle_transfer_logs">
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
     <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Vehicle Transfer Table Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetMasterFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Asset Status</h6></div>
               </div>
               <div class="card-body">
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_1" value="all" {{$status == "all" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_1">
                        All
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_3" value="active" {{$status == "active" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_3">
                       Active
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_4" value="closed" {{$status == "closed" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_4">
                       Closed
                      </label>
                    </div>
                   
                    
               </div>
           </div>
           
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Chassis Number</h6></div>
               </div>
               <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="chassis_number">Chassis Number</label>
                              <select class="form-select custom-select2-field" name="chassis_number" id="chassis_number">
                                <option value="" {{ empty($chassis_number) ? 'selected' : '' }}>Select</option>
                                @if(isset($passed_chassis_nos))
                           
                                  @foreach($passed_chassis_nos as $val)
                                 <option value="{{$val->chassis_number}}" {{ $chassis_number == $val->chassis_number ? 'selected' : '' }}>{{$val->chassis_number}}</option>
                                @endforeach
                                @endif
                             </select>
                    </div>
               </div>
            </div>

           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1" value="today" {{$timeline == "today" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2" value="this_week" {{$timeline == "this_week" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3" value="this_month" {{$timeline == "this_month" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4" value="this_year" {{$timeline == "this_year" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine4">
                       This Year
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetMasterFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
      

@section('script_js')



<script>
$(document).ready(function () {
    $('#loadingOverlay').show();

    var table = $('#AssetMasterTable_List').DataTable({
        pageLength: 15,
        lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
        pagingType: "simple",
        dom: '<"top"lf>rt<"bottom"ip>',
        responsive: false,
        scrollX: true,
        searching: true,
        serverSide: true, // ✅ Enable server-side processing
        processing: true,
        ajax: {
            url: "{{ route('admin.asset_management.vehicle_transfer.log_and_history') }}",
            type: 'GET',
            data: function (d) {
                d.status = $('input[name="assetType"]:checked').val();
                d.timeline = $('input[name="STtimeLine"]:checked').val();
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
                d.chassis_number = $('#chassis_number').val(); // Changed from city to chassis_number
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
                toastr.error('Failed to load data. Please try again.');
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'id' },
            { data: 'transfer_type' },
            { data: 'total_vehicles' },
            { data: 'return_vehicles' },
            { data: 'running_vehicles' },
            { data: 'transfer_date' },
            { data: 'return_date' },
            { data: 'status', orderable: false },
            { data: 'action', orderable: false, searchable: false }
        ],
        initComplete: function () {
            // Improved search with validation
            $('#loadingOverlay').hide();
            let searchDelay;
            let lastNotification;
            let lastSearchTerm = '';

            $('#AssetMasterTable_List_filter input')
                .off('keyup') // remove default DataTables handler
                .on('keyup', function () {
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

    // Filter functions
    window.applyAssetMasterFilter = function() {
        table.ajax.reload();
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) bsOffcanvas.hide();
    };

    window.clearAssetMasterFilter = function() {
        $('input[name="assetType"][value="all"]').prop('checked', true);
        $('input[name="STtimeLine"]').prop('checked', false);
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#chassis_number').val('').trigger('change');
        table.ajax.reload();
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) bsOffcanvas.hide();
    };
});


function getStatusBadge(status) {
    switch(status) {
        case 'pending': return '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Pending Asset';
        case 'uploaded': return '<i class="bi bi-circle-fill" style="color:#1661c7;"></i> Asset Uploaded';
        case 'accepted': return '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Asset Accepted';
        case 'rejected': return '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Asset Rejected';
        default: return 'N/A';
    }
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
        const checkboxes = document.querySelectorAll('.get-export-label');
    
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
        $('#export_select_fields_modal').modal('show');
    }
    
    function ExportAssetMasterData() {
        const selectedStatus = document.querySelector('input[name="assetType"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
    
        const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
        const timeline = selectedTimeline ? selectedTimeline.value : '';
    
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
       const chassis_no = document.getElementById('chassis_number').value; // ✅ Corrected line
    
        let req_ids = [];
        $('input[name="is_select[]"]:checked').each(function () {
            req_ids.push($(this).val());
        });
    
        let get_export_labels = [];
        $('.get-export-label:checked').each(function () {
            get_export_labels.push($(this).val());
        });
    
        if (get_export_labels.length === 0) {
            toastr.error("Please select at least one label Name.");
            return;
        }
    
        // Create form
        var form = $('<form>', {
            method: 'POST',
            action: "{{ route('admin.asset_management.vehicle_transfer.export_detail') }}"
        });
    
        // CSRF Token
        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));
    
        // Append selected IDs
        req_ids.forEach(function (id) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'get_ids[]',
                value: id
            }));
        });
    
        // Append selected export labels
        get_export_labels.forEach(function (label) {
            form.append($('<input>', {
                type: 'hidden',
                name: 'get_export_labels[]',
                value: label
            }));
        });
    
        // Append filter values
        form.append($('<input>', { type: 'hidden', name: 'status', value: status }));
        form.append($('<input>', { type: 'hidden', name: 'timeline', value: timeline }));
        form.append($('<input>', { type: 'hidden', name: 'from_date', value: from_date }));
        form.append($('<input>', { type: 'hidden', name: 'to_date', value: to_date }));
         form.append($('<input>', { type: 'hidden', name: 'chassis_number', value: chassis_no })); // ✅ Added
    
        // Submit form
        form.appendTo('body').submit();
    }



   
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
        
            // Wait for the offcanvas to be fully shown before initializing Select2
    document.getElementById('offcanvasRightHR01').addEventListener('shown.bs.offcanvas', function () {
        const selectFields = document.querySelectorAll('.custom-select2-field');
        if (selectFields.length > 0) {
            $(selectFields).select2({
                width: '100%',
                dropdownParent: $('#offcanvasRightHR01') // IMPORTANT
            });

            if (selectFields.length > 1) {
                selectFields.forEach(function (el) {
                    el.classList.add('multi-select2'); 
                });
            }
        }
    }, { once: true }); // run only once per open
    }
    
    


    
   
</script>
@endsection
</x-app-layout>
