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
</style>



  <div class="main-content">

    <div class="card bg-transparent my-4">
        <div class="card-header" style="background:#fbfbfb;">
            <div class="row g-3">
                <div class="col-md-4 d-flex align-items-center">
                    <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.rider_onboard.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>
                    Rider Onboarding Logs <span
                            class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{count($lists)}}</span></div>
                </div>

            <div class="col-md-8 d-flex align-items-center justify-content-end">
                    <div class="text-center d-flex gap-2">
                         
                          <div class="m-2 bg-white p-2 px-3 border-gray" >
                            <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                <i class="bi bi-download fs-17 me-1"></i> Export
                            </button>
                         </div>
                
                        <!-- Filter Button -->
                        <div class="m-2 bg-white p-2 px-3 border-gray" style="cursor: pointer;" onclick="OnRideBoardDashRightSideFilerOpen()">
                            <i class="bi bi-filter fs-17"></i> Filter
                        </div>
                
                    </div>
            </div>

            </div>
        </div>
    </div>
   </div>


        <div class="table-responsive">
            <table id="RiderOnboardLog_list" class="table text-center" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark">
                            <div class="form-check">
                                <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list."
                                    id="CSelectAllBtn">
                                <label class="form-check-label" for="CSelectAllBtn"></label>
                            </div>
                        </th>
                        <th scope="col" class="custom-dark">ID</th>
                        <th scope="col" class="custom-dark">Name</th>
                        <th scope="col" class="custom-dark">Role</th>
                        <th scope="col" class="custom-dark">Client Info</th>
                        <!--<th scope="col" class="custom-dark">Client Name</th>-->
                        <!--<th scope="col" class="custom-dark">Vehicle No</th>-->
                        <th scope="col" class="custom-dark">Onboarded Date</th>
                        <th scope="col" class="custom-dark">Created By</th>
                        <!--<th scope="col" class="custom-dark">Created Date & Time</th>-->
                        <th scope="col" class="custom-dark">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white border border-white">
                    <!-- Row 1 -->
              
                    @if(isset($lists))
                            @foreach($lists as $val)
                            <?php
                                $roleType = $val->role_type;
                                $roleTypeName = match ($roleType) {
                                    'deliveryman' => 'Rider',
                                    'adhoc' => 'Adhoc',
                                    'helper' =>'Helper',
                                    'in-house'=>'Employee',
                                    default => 'N/A',
                                };
                                 
                            ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;"
                                            type="checkbox" value="{{$val->id}}">
                                    </div>
                                </td>
                                <td>{{$val->deliveryman->emp_id ?? ''}}</td>
                                <td>{{$val->deliveryman->first_name ?? ''}} {{$val->deliveryman->last_name ?? ''}}</td>
                                <td>{{$roleTypeName}}</td>
                                <td>
                                    <div>{{$val->customer->id ?? ''}}</div>
                                    <div>{{$val->customer->trade_name ?? ''}}</div>
                                </td>
                                <!--<td>{{$val->customer->name ?? ''}}</td>-->
                                <td>{{!empty($val->onboard_date) ? date('d M Y',strtotime($val->onboard_date)) : ''}}</td>
                                <td><div>{{$val->CreatedBy->name ?? ''}}</div> <div>({{$val->CreatedBy->get_role->name ?? ''}})</div></td>
                                <!--<td>{{!empty($val->created_at) ? date('d M Y h:i:s A',strtotime($val->created_at)) : ''}}</td>-->
                                
                                <td>
                                    <a href="{{route('admin.Green-Drive-Ev.rider_onboard.view_log',['view_id'=>$val->id])}}"
                                        class="dropdown-item d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye me-2 fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>


        <div class="modal fade" id="export_select_fields_modal" tabindex="-1"
            aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form>
                    <div class="modal-content rounded-4">
                        <div class="modal-header border-0 d-flex justify-content-between">
                            <div>
                                <h1 class="h3 fs-5 text-center custom-dark" id="export_select_fields_modalLabel">
                                    Select Fields</h1>
                            </div>
                            <div>
                                <button type="button" class="btn text-white" style="background:#26c360;" onclick="Rider_Onboard_Log_Excel_Export()">Download</button>
                            </div>
                        </div>
                        <div class="modal-body p-md-3">
                            <div class="row px-4">
                                  <div class="col-md-3 col-12 mb-3">
                                      <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0 text-dark fw-bold h6" for="field1">Select All</label>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input get-export-label" type="checkbox" id="field1" value="">
                                        </div>
                                      </div>
                                    </div>
                              </div>
                            <div class="row p-4">
                                
                                 <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="role_type">Role</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="role_type" id="role_type"value="role_type">
                                        </div>
                                    </div>
                                </div>
                                <!-- 1. Rider ID -->
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="rider_id">ID</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="rider_id" id="rider_id" value="rider_id">
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Rider Name -->
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="rider_name">Name</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="rider_name" id="rider_name" value="rider_name">
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Client ID -->
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="client_id">Client ID</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="client_id" id="client_id">
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Client Name -->
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="client_name">Client Name</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="client_name" id="client_name">
                                        </div>
                                    </div>
                                </div>
                                
                                                                <!--5. City-->
                                 <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="city">City</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="city" id="city">
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <!--6. Hub-->
                               <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="hub">Hub</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="hub" id="hub">
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Vehicle ID -->
                                <!--<div class="col-md-3 col-12 mb-3">-->
                                <!--    <div class="d-flex justify-content-between align-items-center">-->
                                <!--        <label class="form-check-label mb-0" for="vehicle_id">Vehicle ID</label>-->
                                <!--        <div class="form-check form-switch m-0">-->
                                <!--            <input class="form-check-input export-field-checkbox" type="checkbox"-->
                                <!--                name="vehicle_id" id="vehicle_id">-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->

                                <!-- 6. Vehicle Type -->
                                <!--<div class="col-md-3 col-12 mb-3">-->
                                <!--    <div class="d-flex justify-content-between align-items-center">-->
                                <!--        <label class="form-check-label mb-0" for="vehicle_type">Vehicle-->
                                <!--            Type</label>-->
                                <!--        <div class="form-check form-switch m-0">-->
                                <!--            <input class="form-check-input export-field-checkbox" type="checkbox"-->
                                <!--                name="vehicle_type" id="vehicle_type">-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->

                                <!-- 7. Vehicle Model -->
                                <!--<div class="col-md-3 col-12 mb-3">-->
                                <!--    <div class="d-flex justify-content-between align-items-center">-->
                                <!--        <label class="form-check-label mb-0" for="vehicle_model">Vehicle-->
                                <!--            Model</label>-->
                                <!--        <div class="form-check form-switch m-0">-->
                                <!--            <input class="form-check-input export-field-checkbox" type="checkbox"-->
                                <!--                name="vehicle_model" id="vehicle_model">-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->

                                <!-- 8. Vehicle Variant -->
                                <!--<div class="col-md-3 col-12 mb-3">-->
                                <!--    <div class="d-flex justify-content-between align-items-center">-->
                                <!--        <label class="form-check-label mb-0" for="vehicle_variant">Vehicle-->
                                <!--            Variant</label>-->
                                <!--        <div class="form-check form-switch m-0">-->
                                <!--            <input class="form-check-input export-field-checkbox" type="checkbox"-->
                                <!--                name="vehicle_variant" id="vehicle_variant">-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->

                                <!-- 9. Onboarded Date -->
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="onboarded_date">Onboarded
                                            Date</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="onboarded_date" id="onboarded_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="remarks">Remarks</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="remarks" id="remarks">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="created_by">Created By</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="created_by" id="created_by">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-check-label mb-0" for="created_at">Created Date & Time</label>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input export-field-checkbox" type="checkbox"
                                                name="created_at" id="created_at">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>





        <div class="offcanvas offcanvas-end" tabindex="-1" id="OnLogDashoffcanvasRightHR01"
            aria-labelledby="OnLogDashoffcanvasRightHR01Label">
            <div class="offcanvas-header">
                <h5 class="custom-dark" id="OnLogDashoffcanvasRightHR01Label">Rider Onboarding Log Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">

                 <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearRiderLogOnbaordFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyRiderOnbaordLogFilter()">Apply</button>
                </div>

                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div>
                            <h6 class="custom-dark">Select Role Type</h6>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1"  value="all" {{$ch_status == 'all' ? 'checked': ''}}>
                            <label class="form-check-label" for="roleType1">
                                All
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType2" value="deliveryman" {{$ch_status == 'deliveryman' ? 'checked': ''}}>
                            <label class="form-check-label" for="roleType2">
                                Rider
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType3" value="adhoc" {{$ch_status == 'adhoc' ? 'checked': ''}}>
                            <label class="form-check-label" for="roleType3">
                                Adhoc
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType4" value="helper" {{$ch_status == 'helper' ? 'checked': ''}}>
                            <label class="form-check-label" for="roleType4">
                                Helper
                            </label>
                        </div>
                    </div>
                </div>

 <div class="card mb-3">
                    <div class="card-header p-2">
                        <div>
                            <h6 class="custom-dark">Select ID</h6>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="">
                           <select class="form-control bg-white custom-select2-field" id="RiderID">
                                    <option value="">Select</option>
                                @if(isset($deliveryman_data))
                                    @foreach($deliveryman_data as $data)
                                        <option value="{{ $data->id }}" {{$dm_id == $data->id ? 'selected' : ''}}>
                                            {{ $data->emp_id }}
                                        </option>
                                    @endforeach 
                                @endif
                            </select>
                        </div>
                        
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div>
                            <h6 class="custom-dark">Select Client ID</h6>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="">
                          <select class="form-control bg-white custom-select2-field" id="ClientID">
                                    <option value="">Select</option>
                                   @if(isset($customers))
                                        @foreach($customers as $data)
                                            <option value="{{ $data->id }}" {{$c_id == $data->id ? 'selected' : ''}}>
                                                {{ $data->id }}
                                            </option>
                                        @endforeach 
                                    @endif
                            </select>
                        </div>
                        
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div>
                            <h6 class="custom-dark">Select Time Line</h6>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="form-check mb-3">
                            <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                                id="timeLine1"  value="today" {{$timeline == "today" ? 'checked' : ''}}>
                            <label class="form-check-label" for="timeLine1">
                                This day
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                                id="timeLine2" value="this_week" {{$timeline == "this_week" ? 'checked' : ''}}>
                            <label class="form-check-label" for="timeLine2">
                                This Week
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                                id="timeLine3" value="this_month" {{$timeline == "this_month" ? 'checked' : ''}}>
                            <label class="form-check-label" for="timeLine3">
                                This Month
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                                id="timeLine4" value="this_year" {{$timeline == "this_year" ? 'checked' : ''}}>
                            <label class="form-check-label" for="timeLine4">
                                This Year
                            </label>
                        </div>
                    </div>
                </div>
                
               

                <div class="card mb-3">
                    <div class="card-header p-2">
                        <div>
                            <h6 class="custom-dark">Date Between</h6>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label" for="FromDate">From Date</label>
                            <input type="date" name="from_date" id="FromDate" class="form-control"
                                max="{{ date('Y-m-d') }}" value="{{$from_date}}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="ToDate">To Date</label>
                            <input type="date" name="to_date" id="ToDate" class="form-control"
                                max="{{ date('Y-m-d') }}" value="{{$to_date}}">
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-outline-secondary w-50" onclick="clearRiderLogOnbaordFilter()">Clear All</button>
                    <button class="btn btn-success w-50" onclick="applyRiderOnbaordLogFilter()">Apply</button>
                </div>

            </div>
        </div>



       
            
               
@section('script_js')
 <script>
    function applyRiderOnbaordLogFilter() {
        const selectedStatus = document.querySelector('input[name="roleTypebtn"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        const rider_id = document.getElementById('RiderID').value;
        const client_id = document.getElementById('ClientID').value;
        const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
        const timeline = selectedTimeline ? selectedTimeline.value : '';
        
        if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
                toastr.error("From Date and To Date is must be required");
                return;
            }
            
        }

        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('dm_id', rider_id);
        url.searchParams.set('c_id', client_id);
        if (from_date) {
            url.searchParams.set('from_date', from_date);
            url.searchParams.set('to_date', to_date);
            url.searchParams.delete('timeline');
            
        } else {
            
            url.searchParams.set('timeline', timeline);
            url.searchParams.delete('from_date');
            url.searchParams.delete('to_date');
        }
        window.location.href = url.toString();
    }
    
    function clearRiderLogOnbaordFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        if (url.searchParams.has('timeline')) {
            url.searchParams.delete('timeline');
        }
        if (url.searchParams.has('dm_id')) {
            url.searchParams.delete('dm_id');
        }
        if (url.searchParams.has('c_id')) {
            url.searchParams.delete('c_id');
        }

        window.location.href = url.toString();
    }
    
    function Rider_Onboard_Log_Excel_Export(){
            let selected = [];
            document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
              selected.push(cb.value);
            });
        
        
            const selectedFields = [];
        
        document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
          selectedFields.push({
            name: cb.name   
          });
        });
    
    
        if (selectedFields.length === 0) {
            toastr.error("Please select at least one export field.");
            return;
        }
    
    
        let params = new URLSearchParams();
        params.append('status', '{{ $ch_status }}');
        params.append('from_date', '{{ $from_date }}');
        params.append('to_date', '{{ $to_date }}');
        params.append('timeline', '{{ $timeline }}');
        params.append('dm_id', '{{ $dm_id }}');
        params.append('c_id', '{{ $c_id }}');
        
        if (selected.length > 0) {
          params.append('selected_ids', JSON.stringify(selected));
        }
        
        if (selectedFields.length > 0) {
          params.append('fields', JSON.stringify(selectedFields));
        }

    
        const url = `{{ route('admin.Green-Drive-Ev.rider_onboard.export_rider_onboard_log') }}?${params.toString()}`;
        window.location.href = url;
    }
    
    $(document).ready(function () {
           $('#RiderOnboardLog_list').DataTable({
                // dom: 'Blfrtip',
                // dom: 'frtip',
                // buttons: ['excel', 'pdf', 'print'],
                // order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: '_all' }
                ],
                lengthMenu: [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"] ],
                responsive: false,
                scrollX: true,
            });
        });
        
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
  
  
     function OnRideBoardDashRightSideFilerOpen() {
        const bsOffcanvas = new bootstrap.Offcanvas('#OnLogDashoffcanvasRightHR01');
        bsOffcanvas.show();
    }
                    
    document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('exportBtn').addEventListener('click', function() {
    let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
        modal.show();
    });
        });
        
        
document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('field1');
        const checkboxes = document.querySelectorAll('.export-field-checkbox');
    
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
    
      
</script>
                
@endsection
                
                </x-app-layout>
