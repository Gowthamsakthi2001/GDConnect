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
                           Client Zone List
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
                    <table id="ZoneList" class="table text-center table-striped table-bordered table-hover" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                              <tr>
                                <th class="custom-dark text-center">Sl No</th>
                                <th class="custom-dark text-center">Client Name</th>
                                <th class="custom-dark text-center">City</th>
                                <th class="custom-dark text-center">Zones</th>
                                <th class="custom-dark text-center">Client Status</th>
                                <th class="custom-dark text-center">Action</th>
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
                        <label class="form-check-label mb-0" for="field3">Client Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client" name="client">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">City Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="city" name="city">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Zone Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="zone" name="zone">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Zone Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="zone_status" name="zone_status">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Client Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="client_status" name="client_status">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Agent Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="agent_name" name="agent_name">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Client Zone List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
      
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Option</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select City</option>
                            <option value="all" >All</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}" >{{$city->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
                    
                     <div class="mb-3">
                       
                    <label class="form-label mb-0" for="customer_master">Customer</label>
                        
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
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
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
    initSelectAll('#city_id');
    // initSelectAll('#zone_id');
    initSelectAll('#customer_master');
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
    //   $('#ZoneList').DataTable({
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

    
        table.ajax.reload(); // reload DataTable with new filters
        
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }
    
    function clearRiderFilter() {
        $('#city_id').val('').trigger('change'); // 🔹 reset city + trigger change
         $('#status_value, #customer_master').val('').trigger('change');
        $('#zone_id').html('<option value="">Select Zone</option>').trigger('change'); // 🔹 reset zones + trigger change
        table.ajax.reload();
        
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }
  
     $(document).ready(function () {
        // $('#loadingOverlay').show();
         table = $('#ZoneList').DataTable({
            pageLength: 25,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('b2b.admin.zone.zone_list') }}",
                type: 'GET',
                 data: function (d) {
                d.city_id = $('#city_id').val();
                d.zone_id = $('#zone_id').val();
                d.status = $('#status_value').val();
                d.customer = $('#customer_master').val();
                },
            
                beforeSend: function () {
                $('#ZoneList tbody').html(`
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
                $('#ZoneList tbody').html(`
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
            { data: 4 }, // Chassis No
            { data: 5, orderable: false, searchable: false } // Action
            ],
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            // initComplete: function () {
            //     $('#loadingOverlay').hide();
    
            //     // Checkbox handling
            //     $('#ZoneList').on('change', '.sr_checkbox', function () {
            //         $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
            //     });
    
            //     $('#CSelectAllBtn').on('change', function () {
            //         $('.sr_checkbox').prop('checked', this.checked);
            //     });
            // }
        });
    
        // Error handling for DataTables
        // $.fn.dataTable.ext.errMode = 'none';
        // $('#ZoneList').on('error.dt', function (e, settings, techNote, message) {
        //     console.error('DataTables Error:', message);
        //     $('#loadingOverlay').hide();
        //     toastr.error('Error loading data. Please try again.');
        // });
    
        // // Show loading overlay during redraw
        // $('#ZoneList').on('preDraw.dt', function () {
        //     $('#loadingOverlay').show();
        // });
    
        // $('#ZoneList').on('draw.dt', function () {
        //     $('#loadingOverlay').hide();
        // });
    });





  document.getElementById('export_download').addEventListener('click', function () {

    const selectedFields = [];
    
    document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
            selectedFields.push(cb.name);
        });
    

   
    if (selectedFields.length === 0) {
        toastr.error("Please select at least one export field.");
        return;
    }
    
    // const city   = document.getElementById('city_id').value;
    // const status   = document.getElementById('status_value').value;
    const status = getMultiValues('#status_value');
    const city = getMultiValues('#city_id');
    const customer = getMultiValues('#customer_master');
    
    // const params = new URLSearchParams();


    // if (city) params.append('city', city);
    // if (status) params.append('status', status);
    // appendMultiSelect(params, 'status', status);
    // appendMultiSelect(params, 'city_id', city);
    // appendMultiSelect(params, 'customer', customer);
    
    // selectedFields.forEach(f => params.append('fields[]', f));
    
    
    // const url = `{{ route('b2b.admin.zone.export') }}?${params.toString()}`;
    // window.location.href = url;
    
    const data = {
        status: status,
        city_id: city,
        customer: customer,
        fields:selectedFields
    };

    // Show Bootstrap modal
    $("#export_select_fields_modal").modal('hide');
    var exportmodal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportmodal.show();

    $.ajax({
        url: "{{ route('b2b.admin.zone.export') }}",
        method: "GET",
        data: data,
        xhrFields: { responseType: 'blob' },
        success: function(blob) {

            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "zone_list-" + new Date().toISOString().split('T')[0] + ".csv";
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
</script>


@endsection
</x-app-layout>