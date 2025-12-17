@extends('layouts.b2b')
@section('css')
<style>
    .form-check-input:checked {
        background-color: #0f62fe !important;
        border-color: #0f62fe !important;
    }
    
    table thead th {
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
    
    .badge-vehicle {
      width: 64px;           
      height: 27px;          
      border-radius: 50px;   
      border: 1px solid #6d28d9;  
      background-color: #ede9fe;  
      color: #6d28d9;            
      padding: 4px 8px;     
      font-size: 14px;
      font-weight: 400;      
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .badge-rider {
      width: 160px;             
      height: 27px;              
      border-radius: 50px;       
      border: 1px solid #0e7490;  
      background-color: #cffafe;  
      color: #0e7490;             
      padding: 4px 8px;           
      font-size: 14px;
      font-weight: 400;           
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .badge-status {
      width: 200px;         
      height: 27px;          
      border-radius: 50px;   
      border: 1px solid;     
      padding: 4px 8px;
      font-size: 14px;
      font-weight: 400;      
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;              
    }
    
    /* Individual status styles */
    .badge-running {
      background-color: #d1fae5;
      color: #047857;
      border-color: #047857;
    }
    
    .badge-ticket {
      background-color: #dbeafe;
      color: #1d4ed8;
      border-color: #1d4ed8;
    }
    
    .badge-return-request {
      background-color: #fef9c3;
      color: #b45309;
      border-color: #b45309;
    }
    
    .badge-returned {
      background-color: #DAA1DB;
      color: #7B14A3;
      border-color: #7B14A3;
    }
    
    .badge-gdm-init {
      background-color: #FFCFBE;
      color: #c2410c;
      border-color: #c2410c;
    }
    
    .badge-gdm-recovered {
      background-color: #FFEEBE;
      color: #A6911D;
      border-color: #A6911D;
    }
    
    .badge-accident {
      background-color: #FFC1BE;
      color: #DC2626;
      border-color: #DC2626;
    }
    
    .badge-client-request {
      background-color: #FFFFBE;
      color: #8F971E;
      border-color: #8F971E;
    }
    
    .badge-client-recovered {
      background-color: #DCFFBE;
      color: #68971E;
      border-color: #68971E;
    }
    
    .action-icons .btn {
      padding: 2px;       
      margin: 0;          
      line-height: 1;     
    }

    .action-icons .d-flex {
      gap: 0.25rem;  
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
                            Vehicle List 
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
            
                <div id="loadingOverlay" class="datatable-loading-overlay">
                        <div class="loading-spinner"></div>
                    </div>
                    
            <table id="AssignTable_List" class="table text-center table-striped table-bordered table-hover" style="width: 100%;">
                <thead class="bg-white rounded">
                    <tr>
                        <th scope="col" class="custom-dark">
                            <div class="form-check">
                                <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                <label class="form-check-label" for="CSelectAllBtn"></label>
                            </div>
                        </th>
                        <th scope="col" class="custom-dark text-center">Request ID</th>
                        <th scope="col" class="custom-dark text-center">Accountability Type</th>
                        <th scope="col" class="custom-dark text-center">Rider Name</th>
                        <th scope="col" class="custom-dark text-center">Rider Contact</th>
                        <th scope="col" class="custom-dark text-center">Vehicle No</th>
                        <th scope="col" class="custom-dark text-center">Vehicle Type</th>
                        <th scope="col" class="custom-dark text-center">Vehicle Model</th>
                        <th scope="col" class="custom-dark text-center">Vehicle Make</th>
                        <th scope="col" class="custom-dark text-center">City</th>
                        <th scope="col" class="custom-dark text-center">Zone Name</th>
                        <th scope="col" class="custom-dark text-center">Handover Type</th>
                        <th scope="col" class="custom-dark text-center">Handover Date and Time</th>
                        <th scope="col" class="custom-dark text-center">Status</th>
                        <th scope="col" class="custom-dark text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white border border-white">
                </tbody>
                </tbody>
            </table>
        </div>
        
        <!--Filters-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
                <div class="offcanvas-header">
                    <h5 class="custom-dark" id="offcanvasRightHR01Label">Vehicle List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <!--<div class="d-flex gap-2 mb-3">-->
                    <!--    <button class="btn btn-outline-secondary w-50" onclick="clearAssignFilter()">Clear All</button>-->
                    <!--    <button class="btn btn-success w-50" onclick="applyAssignFilter()">Apply</button>-->
                    <!--</div>-->
                    
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
                            <div><h6 class="custom-dark">Select Status</h6></div>
                        </div>
                      <div class="card-body">
         
                            <div class="mb-3">
                            
                                <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="assignment_status">Status</label>
                        
                                <label class="mb-0">
                                    <input type="checkbox" id="assignment_status_select_all">
                                    Select All
                                </label>
                            </div>
                                <select name="assignment_status" id="assignment_status" class="form-control custom-select2-field" multiple> 
                                    <!--<option value="">All</option>-->
                                    <option value="running">Running</option>
                                    <option value="under_maintenance">Under Maintenance</option>
                                    <option value="recovery_request">Recovery Request</option>
                                    <option value="recovered">Recovered</option>
                                    <option value="accident">Accident</option>
                                    <option value="return_request">Return Request</option>
                                </select>
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
                                    <input type="checkbox" id="accountability_type_select_all">
                                    Select All
                                </label>
                            </div>
                                <select name="accountability_type" id="accountability_type" class="form-control custom-select2-field" multiple>
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
                                <select name="zone_id" id="zone_id_1" class="form-control custom-select2-field" multiple>
                                    <!--<option value="">Select Zone</option>-->
                                    @if(!empty($zones))
                                    @foreach($zones as $zone)
                                    <option value='{{$zone->id}}'>{{$zone->name}}</option>
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
                            @foreach($vehicle_types as $val)
                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                            @endforeach
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
                                <option value="{{$val->id}}" >{{$val->vehicle_model}}</option>
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
                                <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>


                       </div>
                    </div>
                    
                    <!-- Date Filter -->
                    <!--<div class="card mb-3">-->
                    <!--    <div class="card-header p-2">-->
                    <!--        <div><h6 class="custom-dark">Date Between</h6></div>-->
                    <!--    </div>-->
                    <!--    <div class="card-body">-->
                    <!--        <div class="mb-3">-->
                    <!--            <label class="form-label" for="FromDate">From Date</label>-->
                    <!--            <input type="date" name="from_date" id="FromDate" class="form-control" max="{{ date('Y-m-d') }}">-->
                    <!--        </div>-->
                    <!--        <div class="mb-3">-->
                    <!--            <label class="form-label" for="ToDate">To Date</label>-->
                    <!--            <input type="date" name="to_date" id="ToDate" class="form-control" max="{{ date('Y-m-d') }}">-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <div class="d-flex gap-2 mb-3">
                        <button class="btn btn-outline-secondary w-50" onclick="clearAssignFilter()">Clear All</button>
                        <button class="btn btn-success w-50" onclick="applyAssignFilter()">Apply</button>
                    </div>
                </div>
            </div>
        
        <!--Exports-->
            <div class="modal fade" id="export_select_fields_modal" tabindex="-1" aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl">
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
                          <label class="form-check-label mb-0" for="vehicle_id">Request ID</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="request_id" id="request_id">
                          </div>
                        </div>
                      </div>
                      
                                            
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Accountability Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="accountability_type" name="accountability_type">
                        </div>
                      </div>
                    </div>
                    
                    
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_id">Vehicle ID</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_id" id="vehicle_id">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="chassis_number">Chassis Number</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="chassis_number" id="chassis_number">
                          </div>
                        </div>
                      </div>
                      
                        <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_number">Vehicle Number</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_number" id="vehicle_number">
                          </div>
                        </div>
                      </div>
                      
                    <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_model">Vehicle Model</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_model" id="vehicle_model">
                          </div>
                        </div>
                      </div>
                      
                        <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_make">Vehicle Make</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_make" id="vehicle_make">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_type">Vehicle Type</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_type" id="vehicle_type">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="handover_type">Handover Type</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="handover_type" id="handover_type">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="handover_time">Handover Time</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="handover_time" id="handover_time">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="city">City</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="city" id="city">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="zone">Zone</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="zone" id="zone">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="customer_name">Customer Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="customer_name" name="customer_name">
                        </div>
                      </div>
                    </div>
                    
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="status">Status</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="status" id="status">
                          </div>
                        </div>
                      </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Rider Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="name" name="name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Mobile Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="mobile_no" name="mobile_no">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Email ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="email" name="email">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">DOB</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="dob" name="dob">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Adhar Card Front Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="adhar_front" name="adhar_front">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Adhar Card Back Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="adhar_back" name="adhar_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Adhar Card Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="adhar_number" name="adhar_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Pan Card Front Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="pan_front" name="pan_front">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Pan Card Back Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="pan_back" name="pan_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Pan Card Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="pan_number" name="pan_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Driving License Front Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="driving_license_front" name="driving_license_front">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Driving License Back Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="driving_license_back" name="driving_license_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Driving License Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="driving_license_number" name="driving_license_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">LLR Img</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="llr_image" name="llr_image">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">LLR Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="llr_number" name="llr_number">
                        </div>
                      </div>
                    </div>
                    
                                            <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="status">Status</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="status" id="status">
                          </div>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                </div>
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
    initSelectAll('#accountability_type', '#accountability_type_select_all');
    initSelectAll('#assignment_status', '#assignment_status_select_all');
    initSelectAll('#zone_id_1', '#zone_id_select_all');

});
</script>


<script>
    $(document).ready(function () {
    // Select all functionality for export fields
    $('#field1').on('change', function() {
        $('.export-field-checkbox').prop('checked', this.checked);
    });
    
    // Individual checkbox functionality
    $('.export-field-checkbox').on('change', function() {
        if (!this.checked) {
            $('#field1').prop('checked', false);
        } else if ($('.export-field-checkbox:checked').length === $('.export-field-checkbox').length) {
            $('#field1').prop('checked', true);
        }
    });
    
    // Your existing code below
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
    
    
    




    
    // $('#AssignTable_List').DataTable({
    //     lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
    //     responsive: false,
    //     scrollX: true,
    //     columnDefs: [
    //         { orderable: false, targets: [0, 9] }
    //     ]
    // });
    
    // Export button click handler
    $('#exportBtn').on('click', function () {
        $('#export_select_fields_modal').modal('show');
    });
    
    
    // Download button handler
    // $('#export_download').on('click', function() {
    //     // Get selected fields
    //     const selectedFields = [];
    //     $('.export-field-checkbox:checked').each(function() {
    //         selectedFields.push($(this).attr('name'));
    //     });
        
    //     // Here you would typically make an AJAX call to your backend
    //     // to generate the export file with the selected fields
    //     console.log('Selected fields for export:', selectedFields);
        
    //     // Close the modal
    //     $('#export_select_fields_modal').modal('hide');
        
    //     // Show a success message
    // });
});

// Filters

// Function to open the filter offcanvas
function RightSideFilerOpen() {
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
    
    //     const accountability_type   = document.getElementById('accountability_type').value;
    // const status   = document.getElementById('assignment_status').value;

   
    const fromDate = document.getElementById('FromDate').value;
    const toDate   = document.getElementById('ToDate').value;
    // const vehicle_model   = document.getElementById('v_model').value;
    // const vehicle_make   = document.getElementById('v_make').value;
    // const vehicle_type   = document.getElementById('v_type').value;
    
    const status = getMultiValues('assignment_status');
    const vehicle_make   = getMultiValues('#v_make');
    const vehicle_model = getMultiValues('#v_model');
    const vehicle_type  = getMultiValues('#v_type');
    const accountability_type = getMultiValues('#accountability_type');
    const zone_id = getMultiValues('#zone_id_1');
    const datefilter   = document.getElementById('quick_date_filter').value;
    // const params = new URLSearchParams();
 
    // if (fromDate) params.append('from_date', fromDate);
    // if (toDate) params.append('to_date', toDate);
    // if (vehicle_model) params.append('vehicle_model', vehicle_model);
    // if (vehicle_make) params.append('vehicle_make', vehicle_make);
    // if (vehicle_type) params.append('vehicle_type', vehicle_type);
    // if (accountability_type) params.append('accountability_type', accountability_type);
    // if (status) params.append('status', status);
    // if (zone_id) params.append('zone_id', zone_id);
    
    // status
    //     appendMultiSelect(params, 'status', status);
        
    //     // vehicle filters
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
    
    
    // const url = `{{ route('b2b.export_vehicle_details') }}?${params.toString()}`;
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
        fields: selectedFields,
        status:status
    };

    // Show Bootstrap modal
    $("#export_select_fields_modal").modal('hide');
    var exportmodal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportmodal.show();

    $.ajax({
        url: "{{ route('b2b.export_vehicle_details') }}",
        method: "GET",
        data: data,
        xhrFields: { responseType: 'blob' },
        success: function(blob) {

            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "vehicle_list-" + new Date().toISOString().split('T')[0] + ".csv";
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

    
var table; // Declare globally

function applyAssignFilter() {
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();

    if ((from_date && !to_date) || (!from_date && to_date)) {
        toastr.error("Both From Date and To Date are required");
        return;
    }
    
    if (from_date && to_date) {
        const from = new Date(from_date);
        const to = new Date(to_date);

        if (to < from) {
            toastr.error("To Date must be the same as or after From Date");
            return;
        }
    }

    table.ajax.reload(); // reload DataTable with new filters
    
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

function clearAssignFilter() {
    $('#FromDate').val('');
    $('#ToDate').val('');
    // $('input[name="status_value"][value="all"]').prop('checked', true);
    $('#assignment_status').val(null).trigger('change');
    $('#quick_date_filter').val(null).trigger('change');
    $('#v_type').val(null).trigger('change');
$('#v_model').val(null).trigger('change');
$('#v_make').val(null).trigger('change');
$('#accountability_type').val(null).trigger('change');
$('#zone_id_1').val(null).trigger('change');

    
    table.ajax.reload();
    
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}
    
 $(document).ready(function () {
    $('#loadingOverlay').show();

      table = $('#AssignTable_List').DataTable({
        pageLength: 25,
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('b2b.vehiclelist') }}",
            type: 'GET',
            data: function (d) {
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
                d.accountability_type = $('#accountability_type').val();
                d.status   = $('#assignment_status').val();
                d.vehicle_model = $('#v_model').val();
                d.vehicle_type = $('#v_type').val();
                d.vehicle_make = $('#v_make').val();
                d.datefilter = $('#quick_date_filter').val();
                d.zone_id = $('#zone_id_1').val() ?? '';
            },
            beforeSend: function () {
                $('#loadingOverlay').show();
            },
            complete: function () {
                $('#loadingOverlay').hide();
            },
            error: function (xhr) {
                $('#loadingOverlay').hide();
                toastr.error(xhr.responseJSON?.error || 'Failed to load data. Please try again.');
            }
        },
        columns: [
            { data: 0, orderable: false, searchable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 },
            { data: 9 , orderable: false, searchable: false},
            { data: 10 },
            { data: 11 },
            { data: 12 },
            { data: 13 },
            { data: 14, orderable: false, searchable: false },
        ],
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        scrollX: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        initComplete: function () {
            $('#loadingOverlay').hide();

            // Checkbox handling
            $('#AssignTable_List').on('change', '.sr_checkbox', function () {
                $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
            });

            $('#CSelectAllBtn').on('change', function () {
                $('.sr_checkbox').prop('checked', this.checked);
            });
        }
    });

    // Error handling for DataTables
    $.fn.dataTable.ext.errMode = 'none';
    $('#AssignTable_List').on('error.dt', function (e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

    // Show loading overlay during redraw
    $('#AssignTable_List').on('preDraw.dt', function () {
        $('#loadingOverlay').show();
    });

    $('#AssignTable_List').on('draw.dt', function () {
        $('#loadingOverlay').hide();
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