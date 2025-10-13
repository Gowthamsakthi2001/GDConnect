<x-app-layout>

    <div class="main-content">

           <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="card-title h4 fw-bold"><a href="{{ route('admin.Green-Drive-Ev.bgvvendor.summary') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>BGV</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">BGV Vendor</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:void(0);">BGV</a></li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-12">
                            <div class="row d-flex">
                                <div class="col-md-4 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div style=" width: 100%;">
                                        <label for="FromDate">From Date</label>
                                        <input type="date" name="from_date" id="FromDate" class="form-control" 
                                        value="{{ !empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : '' }}" style=" padding: 12px 28px; ">
                                    </div>
                                </div>
            
                                <!-- To Date -->
                                <div class="col-md-4 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div style=" width: 100%;">
                                        <label for="ToDate">To Date</label>
                                        <input type="date" name="to_date" id="ToDate" class="form-control"  value="{{ !empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : '' }}" style=" padding: 12px 28px; ">
                                    </div>
                                </div>
                                 <div class="col-md-4 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div style=" width: 100%;">
                                        <label for="Fil_CityID">Select Zone</label>
                                        <select class="form-select border-0 custom-select2-field" id="Fil_CityID" style="width:100%;" onchange="CitywiseFilter(this.value)">
                                          <option value="">Select Zone</option>
                                          @if(isset($cities))
                                            @foreach($cities as $val)
                                            <option value="{{$val->id}}" {{$city_id == $val->id ? 'selected' : ''}}>{{$val->city_name}}</option>
                                            @endforeach
                                          @endif
                                        </select>
                                    </div>
                                </div>
                                <!--  <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">-->
                                <!--    <div class="me-2">-->
                                <!--        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                                <!--    </div>-->
                                <!--    <div>-->
                                <!--        <label for="Fil_BGVStatus">Select BGV Status</label>-->
                                <!--        <select class="form-select border-0 custom-select2-field" id="Fil_BGVStatus" style="width:100%;" onchange="BGVstatuswiseFilter(this.value)">-->
                                <!--          <option value="">Select BGV Status</option>-->
                                <!--          <option value="0" {{$bgv_status == 0 ? 'selected' : ''}}>Pending</option>-->
                                <!--          <option value="1" {{$bgv_status == 1 ? 'selected' : ''}}>Completed</option> -->
                                <!--          <option value="2" {{$bgv_status == 2 ? 'selected' : ''}}>Rejected</option>-->
                                <!--          <option value="3" {{$bgv_status == 3 ? 'selected' : ''}}>Hold</option>-->
                                <!--        </select>-->
                                <!--    </div>-->
                                <!--</div>-->
                                
                                <div class="col-12 p-3 rounded d-flex flex-column flex-sm-row align-items-center justify-content-end" style="background:#ffffff;">
                                    <button class="btn btn-dark me-2 px-4" onclick="DatewiseFiler()">Filter</button>
                                    <a href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=> $type])}}" class="btn btn-dark px-4">Reset</a>
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
                              <th scope="col" class="text-white">Image</th>
                              <th scope="col" class="text-white">Candidate Name</th>
                              <th scope="col" class="text-white">Email ID</th>
                              <th scope="col" class="text-white">Submitted At</th>
                              <th scope="col" class="text-white">Ageing Days</th>
                              <th scope="col" class="text-white">Verified At</th>
                              <th scope="col" class="text-white">Role</th>
                              <th scope="col" class="text-white">Role Type</th>
                              <th scope="col" class="text-white">BGV Status</th>
                              <th scope="col" class="text-white">BGV Status Update <span class="text-success">BV</span></th>
                              <th scope="col" class="text-white">View Query</th>
                              <th scope="col" class="text-white">View</th>
                              <th scope="col" class="text-white">Remark</th>
                              <th scope="col" class="text-white">Upload</th>
                            </tr>
                          </thead>
                          <tbody class="bg-white border border-white">
                                @if(isset($lists))
                                    @foreach($lists as $key => $val)
                                    <?php
                                    $full_name = ($val->first_name ?? '') . ' ' . ($val->last_name ?? '');
                                    $roll_type = '';
                                    if ($val->work_type == 'deliveryman') {
                                        $roll_type = 'Rider';
                                    } else if ($val->work_type == 'in-house') {
                                        $roll_type = 'Employee';
                                    } else if ($val->work_type == 'adhoc') {
                                        $roll_type = 'Adhoc';
                                    }
                 
                                    $image = $val->photo ? asset('public/EV/images/photos/' . $val->photo) : asset('public/admin-assets/img/person.png');
                                    ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$val->reg_application_id ?? '00000000000000'}}</td>
                                        <td>
                                            <div onclick="Profile_Image_View('{{$image}}')">
                                                <img src="{{$image}}" alt="Image" class="profile-image">
                                            </div>
                                        </td>
                                        <td>{{$full_name}}</td>
                                        <td>{{$val->email ?? ''}}</td>
                                        @if($val->register_date_time != "")
                                          <td>{{date('d M Y h:i:s A',strtotime($val->register_date_time))}}</td>
                                        @else
                                          <td>-</td>
                                        @endif
                                        <?php
                                            if (!empty($val->bgv_approve_datetime)) {
                                                $created_date = \Carbon\Carbon::parse($val->register_date_time);
                                                $approved_date = \Carbon\Carbon::parse($val->bgv_approve_datetime);
                                                $ageing_days = $approved_date->diffInDays($created_date);
                                            }else{
                                                $created_date = \Carbon\Carbon::parse($val->register_date_time);
                                                $current_date = \Carbon\Carbon::now();
                                                $ageing_days = $current_date->diffInDays($created_date);
                              
                                            }
                                        ?>


                                        <td>{{$ageing_days}} Days</td>
                                        <td>
                                            {{ !empty($val->bgv_approve_datetime) ? date('d M Y h:i:s A', strtotime($val->bgv_approve_datetime)) : '-' }}
                                        </td>

                                        <td>{{$roll_type}}</td>
                                        <td>{{$val->RiderType->type ?? ''}}</td>
                                      
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
                                            <div>
                                                <select class="form-select border-1 bgv_status_{{$val->id}}"
                                                    {{ in_array($val->kyc_verify, [1, 2]) ? 'disabled' : '' }}
                                                    @if(in_array($val->kyc_verify, [0, 3]))
                                                        onfocus="this.setAttribute('data-prev', this.value)"
                                                        onchange="UpdateBGV_status(this, '{{ route('admin.Green-Drive-Ev.delivery-man.kyc_verify', $val->id) }}','{{$val->id}}')"
                                                        data-prev="{{ $val->kyc_verify }}"
                                                    @endif
                                                >
                                                    <option value="0" {{ $val->kyc_verify == 0 ? 'selected' : '' }}>Pending</option>
                                                    <option value="1" {{ $val->kyc_verify == 1 ? 'selected' : '' }}>Completed</option>
                                                    <option value="2" {{ $val->kyc_verify == 2 ? 'selected' : '' }}>Rejected</option>
                                                    <option value="3" {{ $val->kyc_verify == 3 ? 'selected' : '' }}>Hold</option>
                                                </select>
                                            </div>

                                        </td>
                                        
                                        <td>
                                            <a href="{{route('admin.Green-Drive-Ev.bgvvendor.recruiter_query_list',$val->id)}}"
                                                class="me-1 icon-btn">
                                                <img src="{{asset('public/admin-assets/img/blue_eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>

                                        <td>
                                            <a href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_doc_verify',$val->id)}}"
                                                class="me-1 icon-btn">
                                                
                                                <img src="{{asset('public/admin-assets/img/eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                         
                                        <td>
                                            <div onclick="BGV_Comment_PopupView('{{$val->id}}')">
                                                <img src="{{asset('public/admin-assets/img/document.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>
                                        
                                         <td class="text-center">
                                            <div onclick="BGV_Doc_PopupView('{{$val->id}}')">
                                                <img src="{{asset('public/admin-assets/img/upload.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>
                                        
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                </div>
           
    </div>
    
   <div class="modal fade" id="BGV_comments_update_modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="BGV_comments_updateLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form>
          <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->
            <div class="modal-header border-0 d-flex justify-content-center">
              <div>
                <h1 class="h3 fs-5 text-center" id="BGV_comments_updateLabel">Remarks</h1>
                <p class="text-center">Are you sure you want to Remark?</p>
              </div>
            </div>
            <div class="modal-body">
              <input type="hidden" id="dm_row_id">
              <label for="remarks">Comments</label>
              <textarea class="form-control" id="remarks" rows="6"></textarea>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
              <button type="button" class="btn btn-dark" onclick="Cancel_comment_function()">No, Cancel</button>
              <button type="button" class="btn btn-white" style="border: 1px solid;" onclick="Update_comment_function()">Yes, Remark</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <div class="modal fade" id="BGV_comments_save_modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="BGV_comments_saveLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form>
          <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->
            <div class="modal-header border-0 d-flex justify-content-center">
              <div>
                <h1 class="h3 fs-5 text-center" id="BGV_comments_saveLabel">Remarks</h1>
                <p class="text-center">Are you sure you want to Remark?</p>
              </div>
            </div>
            <div class="modal-body">
              <input type="hidden" id="dm_id">
              <label for="description">Comments</label>
              <textarea class="form-control" id="description" rows="6"></textarea>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
              <button type="button" class="btn btn-dark" onclick="BGV_Comment_PopupHide()">No, Cancel</button>
              <button type="button" class="btn btn-white" style="border: 1px solid;" onclick="Save_comment_function()">Yes, Remark</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <div class="modal fade" id="BGV_doc_save_modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="BGV_doc_saveLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form id="document_store_form" enctype="multipart/form-data">
            @csrf
          <div class="modal-content rounded-4 p-3">
            <div class="modal-header border-0 d-flex justify-content-between mb-3">
              <h1 class="h6 fs-5 text-center fw-medium">Upload Doc</h1>
              <div>
               <button type="button" class="btn btn-dark" onclick="BGV_Doc_PopupHide()">Cancel</button>
               <button type="button" class="btn btn-success" style="border: 1px solid;" onclick="Save_Doc_function()">Submit</button>
              </div>
            </div>
            <div class="modal-body">
              <input type="hidden" id="doc_dm_id" name="doc_dm_id">
              <label class="custom-upload-area col-12" for="documents" style="padding:100px;">
                  <span>Drag & drop files here or click to upload</span>
                  <span id="file-count" class="mt-2 d-block text-muted"></span>
                  <input type="file" id="documents" name="documents[]">
                </label>
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
    
    <!--<div class="modal fade" id="BGV_query_show_modal" tabindex="-1" aria-labelledby="BGV_query_showLabel" aria-hidden="true">-->
    <!--  <div class="modal-dialog modal-lg">-->
    <!--    <form>-->
    <!--      <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->-->
    <!--        <div class="modal-header border-0 d-flex justify-content-center">-->
    <!--          <div>-->
    <!--            <h1 class="h3 fs-5 text-center" id="BGV_query_showLabel">Query</h1>-->
    <!--            <p class="text-center">From HR</p>-->
    <!--          </div>-->
    <!--        </div>-->
    <!--        <div class="modal-body">-->
    <!--          <input type="hidden" id="dm_id">-->
    <!--          <label for="description" class="mb-2">Comment</label><br>-->
    <!--          <textarea class="form-control" id="description" rows="6">-->
    <!--              This query retrieves employee names, their department names, and salaries, sorted by salary in descending order.-->
    <!--          </textarea>-->
    <!--        </div>-->
    <!--        <div class="modal-footer border-0 d-flex justify-content-between">-->
              <!--<button type="button" class="btn btn-dark" onclick="Query_close_modal()">No, Cancel</button>-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </form>-->
    <!--  </div>-->
    <!--</div>-->

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
   
</script>
<script>
    document.querySelector("label[for='FromDate']").addEventListener('click', function() {
        document.getElementById('FromDate').showPicker?.(); // modern browsers
        document.getElementById('FromDate').focus();        // fallback
    });

    document.querySelector("label[for='ToDate']").addEventListener('click', function() {
        document.getElementById('ToDate').showPicker?.();
        document.getElementById('ToDate').focus();
    });
    
    
    function UpdateBGV_status(selectElement, route,id) {
            const selectedValue = selectElement.value;
            const previousValue = selectElement.getAttribute('data-prev');
           
           if(selectedValue == 2 || selectedValue == 3){
                $("#BGV_comments_update_modal").modal('show');
                $("#dm_row_id").val(id);
           }else{
               Swal.fire({
                title: "Are you sure?",
                text: "You want to update this Status?",
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
                        type: "POST",
                        data: {
                            status: selectedValue,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Updated!", response.message, "success");
                                selectElement.setAttribute('data-prev', selectedValue);
                                setTimeout(function(){
                                    window.location.reload();
                                }, 1000);
                                
                            } else {
                                Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                                selectElement.value = previousValue; // revert
                            }
                        },
                        error: function() {
                            Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                            selectElement.value = previousValue; // revert
                        }
                    });
                } else {
                    selectElement.value = previousValue; 
                }
            });
           }
        
            
        }

        function Cancel_comment_function(){
            var id = $("#dm_row_id").val(); 
            var selectElement = $(".bgv_status_" + id);
            var previousValue = selectElement.attr('data-prev');
            selectElement.val(previousValue);
             $("#BGV_comments_update_modal").modal('hide');
             $("#remarks").val("");
        }
        
        function Update_comment_function() {
                var dm_id = $("#dm_row_id").val();
                var bgv_status = $(".bgv_status_" + dm_id).val(); // FIXED: get value
                var remarks = $("#remarks").val();
            
                if (remarks.trim() === "") {
                    toastr.error("Remarks field is required. Please enter a comment.");
                    return;
                }
            
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.delivery-man.bgv_comment_update') }}",
                    type: "POST",
                    data: {
                        dm_id: dm_id,
                        bgv_status: bgv_status,
                        remarks: remarks,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("Added!", response.message, "success");
                            
                            // Close the modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('BGV_comments_update_modal'));
                            if (modal) modal.hide();
                            
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000);
                                
                            // Clear the textarea
                            $("#remarks").val("");
                            $("#dm_row_id").val("");
                        } else {
                            Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                    }
                });
            }

        function BGV_Comment_PopupView(id){
            $("#BGV_comments_save_modal").modal('show');
            $("#dm_id").val(id);
            $("#description").val("");
        }
        function BGV_Comment_PopupHide(){
            $("#BGV_comments_save_modal").modal('hide');
            $("#dm_id").val("");
            $("#description").val("");
        }
        
    
        
        function Save_comment_function() {
                var dm_id = $("#dm_id").val();
                var remarks = $("#description").val();
            
                if (remarks.trim() === "") {
                    toastr.error("Remarks field is required. Please enter a comment.");
                    return;
                }
            
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.bgvvendor.bgv_comment_store') }}",
                    type: "POST",
                    data: {
                        dm_id: dm_id,
                        remarks: remarks,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("Added!", response.message, "success");
                            const modal = bootstrap.Modal.getInstance(document.getElementById('BGV_comments_save_modal'));
                            if (modal) modal.hide();
                            $("#description").val("");
                            $("#dm_id").val("");
                        } else {
                            Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                    }
                });
            }
    
    
      function BGV_Doc_PopupView(id){
            $("#BGV_doc_save_modal").modal('show');
            $("#doc_dm_id").val(id);
            $("#documents").val("");
            $("#file-count").text("");
        }
        function BGV_Doc_PopupHide(){
            $("#BGV_doc_save_modal").modal('hide');
            $("#doc_dm_id").val("");
            $("#documents").val("");
            $("#file-count").text("");
        }
        
        function Save_Doc_function() {
            var form = $("#document_store_form")[0];
            var formData = new FormData(form);
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.bgvvendor.bgv_document_store') }}",
                type: "POST",
                data: formData,
                contentType: false, 
                processData: false, 
                success: function(response) {
                    console.log(response);
                    if (response.success === true) {
                        Swal.fire("Uploaded!", response.message, "success");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('BGV_doc_save_modal'));
                        if (modal) modal.hide();
                    } else {
                        Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error("Please try again.");
                    }
                }
            });
        }
        
         function Profile_Image_View(src){
            $("#ProfileImage_srcModal").modal("show");
            $("#profile_image_src").attr("src",src);
            
        }
        
        // function Query_show_modal(id){
        //      $("#BGV_query_show_modal").modal('show');
        // }
        // function Query_close_modal(){
        //      $("#BGV_query_show_modal").modal('hide');
        // }

</script>

@endsection
</x-app-layout>
