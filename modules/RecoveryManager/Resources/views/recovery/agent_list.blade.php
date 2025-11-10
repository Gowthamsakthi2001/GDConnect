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

    
    #agentList td, 
    #agentList th {
      text-align: center;           /* horizontal center */
      vertical-align: middle !important; /* vertical center */
    }
    
    /* If you need switches/buttons to center inside td */
    #agentList td .form-check,
    #agentList td .d-flex {
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
                            Agent List
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
    
                    <table id="agentList" class="table text-left table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                    <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn">
                                  </div>
                                </th>
                                <th class="custom-dark">Profile</th>
                                <th class="custom-dark">Emp Id</th>
                                <!--<th class="custom-dark">Agent Reg Id</th>-->
                                <th class="custom-dark">Name</th>
                                <th class="custom-dark">Contact No</th>
                                <th class="custom-dark">Email</th>
                                <th class="custom-dark">City</th>
                                <th class="custom-dark">Zone</th>
                                <th class="custom-dark">Pending Req</th>
                                <th class="custom-dark">Closed Req</th>
                                <th class="custom-dark">Active/Inactive</th>
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
            
            <!-- Employee ID -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="emp_id">Reg ID</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="reg_id" name="reg_id">
                </div>
              </div>
            </div>
            
            <!-- Name -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="name">Name</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="name" name="name">
                </div>
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="email">Email</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="email" name="email">
                </div>
              </div>
            </div>

            <!-- Phone -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="phone">Phone</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="phone" name="phone">
                </div>
              </div>
            </div>

            <!-- Gender -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="gender">Gender</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="gender" name="gender">
                </div>
              </div>
            </div>


            <!-- Address -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="address">Address</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="address" name="address">
                </div>
              </div>
            </div>

            <!-- Status -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="status">Status</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="status" name="status">
                </div>
              </div>
            </div>

            <!-- Profile Photo -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="profile_photo_path">Profile Photo</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="profile_photo_path" name="profile_photo_path">
                </div>
              </div>
            </div>

            <!-- City -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="city_id">City</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="city_id" name="city_id">
                </div>
              </div>
            </div>

            <!-- Zone -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="zone_id">Zone</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="zone_id" name="zone_id">
                </div>
              </div>
            </div>
            
            <!-- Opened Counts -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="recovery_opened">Recovery Opened</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="recovery_opened" name="recovery_opened">
                </div>
              </div>
            </div>
            
            <!-- Closed Counts -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="recovery_closed">Recovery Closed</label>
                <div class="form-check form-switch m-0">
                  <input class="form-check-input export-field-checkbox" type="checkbox" id="recovery_closed" name="recovery_closed">
                </div>
              </div>
            </div>

            <!-- Created At -->
            <div class="col-md-3 col-12 mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-check-label mb-0" for="created_at">Created At</label>
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Agent List</h5>
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
                <button class="btn btn-outline-secondary w-50" onclick="clearAgentFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAgentFilter()">Apply</button>
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
    
    var agentTable = $('#agentList').DataTable({
    pagingType: "simple",
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('admin.recovery_management.agent_list', ['type' => $type]) }}",
        type: "GET",
        data: function(d) {
            d.from_date = $('#FromDate').val();
            d.to_date   = $('#ToDate').val();
            d.city_id   = $('#city_id_1').val();
            d.zone_id   = $('#zone_id_1').val();
        },
        beforeSend: function () {
                $('#agentList tbody').html(`
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
                $('#agentList tbody').html(`
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
        { data: 0, className:'text-center', orderable:false, searchable:false  },
        { data: 1 , orderable:true },
        { data: 2, className:'text-center', orderable:false, searchable:false },
        { data: 3, className:'text-center' },
        { data: 4 , orderable:true },
        { data: 5 , orderable:true },
        { data: 6 , orderable:true },
        { data: 7 , orderable:true },
        { data: 8 , orderable:true },
        { data: 9 , orderable:true },
        // { data:10 , orderable:true },
        // { data:11 , orderable:true },
        { data:10, orderable:false, searchable:false },
        { data:11, orderable:false, searchable:false },
    ],
    order:[[1,'desc']],
    lengthMenu:[[25,50,100,-1],[25,50,100,"All"]],
    responsive:true,
    scrollX:true,
});

// ✅ Apply filter button
    window.applyAgentFilter = function() {
        
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
}
        agentTable.ajax.reload();
    }

    // ✅ Clear filter button
    window.clearAgentFilter = function() {
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id_1').val('').trigger('change');
        $('#zone_id_1').val('').trigger('change');
        
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
}

        agentTable.ajax.reload();
    }
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

//      table = $('#agentList').DataTable({
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
//             $('#agentList').on('change', '.sr_checkbox', function () {
//                 $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
//             });

//             $('#CSelectAllBtn').on('change', function () {
//                 $('.sr_checkbox').prop('checked', this.checked);
//             });
//         }
//     });

//     // Error handling for DataTables
//     $.fn.dataTable.ext.errMode = 'none';
//     $('#agentList').on('error.dt', function (e, settings, techNote, message) {
//         console.error('DataTables Error:', message);
//         $('#loadingOverlay').hide();
//         toastr.error('Error loading data. Please try again.');
//     });

//     // Show loading overlay during redraw
//     $('#agentList').on('preDraw.dt', function () {
//         $('#loadingOverlay').show();
//     });

//     $('#agentList').on('draw.dt', function () {
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
    
    

   
    const fromDate = document.getElementById('FromDate').value || '';
    const toDate   = document.getElementById('ToDate').value || '';
    const zone_id   = document.getElementById('zone_id_1')?.value || '';
    const city_id   = document.getElementById('city_id_1').value || '';
    const type = "{{ $type }}";

    // ✅ Build query params
    const params = new URLSearchParams();
    
    params.append('status', type);
    if (fromDate) params.append('from_date', fromDate);
    if (toDate) params.append('to_date', toDate);
    if (zone_id) params.append('zone_id', zone_id);
    if (city_id) params.append('city_id', city_id);

    // append IDs
    selected.forEach(id => params.append('selected_ids[]', id));

    // append fields
    selectedFields.forEach(f => params.append('fields[]', f));
    
    
    const url = `{{ route('admin.recovery_management.agent_list_export') }}?${params.toString()}`;
    window.location.href = url;
  });


</script>

<script>
    // Delegate event because DataTable redraws rows
$(document).on('change', '.custom-switch', function () {
    let checkbox = $(this);
    let agentId = checkbox.data('id');
    let newStatus = checkbox.is(':checked') ? 'Active' : 'Suspended';

    // Revert immediately (wait for confirmation)
    checkbox.prop('checked', !checkbox.is(':checked'));

    Swal.fire({
        title: `Are you sure?`,
        text: `Do you want to set this agent as ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#26c360',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('b2b.admin.agent.updateStatus') }}", // ✅ your route
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: agentId,
                    status: newStatus
                },
                success: function (response) {
                    Swal.fire('Updated!', response.message, 'success');
                    checkbox.prop('checked', newStatus === 'Active'); // reflect status
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to update status.', 'error');
                }
            });
        }
    });
});

</script>

@endsection
</x-app-layout>