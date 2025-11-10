<x-app-layout>
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="card-title h4 fw-bold">Recruiters</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">HR Status</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">Recruiters</a></li>
                                </ol>
                            </nav>
                        </div>
            
                        <div class="col-12">
                            <div class="row d-flex">
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="FromDate">From Date</label>
                                        <input type="date" name="from_date" id="FromDate" class="form-control"
                                            value="{{ !empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
            
                                <!-- To Date -->
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="ToDate">To Date</label>
                                        <input type="date" name="to_date" id="ToDate" class="form-control"
                                            value="{{ !empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="Fil_CityID">Select Zone</label>
                                        <select class="form-select border-0 custom-select2-field" id="Fil_CityID"
                                            style="width:100%;" onchange="CommonstatuswiseFilter()">
                                            <option value="">Select Zone</option>
                                            @if(isset($cities))
                                            @foreach($cities as $val)
                                            <option value="{{$val->id}}" {{$city_id==$val->id ? 'selected' :
                                                ''}}>{{$val->city_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="Fil_BGVStatus">Select Application Status</label>
                                        <select class="form-select border-0 custom-select2-field" id="Fil_RiderStatus"
                                            style="width:100%;" onchange="CommonstatuswiseFilter()">
                                            <option value="">Select Status</option>
                                            <option value="0" {{$rider_status==0 ? 'selected' : '' }}>Pending</option>
                                            <option value="3" {{$rider_status==3 ? 'selected' : '' }}>Approved</option>
                                            <option value="1" {{$rider_status==1 ? 'selected' : '' }}>Live</option>
                                            <option value="2" {{$rider_status==2 ? 'selected' : '' }}>Rejected</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-end align-items-center mt-1">
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="Fil_BGVStatus">Select BGV Status</label>
                                        <select class="form-select border-0 custom-select2-field" id="Fil_BGVStatus"
                                            style="width:100%;" onchange="CommonstatuswiseFilter()">
                                            <option value="">Select BGV Status</option>
                                            <option value="0" {{$bgv_status==0 ? 'selected' : '' }}>Pending</option>
                                            <option value="1" {{$bgv_status==1 ? 'selected' : '' }}>Completed</option>
                                            <option value="2" {{$bgv_status==2 ? 'selected' : '' }}>Rejected</option>
                                            <option value="3" {{$bgv_status==3 ? 'selected' : '' }}>Hold</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold"
                                            style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                     <div>
                                         <label for="">Select Role</label>
                                        <select class="form-select border-0 custom-select2-field" id="Fil_RollType" onchange="CommonstatuswiseFilter()">
                                          <option value="">Select Role</option>
                                          <option value="deliveryman" {{$roll_type == 'deliveryman' ? 'selected' : ''}}>Rider</option>
                                          <option value="adhoc" {{$roll_type == 'adhoc' ? 'selected' : ''}}>Adhoc</option>
                                          <option value="in-house" {{$roll_type == 'in-house' ? 'selected' : ''}}>Employee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5 col-12 p-3 rounded d-flex flex-column flex-sm-row align-items-center justify-content-end gap-2"
                                    style="background:#ffffff;">
                                    <button class="btn btn-dark me-2 px-4" onclick="DatewiseFiler()">Filter</button>
                                    <a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}"
                                        class="btn btn-dark px-4">Reset</a>
                                 <a href="{{route('admin.Green-Drive-Ev.hr_status.add_candidate')}}"
                                        class="btn btn-dark px-4">Add Candidate</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- End Page Header -->

        <div class="table-responsive">
                    <table class="table custom-table text-center" style="width: 100%;">
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
                          
                        <tbody class="bg-white border border-white">
                           
                            @if(isset($lists))
                               @foreach($lists as $key => $val)
                                   <?php
                                     $full_name = ($val->first_name ?? '').' '.($val->last_name ?? '');
                                     $roll_type = '';
                                     if($val->work_type == 'deliveryman'){
                                         $roll_type = 'Rider';
                                     }
                                     else if($val->work_type == 'in-house'){
                                         $roll_type = 'Employee';
                                     }
                                     else if($val->work_type == 'adhoc'){
                                         $roll_type = 'Adhoc';
                                     }
                                     
                                     else if($val->work_type == 'helper'){
                                         $roll_type = 'Helper';
                                     }$image = $val->photo ? asset('public/EV/images/photos/'.$val->photo) : asset('public/admin-assets/img/person.png');
                                   ?>
                                   <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$val->reg_application_id ?? '00000000000000'}}</td>
                                        <td>{{$val->emp_id ?? '-'}}</td>
                                        <td>
                                            <div onclick="Profile_Image_View('{{$image}}')">
                                                <img src="{{$image}}" alt="Image" class="profile-image">
                                            </div>
                                        </td>
                                        <td>{{$full_name}}</td>
                                        <td>{{$val->email ?? ''}}</td>
                                        <td>{{$val->current_city->city_name ?? ''}}</td>
                                        <td>{{$val->register_date_time ? date('d M Y',strtotime($val->register_date_time)) : ''}}</td>
                                        <td>{{$roll_type}}</td>
                                        <td>{{$val->RiderType->type ?? ''}}</td>
                                        <td>{{ $val->probation_from_date ? date('d M Y', strtotime($val->probation_from_date)) : '-' }}</td>
                                        <td>{{ $val->probation_to_date ? date('d M Y', strtotime($val->probation_to_date)) : '-' }}</td>

                                        <td>
                                            @if($val->rider_status == 3)
                                               <button class="btn success-btn-custom btn-md px-5">Accepted</button>
                                            @elseif($val->rider_status == 2)
                                              <button class="btn reject-btn-custom btn-md px-5">Rejected</button>
                                            @elseif($val->rider_status == 1)
                                              <button class="btn live-btn-custom btn-md px-6">Live</button>
                                            @elseif($val->rider_status == 0)
                                              <button class="btn info-btn-custom btn-md px-5">Pending</button>
                                            @else
                                               <button class="btn btn-warning btn-md px-5">N/A</button>
                                            @endif
                                        </td>
                                        <td>
                                            @if($val->kyc_verify == 1)
                                               <button class="btn success-btn-custom btn-md px-6">&nbsp;Verified&nbsp;</button>
                                            @elseif($val->kyc_verify == 2)
                                               <button class="btn reject-btn-custom btn-md px-6">&nbsp;Rejected&nbsp;</button>
                                            @elseif($val->kyc_verify == 3)
                                               <button class="btn hold-btn-custom btn-md px-7">&nbsp;Hold&nbsp;</button>
                                            @else
                                               <button class="btn danger-btn-custom btn-md px-5">Not&nbsp;Verified</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('admin.Green-Drive-Ev.hr_status.recruiter.bgv_comment_view',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/yellow_eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>

                                        
                                        <td>
                                            <a href="{{route('admin.Green-Drive-Ev.hr_status.recruiter.bgv_documnet_view',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/green_eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                        
                                         <td>
                                            <a href="{{route('admin.Green-Drive-Ev.hr_status.recruiter.preview',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                        
                                         <td class="text-center">
                                            <div onclick="HrQuery_open_function('{{$val->id}}')">
                                                <img src="{{asset('public/admin-assets/img/blue_document.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>
                                         <td class="text-center">
                                            <a href="{{route('admin.Green-Drive-Ev.hr_status.edit_candidate', $val->id)}}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        </td>
                                        <td class="text-center" onclick="route_alert('{{ route('admin.Green-Drive-Ev.delivery-man.delete', $val->id)}}','this Candidate')">
                                            <div>
                                                <img src="{{asset('public/admin-assets/img/delete_image.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>
                                        
                                        <td class="text-center">
                                            <div class="d-flex">
                                            @if($val->approver_id == "" || $val->as_approve_datetime == "")    
                                                <button class="btn success-btn-custom btn-md me-2 px-4" 
                                                    onclick="AcceptApplication_status('{{ route('admin.Green-Drive-Ev.delivery-man.application_status_approve', $val->id) }}','Approve this Candidate')">
                                                    Accept
                                                </button>
                                                <button class="btn danger-btn-custom btn-md" 
                                                    onclick="RejectApplication_status('{{ route('admin.Green-Drive-Ev.delivery-man.application_status_reject', $val->id) }}', '{{$val->id}}', 'Reject this Candidate')">
                                                    Rejected
                                                </button>
                                            @else
                                                  @if($val->approved_status == 1)
                                                        <button class="btn btn-md me-2 px-4 w-100" style="border:1px solid #52c552 !important; color:#52c552 !important;">
                                                            APPROVED
                                                        </button>
                                                    @elseif($val->approved_status == 2)
                                                        <button class="btn btn-md me-2 px-4 w-100" style="border:1px solid #f36263 !important; color:#f36263 !important;">
                                                            REJECTED
                                                        </button>
                                                    @else
                                                        <button class="btn btn-md me-2 px-4 w-100" style="border:1px solid #f7f125  !important; color: #f7f125  !important;"   >
                                                            N/A {{$val->approved_status}}
                                                        </button>
                                                    @endif
                                            @endif
                                            </div>
                                        </td>
                                   </tr>
                               @endforeach
                            @endif
                        </tbody>
                      
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
@endsection
</x-app-layout>
