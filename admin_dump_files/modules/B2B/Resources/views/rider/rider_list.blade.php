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
                            List Of Riders
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
    
                    <table id="RiderTable_List" class="table text-left table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">S.No</th>
                              <th scope="col" class="custom-dark">Rider Profile</th>
                              <th scope="col" class="custom-dark">Rider Name</th>
                              <th scope="col" class="custom-dark">Contact No</th>
                              <th scope="col" class="custom-dark">Zone Name</th> <!-- Updated By Gowtham.S-->
                              <th scope="col" class="custom-dark">Created Date & Time</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>

                        <tbody class="border border-white">
                                  
                        
                        
                        </tbody>
                        </table>
                     </div>
         </div>
    


    <!--NEW VEHICLE REQUEST SECTION-->

       <div class="modal fade" id="vehicleRequestModal" tabindex="-1" aria-labelledby="vehicleRequestModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow-sm">
          <!-- Header -->
          <div class="modal-header border-0">
            <h5 class="modal-title" id="vehicleRequestModalLabel">New Vehicle Request</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <!-- Body -->
          <div class="modal-body p-4">
            <form id="vehicleRequestForm">
              <input type="hidden" name="rider_id" id="modalRiderId">
    
             <div class="row g-3 mb-3">
                    <label class="form-label">Assigned Zone <span style="color:red;">*</span></label>
                      @if($login_type == 'master')
                        <select class="form-select custom-select2-field" id="assign_zone" name="assign_zone">
                            <option value="">Select Zone</option>
                            @if(isset($zones))
                                @foreach($zones as $val)
                                    <option value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    
                    @elseif($zone_id && $login_type == 'zone')
                        <input type="hidden" id="assign_zone_hidden" name="assign_zone" value="{{ $zone_id }}">
                        <select class="form-select custom-select2-field" id="assign_zone_disabled" disabled>
                            <option value="">Select Zone</option>
                            @if(isset($zones))
                                @foreach($zones as $val)
                                    <option value="{{ $val->id }}" {{ $val->id == $zone_id ? 'selected' : '' }}>
                                        {{ $val->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    
                    @else
                        <select class="form-select custom-select2-field" id="assign_zone" name="assign_zone">
                            <option value="">Select Zone</option>
                        </select>
                    @endif

            
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="start_date" class="form-label">Vehicle Duration Start Date <span style="color:red;">*</span></label>
                    <input type="date" class="form-control" name="start_date" id="start_date" min="{{ date('Y-m-d') }}">
                  </div>
                </div>
            
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="end_date" class="form-label">Vehicle Duration End Date <span style="color:red;">*</span></label>
                    <input type="date" class="form-control" name="end_date" id="end_date" min="{{ date('Y-m-d') }}">
                  </div>
                </div>
              </div>
    
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Battery Type</label>
                        <select class="form-control" name="battery_type">
                          <option value="">Select Battery Type</option>
                          <option value="1">Self-Charging</option>
                          <option value="2">Portable</option>
                        </select>
                      </div>
                </div>
                     
                     
                <div class="col-md-6">
                  <label class="form-label">Vehicle Type  <span style="color:red;">*</span></label>
                  <select class="form-control" name="vehicle_type">
                    <option value="">Select Vehicle Type</option>
                    @if(isset($vehicle_types))
                    @foreach($vehicle_types as $type)
                    <option value="{{$type->id}}" {{ $type->id == 1 ? 'selected' : '' }}>{{$type->name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
            </div>
              <div class="mb-3">
                <label class="form-label">Terms and Conditions</label>
                <div class="terms-box p-2 border rounded-2 mb-2">
                  <p style="font-size: 0.875rem; max-height: 150px; overflow-y: auto;">
                    By using this service, you agree to abide by our policies and guidelines. 
                    All information provided must be accurate and complete. 
                    Unauthorized use, duplication, or distribution of our materials is strictly prohibited. 
                    We reserve the right to modify or update these terms at any time without prior notice. 
                    Continued use of the service after changes indicates your acceptance of the updated terms.
                  </p>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="termsCheck" style="width:20px;height:20px;">
                  <label class="form-check-label" for="termsCheck">
                    I have agreed to the terms and conditions
                  </label>
                </div>
              </div>
            </form>
          </div>
    
          <!-- Footer -->
          <div class="modal-footer border-0 p-4">
            <button type="button" class="btn btn-primary btn-lg w-100 submit-button">Confirm Request</button>
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
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field1">Select All</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field1">
                        </div>
                      </div>
                    </div>
                
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field2">Request ID</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" name="req_id" id="req_id">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                
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
                    
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field5">Vehicle Duration</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_duration_type" name="vehicle_duration_type">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field5">Start Date</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="start_date" name="start_date">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field5">End Date</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="end_date" name="end_date">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
                    
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
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field12">Status</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="status" name="status">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
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
        
        <!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100" id="qrModalLabel">Thank you for creating rider</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6 class="mb-3" id="riderName"></h6>
        <img id="qrImage" src="" alt="QR Code" class="img-fluid mb-3" style="max-width: 250px;">
        <p class="fw-semibold">Show this QR code to the agent to verify your details.</p>
        <p class="text-muted small">Thank you for choosing our service!</p>
        <a href="#" id="whatsappShare" class="btn btn-success">
          <i class="bi bi-whatsapp"></i> Share QR Code via WhatsApp
        </a>
      </div>
    </div>
  </div>
</div>
    
@endsection

@section('js')


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
    
    
        
    // $(document).ready(function () {
    //   $('#RiderTable_List').DataTable({
    //         // dom: 'Blfrtip',
    //         // dom: 'frtip',
    //         // buttons: ['excel', 'pdf', 'print'],
    //         // order: [[0, 'desc']],
    //         columnDefs: [
    //             { orderable: false, targets: '_all' }
    //         ],
    //         lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
    //         responsive: false,
    //         scrollX: true,
    //     });
    // });
    
    
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
    $('input[name="status_value"][value="all"]').prop('checked', true);
    table.ajax.reload();
    
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

  
  
 $(document).ready(function () {
    $('#loadingOverlay').show();

     table = $('#RiderTable_List').DataTable({
        pageLength: 25,
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('b2b.rider_list') }}",
            type: 'GET',
             data: function (d) {
            d.from_date = $('#FromDate').val();
            d.to_date = $('#ToDate').val();
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
            { data: 0, orderable: false, searchable: false }, // S.No
            { data: 1 }, // Rider Profile
            { data: 2 }, // Rider Name
            { data: 3 }, // Contact No
            { data: 4 }, // Zone Name - Updated By Gowtham.S
            { data: 5 }, // Created Date
            { data: 6, orderable: false, searchable: false }, // Action
        ],
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
        scrollX: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        initComplete: function () {
            $('#loadingOverlay').hide();

            // Checkbox handling
            $('#RiderTable_List').on('change', '.sr_checkbox', function () {
                $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
            });

            $('#CSelectAllBtn').on('change', function () {
                $('.sr_checkbox').prop('checked', this.checked);
            });
        }
    });

    // Error handling for DataTables
    $.fn.dataTable.ext.errMode = 'none';
    $('#RiderTable_List').on('error.dt', function (e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

    // Show loading overlay during redraw
    $('#RiderTable_List').on('preDraw.dt', function () {
        $('#loadingOverlay').show();
    });

    $('#RiderTable_List').on('draw.dt', function () {
        $('#loadingOverlay').hide();
    });
});


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
    
    
    const url = `{{ route('b2b.rider_export') }}?${params.toString()}`;
    window.location.href = url;
  });

document.addEventListener('DOMContentLoaded', function () {
    var vehicleModal = document.getElementById('vehicleRequestModal');
    vehicleModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button/link that triggered modal
        var riderId = button.getAttribute('data-id'); // Get ID from data-id
        var zoneId = button.getAttribute('data-get_zone_id');
        var zoneName = button.getAttribute('data-get_zone_name'); //Updated by Gowtham.s

        document.getElementById('modalRiderId').value = riderId; // Set into hidden input

        // Only initialize if select exists and not disabled
        var ZoneDropdown = $("#assign_zone");
        if (ZoneDropdown.length) {
            ZoneDropdown.select2({
                dropdownParent: $('#vehicleRequestModal'),
                width: '100%' // make it fit Bootstrap form
            });
            ZoneDropdown.val(zoneId).trigger('change');
        }
    });
});


$(document).ready(function () {
    // Create reusable toast instance
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

   $('#vehicleRequestModal .submit-button').on('click', function (e) {
        e.preventDefault();
        
        var $btn = $(this); // reference to the button
        $btn.prop('disabled', true).text('Submitting...'); // disable + change text

        var formData = {
            rider_id: $('#modalRiderId').val(),
            assign_zone: $('[name="assign_zone"]').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            battery_type: $('select[name="battery_type"]').val(),
            vehicle_type: $('select[name="vehicle_type"]').val(),
            terms_agreed: $('#termsCheck').is(':checked') ? 1 : 0,
            _token: '{{ csrf_token() }}'
        };

        // === Validation ===
        if (!formData.start_date) {
            Toast.fire({ icon: 'warning', title: 'Start date is required.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        let today = new Date();
        today.setHours(0, 0, 0, 0);
        let startDate = new Date(formData.start_date);
        if (startDate < today) {
            Toast.fire({ icon: 'warning', title: 'Start date must be today or a future date.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        if (!formData.end_date) {
            Toast.fire({ icon: 'warning', title: 'End date is required.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        let endDate = new Date(formData.end_date);
        if (endDate < startDate) {
            Toast.fire({ icon: 'warning', title: 'End date must be the same as or after Start date.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        if (!formData.vehicle_type) {
            Toast.fire({ icon: 'warning', title: 'Vehicle type is required.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        if (!formData.terms_agreed) {
            Toast.fire({ icon: 'warning', title: 'You must agree to terms and conditions.' });
             $btn.prop('disabled', false).text('Confirm Request');
            return;
        }

        // AJAX call
        $.ajax({
            url: '{{ route("b2b.create_vehicle_request") }}',
            method: 'POST',
            data: formData,
            success: function (response) {
                $('#vehicleRequestModal').modal('hide');

                if (response.success) {
                    
                    $('#riderName').text(response.data.rider.name);
                    $('#qrImage').attr('src', response.data.qr_code);
            
                    // WhatsApp share link (QR + message)
                    // let shareText = `Here is your vehicle request QR Code (ID: ${response.data.request.req_id}).`;
                    // let qrUrl = window.location.origin + '/b2b/qr/' + response.data.request.qrcode_image;
                    // $('#whatsappShare').attr(
                    //     'href',
                    //     `https://wa.me/?text=${encodeURIComponent(shareText + ' ' + qrUrl)}`
                    // );
                    
                    $('#whatsappShare').attr('data-rider-id', response.data.rider.id);
                    
                    // Show QR modal
                    $('#qrModal').modal('show');
        
        
                $('#vehicleRequestForm')[0].reset();
                
                // Reset Vehicle Type to default (id = 1)
                $('select[name="vehicle_type"]').val('1').trigger('change');
                
                // Reset Battery Type to empty
                $('select[name="battery_type"]').val('').trigger('change');
                
                // Uncheck terms
                $('#termsCheck').prop('checked', false);

                // table.ajax.reload(null, false);
                
                table.ajax.reload();
                    Toast.fire({
                        icon: 'success',
                        title: response.message || 'Vehicle request created successfully!'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.message || 'Something went wrong!'
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var messages = Object.values(errors).map(e => e.join(', ')).join('\n');
                    Toast.fire({
                        icon: 'warning',
                        title: messages
                    });
                } 
                else if (xhr.status === 400) {
                    // Handle custom terms-condition warning
                    var data = xhr.responseJSON;
                    var msg = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'info',
                        title: data.title || 'Warning',
                        html: msg, // HTML allowed here
                        confirmButtonText: 'OK'
                    });
                }else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong. Please try again.'
                    });
                }
            },
        complete: function () {
            $btn.prop('disabled', false).text('Confirm Request');
        }
        });
    });
    
        // --- WhatsApp Qr Sending function ---
        $('#whatsappShare').on('click', function() {
            var riderId = $(this).data('rider-id');
    
            if(!riderId){
                Toast.fire({ icon: 'error', title: 'Rider ID not found!' });
                return;
            }
    
            $.ajax({
                url: '{{ route("sendQrCodeWhatsApp") }}',
                method: 'POST',
                data: {
                    rider_id: riderId,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#whatsappShare').prop('disabled', true).text('Sending...');
                },
                success: function(response){
                    Toast.fire({
                        icon: 'success',
                        title: response.message || 'QR sent via WhatsApp!'
                    });
                },
                error: function(xhr){
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.message || 'Failed to send QR'
                    });
                },
                complete: function(){
                    $('#whatsappShare').prop('disabled', false).html('<i class="bi bi-whatsapp"></i> Share QR Code via WhatsApp');
                }
            });
        });

    
    
    
    
});


</script>



@endsection
