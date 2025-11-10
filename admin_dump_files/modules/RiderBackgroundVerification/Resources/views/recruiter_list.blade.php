<x-app-layout>
    <div class="main-content">

           <div class="card bg-transparent my-4">
                <div class="card-header d-flex align-items-center justify-content-between" style="background:#fbfbfb;">
                    <div>
                        <div class="card-title h4 fw-bold">Recruiters</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">HR Status</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Recruiters</a></li>
                            </ol>
                        </nav>
                    </div>
            
                    <!-- Role Selector -->
                    <div class="p-3 rounded d-flex align-items-center rounded" style="background:#ffffff;">
                        <div class="me-2">
                            <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                        </div>
                        <div>
                            <select class="form-select border-0" onchange="RollTypeFiler(this.value)">
                              <option value="">Select Role</option>
                              <option value="deliveryman" {{$roll_type == 'deliveryman' ? 'selected' : ''}}>Rider</option>
                              <option value="adhoc" {{$roll_type == 'adhoc' ? 'selected' : ''}}>Adhoc</option>
                              <option value="in-house" {{$roll_type == 'in-house' ? 'selected' : ''}}>Employee</option>
                            </select>
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
                              <th scope="col" class="text-white">Image</th>
                              <th scope="col" class="text-white">Candidate Name</th>
                              <th scope="col" class="text-white">Email ID</th>
                              <th scope="col" class="text-white">Location</th>
                              <th scope="col" class="text-white">Applied At</th>
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
                                     
                                     $image = $val->photo ? asset('public/EV/images/photos/'.$val->photo) : asset('public/admin-assets/img/person.png');
                                   ?>
                                   <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$val->reg_application_id ?? '00000000000000'}}</td>
                                        <td>
                                            <div>
                                                <img src="{{$image}}" alt="Image" class="profile-image">
                                            </div>
                                        </td>
                                        <td>{{$full_name}}</td>
                                        <td>{{$val->email ?? ''}}</td>
                                        <td>{{$val->current_city_id ?? '44'}}</td>
                                        <td>{{date('d M Y',strtotime($val->created_at))}}</td>
                                        <td>{{$roll_type}}</td>
                                        <td>{{$val->RiderType->type ?? ''}}</td>
                                        <td>{{ $val->probation_from_date ? date('d M Y', strtotime($val->probation_from_date)) : '-' }}</td>
                                        <td>{{ $val->probation_to_date ? date('d M Y', strtotime($val->probation_to_date)) : '-' }}</td>

                                        <td>
                                            @if($val->rider_status == 3)
                                               <button class="btn success-btn-custom btn-md px-6">Accept</button>
                                            @elseif($val->rider_status == 2)
                                              <button class="btn live-btn-custom btn-md px-6">Rejected</button>
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
                                            @else
                                               <button class="btn danger-btn-custom btn-md px-5">Not&nbsp;Verified</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>

                                        
                                        <td>
                                            <a href="{{route('admin.Green-Drive-Ev.delivery-man.preview',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                        
                                         <td>
                                            <a href="{{route('admin.Green-Drive-Ev.background_verification.recruiter.preview',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                        
                                         <td class="text-center">
                                            <div>
                                                <img src="{{asset('public/admin-assets/img/blue_document.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>
                                        <td class="text-center">
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
                                               <button class="btn success-btn-custom btn-md me-2 px-4">
                                                    Accept
                                                </button>
                                                <button class="btn danger-btn-custom btn-md">
                                                    Rejected
                                                </button>
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
    
@section('script_js')
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

   
</script>
@endsection
</x-app-layout>
