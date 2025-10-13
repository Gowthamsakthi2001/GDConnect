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
    .dropdown-menu {
      max-width: 250px;
      word-wrap: break-word;
    }

body {
  overflow-x: hidden !important;
}


</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0">Customer Master<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{count($lists)}}</span></div>
                            
                           
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                           
                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray"  id="exportBtn"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightCmSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                                <div class="m-2 btn btn-success d-flex align-items-center px-3"
                                     style="cursor: pointer;" 
                                     onclick="window.location.href='{{ route('admin.Green-Drive-Ev.master_management.customer_master.create') }}'">
                                    <i class="bi bi-plus-lg me-2 fs-6"></i> Create
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Page Header -->
        


        <div class="table-responsive">
                    <table id="CustomerMasterTableList" class="table text-center" style="width: 100%;">
                        <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                                <th>
                                    <div class="form-check">
                                      <input style="width:25px !important;height:25px !important;" class="form-check-input" style="padding:0.7rem;" type="checkbox" value=""  id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                                <th scope="col" class="custom-dark text-center">Customer ID</th>
                                <th scope="col" class="custom-dark text-center">Company / Customer Name</th>
                                 <th scope="col" class="custom-dark text-center">Trade Name</th>
                                <th scope="col" class="custom-dark text-center">Customer Type</th>
                                <th scope="col" class="custom-dark text-center">Business Type</th>
                                <th scope="col" class="custom-dark text-center">Created At</th>
                                <th scope="col" class="custom-dark text-center">Status</th>
                                <th scope="col" class="custom-dark text-center">Active/In Active</th>
                                <th scope="col" class="custom-dark text-center">Action</th>
                            </tr>
                        </thead>
                    

                        <tbody class="bg-white border border-white">

                        @if(isset($lists))
                            @foreach($lists as $val)
                                <?php
                                    $status = $val->status;
                                    $colorClass = match ($val->status) {
                                        1 => 'text-success',
                                        0 => 'text-danger',
                                        default => 'text-secondary',
                                    };
                                     
                                    $statusName = $val->status == 1 ? 'Active' : 'Inactive';
                                ?>
                    
                                <tr>
                                    <td>
                                       <div class="form-check">
                                          <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="{{$val->id}}">
                                        </div>
                                   </td>
                                   <td>
                                       
                                       {{$val->id}}
                                   </td>
                                   <td>{{ \Illuminate\Support\Str::limit($val->name, 50, '...') }}</td>
                                    <td>
                                        {{$val->trade_name ?? '-'}}
                                    </td>
                                   <td>
                                        @switch($val->customer_type)
                                            @case(1)
                                                Individual
                                                @break
                                            @case(2)
                                                Company
                                                @break
                                            @default
                                                N/A
                                        @endswitch
                                    </td>
                                    

                                    <td>
                                       {{$val->constitution_type->name ?? '-'}}
                                   </td>
                                    
                                    <td >{{ date('d M Y h:i:s A',strtotime($val->created_at)) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                                            <span class="text-capitalize">{{ $statusName }}</span>
                                        </div>
                                    </td>
                    
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                            <input class="form-check-input toggle-status" data-id="{{ $val->id }}" type="checkbox" role="switch" id="toggleSwitch{{ $loop->index }}" {{$status == 1 ? 'checked' : ''}}>
                                        </div>
                                    </td>
                                    <?php
                                      $id_encode = encrypt($val->id);
                                    ?>
                                    
                                   <td class="text-center align-middle">
                                       <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route('admin.Green-Drive-Ev.master_management.customer_master.edit',$id_encode) }}" class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-pencil-square me-2 fs-18"></i>
                                        </a>
                                        
                                          <a href="{{ route('admin.Green-Drive-Ev.master_management.customer_master.login_credential', $id_encode) }}" 
                                           class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-arrow-in-right fs-22 text-success"></i>
                                        </a>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                         @endif
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
                      <button type="button" class="btn text-white" style="background:#26c360;" onclick="CustomerMaster_Excel_Export()">Download</button>
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
                        <label class="form-check-label mb-0" for="field3">Customer ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="customer_id" name="customer_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">Customer Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="customer_type" id="customer_type">
                        </div>
                      </div>
                    </div>
                

                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Bussiness Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="bussiness_type" name="bussiness_type">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Business Constitution Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="business_constitution_type" name="business_constitution_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Company Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="company_name" name="company_name">
                        </div>
                      </div>
                    </div>
                    
                      <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Trade Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="trade_name" name="trade_name">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Email ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="email" name="email">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="phone" name="phone">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="address" name="address">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="City" name="City">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">State</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="state" name="state">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">GST No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="gst_no" name="gst_no">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">PAN No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="pan_no" name="pan_no">
                        </div>
                      </div>
                    </div>
                    
                  <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Adhaar Front Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="adhaar_front" name="adhaar_front">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Adhaar Back Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="adhaar_back" name="adhaar_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Pan Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="pan" name="pan">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">GST Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="gst_image" name="gst_image">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Other Bussiness <br>Proof Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="other_bussiness_proof" name="other_bussiness_proof">
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
                        <label class="form-check-label mb-0" for="field12">POC Details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="poc_details" name="poc_details">
                        </div>
                      </div>
                    </div>
                    
                 <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Customer Hubs</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="customer_hubs" name="customer_hubs">
                        </div>
                      </div>
                    </div>
                
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
        
        
      
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightCustomerMaster01" aria-labelledby="offcanvasRightCustomerMaster01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightCustomerMaster01Label">Customer Master Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearCustomerMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyCustomerMasterFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
              <?php
                $checked_status = request('status');
                $from_date = request('from_date');
                $to_date = request('to_date');
              ?>
               <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status" value="all" {{$ch_status == 'all' ? 'checked': ''}}>
                      <label class="form-check-label" for="status">
                       All
                      </label>
                    </div>
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{$ch_status == 1 ? 'checked': ''}}>
                      <label class="form-check-label" for="status1">
                       Active
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="status" id="status2" value="0" {{$ch_status == 0 ? 'checked': ''}}>
                      <label class="form-check-label" for="status2">
                        Inactive
                      </label>
                    </div>
                    
               </div>
           </div>

            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1" value="today" {{$timeline == "today" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2" value="this_week" {{$timeline == "this_week" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3" value="this_month" {{$timeline == "this_month" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4" value="this_year" {{$timeline == "this_year" ? 'checked' : ''}}>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearCustomerMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyCustomerMasterFilter()">Apply</button>
            </div>
            
          </div>
        </div>
    

@section('script_js')


<script>
    
    
    function CustomerMaster_Excel_Export(){
        let selected = [];
        document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
          selected.push(cb.value);
        });
    
    
        const selectedFields = [];
    
    document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
      selectedFields.push({
        name: cb.name,
        value: cb.value
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
        
        if (selected.length > 0) {
          params.append('selected_ids', JSON.stringify(selected));
        }
        
                if (selectedFields.length > 0) {
      params.append('fields', JSON.stringify(selectedFields));
    }

    
        const url = `{{ route('admin.Green-Drive-Ev.master_management.customer_master.export_customer_master') }}?${params.toString()}`;
        window.location.href = url;
    }
    
    $(document).ready(function () {
      $('#CustomerMasterTableList').DataTable({
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
    
    function applyCustomerMasterFilter() {
        const selectedStatus = document.querySelector('input[name="status"]:checked');
        const status = selectedStatus ? selectedStatus.value : 'all';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
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


    
    function clearCustomerMasterFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('status');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        window.location.href = url.toString();
    }



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
    //   document.addEventListener('DOMContentLoaded', function () {
    //     const selectAll = document.getElementById('field1');
    //     const checkboxes = document.querySelectorAll('.form-check-input');
    
    //     selectAll.addEventListener('change', function () {
    //       checkboxes.forEach(checkbox => {
    //         if (checkbox !== selectAll) {
    //           checkbox.checked = selectAll.checked;
    //         }
    //       });
    //     });
    
    //     // Optional: Update "Select All" if any individual checkbox is unchecked
    //     checkboxes.forEach(checkbox => {
    //       if (checkbox !== selectAll) {
    //         checkbox.addEventListener('change', function () {
    //           const allChecked = Array.from(checkboxes)
    //             .filter(cb => cb !== selectAll)
    //             .every(cb => cb.checked);
    //           selectAll.checked = allChecked;
    //         });
    //       }
    //     });
    //   });
    
 
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
    
    function RightCmSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightCustomerMaster01');
        bsOffcanvas.show();
    }
    
  
</script>

<script>
    $(document).ready(function () {
    $('.toggle-status').change(function (e) {
        e.preventDefault();

        var checkbox = $(this);
        var brandId = checkbox.data('id');
        var intendedStatus = checkbox.is(':checked') ? 1 : 0;

        // Temporarily revert the checkbox until confirmed
        checkbox.prop('checked', !intendedStatus);

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to ${intendedStatus ? 'Active' : 'Inactive'} this Customer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, confirm it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Now update checkbox state immediately after confirmation
                checkbox.prop('checked', intendedStatus);

                // Proceed with AJAX
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.status_update') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: brandId,
                        status: intendedStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Server error occurred.'
                        });
                    }
                });
            } else {
                // Cancelled - keep checkbox in original state
                checkbox.prop('checked', !intendedStatus);
            }
        });
    });
});

// function DeleteRecord(id){
//     Swal.fire({
//             title: 'Are you sure?',
//             text: `You want to delete this Label Name?`,
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Delete!'
//         }).then((result) => {
//             if (result.isConfirmed) {

//                 $.ajax({
//                     url: "{{ route('admin.asset_management.quality_check_list.destroy') }}",
//                     type: "POST",
//                     data: {
//                         _token: "{{ csrf_token() }}",
//                         id: id,
//                     },
//                     success: function (response) {
//                         if (response.success) {
//                             Swal.fire({
//                                 icon: 'success',
//                                 title: 'Deleted!',
//                                 text: response.message,
//                                 timer: 1500,
//                                 showConfirmButton: false
//                             }).then(() => {
//                                 location.reload();
//                             });
//                         } else {
//                             Swal.fire({
//                                 icon: 'error',
//                                 title: 'Failed!',
//                                 text: response.message
//                             });
//                         }
//                     },
//                     error: function () {
//                         Swal.fire({
//                             icon: 'error',
//                             title: 'Oops!',
//                             text: 'Server error occurred.'
//                         });
//                     }
//                 });
//             } else {
//                 console.log("else");
//             }
//         });
// }

</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
    });
</script>
@endsection
</x-app-layout>
