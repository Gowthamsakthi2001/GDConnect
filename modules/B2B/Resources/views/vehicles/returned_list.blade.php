@extends('layouts.b2b')
@section('css')
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
@section('content')
    <div class="main-content">
        <div class="card bg-transparent mb-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3 align-items-center">
                    <!-- Title -->
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="card-title h5 custom-dark m-0 text-center text-md-start">
                            Returned List
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
     <!--<div id="loadingOverlay" class="datatable-loading-overlay">-->
     <!--                   <div class="loading-spinner"></div>-->
     <!--               </div>-->
                    <table id="returnList" class="table text-center table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                    <label class="form-check-label" for="CSelectAllBtn"></label>
                                  </div>
                                </th>
                                <th class="custom-dark">Request ID</th>
                                <th class="custom-dark">Accountability Type</th>
                                <th class="custom-dark">Vehicle No</th>
                                <th class="custom-dark">Chassis No</th>
                                <th class="custom-dark">Vehicle Type</th>
                                <th class="custom-dark">Vehicle Model</th>
                                <th class="custom-dark">Vehicle Make</th>
                                <th class="custom-dark">Rider Name</th>
                                <th class="custom-dark">Contact Details</th>
                                <!--<th class="custom-dark">Client</th>-->
                                <th class="custom-dark">City</th>
                                <th class="custom-dark">Zone</th>
                                <th class="custom-dark">Created Date and Time</th>
                                <th class="custom-dark">Updated Date and Time</th>
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
                        <label class="form-check-label mb-0" for="field4">Accountability Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="accountability_type" name="accountability_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Vehicle Number</label>
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
                        <label class="form-check-label mb-0" for="field4">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_type" name="vehicle_type">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Vehicle Model</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_model" name="vehicle_model">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Vehicle Make</label>
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
                       
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field8">Client Name</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="client" name="client">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
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
                        <label class="form-check-label mb-0" for="field12">Reason</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="reason" name="reason">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field12">Kilometer Value</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="kilometer_value" name="kilometer_value">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Odometer Value</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="odometer_value" name="odometer_value">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field12">Kilometer Image</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="kilometer_image" name="kilometer_image">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Odometer Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="odometer_image" name="odometer_image">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_front" name="vehicle_front">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_back" name="vehicle_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Top</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_top" name="vehicle_top">
                        </div>
                      </div>
                    </div>
                    
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field12">Vehicle Bottom</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_bottom" name="vehicle_bottom">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Left</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_left" name="vehicle_left">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Right</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_right" name="vehicle_right">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Battery</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_battery" name="vehicle_battery">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Vehicle Charger</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_charger" name="vehicle_charger">
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
                        <label class="form-check-label mb-0" for="field12">Closed By</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="closed_by" name="closed_by">
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
                    
    
                
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
        
        
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Returned List</h5>
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
                        <select name="datefilter" id="quick_date_filter" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="last_15_days">Last 15 Days</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 date-container">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3 date-container">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
  
               </div>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Options</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="v_type">Accountability Type</label>
                        
                                <label class="mb-0">
                                    <input type="checkbox" id="accountabilitytype_select_all">
                                    Select All
                                </label>
                            </div>
                        <select name="accountability_type" id="accountabilitytype" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select Type</option>-->
                            @if(isset($accountability_types))
                            @foreach($accountability_types as $type)
                            <option value="{{$type->id}}" >{{$type->name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
                    @if($guard == 'master')
                    <div class="mb-3">
                       <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="zone_id">Zone</label>
                        
                                <label class="mb-0">
                                    <input type="checkbox" id="zone_id_select_all">
                                    Select All
                                </label>
                            </div>
                        <select name="zone_id" id="zone_id" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select Zone</option>-->
                            @if(isset($zones))
                            @foreach($zones as $zone)
                            <option value="{{$zone->id}}" >{{$zone->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                       <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_type">Vehicle Type</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_type_select_all">
                                Select All
                            </label>
                        </div>
                        <select name="v_type" id="v_type" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select</option>-->
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                <option value="{{$val->id}}" >{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_model">Vehicle Model</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_model_select_all">
                                Select All
                            </label>
                        </div>
                        <select name="v_model" id="v_model" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select</option>-->
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}">{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                         <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_make">Vehicle Make</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_make_select_all">
                                Select All
                            </label>
                        </div>
                        <select name="v_make" id="v_make" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select</option>-->
                            @if(isset($vehicle_makes))
                                @foreach($vehicle_makes as $val)
                                <option value="{{$val}}" >{{$val}}</option>
                                @endforeach
                            @endif
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
@endsection
@section('js')

<script>
    function initSelectAll(selector, checkboxSelector) {

    // Select/Deselect all via checkbox
    $(checkboxSelector).on('change', function () {
        if (this.checked) {
            let values = [];
            $(selector + ' option').each(function () {
                values.push($(this).val());
            });
            $(selector).val(values).trigger('change');
        } else {
            $(selector).val(null).trigger('change');
        }
    });

    // Auto sync checkbox based on user actions
    $(selector).on('change', function () {
        let total = $(selector + ' option').length;
        let selected = $(selector).val() ? $(selector).val().length : 0;

        if (selected === total) {
            $(checkboxSelector).prop('checked', true);
        } else {
            $(checkboxSelector).prop('checked', false);
        }
    });
}

$(document).ready(function () {

    initSelectAll('#v_type', '#v_type_select_all');
    initSelectAll('#v_model', '#v_model_select_all');
    initSelectAll('#v_make', '#v_make_select_all');
    initSelectAll('#accountabilitytype', '#accountabilitytype_select_all');
    initSelectAll('#status_value', '#status_value_select_all');
    initSelectAll('#zone_id', '#zone_id_select_all');

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
    //   $('#returnList').DataTable({
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
    
  

    var table; // Declare globally
    
    function applyRiderFilter() {
        const from_date = $('#FromDate').val();
        const to_date = $('#ToDate').val();
    
        if ((from_date && !to_date) || (!from_date && to_date)) {
            toastr.error("Both From Date and To Date are required");
            return;
        }
    
        table.ajax.reload(); // reload DataTable with new filters
        
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }
    
    function clearRiderFilter() {
        $('#FromDate').val('');
        $('#ToDate').val('');
    $('#city_id').val(null).trigger('change'); // 🔹 reset city + trigger change
    $('#accountabilitytype').val(null).trigger('change');
     $('#status_value').val(null).trigger('change');
     $('#v_model').val(null).trigger('change');
    $('#v_make').val(null).trigger('change');
    $('#v_type').val(null).trigger('change');
    $('#zone_id').val(null).trigger('change');
    // $('#zone_id').html('<option value="">Select Zone</option>').trigger('change'); // 🔹 reset zones + trigger change
        table.ajax.reload();
        
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }

  
  
     $(document).ready(function () {
        // $('#loadingOverlay').show();
    
         table = $('#returnList').DataTable({
            pageLength: 25,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('b2b.returned.list') }}",
                type: 'GET',
                 data: function (d) {
                d.city_id = $('#city_id').val();
                d.zone_id = $('#zone_id').val();
                d.status = $('#status_value').val();
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
                d.datefilter = $('#quick_date_filter').val();
                d.accountability_type = $('#accountabilitytype').val();
                d.vehicle_model = $('#v_model').val();
                d.vehicle_type = $('#v_type').val();
                d.vehicle_make = $('#v_make').val();
                },
            
                beforeSend: function () {
                    $('#returnList tbody').html(`
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
                    $('#returnList tbody').html(`
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
            { data: 0, orderable: false, searchable: false }, // Checkbox
            { data: 1 }, // Request Id
            { data: 2 }, // Vehicle No
            { data: 3 }, // Chassis No
            { data: 4 }, // Rider Name
            { data: 5 }, // Contact Details
            // { data: 6 }, // Client
            { data: 6 }, // City
            { data: 7 }, // Zone
            { data: 8 }, // Created Date
            { data: 9 }, // Updated Date
            { data: 10 }, // Created By
            { data: 11 }, // Status
            { data: 12 }, // Status
            { data: 13 },
            { data: 14 },
            { data: 15 },
            { data: 16, orderable: false, searchable: false } // Action
            ],
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            // initComplete: function () {
            //     $('#loadingOverlay').hide();
    
            //     // Checkbox handling
            //     $('#returnList').on('change', '.sr_checkbox', function () {
            //         $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
            //     });
    
            //     $('#CSelectAllBtn').on('change', function () {
            //         $('.sr_checkbox').prop('checked', this.checked);
            //     });
            // }
        });
    
        // Error handling for DataTables
        // $.fn.dataTable.ext.errMode = 'none';
        // $('#returnList').on('error.dt', function (e, settings, techNote, message) {
        //     console.error('DataTables Error:', message);
        //     $('#loadingOverlay').hide();
        //     toastr.error('Error loading data. Please try again.');
        // });
    
        // // Show loading overlay during redraw
        // $('#returnList').on('preDraw.dt', function () {
        //     $('#loadingOverlay').show();
        // });
    
        // $('#returnList').on('draw.dt', function () {
        //     $('#loadingOverlay').hide();
        // });
    });


    function getZones(CityID) {
        let ZoneDropdown = $('#zone_id');
    
        ZoneDropdown.empty().append('<option value="">Loading...</option>');
    
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">Select Zone</option>');
    
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
        }
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
    
    

   
    const fromDate = document.getElementById('FromDate').value;
    const toDate   = document.getElementById('ToDate').value;
    const datefilter   = document.getElementById('quick_date_filter').value;
     const vehicle_make   = getMultiValues('#v_make');
    const vehicle_model = getMultiValues('#v_model');
    const vehicle_type  = getMultiValues('#v_type');
    const accountability_type = getMultiValues('#accountability_type');
    const zone_id = getMultiValues('#zone_id');

    // ✅ Build query params
    // const params = new URLSearchParams();
 
    // if (fromDate) params.append('from_date', fromDate);
    // if (toDate) params.append('to_date', toDate);
    // if (datefilter) params.append('datefilter', datefilter);
    // if (vehicle_model) params.append('vehicle_model', vehicle_model);
    // if (vehicle_make) params.append('vehicle_make', vehicle_make);
    // if (vehicle_type) params.append('vehicle_type', vehicle_type);
    // if (accountability_type) params.append('accountability_type', accountability_type);
    // if (zone_id) params.append('zone_id', zone_id);
    
    // status
        // appendMultiSelect(params, 'status', status);
        
        // vehicle filters
    //     appendMultiSelect(params, 'vehicle_model', vehicle_model);
    //     appendMultiSelect(params, 'vehicle_make', vehicle_make);
    //     appendMultiSelect(params, 'vehicle_type', vehicle_type);
        
    //     // others
    //     appendMultiSelect(params, 'accountability_type', accountability_type);
    //     appendMultiSelect(params, 'zone_id', zone_id);
        
    // // append IDs
    // selected.forEach(id => params.append('selected_ids[]', id));

    // // append fields
    // selectedFields.forEach(f => params.append('fields[]', f));
    
    
    // const url = `{{ route('b2b.returned_export') }}?${params.toString()}`;
    // window.location.href = url;
    
    const data = {
        from_date: fromDate,
        to_date: toDate,
        datefilter: datefilter,
        vehicle_model:vehicle_model,
        vehicle_make:vehicle_make,
        vehicle_type:vehicle_type,
        zone_id: zone_id,
        accountability_type:accountability_type,
        selected_ids: selected,
        fields: selectedFields
    };

    // Show Bootstrap modal
    $("#export_select_fields_modal").modal('hide');
    var exportmodal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportmodal.show();

    $.ajax({
        url: "{{ route('b2b.returned_export') }}",
        method: "GET",
        data: data,
        xhrFields: { responseType: 'blob' },
        success: function(blob) {

            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "returned_list-" + new Date().toISOString().split('T')[0] + ".csv";
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
    $(document).ready(function () {

        function toggleDates() {
            if ($('#quick_date_filter').val() === 'custom') {
                $('.date-container').show();
            } else {
                $('.date-container').hide();
                $('#FromDate').val('');
                $('#ToDate').val('');
            }
        }

        // On change
        $('#quick_date_filter').on('change', toggleDates);

        // On page load (for old values)
        toggleDates();
    });
</script>



@endsection