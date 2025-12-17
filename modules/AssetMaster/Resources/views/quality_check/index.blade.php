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

</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                       <div class="col-md-4 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.asset_management.asset_master.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Total QC Inspection <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);" id="Qc_Filter_Count">{{ $totalRecords ?? 0 }}</span></div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" >
                                    <!--<a href="{{route('admin.asset_management.quality_check.export_quality_check', ['status' => $status, 'from_date' => $from_date, 'to_date' => $to_date , 'timeline' => $timeline])}}" class=" bg-white text-dark"><i class="bi bi-download fs-17 me-1"></i> Export</a>-->
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>

                                    </div>
                                <a href="{{route('admin.asset_management.quality_check.bulk_upload_form')}}" class="m-2 bg-white p-2 px-3 border-gray text-dark"><i class="bi bi-upload fs-17 me-1"></i> Bulk Upload</a>
                                
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="window.location.href='{{route('admin.asset_management.quality_check.add_quality_check')}}'"
                                    style="cursor: pointer;">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>
                        
                        <div class="col-md-6 col-12 d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn custom-btn btn-round btn-sm " href="{{ route('admin.asset_management.quality_check.Excel_download') }}">
                                <i class="bi bi-download"></i> Bulk Demo
                            </a>
                            <a href="{{ route('admin.asset_management.quality_check.quality_check_import_verify') }}" class="btn custom-btn btn-round btn-sm">
                                <i class="bi bi-eye"></i> Import Verify
                            </a>
                           
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
 
                    
                    <table id="QualityCheckTable_List" class="table text-center" style="width: 100%;">
    <thead class="bg-white rounded" style="background:white !important; color:black !important;">
        <tr>
            <th scope="col" class="custom-dark">
                <div class="form-check">
                    <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                    <label class="form-check-label" for="CSelectAllBtn"></label>
                </div>
            </th>
            <th scope="col" class="custom-dark">QC ID</th>
            <th scope="col" class="custom-dark">Vehicle Type</th>
            <th scope="col" class="custom-dark">Vehicle Model</th>
            <th scope="col" class="custom-dark">City</th>
            <th scope="col" class="custom-dark">Zone</th>
            <th scope="col" class="custom-dark">Accountability Type</th>
            <th scope="col" class="custom-dark">Chassis No</th>
            <th scope="col" class="custom-dark">Battery No</th>
            <th scope="col" class="custom-dark">Telematics No</th>
            <th scope="col" class="custom-dark">Motor No</th>
            <th scope="col" class="custom-dark">Current Status</th>
            <th scope="col" class="custom-dark">Action</th>
        </tr>
    </thead>
    <tbody class="bg-white border border-white">
        <!-- Data will be loaded via AJAX -->
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
                      <button type="button" class="btn text-white" style="background:#26c360;" id="export_download">Download</button>
                  </div>
                </div>
                <div class="modal-body p-md-3">
                  <div class="row p-4">
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field1">Select All</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field1">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_type" id="vehicle_type">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Vehicle Model</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_model" name="vehicle_model">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="location" name="location">
                        </div>
                      </div>
                    </div>
                    
                                        <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Zone</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="zone" name="zone">
                        </div>
                      </div>
                    </div>
                    
                    
                                        <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Accountability Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="accountability_type" name="accountability_type">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Customer</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="customer" name="customer">
                        </div>
                        </div>
                      </div>
            
                    
                    
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Chassis Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="chassis_number" name="chassis_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Battery Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="battery_number" name="battery_number">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Telematics Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="telematics_number" name="telematics_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Motor Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="motor_number" name="motor_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Date and Time</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="date_time" name="date_time">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field10">Image</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="image" name="image">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Result</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="result" name="result">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Qc Checklists</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="qc_checklist" name="qc_checklist">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Quality Check</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearQualityCheckFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyQualityCheckFilter()">Apply</button>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
                     <div class="mb-3">
                        <label class="form-label" for="quick_date_filter">Select Date Range</label>
                        <select id="quick_date_filter" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="last_15_days">Last 15 Days</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
               </div>
            </div>
            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                   
                     <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status" value="all" {{ request('status') === 'all' || request('status') === null ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                    
                    
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status1" value="pass" {{ request('status') === 'pass' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status1">
                       Pass
                      </label>
                    </div>
                    
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status2"  value="fail"   {{ request('status') === 'fail' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status2">
                        Fail
                      </label>
                    </div>
                    
                                        
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status3"  value="qc_pending"   {{ request('status') === 'qc_pending' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status3">
                       QC Pending
                      </label>
                    </div>
                    
                   
                    
               </div>
           </div>

           
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Option</h6></div>
               </div>
               <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="v_type">Vehicle Type</label>
                        <select name="v_type[]" id="v_type" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Type</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                <option value="{{$val->id}}" {{ $vehicle_type == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_model">Vehicle Model</label>
                        <select name="v_model[]" id="v_model" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Model</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" {{ $vehicle_model == $val->id ? 'selected' : '' }}>{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="v_make">Vehicle Make</label>
                        <select name="v_make[]" id="v_make" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Make</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" {{ $vehicle_model == $val->id ? 'selected' : '' }}>{{$val->make}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                    <div class="mb-3">
                        <label class="form-label" for="location_id">Select City</label>
                        <select name="location_id[]" id="location_id" class="form-control custom-select2-field" onchange="getMultiZones()" multiple>
                            <option value="" disabled>Select City</option>
                            <option value="all">All</option>
                            @if(isset($location_data))
                                @foreach($location_data as $l)
                                <option value="{{$l->id}}" {{ $location == $l->id ? 'selected' : '' }}>{{$l->city_name}}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Select Zone</label>
                        <select name="zone_id[]" id="zone_id" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select a city first</option>
                        </select>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" for="accountability_type_id">Select Accountability Type</label>
                        <select name="accountability_type_id" id="accountability_type_id" class="form-control custom-select2-field">
                            <option value="">All</option>
                            @if(isset($accountablity_types))
                                @foreach($accountablity_types as $type)
                                <option value="{{$type->id}}" >{{$type->name ?? ''}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
               
            </div>
    
            <div class="card mb-3 d-none" id="CustomerSection">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Customer</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="customer_id">Customer</label>
                        <select name="customer_id[]" id="customer_id" class="form-control custom-select2-field" multiple>
                             <option value="" disabled>Select Customer</option>
                            <option value="all">All</option>
                            @if(isset($customers))
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}" {{ $customer_id == $customer->id ? 'selected' : '' }}>{{$customer->trade_name ?? ''}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearQualityCheckFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyQualityCheckFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
          <!--Export Loader-->
        
          <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content text-center p-3" style="border-radius:12px;background-color: #f8f9fa;">
        
                <div class="modal-header border-0">
                  <h5 class="modal-title w-100">Export in progress</h5>
                </div>
        
                <div class="modal-body d-flex justify-content-center">
                  <img src="{{ asset('admin-assets/export_excel.gif') }}"
                    alt="Loading..."
                    style="width:350px; height:auto; object-fit:contain;">
                </div>
        
              </div>
            </div>
          </div>
        
        
      

        
        
        

    

@section('script_js')

<script>
function toggleDateFields() {
    let value = $("#quick_date_filter").val();

    if (value === "custom") {
        $("#FromDate").closest(".mb-3").show();
        $("#ToDate").closest(".mb-3").show();
    } else {
        $("#FromDate").closest(".mb-3").hide();
        $("#ToDate").closest(".mb-3").hide();
        $("#FromDate").val("");
        $("#ToDate").val("");
    }
}

toggleDateFields();

$("#quick_date_filter").on("change", function () {
    toggleDateFields();
});
function applyQualityCheckFilter() {
    // Get filter values
    var timeline = $("#quick_date_filter").val();
    var from_date = $("#FromDate").val();
    var to_date = $("#ToDate").val();

    if (timeline === "custom") {
        timeline = '';
    } else {
        fromDate = '';
        toDate = '';
    }
    const status = $('input[name="status"]:checked').val();
    const location = $('#location_id').val();
    const vehicle_type = $('#v_type').val();
    const vehicle_model = $('#v_model').val();
    const vehicle_make = $('#v_make').val();
    if (from_date || to_date) {
        if (!from_date || !to_date) {
            toastr.error("Both From Date and To Date are required when filtering by date");
            return;
        }
    }

    // Reload DataTable with new parameters
    $('#QualityCheckTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

function clearQualityCheckFilter() {
    // Reset all filter inputs
    $('input[name="status"][value="all"]').prop('checked', true);
    $("#quick_date_filter").val('').trigger('change');
    toggleDateFields();
    $('#location_id').val([]).trigger('change');
        $('#zone_id').val([]).trigger('change');
    $('#customer_id').val([]).trigger('change');
    $('#v_type').val([]).trigger('change');
    $('#v_model').val([]).trigger('change');
    $('#v_make').val([]).trigger('change');
    $('#accountability_type_id').val('').trigger('change');
    // Reload DataTable with cleared filters
    $('#QualityCheckTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}
function initSelectAll(selector) {
          let internalChange = false;
          $(document).on("mousedown touchstart", selector, function () {
            const prev = $(this).val() || [];
            $(this).data("prevSelection", prev);
          });
          $(document).on("focus", selector, function () {
            const prev = $(this).val() || [];
            $(this).data("prevSelection", prev);
          });
          $(selector).on("change", function () {
            if (internalChange) return;
            const $el = $(this);
            let prev = $el.data("prevSelection") || [];
            let current = $el.val() || [];
            prev = prev.map(String);
            current = current.map(String);
            internalChange = true;
            if (prev.includes("all") && current.includes("all") && current.length > 1) {
              const cleaned = current.filter((v) => v !== "all");
              $el.val(cleaned).trigger("change.select2");
              $el.data("prevSelection", cleaned);
              internalChange = false;
              return;
            }
            if (!prev.includes("all") && current.includes("all")) {
              $el.val(["all"]).trigger("change.select2");
              $el.data("prevSelection", ["all"]);
              internalChange = false;
              return;
            }
            if (current.includes("all") && current.length > 1) {
              $el.val(["all"]).trigger("change.select2");
              $el.data("prevSelection", ["all"]);
              internalChange = false;
              return;
            }
            if (!current.includes("all")) {
              const cleaned = current.filter((v) => v !== "all");
              if (cleaned.length !== current.length) {
                $el.val(cleaned).trigger("change.select2");
                $el.data("prevSelection", cleaned);
                internalChange = false;
                return;
              }
            }
            $el.data("prevSelection", current);
            internalChange = false;
          });
    }
$(document).ready(function () {
  initSelectAll("#v_type");
  initSelectAll("#v_model");
  initSelectAll("#v_make");
  initSelectAll("#v_model");
  initSelectAll("#v_make");
  initSelectAll("#location_id");
  initSelectAll("#zone_id");
  initSelectAll("#customer_id");
});
function getMultiZones() {
      let cityIds = $("#location_id").val();
      console.log(cityIds);
      let ZoneDropdown = $("#zone_id");
      ZoneDropdown.empty().append('<option value="">Loading...</option>');
      if (cityIds && cityIds.length > 0) {
        $.ajax({
          url: "{{ route('global.get_multi_city_zones') }}",
          type: "GET",
          data: { city_id: cityIds }, // pass array
          success: function (response) {
            ZoneDropdown.empty()
              .append('<option value="" disabled>Select Zone</option>')
              .append('<option value="all">All</option>');
            if (response.data && response.data.length > 0) {
              $.each(response.data, function (key, zone) {
                ZoneDropdown.append(
                  `<option value="${zone.id}">${zone.name}</option>`
                );
              });
            } else {
              ZoneDropdown.append(
                '<option value="" disabled>No Zones available</option>'
              );
            }
          },
          error: function () {
            ZoneDropdown.empty().append(
              '<option value="" disabled>Error loading zones</option>'
            );
          },
        });
      } else {
        ZoneDropdown.empty().append(
          '<option value="" disabled>Select a city first</option>'
        );
      }
    }
$(document).ready(function () {
    // Show loading overlay initially
    $('#loadingOverlay').show();

    var table = $('#QualityCheckTable_List').DataTable({
        pageLength: 15,
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.asset_management.quality_check.list') }}",
            type: 'GET',
            data: function (d) {
                // Add filter parameters to the AJAX request
                d.status = $('input[name="status"]:checked').val();
                d.from_date = $('#FromDate').val() || '';
                d.to_date   = $('#ToDate').val() || '';
                d.timeline  = $('#quick_date_filter').val() || ''; 
                d.location = $('#location_id').val() || [];
                d.zone = $('#zone_id').val() || [];
                d.customer = $('#customer_id').val() || [];
                d.vehicle_type = $('#v_type').val() || [];
                d.vehicle_model = $('#v_model').val() || [];
                d.vehicle_make = $('#v_make').val() || [];
                d.accountability_type = $('#accountability_type_id').val();
            },
            beforeSend: function() {
                // Show loading overlay when AJAX starts
                $('#loadingOverlay').show();
            },
            complete: function(data) {
                // Hide loading overlay when AJAX completes
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
            { data: 'id', name: 'id' },
            { data: 'vehicle_type', name: 'vehicle_type' },
            { data: 'vehicle_model', name: 'vehicle_model' },
            { data: 'location', name: 'location' },
            { data: 'zone', name: 'zone' },
            { data: 'accountability_type', name: 'accountability_type' },
            { data: 'chassis_number', name: 'chassis_number' },
            { data: 'battery_number', name: 'battery_number' },
            { data: 'telematics_number', name: 'telematics_number' },
            { data: 'motor_number', name: 'motor_number' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
        responsive: false,
        scrollX: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        initComplete: function() {
            console.log("hiiii data new");
            // Hide loading overlay when table is initialized
            $('#loadingOverlay').hide();
            
            // Checkbox handling
            $('#QualityCheckTable_List').on('change', '.sr_checkbox', function() {
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
            
            
            
            $('#QualityCheckTable_List_filter input')
                .off('keyup')
                .on('keyup', function() {
                    const searchTerm = this.value.trim();
                    
                    // Clear previous timeouts and notifications
                    clearTimeout(searchDelay);
                    if (lastNotification) {
                        toastr.clear(lastNotification);
                    }
                    
                    // Skip if same as last search
                    if (searchTerm === lastSearchTerm) {
                        return;
                    }
                    
                    // Validate search term length
                    if (searchTerm.length > 0 && searchTerm.length < 4) {
                        searchDelay = setTimeout(() => {
                            lastNotification = toastr.info(
                                "Please enter at least 4 characters for better results",
                                {timeOut: 2000}
                            );
                        }, 500);
                        return;
                    }
                    
                    // Perform search if valid length or empty
                    searchDelay = setTimeout(() => {
                        lastSearchTerm = searchTerm;
                        table.search(searchTerm).draw();
                    }, 400);
                });
        },
        drawCallback: function(settings) {
            // Access the response JSON
            var response = settings.json;
            if (response) {
                // $('#totalRecordsSpan').text(response.recordsTotal);
                $('#Qc_Filter_Count').text(response.recordsFiltered);
            }
        }
    });

    // Initialize Select2 for location dropdown
    $('#location_id').select2({
        width: '100%',
        dropdownParent: $('#offcanvasRightHR01')
    });

    // Error handling
    $.fn.dataTable.ext.errMode = 'none';
    $('#QualityCheckTable_List').on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

    // Show loading when table is being redrawn
    $('#QualityCheckTable_List').on('preDraw.dt', function() {
        $('#loadingOverlay').show();
    });

    // Hide loading when table draw is complete
    $('#QualityCheckTable_List').on('draw.dt', function() {
        $('#loadingOverlay').hide();
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('export_select_fields_modal');
    const selectAll = modal.querySelector('#field1'); // "Select All" checkbox
    const checkboxes = modal.querySelectorAll('.form-check-input:not(#field1)'); // All other checkboxes

    // When "Select All" is clicked
    selectAll.addEventListener('change', function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    });

    // When any checkbox changes, update the "Select All" state
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
        });
    });
});
</script>


<script>
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen() {
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        
        // Show the offcanvas
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

<script>
  document.getElementById('export_download').addEventListener('click', function () {
 
      
    const selected = [];
    const selectedFields = [];
    
    document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
      selectedFields.push(cb.name);
    });


    
    document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
      selected.push(cb.value);
    });
    
    // document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
    //   selectedFields.push(cb.name); 
    // });


    if (selectedFields.length === 0) {
        toastr.error("Please select at least one export field.");
        return;
    }
    function cleanArray(val) {
        if (!Array.isArray(val)) return [];
        return val.filter(v => v !== 'all');
    }
    var from_date = $('#FromDate').val() || '';
    var to_date   = $('#ToDate').val() || '';
    var timeline  = $('#quick_date_filter').val() || '';
    
    if (timeline === 'custom') {
        timeline = '';
    }

    // const params = new URLSearchParams();
    // params.append('status', $('#status').val() || '');
    // params.append('from_date', from_date);
    // params.append('to_date', to_date);
    // params.append('timeline', timeline);
    
    // params.append('location', JSON.stringify(cleanArray($('#location_id').val() || [])));
    // params.append('zone', JSON.stringify(cleanArray($('#zone_id').val() || [])));
    // params.append('vehicle_type', JSON.stringify(cleanArray($('#v_type').val() || [])));
    // params.append('vehicle_model', JSON.stringify(cleanArray($('#v_model').val() || [])));
    // params.append('vehicle_make', JSON.stringify(cleanArray($('#v_make').val() || [])));
    // params.append('customer', JSON.stringify(cleanArray($('#customer_id').val() || [])));
    // params.append('accountability_type', $('#accountability_type_id').val() || '');

    // if (selected.length > 0) {
    //   params.append('selected_ids', JSON.stringify(selected));
    // }
    // if (selectedFields.length > 0) {
    //   params.append('fields', JSON.stringify(selectedFields));
    // }

    // const url = `{{ route('admin.asset_management.quality_check.export_quality_check') }}?${params.toString()}`;
    // window.location.href = url;
    
    
     const data = {
        _token: "{{ csrf_token() }}",

        status: $('#status').val() || '',
        from_date: from_date,
        to_date: to_date,
        timeline: timeline,

        location: cleanArray($('#location_id').val() || []),
        zone: cleanArray($('#zone_id').val() || []),
        vehicle_type: cleanArray($('#v_type').val() || []),
        vehicle_model: cleanArray($('#v_model').val() || []),
        vehicle_make: cleanArray($('#v_make').val() || []),
        customer: cleanArray($('#customer_id').val() || []),
        accountability_type: $('#accountability_type_id').val() || '',

        selected_ids: selected,
        fields: selectedFields
    };

    // Show Export Loading Modal
    $("#export_select_fields_modal").modal("hide");
    var exportmodal = new bootstrap.Modal(document.getElementById("exportModal"));
    exportmodal.show();

   $.ajax({
        url: "{{ route('admin.asset_management.quality_check.export_quality_check') }}",
        method: "GET",
        data: data,
        xhrFields: { responseType: 'blob' },
        success: function(blob) {
 
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "quality_check-" + new Date().toISOString().split('T')[0] + ".xlsx";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
 
            exportmodal.hide();
        },
        error: function() {
            toastr.error("Network connection failed. Please try again.");
            exportmodal.hide();
        }
    });

  });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
    });
    
 function DeleteRecord(id, redirect = window.location.href) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want delete this QC record",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Enter remarks here...',
        inputAttributes: {
            rows: 4
        },
        showCancelButton: true,
        cancelButtonColor: '#6c757d',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
        preConfirm: (remarks) => {
            if (!remarks || !remarks.trim()) {
                Swal.showValidationMessage('Remarks are required');
            }
            return remarks.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const Remarks = result.value;
            $.ajax({
                url: "{{ route('admin.asset_management.quality_check.destroy') }}",
                type: "POST",
                data: {
                    id: id,
                    remarks: Remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Deleted! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 2000
                        });

                        setTimeout(function() {
                            window.location.href = redirect;
                        }, 1000);
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Warning! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 3000
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error! The network connection has failed. Please try again later.',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 3000
                    });
                }
            });
        }
    });
}

function getZones(CityID) {
        let ZoneDropdown = $('#zone_id');
        ZoneDropdown.empty().append('<option value="">Loading...</option>');
          
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
                        
                        const selectedZone = "{{ $zone_id ?? '' }}";
                        if (selectedZone) {
                        ZoneDropdown.val(selectedZone).trigger('change');
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
            // ZoneWrapper.hide();
        }
      
      
    }
    
    
    $(document).ready(function () {
        const existingCity = "{{ $location ?? '' }}";
            if (existingCity) {
                getZones(existingCity);
            }
    });
    
    $(document).ready(function() {
    function toggleCustomerSection() {
        const selectedType = $('#accountability_type_id').val();
        if (selectedType == '2') {
            $('#CustomerSection').removeClass('d-none'); // Show section
        } else {
            $('#CustomerSection').addClass('d-none'); // Hide section
            $('#customer_id').val([]).trigger('change');
        }
    }
    // Run on page load in case a value is pre-selected
    toggleCustomerSection();
    // Run on change
    $('#accountability_type_id').on('change', function() {
        toggleCustomerSection();
    });
});


</script>

</script>




@endsection
</x-app-layout>
