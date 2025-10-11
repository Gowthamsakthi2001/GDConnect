<x-app-layout>
    
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        color: #fff;
        background-color: #ffffff;
        box-shadow: none !important;
    }
    .nav-pills .nav-link.active .head-text {
        color: #0000009c !important;
    }
    .custom-card-body {
      height: 500px; 
      overflow-y: auto; 
    }

    .action-btn {
        width: 100%;
        height: 48px;
        font-weight: 500;
        display: flex;
        font-size: 14px;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: 0.3s;
        border: 1px solid #ccc;
    }
    

    input[type="radio"]:not(:checked) + .action-btn {
        background-color: #fff;
        color: #000;
    }
    

    input[type="radio"]#approve:checked + label[data-type="approve"] {
        background-color: #28a745;
        color: white !important;
        border: none;
    }
    input[type="radio"]#bgv:checked + label[data-type="bgv"] {
        background-color: #ffc107;
        color: white !important;
        border: none;
    }
    input[type="radio"]#hold:checked + label[data-type="hold"] {
        background-color: #17a2b8;
        color: white !important;
        border: none;
    }
    input[type="radio"]#rejected:checked + label[data-type="rejected"] {
        background-color: #dc3545;
        color: white !important;
        border: none;
    }



</style>

    <div class="main-content">
       
        <div class="card my-4">
            <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>
                                <?php
                                 $image = $application->photo ? asset('public/EV/images/photos/'.$application->photo) : asset('public/admin-assets/img/person.png');
                                
                                $roll_type = '';
                                 if($application->work_type == 'deliveryman'){
                                     $roll_type = 'Rider';
                                 }
                                 else if($application->work_type == 'in-house'){
                                     $roll_type = 'Employee';
                                 }
                                 else if($application->work_type == 'adhoc'){
                                     $roll_type = 'Adhoc';
                                 }
                                 else if($application->work_type == 'helper'){
                                     $roll_type = 'Helper';
                                 }else{
                                     $roll_type = "-";
                                 }
                                ?>
                                
                               <img src="{{$image}}" alt="Profile" width="70" height="70" style="border-radius:50%;">
                            </div>
                            <div class="px-3">
                                <div class="h4 fw-bold mt-2">{{$application->first_name ?? '' }} {{$application->last_name ?? '' }}</div>
                                 <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item" style="word-spacing: 3px;"><img src="{{asset('public/admin-assets/icons/custom/profile_icon.png')}}" alt="Profile"> Application ID : {{$application->reg_application_id ?? ''}}</li>
                                        <li class="breadcrumb-item"><i class="bi bi-person fw-bold"></i> Role : {{$roll_type}}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <button class="btn btn-primary edit-candidate-btn" onclick="edit_candidate()"><i class="bi bi-pencil-square me-2"></i> Edit Candidate</button>
                            <a href="{{$prev_url}}" class="btn btn-dark edit-candidate-btn px-5"><i class="bi bi-arrow-left me-2"></i> Back</a>
                            <button class="btn btn-success update-candidate d-none" onclick="update_candidate()"><i class="bi bi-floppy me-2"></i>Save Changes</button>
                            <button class="btn border-gray update-candidate d-none" onclick="update_candidate()"><i class="bi bi-x me-2"></i> Cancel</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="card my-3">
            
           <div class="card-header" style="background:#f1f5f9;">
                <ul class="nav nav-pills row d-flex align-items-center" id="pills-tab" role="tablist">
                  <li class="nav-item col-md-3" role="presentation">
                    <button class="nav-link active" id="pills-basic-information-tab" data-bs-toggle="pill" data-bs-target="#pills-basic-information" type="button" role="tab" aria-controls="pills-basic-information" aria-selected="true"><img src="{{asset('public/admin-assets/icons/custom/person.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">Basic Information</span></button>
                  </li>
                  <li class="nav-item col-md-3" role="presentation">
                    <button class="nav-link" id="pills-kyc-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-kyc-doc" type="button" role="tab" aria-controls="pills-kyc-doc" aria-selected="false"><img src="{{asset('public/admin-assets/icons/custom/kyc_doc.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">KYC Docuements</span></button>
                  </li>
                  <li class="nav-item col-md-3 " role="presentation">
                    <button class="nav-link" id="pills-query-comments-tab" data-bs-toggle="pill" data-bs-target="#pills-query-comments" type="button" role="tab" aria-controls="pills-query-comments" aria-selected="false"><img src="{{asset('public/admin-assets/icons/custom/query.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">Query</span></button>
                  </li>
                  <li class="nav-item col-md-3" role="presentation">
                    <button class="nav-link" id="pills-edit-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-edit-doc" type="button" role="tab" aria-controls="pills-edit-doc" aria-selected="false"><img src="{{asset('public/admin-assets/icons/custom/document_upload.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">Reupload DOC</span></button>
                  </li>
                </ul>
           </div>
           <div class="card-body" style="background:#fbfbfb;">
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-basic-information" role="tabpanel" aria-labelledby="pills-basic-information-tab" tabindex="0">
                      <div class="card">
                         <div class="card-header" style="background:#eef2ff;">
                             <h5 style="color:#1e3a8a;" class="fw-bold">Basic Information</h5>
                             <p class="mb-0" style="color:#1e3a8a;">basic information of your Application details</p>
                         </div>
                          <div class="card-body custom-card-body">
                            <div class="row">
                                <!--<div class="col-md-6 mb-3">-->
                                <!--    <div class="form-group">-->
                                <!--        <label class="input-label mb-2 ms-1" for="mobile_no">{{'GDM ID'}}</label>-->
                                <!--        <input type="tel" class="form-control bg-white" name="emp_id" id="emp_id" oninput="sanitizeAndValidatePhone(this)"  value="{{$application->emp_id ?? 'Still Under Review'}}" readonly>-->
        
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="col-12 text-center my-3">
                                    <img src="{{$image}}" alt="Profile" class="" width="90" height="90" style="border-radius: 50%;">
                                    <p class="text-muted mt-2">Profile Picture</p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="mobile_no">{{'Contact No'}}</label>
                                        <input type="tel" class="form-control bg-white" name="mobile_number" id="mobile_no" oninput="sanitizeAndValidatePhone(this)"  value="{{$application->mobile_number ?? ''}}" readonly>
        
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="f_name">{{'First Name'}}</label>
                                       <input type="text" class="form-control bg-white" name="first_name" id="f_name" value="{{$application->first_name ?? '' }}" readonly>
                                    </div>
                                </div>
                                    
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="l_name">{{'Last Name'}}</label>
                                       <input type="text" class="form-control bg-white" name="last_name" id="l_name" value="{{$application->last_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="email">{{'Email'}}</label>
                                       <input type="text" class="form-control bg-white" name="email" id="email" value="{{$application->email ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="gender">{{'Gender'}}</label>
                                       <input type="text" class="form-control bg-white" name="gender" id="gender" value="{{ucfirst($application->gender) ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="house_no">{{'House No'}}</label>
                                       <input type="text" class="form-control bg-white" name="house_no" id="house_no" value="{{$application->house_no ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="street_name">{{'Street Name'}}</label>
                                       <input type="text" class="form-control bg-white" name="street_name" id="street_name" value="{{$application->street_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="current_city_id">City</label>
                                        <select class="form-control basic-single bg-white" id="current_city_id" name="current_city_id" onchange="get_area('current_city_id')" disabled>
                                        @if($cities)
                                            @foreach($cities as $data)
                                                <option value="{{ $data->id }}" {{ old('current_city_id', $application->current_city_id) == $data->id ? 'selected' : '' }}>
                                                    {{ $data->city_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="interested_city_id">Area</label>
                                        <select class="form-control basic-single bg-white" id="interested_city_id" name="interested_city_id" disabled>
                                        @if($areas)
                                            @foreach($areas as $data)
                                                <option value="{{ $data->id }}" {{ old('interested_city_id', $application->interested_city_id) == $data->id ? 'selected' : '' }}>
                                                    {{ $data->Area_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="pincode">{{'Pincode'}}</label>
                                       <input type="text" class="form-control bg-white" name="pincode" id="pincode" value="{{$application->pincode ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="alternative_number">{{'Alternative Number'}}</label>
                                       <input type="text" class="form-control bg-white" name="alternative_number" id="alternative_number" value="{{$application->alternative_number ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="role">Role</label>
                                        <select class="form-control basic-single bg-white" id="role" name="role" disabled>
                                            <option value="deliveryman" {{ $application->work_type == "deliveryman" ? 'selected' : '' }}>Rider</option>
                                            <option value="in-house" {{ $application->work_type == "in-house" ? 'selected' : '' }}>Employee</option>
                                            <option value="adhoc" {{ $application->work_type == "adhoc" ? 'selected' : '' }}>Adhoc</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="ac_holder_name">{{'Account Holder Name'}}</label>
                                       <input type="text" class="form-control bg-white" name="ac_holder_name" id="ac_holder_name" value="{{$application->account_holder_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="bank_name">{{'Bank Name'}}</label>
                                       <input type="text" class="form-control bg-white" name="bank_name" id="bank_name" value="{{$application->bank_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="ifsc_code">{{'IFSC Code'}}</label>
                                       <input type="text" class="form-control bg-white" name="ifsc_code" id="ifsc_code" value="{{$application->ifsc_code ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="bank_ac_no">{{'Bank Account No'}}</label>
                                       <input type="text" class="form-control bg-white" name="bank_ac_no" id="bank_ac_no" value="{{$application->account_number ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="d_o_b">{{'DOB'}}</label>
                                       <input type="text" class="form-control bg-white" name="d_o_b" id="d_o_b" value="{{$application->date_of_birth ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="present_address">{{'Present Address'}}</label>
                                       <input type="text" class="form-control bg-white" name="present_address" id="present_address" value="{{$application->present_address ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="permanent_address">Permanent Address</label>
                                       <input type="text" class="form-control bg-white" name="permanent_address" id="permanent_address" value="{{$application->permanent_address ?? ''}}" readonly>
                                    </div>
                                </div>
                                @if($application->work_type != "in-house" && $application->work_type != "")
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="prev_rider_id">Pervious Rider ID</label>
                                       <input type="text" class="form-control bg-white" name="prev_rider_id" id="prev_rider_id" value="{{$application->emp_prev_company_id ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="prev_company_experience">Past Experience</label>
                                       <input type="text" class="form-control bg-white" name="prev_company_experience" id="prev_company_experience" value="{{$application->emp_prev_experience ?? ''}}" readonly>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="guardian_name">Father/ Mother/ Guardian Name</label>
                                       <input type="text" class="form-control bg-white" name="guardian_name" id="guardian_name" value="{{$application->father_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="guardian_phone">Father/ Mother/ Guardian Contact No</label>
                                       <input type="text" class="form-control bg-white" name="guardian_phone" id="guardian_phone" value="{{$application->father_mobile_number ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="reference_name">Reference Name</label>
                                       <input type="text" class="form-control bg-white" name="reference_name" id="reference_name" value="{{$application->referal_person_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="reference_mobile">Reference Contact No</label>
                                       <input type="text" class="form-control bg-white" name="reference_mobile" id="reference_mobile" value="{{$application->referal_person_number ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="reference_relationship">Reference Relationship</label>
                                       <input type="text" class="form-control bg-white" name="reference_relationship" id="reference_relationship" value="{{$application->referal_person_relationship ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="spouse_name">Spouse Name</label>
                                       <input type="text" class="form-control bg-white" name="spouse_name" id="spouse_name" value="{{$application->spouse_name ?? ''}}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="spouse_mobile">Spouse Contact No</label>
                                       <input type="text" class="form-control bg-white" name="spouse_mobile" id="spouse_mobile" value="{{$application->spouse_mobile_number ?? ''}}" readonly>
                                    </div>
                                </div>
                    
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="blood_group">Blood Group</label>
                                       <input type="text" class="form-control bg-white" name="blood_group" id="blood_group" value="{{$application->blood_group ?? ''}}" readonly>
                                    </div>
                                </div>
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="social_links">Social Link</label>
                                       <input type="text" class="form-control bg-white" name="social_links" id="social_links" value="{{$application->social_links ?? ''}}" readonly>
                                    </div>
                                </div>
                                <?php
                                 $work_type = $application->work_type ?? '';
                                ?>
                                @if($work_type != "in-house")
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="rider_type">Rider Type</label>
                                        <select class="form-control basic-single bg-white" id="rider_type" name="rider_type" disabled>
                                        @if($rider_types)
                                            @foreach($rider_types as $data)
                                                <option value="{{ $data->id }}" {{ $application->rider_type == $data->id ? 'selected' : '' }}>
                                                    {{ $data->type }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                </div>
                                <?php
                                    $vehicleTypes = ['2W', '3W', '4W', '8W','Rental'];
                                ?>
                                
                                 <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="rider_type">Vehicle Type</label>
                                        <select class="form-control basic-single bg-white" id="vehicle_type" name="vehicle_type" disabled>
                                        @if($vehicleTypes)
                                            @foreach($vehicleTypes as $type)
                                                <option value="{{ $type }}" {{ old('vehicle_type',$application->vehicle_type) == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                      </div>
                   </div>
                   
                   <!-- KYC Documents-->
                  <div class="tab-pane fade" id="pills-kyc-doc" role="tabpanel" aria-labelledby="pills-kyc-doc-tab" tabindex="0">
                           <div class="card">
                                <div class="card-header" style="background:#edfcff;">
                                    <h5 style="color:#1b4d5e;" class="fw-bold">KYC Documents</h5>
                                    <p class="mb-0" style="color:#1b4d5e;">KYC Documents submitted on Application form</p>
                                </div>
                                 <?php
                                    $user = \App\Models\User::find($application->who_aadhar_verify_id);
                            
                                    
                                    $verify_name = $user->name ?? '';
                                    
                                    $verify_role = '';
                                
                                    if ($user && $user->role) {
                                        $verify_by = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user->role)->first();
                                        $verify_role = $verify_by->name ?? '';
                                    }
                                    $front = isset($application->aadhar_card_front) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_front) : asset('public/EV/images/dummy.jpg');
                                    $back = isset($application->aadhar_card_back) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_back) : asset('public/EV/images/dummy.jpg');
                                ?>
                                
                                <div class="card-body custom-card-body">
                                    <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Aadhar Card Front</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                            {{ $verify_name ? $verify_name . ' (' . $verify_role . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                            {{ !empty($application->aadhar_verify_date) ? date('d M Y h:i:s A', strtotime($application->aadhar_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                   @if($application->aadhar_verify == 1)
                                                    <button class="btn btn-success px-5" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4"  onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                    <div class="image-container" onclick="OpenImageModal('{{$front}}')">
                                                        <img id=""
                                                            src="{{$front}}"
                                                            class="preview-image img-fluid" alt="Image"
                                                            style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
                                            
                                               <div class="col-12 mt-5">
                                                  <button onclick="OpenImageModal('{{$front}}')" class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>

                                                
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Aadhar Card Back</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                            {{ $verify_name ? $verify_name . ' (' . $verify_role . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                            {{ !empty($application->aadhar_verify_date) ? date('d M Y h:i:s A', strtotime($application->aadhar_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                   @if($application->aadhar_verify == 1)
                                                    <button class="btn btn-success px-5" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4"  onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                   <div class="image-container" onclick="OpenImageModal('{{$back}}')">
                                                        <img id=""
                                                            src="{{$back}}"
                                                            class="preview-image img-fluid" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
                                            
                                               <div class="col-12 mt-5">
                                                  <button class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>

                                                
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                    
                                     <?php
                                            $user1 = \App\Models\User::find($application->who_pan_verify_id);
                                            $verify_name1 = $user1->name ?? '';
                                            $verify_role1 = '';
                                        
                                            if ($user1 && $user1->role) {
                                                $verify_by1 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user1->role)->first();
                                                $verify_role1 = $verify_by1->name ?? '';
                                            }
                                            
                                            $pan_image = isset($application->pan_card_front) ? asset('public/EV/images/pan/' . $application->pan_card_front) : asset('public/EV/images/dummy.jpg');
                                        ?>
                                    
                                <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">PAN Card</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                             {{ $verify_name1 ? $verify_name1 . ' (' . $verify_role1 . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                           {{ !empty($application->pan_verify_date) ? date('d M Y h:i:s A', strtotime($application->pan_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                   @if($application->pan_verify == 1)
                                                    <button class="btn btn-success px-5"  onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->pan_verify, 'pan_verify']) }}', 
                                                            '{{ $application->pan_verify ? 'UnVerified' : 'Verified' }} this Pan?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->pan_verify, 'pan_verify']) }}', 
                                                            '{{ $application->pan_verify ? 'UnVerified' : 'Verified' }} this Pan?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                    <div class="image-container" onclick="OpenImageModal('{{$pan_image}}')">
                                                        <img id=""
                                                            src="{{$pan_image}}"
                                                            class="preview-image img-fluid" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
                                            
                                               <div class="col-12 mt-5">
                                                  <button class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>

                                                
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                    
                                     <?php
                                        $user3 = \App\Models\User::find($application->who_license_verify_id);
                                        $verify_name3 = $user3->name ?? '';
                                        $verify_role3 = '';
                                    
                                        if (!empty($user3?->role)) {
                                            $verify_by3 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user3->role)->first();
                                            $verify_role3 = $verify_by3->name ?? '';
                                        }
                                        
                                        $front1 = isset($application->driving_license_front) ? asset('public/EV/images/driving_license/' . $application->driving_license_front) : asset('public/EV/images/dummy.jpg');
                                        $back1 = isset($application->driving_license_back) ? asset('public/EV/images/driving_license/' . $application->driving_license_back) : asset('public/EV/images/dummy.jpg');
                                    ?>
                                    
                                    <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Driving License Front</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                            {{ $verify_name3 ? $verify_name3 . ' (' . $verify_role3 . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                            {{ !empty($application->lisence_verify_date) ? date('d M Y h:i:s A', strtotime($application->lisence_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                   @if($application->aadhar_verify == 1)
                                                    <button class="btn btn-success px-5" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4"  onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                    <div class="image-container" onclick="OpenImageModal('{{$front1}}')">
                                                        <img id=""
                                                            src="{{$front1}}"
                                                            class="preview-image img-fluid" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
                                            
                                               <div class="col-12 mt-5">
                                                  <button class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>

                                                
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Driving License Back</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                            {{ $verify_name3 ? $verify_name3 . ' (' . $verify_role3 . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                            {{ !empty($application->lisence_verify_date) ? date('d M Y h:i:s A', strtotime($application->lisence_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                   @if($application->aadhar_verify == 1)
                                                    <button class="btn btn-success px-5" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4"  onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->aadhar_verify, 'aadhar_verify']) }}', 
                                                            '{{ $application->aadhar_verify ? 'UnVerified' : 'Verified' }} this Aadhar?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                    <div class="image-container" onclick="OpenImageModal('{{$back1}}')">
                                                            <img id=""
                                                                src="{{$back1}}"
                                                                class="preview-image img-fluid" alt="Image"
                                                                style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                        </div>
                                                </div>
                                            
                                               <div class="col-12 mt-5">
                                                  <button class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>

                                                
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                    
                                     <?php
                                        $user2 = \App\Models\User::find($application->who_bank_verify_id);
                                        $verify_name2 = $user2->name ?? '';
                                        $verify_role2 = '';
                                    
                                        if (!empty($user2?->role)) {
                                            $verify_by2 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user2->role)->first();
                                            $verify_role2 = $verify_by2->name ?? '';
                                        }
                                        $bank_image = isset($application->pan_card_front) ? asset('public/EV/images/bank_passbook/' . $application->bank_passbook) : asset('public/EV/images/dummy.jpg');
                                    ?>
                                    <div class="card mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Bank Details</div>
                                                     <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                     
                                                    <p class="mb-2 fw-medium">
                                                        Verified by: 
                                                        <span class="text-success">
                                                            {{ $verify_name2 ? $verify_name2 . ' (' . $verify_role2 . ')' : '' }}
                                                        </span>
                                                    </h6>
                                
                                                    <p class="fw-medium">Verified At: 
                                                        <span class="text-success">
                                                           {{ !empty($application->bank_verify_date) ? date('d M Y h:i:s A', strtotime($application->bank_verify_date)) : '' }}
                                                        </span>
                                                    </h6>
                                                </div>
                        
                                                <div class="col text-end">
                                                    @if($application->bank_verify == 1)
                                                    <button class="btn btn-success px-5" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->bank_verify, 'bank_verify']) }}', 
                                                            '{{ $application->bank_verify ? 'UnVerified' : 'Verified' }} this Bank Details?', 
                                                            event
                                                        )">Verified</button>
                                                   @else
                                                     <button class="btn btn-danger px-4" onclick="status_change_alert(
                                                            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$application->id, $application->bank_verify, 'bank_verify']) }}', 
                                                            '{{ $application->bank_verify ? 'UnVerified' : 'Verified' }} this Bank Details?', 
                                                            event
                                                        )">Verify</button>
                                                   @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            
                                            
                                            <div class="row mt-5">
                                                <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                                                    <div class="image-container" onclick="OpenImageModal('{{$bank_image}}')">
                                                        <img id=""
                                                            src="{{$bank_image}}"
                                                            class="preview-image img-fluid" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-6 mt-3 mt-md-0">
                                                     <h6 class="my-3"> Bank Holder Name:&nbsp; <span class="text-secondary">{{ $application->account_holder_name ?? ''}}</span></h6>
                                                     <h6 class="mb-3"> Bank Name: &nbsp;<span class="text-secondary">{{ $application->bank_name ?? ''}}</span></h6>
                                                     <h6 class="mb-3"> IFSC Code:&nbsp; <span class="text-secondary">{{ $application->ifsc_code ?? '' }}</span></h6>
                                                     <h6 class="mb-3"> Account Number:&nbsp; <span class="text-secondary">{{ $application->account_number ?? '' }}</span></h6>
                                                </div>
                                                
                                                <div class="col-12 mt-5">
                                                  <button class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                  </button>
                                                </div>
                                            </div>
                        
                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                  </div>
                  
                  <!--Queries Tab-->
                  <div class="tab-pane fade" id="pills-query-comments" role="tabpanel" aria-labelledby="pills-query-comments-tab" tabindex="0">
                        <div class="card">
                                <div class="card-header" style="background:#ffeded;">
                                    <h5 style="color:#5e1b1b;" class="fw-bold">Query</h5>
                                    <p class="mb-0" style="color:#5e1b1b;">BGV Query sent by HR Team and BGV Team</p>
                                </div>
                                <div class="card-body custom-card-body">
                                     <div class="row">
                                         <div class="col-12 my-4 text-center">
                                             <h5 class="fw-bold">Application ID : {{$application->reg_application_id ?? '-'}}</h5>
                                             <p class="mb-0">Candidate Name : {{$application->first_name ?? '' }} {{$application->last_name ?? '' }}</p>
                                         </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-end">
                                                        <label class="input-label">{{'Comment'}}</label>
                                                    </div>
                                                    <div>
                                                        <small class="input-label">Sent by HR 02</small><br>
                                                        <small class="input-label">10 May 2025, 10:00:00 AM</small>
                                                    </div>
                                                </div>
                                               <div class="form-group">
                                                    <textarea name="remarks" class="form-control">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates perspic earum cumque, illo distinctio harum deleniti ipsam culpa hic.</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex align-items-end">
                                                        <label class="input-label">{{'Comment'}}</label>
                                                    </div>
                                                    <div>
                                                        <small class="input-label">Sent by BGV</small><br>
                                                        <small class="input-label">10 May 2025, 10:00:00 AM</small>
                                                    </div>
                                                </div>
                                                
                                               <div class="form-group">
                                                    <textarea name="remarks" class="form-control">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates perspic earum cumque, illo distinctio harum deleniti ipsam culpa hic.</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-end">
                                                    <label class="input-label">{{'Comment'}}</label>
                                                </div>
                                                <div>
                                                    <small class="input-label">Sent by BGV</small><br>
                                                    <small class="input-label">10 May 2025, 10:00:00 AM</small>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <textarea name="remarks" class="form-control">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates perspic earum cumque, illo distinctio harum deleniti ipsam culpa hic.</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                  </div>
                  
                  
                  
                  
                  <!-- Reupload Document -->
                  <div class="tab-pane fade" id="pills-edit-doc" role="tabpanel" aria-labelledby="pills-edit-doc-tab" tabindex="0">
                      <div class="card">
                            <div class="card-header" style="background:#edffee;">
                                <h5 style="color:#305e1b;" class="fw-bold">Reupload Document</h5>
                                <p class="mb-0" style="color:#305e1b;">Reupload Doc sent by Candidate</p>
                            </div>
                            <?php
                               $RUaadhaar_front = isset($application->aadhar_card_front) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_front) : asset('public/EV/images/dummy.jpg');
                               $RUaadhaar_Back = isset($application->aadhar_card_back) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_back) : asset('public/EV/images/dummy.jpg');
                            ?>
                            <div class="card-body custom-card-body">
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Aadhar Card Front</div>
                                                     <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_Aadhaar_front_Form','You Want to update Aadhaar Front','aahaar_front_img','Aadhar Front Image',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <form id="RU_Aadhaar_front_Form" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-12 pt-1 pb-1 mb-3">
                                                            <div class="form-group">
                                                                <input type="file" class="form-control" name="aahaar_front_img" id="aahaar_front_img" onchange="show_imagefunction(this, '#aahaar_front_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                                
                                                            </div>
                                                    </div>
                                                    <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                       <div class="">
                                                            <img id="aahaar_front_view"
                                                                src="{{$RUaadhaar_front}}"
                                                                class="preview-image img-fluid border-gray" alt="Image"
                                                                style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                        </div>
                                                    </div>
              
                                                </div>
                                            </form>
                        
                                        </div>
                                    </div>
                                    
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Aadhar Card Back</div>
                                                     <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_Aadhaar_back_form','You Want to update Aadhaar Back','aahaar_front_back','Aadhar Back Image',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <form id="RU_Aadhaar_back_form" enctype="multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="col-md-12 pt-1 pb-1 mb-3">
                                                        <div class="form-group">
                                                            <input type="file" class="form-control" name="aahaar_front_back" id="aahaar_front_back" onchange="show_imagefunction(this, '#aahaar_back_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                            
                                                        </div>
                                                </div>
                                                
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                   <div class="">
                                                        <img id="aahaar_back_view"
                                                            src="{{$RUaadhaar_Back}}"
                                                            class="preview-image img-fluid border-gray" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
          
                                            </div>
                                         </form>
                        
                                        </div>
                                    </div>
                                    
                                    <?php
                                     $RUpan_image = isset($application->pan_card_front) ? asset('public/EV/images/pan/' . $application->pan_card_front) : asset('public/EV/images/dummy.jpg');
                                    ?>
                                    
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">PAN Card</div>
                                                     <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_Pan_Form','You Want to update PAN Card','pan_card_img','PAN Card Image',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <form id="RU_Pan_Form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12 pt-1 pb-1 mb-3">
                                                        <div class="form-group">
                                                            <input type="file" class="form-control" name="pan_card_img" id="pan_card_img" onchange="show_imagefunction(this, '#pan_card_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                            
                                                        </div>
                                                </div>
                                                
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                   <div class="">
                                                        <img id="pan_card_view"
                                                            src="{{$RUpan_image}}"
                                                            class="preview-image img-fluid border-gray" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
          
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                    
                                    <?php
                                      $RU_dl_front1 = isset($application->driving_license_front) ? asset('public/EV/images/driving_license/' . $application->driving_license_front) : asset('public/EV/images/dummy.jpg');
                                        $RU_dl_back1 = isset($application->driving_license_back) ? asset('public/EV/images/driving_license/' . $application->driving_license_back) : asset('public/EV/images/dummy.jpg');
                                    ?>
                                    
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Driving License Front</div>
                                                     <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_DL_Front_Form','You Want to update Driving License Front','dl_front_img','Driving License Front',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <form id="RU_DL_Front_Form" enctype="multipart/form-data">
                                                @csrf   
                                                <div class="row">
                                                    <div class="col-md-12 pt-1 pb-1 mb-3">
                                                            <div class="form-group">
                                                                <input type="file" class="form-control" name="dl_front_img" id="dl_front_img" onchange="show_imagefunction(this, '#dl_front_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                                
                                                            </div>
                                                    </div>
                                                    
                                                    <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                       <div class="">
                                                            <img id="dl_front_view"
                                                                src="{{$RU_dl_front1}}"
                                                                class="preview-image img-fluid border-gray" alt="Image"
                                                                style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                        </div>
                                                    </div>
              
                                                </div>
                                            </form>
                                        </div>
                                </div>
                                
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Driving License Back</div>
                                                     <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_DL_back_Form','You Want to update Driving License Back','dl_back_img','Driving License Back',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                        <form id="RU_DL_back_Form" enctype="multipart/form-data">
                                                @csrf
                                            <div class="row">
                                                <div class="col-md-12 pt-1 pb-1 mb-3">
                                                        <div class="form-group">
                                                            <input type="file" class="form-control" name="dl_back_img" id="dl_back_img" onchange="show_imagefunction(this, '#dl_back_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                            
                                                        </div>
                                                </div>
                                                
                                                <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                   <div class="">
                                                        <img id="dl_back_view"
                                                            src="{{$RU_dl_back1}}"
                                                            class="preview-image img-fluid border-gray" alt="Image"
                                                            style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                    </div>
                                                </div>
          
                                            </div>
                                        </form>
                                        </div>
                                </div>
                                
                                <?php
                                 $RU_bank_image = isset($application->pan_card_front) ? asset('public/EV/images/bank_passbook/' . $application->bank_passbook) : asset('public/EV/images/dummy.jpg');
                                ?>
                                
                                <div class="card rounded mb-3 shadow shadow-md">
                                        <div class="card-header">
                                            <div class="row d-flex justify-content-between g-3">
                                                <div class="col">
                                                    <div class="card-title h5 fw-medium">Bank Details</div>
                                                    <div class="card-title text-muted">Uploaded at 24 Mar 2025, 12:30:00 AM</div>
                                                </div>
                        
                                                <div class="col text-end">
                                                    <button class="btn btn-success px-4" onclick="kycStatus_change_alert('RU_bank_Form','You Want to update Bank Details','bank_passbook_image','Bank Book',event)">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                          <form id="RU_bank_Form" enctype="multipart/form-data">
                                                @csrf  
                                            <div class="row">
                                                
                                                <div class="col-12 pt-1 pb-1 mb-3">
                                                    <div class="form-group">
                                                        <input type="file" class="form-control" name="bank_passbook_image" id="bank_passbook_image" onchange="show_imagefunction(this, '#dl_back_view');" placeholder="Select avatar image" accept="image/jpeg,image/png,image/jpg,image/gif">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 col-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                    <img id="dl_back_view"
                                                        src="{{$RU_bank_image}}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                                
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group mb-3">
                                                        <label class="input-label mb-2 ms-1" for="updateBank_holder_name">Bank Holder Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="updatebank_holder_name" class="form-control mb-3" id="updateBank_holder_name" value="{{ $application->account_holder_name ?? ''}}">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="input-label mb-2 ms-1" for="updateBank_holder_name">Bank Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="updatebank_name" class="form-control mb-3" id="updateBank_name" value="{{ $application->bank_name ?? '' }}">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="input-label mb-2 ms-1" for="updateBank_holder_name">IFSC Code <span class="text-danger">*</span></label>
                                                        <input type="text" name="updatebank_ifsc_code" class="form-control mb-3" id="updateBank_ifsc_code" value="{{ $application->ifsc_code ?? '' }}">
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="input-label mb-2 ms-1" for="updateBank_holder_name">Account Number <span class="text-danger">*</span></label>
                                                        <input type="text" name="updatebank_ac" class="form-control mb-3" id="updateBank_AC" value="{{ $application->account_number ?? '' }}">
                                                    </div>
                                                </div>
      
          
                                            </div>
                                          </form>
                                        </div>
                                </div>
                                
                                

                            </div>
                        </div>
                  </div>
                  
                </div>
           </div>
            
        </div>
        
        <div class="card">
                <div class="card-header border-0 pb-0">
                    <h5 class="fw-bold">Action Panel</h5>
                </div>
                <div class="card-body">
                   
                    <div class="mb-3 d-none" id="RemarkSection">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="input-label">Remarks <span class="text-danger fw-bold">*</span></label>
                            <button class="btn btn-md border-gray" id="submitRemarkBtn">Submit</button>
                        </div>
                            <div class="form-group">
                                 <textarea class="form-control" rows="5" id="remarkText"></textarea>
                                 <input type="hidden" id="remark_type" value="">
                            </div>
                    </div>
                   
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <input type="radio" name="status" id="approve" class="d-none" / onchange="UpdateCandidateStatus('approve_sent_to_hr02')">
                            <label for="approve" class="btn btn-md border-gray action-btn" data-type="approve">
                                <i class="bi bi-check2-circle"></i> Approve & Sent to HR 02
                            </label>
                        </div>
                    
                        <div class="col-md-3 col-6 mb-3">
                            <input type="radio" name="status" id="bgv" class="d-none" / onchange="UpdateCandidateStatus('sent_to_bgv')">
                            <label for="bgv" class="btn btn-md border-gray action-btn" data-type="bgv">
                                <i class="bi bi-send"></i> Sent to BGV
                            </label>
                        </div>
                    
                        <div class="col-md-3 col-6 mb-3">
                            <input type="radio" name="status" id="hold" class="d-none" / onchange="UpdateCandidateStatus('on_hold')">
                            <label for="hold" class="btn btn-md border-gray action-btn" data-type="hold">
                                <i class="bi bi-clock"></i> On Hold
                            </label>
                        </div>
                    
                        <div class="col-md-3 col-6 mb-3">
                            <input type="radio" name="status" id="rejected" class="d-none" / onchange="UpdateCandidateStatus('rejected')">
                            <label for="rejected" class="btn btn-md border-gray action-btn" data-type="rejected">
                                <i class="bi bi-x-circle"></i> Rejected
                            </label>
                        </div>
                    </div>


                    
                    <div class="row p-3 rounded " style="background:#eaeaea;">
                        
                        <p class="mb-3 text-start fw-medium">Previous Remarks :</p>
                        
                       <div class="col-12 border-gray p-3 mb-3">
                            <p class="text-start" style="color:#00000080;">
                                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates
                                perspiciatis excepturi incidunt saepe et, earum cumque, illo distinctio harum deleniti ipsam culpa hic.
                            </p>
                            
                            <div class="text-end">
                                <small class="fw-normal">12 May 2025, 10:00:00 AM</small>
                            </div>
                        </div>

                         <div class="col-12 border-gray p-3 mb-3">
                            <p class="text-start" style="color:#00000080;">
                                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates 
                                perspiciatis excepturi incidunt saepe et, earum cumque, illo distinctio harum deleniti ipsam culpa hic.
                            </p>
                            
                            <div class="text-end">
                                <small class="fw-normal">12 May 2025, 10:00:00 AM</small>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
        </div>
    </div>
   
@section('script_js')


<script>
    
    function edit_candidate() {
        $(".edit-candidate-btn").each(function () {
            $(this).addClass("d-none").removeClass("d-block");
        });
    
        $(".update-candidate").each(function () {
            $(this).addClass("d-block").removeClass("d-none");
        });
        
        $("input").attr("readonly", false);
    }
    
    function update_candidate() {
        $(".update-candidate").each(function () {
            $(this).addClass("d-none").removeClass("d-block");
        });
    
        $(".edit-candidate-btn").each(function () {
            $(this).addClass("d-block").removeClass("d-none");
        });
        
        $("input").attr("readonly", true);
    }

    
    $(document).ready(function () {
        // Initial state: Show Edit Candidate, hide Save and Cancel
        $('.update-candidate').addClass('d-none');
        $('.edit-candidate').removeClass('d-none');

        // Edit button click
        // $('.edit-candidate').on('click', function (e) {
        //     e.preventDefault();
        //     $('.edit-candidate').addClass('d-none');
        //     $('.update-candidate').removeClass('d-none');
        // });

        // // Save Changes or Cancel button click
        // $('.update-candidate').on('click', function (e) {
        //     if ($(this).hasClass('btn-success') || $(this).hasClass('border-gray')) {
        //         $('.update-candidate').addClass('d-none');
        //         $('.edit-candidate').removeClass('d-none');
        //     }
        // });
    });
</script>


<script>

function show_imagefunction(input,src){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(src).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

//   function UpdateCandidateStatus(type){
//       if(type == "on_hold" || type == "rejected"){
//           $("#RemarkSection").addClass("d-block").removeClass("d-none");
//           var remark_type = type == "on_hold" ? 'on_hold' : 'rejected';
//           $("#remark_type").val(remark_type);
//       }else{
//           $("#RemarkSection").addClass("d-none").removeClass("d-block");
//           $("#remark_type").val('');
//       }
//   }
   
   
   function kycStatus_change_alert(formId, message, fileInputId, file_type, e) {
    e.preventDefault();

       const fileInput = document.getElementById(fileInputId);
        const file = fileInput?.files[0];
    
        // Check if file input is empty
        if (!file) {
            toastr.error(file_type + " field is required");
            return;
        }
        
        console.log(bank_passbook_image);
        
     if (formId == "RU_bank_Form") {

        var holder_name = $("#updateBank_holder_name").val();
        var bank_name   = $("#updateBank_name").val();
        var ifsc_code   = $("#updateBank_ifsc_code").val();
        var bank_ac     = $("#updateBank_AC").val();
    
        var isValid = true;
        if (holder_name == "") {
            toastr.error("Bank Holder Name field is required");
            isValid = false;
        }
        if (bank_name == "") {
            toastr.error("Bank Name field is required");
            isValid = false;
        }
        if (ifsc_code == "") {
            toastr.error("IFSC Code field is required");
            isValid = false;
        }
        if (bank_ac == "") {
            toastr.error("Bank Account No field is required");
            isValid = false;
        }
        if (!isValid) {
            return; 
        }
    }


        Swal.fire({
            title: "Are you sure?",
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
                let form = document.getElementById(formId);
                let formData = new FormData(form);
                formData.append('form_type', formId);
                formData.append('_token', '{{ csrf_token() }}');
    
    
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.hr_level_one.candidate_kyc_update',[$application->id]) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success === true) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error("Please try again.");
                        }
                    }
                });
            }
        });
    }
    
    function showToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: icon,
                title: title
            });
        }


        // Function to handle status updates
        function submitStatusChange(applicationId, status, remarks = '') {
            Swal.fire({
                title: 'Processing...',
                html: 'Updating candidate status',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // First update the status
            $.ajax({
                url: '{{ route("admin.Green-Drive-Ev.hr_level_one.updateCandidateStatus") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    application_id: applicationId,
                    status: status,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr) {
                    showToast('error', 'An error occurred: ' + xhr.responseJSON.message);
                }
            });;
        }

        function UpdateCandidateStatus(type) {
            
            const applicationId = '{{ $application->id }}';
            const candidateName = '{{ $application->first_name }} {{ $application->last_name }}';
           
            if (type === "on_hold" || type === "rejected") {
                $("#RemarkSection").addClass("d-block").removeClass("d-none");
                $("#remark_type").val(type);

                $("#submitRemarkBtn").off('click').on('click', function() {
                    
                    const remark = $("#remarkText").val().trim();
                    if (!remark) {
                        showToast('error', 'Please enter remarks');
                        return;
                    }
                    var typeText = type == "on_hold" ? 'On Hold' : 'Rejected';
                    // Show confirmation dialog
                    Swal.fire({
                        title: `Confirm ${type.replace('_', ' ')}?`,
                        text: `This will mark the application as ${typeText} and notify relevant teams`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: `Yes, ${typeText} this application`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitStatusChange(applicationId, type, remark);
                        }
                    });
                });
            } else {
                let title, text;
                switch (type) {
                    case 'approve_sent_to_hr02':
                        title = 'Approve and Send to HR2?';
                        text = 'This will approve the candidate and notify HR2 team';
                        break;
                    case 'sent_to_bgv':
                        title = 'Send to BGV?';
                        text = 'This will send the candidate for background verification';
                        break;
                    default:
                        title = 'Confirm Action';
                        text = 'Are you sure you want to perform this action?';
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, proceed!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitStatusChange(applicationId, type);
                    } else {
                        // Uncheck the radio button if cancelled
                        $(`input[name="status"][value="${type}"]`).prop('checked', false);
                    }
                });
            }
        }

   
</script>
@endsection
</x-app-layout>
