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
        border-top: 5px solid #0f62fe;
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
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.asset_management.asset_master.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Total QC Inspection <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);" id="Qc_Filter_Count">{{ $totalRecords ?? 0 }}</span></div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">

                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" >
                                    <!--<a href="{{route('admin.asset_management.quality_check.export_quality_check', ['status' => $status, 'from_date' => $from_date, 'to_date' => $to_date , 'timeline' => $timeline])}}" class=" bg-white text-dark"><i class="bi bi-download fs-17 me-1"></i> Export</a>-->
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>

                                    </div>
                                <a href="{{route('admin.asset_management.quality_check.bulk_upload_form')}}" class="m-2 bg-white p-2 px-3 border-gray text-dark"><i class="bi bi-upload fs-17 me-1"></i> Bulk Upload</a>
                                
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                    onclick="window.location.href='{{route('admin.asset_management.quality_check.add_quality_check')}}'"
                                    style="cursor: pointer;">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>

                            </div>
                        </div>
                        
                        <div class="col-md-6 col-12 d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn custom-btn btn-round btn-sm " href="{{ route('admin.asset_management.quality_check.Excel_download') }}">
                                <i class="bi bi-download"></i> Bulk Demo
                            </a>
                            <a href="{{ route('admin.asset_management.quality_check.quality_check_import_verify') }}" class="btn custom-btn btn-round btn-sm">
                                <i class="bi bi-eye"></i> Import Verify
                            </a>
                           
                        </div>
                    </div>
                    

                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        


<!--        <div class="table-responsive table-container">-->
<!--    <div id="loadingOverlay" class="datatable-loading-overlay">-->
<!--        <div class="loading-spinner"></div>-->
<!--    </div>-->
<!--    <table id="QualityCheckTable_List" class="table text-center" style="width: 100%;">-->
<!--         Your table headers and body -->
<!--    </table>-->
<!--</div>-->

        <div class="table-responsive table-container">
            <div id="loadingOverlay" class="datatable-loading-overlay">
        <div class="loading-spinner"></div>
    </div>
                    <!--<table id="QualityCheckTable_List" class="table text-center" style="width: 100%;">-->
                    <!--      <thead class="bg-white rounded" style="background:white !important; color:black !important;">-->
                    <!--        <tr>-->
                    <!--          <th scope="col" class="custom-dark">-->
                    <!--              <div class="form-check">-->
                    <!--                  <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">-->
                    <!--                  <label class="form-check-label" for="CSelectAllBtn"></label>-->
                    <!--                </div>-->
                    <!--            </th>-->
                    <!--          <th scope="col" class="custom-dark">QC ID</th>-->
                    <!--            <th scope="col" class="custom-dark">Vehicle Type</th>-->
                    <!--        <th scope="col" class="custom-dark">Vehicle Model</th>-->
                    <!--          <th scope="col" class="custom-dark">Location</th>-->
                    <!--          <th scope="col" class="custom-dark">Chassis No</th>-->
                    <!--           <th scope="col" class="custom-dark">Battery No</th>-->
                    <!--          <th scope="col" class="custom-dark">Telematics No</th>-->
                    <!--        <th scope="col" class="custom-dark">Motor No</th>-->
                    <!--          <th scope="col" class="custom-dark">Current Status</th>-->
                    <!--          <th scope="col" class="custom-dark">Action</th>-->
                    <!--        </tr>-->
                    <!--      </thead>-->

                          
                    <!--    <tbody class="bg-white border border-white">-->
                                  
                      
                                  
                                   
                    <!--            @if(isset($datas))-->
                    <!--               @foreach($datas as $data)-->
                    <!--               <tr>-->
                    <!--                    <td>-->
                    <!--                        <div class="form-check">-->
                    <!--                            <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="{{$data['id']}}">-->
                    <!--                        </div>-->
                    <!--                    </td>-->
                    <!--                 <td>{{ $data['id'] }}</td>-->
                    <!--                     <td>{{ \Modules\VehicleManagement\Entities\VehicleType::find($data['vehicle_type'])->name ?? '-' }}</td>-->
                    <!--                    <td>{{ \DB::table('ev_tbl_vehicle_models')->where('id', $data['vehicle_model'])->value('vehicle_model') ?? '-' }}</td>-->
                    <!--                    <td>{{ \Modules\AssetMaster\Entities\LocationMaster::find($data['location'])->name ?? '-' }}</td>-->
                    <!--                    <td>{{ $data['chassis_number'] }}</td>-->
                    <!--                    <td>{{ $data['battery_number'] }}</td>-->
                    <!--                    <td>{{ $data['telematics_number'] }}</td>-->
                    <!--                   <td>{{ $data['motor_number'] }}</td>-->
                    <!--                    <td>-->
                    <!--                        @php-->
                    <!--                            $rawStatus = $data['status'] ?? null;-->
                    <!--                            $normalizedStatus = strtolower($rawStatus);-->
                                        
                    <!--                            // Determine color class based on normalized status-->
                    <!--                            $colorClass = match ($normalizedStatus) {-->
                    <!--                                'pass' => 'text-success',-->
                    <!--                                'fail' => 'text-danger',-->
                    <!--                                'qc_pending', 'nqc_pending', 'pending', null, '' => 'text-warning',-->
                    <!--                                default => 'text-warning',-->
                    <!--                            };-->
                                        
                    <!--                            // Display label formatting-->
                    <!--                            $displayStatus = match ($normalizedStatus) {-->
                    <!--                                'qc_pending' => 'QC Pending',-->
                    <!--                                'nqc_pending' => 'NQC Pending',-->
                    <!--                                'pass' => 'Pass',-->
                    <!--                                'fail' => 'Fail',-->
                    <!--                                default => ucfirst($normalizedStatus ?: 'Pending'),-->
                    <!--                            };-->
                    <!--                        @endphp-->
                                        
                    <!--                        <div class="d-flex align-items-center gap-2">-->
                    <!--                            <i class="bi bi-circle-fill {{ $colorClass }}"></i>-->
                    <!--                            <span>{{ $displayStatus }}</span>-->
                    <!--                        </div>-->
                    <!--                    </td>-->



                    <!--                    <td>-->
                    <!--                      <div class="dropdown">-->
                    <!--                        <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">-->
                    <!--                          <i class="bi bi-three-dots"></i>-->
                    <!--                        </button>-->
                                            
                    <!--                              <?php-->
                    <!--                                  $id_encode = encrypt($data['id']);-->
                    <!--                                ?>-->
                    <!--                       <ul class="dropdown-menu dropdown-menu-end text-center p-1">-->
                    <!--                          <li>-->
                    <!--                            <a href="{{route('admin.asset_management.quality_check.view_quality_check',['id'=>$id_encode])}}" class="dropdown-item d-flex align-items-center justify-content-center">-->
                    <!--                              <i class="bi bi-eye me-2 fs-5"></i> View-->
                    <!--                            </a>-->
                    <!--                          </li>-->
                    <!--                          @if($data['status'] != 'pass')-->
                    <!--                           <li>-->
                    <!--                            <a href="javascript:void(0);" -->
                    <!--                               class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord('{{$data->id}}')">-->
                    <!--                              <i class="bi bi-trash me-2"></i> Delete-->
                    <!--                            </a>-->
                    <!--                          </li>-->
                    <!--                          @endif-->
                                          
                    <!--                        </ul>-->
                                            



                    <!--                      </div>-->
                    <!--                    </td>-->
                    <!--               </tr>-->

                    <!--               @endforeach-->
                                   
                    <!--              @endif-->
                             
                    <!--    </tbody>-->
                    <!--    </table>-->
                    
                    <table id="QualityCheckTable_List" class="table text-center" style="width: 100%;">
    <thead class="bg-white rounded" style="background:white !important; color:black !important;">
        <tr>
            <th scope="col" class="custom-dark">
                <div class="form-check">
                    <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                    <label class="form-check-label" for="CSelectAllBtn"></label>
                </div>
            </th>
            <th scope="col" class="custom-dark">QC ID</th>
            <th scope="col" class="custom-dark">Vehicle Type</th>
            <th scope="col" class="custom-dark">Vehicle Model</th>
            <th scope="col" class="custom-dark">Location</th>
            <th scope="col" class="custom-dark">Chassis No</th>
            <th scope="col" class="custom-dark">Battery No</th>
            <th scope="col" class="custom-dark">Telematics No</th>
            <th scope="col" class="custom-dark">Motor No</th>
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
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="vehicle_type" id="vehicle_type">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Vehicle Model</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="vehicle_model" name="vehicle_model">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Location</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="location" name="location">
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
                        <label class="form-check-label mb-0" for="field6">Battery Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="battery_number" name="battery_number">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Telematics Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="telematics_number" name="telematics_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Motor Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="motor_number" name="motor_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Date and Time</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="date_time" name="date_time">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field10">Image</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input export-field-checkbox" type="checkbox" id="image" name="image">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Result</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="result" name="result">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Qc Checklists</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="qc_checklist" name="qc_checklist">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Quality Check</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearQualityCheckFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyQualityCheckFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                   
                     <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status" value="all" {{ request('status') === 'all' || request('status') === null ? 'checked' : '' }}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                    
                    
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status1" value="pass" {{ request('status') === 'pass' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status1">
                       Pass
                      </label>
                    </div>
                    
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status2"  value="fail"   {{ request('status') === 'fail' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status2">
                        Fail
                      </label>
                    </div>
                    
                                        
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status3"  value="qc_pending"   {{ request('status') === 'qc_pending' ? 'checked' : '' }}>
                      <label class="form-check-label" for="status3">
                       QC Pending
                      </label>
                    </div>
                    
                   
                    
               </div>
           </div>

           
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Location</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="location_id">Location</label>
                        <select name="location_id" id="location_id" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($location_data))
                            @foreach($location_data as $l)
                            <option value="{{$l->id}}" {{ $location == $l->id ? 'selected' : '' }}>{{$l->name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date}}" max="{{date('Y-m-d')}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date}}" max="{{date('Y-m-d')}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearQualityCheckFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyQualityCheckFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
        
        

    

@section('script_js')


<script>
    
    
       
//     function applyQualityCheckFilter() {
//         const selectedStatus = document.querySelector('input[name="status"]:checked');
//         const status = selectedStatus ? selectedStatus.value : 'all';
//          const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
//          const timeline = selectedTimeline ? selectedTimeline.value : '';
//         const from_date = document.getElementById('FromDate').value;
//         const to_date = document.getElementById('ToDate').value;
        
//         if(from_date != "" || to_date != ""){
//             if(to_date == "" || from_date == ""){
//                 toastr.error("From Date and To Date is must be required");
//                 return;
//             }
            
//         }
//         const location = document.getElementById('location_id').value;
        
    
//         const url = new URL(window.location.href);
//         url.searchParams.set('status', status);
//          url.searchParams.set('location', location);
//         // url.searchParams.set('from_date', from_date);
//         // url.searchParams.set('to_date', to_date);
        
//     if (from_date && to_date) {
//         // Use from_date and to_date, remove timeline
//         url.searchParams.set('from_date', from_date);
//         url.searchParams.set('to_date', to_date);
//         url.searchParams.delete('timeline');
//     } else if (timeline) {
//         // Use timeline, remove from_date and to_date
//         url.searchParams.set('timeline', timeline);
//         url.searchParams.delete('from_date');
//         url.searchParams.delete('to_date');
//     }

    
//         window.location.href = url.toString();
//     }


    
//     function clearQualityCheckFilter() {
//         const url = new URL(window.location.href);
//         url.searchParams.delete('status');
//         url.searchParams.delete('from_date');
//         url.searchParams.delete('to_date');
//         url.searchParams.delete('timeline');
//          url.searchParams.delete('location');
//         window.location.href = url.toString();
//     }
    
    
    
    
//   $(document).ready(function () {
//     $('#CSelectAllBtn').on('change', function () {
//       $('.sr_checkbox').prop('checked', this.checked);
//     });

//     $('.sr_checkbox').on('change', function () {
//       if (!this.checked) {
//         $('#CSelectAllBtn').prop('checked', false);
//       } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
//         $('#CSelectAllBtn').prop('checked', true);
//       }
//     });
//   });
</script>

<script>
function applyQualityCheckFilter() {
    // Get filter values
    const status = $('input[name="status"]:checked').val();
    const timeline = $('input[name="STtimeLine"]:checked').val();
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();
    const location = $('#location_id').val();

    // Validate date range if either date is provided
    if (from_date || to_date) {
        if (!from_date || !to_date) {
            toastr.error("Both From Date and To Date are required when filtering by date");
            return;
        }
    }

    // Reload DataTable with new parameters
    $('#QualityCheckTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

function clearQualityCheckFilter() {
    // Reset all filter inputs
    $('input[name="status"][value="all"]').prop('checked', true);
    $('input[name="STtimeLine"]').prop('checked', false);
    $('#FromDate').val('');
    $('#ToDate').val('');
    $('#location_id').val('').trigger('change');
    
    // Reload DataTable with cleared filters
    $('#QualityCheckTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

$(document).ready(function () {
    // Show loading overlay initially
    $('#loadingOverlay').show();

    var table = $('#QualityCheckTable_List').DataTable({
        pageLength: 15,
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.asset_management.quality_check.list') }}",
            type: 'GET',
            data: function (d) {
                // Add filter parameters to the AJAX request
                d.status = $('input[name="status"]:checked').val();
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
                d.timeline = $('input[name="STtimeLine"]:checked').val();
                d.location = $('#location_id').val();
            },
            beforeSend: function() {
                // Show loading overlay when AJAX starts
                $('#loadingOverlay').show();
            },
            complete: function(data) {
                // Hide loading overlay when AJAX completes
                $('#loadingOverlay').hide();
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                $('#loadingOverlay').hide();
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error);
                } else {
                    toastr.error('Failed to load data. Please try again.');
                }
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'vehicle_type', name: 'vehicle_type' },
            { data: 'vehicle_model', name: 'vehicle_model' },
            { data: 'location', name: 'location' },
            { data: 'chassis_number', name: 'chassis_number' },
            { data: 'battery_number', name: 'battery_number' },
            { data: 'telematics_number', name: 'telematics_number' },
            { data: 'motor_number', name: 'motor_number' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
        responsive: false,
        scrollX: true,
        dom: '<"top"lf>rt<"bottom"ip>',
        initComplete: function() {
            console.log("hiiii data new");
            // Hide loading overlay when table is initialized
            $('#loadingOverlay').hide();
            
            // Checkbox handling
            $('#QualityCheckTable_List').on('change', '.sr_checkbox', function() {
                if (!this.checked) {
                    $('#CSelectAllBtn').prop('checked', false);
                } else {
                    var allChecked = $('.sr_checkbox:checked').length === $('.sr_checkbox').length;
                    $('#CSelectAllBtn').prop('checked', allChecked);
                }
            });

            $('#CSelectAllBtn').on('change', function() {
                $('.sr_checkbox').prop('checked', this.checked);
            });
            
            // Improved search with validation
            let searchDelay;
            let lastNotification;
            let lastSearchTerm = '';
            
            
            
            $('#QualityCheckTable_List_filter input')
                .off('keyup')
                .on('keyup', function() {
                    const searchTerm = this.value.trim();
                    
                    // Clear previous timeouts and notifications
                    clearTimeout(searchDelay);
                    if (lastNotification) {
                        toastr.clear(lastNotification);
                    }
                    
                    // Skip if same as last search
                    if (searchTerm === lastSearchTerm) {
                        return;
                    }
                    
                    // Validate search term length
                    if (searchTerm.length > 0 && searchTerm.length < 4) {
                        searchDelay = setTimeout(() => {
                            lastNotification = toastr.info(
                                "Please enter at least 4 characters for better results",
                                {timeOut: 2000}
                            );
                        }, 500);
                        return;
                    }
                    
                    // Perform search if valid length or empty
                    searchDelay = setTimeout(() => {
                        lastSearchTerm = searchTerm;
                        table.search(searchTerm).draw();
                    }, 400);
                });
        },
        drawCallback: function(settings) {
            // Access the response JSON
            var response = settings.json;
            if (response) {
                // $('#totalRecordsSpan').text(response.recordsTotal);
                $('#Qc_Filter_Count').text(response.recordsFiltered);
            }
        }
    });

    // Initialize Select2 for location dropdown
    $('#location_id').select2({
        width: '100%',
        dropdownParent: $('#offcanvasRightHR01')
    });

    // Error handling
    $.fn.dataTable.ext.errMode = 'none';
    $('#QualityCheckTable_List').on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

    // Show loading when table is being redrawn
    $('#QualityCheckTable_List').on('preDraw.dt', function() {
        $('#loadingOverlay').show();
    });

    // Hide loading when table draw is complete
    $('#QualityCheckTable_List').on('draw.dt', function() {
        $('#loadingOverlay').hide();
    });
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
    
    //     $(document).ready(function () {
    //   $('#QualityCheckTable_List').DataTable({
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
    
    
//   $(document).ready(function () {
//     $('#loadingOverlay').show();

//     // Start timer for minimum display time
//     var loadingTimer = setTimeout(function() {
//         $('#loadingOverlay').fadeOut();
//     }, 1000);

//     var table = $('#QualityCheckTable_List').DataTable({
//         pageLength: 15,
//         pagingType: "simple", // Only prev/next
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: "{{ route('admin.asset_management.quality_check.list') }}",
//             type: 'GET',
//             data: function (d) {
//                 d.status = $('input[name="status"]:checked').val();
//                 d.from_date = $('#FromDate').val();
//                 d.to_date = $('#ToDate').val();
//                 d.timeline = $('input[name="STtimeLine"]:checked').val();
//                 d.location = $('#location_id').val();
//             },
//             error: function(xhr) {
//                 console.error('AJAX Error:', xhr.responseText);
//                 if (xhr.responseJSON && xhr.responseJSON.error) {
//                     toastr.error(xhr.responseJSON.error);
//                 } else {
//                     toastr.error('Failed to load data. Please try again.');
//                 }
//             }
//         },
//         columns: [
//             { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
//             { data: 'id', name: 'id', className: 'text-center' },
//             { data: 'vehicle_type', name: 'vehicle_type', className: 'text-center' },
//             { data: 'vehicle_model', name: 'vehicle_model', className: 'text-center' },
//             { data: 'location', name: 'location', className: 'text-center' },
//             { data: 'chassis_number', name: 'chassis_number', className: 'text-center' },
//             { data: 'battery_number', name: 'battery_number', className: 'text-center' },
//             { data: 'telematics_number', name: 'telematics_number', className: 'text-center' },
//             { data: 'motor_number', name: 'motor_number', className: 'text-center' },
//             { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
//             { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
//         ],
//         lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
//         pageLength: 15,
//         responsive: false,
//         scrollX: true,
//         dom: '<"top"lf>rt<"bottom"ip>',
//         initComplete: function() {
//             $('#QualityCheckTable_List').on('change', '.sr_checkbox', function() {
//                 if (!this.checked) {
//                     $('#CSelectAllBtn').prop('checked', false);
//                 } else {
//                     var allChecked = $('.sr_checkbox:checked').length === $('.sr_checkbox').length;
//                     $('#CSelectAllBtn').prop('checked', allChecked);
//                 }
//             });

//             $('#CSelectAllBtn').on('change', function() {
//                 $('.sr_checkbox').prop('checked', this.checked);
//             });
//         }
//     });

//     // 🔹 Add instant search with debounce
//     let searchDelay;
//     $('#QualityCheckTable_List_filter input')
//         .unbind()
//         .bind('keyup', function () {
//             clearTimeout(searchDelay);
//             let searchTerm = this.value;
//             searchDelay = setTimeout(function () {
//                 table.search(searchTerm).draw();
//             }, 400); // 0.4 sec delay
//         });

//     // When filters change, reload the table
//     $('input[name="status"], #FromDate, #ToDate, input[name="STtimeLine"], #location_id').on('change', function() {
//         table.ajax.reload();
//     });

//     // Error handling for DataTables
//     $.fn.dataTable.ext.errMode = 'none';
//     $('#QualityCheckTable_List').on('error.dt', function(e, settings, techNote, message) {
//         console.error('DataTables Error:', message);
//         toastr.error('DataTables error occurred. Please try again.');
//     });
// });



    
    
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
    // console.log(selectedFields);
    
    


    const params = new URLSearchParams();
    params.append('status', '{{ $status }}');
    params.append('from_date', '{{ $from_date }}');
    params.append('to_date', '{{ $to_date }}');
    params.append('timeline', '{{ $timeline }}');
    params.append('location', '{{ $location }}');
    
         if (selected.length > 0) {
      params.append('selected_ids', JSON.stringify(selected));
    }
        if (selectedFields.length > 0) {
      params.append('fields', JSON.stringify(selectedFields));
    }

    const url = `{{ route('admin.asset_management.quality_check.export_quality_check') }}?${params.toString()}`;
    window.location.href = url;
  });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
    });
    
 function DeleteRecord(id, redirect = window.location.href) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want delete this QC record",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Enter remarks here...',
        inputAttributes: {
            rows: 4
        },
        showCancelButton: true,
        cancelButtonColor: '#6c757d',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
        preConfirm: (remarks) => {
            if (!remarks || !remarks.trim()) {
                Swal.showValidationMessage('Remarks are required');
            }
            return remarks.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const Remarks = result.value;
            $.ajax({
                url: "{{ route('admin.asset_management.quality_check.destroy') }}",
                type: "POST",
                data: {
                    id: id,
                    remarks: Remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Deleted! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 2000
                        });

                        setTimeout(function() {
                            window.location.href = redirect;
                        }, 1000);
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Warning! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 3000
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error! The network connection has failed. Please try again later.',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 3000
                    });
                }
            });
        }
    });
}

</script>

@endsection
</x-app-layout>
