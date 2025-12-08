<x-app-layout>
@section('style_css')
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

    
    #serviceList td, 
    #serviceList th {
      text-align: center;           /* horizontal center */
      vertical-align: middle !important; /* vertical center */
    }
    
    /* If you need switches/buttons to center inside td */
    #serviceList td .form-check,
    #serviceList td .d-flex {
      justify-content: center;
    }
        
        table td .form-check {
          margin: 0;              /* remove extra margin */
          padding: 0;             /* remove default padding */
          display: flex;          /* make it flexible */
          justify-content: center;/* center horizontally */
          align-items: center;    /* center vertically */
          height: 100%;           /* take full cell height */
        }
        
        /* Resize and recolor the switch */
        table td .form-check-input.custom-switch {
          width: 2.5em;
          height: 1.3em;
          cursor: pointer;
        }
        
        table td .form-check-input.custom-switch:checked {
          background-color: #28a745 !important; /* green */
          border-color: #28a745 !important;
        }
        
        /* Green focus ring */
        .form-check-input.custom-switch:focus {
          box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
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
    


        /* ---------- Pagination container: force single-line flex layout ---------- */
    .dataTables_wrapper .dataTables_paginate {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end; /* change to center if you want it centered */
      flex-wrap: nowrap;
      white-space: nowrap;
      margin-top: 12px;
    }
    
    /* Style Previous / Next only */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 100px;   /* ðŸ”¹ make both equal width */
      height: 40px;
      padding: 8px 16px;  /* ðŸ”¹ more padding for balanced look */
      border-radius: 6px;
      border: none;
      color: #fff !important;
      background-color: #0d6efd;
      cursor: pointer;
      font-weight: 500;
    }
    
    /* Hover */
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
      background-color: #0b5ed7;
    }
    
    /* Disabled */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
      background-color: #e9ecef;
      color: #6c757d !important;
      cursor: not-allowed;
    }
</style>
@endsection
    <div class="main-content">
        <div class="card bg-transparent mb-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3 align-items-center">
                    <!-- Title -->
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="card-title h5 custom-dark m-0 text-center text-md-start">
                            Service List
                        </div>
                    </div>
        
                    <!-- Action buttons -->
                    <div class="col-12 col-md-6 d-flex flex-wrap gap-2 align-items-center justify-content-center justify-content-md-end">
                        <div class="bg-white p-2 px-3 border-gray rounded text-center">
                            <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                <i class="bi bi-download fs-17 me-1"></i> Export
                            </button>
                        </div>
                        <div class="bg-white p-2 px-3 border-gray rounded text-center" onclick="RightSideFilerOpen()">
                            <i class="bi bi-filter fs-17"></i> Filters
                        </div>
                    </div>
                </div>
            </div>
        </div>

        



        <div class="table-responsive">
    
                    <table id="serviceList" class="table text-left table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                    <label class="form-check-label" for="CSelectAllBtn"></label>
                                  </div>
                                </th>
                                <th class="custom-dark">Request Id</th>
                                <th class="custom-dark">Ticket Id</th>
                                <th class="custom-dark">Accountablity Type</th>
                                <th class="custom-dark">Vehicle No</th>
                                <th class="custom-dark">Chassis No</th>
                                <th class="custom-dark">Vehicle Type</th>
                                <th class="custom-dark">Vehicle Model</th>
                                <th class="custom-dark">Vehicle Make</th>
                                <th class="custom-dark">Rider Name</th>
                                <th class="custom-dark">Contact Details</th>
                                <th class="custom-dark">Client</th>
                                 <th class="custom-dark">City</th>
                                 <th class="custom-dark">Zone</th>
                                <th class="custom-dark">Created Date and Time</th>
                                <th class="custom-dark">Updated Date and Time</th>
                                <th class="custom-dark">Created By</th>
                                <th class="custom-dark">Aging</th>
                                <th class="custom-dark">Status</th>
                                <th class="custom-dark">Action</th>
                              </tr>
                            </thead>
                            
                            <tbody class="border border-white">

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
                        <label class="form-check-label mb-0" for="field3">Request ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="req_id" name="req_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Ticket ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="ticket_id" name="ticket_id">
                        </div>
                      </div>
                    </div>
                    <!--updated by logesh-->
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_id">Accountablity Type</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="accountability_type" id="accountability_type">
                          </div>
                        </div>
                      </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Vehicle_number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_no" name="vehicle_no">
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
                        <label class="form-check-label mb-0" for="field5">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_type" name="vehicle_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Vehicle Model</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_model" name="vehicle_model">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Vehicle Make</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_make" name="vehicle_make">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Rider Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="rider_name" name="rider_name">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Contact_details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="mobile_no" name="mobile_no">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Client Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client" name="client">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="city" name="city">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Zone</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="zone" name="zone">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">POC Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="poc_name" name="poc_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">POC Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="poc_number" name="poc_number">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Description</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="description" name="description">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Repair Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="repair_type" name="repair_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Location</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="location" name="location">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Created By</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="created_by" name="created_by">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="status" name="status">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                        <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Created Date & Time</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="created_at" name="created_at">
                        </div>
                      </div>
                    </div>
                    
                       <div class="col-md-3 col-12 mb-3">
    
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="updated_at">Updated Date & Time</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="updated_at" name="updated_at">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="aging">Aging</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="aging" name="aging">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Service List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
             <div class="card mb-3">
               <div class="card-header p-2">
                   <h6 class="custom-dark">Quick Date Filter</h6>
               </div>
               <div class="card-body">
 
                     <div class="mb-3">
                        <label class="form-label" for="quick_date_filter">Select Date Range</label>
                        <select id="quick_date_filter" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="last_15_days">Last 15 Days</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
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
                   <div><h6 class="custom-dark">Select Option</h6></div>
               </div>
               <div class="card-body">
                   
                     <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id" class="form-control custom-select2-field" onchange="getZones()" multiple>
                            <option value="" disabled>Select City</option>
                            <option value="all">All</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}" >{{$city->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Zone</label>
                        <select name="zone_id" id="zone_id" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Zone</option>

                        </select>
                    </div>
                    
                    
                                        
                    <div class="mb-3">
                        <label class="form-label" for="v_type">Vehicle Type</label>
                        <select name="v_type" id="v_type" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                <option value="{{$val->id}}">{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_model">Vehicle Model</label>
                        <select name="v_model" id="v_model" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}">{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_make">Vehicle Model</label>
                        <select name="v_make" id="v_make" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_makes))
                                @foreach($vehicle_makes as $val)
                                <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">Accountability Type</label>
                        <select name="accountability_type" id="accountability_type_1" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Type</option>
                            <option value="all">All</option>
                            @if(isset($accountability_types))
                            @foreach($accountability_types as $type)
                            <option value="{{$type->id}}" >{{$type->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">Customer</label>
                        <select name="customer_master" id="customer_master" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Customer</option>
                            <option value="all">All</option>
                            @if(isset($customers))
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}" >{{$customer->trade_name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    
                    
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Status</label>
                        <select name="status_value" id="status_value" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            <option value="unassigned">Un-Assigned</option>
                            <option value="inprogress">In Progress</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
               </div>
            </div>
            

            
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearRiderFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyRiderFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        

@section('script_js')

<script>
function initSelectAll(selector) {
    let internalChange = false;

    // store previous selection when user interacts (before change fires)
    $(document).on('mousedown touchstart', selector, function () {
        // save previous selection as data on element
        const prev = $(this).val() || [];
        $(this).data('prevSelection', prev);
    });

    // For keyboard interactions (focus + key), also capture focus
    $(document).on('focus', selector, function () {
        const prev = $(this).val() || [];
        $(this).data('prevSelection', prev);
    });

    $(selector).on('change', function () {
        if (internalChange) return;

        const $el = $(this);
        let prev = $el.data('prevSelection') || [];
        let current = $el.val() || [];

        // normalize to strings (safety)
        prev = prev.map(String);
        current = current.map(String);

        internalChange = true;

        // CASE A: previously had "all" and now user added other values
        // -> remove "all" and keep newly selected items
        if (prev.includes('all') && current.includes('all') && current.length > 1) {
            // user had all, then clicked another option: we keep the other options
            const cleaned = current.filter(v => v !== 'all');
            $el.val(cleaned).trigger('change.select2');
            // update stored prev
            $el.data('prevSelection', cleaned);
            internalChange = false;
            return;
        }

        // CASE B: user selected "all" after having other items -> keep only 'all'
        if (!prev.includes('all') && current.includes('all')) {
            // user selected "all" now, so keep only all
            $el.val(['all']).trigger('change.select2');
            $el.data('prevSelection', ['all']);
            internalChange = false;
            return;
        }

        // CASE C: user selected "all" + others in one action OR current has all+others
        // -> prefer KEEPING only 'all'
        if (current.includes('all') && current.length > 1) {
            $el.val(['all']).trigger('change.select2');
            $el.data('prevSelection', ['all']);
            internalChange = false;
            return;
        }

        // CASE D: user selected other items (no 'all') -> ensure 'all' removed (if present)
        if (!current.includes('all')) {
            const cleaned = current.filter(v => v !== 'all');
            // if cleaned differs from current, set it (safety)
            if (cleaned.length !== current.length) {
                $el.val(cleaned).trigger('change.select2');
                $el.data('prevSelection', cleaned);
                internalChange = false;
                return;
            }
        }

        // default -> just store current as prev
        $el.data('prevSelection', current);
        internalChange = false;
    });
}




$(document).ready(function () {
    initSelectAll('#status_value');
    initSelectAll('#customer_master');
    initSelectAll('#accountability_type_1');
    initSelectAll('#v_make');
    initSelectAll('#v_model');
    initSelectAll('#v_type');
    initSelectAll('#zone_id');
    initSelectAll('#city_id');
});


</script>

<script>
    
    
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
  
    //   $(document).ready(function () {
    //   $('#serviceList').DataTable({
    //         dom: 'Blfrtip',
    //         dom: 'frtip',
    //         buttons: ['excel', 'pdf', 'print'],
    //         order: [[0, 'desc']],
    //         columnDefs: [
    //             { orderable: false, targets: '_all' }
    //         ],
    //         lengthMenu: [ [25, 50, 100, 250, -1], [25, 50, 100, 250, "All"] ],
    //         responsive: false,
    //         scrollX: true,
    //     });
    // });
</script>


<script>
$(document).ready(function () {

    var serviceList = $('#serviceList').DataTable({
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('b2b.admin.service_request.list') }}",
            type: "GET",
            data: function (d) {
                // Pass date filters to the server
                d.city_id = $('#city_id').val();
                d.zone_id = $('#zone_id').val();
                d.from_date = $('#FromDate').val();
                d.date_filter = $('#quick_date_filter').val();
                d.to_date = $('#ToDate').val();
                d.status = $('#status_value').val();
               d.vehicle_type = $('#v_type').val();
                d.vehicle_model = $('#v_model').val();
                d.vehicle_make = $('#v_make').val();
                d.accountability_type = $('#accountability_type_1').val(); //updated by logesh
                d.customer_id = $('#customer_master').val(); //updated by logesh
            },
            beforeSend: function () {
                $('#serviceList tbody').html(`
                  <tr>
                    <td colspan="14" class="text-center p-4">
                      <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                `);
            },
            error: function () {
                $('#serviceList tbody').html(`
                  <tr>
                    <td colspan="14" class="text-center text-danger p-4">
                      <i class="bi bi-exclamation-triangle"></i> 
                      Failed to load data. Please try again.
                    </td>
                  </tr>
                `);
            }
        },
        columns: [
            { data: 0, className: 'text-center' },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 },
            { data: 9 },
            { data: 10 },
            { data: 11 },
            { data: 12 },
            { data: 13 },
            { data: 14 },
            { data: 15 },
            { data: 16 },
            { data: 17 },
            { data: 18 },
            { data: 19, className: 'text-center', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1, 5] }
        ],
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        responsive: false,
        scrollX: true
    });

    // ✅ Apply filter button
    window.applyRiderFilter = function() {
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
        
        serviceList.ajax.reload();
        
    }

    // ✅ Clear filter button
    window.clearRiderFilter = function() {
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id').val([]).trigger('change');
        $('#zone_id').val([]).trigger('change');
        $('#status_value').val([]).trigger('change');
        $('#v_type').val([]).trigger('change');
        $('#v_model').val([]).trigger('change');
        $('#v_make').val([]).trigger('change');
        
        $('#accountability_type_1').val([]).trigger('change'); //updated by logesh
        $('#customer_master').val([]).trigger('change');
        $('#quick_date_filter').val('').trigger('change');
        toggleDateFields();
        
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
        
        serviceList.ajax.reload();
    }
    
              // -------------------------------
    // SHOW/HIDE DATE FIELDS
    // -------------------------------
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
});
</script>

<script>
      document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('export_select_fields_modal');
        const selectAll = modal.querySelector('#field1'); // select-all checkbox inside modal
        const checkboxes = modal.querySelectorAll('.export-field-checkbox'); // only modal checkboxes
    
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
    
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            $('.custom-select2-field').select2({
              dropdownParent: $('#offcanvasRightHR01') // Fix for offcanvas
            });
            modal.show();
        });
    });
    
    
        

    
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    



  document.getElementById('export_download').addEventListener('click', function () {
 
      
    const selected = [];
    const selectedFields = [];
    
    document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
            selectedFields.push(cb.name);
        });
    
    document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
      selected.push(cb.value);
    });

   
    if (selectedFields.length === 0) {
        toastr.error("Please select at least one export field.");
        return;
    }
    
    const fromDate = document.getElementById('FromDate')?.value || '';
    const toDate   = document.getElementById('ToDate')?.value || '';
    // const city   = document.getElementById('city_id')?.value || '';
    // const zone   = document.getElementById('zone_id')?.value || '';
    // const status = document.getElementById('status_value').value;
    // const customer_id = document.getElementById('customer_master').value;
    // const accountability_type   = document.getElementById('accountability_type_1').value;
    
    // const vehicle_type   = document.getElementById('v_type').value;
    // const vehicle_model   = document.getElementById('v_model').value;
    const date_filter   = document.getElementById('quick_date_filter').value;
    const status = getMultiValues('#status_value');
    const vehicle_make   = getMultiValues('#v_make');
    const vehicle_model = getMultiValues('#v_model');
    const vehicle_type  = getMultiValues('#v_type');
    const accountability_type = getMultiValues('#accountability_type_1');
    const zone_id = getMultiValues('#zone_id');
    const city_id = getMultiValues('#city_id');
    const customer_id = getMultiValues('#customer_master');

    const params = new URLSearchParams();
    
    //     if (vehicle_type) params.append('vehicle_type', vehicle_type);
    // if (vehicle_model) params.append('vehicle_model', vehicle_model);
    if (date_filter) params.append('date_filter', date_filter);
 
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);
    // if (zone) params.append('zone_id', zone);
    // if (city) params.append('city_id', city);
    // if (status) params.append('status', status);
    // if (customer_id) params.append('customer_id', customer_id);
    // if (accountability_type) params.append('accountability_type', accountability_type);
    // append IDs
    
        appendMultiSelect(params, 'status', status);
        
        // vehicle filters
        appendMultiSelect(params, 'vehicle_model', vehicle_model);
        appendMultiSelect(params, 'vehicle_make', vehicle_make);
        appendMultiSelect(params, 'vehicle_type', vehicle_type);
        
        // others
        appendMultiSelect(params, 'accountability_type', accountability_type);
        appendMultiSelect(params, 'zone_id', zone_id);
        appendMultiSelect(params, 'city_id', city_id);
        appendMultiSelect(params, 'customer_id', customer_id);
        
    selected.forEach(id => params.append('selected_ids[]', id));

    // append fields
    selectedFields.forEach(f => params.append('fields[]', f));
    
    const url = `{{ route('b2b.admin.service_request.export') }}?${params.toString()}`;
    window.location.href = url;
  });

        function appendMultiSelect(params, key, values) {
            if (values && values.length > 0) {
                values.forEach(v => params.append(key + '[]', v));
            }
        }
        function getMultiValues(selector) {
            return Array.from(document.querySelectorAll(selector + ' option:checked'))
                        .map(option => option.value);
        }


</script>
<script>
function getZones() {
    let cityIds = $('#city_id').val();  
    let ZoneDropdown = $('#zone_id');

    ZoneDropdown.empty().append('<option value="">Loading...</option>');

    if (cityIds && cityIds.length > 0) {

        $.ajax({
            url: "{{ route('global.get_multi_city_zones') }}",
            type: "GET",
            data: { city_id: cityIds },  // pass array
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
                    ZoneDropdown.append('<option value="" disabled>No Zones available</option>');
                }
            },
            error: function () {
                ZoneDropdown.empty().append('<option value="" disabled>Error loading zones</option>');
            }
        });

    } else {
        ZoneDropdown.empty().append('<option value="" disabled>Select a city first</option>');
    }
}
</script>


@endsection
</x-app-layout>