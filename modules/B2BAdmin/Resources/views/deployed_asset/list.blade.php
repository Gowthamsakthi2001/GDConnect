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

    <div class="main-content">
        <div class="card bg-transparent mb-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3 align-items-center">
                    <!-- Title -->
                    <div class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="card-title h5 custom-dark m-0 text-center text-md-start">
                            Asset List
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
    
                    <table id="Asset_List" class="table text-left table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                            <th scope="col" class="custom-dark">
                                <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                    <label class="form-check-label" for="CSelectAllBtn"></label>
                                </div>
                            </th>
                              <th scope="col" class="custom-dark">Vehicle No</th>
                              <th scope="col" class="custom-dark">Chassis No</th>
                              <th scope="col" class="custom-dark">vehicle Type</th>
                              <th scope="col" class="custom-dark">Vehicle Model</th>
                              <th scope="col" class="custom-dark">Rider Name</th>
                              <th scope="col" class="custom-dark">Contact No</th>
                              <th scope="col" class="custom-dark">Client Name</th>
                              <th scope="col" class="custom-dark">City</th>
                              <th scope="col" class="custom-dark">Zone</th>
                              <th scope="col" class="custom-dark">Assigned Date & Time</th>
                              <th scope="col" class="custom-dark">Contract Expiry Date</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                        
                        <tbody class="border border-white"></tbody>
                        
                        </table>
                     </div>
         </div>
    




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
                          <label class="form-check-label mb-0" for="vehicle_type">Vehicle Type</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_type" id="vehicle_type">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_type">Vehicle Model</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_model" id="vehicle_model">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_type">Contract Start Date</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="contract_start_date" id="contract_start_date">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3 col-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                          <label class="form-check-label mb-0" for="vehicle_type">Contract Expiry Date</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="contract_expiry_date" id="contract_expiry_date">
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
                          <label class="form-check-label mb-0" for="status">Status</label>
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input export-field-checkbox" type="checkbox" name="status" id="status">
                          </div>
                        </div>
                      </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Client Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client_name" name="client_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Client Contact</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client_contact" name="client_contact">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Client Email</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client_email" name="client_email">
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
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
                
        
       <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Return List</h5>
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
                        <select name="city_id" id="city_id_1" class="form-control custom-select2-field" onchange="getZones(this.value)">
                            <option value="">Select City</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}" >{{$city->city_name}}</option>
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
                            <option value="">Select Zone</option>

                        </select>
                    </div>
               </div>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Status</label>
                        <select name="status_value" id="status_value" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="opened">Opened</option>
                            <option value="closed">Closed</option>
                        </select>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDeployedFilter()">Clear All</button>
                <button class="btn btn-success w-50" id="applyFilterBtn">Apply</button>
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
    
    var assetTable = $('#Asset_List').DataTable({
    pageLength: 25,
    pagingType: "simple",
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('b2b.admin.deployed_asset.list') }}",
        type: "GET",
        data: function(d) {
            d.city_id = $('#city_id_1').val();
                d.zone_id = $('#zone_id_1').val();
                d.status = $('#status_value').val();
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
        },
        beforeSend: function () {
                $('#Asset_List tbody').html(`
                  <tr>
                    <td colspan="13" class="text-center p-4">
                      <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                `);
            },
            error: function () {
                $('#Asset_List tbody').html(`
                  <tr>
                    <td colspan="13" class="text-center text-danger p-4">
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
            { data: 6 }, // Client
            { data: 7 }, // Client
            { data: 8 }, // Client
            { data: 9 }, // City
            { data: 10 }, // Zone
            { data: 11 }, // Created Date
            { data: 12, orderable: false, searchable: false } // Action
            ],
    order:[[1,'desc']],
    lengthMenu:[[25,50,100,-1],[25,50,100,"All"]],
    responsive:true,
    scrollX:true,
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

    assetTable.ajax.reload();

    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
});

    // ✅ Clear filter button
    window.clearDeployedFilter = function() {
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id_1').val('').trigger('change');
        $('#zone_id_1').val('').trigger('change');
        assetTable.ajax.reload();
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

//      table = $('#Asset_List').DataTable({
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
//             $('#Asset_List').on('change', '.sr_checkbox', function () {
//                 $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
//             });

//             $('#CSelectAllBtn').on('change', function () {
//                 $('.sr_checkbox').prop('checked', this.checked);
//             });
//         }
//     });

//     // Error handling for DataTables
//     $.fn.dataTable.ext.errMode = 'none';
//     $('#Asset_List').on('error.dt', function (e, settings, techNote, message) {
//         console.error('DataTables Error:', message);
//         $('#loadingOverlay').hide();
//         toastr.error('Error loading data. Please try again.');
//     });

//     // Show loading overlay during redraw
//     $('#Asset_List').on('preDraw.dt', function () {
//         $('#loadingOverlay').show();
//     });

//     $('#Asset_List').on('draw.dt', function () {
//         $('#loadingOverlay').hide();
//     });
// });


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

    // ✅ Build query params
    const params = new URLSearchParams();
 
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);

    // append IDs
    selected.forEach(id => params.append('selected_ids[]', id));

    // append fields
    selectedFields.forEach(f => params.append('fields[]', f));
    
    
    const url = `{{ route('b2b.admin.deployed_asset.export') }}?${params.toString()}`;
    window.location.href = url;
  });


</script>



@endsection
</x-app-layout>