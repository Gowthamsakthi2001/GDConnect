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
        border-top: 5px solid #52c552;
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
                            @php
                                $statusHeading = match($type) {
                                    'pending' => 'Pending Tickets',
                                    'assigned' => 'Assigned Tickets',
                                    'work_in_progress' => 'Work in Progress Tickets',
                                    'hold' => 'On Hold Tickets',
                                    'closed' => 'Closed Tickets',
                                    default => 'Total Tickets',
                                };
                            @endphp
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.asset_management.asset_master.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> {{ $statusHeading }}<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);" id="data_count"></span></div>
                            
                            
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" >
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>

                                    </div>
                                
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                
                                        <!-- Refresh Button -->
                                <div class="m-2 bg-white p-2 px-3 border-gray">
                                    <button type="button" id="refreshBtn" class="bg-white text-dark border-0">
                                        <i class="bi bi-arrow-clockwise fs-17 me-1"></i> Refresh
                                    </button>
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
                
                    
                    <table id="TicketTable_List" class="table" style="width: 100%;">
                        <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                                <th scope="col" class="custom-dark">
                                    <div class="form-check">
                                        <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                        <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                                <th scope="col" class="custom-dark">Ticket ID</th>
                                <th scope="col" class="custom-dark">Vehicle Type</th>
                                <th scope="col" class="custom-dark">Vehicle Number</th>
                                <th scope="col" class="custom-dark">City</th>
                                <th scope="col" class="custom-dark">Created At</th>
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
                    
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="ticket_id">Ticket ID</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="ticket_id" id="ticket_id">
                </div>
                </div>
                </div>
                
                    <!-- All Fields -->
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
                        <label class="form-check-label mb-0" for="vehicle_number">Vehicle Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_number" id="vehicle_number">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="vehicle_name">Vehicle Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_name" id="vehicle_name">
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
                        <label class="form-check-label mb-0" for="updatedat">Updated At</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="updatedat" id="updatedat">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="ticket_status">Ticket Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="ticket_status" id="ticket_status">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="telematics">Telematics</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="telematics" id="telematics">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="technician_notes">Technician Notes</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="technician_notes" id="technician_notes">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="task_performed">Task Performed</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="task_performed" id="task_performed">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="sync">Sync</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="sync" id="sync">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="state">State</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="state" id="state">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="started_location">Started Location</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="started_location" id="started_location">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="started_at">Started At</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="started_at" id="started_at">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="service_type">Service Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="service_type" id="service_type">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="service_charges">Service Charges</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="service_charges" id="service_charges">
                        </div>
                      </div>
                    </div>
                
                    <!-- Remaining fields -->
                <div class="col-md-3 col-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                    <label class="form-check-label mb-0" for="role">Role</label>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input export-field-checkbox" type="checkbox" name="role" id="role">
                    </div>
                      </div>
                 </div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="repair_type">Repair Type</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="repair_type" id="repair_type"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="priority">Priority</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="priority" id="priority"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="point_of_contact_info">Point of Contact Info</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="point_of_contact_info" id="point_of_contact_info"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="odometer">Odometer</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="odometer" id="odometer"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="observation">Observation</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="observation" id="observation"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="location">Location</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="location" id="location"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="lastsync">Last Sync</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="lastsync" id="lastsync"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="labour_description">Labour Description</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="labour_description" id="labour_description"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="job_type">Job Type</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="job_type" id="job_type"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="issue_description">Issue Description</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="issue_description" id="issue_description"></div></div></div>
            
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="ended_location">Ended Location</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="ended_location" id="ended_location"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="ended_at">Ended At</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="ended_at" id="ended_at"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="customer_number">Customer Number</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="customer_number" id="customer_number"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="customer_name">Customer Name</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="customer_name" id="customer_name"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="current_status">Current Status</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="current_status" id="current_status"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="createdat">Created At</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="createdat" id="createdat"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="contact_details">Contact Details</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="contact_details" id="contact_details"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="city">City</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="city" id="city"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="chassis_number">Chassis Number</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="chassis_number" id="chassis_number"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="category">Category</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="category" id="category"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="battery">Battery</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="battery" id="battery"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="assignment_info">Assignment Info</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="assignment_info" id="assignment_info"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="assigned_technician_id">Assigned Technician ID</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="assigned_technician_id" id="assigned_technician_id"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="assigned_by">Assigned By</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="assigned_by" id="assigned_by"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="assigned_at">Assigned At</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="assigned_at" id="assigned_at"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="address">Address</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="address" id="address"></div></div></div>
            
                <div class="col-md-3 col-12 mb-3"><div class="d-flex justify-content-between align-items-center"><label class="form-check-label mb-0" for="final_technician_notes">Final Technician Notes</label><div class="form-check form-switch m-0"><input class="form-check-input export-field-checkbox" type="checkbox" name="final_technician_notes" id="final_technician_notes"></div></div></div>

                
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Ticket Management</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearTicketFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyTicketFilter()">Apply</button>
            </div>

           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="today" {{ request('timeline') == 'today' ? 'checked' : '' }} name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_week" {{ request('timeline') == 'this_week' ? 'checked' : '' }} name="STtimeLine" id="timeLine2">
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_month" {{ request('timeline') == 'this_month' ? 'checked' : '' }} name="STtimeLine" id="timeLine3">
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_year" {{ request('timeline') == 'this_year' ? 'checked' : '' }} name="STtimeLine" id="timeLine4">
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{ request('from_date') }}" max="{{date('Y-m-d')}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{ request('to_date') }}" max="{{date('Y-m-d')}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearTicketFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyTicketFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
        
        

    

@section('script_js')



<script>
        function applyTicketFilter() {
            const timeline = $('input[name="STtimeLine"]:checked').val();
            const from_date = $('#FromDate').val();
            const to_date = $('#ToDate').val();
        
            if ((from_date && !to_date) || (!from_date && to_date)) {
                toastr.error("Both From Date and To Date are required when filtering by date");
                return;
            }
        
            loadTickets(); // <-- call your AJAX loader function
        
            // Close offcanvas
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
            if (bsOffcanvas) bsOffcanvas.hide();
        }

        function clearTicketFilter() {
        
            $('input[name="STtimeLine"]').prop('checked', false);
            $('#FromDate').val('');
            $('#ToDate').val('');
            
            // Reload DataTable with cleared filters
           loadTickets(); // <-- call your AJAX loader function
            
            // Close the offcanvas filter panel
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }



                // Function to load ticket data
                function loadTickets() {
                    $('#loadingOverlay').show();
            
                    $.ajax({
                        url: "{{ route('admin.ticket_management.list', ['type' => $type]) }}",
                        type: 'GET',
                        data: { 
                            type: '{{ $type }}',
                            from_date: $('#FromDate').val(),
                            to_date: $('#ToDate').val(),
                            timeline: $('input[name="STtimeLine"]:checked').val()
                        },
                        beforeSend: function() { $('#loadingOverlay').show(); },
                        success: function(response) {
                            let data = response.data || [];
                                // Convert object to array if needed
                            if (!Array.isArray(data)) {
                                data = Object.values(data);
                            }
            
                            // Update header count badge
                            $('#data_count').text(data.length);
            
                            // Destroy existing DataTable if it exists
                            if ($.fn.DataTable.isDataTable('#TicketTable_List')) {
                                $('#TicketTable_List').DataTable().clear().destroy();
                            }
            
                            // Initialize DataTable (client-side)
                            var table = $('#TicketTable_List').DataTable({
                                data: data,
                                columns: [
                                    { data: 'checkbox', orderable: false, searchable: false },
                                    { data: 'ticket_id' },
                                    { data: 'vehicle_type' },
                                    { data: 'vehicle_number' },
                                    { data: 'city' },
                                    { data: 'createdat' },
                                    { data: 'status', orderable: false, searchable: false },
                                    { data: 'action', orderable: false, searchable: false }
                                ],
                                pageLength: 15,
                                lengthMenu: [[15,25,50,100,-1],[15,25,50,100,"All"]],
                                scrollX: true,
                                responsive: false,
                                dom: '<"top"lf>rt<"bottom"ip>',
                                order: [[5, 'desc']] // <-- Descending order on the 6th column (createdat)
                            });
            
                            $('#loadingOverlay').hide();
            
                            // Checkbox handling
                            $('#TicketTable_List').on('change', '.sr_checkbox', function() {
                                $('#CSelectAllBtn').prop(
                                    'checked',
                                    $('.sr_checkbox:checked').length === $('.sr_checkbox').length
                                );
                            });
            
                            $('#CSelectAllBtn').on('change', function() {
                                $('.sr_checkbox').prop('checked', this.checked);
                            });
            
                            // Search input filtering
                            let searchDelay, lastNotification, lastSearchTerm = '';
                            $('#TicketTable_List_filter input').off('keyup').on('keyup', function() {
                                const searchTerm = this.value.trim();
                                clearTimeout(searchDelay);
                                if (lastNotification) toastr.clear(lastNotification);
            
                                if (searchTerm === lastSearchTerm) return;
            
                                searchDelay = setTimeout(() => {
                                    lastSearchTerm = searchTerm;
                                    table.search(searchTerm).draw();
                                }, 400);
                            });
                        },
                        error: function(xhr) {
                            console.error('API Error:', xhr.responseText);
                            $('#loadingOverlay').hide();
                            toastr.error(xhr.responseJSON?.error ?? 'Failed to load tickets.');
                        }
                    });
                }
                
                
            $(document).ready(function () {
        
                // Initial load
                loadTickets();
            
                // Refresh button click
                $('#refreshBtn').on('click', function() {
                    loadTickets();
                });
            
                // Global DataTable error handling
                $.fn.dataTable.ext.errMode = 'none';
                $('#TicketTable_List').on('error.dt', function(e, settings, techNote, message) {
                    console.error('DataTables Error:', message);
                    $('#loadingOverlay').hide();
                    toastr.error('Error loading data. Please try again.');
                });
            
                // Show/hide loading overlay during draw
                $('#TicketTable_List').on('preDraw.dt', function() { $('#loadingOverlay').show(); });
                $('#TicketTable_List').on('draw.dt', function() { $('#loadingOverlay').hide(); });
            
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
        
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
    });
    
      document.getElementById('export_download').addEventListener('click', function () {
     
          
        const selected = [];
        const selectedFields = [];
        
        document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
          selectedFields.push({
            name: cb.name,
            value: cb.value
          });
        });
    
    
        
        document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
          selected.push(cb.value);
        });
    
        // ✅ Validate: At least one field must be selected
        if (selectedFields.length === 0) {
            toastr.error("Please select at least one export field.");
            return;
        }
        
            const timeline = $('input[name="STtimeLine"]:checked').val();
            const from_date = $('#FromDate').val();
            const to_date = $('#ToDate').val();
            
    
    
        const params = new URLSearchParams();
        params.append('type', '{{$type}}');
        
        params.append('from_date', from_date);
        params.append('to_date', to_date);
        params.append('timeline', timeline);
        
        if (selected.length > 0) {
          params.append('selected_ids', JSON.stringify(selected));
        }
            if (selectedFields.length > 0) {
          params.append('fields', JSON.stringify(selectedFields));
        }
    
        const url = `{{ route('admin.ticket_management.export_ticket') }}?${params.toString()}`;
        window.location.href = url;
      });
    </script>



@endsection
</x-app-layout>
