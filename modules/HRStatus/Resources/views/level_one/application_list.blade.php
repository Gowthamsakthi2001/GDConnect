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
      
    textarea {
        text-align: left !important;
        direction: ltr !important;
    }

</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            @if($type == "total_application")
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Total Applications <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "pending_application")
                            <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Pending HR 01 <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "sent_to_bgv")
                            <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Sent to BGV <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "sent_to_hr_02")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Sent to HR 02 <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "on_hold")
                             <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> On Hold <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "hr01_rejected")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Rejected<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @elseif($type == "hr01_approved_employees")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Approved - Employees(Live)<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                              @elseif($type == "hr01_approved_riders")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_one.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Approved - Riders(Probation)<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div>
                            @endif
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            @if($type == "sent_to_bgv" || $type == "sent_to_hr_02")
                            <div class="bg-white p-1 border-gray">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                  <button type="button" class="btn btn-white">Over All</button>
                                  <button type="button" class="btn btn-white">Pending</button>
                                  <button type="button" class="btn btn-white">Approved</button>
                                  <button type="button" class="btn btn-white">Rejected</button>
                                </div>
                            </div>
                            @endif
                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="SelectExportFields()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <!-- End Page Header -->

        <div class="table-responsive">
                    <table class="table custom-table text-center" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Application ID</th>
                              <th scope="col" class="custom-dark">Candidate Name</th>
                              <th scope="col" class="custom-dark">Contact</th>
                              <th scope="col" class="custom-dark">Location</th>
                              <th scope="col" class="custom-dark">Role</th>
                              <!--<th scope="col" class="custom-dark">Role Type</th>-->
                              <th scope="col" class="custom-dark">Application Date</th>
                              <th scope="col" class="custom-dark">Last Updated On</th>
                              <th scope="col" class="custom-dark">Ageing Days</th>
                              <th scope="col" class="custom-dark">Current Status</th>
                              <th scope="col" class="custom-dark">Action</th>
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
                                     }else{
                                         $roll_type = "-";
                                     }
                                     
                                     $image = $val->photo ? asset('public/EV/images/photos/'.$val->photo) : asset('public/admin-assets/img/person.png');
                                   ?>
                                   

                                   
                                   <tr>
                                       <td>
                                           <div class="form-check">
                                              <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="">
                                            </div>
                                       </td>
                                       <td>
                                           {{$val->reg_application_id ?? '-'}}
                                       </td>
                                       <td>{{$full_name}}</td>
                                       <td>{{$val->mobile_number ?? '-'}}</td>
                                       <td>{{$val->current_city->city_name ?? '-'}}</td>
                                       <td>{{$roll_type}}</td>
                                       <td>
                                           <div>{{date('d M Y',strtotime($val->register_date_time))}},</div>
                                           <div>{{date('h:i:s A',strtotime($val->register_date_time))}}</div>
                                        </td>
                                        <td>
                                           <div>{{date('d M Y',strtotime($val->updated_at))}},</div>
                                           <div>{{date('h:i:s A',strtotime($val->updated_at))}}</div>
                                        </td>
                                        
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
                                        
                                        <?php
                                          $current_status = $val->hr_level_one_status;
                                          $current_status_clr = "#72cf72";
                                          
                                          if($current_status == 'pending'){
                                            $current_status = 'Pending HR 01';
                                            $current_status_clr = "#72cf72"; 
                                          }
                                          else if($current_status == 'approve_sent_to_hr02'){
                                            $current_status = 'Sent to HR 02';
                                            $current_status_clr = "#efd27b"; 
                                          }
                                          else if($current_status == 'sent_to_bgv'){
                                            $current_status = 'Sent to BGV';
                                            $current_status_clr = "#1661c7"; 
                                          }
                                          else if($current_status == 'on_hold'){
                                            $current_status = 'On Hold';
                                            $current_status_clr = "#c132d7"; 
                                          }
                                          else if($current_status == 'rejected'){
                                            $current_status = 'Rejected';
                                            $current_status_clr = "#866518"; 
                                          }else{
                                            $current_status = 'N/A';
                                            $current_status_clr = 'yellow'; 
                                          }
                                        ?>
                                        <td><i class="bi bi-circle-fill" style="color:{{$current_status_clr}};"></i> {{$current_status}}</td></td>
                                        <td>
                                          <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="bi bi-three-dots"></i>
                                            </button>
                                           <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                                              <li>
                                                <a href="{{route('admin.Green-Drive-Ev.hr_level_one.app_preview',$val->id)}}" class="dropdown-item d-flex align-items-center justify-content-center">
                                                  <i class="bi bi-eye me-2 fs-5"></i> View
                                                </a>
                                              </li>
                                            </ul>

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
                      <button class="btn text-white" style="background:#26c360;">Download</button>
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
                        <label class="form-check-label mb-0" for="field2">First Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field2">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Last Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field3">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Email ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field4">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Gender</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field5">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field6">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">House No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field7">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Street Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field8">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field9">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">Area</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field10">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Pincode</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field11">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Alternative No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field12">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field13">Role</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field13">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field14">Account Holder Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field14">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field15">Bank Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field15">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">IFSC Code</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field16">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">Bank Account No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field16">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field17">DOB</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field17">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field18">Present Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field18">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Premanent Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field19">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field20">Rider ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field20">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field21">Past Experience</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field21">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field22">Father/ Mother/ Guardian</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field22">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field23">Father/ Mother/ Guardian Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field23">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field24">Reference Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field24">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field25">Reference Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field25">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field26">Rerence Relationship</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field26">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field27">Spouse Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field27">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field28">Spouse Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field28">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field29">Blood Group</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field29">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field30">Social Media Link</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field30">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field31">Rider Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field31">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field32">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field32">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field33">Aadhaar No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field33">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Aadhaar Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field34">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field35">Aadhaar Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field35">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field36">Pan No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field36">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field37">Pan Card</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field37">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field38">Driving license No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field38">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field39">Driving license Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field39">
                        </div>
                      </div>
                    </div>
                     
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Driving license Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Bank Details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Profile Photo</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field40">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">HR Level 01 Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Role Type</h6></div>
               </div>
               <div class="card-body">
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1" checked>
                      <label class="form-check-label" for="roleType1">
                        All
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1">
                      <label class="form-check-label" for="roleType1">
                        Employee
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType2">
                      <label class="form-check-label" for="roleType2">
                       Rider
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType3">
                      <label class="form-check-label" for="roleType3">
                       Adhoc
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType4">
                      <label class="form-check-label" for="roleType4">
                       Helper
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
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2">
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3">
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4">
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
            </div>
            
          </div>
        </div>
    

@section('script_js')


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
</script>


<script>
      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('field1');
        const checkboxes = document.querySelectorAll('.form-check-input');
    
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
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
//   function RollTypeFiler(value){
//       var url = new URL(window.location.href);
//       url.searchParams.set('roll_type',value);
//       window.location.href = url.toString();
//   }
   

   
    // function AcceptApplication_status(route, message, title = "Are you sure?") {
    //     Swal.fire({
    //         title: title,
    //         text: message,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: "Yes, Accept it!",
    //         cancelButtonText: "Cancel",
    //         confirmButtonColor: "#28a745",  
    //         cancelButtonColor: "#343a40",
    //         reverseButtons: true
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                     url: route,
    //                     type: "POST",
    //                     data: {
    //                         _token: $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     success: function(response) {
    //                         if (response.success) {
    //                             Swal.fire("Accepted!", response.message, "success");
    //                             setTimeout(() => location.reload(), 1000);
    //                         } else {
    //                             Swal.fire("Warning!", response.message ?? "Update failed.", "error");
    //                         }
    //                     },
    //                     error: function() {
    //                         Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
    //                     }
    //                 });
    //         }
    //     });
    // }
 
   
   
</script>
@endsection
</x-app-layout>
