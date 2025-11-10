<x-app-layout>
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
<div class="col-12 mb-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        
        <!-- Left Section (Title + Breadcrumb) -->
        <div>
            <div class="card-title h4 fw-bold mb-2">Recruiters</div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#">HR Status</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Recruiters</li>
                </ol>
            </nav>
        </div>

        <!-- Right Section (Buttons + Filter) -->
        <div class="d-flex flex-column flex-sm-row align-items-center gap-2">
            <a href="{{ route('admin.Green-Drive-Ev.hr_status.add_candidate') }}"
               class="btn btn-dark px-4">
                Add Candidate
            </a>
            <div class="bg-white p-2 px-3 border rounded text-center cursor-pointer" 
                 onclick="RightSideFilerOpen()">
                <i class="bi bi-filter fs-5"></i> Filters
            </div>
        </div>
        
    </div>
</div>

            
                        <!--<div class="col-12">-->
                        <!--    <div class="row d-flex">-->
                        <!--        <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--            <div>-->
                        <!--                <label for="FromDate">From Date</label>-->
                        <!--                <input type="date" name="from_date" id="FromDate" class="form-control">-->
                        <!--            </div>-->
                        <!--        </div>-->
            
                                <!-- To Date -->
                        <!--        <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--            <div>-->
                        <!--                <label for="ToDate">To Date</label>-->
                        <!--                <input type="date" name="to_date" id="ToDate" class="form-control">-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--            <div>-->
                        <!--                <label for="Fil_CityID">Select Zone</label>-->
                        <!--                <select class="form-select border-0 custom-select2-field" id="Fil_CityID"-->
                        <!--                    style="width:100%;" onchange="CommonstatuswiseFilter()">-->
                        <!--                    <option value="">Select Zone</option>-->
                        <!--                    @if(isset($cities))-->
                        <!--                    @foreach($cities as $val)-->
                        <!--                    <option value="{{$val->id}}" ></option>{{$val->city_name}}</option>-->
                        <!--                    @endforeach-->
                        <!--                    @endif-->
                        <!--                </select>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--            <div>-->
                        <!--                <label for="Fil_BGVStatus">Select Application Status</label>-->
                        <!--                <select class="form-select border-0 custom-select2-field" id="Fil_RiderStatus"-->
                        <!--                    style="width:100%;" onchange="CommonstatuswiseFilter()">-->
                        <!--                    <option value="">Select Status</option>-->
                        <!--                    <option value="0" >Pending</option>-->
                        <!--                    <option value="3" >Approved</option>-->
                        <!--                    <option value="1" >Live</option>-->
                        <!--                    <option value="2" >Rejected</option>-->
                                            
                        <!--                </select>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--    <div class="row d-flex justify-content-end align-items-center mt-1">-->
                        <!--        <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--            <div>-->
                        <!--                <label for="Fil_BGVStatus">Select BGV Status</label>-->
                        <!--                <select class="form-select border-0 custom-select2-field" id="Fil_BGVStatus"-->
                        <!--                    style="width:100%;" onchange="CommonstatuswiseFilter()">-->
                        <!--                    <option value="">Select BGV Status</option>-->
                        <!--                    <option value="0" >Pending</option>-->
                        <!--                    <option value="1" >Completed</option>-->
                        <!--                    <option value="2" >Rejected</option>-->
                        <!--                    <option value="3" >Hold</option>-->
                        <!--                </select>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="col-md-3 col-12 rounded d-flex align-items-center" style="background:#ffffff;">-->
                        <!--            <div class="me-2">-->
                        <!--                <i class="bi bi-calendar p-2 fw-bold"-->
                        <!--                    style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                        <!--            </div>-->
                        <!--             <div>-->
                        <!--                 <label for="">Select Role</label>-->
                        <!--                <select class="form-select border-0 custom-select2-field" id="Fil_RollType" onchange="CommonstatuswiseFilter()">-->
                        <!--                  <option value="">Select Role</option>-->
                        <!--                  <option value="deliveryman" >Rider</option>-->
                        <!--                  <option value="adhoc" >Adhoc</option>-->
                        <!--                  <option value="in-house" >Employee</option>-->
                        <!--                </select>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--        <div class="col-md-5 col-12 p-3 rounded d-flex flex-column flex-sm-row align-items-center justify-content-end gap-2"-->
                        <!--            style="background:#ffffff;">-->
                        <!--            <button class="btn btn-dark me-2 px-4" onclick="DatewiseFiler()">Filter</button>-->
                        <!--            <a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}"-->
                        <!--                class="btn btn-dark px-4">Reset</a>-->
                        <!--         <a href="{{route('admin.Green-Drive-Ev.hr_status.add_candidate')}}"-->
                        <!--                class="btn btn-dark px-4">Add Candidate</a>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>

        <!-- End Page Header -->

        <div class="table-responsive">
                    <table id="recruiterTable" class="table text-center" style="width: 100%;">
                          <thead class="bg-success rounded">
                            <tr>
                              <th scope="col" class="text-white">#</th>
                              <th scope="col" class="text-white">Candidate ID</th>
                              <th scope="col" class="text-white">GDM ID</th>
                              <th scope="col" class="text-white">Image</th>
                              <th scope="col" class="text-white">Candidate Name</th>
                              <th scope="col" class="text-white">Email ID</th>
                              <th scope="col" class="text-white">Location</th>
                              <th scope="col" class="text-white">Submitted At</th>
                              <th scope="col" class="text-white">Role</th>
                              <th scope="col" class="text-white">Role Type</th>
                              <th scope="col" class="text-white">Provision From Date</th>
                              <th scope="col" class="text-white">Provision To Date</th>
                              <th scope="col" class="text-white">Application Status</th>
                              <th scope="col" class="text-white">BGV Status</th>
                              <th scope="col" class="text-white">BGV Comment</th>
                              <th scope="col" class="text-white">BGV Documents</th>
                              <th scope="col" class="text-white">View</th>
                              <th scope="col" class="text-white">Query</th>
                              <th scope="col" class="text-white">Edit</th>
                              <th scope="col" class="text-white">Delete</th>
                              <th scope="col" class="text-white">Action</th>
                            </tr>
                          </thead>
                         
                        <tbody class="bg-white border border-white"></tbody> 
                        
                      
                        </table>
                </div>
           
    </div>
    
       <div class="modal fade" id="Application_status_remark_update" data-bs-backdrop="static" tabindex="-1" aria-labelledby="Application_status_remarkLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <form>
              <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->
                <div class="modal-header border-0 d-flex justify-content-center">
                  <div>
                    <h1 class="h3 fs-5 text-center" id="Application_status_remarkLabel">Reject Candidate</h1>
                    <p class="text-center">Are you sure you want to Reject?</p>
                  </div>
                </div>
                <div class="modal-body">
                  <input type="hidden" id="dm_row_id">
                  <label for="remarks">Comments</label><br>
                  <textarea class="form-control" id="remarks" rows="6" placeholder="Enter the Comments"></textarea>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                  <button type="button" class="btn btn-dark" onclick="Cancel_remark_function()">No, Cancel</button>
                  <button type="button" class="btn btn-white" style="border: 1px solid;" onclick="Update_reject_remark_function()">Yes, Remark</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
       <div class="modal fade" id="Hr_query_save_modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="Hr_query_saveLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <form id="save_hr_query_form">
              <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->
                <div class="modal-header border-0 d-flex justify-content-center">
                  <div>
                    <h1 class="h3 fs-5 text-center" id="Hr_query_saveLabel">Query</h1>
                    <p class="text-center">Are you sure you want to make Query to Vendor?</p>
                  </div>
                </div>
                <div class="modal-body">
                  <input type="hidden" id="query_dm_id">
                  <label for="description">Comments</label><br><br>
                  <textarea class="form-control" id="query_description" rows="6" placeholder="Enter the Comments"></textarea>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                  <button type="button" class="btn btn-dark" onclick="query_modal_PopupHide()">No, Cancel</button>
                  <button type="button" class="btn btn-white" style="border: 1px solid;" onclick="Save_hrQuery_function()">Yes, Query</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
          <div class="modal fade" id="ProfileImage_srcModal" tabindex="-1" aria-labelledby="ProfileImage_srcModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-end">
                  <button type="button" class="btn btn-dark btn-sm btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="d-flex justify-content-center">
                      <img class="img-fluid" id="profile_image_src" src="">
                  </div>
                </div>
               
              </div>
            </form>
          </div>
        </div>
    
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Recruiters List</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            
            <div class="card mb-3">
                <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Filter</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id" class="form-control custom-select2-field">
                            <option value="">Select City</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}" >{{$city->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label" for="bgv_status">BGV Status</label>
                        <select name="bgv_status" id="bgv_status" class="form-control custom-select2-field">
                            <option value="">Select BGV Status</option>
                            <option value="1" >Verified</option>
                            <option value="2" >Rejected</option>
                            <option value="3" >Hold</option>
                            <!--<option value="4" >Not Verified</option>-->
                            
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="role_type">Role Type</label>
                        <select name="roll_type" id="roll_type" class="form-control custom-select2-field">
                            <option value="">Select Role</option>
                            <option value="deliveryman" >Rider</option>
                            <option value="adhoc" >Adhoc</option>
                            <option value="in-house" >Employee</option>
                            <option value="helper" >Helper</option>
                            
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="rider_status">Rider Status</label>
                        <select name="rider_status" id="rider_status" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="0" >Pending</option>
                            <option value="1" >Live</option>
                            <option value="2" >Rejected</option>
                            <option value="3" >Accepted</option>
                            
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
                        <input type="date" name="from_date" id="from_date" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearFilter()">Clear All</button>
                <button class="btn btn-success w-50" id="applyFilterBtn">Apply</button>
            </div>
            
          </div>
        </div>
        
@section('script_js')
<script>
   function DatewiseFiler(){
       var fromDate = $("#FromDate").val();
       var toDate = $("#ToDate").val();
       
       if (!fromDate || !toDate) {
            toastr.error("From date and To date fields are required");
            return;
        }
       var url = new URL(window.location.href);
       url.searchParams.set('from_date',fromDate);
       url.searchParams.set('to_date',toDate);
       window.location.href = url.toString();
   }
   
    function CitywiseFilter(value){
       var url = new URL(window.location.href);
       url.searchParams.set('city_id',value);
       window.location.href = url.toString();
   }
   
    function BGVstatuswiseFilter(value){
       var url = new URL(window.location.href);
       url.searchParams.set('bgv_status',value);
       window.location.href = url.toString();
   }
    function CommonstatuswiseFilter(){
       var fromDate = $("#FromDate").val();
       var toDate = $("#ToDate").val();
       var city_id = $("#Fil_CityID").val();
       var rider_status = $("#Fil_RiderStatus").val();
       var bgv_status = $("#Fil_BGVStatus").val();
       var roll_type = $("#Fil_RollType").val();
       var url = new URL(window.location.href);
       url.searchParams.set('from_date',fromDate);
       url.searchParams.set('to_date',toDate);
       url.searchParams.set('city_id',city_id);
       url.searchParams.set('rider_status',rider_status);
       url.searchParams.set('bgv_status',bgv_status);
       url.searchParams.set('roll_type',roll_type);
       window.location.href = url.toString();
   }
   
</script>
<script>
   function RollTypeFiler(value){
       var url = new URL(window.location.href);
       url.searchParams.set('roll_type',value);
       window.location.href = url.toString();
   }
   
   function Cancel_remark_function(){
        const modal = bootstrap.Modal.getInstance(document.getElementById('Application_status_remark_update'));
        if (modal) modal.hide();
        $("#remarks").val("");
        $("#dm_row_id").val("");
   }
   
   function query_modal_PopupHide(){
        const modal = bootstrap.Modal.getInstance(document.getElementById('Hr_query_save_modal'));
        if (modal) modal.hide();
        $("#query_description").val("");
        $("#query_dm_id").val("");
   }
   
    function HrQuery_open_function(id){
         $("#Hr_query_save_modal").modal("show");
        $("#query_description").val("");
        $("#query_dm_id").val(id);
   }
   
   function Save_hrQuery_function(){
        var dm_id = $("#query_dm_id").val();
                var remarks = $("#query_description").val();
            
                if (remarks.trim() === "") {
                    toastr.error("Query Remarks field is required. Please enter a comment.");
                    return;
                }
            
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.hr_status.recruiter.query_add') }}",
                    type: "POST",
                    data: {
                        dm_id:dm_id,
                        remarks: remarks,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("Added!", response.message, "success");
                            const modal = bootstrap.Modal.getInstance(document.getElementById('Hr_query_save_modal'));
                            if (modal) modal.hide();
                            $("#query_dm_id").val("");
                            $("#query_description").val("");
                            // setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                    }
                });
   }
   
   
   
    function AcceptApplication_status(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes, Accept it!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#28a745",  
            cancelButtonColor: "#343a40",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                        url: route,
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Accepted!", response.message, "success");
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                        }
                    });
            }
        });
    }
    
    function RejectApplication_status(route, id, message, title = "Are you sure?") {
        $("#Application_status_remark_update").modal('show');
        $("#dm_row_id").val(id);
    }
    
    function Update_reject_remark_function() {
            var dm_id = $("#dm_row_id").val();
            var remarks = $("#remarks").val();
        
            if (remarks.trim() === "") {
                toastr.error("Remarks field is required. Please enter a comment.");
                return;
            }
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.delivery-man.application_status_reject') }}",
                type: "POST",
                data: {
                    dm_id:dm_id,
                    remarks: remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Rejected!", response.message, "success");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('Application_status_remark_update'));
                        if (modal) modal.hide();
                        $("#remarks").val("");
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                }
            });
        }
        
     function route_alert(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            setTimeout(function() {
                                location.reload(); 
                            }, 1000);
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }

                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                    }
                });
            }
        });
    }

    function Profile_Image_View(src){
        $("#ProfileImage_srcModal").modal("show");
        $("#profile_image_src").attr("src",src);
        
    }

   
</script>
<script>
   $(document).ready(function() {
    var table = $('#recruiterTable').DataTable({
        processing: true,
        serverSide: true, // 🔹 Enable server-side
        pageLength: 10,   // 🔹 Load only 10 records initially
        ajax: {
            url: "{{ route('admin.Green-Drive-Ev.hr_status.index') }}",
            data: function(d){
                d.from_date   = $('#from_date').val();
                d.to_date     = $('#to_date').val();
                d.roll_type   = $('#roll_type').val();
                d.city_id     = $('#city_id').val();
                d.bgv_status  = $('#bgv_status').val();
                d.rider_status= $('#rider_status').val();
            },
            beforeSend: function () {
                $('#recruiterTable tbody').html(`
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
                $('#recruiterTable tbody').html(`
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
            {data: 'id'},
            {data: 'candidate_id'},
            {data: 'gdm_id'},
            {data: 'image'},
            {data: 'full_name'},
            {data: 'email'},
            {data: 'location'},
            {data: 'submitted_at'},
            {data: 'role'},
            {data: 'role_type'},
            {data: 'probation_from'},
            {data: 'probation_to'},
            {data: 'application_status'},
            {data: 'bgv_status'},
            {data: 'bgv_comment'},
            {data: 'bgv_documents'},
            {data: 'view'},
            {data: 'query'},
            {data: 'edit'},
            {data: 'delete'},
            {data: 'action'},
        ],
        columnDefs: [
            { targets: [3,14,15,16,17,18,19,20], orderable: false, searchable: false },
        ],
        scrollX:true,
    });

    $('#applyFilterBtn').on('click', function(e){
        table.ajax.reload();
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) bsOffcanvas.hide();
    });

    window.clearFilter = function() {
        $('#to_date, #from_date').val('');
        $('#city_id, #rider_status, #bgv_status, #roll_type').val(null).trigger('change');
        table.ajax.reload();
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) bsOffcanvas.hide();
    }
});


function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
</script>
@endsection
</x-app-layout>
