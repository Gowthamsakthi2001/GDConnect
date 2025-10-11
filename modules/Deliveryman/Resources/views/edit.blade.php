<x-app-layout>
<style>
    .upload-area {
        border: 2px dashed #107980;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease;
        position: relative;
        width: 100%; /* Full width */
        height: 0; /* Setting height to 0 to maintain aspect ratio */
        padding-bottom: 40%; /* Makes the box square */
        overflow: hidden; /* Hide overflow */
    }
    
    .upload-area:hover {
        border-color:  #24bac3; /* Change color on hover */
    }
    
    .upload-content {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .upload-content p {
        margin-top: 80px;
    }
    
    .preview-img {
        display: none; /* Hide image preview by default */
        width: 100%; /* Fill the width of the box */
        height: 100%; /* Fill the height of the box */
        object-fit: cover; /* Maintain aspect ratio and cover the box */
        position: absolute; /* Position image absolutely to cover the entire box */
        top: 0;
        left: 0;
    }
    
    .upload-content p {
        z-index: 1; /* Ensure text is above the image */
        color: #999;   /* Light color for the text */
    }
    
    .text-danger {
        color: red; /* Error color */
    }
    
    .text-success {
        color: green; /* Success color */
    }
    
    .btn-grd-primary:hover{background-image: linear-gradient(310deg,#ff0080, #7928ca) !important;}
    
    .progress {
      height: 20px;
      border-radius: 5px;
    }
    .custom-radio input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-radius: 50%;
        position: relative;
        outline: none;
        cursor: pointer;
        transition: border-color 0.3s;
    }

    /* Style the radio button when checked */
    .custom-radio input[type="radio"]:checked {
        border-color: transparent;
        background: linear-gradient(310deg, #17c653, #0d8a3f);
    }

    /* Add an inner circle to show selection */
    .custom-radio input[type="radio"]:checked::before {
        content: "";
        position: absolute;
        top: 2px;
        left: 2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
    }

</style>
   <?php
    $TitleName = "";
     if($dm->work_type == "deliveryman"){
          $TitleName = "Rider";
        }
        else if($dm->work_type == "in-house"){
            $TitleName = "Employee";
        }
        else if($dm->work_type == "adhoc"){
             $TitleName = "Adhoc";
        }else{
            $TitleName = "Data";
        }
   ?>
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title ms-3 mt-2">
            <span>Edit {{$TitleName}}</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    <!--navigation buttons start -->
        
<div class="row d-flex justify-content-around">
    <div class="col-md-6">
        <div class="m-5">
            @if ( $nextDm)
                <a href="{{ route('admin.Green-Drive-Ev.delivery-man.edit', ['id' =>  $nextDm->id]) }}" class="btn btn-primary"> <i class="bi bi-arrow-left-circle-fill"></i> Previous  </a>
            @endif
        </div>

    </div>

    <div class="col-md-6 text-end">
        <div class="m-5">
            @if ($prevDm)
                <a href="{{ route('admin.Green-Drive-Ev.delivery-man.edit', ['id' => $prevDm->id]) }}"  class="btn btn-primary px-4">Next <i class="bi bi-arrow-right-circle-fill"></i></a>
            @endif
        </div>
    </div>

</div>
        
        <!--navigation buttons end -->

    <!-- Wizard Form -->
   <div class="card">
       <div class="card-body">
            <form action="{{route('admin.Green-Drive-Ev.delivery-man.update', ['id' => $dm->id])}}" method="POST" enctype="multipart/form-data" id="wizardForm" class=" g-3 p-3" >
                @csrf
                <div class="progress mb-4">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                          style="width: 0%; background: linear-gradient(310deg, #17c653, #0d8a3f) !important;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div id="wizard">
                    <!-- Step 1: Rider Information -->
                    <div class="wizard-step ">
                        <h3 class="mb-3">{{$TitleName}} Info</h3>
                        <div class="row gy-4">
                           
                            <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="f_name">{{'First Name'}}</label>
                                   <input type="text" class="form-control" name="first_name" id="f_name" value="{{ old('first_name', $dm->first_name) }}"  placeholder="{{'Ex: will'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="l_name">{{'Last Name'}}</label>
                                   <input type="text" class="form-control" name="last_name" id="l_name" value="{{old('last_name',$dm->last_name)}}" placeholder="{{'Ex: smith'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="mobile_no">{{'Mobile Number'}}</label>
                                    <input type="tel" class="form-control" name="mobile_number" id="mobile_no" oninput="sanitizeAndValidatePhone(this)"  value="{{old('mobile_number',$dm->mobile_number)}}" placeholder="{{'+917894561230'}}" >

                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="current_city_id">City</label>
                                    <select class="form-control basic-single" id="current_city_id" name="current_city_id" onchange="get_area('current_city_id')">
                                        @foreach($city as $data)
                                            <option value="{{ $data->id }}" {{ old('current_city_id', $dm->current_city_id) == $data->id ? 'selected' : '' }}>
                                                {{ $data->city_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="lead_source_id">Lead Source</label>-->
                            <!--        <select class="form-control basic-single" id="lead_source_id" name="lead_source_id">-->
                            <!--            @foreach($source as $data)-->
                            <!--                <option value="{{ $data->id }}" {{ old('lead_source_id',$dm->lead_source_id) == $data->id ? 'selected' : '' }}>-->
                            <!--                    {{ $data->source_name }}-->
                            <!--                </option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="interested_city_id"> Area</label>
                                    <select class="form-control basic-single" id="interested_city_id" name="interested_city_id">
                                       
                                    </select>
                                </div>
                            </div>
                            
                            @php
                                $vehicleTypes = ['2W', '3W', '4W','Rental'];
                            @endphp

                            @if($dm->work_type != "in-house")
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                    <select class="form-control basic-single" id="vehicle_type" name="vehicle_type">
                                        @foreach($vehicleTypes as $type)
                                            <option value="{{ $type }}" {{ old('vehicle_type',$dm->vehicle_type) == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1"  for="rider_type">Rider Type</label>
                                    <select class="form-control basic-single" id="rider_type" name="rider_type">
                                        @foreach($rider_type as $type)
                                            <option value="{{ $type->id }}" {{ old('rider_type',$dm->rider_type) == $type->type ? 'selected' : '' }}>
                                                {{ $type->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="Zones">Zones</label>-->
                            <!--        <select class="form-control basic-single" id="Zones" name="Zones">-->
                            <!--            @foreach($Zones as $data)-->
                            <!--                <option value="{{ $data->id }}" {{ old('Zones', $dm->zone_id) == $data->id ? 'selected' : '' }}>-->
                            <!--                    {{ $data->name }}-->
                            <!--                </option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div> -->

                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label for="statusSelect">Status</label>-->
                            <!--        <select class="form-control basic-single" id="statusSelect" name="status">-->
                            <!--            <option value="1">Active</option>-->
                            <!--            <option value="0">Inactive</option>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-md-6 mt-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emp_id">{{ 'Employee ID' }}</label>-->
                            <!--        <input type="text" class="form-control" name="emp_id" id="emp_id" value="{{ old('emp_id', $dm->emp_id) }}" placeholder="{{ 'Ex: GDC-R-001' }}">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-6 mt-4 d-none">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1"  for="work_type">Employee Work Type</label>
                                        <select class="form-control basic-single" id="work_type" name="work_type">
                                           <option value="in-house" {{$dm->work_type == 'in-house' ? 'selected' : ''}}>In House</option>
                                           <option value="deliveryman" {{$dm->work_type == 'deliveryman' ? 'selected' : ''}}>Deliveryman</option>
                                        </select>
                                    </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="remarks">{{'remarks (If any)'}}</label>
                                    <textarea name="remarks" class="form-control" id="remarks">{{ old('remarks',$dm->remarks) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-buttons d-flex justify-content-between  align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <button type="button" class="btn btn-success btn-round text-white px-4 next">{{'Next Page'}}</button>
                        </div>
                    </div>
                    
                    @php
                        $job_apply_type = ['Walk In', 'Social Mediia Ads', 'Referral', 'Job Agency','Others'];
                    @endphp

                            <!-- Step 2: Apply Job -->
                    <div class="wizard-step d-none">
                        <h3 class="ms-2">{{'Apply Job'}}</h3>
                        <div class="row p-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1"  for="profile_picturs_upload">{{ 'Profile Picture' }}</label>
                                    <div class="upload-area" id="profile_pictures_upload" onclick="document.getElementById('profile_pic').click();">
                                        <input type="file" class="d-none" name="photo" id="profile_pic" accept="image/*" onchange="previewImage(event, 'profile_pictures_upload')">
                                        <div class="upload-content">
                                            <img id="profile_preview" class="preview-img" src="{{ asset('public/EV/images/photos/'.$dm->photo) }}" style="display: {{ $dm->photo ? 'block' : 'none' }};" alt="License Preview" />
                                            <p>No file chosen, yet!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="apply_job_source">Job Apply Source</label>
                                        <select class="form-select form-control basic-single" id="apply_job_source" name="apply_job_source" onchange="toggleReferralFields()">
                                            @foreach($job_apply_type as $type)
                                                <option value="{{ $type }}" {{ old('apply_job_source',$dm->apply_job_source) == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="referralFields" class="col-md-12" style="display: {{ $dm->apply_job_source == 'Referral' ? 'block' : 'none' }};">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="referal_person_number">Referral Mobile Number</label>
                                                <input type="text" class="form-control" id="referal_person_number"  name="referal_person_number" oninput="sanitizeAndValidatePhone(this)" placeholder="Enter mobile number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="referal_person_name">Referral Name</label>
                                                <input type="text" class="form-control" id="referal_person_name" name="referal_person_name"  placeholder="Enter referral name">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="wizard-buttons d-flex justify-content-between align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <div>
                                <button type="button" class="btn btn-secondary previous text-white">{{'Previous Page'}}</button>
                                <button type="button" class="btn btn-success btn-round next text-white">{{'Next Page'}}</button>
                           </div>
                        </div>
                    </div>
    
                                <!--step 3-->
                    <div class="wizard-step d-none">
                        <h3>{{'KYC'}}</h3>
                        <br>
                        <div class="row gy-4">
                            <!-- Upload Aadhar Card -->
                            <br>
                            <div class="col-12 ">
                                <h4>{{'Aadhar Details'}}</h4>
                                <div class="row gy-4">
                                    
                                    <div class="col-md-4 ">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="aadhar_upload_front">{{ 'Aadhar Card Front' }}</label>
                                            <div class="upload-area" id="aadhar_upload_front" onclick="document.getElementById('aadhar_card_front').click();">
                                                <input type="file" class="d-none" name="aadhar_card_front" id="aadhar_card_front" accept="image/*" onchange="previewImage(event, 'aadhar_upload_front')">
                                                <div class="upload-content">
                                                    <img id="aadhar_front_preview" class="preview-img" src="{{ asset('public/EV/images/aadhar/'.$dm->aadhar_card_front) }}" style="display: {{ $dm->aadhar_card_front ? 'block' : 'none' }};"alt="Aadhar Preview" />
                                                    <p>No file chosen, yet!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="aadhar_upload_back">{{ 'Aadhar Card Back' }}</label>
                                            <div class="upload-area" id="aadhar_upload_back" onclick="document.getElementById('aadhar_card_back').click();">
                                                <input type="file" class="d-none" name="aadhar_card_back" id="aadhar_card_back" accept="image/*" onchange="previewImage(event, 'aadhar_upload_back')">
                                                <div class="upload-content">
                                                    <img id="aadhar_back_preview" class="preview-img" src="{{ asset('public/EV/images/aadhar/'.$dm->aadhar_card_back) }}" style="display: {{ $dm->aadhar_card_back ? 'block' : 'none' }};" alt="Aadhar Preview" />
                                                    <p>No file chosen, yet!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="aadhar_number" >{{'Aadhar Number'}}</label>
                                            <input type="text" class="form-control" name="aadhar_number" id="aadhar_number"   oninput="aadharNumber(this)" value="{{old('aadhar_number',$dm->aadhar_number)}}" placeholder="{{'ex: 1234 5678 9123'}}" max="12">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                    
                            <!-- Pan Details -->
                            <div class="col-12">
                                <h4 class="mt-4">{{'Pan Details'}}</h4>
                                <div class="row gy-4">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="pan_upload_front">{{ 'Pan Card Front' }}</label>
                                            <div class="upload-area" id="pan_upload_front" onclick="document.getElementById('pan_card_front').click();">
                                                <input type="file" class="d-none" name="pan_card_front" id="pan_card_front" accept="image/*" onchange="previewImage(event, 'pan_upload_front')">
                                                <div class="upload-content">
                                                    <img id="pan_front_preview" class="preview-img" src="{{ asset('public/EV/images/pan/'.$dm->pan_card_front) }}" style="display: {{ $dm->pan_card_front ? 'block' : 'none' }};" alt="Pan Preview" />
                                                    <p>No file chosen, yet!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label class="input-label mb-2 ms-1" for="pan_upload_back">{{ 'Pan Card Back' }}</label>-->
                                    <!--        <div class="upload-area" id="pan_upload_back" onclick="document.getElementById('pan_card_back').click();">-->
                                    <!--            <input type="file" class="d-none" name="pan_card_back" id="pan_card_back" accept="image/*" onchange="previewImage(event, 'pan_upload_back')">-->
                                    <!--            <div class="upload-content">-->
                                    <!--                <img id="pan_back_preview" class="preview-img" src="{{ asset('public/EV/images/pan/'.$dm->pan_card_back) }}" style="display: {{ $dm->pan_card_back ? 'block' : 'none' }};" alt="Pan Preview" />-->
                                    <!--                <p>No file chosen, yet!</p>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="pan_number">{{'Pan Number'}}</label>
                                            <input type="text" class="form-control pan" name="pan_number" id="pan_number" oninput="validatePAN()"  value="{{old('pan_number',$dm->pan_number)}}" placeholder="{{'ex: ALWPG5809L'}}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($dm->work_type) && $dm->work_type != "in-house")
                            
                            <div class="col-12 mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="llr_checkbox" name="is_llr" 
                                        {{ (empty($dm->driving_license_front) && empty($dm->driving_license_back) && empty($dm->license_number)) ? 'checked' : '' }} 
                                        value="1" onchange="toggleLicenseLlr()">
                                    <label class="form-check-label" for="llr_checkbox">
                                        I have LLR only
                                    </label>
                                </div>
                            </div>
                            
                            
                            
                            <!--Lisence Details-->
                        <div id="license_details_section" style="display: {{ (empty($dm->driving_license_front) && empty($dm->driving_license_back) && empty($dm->license_number)) ? 'none' : 'block' }};">
                            <div class="col-12">
                                <h4 class="mt-4">{{'License Details'}}</h4>
                                <div class="row gy-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="license_upload_front">{{ 'License Card Front' }}</label>
                                            <div class="upload-area" id="license_upload_front" onclick="document.getElementById('driving_license_front').click();">
                                                <input type="file" class="d-none" name="driving_license_front" id="driving_license_front" accept="image/*" onchange="previewImage(event, 'license_upload_front')">
                                                <div class="upload-content">
                                                    <img id="lisence_front_preview" class="preview-img" src="{{ asset('public/EV/images/driving_license/'.$dm->driving_license_front) }}" style="display: {{ $dm->driving_license_front ? 'block' : 'none' }};" alt="License Preview" />
                                                    <p>No file chosen, yet!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="license_upload_back">{{ 'License Card Back' }}</label>
                                            <div class="upload-area" id="license_upload_back" onclick="document.getElementById('driving_license_back').click();">
                                                <input type="file" class="d-none" name="driving_license_back" id="driving_license_back" accept="image/*" onchange="previewImage(event, 'license_upload_back')">
                                                <div class="upload-content">
                                                    <img id="lisence_back_preview" class="preview-img" src="{{ asset('public/EV/images/driving_license/'.$dm->driving_license_back) }}" style="display: {{ $dm->driving_license_back ? 'block' : 'none' }};" alt="License Preview" />
                                                    <p>No file chosen, yet!</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="license_number">License Number</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="license_number" 
                                                id="license_number" 
                                                oninput="validateLicense()" 
                                                value="{{ $dm->license_number ?? '' }}" 
                                                placeholder="ex: ABC1234567">
                                            <small id="license_error" class="text-danger d-none"></small>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div> 
                            
                            
                        <div id="llr_details_section" style="display: {{ (empty($dm->driving_license_front) && empty($dm->driving_license_back) && empty($dm->license_number)) ? 'block' : 'none' }};">
                            <h5 class="text-primary mt-4">LLR Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="llr_upload">LLR Image <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="llr_upload" onclick="document.getElementById('llr_image').click();">
                                        <input type="file" class="d-none upload-img" name="llr_image" id="llr_image" accept="image/*,application/pdf" onchange="previewImage(event, 'llr_upload')">
                                        <div class="upload-content">
                                            <img id="llr_preview" class="preview-img"
                                                 src="{{ $dm->llr_image ? asset('public/EV/images/llr_images/' . $dm->llr_image) : '' }}" 
                                                 style="display: {{ $dm->llr_image ? 'block' : 'none' }};" 
                                                 alt="LLR Preview">
                                            <p>No file chosen, yet!</p>
                                            <button id="imgclose-btn" class="imgclose-btn" type="button">&times;</button>
                                        </div>
                                    </div>
                                </div>
                        
                                <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="llr_number">LLR Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="llr_number" id="llr_number" value="{{ $dm->llr_number ?? '' }}" placeholder="ex: LLR1234567">
                                </div>
                            </div>
                        </div>
                            
                     
                            
                            
                            @endif
                        </div>
                        <div class="wizard-buttons d-flex justify-content-between align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <div>
                                <button type="button" class="btn btn-secondary previous text-white">{{'Previous Page'}}</button>
                                <button type="button" class="btn btn-success btn-round next text-white">{{'Next Page'}}</button>
                           </div>
                        </div>
                    </div>

                    
                                 <!-- step 4 -->
                    <div class="wizard-step d-none">
                        <h3>{{'Bank Information'}}</h3>
                        <div class="row gy-4">
                            
                           <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="bank_name">{{'Bank Name'}}</label>
                                   <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{old('bank_name',$dm->bank_name)}}" placeholder="{{'Ex: Indian Overseas'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="ifsc_code">{{'IFSC Code'}}</label>
                                   <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="{{old('ifsc_code',$dm->ifsc_code)}}" placeholder="{{'Ex: IOBA000VEP'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="account_number">{{'Bank Account No'}}</label>
                                   <input type="text" class="form-control" name="account_number" id="account_number" value="{{old('account_number',$dm->account_number)}}" placeholder="{{'Ex: 74125896398'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="account_holder_name">{{'Account Holder Name'}}</label>
                                   <input type="text" class="form-control" name="account_holder_name" id="account_holder_name" value="{{old('account_holder_name',$dm->account_holder_name)}}" placeholder="{{'Ex: smith'}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                     <label class="input-label mb-2 ms-1" for="passbook_upload">{{ 'Bank Pass Book' }}</label>
                                    <div class="upload-area" id="passbook_upload" onclick="document.getElementById('bank_passbook').click();">
                                        <input type="file" class="d-none" name="bank_passbook" id="bank_passbook" accept="image/*" onchange="previewImage(event, 'passbook_upload')">
                                        <div class="upload-content">
                                            <img id="lisence_back_preview" class="preview-img" src="{{ asset('public/EV/images/bank_passbook/'.$dm->bank_passbook) }}" style="display: {{ $dm->bank_passbook ? 'block' : 'none' }};" alt="passbook Preview" />
                                            <p>No file chosen, yet!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-buttons d-flex justify-content-between align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <div>
                                <button type="button" class="btn btn-secondary text-white previous">{{'Previous Page'}}</button>
                                <button type="button" class="btn btn-success btn-round text-white next">{{'Next Page'}}</button>
                           </div>
                        </div>
                    </div>
                    
                        <!-- step 5 Personal Information -->
                    <div class="wizard-step d-none">
                        <h3>{{'Personal Information'}}</h3>
                        @php
                            $formattedDate = \Carbon\Carbon::parse($dm->date_of_birth)->format('Y-m-d');
                        @endphp
                        <div class="row gy-4">
                            <!-- Date of Birth -->
                            <div class="col-md-6 mt-5">
                                <div class="form-group">
                                  <label class="input-label mb-2 ms-1" for="date_of_birth">{{'DOB'}}</label>
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth"  value="{{ old('date_of_birth',$formattedDate  ?: date('Y-m-d')) }}" required>
                                </div>
                            </div>
                    
                            <!-- Present Address -->
                            <div class="col-md-6 mt-5">
                                <div class="form-group">
                                     <label class="input-label mb-2 ms-1" for="present_address">{{'Present Address'}}</label>
                                    <input type="text" class="form-control" name="present_address" id="present_address" value="{{ old('present_address',$dm->present_address) }}" placeholder="{{'Enter Present Address'}}" required>
                                </div>
                            </div>
                    
                            <!-- Permanent Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="permanent_address">{{'Permanent Address'}}</label>
                                    <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="{{ old('permanent_address',$dm->permanent_address) }}" placeholder="{{'Enter Permanent Address'}}" required>
                                </div>
                            </div>
                    
                            <!-- Father's Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                      <label class="input-label mb-2 ms-1" for"father_name">Father/ Mother/ Guardian Name</label>
                                    <input type="text" class="form-control" name="father_name" id="father_name" value="{{ old('father_name',$dm->father_name) }}" placeholder="{{'Enter Father\'s Name'}}">
                                </div>
                            </div>
                    
                            <!-- Father's Mobile Number -->
                            <div class="col-md-6">
                                <div class="form-group">
                                   <label class="input-label mb-2 ms-1" for"father_mobile_number">Father/ Mother/ Guardian Contact No</label>
                                    <input type="tel" class="form-control" name="father_mobile_number" id="father_mobile_number" oninput="sanitizeAndValidatePhone(this)" value="{{ old('father_mobile_number',$dm->father_mobile_number) }}" placeholder="{{'Enter Father\'s Mobile Number'}}">
                                </div>
                            </div>
                    
                            <!-- Guardian's Name -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--     <label class="input-label mb-2 ms-1" for="mother_name">{{'Guardian\'s Name (optional)'}}</label>-->
                            <!--        <input type="text" class="form-control" name="mother_name" id="mother_name" value="{{ old('mother_name',$dm->mother_name) }}" placeholder="{{'Enter Guardian\'s Name'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <!-- Guardian's Mobile Number -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--       <label class="input-label mb-2 ms-1" for="mother_mobile_number">{{'Guardian\'s Mobile Number (optional)'}}</label>-->
                            <!--        <input type="tel" class="form-control" name="mother_mobile_number" id="mother_mobile_number" oninput="sanitizeAndValidatePhone(this)" value="{{ old('Guardian_mobile_number',$dm->mother_mobile_number) }}" placeholder="{{'Enter Mother\'s Mobile Number'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            
                            <div class="col-md-6">
                                <div class="form-group custom-radio">
                                    <label class="input-label mb-2 ms-1">Marital Status</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input type="radio" name="marital_status" id="marital_status" value="1" {{ $dm->spouse_name ? 'checked' : '' }}  onchange="toggleSpouseFields()">
                                            <label class="form-check-label" for="marital_status">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="marital_status" id="marital_status" value="0" {{ !$dm->spouse_name ? 'checked' : '' }} onchange="toggleSpouseFields()">
                                            <label class="form-check-label" for="marital_status">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <div class="col-md-12" id="spouse" style="display: {{ $dm->marital_status == '1' ? 'block' : 'none' }};">
                            <!-- Spouse's Name -->
                            <div class="row">
                                <div class="col-md-6" >
                                    <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="spouse_name">{{'Spouse Name'}}</label>
                                        <input type="text" class="form-control" name="spouse_name" id="spouse_name" value="{{ old('spouse_name',$dm->spouse_name) }}" placeholder="{{'Enter Spouse\'s Name'}}">
                                    </div>
                                </div>
                        
                                <!-- Spouse's Mobile Number -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                          <label class="input-label mb-2 ms-1" for="spouse_mobile_number">{{'Spouse Contact No'}}</label>
                                        <input type="tel" class="form-control" name="spouse_mobile_number" id="spouse_mobile_number" oninput="sanitizeAndValidatePhone(this)" value="{{ old('spouse_mobile_number',$dm->spouse_mobile_number) }}" placeholder="{{'Enter Spouse\'s Mobile Number'}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="wizard-buttons d-flex justify-content-between align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <div>
                                <button type="button" class="btn btn-secondary  text-white previous ">{{'Previous Page'}}</button>
                                <button type="button" class="btn btn-success btn-round px-4 text-white next">{{'Next Page'}}</button>
                            </div>
                        </div>
                    </div>

                                <!--step 6-->
                    <div class="wizard-step d-none">
                        <!--<h3 class="mb-4">{{'Emergency Details'}}</h3>-->
                        <div class="row gy-4">
                           
                            <!--<div class="col-md-6  mt-4">-->
                            <!--    <div class="form-group">-->
                            <!--          <label class="input-label mb-2 ms-1" for="emergency_contact_person_1_name">{{'Emergency Contact Person Name 01'}}</label>-->
                            <!--        <input type="text" class="form-control" name="emergency_contact_person_1_name" id="emergency_contact_person_1_name" value="{{ old('emergency_contact_person_1_name',$dm->emergency_contact_person_1_name) }}" placeholder="{{'Enter Contact Person Name 01'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
 
                            <!--<div class="col-md-6  mt-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_1_mobile">{{'Emergency Contact Person 01 Mobile'}}</label>-->
                            <!--        <input type="tel" class="form-control" name="emergency_contact_person_1_mobile" id="emergency_contact_person_1_mobile" oninput="sanitizeAndValidatePhone(this)" value="{{ old('emergency_contact_person_1_mobile',$dm->emergency_contact_person_1_mobile) }}" placeholder="{{'Enter Mobile Number 01'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
          
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_2_name">{{'Emergency Contact Person Name 02'}}</label>-->
                            <!--        <input type="text" class="form-control" name="emergency_contact_person_2_name" id="emergency_contact_person_2_name" value="{{ old('emergency_contact_person_2_name',$dm->emergency_contact_person_2_name) }}" placeholder="{{'Enter Contact Person Name 02'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
         
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--     <label class="input-label mb-2 ms-1" for="emergency_contact_person_2_mobile">{{'Emergency Contact Person 02 Mobile'}}</label>-->
                            <!--        <input type="tel" class="form-control" name="emergency_contact_person_2_mobile" id="emergency_contact_person_2_mobile" oninput="sanitizeAndValidatePhone(this)" value="{{ old('emergency_contact_person_2_mobile',$dm->emergency_contact_person_2_mobile) }}" placeholder="{{'Enter Mobile Number 02'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                           @php($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                            <!-- Blood Group (Select Box) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                     <label class="input-label mb-2 ms-1" for="blood_group">{{ 'Blood Group' }}</label>
                                    <select class="form-select form-control" name="blood_group" id="blood_group" required>
                        
                                        @foreach ($bloodGroups as $bloodGroup)
                                            <option value="{{ $bloodGroup }}" {{ $dm->blood_group == $bloodGroup ? 'selected' : '' }}>{{ $bloodGroup }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <div class="wizard-buttons d-flex justify-content-between align-items-end mt-3">
                            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary">Back</a>
                            <div>
                                <button type="button" class="btn btn-secondary text-white previous">{{'Previous Page'}}</button>
                                <button type="submit" class="btn btn-success btn-round px-4 text-white final-submit">{{'Final Submit'}}</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>
       </div>
   </div>

   <script>
 document.addEventListener('DOMContentLoaded', function () {
    toggleReferralFields();
    toggleSpouseFields();
       // Initial configurations
    let today = new Date();
    let formattedDate = today.toISOString().split("T")[0];
    document.getElementById("date_of_birth").setAttribute("max", formattedDate);

    let currentStep = 1;
    const totalSteps = $('.wizard-step').length;
    
    // Progress bar update function
    function updateProgressBar() {
        let progressPercentage = (currentStep / totalSteps) * 100;
        $('#progressBar').css('width', progressPercentage + '%');
        $('#progressBar').attr('aria-valuenow', progressPercentage);
    }
    
    // Show the current step function
    function showStep(step) {
        $('.wizard-step').addClass('d-none');
        $(`.wizard-step:nth-of-type(${step})`).removeClass('d-none');
        updateProgressBar(); // Update progress bar with each step change
    }

    // Validation functions (as per your existing code)...
    function validateFields(fields) {
        let valid = true;
        const phoneRegex = /^\+91[0-9]{10}$/;

        fields.forEach(function (field) {
            let inputField = $(`[name="${field}"]`);
            if (field.includes('mobile_number')) {
                if (!phoneRegex.test(inputField.val())) {
                    inputField.addClass('is-invalid');
                    valid = false;
                } else {
                    inputField.removeClass('is-invalid').addClass('is-valid');
                }
            } else if (inputField.is('select')) {
                if (inputField.val() === '' || inputField.val() === null) {
                    inputField.addClass('is-invalid');
                    valid = false;
                } else {
                    inputField.removeClass('is-invalid').addClass('is-valid');
                }
            } else {
                if (inputField.val() === '') {
                    inputField.addClass('is-invalid');
                    valid = false;
                } else {
                    inputField.removeClass('is-invalid').addClass('is-valid');
                }
            }
        });

        return valid;
    }
    function step_one() {
        let fields = ['first_name', 'last_name', 'mobile_number', 'current_city_id', 'interested_city_id', 'vehicle_type'];
        return validateFields(fields);
    }

       function step_two() {
           let fields = ['apply_job_source'];
           return validateFields(fields);
       }

       function step_three() {
           let fields = ['aadhar_number', 'pan_number'];
           
           @if(isset($dm->work_type) && $dm->work_type != "in-house")
           
            let llrCheckbox = document.getElementById('llr_checkbox').checked;


            if (!llrCheckbox) {
                fields.push('license_number');
            } else {
                fields.push('llr_number');
            }
            
            @endif
    
           return validateFields(fields);
       }

       function step_four() {
           let fields = [
               'account_number', 
               'account_holder_name', 
               'bank_name', 
               'ifsc_code'
           ];
           return validateFields(fields);
       }

       function step_five() {
           let fields = [
               'date_of_birth', 
               'present_address', 
               'permanent_address', 
               'father_name', 
               'father_mobile_number',
            //   'mother_name',
            //   'mother_mobile_number',
           ];
           return validateFields(fields);
       }


       function step_six() {
           let fields = [
            //   'emergency_contact_person_1_name', 
            //   'emergency_contact_person_1_mobile', 
            //   'emergency_contact_person_2_name', 
            //   'emergency_contact_person_2_mobile', 
               'blood_group',
           ];
           return validateFields(fields);
       }

        function isStepValid() {
        let valid = true;
        switch (currentStep) {
            case 1: valid = step_one(); break;
            case 2: valid = step_two(); break;
            case 3: valid = step_three(); break;
            case 4: valid = step_four(); break;
            case 5: valid = step_five(); break;
            case 6: valid = step_six(); break;
        }
        return valid;
    }

    // Handle "Next" button
    $('.next').on('click', function () {
        if (isStepValid()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });

    // Handle "Previous" button
    $('.previous').on('click', function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Initialize the form with the first step visible and progress bar at 0%
    showStep(currentStep);
       
        $('.final-submit').on('click', function (event) {
            event.preventDefault(); // Prevent the default form submission
        
            function step_six() {
                let fields = [
                    // 'emergency_contact_person_1_name', 
                    // 'emergency_contact_person_1_mobile', 
                    // 'emergency_contact_person_2_name', 
                    // 'emergency_contact_person_2_mobile', 
                    'blood_group',
                ];
                let valid = true;
                const phoneRegex = /^\+91[0-9]{10}$/; // Regex for Indian mobile number format
        
                fields.forEach(function (field) {
                    let inputField = $(`[name="${field}"]`);
        
                    // Validate mobile numbers
                    if (field.includes('mobile')) {
                        if (!phoneRegex.test(inputField.val())) {
                            inputField.addClass('is-invalid'); // Add invalid class to input field if mobile number is invalid
                            valid = false;
                        } else {
                            inputField.removeClass('is-invalid').addClass('is-valid'); // Mark as valid if mobile number matches regex
                        }
                    } 
                    // Validate select box for blood group
                    else if (field === 'blood_group') {
                        if (inputField.val() === '' || inputField.val() === null) {
                            inputField.addClass('is-invalid'); // Add invalid class if no blood group is selected
                            valid = false;
                        } else {
                            inputField.removeClass('is-invalid').addClass('is-valid'); // Mark select box as valid if a value is selected
                        }
                    } 
                    // Validate other fields
                    else {
                        if (inputField.val() === '') {
                            inputField.addClass('is-invalid'); // Add invalid class to input field if empty
                            valid = false;
                        } else {
                            inputField.removeClass('is-invalid').addClass('is-valid'); // Mark as valid if field is not empty
                        }
                    }
                });
        
                return valid; // Return false if any field is invalid
            }
        
            // Call the validation function
            if (step_six()) {
                // If valid, submit the form
                $('#wizardForm').submit(); // Programmatically submit the form
            } 
        });

       let id_name_value = $("#current_city_id").val(); // Get the value of the city select dropdown
       let interest_area = {{$dm->interested_city_id}};
        let formData = {
            id: id_name_value, 
            i_id : interest_area,// City ID to be sent
        };
        
        $.ajax({
            url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}', // Route to the controller
            method: 'GET', // Use GET if necessary, otherwise POST is preferred
            data: formData, // The data to send
            success: function(response) {
                if (response.status) {
                    // Directly update the dropdown with the HTML string in response.data
                    $("#interested_city_id").html(response.data); 
                } else {
                    alert(response.message); // Display message if no areas found
                }
            },
            error: function(xhr) {
                // Handle errors (e.g., validation errors)
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Display the error messages
                    for (var key in errors) {
                        alert(errors[key].join(', ')); // Show the errors
                    }
                }
            }
        });
   });

   // Preview image function for file uploads
   function previewImage(event, uploadAreaId) {
       const file = event.target.files[0];
       const uploadArea = document.getElementById(uploadAreaId);
       const previewImg = uploadArea.querySelector('.preview-img');
       const uploadContent = uploadArea.querySelector('p');

       if (file) {
           const reader = new FileReader();

           reader.onload = function (e) {
               previewImg.src = e.target.result; // Set the source of the image to the uploaded file
               previewImg.style.display = 'block'; // Show the preview image
               uploadContent.textContent = file.name; // Show the name of the file chosen
           };

           reader.readAsDataURL(file); // Read the file as a data URL
       } else {
           previewImg.src = ''; // Reset the preview image
           previewImg.style.display = 'none'; // Hide the preview image
           uploadContent.textContent = 'No file chosen, yet!'; // Reset text if no file is chosen
       }
   }

   // mobile number validate
    function sanitizeAndValidatePhone(input) {
        // Ensure the input starts with '+91' and lock the first 3 characters to '+91'
        if (!input.value.startsWith('+91')) {
            input.value = '+91' + input.value.replace(/^\+?91/, ''); // Ensure it starts with "+91"
        }

        // Allow only digits after '+91'
        input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, ''); 

        // Limit the total length to 13 characters (including '+91')
        if (input.value.length > 13) {
            input.value = input.value.substring(0, 13);
        }
    }
    // aadhar validate   
    function aadharNumber(input) {
        // Remove any characters that are not digits
        var sanitizedInput = input.value.replace(/[^\d]/g, '');
    
        // Limit the input to 12 digits
        if (sanitizedInput.length > 12) {
            sanitizedInput = sanitizedInput.substring(0, 12); // Trim to 12 digits
        }
    
        // Set the cleaned input back to the input field
        input.value = sanitizedInput;
    
        // Validate if the input is exactly 12 digits
        if (sanitizedInput.length === 12) {
            $('#error').text(''); 
            return true; // Aadhaar number is valid
        } else {
            $('#error').text('Please enter a valid 12-digit Aadhaar number.');
            return false; 
        }
    }
    //pan validate
    function validatePAN() { 
        var input = document.getElementById('pan_number'); 
        var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/; 
    
        // Test if input matches the PAN format
        if (regpan.test(input.value)) {
            return true;
            
        } else {
            return false;
        }
    }
    function toggleReferralFields() {
        const jobSource = document.getElementById('apply_job_source').value;
        const referralFields = document.getElementById('referralFields');
        
        if (jobSource === 'Referral') {
            referralFields.style.display = 'block';
            document.getElementById('referal_person_number').value = "{{ $dm->referal_person_number }}";
            document.getElementById('referal_person_name').value = "{{ $dm->referal_person_name }}";
        } else {
            referralFields.style.display = 'none';
            document.getElementById('referal_person_number').value = "";
            document.getElementById('referal_person_name').value = "";
        }
    }
    
    function toggleSpouseFields() {
        const maritalStatusYes = document.getElementById('marital_status').checked;
        const spouseFields = document.getElementById('spouse');
    
        if (maritalStatusYes) {
            spouseFields.style.display = 'block';
            document.getElementById('spouse_name').value = "{{ $dm->spouse_name }}";
            document.getElementById('spouse_mobile_number').value = "{{ $dm->spouse_mobile_number }}";
        } else {
            spouseFields.style.display = 'none';
            document.getElementById('spouse_name').value = "";
            document.getElementById('spouse_mobile_number').value = "";
        }
    }
    
    function get_area(id_name) {
        var id_name_value = $("#" + id_name).val(); // Get the value of the city select dropdown
        let formData = {
            id: id_name_value, // City ID to be sent
        };
    
        $.ajax({
            url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}', // Route to the controller
            method: 'GET', // Use GET if necessary, otherwise POST is preferred
            data: formData, // The data to send
            success: function(response) {
                if (response.status) {
                    // Directly update the dropdown with the HTML string in response.data
                    $("#interested_city_id").html(response.data); 
                } else {
                    alert(response.message); // Display message if no areas found
                }
            },
            error: function(xhr) {
                // Handle errors (e.g., validation errors)
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Display the error messages
                    for (var key in errors) {
                        alert(errors[key].join(', ')); // Show the errors
                    }
                }
            }
        });
    }


function validateLicense() {
        const input = document.getElementById('license_number');
        const errorElement = document.getElementById('license_error');
        const value = input.value;
    
        // Define your validation regex for license number
        const licensePattern = /^[A-Za-z0-9]{5,10}$/; // Example: Alphanumeric, 5-10 characters
    
        if (licensePattern.test(value)) {
            // If valid, remove error message and reset styles
            errorElement.classList.add('d-none');
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            // If invalid, show error message and apply error styles
            errorElement.classList.remove('d-none');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        }
    }


function toggleLicenseLlr() {
    const isLLR = document.getElementById('llr_checkbox').checked;
    const licenseSection = document.getElementById('license_details_section');
    const llrSection = document.getElementById('llr_details_section');

    licenseSection.style.display = isLLR ? 'none' : 'block';
    llrSection.style.display = isLLR ? 'block' : 'none';

    if (isLLR) {
        // Clear license fields when LLR is selected
       
        $('#llr_number').val("{{ $dm->llr_number ?? '' }}");
        
        $('#driving_license_front').val('');
        $('#driving_license_back').val('');
        $('#license_number').val('');
        $('#license_front_preview').hide();
        $('#license_back_preview').hide();
    } else {
        // Restore existing license number
        $('#license_number').val("{{ $dm->license_number ?? '' }}");
        
        $('#llr_number').val("");
        // Show previews if existing
        const frontImg = $('#license_front_preview');
        const backImg = $('#license_back_preview');

        if (frontImg.attr('src') && !frontImg.attr('src').includes('dummy')) {
            frontImg.show();
        }
        if (backImg.attr('src') && !backImg.attr('src').includes('dummy')) {
            backImg.show();
        }
    }
}


$(document).ready(function() {
    toggleLicenseLlr(); // Ensure initial state on page load
});

</script>

</x-app-layout>