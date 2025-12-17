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

    /* Datepicker input */
    .date-range {
        max-width: 220px;
    }

    /* Checkbox alignment */
    .form-check-input {
        width: 22px;
        height: 22px;
    }

    /* Responsive table scroll */
    @media (max-width: 768px) {
        .filters-container {
            flex-direction: column;
            gap: 10px;
        }
    }

    /* Animate export button */
    .btn-export {
        transition: all 0.3s ease;
    }

    .btn-export:hover {
        transform: scale(1.05);
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
      min-width: 80px;   /* ðŸ”¹ make both equal width */
      height: 30px;
      padding: 4px 4px;  /* ðŸ”¹ more padding for balanced look */
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

</style>
@endsection

<div class="container-fluid">
    <div class="card p-4 shadow-sm rounded mt-3">

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="mb-0" style="font-size:18px; font-weight:600">Accident Report</h5>
        
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" onclick="window.location.href='{{ route('b2b.admin.report.list') }}'"  class="btn btn-outline-secondary btn-back">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
        
                <button type="button" class="btn btn-outline-primary btn-export">
                    <i class="bi bi-download"></i> Export
                </button>
                
                <!-- Filter Button -->
                <button type="button" class="btn btn-outline-dark" onclick="AMVDashRightSideFilerOpen()">
                    <i class="bi bi-filter"></i> Filter
                </button>
                
            </div>
        </div>

 
        
        <!-- Accident Report Filter (Offcanvas Right) -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="AccidentReportRightAMV" aria-labelledby="AccidentReportRightAMVLabel">
            <div class="offcanvas-header">
                <h5 class="custom-dark mb-0" id="AccidentReportRightAMVLabel">Accident Report Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        
            <div class="offcanvas-body">
                <!-- Top Buttons -->
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="serviceDeploymentFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyServiceFilter()">Apply</button>
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
                                <option value="last7">Last 7 Days</option>
                                <option value="last30">Last 30 Days</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
        
                        <div class="custom-date d-none" id="custom-date-range">
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
                </div>
        
                <!-- Options Card -->
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <h6 class="custom-dark mb-0">Select Options</h6>
                    </div>
                    <div class="card-body">
        
                        <!-- Accountability Type -->
                        <div class="mb-3">
                            <label class="form-label" for="accountability_type">Accountability Type</label>
                            <select class="form-control custom-select2-field" id="accountability_type" name="accountability_type" multiple>
                                <option value="" disabled>Select</option>
                                <option value="all">All</option>
                                @if($accountability_types)
                                    @foreach($accountability_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <!-- City -->
                        <div class="mb-3">
                            <label class="form-label" for="city_id">City</label>
                            <select class="form-control custom-select2-field" id="city_id" name="city_id" onchange="getZones()" multiple>
                                <option value="" disabled>Select</option>
                            <option value="all">All</option>
                                @if($cities)
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
        
                        <!-- Zone -->
                        <div class="mb-3">
                            <label class="form-label" for="zone_id">Zone</label>
                            <select class="form-control custom-select2-field" id="zone_id" name="zone_id" multiple>
                                <option value="" disabled>Select a city first</option>
                            </select>
                        </div>
                        
                        <!-- Vehicle Type -->
                        <div class="mb-3">
                            <label class="form-label" for="vehicle_type">Vehicle Type</label>
                            <select class="form-control custom-select2-field" id="vehicle_type" name="vehicle_type" multiple>
                                <option value="" disabled>Select</option>
                            <option value="all">All</option>
                                @if($vehicle_types)
                                    @foreach($vehicle_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="mb-3">
                        <label class="form-label" for="v_model">Vehicle Model</label>
                        <select name="v_model" id="v_model" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}">{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_make">Vehicle Make</label>
                        <select name="v_make" id="v_make" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_makes))
                                @foreach($vehicle_makes as $val)
                                <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
        
                        <!-- Customer -->
                        <div class="mb-3">
                            <label class="form-label" for="customer_id">Customer</label>
                            <select class="form-control custom-select2-field" id="customer_id" name="customer_id" multiple>
                                <option value="" disabled>Select</option>
                                <option value="all">All</option>
                                @if($customers)
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->trade_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
        
                        <!-- Vehicle No -->
                        <div class="mb-3">
                            <label class="form-label" for="vehicle_no">Vehicle No</label>
                            <select class="form-control custom-select2-field" id="vehicle_no" name="vehicle_no" multiple>
                                <!-- Populated via JS -->
                            </select>
                        </div>
        
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label" for="accident_status">Status</label>
                            <select class="form-control custom-select2-field" id="accident_status" name="accident_status" multiple>
                                <option value="" disabled>Select</option>
                                <option value="all">All</option>
                                <option value="claimed_initiated">Claimed Initiated</option>
                                <option value="insurer_visit_confirmed">Insurer Visit Confirmed</option>
                                <option value="inspection_completed">Inspection Completed</option>
                                <option value="approval_pending">Approval Pending</option>
                                <option value="repair_started">Repair Started</option>
                                <option value="repair_completed">Repair Completed</option>
                                <option value="invoice_submitted">Invoice Submitted</option>
                                <option value="payment_approved">Payment Approved</option>
                                <option value="claim_closed">Claim Closed (Settled)</option>
                            </select>
                        </div>
                    </div>
                </div>
        
                <!-- Bottom Buttons -->
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-outline-secondary w-50" onclick="serviceDeploymentFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyServiceFilter()">Apply</button>
                </div>
            </div>
        </div>
        

        <!-- Table -->
        <div class="table-responsive">
            <div id="loadingOverlay" class="datatable-loading-overlay">
                <div class="loading-spinner"></div>
            </div>
            <table id="accidentTable" class="table text-center table-striped table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>SL NO</th>
                        <th>Request ID</th>
                        <th>Vehicle Number</th>
                        <th>Chassis Number</th>
                        <th>Vehicle Make</th>
                        <th>Vehicle Model</th>
                        <th>Vehicle Type</th>
                        <th>City</th>
                        <th>Zone</th>
                        <th>Customer Name</th>
                        <th>Rider Name</th>
                        <th>Rider Number</th>
                        <th>Created Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
               <tbody class="bg-white border border-white">

                </tbody>
            </table>
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
    initSelectAll('#accident_status');
    initSelectAll('#customer_id');
    initSelectAll('#accountability_type');
    initSelectAll('#v_make');
    initSelectAll('#v_model');
    initSelectAll('#vehicle_type');
    initSelectAll('#zone_id');
    initSelectAll('#city_id');
});


</script>

<script>
    $(document).ready(function () {
        $('#filter-date-range').on('change', function() {
            const selected = $(this).val();
            
            if (selected === 'custom') {
                // Show custom date fields
                $('.custom-date').removeClass('d-none').addClass('d-block');
            } else {
                // Hide custom date fields
                $('.custom-date').removeClass('d-block').addClass('d-none');
                $('#from-date, #to-date').val('');
            }
        });
        fetchVehicles();
        
        function fetchVehicles() {
            $.ajax({
                url: "{{ route('b2badmin.get_deployment_vehicles') }}",
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#vehicle_no').html('<option>Loading...</option>');
                },
                success: function (response) {
                    if (response && response.length > 0) {
                        let options = '<option value="">Select Vehicle</option>';
                        $.each(response, function (index, vehicle) {
                            options += `<option value="${vehicle.id}">${vehicle.vehicle_no}</option>`;
                        });
                        $('#vehicle_no').html(options);
                    } else {
                        $('#vehicle_no').html('<option value="">No Vehicles Found</option>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching vehicles:', error);
                    $('#vehicle_no').html('<option value="">Error loading</option>');
                }
            });
        }

    });
          function getZones() {
    let cityIds = $('#city_id').val();  
    let ZoneDropdown = $('#zone_id');

    ZoneDropdown.empty().append('<option value="">Loading...</option>');

    if (cityIds && cityIds.length > 0) {

        $.ajax({
            url: "{{ route('global.get_multi_city_zones') }}",
            type: "GET",
            data: { city_id: cityIds },  // pass array
            success: function (response) {

                ZoneDropdown.empty()
                    .append('<option value="" disabled>Select Zone</option>')
                    .append('<option value="all">All</option>');

                if (response.data && response.data.length > 0) {

                    $.each(response.data, function (key, zone) {
                        ZoneDropdown.append(
                            `<option value="${zone.id}">${zone.name}</option>`
                        );
                    });

                } else {
                    ZoneDropdown.append('<option value="" disabled>No Zones available</option>');
                }
            },
            error: function () {
                ZoneDropdown.empty().append('<option value="" disabled>Error loading zones</option>');
            }
        });

    } else {
        ZoneDropdown.empty().append('<option value="" disabled>Select a city first</option>');
    }
}
    
    
    $(document).ready(function () {
        $('#loadingOverlay').show();
    
          table = $('#accidentTable').DataTable({
            pageLength: 25,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('b2b.admin.report.accident_report') }}",
                type: 'GET',
                data: function (d) {
                    d.date_range = $('#filter-date-range').val();
                    d.from_date = $('#from-date').val();
                    d.to_date = $('#to-date').val();
                    d.zone = $('#zone_id').val();
                    d.city = $('#city_id').val();
                    d.vehicle_type = $('#vehicle_type').val();
                    d.vehicle_model = $('#v_model').val();
                    d.vehicle_make = $('#v_make').val();
                    d.vehicle_no = $('#vehicle_no').val();
                    d.status = $('#accident_status').val();
                    d.accountability_type = $('#accountability_type').val();
                    d.customer = $('#customer_id').val();
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
                { data: 0 },
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
                { data: 13, orderable: false, searchable: false }, // Status
            ],
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, "All"]],
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            initComplete: function () {
                $('#loadingOverlay').hide();
    
                // Checkbox handling
                $('#accidentTable').on('change', '.sr_checkbox', function () {
                    $('#CSelectAllBtn').prop('checked', $('.sr_checkbox:checked').length === $('.sr_checkbox').length);
                });
    
                $('#CSelectAllBtn').on('change', function () {
                    $('.sr_checkbox').prop('checked', this.checked);
                });
            }
        });
        // Error handling for DataTables
        $.fn.dataTable.ext.errMode = 'none';
        $('#accidentTable').on('error.dt', function (e, settings, techNote, message) {
            console.error('DataTables Error:', message);
            $('#loadingOverlay').hide();
            toastr.error('Error loading data. Please try again.');
        });
    
        // Show loading overlay during redraw
        $('#accidentTable').on('preDraw.dt', function () {
            $('#loadingOverlay').show();
        });
    
        $('#accidentTable').on('draw.dt', function () {
            $('#loadingOverlay').hide();
        });
        
           window.applyServiceFilter = function () {
            table.ajax.reload();
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('AccidentReportRightAMV'));
            bsOffcanvas.hide();
        };
    
        // Handle "Clear All" click inside offcanvas
        window.serviceDeploymentFilter = function () {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('AccidentReportRightAMV'));
            $('#filter-date-range').val('today').trigger('change');
            $('#from-date, #to-date').val('');
            $('#zone_id, #city_id, #vehicle_type, #accident_status, #accountability_type, #customer_id,#v_model,#v_make')
                .val([]).trigger('change');
            $('#vehicle_no').val([]).trigger('change');
            $('.custom-date').addClass('d-none');
            table.ajax.reload();
            bsOffcanvas.hide();
        };
        
        
    });
    
    
    
    document.querySelector('.btn-export').addEventListener('click', function () {
        // const params = new URLSearchParams();
    
        // Date range
        const dateRange = document.getElementById('filter-date-range').value;
        const fromDate  = document.getElementById('from-date').value;
        const toDate    = document.getElementById('to-date').value;
        
        const status = getMultiValues('#accident_status'); 
        const vehicle_make   = getMultiValues('#v_make');
        const vehicle_model = getMultiValues('#v_model');
        const vehicle_type  = getMultiValues('#vehicle_type');
        const accountability_type = getMultiValues('#accountability_type');
        const zone_id = getMultiValues('#zone_id');
        const city_id = getMultiValues('#city_id');
        const customer_id = getMultiValues('#customer_id');
        // const status    = document.getElementById('accident_status').value;
        
        // const accountability_type    = document.getElementById('accountability_type').value;
        // const customer    = document.getElementById('customer_id').value;
    
        // if (dateRange) params.append('date_range', dateRange);
        // if (fromDate)  params.append('from_date', fromDate);
        // if (toDate)    params.append('to_date', toDate);
        // if (status)    params.append('status', status);
        // if (accountability_type)    params.append('accountability_type', accountability_type);
        // if (customer)    params.append('customer', customer);
    
        // Vehicle type
        // const vehicleType = document.getElementById('vehicle_type').value;
        // if (vehicleType) params.append('vehicle_type', vehicleType);
    
        // Zone
        // const zone = document.getElementById('zone_id') ? document.getElementById('zone_id').value : '';
        // if (zone) params.append('zone', zone);
    
        // City
        // const city = document.getElementById('city_id').value;
        // if (city) params.append('city', city);
        
        // appendMultiSelect(params, 'status', status);
        
        // // vehicle filters
        // appendMultiSelect(params, 'vehicle_model', vehicle_model);
        // appendMultiSelect(params, 'vehicle_make', vehicle_make);
        // appendMultiSelect(params, 'vehicle_type', vehicle_type);
        
        // // others
        // appendMultiSelect(params, 'accountability_type', accountability_type);
        // appendMultiSelect(params, 'zone_id', zone_id);
        // appendMultiSelect(params, 'city_id', city_id);
        // appendMultiSelect(params, 'customer_id', customer_id);
        // Vehicle No
        const vehicleSelect = document.getElementById('vehicle_no');
        // if (vehicleSelect) {
        const vehicle_no = Array.from(vehicleSelect.selectedOptions).map(opt => opt.value);
        //     selectedVehicles.forEach(v => params.append('vehicle_no[]', v));
        // }
    
        // Build URL and redirect
        // const url = `{{ route('b2b.admin.report.export_accident_report') }}?${params.toString()}`;
        // window.location.href = url;
        
        const data = {
        from_date: fromDate,
        to_date: toDate,
        date_range: dateRange,
        zone_id: zone_id,
        city_id: city_id,
        customer_id:customer_id,
        accountability_type:accountability_type,
        vehicle_type:vehicle_type,
        vehicle_model:vehicle_model,
        vehicle_make:vehicle_make,
        status:status,
        vehicle_no:vehicle_no
        
    };

    // Show Bootstrap modal
    $("#export_select_fields_modal").modal('hide');
    var exportmodal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportmodal.show();

    $.ajax({
        url: "{{ route('b2b.admin.report.export_accident_report') }}",
        method: "GET",
        data: data,
        xhrFields: { responseType: 'blob' },
        success: function(blob) {

            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "accident_report-" + new Date().toISOString().split('T')[0] + ".csv";
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
        
    function AMVDashRightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#AccidentReportRightAMV');
                $('.custom-select2-field').select2({
            dropdownParent: $('#AccidentReportRightAMV') // Fix for offcanvas
        });
        bsOffcanvas.show();
    }
</script>
@endsection
</x-app-layout>

