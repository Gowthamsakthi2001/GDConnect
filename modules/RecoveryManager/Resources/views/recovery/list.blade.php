<x-app-layout>
@section('style_css')
<style>
    
    .timeline-wrapper {
            position: relative;
            margin-left: 40px;
            padding-left: 30px;
        }
        .timeline-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 10px;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #4cafef, #28a745, #ff9800, #f44336);
            border-radius: 2px;
            animation: growLine 1.5s ease-in-out forwards;
        }
        
        .timeline-icon {
            position: absolute;
            left: -40px;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 20px;
            color: #fff;
            animation: bounceIn 1s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .timeline-content {
            background: #fff;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .timeline-content:hover {
            transform: translateX(10px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
            border-left: 4px solid #28a745;
        }
        
        /* Progressive colors */
        .step-0 { background: #4cafef; }
        .step-1 { background: #28a745; }
        .step-2 { background: #ff9800; }
        .step-3 { background: #f44336; }

        @keyframes growLine {
            from { height: 0; }
            to { height: 100%; }
        }
        
        .timeline-step {
            position: relative;
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.8s forwards;
        }
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #modalBody::-webkit-scrollbar {
              display: none; /* Chrome, Safari, Edge */
            }

        /* 2️⃣ Make modal content take full available height */
        .custom-modal-height .modal-content {
            height: 70vh;
            display: flex;
            flex-direction: column;
        }
        
        /* 3️⃣ Allow modal body to scroll */
        .custom-modal-height .modal-body {
            flex: 1; /* take remaining space */
            overflow-y: auto;
            overflow-x: hidden;
        }

    .form-check-input:checked {
        background-color: #0f62fe !important;
        border-color: #0f62fe !important;
    }
    
    .comments-scroll {
          flex: 1;
          overflow-y: auto;
        }
        
        .chat-bubble {
              border-radius: 15px;
              padding: 10px 15px;
              word-wrap: break-word;
            }
            
            .sticky-footer {
              position: sticky;
              bottom: 0;
              background: #fff;
              z-index: 10;
              box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
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

    
    #recoveryList td, 
    #recoveryList th {
      text-align: center;           /* horizontal center */
      vertical-align: middle !important; /* vertical center */
    }
    
    /* If you need switches/buttons to center inside td */
    #recoveryList td .form-check,
    #recoveryList td .d-flex {
      justify-content: start;
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
                            Recovery List
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
    
                    <table id="recoveryList" class="table text-left table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                    <label class="form-check-label" for="CSelectAllBtn"></label>
                                  </div>
                                </th>
                                <th class="custom-dark">Request Id</th>
                                <th class="custom-dark">Vehicle No</th>
                                <th class="custom-dark">Chassis No</th>
                                <th class="custom-dark">Rider Name</th>
                                <th class="custom-dark">Contact Details</th>
                                <th class="custom-dark">Client</th>
                                <th class="custom-dark">City</th>
                                <th class="custom-dark">Zone</th>
                                <th class="custom-dark">Created By</th>
                                <th class="custom-dark">Created Date and Time</th>
                                <th class="custom-dark">Updated Date and Time</th>
                                
                                <th class="custom-dark">Agent Status</th>
                                <th class="custom-dark">Aging</th>
                                <th class="custom-dark">Status</th>
                                <th class="custom-dark">Action</th>
                              </tr>
                            </thead>
                            
                            <tbody class="border border-white"></tbody>
                          

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
                        <label class="form-check-label mb-0" for="field5">Vehicle Color</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_color" name="vehicle_color">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Registration Certificate</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="registration_certificate" name="registration_certificate">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Insurance Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="insurance_attachment" name="insurance_attachment">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">HSRP Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="hsrp_attachment" name="hsrp_attachment">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Fitness Certificate</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="fitness_certificate" name="fitness_certificate">
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
                        <label class="form-check-label mb-0" for="field8">Rider Email</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="rider_email" name="rider_email">
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
                        <label class="form-check-label mb-0" for="field6">Contact_details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="mobile_no" name="mobile_no">
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
                        <label class="form-check-label mb-0" for="field12">Reason</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="reason" name="reason">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field12">Location</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="location" name="location">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
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
                        <label class="form-check-label mb-0" for="field12">Agent Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="agent_status" name="agent_status">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Recovery Images</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="recovery_images" name="recovery_images">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Recovery Video</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="recovery_video" name="recovery_video">
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
                        <label class="form-check-label mb-0" for="field12">Closed Date & Time</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="closed_at" name="closed_at">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Recovery List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select City</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id_1" class="form-control custom-select2-field" @if(empty($zones)) onchange="getZones(this.value)" @else disabled @endif>
                            <option value="">Select City</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}"  @if(!empty($zones)) selected @endif >{{$city->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Zone</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Zone</label>
                        <select name="zone_id" id="zone_id_1" class="form-control custom-select2-field">
                            
                            @if(isset($zones) && !empty($zones))
                            <option value="">Select Zone</option>
                            @foreach($zones as $zone)
                            
                            <option value="{{$zone->id}}">{{$zone->name}}</option>
                            @endforeach
                            @else
                            <option value="">Select City First</option>
                            @endif
                        </select>
                    </div>
               </div>
            </div>
            
            <!--<div class="card mb-3">-->
            <!--   <div class="card-header p-2">-->
            <!--       <div><h6 class="custom-dark">Select Status</h6></div>-->
            <!--   </div>-->
            <!--   <div class="card-body">-->
 
            <!--        <div class="mb-3">-->
            <!--            <label class="form-label" for="zone_id">Status</label>-->
            <!--            <select name="status_value" id="status_value" class="form-control custom-select2-field">-->
            <!--                <option value="">Select</option>-->
            <!--                <option value="opened">Opened</option>-->
            <!--                <option value="closed">Closed</option>-->
            <!--            </select>-->
            <!--        </div>-->
            <!--   </div>-->
            <!--</div>-->
            
            
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
                <button class="btn btn-outline-secondary w-50" onclick="clearRecoveryFilter()">Clear All</button>
                <button class="btn btn-success w-50" id="applyFilterBtn">Apply</button>
            </div>
            
          </div>
        </div>
        
        
                 <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content rounded-3 border-0 shadow-sm">
                          
                          <!-- Header -->
                          <div class="modal-header border-0">
                            <h5 class="modal-title" id="updateStatusModalLabel">Update Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                    
                          <!-- Body -->
                          <div class="modal-body p-4">
                            <form id="updateStatusForm">
                              @csrf
                              <input type="hidden" id="request_id" name="request_id" value="">
                    
                              <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                  <label class="form-label">Update Status <span style="color:red;">*</span></label>
                                  <select class="form-control" id="update_status" name="update_status" required>
                                    <option value="" >Select Status</option>
                                      <!--<option value="assigned" >Assigned</option>-->
                                       <option value="closed" >Closed</option>
                                       <option value="not_recovered" >Not Recovered</option>
                                  </select>
                            
                                </div>
                                  
                                <div class="col-md-12 mt-3">
                                  <label class="form-label">Remarks</label>
                                  <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder="Enter remarks (optional)"></textarea>
                                </div>
                              </div>
                            </form>
                            <div id="status-error-message" class="text-danger mb-2 text-center" style="display:none;"></div>
                          </div>
                    
                          <!-- Footer -->
                          <div class="modal-footer border-0 p-4">
                            
                            <button type="button" class="btn update-btn btn-primary btn-lg w-100" onclick="updateStatus()">Update Status</button>
                          </div>
                        </div>
                      </div>
                    </div>


        
         <div class="modal fade" id="vehicleRequestModal" tabindex="-1" aria-labelledby="vehicleRequestModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-3 border-0 shadow-sm">
                  
                  <!-- Header -->
                  <div class="modal-header border-0">
                    <h5 class="modal-title" id="vehicleRequestModalLabel">Assign Agent to Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
            
                  <!-- Body -->
                  <div class="modal-body p-4">
                    <form id="assignAgentForm">
                      @csrf
                      <input type="hidden" id="request_id" name="request_id" value="">
            
                      <div class="row g-3 mb-3">
                        <div class="col-md-6">
                          <label class="form-label">Assign Agent <span style="color:red;">*</span></label>
                          <select class="form-control" id="agent_id" name="agent_id" required>
                            
                          </select>
                    
                        </div>
                          
                        <div class="col-md-12 mt-3">
                          <label class="form-label">Remarks</label>
                          <textarea class="form-control" id="agent-remarks" name="remarks" rows="4" placeholder="Enter remarks (optional)"></textarea>
                        </div>
                      </div>
                    </form>
                    <div id="assign-error-message" class="text-danger mb-2 text-center" style="display:none;"></div>
                  </div>
            
                  <!-- Footer -->
                  <div class="modal-footer border-0 p-4">
                    
                    <button type="button" class="btn assign-btn btn-primary btn-lg w-100" onclick="assignAgent()">Assign Agent</button>
                  </div>
                </div>
              </div>
            </div>

        <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-3 border-0 shadow-sm">
                  
                  <!-- Header -->
                  <div class="modal-header border-0">
                    <h5 class="modal-title" id="addCommentModalLabel">Add Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
            
                  <!-- Body -->
                  <div class="modal-body p-4">
                    <form id="addNewCommentForm">
                      @csrf
                      <input type="hidden" id="request_id" name="request_id" value="">
            
                      <div class="row g-3 mb-3">
                          
                        <div class="col-md-12 mt-3">
                          <label class="form-label">Comments</label>
                          <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Enter Comments"></textarea>
                        </div>
                      </div>
                    </form>
                    <div id="comment-error-message" class="text-danger mb-2 text-center" style="display:none;"></div>
                  </div>
            
                  <!-- Footer -->
                  <div class="modal-footer border-0 p-4">
                    
                    <button type="button" class="btn add-btn btn-primary btn-lg w-100" onclick="addNewComment()">Add Comment</button>
                  </div>
                </div>
              </div>
            </div>

<div class="modal fade" id="showLogModal" tabindex="-1" aria-labelledby="showLogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered custom-modal-height">
    <div class="modal-content rounded-3 border-0 shadow-sm">
      
      <!-- Header -->
      <div class="modal-header border-0 align-items-center">
            <h5 class="modal-title me-auto" id="showLogModalLabel">Logs</h5>

        </div>


      <!-- Scrollable Body -->
      <div class="modal-body p-4" id="modalBody" style="max-height: 80vh; overflow-y: auto;scrollbar-width:none; -ms-overflow-style:none;margin-bottom:8px">
        <div id="logs" style="display:flex; flex-direction:column;">
          <!-- Comments will be loaded here -->
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered custom-modal-height">
    <div class="modal-content rounded-3 border-0 shadow-sm">
      
      <!-- Header -->
      <div class="modal-header border-0 align-items-center">
            <h5 class="modal-title me-auto" id="commentModalLabel">Agent Comments</h5>
        
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm refresh-btn me-2 d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
        
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>


      <!-- Scrollable Body -->
      <div class="modal-body p-4" id="modalBody" style="max-height: 70vh; overflow-y: auto;">
        <div id="comments-timeline" style="display:flex; flex-direction:column;">
          <!-- Comments will be loaded here -->
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="modal-footer bg-light border-0 sticky-footer p-3">
        <form id="addCommentForm" class="d-flex w-100 align-items-center gap-2">
          @csrf
          <input type="hidden" name="req_id" id="req_id" value="">
          
          <input type="text" name="comments" id="commentText" class="form-control"
                 placeholder="Add your comment..." required>

          <!--<select name="comment_status" id="comment-status" class="form-select" style="width:160px;">-->
          <!--  <option value="">No Status</option>-->
          <!--  <option value="in_progress">In Progress</option>-->
          <!--  <option value="pickup_reached">Pickup Reached</option>-->
          <!--  <option value="recovered">Recovered</option>-->
          <!--  <option value="not_recovered">Not Recovered</option>-->
          <!--  <option value="vehicle_handovered">Vehicle Handovered</option>-->
          <!--</select>-->

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-send-fill"></i> 
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

                    
@section('script_js')

<script>
    
    
  $(document).ready(function () {
      
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
    
    var recoveryTable = $('#recoveryList').DataTable({
    pagingType: "simple",
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('admin.recovery_management.list', ['type' => $type]) }}",
        type: "GET",
        data: function(d) {
            d.city_id = $('#city_id_1').val();
                d.zone_id = $('#zone_id_1').val();
                d.status = $('#status_value').val() || '';
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
        },
        beforeSend: function () {
                $('#recoveryList tbody').html(`
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
                $('#recoveryList tbody').html(`
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
            { data: 15, orderable: false, searchable: false } // Action
            ],
    order:[[1,'desc']],
    lengthMenu:[[25,50,100,-1],[25,50,100,"All"]],
    responsive:true,
    scrollX:true,
    dom: '<"top"lf>rt<"bottom"ip>',
});

// ✅ Apply filter button
$('#applyFilterBtn').on('click', function(e){
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();
    
    if ((from_date && !to_date) || (!from_date && to_date)) {
        e.preventDefault();
        toastr.error("Both From Date and To Date are required");
        return;
    }

    if (from_date && to_date && to_date < from_date) {
        e.preventDefault();
        toastr.error("To Date must be greater than or equal to From Date");
        return;
    }

    recoveryTable.ajax.reload();

    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
});

    // ✅ Clear filter button
    window.clearRecoveryFilter = function() {
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id_1').val('').trigger('change');
        $('#zone_id_1').val('').trigger('change');
        recoveryTable.ajax.reload();
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }
    
       
    
  });
  

</script>

<script>
    function getZones(CityID) {
        let ZoneDropdown = $('#zone_id_1');
    
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
    const city   = document.getElementById('city_id')?.value || '';
    const zone   = document.getElementById('zone_id')?.value || '';
    const type = "{{ $type }}";
    // ✅ Build query params
    const params = new URLSearchParams();
    
    params.append('status', type);
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);
    if (zone) params.append('zone_id', zone);
    if (city) params.append('city_id', city);
    // append IDs
    selected.forEach(id => params.append('selected_ids[]', id));

    // append fields
    selectedFields.forEach(f => params.append('fields[]', f));
    
    const url = `{{ route('admin.recovery_management.request_list_export') }}?${params.toString()}`;
    window.location.href = url;
  });


</script>

<script>
let currentZoneId = null; // We'll store the zone ID globally for use in assignAgent()
let agentId = '';
let requestId = '';
// When clicking the Assign button (SVG link)
$(document).on('click', '[data-bs-target="#vehicleRequestModal"]', function () {
    requestId = $(this).data('id');
    const zoneId = $(this).data('get_zone_id');
    const cityId = $(this).data('get_city_id');
    agentId = $(this).data('agent_id') || '';
    // Save for later use
    currentZoneId = zoneId;
    
    // Set request id into hidden field
    $('#request_id').val(requestId);

    // Reset remarks field
    $('#agent-remarks').val('');

    // Clear current agents and show loading text
    $('#agent_id').html('<option value="">Loading agents...</option>');

    // Fetch agents for this zone
    $.ajax({
        url: "{{ route('admin.recovery_management.get_agents_by_zone') }}",
        type: "GET",
        data: { zone_id: zoneId,
        city_id: cityId
        },
        success: function(response) {
            if (response.success && response.agents.length > 0) {
                let options = '<option value="">Select Agent</option>';
                $.each(response.agents, function(index, agent) {
                    let selected = (String(agent.id) === String(agentId)) ? 'selected' : '';
                    options += `<option value="${agent.id}" ${selected}>${agent.first_name} ${agent.last_name}</option>`;
                });
                $('#agent_id').html(options);
            } else {
                $('#agent_id').html('<option value="">No agents found for this zone</option>');
            }
        },
        error: function() {
            $('#agent_id').html('<option value="">Failed to load agents</option>');
        }
    });

    // Show the modal
    $('#vehicleRequestModal').modal('show');
});

// When clicking "Assign Agent" button inside modal
function assignAgent() {
    let formData = {
        _token: "{{ csrf_token() }}",
        request_id: requestId,
        zone_id: currentZoneId,
        agent_id: $('#agent_id').val(),
        remarks: $('#agent-remarks').val(),
    };

    if (!formData.agent_id) {
        $('#assign-error-message')
            .text('⚠️ Please select an agent.')
            .show();
        return;
    }

    // Validation 2: Same agent selected for update
    if (formData.agent_id && agentId == formData.agent_id) {
        $('#assign-error-message')
            .text('⚠️ Please select a different agent to update.')
            .show();
        return;
    }
   
       if (agentId !== null && agentId !== '') {
        Swal.fire({
            title: 'Change Agent?',
            text: "Are you sure you want to change the assigned agent?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitAgentAssignment(formData);
            }
        });
    } else {
        // No existing agent — directly assign
        submitAgentAssignment(formData);
    }
}

// Separate function for AJAX logic
function submitAgentAssignment(formData) {
    $.ajax({
        url: "{{ route('admin.recovery_management.assign_agent') }}",
        type: "POST",
        data: formData,
        beforeSend: function() {
            $('.assign-btn').prop('disabled', true).text('Assigning...');
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#vehicleRequestModal').modal('hide');
                $('#assignAgentForm')[0].reset();
                $('#recoveryList').DataTable().ajax.reload();
            } else {
                toastr.error(response.message || 'Something went wrong.');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to assign agent. Please try again.');
        },
        complete: function() {
            $('.assign-btn').prop('disabled', false).text('Assign Agent');
        }
    });
} 

let status ='' ;
$(document).on('click', '[data-bs-target="#updateStatusModal"]', function () {
    requestId = $(this).data('id');
    status = $(this).data('status');
    agentId = $(this).data('agent_id') || '';
    
    // Set request id into hidden field
    $('#request_id').val(requestId);

    // Reset remarks field
    $('#remarks').val('');

    $('#updateStatusModal').modal('show');
});

function updateStatus() {
    let formData = {
        _token: "{{ csrf_token() }}",
        request_id: requestId,
        update_status: $('#update_status').val(),
        agent_id: agentId,
        remarks: $('#remarks').val(),
    };

    if (!formData.update_status) {
        $('#status-error-message')
            .text('⚠️ Please select an Status.')
            .show();
        return;
    }

    // Validation 2: Same agent selected for update
    if (formData.update_status && status == formData.update_status) {
        $('#status-error-message')
            .text('⚠️ Please select a different Status to update.')
            .show();
        return;
    }
    
    $.ajax({
        url: "{{ route('admin.recovery_management.update_status') }}",
        type: "POST",
        data: formData,
        beforeSend: function() {
            $('.update-btn').prop('disabled', true).text('Updating...');
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#updateStatusModal').modal('hide');
                $('#updateStatusForm')[0].reset();
                $('#recoveryList').DataTable().ajax.reload();
            } else {
                toastr.error(response.message || 'Something went wrong.');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to update status. Please try again.');
        },
        complete: function() {
            $('.update-btn').prop('disabled', false).text('Update Status');
        }
    });
}
</script>

<script>
let timeline='';
$(document).on('click', '[data-bs-target="#commentModal"]', function () {
    requestId = $(this).data('id');
    const agentId = $(this).data('agent_id');
    $('#req_id').val(requestId);
    $('#commentText').val('');
    $('#status').val('');
    
    $('#comments-timeline').html(`
        <div class="text-center py-4 text-muted">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div>Loading comments...</div>
        </div>
    `);
    
    // Load existing comments via AJAX
    $.ajax({
        url: "{{ url('admin/recovery-management/get-agent-comments') }}/" + requestId,
        method: "GET",
        success: function(response) {
            $('#comments-timeline').html(response.html);
            $('#commentModal').modal('show');

            const modalBody = document.getElementById('modalBody'); // use the scrollable div
            if (modalBody) {
                modalBody.scrollTop = modalBody.scrollHeight; // scroll to bottom
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to load comments.');
        }
    });
});

$(document).on('click', '.refresh-btn', function () {
    const $btn = $(this); // reference the clicked button
    const originalText = $btn.html();

    // show spinner and disable button
    $btn.prop('disabled', true).html(`
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Loading...
    `);

    $('#commentText').val('');
    $('#status').val('');

    // use the global or last used requestId (ensure it's set)
    if (!requestId) {
        toastr.error('No request ID found!');
        $btn.prop('disabled', false).html(originalText);
        return;
    }
    $('#comments-timeline').html(`
        <div class="text-center py-4 text-muted">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div>Loading comments...</div>
        </div>
    `);
    // AJAX call
    $.ajax({
        url: "{{ url('admin/recovery-management/get-agent-comments') }}/" + requestId,
        method: "GET",
        success: function(response) {
            $('#comments-timeline').html(response.html);
            $('#commentModal').modal('show');

            // Scroll to bottom after load
            const modalBody = document.getElementById('modalBody'); // use the scrollable div
            if (modalBody) {
                modalBody.scrollTop = modalBody.scrollHeight; // scroll to bottom
            }

            // Restore button after success
            $btn.prop('disabled', false).html('Refresh');
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to load comments.');

            // Restore button after error
            $btn.prop('disabled', false).html('Refresh');
        }
    });
});


$(document).ready(function() {
    $('#addCommentForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = {
        _token: "{{ csrf_token() }}",
        req_id: $('#req_id').val(),
        status: '',
        agent_id: agentId,
        comments: $('#commentText').val(),
    };
    var url = "{{ route('admin.recovery_management.add_comment') }}";
  
        // const form = $(this);
        // const formData = form.serialize();
        console.log(formData);
        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            success: function(response) {
                console.log(response);
                if (response.success) {
                    let statusHtml = '';
                    if (response.comment.status) {
                        statusHtml = `<div class="small mt-2 fw-bold" style="color:${response.comment.status_color}">
                            ${response.comment.status}
                        </div>`;
                    }

                    let newComment = `
                        <div class="d-flex flex-column mb-3 align-items-end">
                            <div class="p-3 rounded chat-bubble shadow-sm text-end ms-auto bg-primary text-white" style="max-width: 80%;">
                                <div class="fw-bold small mb-1">You</div>
                                <div>${response.comment.comments}</div>
                                ${statusHtml}
                            </div>
                            <small class="text-muted mt-1">${response.comment.created_at}</small>
                        </div>`;

                    timeline = $('#comments-timeline');
                    timeline.append(newComment);
                    
                    const modalBody = document.getElementById('modalBody'); // use the scrollable div
                        if (modalBody) {
                            modalBody.scrollTop = modalBody.scrollHeight; // scroll to bottom
                        }

                    $('#commentText').val('');
                    $('#status').val('');

                    toastr.success('Comment sent successfully!');
                } else {
                    toastr.error('Failed to send comment');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                toastr.error('Something went wrong.');
            }
        });
    });
    
});
</script>

<script>

$(document).on('click', '[data-bs-target="#addCommentModal"]', function () {
    requestId = $(this).data('id');
    // status = $(this).data('status');
    agentId = $(this).data('agent_id') || '';
    
    // Set request id into hidden field
    $('#request_id').val(requestId);

    // Reset remarks field
    $('#comments').val('');

    $('#addCommentModal').modal('show');
});

function addNewComment() {
    let formData = {
        _token: "{{ csrf_token() }}",
        req_id: requestId,
        // agent_id: agentId,
        comments: $('#comments').val(),
    };

    if (!formData.comments) {
        $('#comment-error-message')
            .text('⚠️ Please Enter a Comment.')
            .show();
        return;
    }

    $.ajax({
        url: "{{ route('admin.recovery_management.add_comment') }}",
        type: "POST",
        data: formData,
        beforeSend: function() {
            $('.add-btn').prop('disabled', true).text('Adding...');
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                $('#addCommentModal').modal('hide');
                $('#addCommentForm')[0].reset();
                $('#recoveryList').DataTable().ajax.reload();
            } else {
                toastr.error(response.message || 'Something went wrong.');
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to update status. Please try again.');
        },
        complete: function() {
            $('.add-btn').prop('disabled', false).text('Add Comment');
        }
    });
}
</script>

<script>
    $(document).on('click', '[data-bs-target="#showLogModal"]', function () {
    requestId = $(this).data('id');
    const agentId = $(this).data('agent_id');
    $('#req_id').val(requestId);
    
    $('#logs').html(`
        <div class="text-center py-4 text-muted">
            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
            <div>Loading logs...</div>
        </div>
    `);
    
    // Load existing comments via AJAX
    $.ajax({
        url: "{{ url('admin/recovery-management/get-agent-comments') }}/" + requestId,
        method: "GET",
        success: function(response) {
            $('#logs').html(response.html);
            $('#showLogModal').modal('show');

            // const modalBody = document.getElementById('modalBody'); // use the scrollable div
            // if (modalBody) {
            //     modalBody.scrollTop = modalBody.scrollHeight; // scroll to bottom
            // }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to load logs.');
        }
    });
});
</script>


@endsection
</x-app-layout>