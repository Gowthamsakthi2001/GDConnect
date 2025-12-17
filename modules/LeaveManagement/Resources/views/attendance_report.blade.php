<x-app-layout>
@section('style_css')
<style>
    /* Table styling */
    table thead th {
        background: #fff !important;
        color: #4b5563 !important;
        font-weight: 600;
    }

    .table tbody td {
        border: none !important;
        vertical-align: middle;
    }

    .table-responsive {
        margin-top: 20px;
    }

    /* Filter section */
    .filters-container {
        gap: 15px;
        flex-wrap: wrap;
    }
    .filters-container .form-select,
    .filters-container .btn {
        min-width: 150px;
        transition: all 0.3s ease;
    }

    .filters-container .form-select:focus,
    .filters-container .btn:focus {
        outline: none;
        box-shadow: 0 0 10px rgba(0,123,255,0.25);
    }

    .date-range {
        max-width: 220px;
    }

    .form-check-input {
        width: 22px;
        height: 22px;
    }

    @media (max-width: 768px) {
        .filters-container {
            flex-direction: column;
            gap: 10px;
        }
    }

    .btn-export {
        transition: all 0.3s ease;
    }

    .btn-export:hover {
        transform: scale(1.05);
    }

    .dataTables_wrapper .dataTables_paginate {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: nowrap;
      white-space: nowrap;
      margin-top: 12px;
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
    
    /* Style Previous / Next only */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 80px;   
      height: 30px;
      padding: 4px 4px;  
      border-radius: 6px;
      border: none;
      color: #fff !important;
      background-color: #0d6efd;
      cursor: pointer;
      font-weight: 500;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
      background-color: #0b5ed7;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
      background-color: #e9ecef;
      color: #6c757d !important;
      cursor: not-allowed;
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
     .form-check-input[type="checkbox"] {
        width: 2.3rem;
        height: 1.2rem;
    }

</style>
@endsection
    <div class="main-content">
        <div class="container-fluid">
          <div class="card p-4 shadow-sm rounded mt-3">
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="mb-0" style="font-size:18px; font-weight:600">Attendance Report</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" id="exportBtn"  class="btn btn-outline-primary btn-export">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <button type="button" class="btn btn-outline-dark" onclick="ARRightSideFilerOpen()">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </div>
                
            <div class="table-responsive">
                <div id="loadingOverlay" class="datatable-loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                <table id="AttendanceReportTable" class="table text-center table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>SL NO</th>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Date</th>
                            <th>Punch IN</th>
                            <th>Punch Out</th>
                            <!--<th>Punch In Location</th>-->
                            <!--<th>Punch Out Location</th>-->
                            <th>Total Online Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                   <tbody class="bg-white border border-white">
    
                    </tbody>
                </table>
            </div>
            
        <div class="offcanvas offcanvas-end" tabindex="-1" id="AttendanceReportRightAMV" aria-labelledby="AttendanceReportRightAMVLabel">
            <div class="offcanvas-header">
                <h5 class="custom-dark mb-0" id="AttendanceReportRightAMVLabel">Report Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        
            <div class="offcanvas-body">
                <!-- Top Buttons -->
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearAttendanceReportFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyAttendanceReportFilter()">Apply</button>
                </div>
        
                <!-- Timeline Card -->
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <h6 class="custom-dark mb-0">Select Time Line</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="filter-date-range">Date Range</label>
                            <select class="form-control custom-select2-field" id="filter-date-range">
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="this_week">This Week</option>
                                <option value="last_week">Last Week</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="from-date">From Date</label>
                            <input type="date" class="form-control" id="from-date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="to-date">To Date</label>
                            <input type="date" class="form-control" id="to-date">
                        </div>
                   
                    </div>
                </div>
        
                <!-- Options Card -->
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <h6 class="custom-dark mb-0">Select Options</h6>
                    </div>
                    <div class="card-body">

                        <!-- City -->
                        <div class="mb-3">
                            <label class="form-label" for="city_id">City</label>
                            <select class="form-control custom-select2-field" id="city_id" name="city_id" onchange="getAreas()" multiple>
                                <option value="" disabled>Select</option>
                            <option value="all">All</option>
                                @if(isset($cities))
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
        
                        <!-- Zone -->
                        <div class="mb-3">
                            <label class="form-label" for="area_id">Area</label>
                            <select class="form-control custom-select2-field" id="area_id" name="area_id" multiple>
                                <option value="" disabled>Select a city first</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="user_type">User Type</label>
                            <select class="form-control custom-select2-field" id="user_type" name="user_type" onchange="getUsers()" multiple>
                                <option value="" disabled>Select</option>
                                <option value="all">All</option>
                                <option value="in-house">Employee</option>
                                <option value="deliveryman">Rider</option>
                                <option value="helper">Helper</option>
                                <option value="adhoc">Adhoc</option>
                            </select>
                        </div>
                    
                        <div class="mb-3">
                            <label class="form-label" for="user_id">Select User</label>
                            <select class="form-control custom-select2-field" id="user_id" name="user_id" multiple>
                                 <option value="" disabled>Select a user type first</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="emp_id">Emp ID</label>
                            <select class="form-control custom-select2-field" id="emp_id" name="emp_id" multiple>
                                <option value="" disabled>Select</option>
                            </select>
                        </div>
                        
                        
                    </div>
                </div>
        
                <!-- Bottom Buttons -->
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearAttendanceReportFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyAttendanceReportFilter()">Apply</button>
                </div>
            </div>
        </div>
            
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
    
                <!-- Select All -->
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="field1">Select All</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input" type="checkbox" id="field1">
                    </div>
                  </div>
                </div>
    
                <!-- Employee ID -->
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="emp_id">Employee ID</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="emp_id" name="emp_id">
                    </div>
                  </div>
                </div>

                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="deliveryman_name">Name</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="deliveryman_name" name="deliveryman_name">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="city">City Name</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="city" name="city">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="area_name">Area Name</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="area_name" name="area_name">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="punchin_date">Punch In Date</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="punchin_date" name="punchin_date">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="punch_in">Punch In</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="punch_in" name="punch_in">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="punch_out">Punch Out</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="punch_out" name="punch_out">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="punch_in_location">Punch In Location</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="punch_in_location" name="punch_in_location">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="punchout_location">Punch Out Location</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="punchout_location" name="punchout_location">
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-12 mb-3">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="total_online_duration">Total Online Duration</label>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input export-field-checkbox" type="checkbox" id="total_online_duration" name="total_online_duration">
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
        
    </div>
    <div class="modal fade" id="attendanceViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="
                border-radius: 18px;
                border: none;
                box-shadow: 0 20px 60px rgba(0,0,0,0.25);
                overflow: hidden;
            ">
                <div class="modal-header" style="
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: #fff;
                    padding: 22px 28px;
                    border: none;
                ">
                    <h5 class="modal-title" style="
                        font-size: 18px;
                        font-weight: 700;
                        display: flex;
                        align-items: center;
                        gap: 10px;
                    ">
                        <i class="fa fa-clipboard-check"></i>
                        Attendance Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter: invert(1); opacity: .9;"></button>
                </div>
                <div class="modal-body" style="padding: 28px; background: #f8fafc;">
                    <div style="margin-bottom: 28px;">
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            margin-bottom: 18px;
                            border-bottom: 2px solid #e5e7eb;
                            padding-bottom: 10px;
                        ">
                            <div style="
                                width: 38px;
                                height: 38px;
                                border-radius: 10px;
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: #fff;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <i class="fa fa-user"></i>
                            </div>
                            <span style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #374151;
                                letter-spacing: .5px;
                                text-transform: uppercase;
                            ">
                                Employee Information
                            </span>
                        </div>
    
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">EMP ID</div>
                                    <div id="m_emp_id" style="font-size:15px;font-weight:700;color:#111827;"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">NAME</div>
                                    <div id="m_name" style="font-size:15px;font-weight:700;color:#111827;"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">CITY</div>
                                    <div id="m_city" style="font-size:15px;font-weight:700;color:#111827;"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">Area</div>
                                    <div id="m_area" style="font-size:15px;font-weight:700;color:#111827;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom: 28px;">
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            margin-bottom: 18px;
                            border-bottom: 2px solid #e5e7eb;
                            padding-bottom: 10px;
                        ">
                            <div style="
                                width: 38px;
                                height: 38px;
                                border-radius: 10px;
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: #fff;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <i class="fa fa-clock"></i>
                            </div>
                            <span style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #374151;
                                letter-spacing: .5px;
                                text-transform: uppercase;
                            ">
                                Attendance Information
                            </span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">DATE</div>
                                    <div id="m_date" style="font-size:14px;font-weight:600;"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">PUNCH IN</div>
                                    <div id="m_punchin" style="font-size:14px;font-weight:600;"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="background:#fff;border-radius:12px;padding:16px;border:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">PUNCH OUT</div>
                                    <div id="m_punchout" style="font-size:14px;font-weight:600;"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div style="
                                    background: linear-gradient(135deg,#667eea,#764ba2);
                                    border-radius:12px;
                                    padding:16px;
                                    color:#fff;
                                ">
                                    <div style="font-size:11px;font-weight:600;">TOTAL DURATION</div>
                                    <div id="m_duration" style="font-size:20px;font-weight:800;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            margin-bottom: 18px;
                            border-bottom: 2px solid #e5e7eb;
                            padding-bottom: 10px;
                        ">
                            <div style="
                                width: 38px;
                                height: 38px;
                                border-radius: 10px;
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: #fff;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                            <span style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #374151;
                                letter-spacing: .5px;
                                text-transform: uppercase;
                            ">
                                Location Details
                            </span>
                        </div>
    
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div style="background:#fff;border-radius:12px;padding:16px;border-left:4px solid #3b82f6;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">PUNCH IN LOCATION</div>
                                    <div id="m_punchin_location" style="font-size:13px;color:#374151;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background:#fff;border-radius:12px;padding:16px;border-left:4px solid #3b82f6;">
                                    <div style="font-size:11px;color:#6b7280;font-weight:600;">PUNCH OUT LOCATION</div>
                                    <div id="m_punchout_location" style="font-size:13px;color:#374151;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                </div>
                <div class="modal-footer" style="background:#f1f5f9;border-top:1px solid #e5e7eb;">
                    <button class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        Close
                    </button>
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

@section('script_js')
<script>
  
    $(document).ready(function () {
        
        $('#loadingOverlay').show();
    
          table = $('#AttendanceReportTable').DataTable({
            pageLength: 25,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.Green-Drive-Ev.leavemanagement.attendance_report_render') }}",
                type: 'GET',
                data: function (d) {
                    d.date_filter = $('#filter-date-range').val();
                    d.from_date = $('#from-date').val();
                    d.to_date = $('#to-date').val();
                    d.city = $('#city_id').val();
                    d.area = $('#area_id').val();
                    d.user_type = $('#user_type').val();
                    d.user_id = $('#user_id').val();
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
           columnDefs: [
                { targets: 0, width: "60px", className: "text-center" },
                { targets: 1, width: "90px" },
                { targets: 2, width: "180px" },
                { targets: 3, width: "120px" },
                { targets: 4, width: "160px" },
                { targets: 5, width: "160px" },
                { targets: 6, width: "180px" },
                { targets: 7, width: "80px", className: "text-center" }
            ],
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            initComplete: function () {
                $('#loadingOverlay').hide();
            }
        });
        $.fn.dataTable.ext.errMode = 'none';
        $('#AttendanceReportTable').on('error.dt', function (e, settings, techNote, message) {
            console.error('DataTables Error:', message);
            $('#loadingOverlay').hide();
            toastr.error('Error loading data. Please try again.');
        });

        $('#AttendanceReportTable').on('preDraw.dt', function () {
            $('#loadingOverlay').show();
        });
    
        $('#AttendanceReportTable').on('draw.dt', function () {
            $('#loadingOverlay').hide();
        });
        
        window.applyAttendanceReportFilter = function () {
            table.ajax.reload();
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('AttendanceReportRightAMV'));
            bsOffcanvas.hide();
        };
    
        window.clearAttendanceReportFilter = function () {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('AttendanceReportRightAMV'));
            $('#filter-date-range').val('today').trigger('change');
            $('#from-date, #to-date').val('');
            $('#city_id, #area_id, #user_type, #user_id')
                .val([]).trigger('change');
            fetchEmpIds();
            $('.custom-date').addClass('d-none');
            table.ajax.reload();
            bsOffcanvas.hide();
        };
    });
    
    function initSelectAll(selector) {
        let internalChange = false;
        $(document).on('mousedown touchstart', selector, function () {
            const prev = $(this).val() || [];
            $(this).data('prevSelection', prev);
        });
        $(document).on('focus', selector, function () {
            const prev = $(this).val() || [];
            $(this).data('prevSelection', prev);
        });
     
        $(selector).on('change', function () {
            if (internalChange) return;
     
            const $el = $(this);
            let prev = $el.data('prevSelection') || [];
            let current = $el.val() || [];
            prev = prev.map(String);
            current = current.map(String);
     
            internalChange = true;
            if (prev.includes('all') && current.includes('all') && current.length > 1) {
                const cleaned = current.filter(v => v !== 'all');
                $el.val(cleaned).trigger('change.select2');
                $el.data('prevSelection', cleaned);
                internalChange = false;
                return;
            }
            if (!prev.includes('all') && current.includes('all')) {
                $el.val(['all']).trigger('change.select2');
                $el.data('prevSelection', ['all']);
                internalChange = false;
                return;
            }
            if (current.includes('all') && current.length > 1) {
                $el.val(['all']).trigger('change.select2');
                $el.data('prevSelection', ['all']);
                internalChange = false;
                return;
            }
            if (!current.includes('all')) {
                const cleaned = current.filter(v => v !== 'all');
                if (cleaned.length !== current.length) {
                    $el.val(cleaned).trigger('change.select2');
                    $el.data('prevSelection', cleaned);
                    internalChange = false;
                    return;
                }
            }
            $el.data('prevSelection', current);
            internalChange = false;
        });
    }
    function toggleDateFields() {
        let value = $("#filter-date-range").val();
        if (value === "custom") {
            $("#from-date").closest(".mb-3").show();
            $("#to-date").closest(".mb-3").show();
        } else {
            $("#from-date").closest(".mb-3").hide();
            $("#to-date").closest(".mb-3").hide();
            $("#from-date").val("");
            $("#to-date").val("");
        }
    }
    
    toggleDateFields();
    
    $("#filter-date-range").on("change", function () {
        toggleDateFields();
    });
    
        
    function fetchEmpIds() {
        let user_types = $('#user_type').val() || [];
        let emp_ids = $('#emp_id').val() || [];

        $.ajax({
            url: "{{ route('global.getDeliveryMans_Ids') }}",
            type: 'GET',
            data: {
                user_types: user_types,  
                emp_ids: emp_ids        
            },
            dataType: 'json',
            beforeSend: function () {
                $('#emp_id').html('<option>Loading...</option>');
            },
            success: function (response) {
                if (response.success && response.data.length > 0) {
    
                    let options = '<option value="">Select Emp ID</option>';
    
                    $.each(response.data, function (index, value) {
                        if(value.emp_id != "" && value.emp_id != null){
                            options += `<option value="${value.id}">${value.emp_id}</option>`;
                        }
                        
                    });
    
                    $('#emp_id').html(options);
    
                } else {
                    $('#emp_id').html('<option value="">No Emp Id Found</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching emp ids:', error);
                $('#emp_id').html('<option value="">Error loading</option>');
            }
        });
    }

    
    $(document).ready(function () {
        initSelectAll('#city_id');
        initSelectAll('#area_id');
        initSelectAll('#user_type');
        initSelectAll('#user_id');
        fetchEmpIds();
    });
    function ARRightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#AttendanceReportRightAMV');
                $('.custom-select2-field').select2({
            dropdownParent: $('#AttendanceReportRightAMV') // Fix for offcanvas
        });
        bsOffcanvas.show();
    }
    
    function getAreas() {
        let cityIds = $('#city_id').val();  
        let AreaDropdown = $('#area_id');
    
        AreaDropdown.empty().append('<option value="">Loading...</option>');
    
        if (cityIds && cityIds.length > 0) {
    
            $.ajax({
                url: "{{ route('global.get_multi_city_areas') }}",
                type: "GET",
                data: { city_id: cityIds },  // pass array
                success: function (response) {
    
                    AreaDropdown.empty()
                        .append('<option value="" disabled>Select Zone</option>')
                        .append('<option value="all">All</option>');
    
                    if (response.data && response.data.length > 0) {
    
                        $.each(response.data, function (key, zone) {
                            AreaDropdown.append(
                                `<option value="${zone.id}">${zone.Area_name}</option>`
                            );
                        });
    
                    } else {
                        AreaDropdown.append('<option value="" disabled>No Zones available</option>');
                    }
                },
                error: function () {
                    AreaDropdown.empty().append('<option value="" disabled>Error loading zones</option>');
                }
            });
    
        } else {
            AreaDropdown.empty().append('<option value="" disabled>Select a city first</option>');
        }
    }

   function getUsers() {
        let userTypes = $('#user_type').val();
        let UserDropdown = $('#user_id');
        fetchEmpIds(); //reiniate deliveryman ids
        UserDropdown.empty().append('<option value="">Loading...</option>');
        if (!userTypes || userTypes.length === 0) {
            UserDropdown.empty()
                .append('<option value="" disabled>Select a user type first</option>');
            return;
        }
        
        $.ajax({
            url: "{{ route('global.get_deliverymans') }}",
            type: "GET",
            data: { user_types: userTypes },
            success: function (response) {
    
                UserDropdown.empty()
                    .append('<option value="" disabled>Select User</option>')
                    .append('<option value="all">All</option>');
    
                if (response.data && response.data.length > 0) {
    
                    $.each(response.data, function (key, value) {
                        let fullName = `${value.first_name ?? ''} ${value.last_name ?? ''}`.trim();
                        let mobile = value.mobile_number ?? '';
    
                        UserDropdown.append(
                            `<option value="${value.id}">
                                ${fullName} ${mobile}
                             </option>`
                        );
                    });
    
                } else {
                    UserDropdown.append('<option value="" disabled>No users available</option>');
                }
            },
            error: function () {
                UserDropdown.empty()
                    .append('<option value="" disabled>Error loading users</option>');
            }
        });
    }
    
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
        
         $('#CSelectAllBtn').on('change', function (e) {
            e.stopPropagation();
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
    
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('export_select_fields_modal');
        const selectAll = modal.querySelector('#field1');
        const checkboxes = modal.querySelectorAll('.export-field-checkbox'); 
    
        selectAll.addEventListener('change', function () {
          checkboxes.forEach(checkbox => {
            if (checkbox !== selectAll) {
              checkbox.checked = selectAll.checked;
            }
          });
        });
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
      
    function getMultiValues(selector) {
        return Array.from(document.querySelectorAll(selector + ' option:checked'))
                    .map(option => option.value);
    }
    

    document.getElementById('export_download').addEventListener('click', function () {
    
        const selected = [];
        const selectedFields = [];
    
        document
            .querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked')
            .forEach(cb => {
                selectedFields.push(cb.name);
            });
    
        if (selectedFields.length === 0) {
            toastr.error("Please select at least one export field.");
            return;
        }
    
        const fromDate   = document.getElementById('FromDate')?.value || '';
        const toDate     = document.getElementById('ToDate')?.value || '';
        const datefilter = document.getElementById('filter-date-range')?.value || '';
    
        const city_id  = getMultiValues('#city_id');
        const area     = getMultiValues('#area_id');
        const user_id  = getMultiValues('#user_id');
        const emp_id   = getMultiValues('#emp_id');
        const user_type = getMultiValues('#user_type'); 
    
        const data = {
            from_date: fromDate,
            to_date: toDate,
            date_range: datefilter,
            city_id: city_id,
            area: area,
            user_id: user_id,
            emp_id: emp_id,
            user_type: user_type,
            selected_ids: selected,
            fields: selectedFields
        };
    
        $("#export_select_fields_modal").modal('hide');
        
        var exportmodal = new bootstrap.Modal(document.getElementById('exportModal'));
        exportmodal.show();
    
        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.leavemanagement.export_attendance_report') }}",
            type: "POST",
            data: data,
            xhrFields: {
                responseType: 'blob'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (blob) {
    
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "attendance-report-" + new Date().toISOString().split('T')[0] + ".csv";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                exportmodal.hide();
            },
            error: function () {
                toastr.error("Network connection failed. Please try again.");
                exportmodal.hide();
            }
        });
    });


    $(document).on('click', '.view-attendance', function () {
        var emp_id = $(this).data('emp_id') || '-';
        var area = $(this).data('area') || '-';
        var punchout = $(this).data('punchout') || 'Not Punched Out';
        var punchout_address = $(this).data('punchout_location') || '-';
        if(punchout == ""){
            punchout_address = 'Not Punched Out';
        }
        $('#m_emp_id').text(emp_id);
        $('#m_name').text($(this).data('name'));
        $('#m_city').text($(this).data('city'));
        $('#m_area').text(area);
        $('#m_date').text($(this).data('date'));
        $('#m_punchin').text($(this).data('punchin'));
        $('#m_punchout').text(punchout);
        $('#m_punchin_location').text($(this).data('punchin_location'));
        $('#m_punchout_location').text(punchout_address);
        $('#m_duration').text($(this).data('duration'));
        $('#attendanceViewModal').modal('show');
    });



</script>
@endsection
</x-app-layout>
