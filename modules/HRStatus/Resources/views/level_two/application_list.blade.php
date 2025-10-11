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
        {{-- <h1>Hello</h1> --}}
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            @if($type == "total_application")
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Total Applications <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                            @elseif($type == "pending")
                            <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Pending <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                            @elseif($type == "sent_to_bgv")
                            <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Sent Back to BGV <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                            @elseif($type == "sent_to_hr1")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Sent Back to HR 01 <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                            {{-- @elseif($type == "on_hold") --}}
                             {{-- <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> On Hold <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">0</span></div> --}}
                            @elseif($type == "reject_by_hr2")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Rejected<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>

                            @elseif($type == "approved_employee")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Approved - Employees<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                              @elseif($type == "approved_rider")
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.dashboard') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Approved - Riders<span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$lists->count()}}</span></div>
                            @endif
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <!--@if($type == "sent_to_bgv" || $type == "sent_to_hr1")-->
                            <!--<div class="bg-white p-1 border-gray">-->
                            <!--    <div class="btn-group" role="group" aria-label="Basic example">-->
                            <!--      <button type="button" class="btn btn-white">Over All</button>-->
                            <!--      <button type="button" class="btn btn-white">Pending</button>-->
                            <!--      <button type="button" class="btn btn-white">Approved</button>-->
                            <!--      <button type="button" class="btn btn-white">Rejected</button>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--@endif-->
                           <div class="d-flex align-items-center gap-2">

                           
                              
                              <!--<button type="button" id="exportBtn" class="bg-white text-dark border-0">-->
                              <!--            <i class="bi bi-download fs-17 me-1"></i> Export-->
                              <!--      </button>-->
                              <button type="button" id="exportBtn" class="bg-white border border-gray px-3 d-flex align-items-center"  style="height: 42px; cursor: pointer;">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>
                            
                              <!-- Filter Button -->
                              <div onclick="RightSideFilerOpen()" 
                                   class="bg-white border border-gray px-3 d-flex align-items-center" 
                                   style="height: 42px; cursor: pointer;">
                                <i class="bi bi-filter fs-6 me-2"></i> Filter
                              </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        <!-- End Page Header -->

        <div class="table-responsive">
                    <table id="HR01Table_List" class="table text-center" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
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
                              <th scope="col" class="custom-dark">Remarks</th>
                              <th scope="col" class="custom-dark">Current Status</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                          
                        <tbody class="bg-white border border-white">
                                   <!-- <tr>-->
                                   <!--    <td>-->
                                   <!--        <div class="form-check">-->
                                   <!--           <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="">-->
                                   <!--         </div>-->
                                   <!--    </td>-->
                                   <!--    <td>100001</td>-->
                                   <!--    <td>Ram</td>-->
                                   <!--    <td>+917867897654</td>-->
                                   <!--    <td>Chennai</td>-->
                                   <!--    <td>Rider</td>-->
                                   <!--    <td>-->
                                   <!--        <div>10 May 2025,</div>-->
                                   <!--        <div>10:45 AM</div>-->
                                   <!--    </td>-->
                                   <!--    <td>-->
                                   <!--        <div>11 May 2025,</div>-->
                                   <!--        <div>10:45 AM</div>-->
                                   <!--    </td>-->
                                   <!--    <td>1 Day</td>-->
                                   <!--    <td><i class="bi bi-circle-fill" style="color:#72cf72;"></i> Pending HR 01</td>-->
                                   <!--   <td>-->
                                   <!--       <div class="dropdown">-->
                                   <!--         <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">-->
                                   <!--           <i class="bi bi-three-dots"></i>-->
                                   <!--         </button>-->
                                   <!--         <ul class="dropdown-menu dropdown-menu-end">-->
                                   <!--           <li><button class="dropdown-item" type="button">View</button></li>-->
                                   <!--         </ul>-->
                                   <!--       </div>-->
                                   <!--     </td>-->

                                   <!--</tr>-->
                                   
                                   
                            @if(isset($lists))
                               @foreach($lists as $key => $val)
                                   <?php
                                     $full_name = ($val->delivery_man->first_name ?? '').' '.($val->last_name ?? '');
                                     $roll_type = '';
                                     if($val->delivery_man->work_type == 'deliveryman'){
                                         $roll_type = 'Rider';
                                     }
                                     else if($val->delivery_man->work_type == 'in-house'){
                                         $roll_type = 'Employee';
                                     }
                                     else if($val->delivery_man->work_type == 'adhoc'){
                                         $roll_type = 'Adhoc';
                                     }
                                     else if($val->delivery_man->work_type == 'helper'){
                                         $roll_type = 'Helper';
                                     }else{
                                         $roll_type = "-";
                                     }
                                     
                                     $image = $val->photo ? asset('public/EV/images/photos/'.$val->delivery_man->photo) : asset('public/admin-assets/img/person.png');
                                   ?>
                                   

                                   
                                   <tr>
                                       <td>
                                           <div class="form-check">
                                              <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="{{$val['id']}}">
                                            </div>
                                       </td>
                                       <td>
                                           {{$val->delivery_man->reg_application_id ?? '-'}}
                                       </td>
                                       <td>{{$val->delivery_man->first_name ." ".$val->delivery_man->last_name}}</td>
                                       <td>{{$val->delivery_man->mobile_number ?? '-'}}</td>
                                       <td>{{$val->delivery_man->current_city->city_name ?? '-'}}</td>
                                       <td>{{$roll_type}}</td>
                                       <td>
                                           <div>{{date('d M Y',strtotime($val->delivery_man->register_date_time))}},</div>
                                           <div>{{date('h:i:s A',strtotime($val->delivery_man->register_date_time))}}</div>
                                        </td>
                                        <td>
                                           <div>{{date('d M Y',strtotime($val->delivery_man->updated_at))}},</div>
                                           <div>{{date('h:i:s A',strtotime($val->delivery_man->updated_at))}}</div>
                                        </td>
                                        
                                         <?php
                                            if (!empty($val->bgv_approve_datetime)) {
                                                $created_date = \Carbon\Carbon::parse($val->delivery_man->register_date_time);
                                                $approved_date = \Carbon\Carbon::parse($val->delivery_man->bgv_approve_datetime);
                                                $ageing_days = $approved_date->diffInDays($created_date);
                                            }else{
                                                $created_date = \Carbon\Carbon::parse($val->delivery_man->register_date_time);
                                                $current_date = \Carbon\Carbon::now();
                                                $ageing_days = $current_date->diffInDays($created_date);
                              
                                            }
                                        ?>
                                        <td>{{$ageing_days}} Days</td>
                                        
                                        @php
                                            $statusKey = $val->current_status ?? '';
                                        
                                            switch ($statusKey) {
                                                case 'pending':
                                                    $current_status = 'Pending';
                                                    $current_status_clr = '#FFC107';
                                                    break;
                                        
                                                case 'sent_to_bgv':
                                                    $current_status = 'Sent Back to BGV';
                                                    $current_status_clr = '#17A2B8';
                                                    break;
                                        
                                                case 'sent_to_hr1':
                                                    $current_status = 'Sent Back to HR 01';
                                                    $current_status_clr = '#FF9800';
                                                    break;
                                        
                                                case 'approved':
                                                    $current_status = 'Approved';
                                                    $current_status_clr = '#28A745';
                                                    break;
                                        
                                                case 'rejected':
                                                    $current_status = 'Reject By HR 02';
                                                    $current_status_clr = '#DC3545';
                                                    break;
                                        
                                                default:
                                                    $current_status = 'Unknown';
                                                    $current_status_clr = '#6C757D'; // gray
                                                    break;
                                            }
                                        @endphp

                                        <td>
                                            <div onclick="HR_Comment_PopupView('{{$val->delivery_man->id}}')">
                                                <img src="{{asset('public/admin-assets/img/document.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </div>
                                        </td>       
                                                
                                        <td>
                                            <i class="bi bi-circle-fill" style="color: {{ $current_status_clr }};"></i>
                                            {{ $current_status }}
                                        </td>

                                        
                                        <!--<td><i class="bi bi-circle-fill" style="color:{{$current_status_clr}};"></i> {{$current_status}}</td></td>-->
                                        <td>
                                          <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="bi bi-three-dots"></i>
                                            </button>
                                           <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                                              <li>
                                                <a href="{{route('admin.Green-Drive-Ev.hr_level_two.app_preview',$val->delivery_man->id)}}" class="dropdown-item d-flex align-items-center justify-content-center">
                                                  <i class="bi bi-eye me-2 fs-5"></i> View
                                                </a>
                                              </li>
                                             <!--<li>-->
                                             <!--   <a href="javascript:void(0);" -->
                                             <!--      class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord('{{$val->id}}')">-->
                                             <!--     <i class="bi bi-trash me-2"></i> Delete-->
                                             <!--   </a>-->
                                             <!-- </li>-->
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
                      <button class="btn text-white" type="button" style="background:#26c360;"  id="export_download">Download</button>
                  </div>
                </div>
                <div class="modal-body p-md-3">
                  <div class="row p-4">
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field1">Select All</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input" type="checkbox" id="field1" >
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">First Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" name="first_name" id="field2">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Last Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field3" name="last_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Email ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field4" name="email">
                        </div>
                      </div>
                    </div>
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Gender</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field5" name="gender">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field6" name="mobile_number">
                        </div>
                      </div>
                    </div>
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">House No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field7" name="house_no">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Street Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field8" name="street_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field9" name="current_city_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">Area</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field10" name="interested_city_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Pincode</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field11" name="pincode">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Alternative No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field12" name="alternative_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field13">Role</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field13" name="work_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field14">Account Holder Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field14" name="account_holder_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field15">Bank Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field15" name="bank_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">IFSC Code</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field16" name="ifsc_code">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">Bank Account No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field16" name="account_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field17">DOB</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field17" name="date_of_birth">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field18">Present Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field18" name="present_address">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Premanent Address</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field19" name="permanent_address">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field20">Rider ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field20" name="emp_prev_company_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field21">Past Experience</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field21" name="emp_prev_experience">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field22">Father/ Mother/ Guardian</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field22" name="father_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field23">Father/ Mother/ Guardian Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field23" name="father_mobile_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field24">Reference Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field24" name="referal_person_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field25">Reference Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field25" name="referal_person_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field26">Rerence Relationship</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field26" name="referal_person_relationship">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field27">Spouse Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field27" name="spouse_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field28">Spouse Contact No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field28" name="spouse_mobile_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field29">Blood Group</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field29" name="blood_group">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field30">Social Media Link</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field30" name="social_links">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field31">Rider Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field31" name="rider_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field32">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field32" name="vehicle_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field33">Aadhaar No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field33" name="aadhar_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Aadhaar Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field34" name="aadhar_card_front">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field35">Aadhaar Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field35" name="aadhar_card_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field36">Pan No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field36" name="pan_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field37">Pan Card</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field37" name="pan_card_front">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field38">Driving license No</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field38" name="license_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field39">Driving license Front</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field39" name="driving_license_front">
                        </div>
                      </div>
                    </div>
                     
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Driving license Back</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field40" name="driving_license_back">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Bank Details</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field40" name="bank_passbook">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Profile Photo</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field40" name="photo">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">LLR Image</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field40" name="llr_image">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Current Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input export-field-checkbox" type="checkbox" id="field40" name="current_status">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">HR Level 02 Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearHR01Filter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyHR01Filter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Role Type</h6></div>
               </div>
               <div class="card-body">
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" value="all" id="roleType1" {{ request('roletype') === 'all' || request('roletype') === null ? 'checked' : '' }}>
                      <label class="form-check-label" for="roleType1">
                        All
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn"  value="in-house" id="roleType1" {{ request('roletype') === 'in-house' ? 'checked' : '' }}>
                      <label class="form-check-label" for="roleType1">
                        Employee
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" value="deliveryman" {{ request('roletype') === 'deliveryman' ? 'checked' : '' }} id="roleType2">
                      <label class="form-check-label" for="roleType2">
                       Rider
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" value="adhoc" {{ request('roletype') === 'adhoc' ? 'checked' : '' }} id="roleType3">
                      <label class="form-check-label" for="roleType3">
                       Adhoc
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" value="helper" {{ request('roletype') === 'helper' ? 'checked' : '' }} id="roleType4">
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
                      <input class="form-check-input select_time_line" type="radio" value="today" {{ request('timeline') == 'today' ? 'checked' : '' }} name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        This day
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearHR01Filter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyHR01Filter()">Apply</button>
            </div>
            
          </div>
        </div>
    
    
    
        <div class="modal fade" id="HR_comments_save_modal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="HR_comments_saveLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form>
          <div class="modal-content rounded-4"><!-- Added 'rounded-4' here -->
            <div class="modal-header border-0 d-flex justify-content-center">
              <div>
                <h1 class="h3 fs-5 text-center" id="HR_comments_saveLabel">Remarks</h1>
                <p class="text-center">Are you sure you want to Remark?</p>
              </div>
            </div>
            <div class="modal-body">
              <input type="hidden" id="dm_id">
              <label for="description">Comments</label>
              <textarea class="form-control" id="description" rows="6"></textarea>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" onclick="HR_Comment_PopupHide()">No, Cancel</button>
              <button type="button" class="btn btn-primary" id="remarkSubmitBtn" style="border: 1px solid;" onclick="Save_comment_function()">Yes, Remark</button>
            </div>
          </div>
        </form>
      </div>
    </div>

@section('script_js')

<script>
    // document.getElementById('export_download').addEventListener('click', function () {
    // const selected = [];
    // const selectedFields = [];
    
    // // Get selected export fields
    // document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
    //     selectedFields.push({
    //         name: cb.name,
    //         value: cb.value
    //     });
    // });

    // // Get selected row IDs
    // document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
    //     selected.push(cb.value);
    // });

    // // Validate: At least one field must be selected
    // if (selectedFields.length === 0) {
    //     toastr.error("Please select at least one export field.");
    //     return;
    // }


    // const params = new URLSearchParams();
    // params.append('status', '{{$type}}'); // pending, sent_to_bgv, etc.
    // params.append('from_date', '{{ $from_date }}');
    // params.append('to_date', '{{ $to_date }}');
    // params.append('timeline', '{{ $timeline }}');
    
    // if (selected.length > 0) {
    //     params.append('selected_ids', JSON.stringify(selected));
    // }
    // if (selectedFields.length > 0) {
    //     params.append('fields', JSON.stringify(selectedFields));
    // }

    // const url = `{{ route('admin.Green-Drive-Ev.hr_level_two.export_data') }}?${params.toString()}`;
    // window.location.href = url;
    
    
// });

document.getElementById('export_download').addEventListener('click', function () {
    const selected = [];
    const selectedFields = [];
    
    // Get selected export fields
    document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
        selectedFields.push({
            name: cb.name,
            value: cb.value
        });
    });

    // Get selected row IDs
    document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
        selected.push(cb.value);
    });

    // Get role type filter - ensure values match database values
    const selectedRole = document.querySelector('input[name="roleTypebtn"]:checked');
    const roleType = selectedRole ? selectedRole.value : 'all';

    // Validate: At least one field must be selected
    if (selectedFields.length === 0) {
        toastr.error("Please select at least one export field.");
        return;
    }

    const params = new URLSearchParams();
    params.append('status', '{{$type}}');
    params.append('from_date', '{{ $from_date }}');
    params.append('to_date', '{{ $to_date }}');
    params.append('timeline', '{{ $timeline }}');
    params.append('roletype', roleType); // Make sure this matches the controller parameter name
    
    if (selected.length > 0) {
        params.append('selected_ids', JSON.stringify(selected));
    }
    if (selectedFields.length > 0) {
        params.append('fields', JSON.stringify(selectedFields));
    }

    const url = `{{ route('admin.Green-Drive-Ev.hr_level_two.export_data') }}?${params.toString()}`;
    window.location.href = url;
});
</script>

<script>


//   document.getElementById('export_download').addEventListener('click', function () {
 
      
//     const selected = [];
//     const selectedFields = [];
    
//     document.querySelectorAll('#export_select_fields_modal .export-field-checkbox:checked').forEach(cb => {
//       selectedFields.push({
//         name: cb.name,
//         value: cb.value
//       });
//     });

//         const selectedrole = document.querySelector('input[name="roleTypebtn"]:checked');
//         const roletype = selectedrole ? selectedrole.value : 'all';
    
//     document.querySelectorAll('.sr_checkbox:checked').forEach(cb => {
//       selected.push(cb.value);
//     });

//     // ✅ Validate: At least one field must be selected
//     if (selectedFields.length === 0) {
//         toastr.error("Please select at least one export field.");
//         return;
//     }
//     // console.log(selectedFields);
    
    


//     const params = new URLSearchParams();
//     params.append('status', '{{$roll_type}}');
//     params.append('from_date', '{{ $from_date }}');
//     params.append('to_date', '{{ $to_date }}');
//     params.append('timeline', '{{ $timeline }}');
    
//          if (selected.length > 0) {
//       params.append('selected_ids', JSON.stringify(selected));
//     }
//         if (selectedFields.length > 0) {
//       params.append('fields', JSON.stringify(selectedFields));
//     }

//     const url = `{{ route('admin.Green-Drive-Ev.hr_level_two.export_data') }}?${params.toString()}`;
//     window.location.href = url;
//   });

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


<script>
    
           
    function applyHR01Filter() {
        const selectedrole = document.querySelector('input[name="roleTypebtn"]:checked');
        const roletype = selectedrole ? selectedrole.value : 'all';
         const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
         const timeline = selectedTimeline ? selectedTimeline.value : '';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
        if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
                toastr.error("From Date and To Date is must be required");
                return;
            }
            
        }
        
    
        const url = new URL(window.location.href);
        url.searchParams.set('roletype', roletype);


    if (from_date && to_date) {
        // Use from_date and to_date, remove timeline
        url.searchParams.set('from_date', from_date);
        url.searchParams.set('to_date', to_date);
        url.searchParams.delete('timeline');
    } else if (timeline) {
        // Use timeline, remove from_date and to_date
        url.searchParams.set('timeline', timeline);
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
    }

    
        window.location.href = url.toString();
    }

    function clearHR01Filter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('roletype');
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        url.searchParams.delete('timeline');
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
      document.addEventListener('DOMContentLoaded', function () {
          const modal = document.getElementById('export_select_fields_modal');
        const selectAll = document.getElementById('field1');
        const checkboxes = modal.querySelectorAll('.form-check-input:not(#field1)'); // All other checkboxes
    
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
    
    // function SelectExportFields(){
    //     $("#export_select_fields_modal").modal('show');
    // }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
    }
    
  function RollTypeFiler(value){
      var url = new URL(window.location.href);
      url.searchParams.set('roll_type',value);
      window.location.href = url.toString();
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
    
            $(document).ready(function () {
       $('#HR01Table_List').DataTable({
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
 
           function HR_Comment_PopupView(id){
            $("#HR_comments_save_modal").modal('show');
            $("#dm_id").val(id);
            $("#description").val("");
        }
                function HR_Comment_PopupHide(){
            $("#HR_comments_save_modal").modal('hide');
            $("#dm_id").val("");
            $("#description").val("");
        }
        
        function Save_comment_function() {
            var dm_id = $("#dm_id").val();
            var remarks = $("#description").val();
            var $submitBtn = $("#remarkSubmitBtn");
        
            if (remarks.trim() === "") {
                toastr.error("Remarks field is required. Please enter a comment.");
                return;
            }
        
            // Change button text and disable
            $submitBtn.text("Saving...").prop("disabled", true);
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.hr_level_two.comment_store') }}",
                type: "POST",
                data: {
                    dm_id: dm_id,
                    remarks: remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire("Added!", response.message, "success");
                        const modal = bootstrap.Modal.getInstance(document.getElementById('HR_comments_save_modal'));
                        if (modal) modal.hide();
                        $("#description").val("");
                        $("#dm_id").val("");
                    } else {
                        Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                    }
                },
                error: function() {
                    Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                },
                complete: function() {
                    // Reset button text & state
                    $submitBtn.text("Yes, Remark").prop("disabled", false);
                }
            });
        }

   
    function DeleteRecord(id, redirect = window.location.href) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this record",
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#6c757d',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.hr_level_two.destroy') }}",
                type: "POST",  // or "DELETE" if your route supports it
                data: {
                    id: id,
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
