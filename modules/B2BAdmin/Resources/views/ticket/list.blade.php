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

    
    #ticketList td, 
    #ticketList th {
      text-align: center;           /* horizontal center */
      vertical-align: middle !important; /* vertical center */
    }
    
    /* If you need switches/buttons to center inside td */
    #ticketList td .form-check,
    #ticketList td .d-flex {
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
    


    
</style>
@endsection
    <div class="main-content">
        <div class="card bg-transparent mb-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3 align-items-center">
                    <!-- Title -->
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="card-title h5 custom-dark m-0 text-center text-md-start">
                            Ticket List
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
    
                    <table id="ticketList" class="table text-left" style="width: 100%;white-space: nowrap;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                    <label class="form-check-label" for="CSelectAllBtn"></label>
                                  </div>
                                </th>
                                <th class="custom-dark">Ticket Id</th>
                                <th class="custom-dark">Ticket Creator Name</th>
                                <th class="custom-dark">Contact Details</th>
                                <th class="custom-dark">Ticket Category</th>
                                <th class="custom-dark">Created Date and Time</th>
                                <th class="custom-dark">Updated Date and Time</th>
                                <th class="custom-dark">Status</th>
                                <th class="custom-dark">Action</th>
                              </tr>
                            </thead>
                            
                            <tbody class="border border-white">
                              <tr>
                                <td><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox"></td>
                                <td>AGT001</td>
                                <td>Arjun Kumar</td>
                                <td>9876543210</td>
                                <td>Delhi</td>
                                <td>2025-08-01 09:30 AM</td>
                                <td>2025-08-15 10:20 AM</td>
                                <td>
                                  <span style="background-color:#EECACB; color:#721c24;border:#721c24 1px solid" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Closed
                                  </span>
                                </td>
                                <td>
                                   <!-- Row with action buttons -->
                                    <div class="d-flex align-items-center gap-2">
                                      <!-- View Ticket Button -->
                                      <a title="View Ticket Details" href="{{route('b2b.admin.ticket.ticket_view')}}"
                                         class="d-flex align-items-center justify-content-center border-0"
                                         style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                                        <i class="bi bi-eye fs-5"></i>
                                      </a>
                                    
                                      <!-- Update Status Button -->
                                      <button type="button" class="d-flex align-items-center justify-content-center border-0"
                                              style="background-color:#D2CAED; border-radius:8px; width:40px; height:40px;"
                                              data-bs-toggle="modal" data-bs-target="#ticketStatusModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" fill="none">
                                          <rect width="27" height="27" rx="8" fill="#D2CAED"/>
                                          <path d="M21.2917 7.54297H11.2083C7.80449 7.54297 5.25 10.0042 5.25 13.5013" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M5.70834 19.4583H15.7917C19.1955 19.4583 21.75 16.9971 21.75 13.5" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M19.4583 5.25C19.4583 5.25 21.75 6.93779 21.75 7.54169C21.75 8.14559 19.4583 9.83333 19.4583 9.83333" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M7.54165 17.168C7.54165 17.168 5.25001 18.8557 5.25 19.4596C5.24999 20.0635 7.54167 21.7513 7.54167 21.7513" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                      </button>
                                    </div>
                                </td>
                                
                              </tr>
                            
                              <tr>
                                <td><input class="form-check-input sr_checkbox" type="checkbox" style="width:25px; height:25px;"></td>
                                <td>AGT002</td>
                                <td>Ravi Sharma</td>
                                <td>9876501234</td>
                                <td>Mumbai</td>
                                <td>2025-07-25 11:00 AM</td>
                                <td>2025-08-10 04:15 PM</td>
                                <td>
                                  <span style="background-color:#CAEDCE; color:#155724;border:#155724 1px solid" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Opened
                                  </span>
                                </td>
                                <td>
                                  <!-- Row with action buttons -->
                                    <div class="d-flex align-items-center gap-2">
                                      <!-- View Ticket Button -->
                                      <a title="View Ticket Details" href="{{route('b2b.admin.ticket.ticket_view')}}"
                                         class="d-flex align-items-center justify-content-center border-0"
                                         style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                                        <i class="bi bi-eye fs-5"></i>
                                      </a>
                                    
                                      <!-- Update Status Button -->
                                      <button type="button" class="d-flex align-items-center justify-content-center border-0"
                                              style="background-color:#D2CAED; border-radius:8px; width:40px; height:40px;"
                                              data-bs-toggle="modal" data-bs-target="#ticketStatusModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" fill="none">
                                          <rect width="27" height="27" rx="8" fill="#D2CAED"/>
                                          <path d="M21.2917 7.54297H11.2083C7.80449 7.54297 5.25 10.0042 5.25 13.5013" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M5.70834 19.4583H15.7917C19.1955 19.4583 21.75 16.9971 21.75 13.5" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M19.4583 5.25C19.4583 5.25 21.75 6.93779 21.75 7.54169C21.75 8.14559 19.4583 9.83333 19.4583 9.83333" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M7.54165 17.168C7.54165 17.168 5.25001 18.8557 5.25 19.4596C5.24999 20.0635 7.54167 21.7513 7.54167 21.7513" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                      </button>
                                    </div>
                                </td>
                              </tr>
                            
                              <tr>
                                <td><input class="form-check-input sr_checkbox" type="checkbox" style="width:25px; height:25px;"></td>
                                <td>AGT003</td>
                                <td>Mohit Singh</td>
                                <td>9123456789</td>
                                <td>Bangalore</td>
                                <td>2025-07-15 03:40 PM</td>
                                <td>2025-08-12 08:10 AM</td>
                                <td>
                                  <span style="background-color:#CAEDCE; color:#155724; border:#155724 1px solid" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Opened
                                  </span>
                                </td>
                                <td>
                                  <!-- Row with action buttons -->
                                    <div class="d-flex align-items-center gap-2">
                                      <!-- View Ticket Button -->
                                      <a title="View Ticket Details" href="{{route('b2b.admin.ticket.ticket_view')}}"
                                         class="d-flex align-items-center justify-content-center border-0"
                                         style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                                        <i class="bi bi-eye fs-5"></i>
                                      </a>
                                    
                                      <!-- Update Status Button -->
                                      <button type="button" class="d-flex align-items-center justify-content-center border-0"
                                              style="background-color:#D2CAED; border-radius:8px; width:40px; height:40px;"
                                              data-bs-toggle="modal" data-bs-target="#ticketStatusModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" fill="none">
                                          <rect width="27" height="27" rx="8" fill="#D2CAED"/>
                                          <path d="M21.2917 7.54297H11.2083C7.80449 7.54297 5.25 10.0042 5.25 13.5013" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M5.70834 19.4583H15.7917C19.1955 19.4583 21.75 16.9971 21.75 13.5" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M19.4583 5.25C19.4583 5.25 21.75 6.93779 21.75 7.54169C21.75 8.14559 19.4583 9.83333 19.4583 9.83333" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M7.54165 17.168C7.54165 17.168 5.25001 18.8557 5.25 19.4596C5.24999 20.0635 7.54167 21.7513 7.54167 21.7513" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                      </button>
                                    </div>
                                </td>
                              </tr>
                            
                              <tr>
                                <td><input class="form-check-input sr_checkbox" type="checkbox" style="width:25px; height:25px;"></td>
                                <td>AGT004</td>
                                <td>Karan Patel</td>
                                <td>9988776655</td>
                                <td>Pune</td>
                                <td>2025-08-02 01:15 PM</td>
                                <td>2025-08-13 09:45 AM</td>
                                <td>
                                  <span style="background-color:#EECACB; color:#721c24;border:#721c24 1px solid" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Closed
                                  </span>
                                </td>
                                <td>
                                  <!-- Row with action buttons -->
                                    <div class="d-flex align-items-center gap-2">
                                      <!-- View Ticket Button -->
                                      <a title="View Ticket Details" href="{{route('b2b.admin.ticket.ticket_view')}}"
                                         class="d-flex align-items-center justify-content-center border-0"
                                         style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                                        <i class="bi bi-eye fs-5"></i>
                                      </a>
                                    
                                      <!-- Update Status Button -->
                                      <button type="button" class="d-flex align-items-center justify-content-center border-0"
                                              style="background-color:#D2CAED; border-radius:8px; width:40px; height:40px;"
                                              data-bs-toggle="modal" data-bs-target="#ticketStatusModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 27 27" fill="none">
                                          <rect width="27" height="27" rx="8" fill="#D2CAED"/>
                                          <path d="M21.2917 7.54297H11.2083C7.80449 7.54297 5.25 10.0042 5.25 13.5013" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M5.70834 19.4583H15.7917C19.1955 19.4583 21.75 16.9971 21.75 13.5" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M19.4583 5.25C19.4583 5.25 21.75 6.93779 21.75 7.54169C21.75 8.14559 19.4583 9.83333 19.4583 9.83333" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                          <path d="M7.54165 17.168C7.54165 17.168 5.25001 18.8557 5.25 19.4596C5.24999 20.0635 7.54167 21.7513 7.54167 21.7513" stroke="#6C51BF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                      </button>
                                    </div>
                                </td>
                              </tr>
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
                        <label class="form-check-label mb-0" for="field12">Created At</label>
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Rider List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Date Between</h6></div>
               </div>
               <div class="card-body">
 
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
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearRiderFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyRiderFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
    <!-- Ticket Status Modal -->
<div class="modal fade" id="ticketStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px;">
      <div class="modal-header border-0">
        <h5 class="modal-title">Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="ticketStatusForm">
          <!-- Ticket Status Select -->
          <div class="mb-3">
            <select class="form-select" name="status" required>
              <option value="">Select Ticket Status</option>
              <option value="Opened">Opened</option>
              <option value="Closed">Closed</option>
            </select>
          </div>

          <!-- Description -->
          <div class="mb-3">
            <textarea class="form-control" name="description" placeholder="Description" rows="4"></textarea>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn w-100" 
                  style="background:#1565FF; color:white; font-weight:600; border-radius:8px;">
            Update Ticket Status
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@section('script_js')

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
  
       $(document).ready(function () {
      $('#ticketList').DataTable({
            dom: 'Blfrtip',
            dom: 'frtip',
            buttons: ['excel', 'pdf', 'print'],
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: '_all' }
            ],
            lengthMenu: [ [25, 50, 100, 250, -1], [25, 50, 100, 250, "All"] ],
            responsive: false,
            scrollX: true,
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
    
  

// var table; // Declare globally

// function applyRiderFilter() {
//     const from_date = $('#FromDate').val();
//     const to_date = $('#ToDate').val();

//     if ((from_date && !to_date) || (!from_date && to_date)) {
//         toastr.error("Both From Date and To Date are required");
//         return;
//     }

//     table.ajax.reload(); // reload DataTable with new filters
    
//         const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
//     if (bsOffcanvas) {
//         bsOffcanvas.hide();
//     }
// }

// function clearRiderFilter() {
//     $('#FromDate').val('');
//     $('#ToDate').val('');
//     $('input[name="status_value"][value="all"]').prop('checked', true);
//     table.ajax.reload();
    
//         const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
//     if (bsOffcanvas) {
//         bsOffcanvas.hide();
//     }
// }

  
  
//  $(document).ready(function () {
//     $('#loadingOverlay').show();

//      table = $('#ticketList').DataTable({
//         pageLength: 25,
//         pagingType: "simple",
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: "{{ route('b2b.rider_list') }}",
//             type: 'GET',
//              data: function (d) {
//             d.from_date = $('#FromDate').val();
//             d.to_date = $('#ToDate').val();
//             },
        
//             beforeSend: function () {
//                 $('#loadingOverlay').show();
//             },
//             complete: function () {
//                 $('#loadingOverlay').hide();
//             },
//             error: function (xhr) {
//                 $('#loadingOverlay').hide();
//                 toastr.error(xhr.responseJSON?.error || 'Failed to load data. Please try again.');
//             }
//         },
//         columns: [
//             { data: 0, orderable: false, searchable: false }, // S.No
//             { data: 1 }, // Rider Profile
//             { data: 2 }, // Rider Name
//             { data: 3 }, // Contact No
//             { data: 4 }, // Created Date
//             { data: 5, orderable: false, searchable: false }, // Action
//         ],
//         lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
//         scrollX: true,
//         dom: '<"top"lf>rt<"bottom"ip>',
//         initComplete: function () {
//             $('#loadingOverlay').hide();

//             // Checkbox handling
//             $('#ticketList').on('change', '.sr_checkbox', function () {
//                 $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
//             });

//             $('#CSelectAllBtn').on('change', function () {
//                 $('.sr_checkbox').prop('checked', this.checked);
//             });
//         }
//     });

//     // Error handling for DataTables
//     $.fn.dataTable.ext.errMode = 'none';
//     $('#ticketList').on('error.dt', function (e, settings, techNote, message) {
//         console.error('DataTables Error:', message);
//         $('#loadingOverlay').hide();
//         toastr.error('Error loading data. Please try again.');
//     });

//     // Show loading overlay during redraw
//     $('#ticketList').on('preDraw.dt', function () {
//         $('#loadingOverlay').show();
//     });

//     $('#ticketList').on('draw.dt', function () {
//         $('#loadingOverlay').hide();
//     });
// });


//   document.getElementById('export_download').addEventListener('click', function () {
 
      
//     const selected = [];
//     const selectedFields = [];
    
//     document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
//             selectedFields.push(cb.name);
//         });
    
//     document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
//       selected.push(cb.value);
//     });

   
//     if (selectedFields.length === 0) {
//         toastr.error("Please select at least one export field.");
//         return;
//     }
    
    

   
//     const fromDate = document.getElementById('FromDate').value;
//     const toDate   = document.getElementById('ToDate').value;

//     // ✅ Build query params
//     const params = new URLSearchParams();
 
//     if (fromDate) params.append('from_date', fromDate);
//     if (toDate) params.append('to_date', toDate);

//     // append IDs
//     selected.forEach(id => params.append('selected_ids[]', id));

//     // append fields
//     selectedFields.forEach(f => params.append('fields[]', f));
    
    
//     const url = `{{ route('b2b.rider_export') }}?${params.toString()}`;
//     window.location.href = url;
//   });


</script>

<script>
document.getElementById('ticketStatusForm').addEventListener('submit', function(e) {
  e.preventDefault();

  let formData = new FormData(this);

  fetch("{{ route('b2b.admin.ticket.update_ticket_status') }}", {
    method: "POST",
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.success){
      alert('Ticket status updated!');
      location.reload();
    } else {
      alert('Error updating ticket status');
    }
  })
  .catch(err => console.error(err));
});
</script>



@endsection
</x-app-layout>