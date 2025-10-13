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
    
    
     .imgclose-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
            padding: 5px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
        }
    

</style>


    <!-- Page Header -->
    <div class="page-header">
         <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/deliveryman.jpg')}}" class="img-fluid rounded"><span class="ps-2"> Add Adhoc</span>
                    </div>
               </div>
            </h2>
    </div>
    <!-- End Page Header -->

    <!-- Wizard Form  -->
   <div class="card">
       <div class="card-body">
            <form  method="POST" enctype="multipart/form-data" id="wizardForm" class=" g-3 p-3">
                @csrf
                <div class="progress mb-4">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                          style="width: 0%; background: linear-gradient(310deg, #17c653, #0d8a3f) !important;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div id="wizard">
                    
                    <!-- Step 1: Rider Information -->
                    <div class="wizard-step">
                             <h5 class="text-primary">{{'Adhoc Info'}}</h5>
                        
                                <!--<div class="form-row">-->
                                <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="f_name">{{'First Name'}} <span class="text-danger">*</span></label>
                                               <input type="text" class="form-control" name="first_name" id="f_name" value="{{old('first_name')}}" placeholder="{{'Ex: will'}}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="l_name">{{'Last Name'}} <span class="text-danger">*</span></label>
                                               <input type="text" class="form-control" name="last_name" id="l_name" value="{{old('last_name')}}" placeholder="{{'Ex: smith'}}">
                                            </div>
                                        </div>
                                </div> 
                        
                                <div class="row mt-3">
                                            
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="mobile_no">{{'Mobile Number'}} <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" name="mobile_number" id="mobile_no" oninput="sanitizeAndValidatePhone(this)" value="{{old('mobile_number')}}" placeholder="{{'+917894561230'}}" maxlength="13">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1"  for="current_city_id">City <span class="text-danger">*</span></label>
                                                <select class="form-control basic-single" id="current_city_id" name="current_city_id" onchange="get_area('current_city_id')">
                                                    @foreach($city as $data)
                                                        <option value="{{ $data->id }}" {{ old('current_city_id') == $data->id ? 'selected' : '' }}>
                                                            {{ $data->city_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                </div>
                         
                                <div class="row mt-3">
            
                                        <!--<div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label class="input-label mb-2 ms-1" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>-->
                                        <!--        <select class="form-control basic-single" id="lead_source_id" name="lead_source_id">-->
                                        <!--            @foreach($source as $data)-->
                                        <!--                <option value="{{ $data->id }}" {{ old('lead_source_id') == $data->id ? 'selected' : '' }}>-->
                                        <!--                    {{ $data->source_name }}-->
                                        <!--                </option>-->
                                        <!--            @endforeach -->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        
                             
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="interested_city_id">Interested Area <span class="text-danger">*</span></label>
                                                <select class="form-control basic-single" id="interested_city_id" name="interested_city_id">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        @php
                                            $vehicleTypes = ['2W', '3W', '4W','Rental'];
                                        @endphp
                                    
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="input-label mb-2 ms-1"  for="vehicle_type">Vehicle Type <span class="text-danger">*</span></label>
                                                    <select class="form-control basic-single" id="vehicle_type" name="vehicle_type">
                                                        @foreach($vehicleTypes as $type)
                                                            <option value="{{ $type }}" {{ old('vehicle_type') == $type ? 'selected' : '' }}>
                                                                {{ $type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                        </div>
                            
                    </div>

                                <div class="row mt-3">
                        
                                        
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="input-label mb-2 ms-1"  for="rider_type">Rider Type <span class="text-danger">*</span></label>
                                                    <select class="form-control basic-single" id="rider_type" name="rider_type">
                                                        @foreach($rider_type as $type)
                                                            <option value="{{ $type->id }}" {{ old('rider_type') == $type->type ? 'selected' : '' }}>
                                                                {{ $type->type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                        </div>
                                </div>
                                <!--<div class="col-md-6">-->
                                <!--            <div class="form-group">-->
                                <!--                <label class="input-label mb-2 ms-1" for="Zones">Zones</label>-->
                                <!--                <select class="form-control basic-single" id="Zones" name="Zones">-->
                                <!--                    @foreach($Zones as $data)-->
                                <!--                        <option value="{{ $data->id }}" {{ old('Zones') == $data->id ? 'selected' : '' }}>-->
                                <!--                            {{ $data->name }}-->
                                <!--                        </option>-->
                                <!--                    @endforeach-->
                                <!--                </select>-->
                                <!--            </div>-->
                                <!--        </div>-->
                                                
                                                        <!--<div class="col-md-6">-->
                                                        <!--    <div class="form-group">-->
                                                        <!--        <label for="statusSelect">Status</label>-->
                                                        <!--        <select class="form-control js-select2-custom" id="statusSelect" name="status">-->
                                                        <!--            <option value="1">Active</option>-->
                                                        <!--            <option value="0">Inactive</option>-->
                                                        <!--        </select>-->
                                                        <!--    </div>-->
                                                        <!--</div>-->
                                                        
                                <div class="row mt-3">
                                
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="remarks">{{'remarks (If any)'}}</label>
                                                <textarea name="remarks" class="form-control" id="remarks">{{ old('remarks') }}</textarea>
                                            </div>
                                        </div>
                                        <!--<div class="col-md-6 mt-2">-->
                                        <!--        <div class="form-group">-->
                                        <!--            <label class="input-label mb-2 ms-1"  for="work_type">Employee Work Type</label>-->
                                        <!--            <select class="form-control basic-single" id="work_type" name="work_type">-->
                                        <!--               <option value="in-house">In House</option>-->
                                        <!--               <option value="deliveryman">Deliveryman</option>-->
                                        <!--            </select>-->
                                        <!--        </div>-->
                                        <!--</div>-->
                                        
                                        <input type="hidden" name="work_type" id="work_type" value="adhoc">
                                </div>
                            
                    
                        
                                <div class="wizard-buttons d-flex justify-content-end align-items-end mt-3">
                                        <button type="button" class="btn custom-btn-primary btn-round px-4 next">{{'Next'}}</button>
                                </div>
                    </div>
                    
                    
                    @php
                        $job_apply_type = ['Walk In', 'Social Mediia Ads', 'Referral', 'Job Agency','Others'];
                    @endphp
                    

                    <!-- Step 2: Apply Job -->
                    <div class="wizard-step d-none">
                        <h5 class="text-primary">{{'Apply Job'}}</h5>
                        <div class="row p-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1"  for="profile_picturs_upload">{{ 'Profile Picture' }} <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="profile_picturs_upload" onclick="document.getElementById('profile_pic').click();">
                                        <input type="file" class="d-none upload-img" name="photo" id="profile_pic" accept="image/*" onchange="previewImage(event, 'profile_picturs_upload')">
                                        <div class="upload-content">
                                            <img id="lisence_back_preview" class="preview-img" src="" alt="License Preview" />
                                            <p>No file chosen, yet!</p>
                                            <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="apply_job_source">Job Apply Source <span class="text-danger">*</span></label>
                                        <select class="form-control basic-single" id="apply_job_source" name="apply_job_source" onchange="toggleReferralFields()">
                                            @foreach($job_apply_type as $type)
                                                <option value="{{ $type }}" {{ old('apply_job_source    ') == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="referralFields" class="col-md-12" style="display: none;">
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
                            
                        </div>

                        <div class="wizard-buttons d-flex justify-content-end align-items-end">
                            <div>
                                <button type="button" class="btn btn-danger previous text-white">{{'Previous'}}</button>
                                <button type="button" class="btn custom-btn-primary btn-round next px-4">{{'Next'}}</button>
                           </div>
                        </div>
                    </div>
                        
                    
                    
                        <!--step 3-->
                    <div class="wizard-step d-none">
                        <h5 class="text-warning">{{'KYC Info'}}</h5>
                        <br>
                             <div class="row">
                            <!-- Upload Aadhar Card -->
                        <div class="col-12">
                            <h5 class="text-primary">{{'Aadhar Details'}}</h5>
                              
                            <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="aadhar_upload_front">{{ 'Aadhar Card Front' }} <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="aadhar_upload_front" onclick="document.getElementById('aadhar_card_front').click();">
                                                <input type="file" class="d-none upload-img" name="aadhar_card_front" id="aadhar_card_front" accept="image/*" onchange="previewImage(event, 'aadhar_upload_front')">
                                                <div class="upload-content">
                                                    <img id="aadhar_front_preview" class="preview-img" src="" alt="Aadhar Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="aadhar_upload_back">{{ 'Aadhar Card Back' }} <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="aadhar_upload_back" onclick="document.getElementById('aadhar_card_back').click();">
                                                <input type="file" class="d-none upload-img" name="aadhar_card_back" id="aadhar_card_back" accept="image/*" onchange="previewImage(event, 'aadhar_upload_back')">
                                                <div class="upload-content">
                                                    <img id="aadhar_back_preview" class="preview-img" src="" alt="Aadhar Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="aadhar_number" >{{'Aadhar Number'}} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" oninput="aadharNumber(this)" value="{{old('aadhar_number')}}" placeholder="{{'ex: 1234 5678 9123'}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                    
                            <!-- Pan Details -->
                            <div class="col-12">
                                <h5 class="text-primary mt-4">{{'Pan Details'}}</h5>
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="pan_upload_front">{{ 'Pan Card Front' }} <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="pan_upload_front" onclick="document.getElementById('pan_card_front').click();">
                                                <input type="file" class="d-none upload-img" name="pan_card_front" id="pan_card_front" accept="image/*" onchange="previewImage(event, 'pan_upload_front')">
                                                <div class="upload-content">
                                                    <img id="pan_front_preview" class="preview-img" src="" alt="Pan Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label class="input-label mb-2 ms-1" for="pan_upload_back">{{ 'Pan Card Back' }}</label>-->
                                    <!--        <div class="upload-area" id="pan_upload_back" onclick="document.getElementById('pan_card_back').click();">-->
                                    <!--            <input type="file" class="d-none upload-img" name="pan_card_back" id="pan_card_back" accept="image/*" onchange="previewImage(event, 'pan_upload_back')">-->
                                    <!--            <div class="upload-content">-->
                                    <!--                <img id="pan_back_preview" class="preview-img " src="" alt="Pan Preview" />-->
                                    <!--                <p>No file chosen, yet!</p>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="pan_number">{{'Pan Number'}} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="pan_number" id="pan_number" oninput="validatePAN()" value="{{old('pan_number')}}" placeholder="{{'ex: ALWPG5809L'}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                     <div class="col-12 mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="llr_checkbox" name="is_llr" value="1" onchange="toggleLicenseLlr()">
                                <label class="form-check-label" for="llr_checkbox">
                                    I have LLR only
                                </label>
                            </div>
                        </div>
                        
                        
                            <!--Lisence Details-->
                        <div id="license_details_section">
                            <div class="col-12">
                                <h5 class="text-primary mt-4">{{'License Details'}}</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="license_upload_front">{{ 'License Card Front' }} <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="license_upload_front" onclick="document.getElementById('driving_license_front').click();">
                                                <input type="file" class="d-none upload-img" name="driving_license_front" id="driving_license_front" accept="image/*" onchange="previewImage(event, 'license_upload_front')">
                                                <div class="upload-content">
                                                    <img id="lisence_front_preview" class="preview-img" src="" alt="License Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"  for="license_upload_back">{{ 'License Card Back' }} <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="license_upload_back" onclick="document.getElementById('driving_license_back').click();">
                                                <input type="file" class="d-none upload-img" name="driving_license_back" id="driving_license_back" accept="image/*" onchange="previewImage(event, 'license_upload_back')">
                                                <div class="upload-content">
                                                    <img id="lisence_back_preview" class="preview-img" src="" alt="License Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="license_number">License Number <span class="text-danger">*</span></label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                name="license_number" 
                                                id="license_number" 
                                                oninput="validateLicense()" 
                                                value="{{ old('license_number') }}" 
                                                placeholder="ex: ABC1234567">
                                            <small id="license_error" class="text-danger d-none"></small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        
                         <div id="llr_details_section" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-primary mt-4">LLR Details</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="llr_upload">LLR Image <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="llr_upload" onclick="document.getElementById('llr_image').click();">
                                                <input type="file" class="d-none upload-img" name="llr_image" id="llr_image" accept="image/*,application/pdf" onchange="previewImage(event, 'llr_upload')">
                                                <div class="upload-content">
                                                    <img id="llr_preview" class="preview-img" src="" alt="LLR Preview" />
                                                    <p>No file chosen, yet!</p>
                                                    <button id="imgclose-btn" class="imgclose-btn" type="button">&times;</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="llr_number">LLR Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="llr_number" id="llr_number" value="{{ old('llr_number') }}" placeholder="ex: LLR1234567">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                            <div class="wizard-buttons d-flex justify-content-end align-items-end">
                                    <div>
                                        <button type="button" class="btn btn-danger previous text-white">{{'Previous'}}</button>
                                        <button type="button" class="btn custom-btn-primary btn-round next px-4">{{'Next'}}</button>
                                   </div>
                        </div>
                    </div>
                    

                    
                    <!-- step 4 -->
                    <div class="wizard-step d-none">
                        <h5 class="text-primary">{{'Bank Information'}}</h5>
                        <div class="row gy-4">
                            
                           <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="bank_name" >{{'Bank Name'}} <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="bank_name" id="bank_name" value="{{old('bank_name')}}" placeholder="{{'Ex: Indian Overseas'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mt-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="ifsc_code">{{'IFSC Code'}} <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="{{old('ifsc_code')}}" placeholder="{{'Ex: IOBA000VEP'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="account_number">{{'Bank Account No'}} <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="account_number" id="account_number" value="{{old('account_number')}}" placeholder="{{'Ex: 74125896398'}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="account_holder_name">{{'Account Holder Name'}} <span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="account_holder_name" id="account_holder_name" value="{{old('account_holder_name')}}" placeholder="{{'Ex: smith'}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="passbook_upload">{{ 'Bank Pass Book' }} <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="passbook_upload" onclick="document.getElementById('bank_passbook').click();">
                                        <input type="file" class="d-none upload-img" name="bank_passbook" id="bank_passbook" accept="image/*" onchange="previewImage(event, 'passbook_upload')">
                                        <div class="upload-content">
                                            <img id="lisence_back_preview" class="preview-img" src="" alt="passbook Preview" />
                                            <p>No file chosen, yet!</p>
                                            <button id="imgclose-btn" class="imgclose-btn">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-buttons d-flex justify-content-end align-items-end">
                            <div>
                                <button type="button" class="btn btn-danger text-white previous">{{'Previous'}}</button>
                                <button type="button" class="btn custom-btn-primary btn-round px-4 next">{{'Next'}}</button>
                           </div>
                        </div>
                    </div>
                    
                    
                    <!-- step 5 Personal Information -->
                    <div class="wizard-step d-none">
                        <h5 class="text-primary">{{'Personal Information'}}</h5>
                        <div class="row gy-4">
                            <!-- Date of Birth -->
                            <div class="col-md-6 mt-5">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="date_of_birth">{{'DOB'}} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                    
                            <!-- Present Address -->
                            <div class="col-md-6 mt-5">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="present_address">{{'Present Address'}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="present_address" id="present_address" value="{{ old('present_address') }}" placeholder="{{'Enter Present Address'}}" required>
                                </div>
                            </div>
                    
                            <!-- Permanent Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="permanent_address">{{'Permanent Address'}} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="{{ old('permanent_address') }}" placeholder="{{'Enter Permanent Address'}}" required>
                                </div>
                            </div>
                    
                            <!-- Father's Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for"father_name">Father/ Mother/ Guardian Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="father_name" id="father_name" value="{{ old('father_name') }}" placeholder="{{'Enter Father\'s Name'}}">
                                </div>
                            </div>
                    
                            <!-- Father's Mobile Number -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for"father_mobile_number">{{'Father/ Mother/ Guardian Contact No'}} <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="father_mobile_number" id="father_mobile_number"  oninput="sanitizeAndValidatePhone(this)" value="{{ old('father_mobile_number') }}" placeholder="{{'Enter Father\'s Mobile Number'}}">
                                </div>
                            </div>
                    
                            <!-- Guardian's Name -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="mother_name">{{'Guardian\'s Name (optional)'}}</label>-->
                            <!--        <input type="text" class="form-control" name="mother_name" id="mother_name"  value="{{ old('mother_name') }}" placeholder="{{'Enter Guardian\'s Name'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <!-- Guardian's Mobile Number -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="mother_mobile_number">{{'Guardian\'s Mobile Number (optional)'}}</label>-->
                            <!--        <input type="tel" class="form-control" name="mother_mobile_number" id="mother_mobile_number"  oninput="sanitizeAndValidatePhone(this)" value="{{ old('Guardian_mobile_number') }}" placeholder="{{'Enter Guardian\'s Mobile Number'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <div class="col-md-6">
                                <div class="form-group custom-radio">
                                    <label class="input-label mb-2 ms-1">Marital Status</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input type="radio" name="marital_status" id="marital_status" value="1" {{ old('marital_status') == '1' ? 'checked' : '' }}  onchange="toggleSpouseFields()">
                                            <label class="form-check-label" for="marital_status">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="marital_status" id="marital_status" value="0" {{ old('marital_status') == '0' ? 'checked' : '' }} onchange="toggleSpouseFields()">
                                            <label class="form-check-label" for="marital_status">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <div class="col-md-12" id="spouse" style="display:none">
                                <!-- Spouse's Name -->
                                <div class="row">
                                    <div class="col-md-6" >
                                        <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="spouse_name">{{'Spouse Name'}}</label>
                                            <input type="text" class="form-control" name="spouse_name" id="spouse_name" placeholder="{{'Enter Spouse\'s Name'}}">
                                        </div>
                                    </div>
                            
                                    <!-- Spouse's Mobile Number -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                              <label class="input-label mb-2 ms-1" for="spouse_mobile_number">{{'Spouse Contact No'}}</label>
                                            <input type="tel" class="form-control" name="spouse_mobile_number" id="spouse_mobile_number" oninput="sanitizeAndValidatePhone(this)" placeholder="{{'Enter Spouse\'s Mobile Number'}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="wizard-buttons d-flex justify-content-end align-items-end">
                            <div>
                                <button type="button" class="btn btn-danger  text-white previous">{{'Previous'}}</button>
                                <button type="button" class="btn custom-btn-primary btn-round px-4 next">{{'Next'}}</button>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!--step 6-->
                    <div class="wizard-step d-none ">
                        <!--<h5 class="text-primary">{{'Emergency Details'}}</h5>-->
                        <div class="row gy-4">
                            <!-- Emergency Contact Person 1 Name -->
                            <!--<div class="col-md-6 mt-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_1_name">{{'Emergency Contact Person Name 01'}} <span class="text-danger">*</span></label>-->
                            <!--        <input type="text" class="form-control" name="emergency_contact_person_1_name" id="emergency_contact_person_1_name" value="{{ old('emergency_contact_person_1_name') }}" placeholder="{{'Enter Contact Person Name 01'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <!-- Emergency Contact Person 1 Mobile -->
                            <!--<div class="col-md-6 mt-4">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_1_mobile">{{'Emergency Contact Person 01 Mobile'}} -->
                            <!--        <span class="text-danger">*</span></label>-->
                            <!--        <input type="tel" class="form-control" name="emergency_contact_person_1_mobile" id="emergency_contact_person_1_mobile" oninput="sanitizeAndValidatePhone(this)" value="{{ old('emergency_contact_person_1_mobile') }}" placeholder="{{'Enter Mobile Number 01'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <!-- Emergency Contact Person 2 Name -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_2_name">{{'Emergency Contact Person Name 02'}} <span class="text-danger">*</span></label>-->
                            <!--        <input type="text" class="form-control" name="emergency_contact_person_2_name" id="emergency_contact_person_2_name" value="{{ old('emergency_contact_person_2_name') }}" placeholder="{{'Enter Contact Person Name 02'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                    
                            <!-- Emergency Contact Person 2 Mobile -->
                            <!--<div class="col-md-6">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="emergency_contact_person_2_mobile">{{'Emergency Contact Person 02 Mobile'}} -->
                            <!--        <span class="text-danger">*</span></label>-->
                            <!--        <input type="tel" class="form-control" name="emergency_contact_person_2_mobile" id="emergency_contact_person_2_mobile" oninput="sanitizeAndValidatePhone(this)" value="{{ old('emergency_contact_person_2_mobile') }}" placeholder="{{'Enter Mobile Number 02'}}">-->
                            <!--    </div>-->
                            <!--</div>-->
                           @php($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                            <!-- Blood Group (Select Box) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="blood_group">{{ 'Blood Group' }} <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="blood_group" id="blood_group" required>
                                        @foreach ($bloodGroups as $bloodGroup)
                                            <option value="{{ $bloodGroup }}">{{ $bloodGroup }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <div class="wizard-buttons d-flex justify-content-end align-items-end">
                            <div>
                                <button type="button" class="btn btn-danger text-white previous">{{'Previous'}}</button>
                                <button type="submit" class="btn btn-success btn-round px-4 text-white final-submit">{{'Submit'}}</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </form>
       </div>
   </div>

<script>

// Select all image upload elements
        const imageUploadWrappers = document.querySelectorAll('.upload-area');

        imageUploadWrappers.forEach(wrapper => {
            const imageInput = wrapper.querySelector('.upload-img');
            const imagePreview = wrapper.querySelector('.preview-img');
            const resetButton = wrapper.querySelector('.imgclose-btn');

            // Preview the selected image
            imageInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        imagePreview.src = reader.result;
                        imagePreview.style.display = 'block'; // Show the preview
                        resetButton.style.display = 'block'; // Show the reset button
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Reset the image selection
            resetButton.addEventListener('click', () => {
                imageInput.value = ''; // Clear the input
                imagePreview.src = ''; // Clear the preview
                imagePreview.style.display = 'none'; // Hide the preview
                resetButton.style.display = 'none'; // Hide the reset button
            });
        });






 document.addEventListener('DOMContentLoaded', function () {

        let today = new Date();
        let formattedDate = today.toISOString().split("T")[0];
        document.getElementById("date_of_birth").setAttribute("max", formattedDate);

       let currentStep = 1;
       const totalSteps = $('.wizard-step').length;

    function updateProgressBar() {
        let progressPercentage = (currentStep / totalSteps) * 100;
        $('#progressBar').css('width', progressPercentage + '%');
        $('#progressBar').attr('aria-valuenow', progressPercentage);
    }

       function showStep(step) {
           $('.wizard-step').addClass('d-none');
           $(`.wizard-step:nth-of-type(${step})`).removeClass('d-none');
           updateProgressBar();
       }
       
       function step_one() {
            let fields = [
                'first_name', 
                'last_name', 
                'mobile_number', 
                'current_city_id',  // Select box
                // 'lead_source_id',    // Select box
                'interested_city_id',// Select box
                'vehicle_type'       // Select box
            ];
        
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
               } 
               
                else if (field === 'current_city_id' ||  field === 'interested_city_id' || field === 'vehicle_type') {
                    if (inputField.val() === '' || inputField.val() === null) {
                        inputField.addClass('is-invalid');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); 
                    }
                }
                else {
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


       
       function step_two() {
            let fields = [
                'photo',  // Image input
                'apply_job_source',  // Select box
            ];
            let valid = true;
        
            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let messageElement = inputField.siblings('div').find('p');
                if (field === 'apply_job_source') {
                    if (inputField.val() === '') {
                        inputField.addClass('is-invalid'); 
                        messageElement.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        messageElement.removeClass('text-danger').addClass('text-success'); 
                    }
                } else if (field === 'photo') {
                    // Validate image input
                    if (inputField.val() === '' || inputField[0].files.length === 0) {
                        inputField.addClass('is-invalid'); 
                        messageElement.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); 
                        messageElement.removeClass('text-danger').addClass('text-success');
                    }
                }
            });
        
            return valid; 
        }


        // function step_three() {
        //     let fields = ['aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'pan_card_front', 'pan_number', 'driving_license_front', 'driving_license_back','license_number'];
        //     let valid = true;
        
        //     fields.forEach(function (field) {
        //         let inputField = $(`[name="${field}"]`);
        //         let messageElement = inputField.siblings('div').find('p'); 
        
        
        //              // Validate aadhar number
        //         if (field.includes('aadhar_number')) {
        //             var aadharValue = $('#aadhar_number').val(); 
                    
        //             if (!aadharNumber(document.getElementById('aadhar_number')) || inputField.val() === '') { 
        //                 inputField.addClass('is-invalid');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid');
        //             }
        //         }
                
                
        //          // Validate pan  number
        //          else if (field.includes('pan_number')) {
        //             var panValue = $('#pan_number').val(); 
                    
        //             if (!validatePAN(document.getElementById('pan_number')) || inputField.val() === '' ) { 
        //                 inputField.addClass('is-invalid');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid');
        //             }
        //         }
                
        //           else if (field.includes('license_number')) {
        //             var licenseValue = $('#license_number').val(); 
                    
        //             if (!validatePAN(document.getElementById('license_number')) || inputField.val() === '' ) { 
        //                 inputField.addClass('is-invalid');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid');
        //             }
        //         }
                
        //         else if (field === 'aadhar_card_front') {
        //             // Validate image input
        //             if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                 inputField.addClass('is-invalid'); 
        //                 messageElement.addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid'); 
        //                 messageElement.removeClass('text-danger').addClass('text-success');
        //             }
        //         }
        
        //          else if (field === 'aadhar_card_back') {
        //             // Validate image input
        //             if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                 inputField.addClass('is-invalid'); 
        //                 messageElement.addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid'); 
        //                 messageElement.removeClass('text-danger').addClass('text-success');
        //             }
        //         }
                
        //         else if (field === 'pan_card_front') {
        //             // Validate image input
        //             if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                 inputField.addClass('is-invalid'); 
        //                 messageElement.addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid'); 
        //                 messageElement.removeClass('text-danger').addClass('text-success');
        //             }
        //         }
                
        //         else if (field === 'driving_license_front') {
        //             // Validate image input
        //             if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                 inputField.addClass('is-invalid'); 
        //                 messageElement.addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid'); 
        //                 messageElement.removeClass('text-danger').addClass('text-success');
        //             }
        //         }
        
        //         else if (field === 'driving_license_back') {
        //             // Validate image input
        //             if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                 inputField.addClass('is-invalid'); 
        //                 messageElement.addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid'); 
        //                 messageElement.removeClass('text-danger').addClass('text-success');
        //             }
        //         }
        
        
        
        //         // if (inputField.val() === '') {
        //         //     // If field is empty, invalidate and show error
        //         //     inputField.addClass('is-invalid'); // Add invalid class to input field
        //         //     messageElement.addClass('text-danger'); // Add invalid class to <p>
        //         //     valid = false;
        //         // } else {
        //         //     inputField.removeClass('is-invalid').addClass('is-valid'); // Mark input as valid
        //         //     messageElement.removeClass('text-danger'); // Clear message
        //         // }
                
                
        //     });
        
        //     return valid; // Return false if any image field is empty
        // }
        
        
        
function step_three() {
    let valid = true;

    // Validate Aadhar and PAN - always
    let fields = ['aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'pan_card_front', 'pan_number'];

    fields.forEach(function (field) {
        let inputField = $(`[name="${field}"]`);
        let messageElement = inputField.siblings('div').find('p');

        if (field === 'aadhar_number') {
            if (!aadharNumber(document.getElementById('aadhar_number')) || inputField.val() === '') {
                inputField.addClass('is-invalid');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
            }
        } else if (field === 'pan_number') {
            if (!validatePAN(document.getElementById('pan_number')) || inputField.val() === '') {
                inputField.addClass('is-invalid');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
            }
        } else {
            if (inputField.val() === '' || inputField[0].files.length === 0) {
                inputField.addClass('is-invalid');
                messageElement.addClass('text-danger');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
                messageElement.removeClass('text-danger').addClass('text-success');
            }
        }
    });


    // Validate LLR if checkbox is checked
    if ($('#llr_checkbox').is(':checked')) {
        let llrImageField = $('[name="llr_image"]');
        let llrNumberField = $('[name="llr_number"]');
        let uploadArea = document.getElementById('llr_upload');
        let messageElement = uploadArea.querySelector('p');
    
        if (llrImageField.val() === '' || llrImageField[0].files.length === 0) {
            llrImageField.addClass('is-invalid');
            messageElement.style.color = 'red';
            messageElement.style.display = 'block';
            valid = false;
        } else {
            llrImageField.removeClass('is-invalid').addClass('is-valid');
            messageElement.style.display = 'none';
        }
    
        if (llrNumberField.val().trim() === '') {
            llrNumberField.addClass('is-invalid');
            valid = false;
        } else {
            llrNumberField.removeClass('is-invalid').addClass('is-valid');
        }
    }
        // Validate License if checkbox is not checked
        else {
            let licenseFields = ['driving_license_front', 'driving_license_back', 'license_number'];
    
            licenseFields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let messageElement = inputField.siblings('div').find('p');
    
                if (field === 'license_number') {
                    if (!validatePAN(document.getElementById('license_number')) || inputField.val() === '') {
                        inputField.addClass('is-invalid');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                    }
                } else {
                    if (inputField.val() === '' || inputField[0].files.length === 0) {
                        inputField.addClass('is-invalid');
                        messageElement.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        messageElement.removeClass('text-danger').addClass('text-success');
                    }
                }
            });
        }
    
        return valid;
}



        
        function step_four() {
            let fields = [
                'bank_passbook', 
                'account_number', 
                'account_holder_name', 
                'bank_name', 
                'ifsc_code'
            ];
            let valid = true;
        
            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let messageElement = inputField.siblings('div').find('p'); // Find the <p> tag inside the .upload-content div
        
                // Reset message class at the start of each validation
                messageElement.removeClass('text-danger text-success'); // Clear previous message classes
        
                if (field === 'bank_passbook') {
                    // Handle the image upload validation
                    if (inputField.val() === '') {
                        // If image field is empty, invalidate and show error
                        inputField.addClass('is-invalid'); // Add invalid class to input field
                        messageElement.addClass('text-danger'); // Add danger class to <p>
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); // Mark input as valid
                        messageElement.removeClass('text-danger').addClass('text-success'); // Add success class to <p>
                    }
                } else {
                    // Handle the regular input validation
                    if (inputField.val() === '') {
                        // If field is empty, invalidate and show error
                        inputField.addClass('is-invalid'); // Add invalid class to input field
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); // Mark input as valid
                    }
                }
            });
        
            return valid; // Return false if any field is empty
        }
        
        function step_five() {
            let fields = [
                'date_of_birth', 
                'present_address', 
                'permanent_address', 
                'father_name', 
                'father_mobile_number',
                // 'mother_name',
                // 'mother_mobile_number',
            ];
            let valid = true;
        
            const phoneRegex = /^\+91[0-9]{10}$/; // Regex for Indian mobile number format
        
            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
        
                if (field.includes('mobile_number')) {
                    // Validate mobile number fields using regex
                    if (!phoneRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid'); // Add invalid class to input field
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); // Mark input as valid
                    }
                }else {
                    // Handle the regular input validation
                    if (inputField.val() === '') {
                        // If field is empty, invalidate and show error
                        inputField.addClass('is-invalid'); // Add invalid class to input field
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); // Mark input as valid
                    }
                }
            });
        
            return valid; // Return false if any field is invalid
        }

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
                if (field.includes('mobile')) { // 'mobile_number' changed to 'mobile' as per your field naming
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
                // Validate other fields as required (name fields, etc.)
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

        
       // Check if any field in the current step is empty or invalid
       function isStepValid() {
           let valid = true;

           if (currentStep === 1 && !step_one()) {
               valid = false; 
           }else if (currentStep === 2 && !step_two()) {
               valid = false; 
           }else if (currentStep === 3 && !step_three()) {
               valid = false; 
           }else if (currentStep === 4 && !step_four()){
               valid = false;
           }else if (currentStep === 5 && !step_five()){
               valid = false;
           }else if (currentStep === 6 && !step_six()){
               valid = false;
           }

           return valid;
       }
       
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
        
            if (step_six()) {
                // alert("jkkjhjk");
                var form = $('#wizardForm')[0]; 
                var formData = new FormData(form);
                
                var submitBtn = $('.final-submit');
                submitBtn.prop('disabled', true).text('Submitting...');
                
            
                $.ajax({
                    url: "{{route('admin.Green-Drive-Ev.delivery-man.create')}}",
                    type: "POST",
                    data: formData,
                    processData: false,  
                    contentType: false,  
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                            submitBtn.prop('disabled', false).text('Submit');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) { 
                            $.each(xhr.responseJSON.errors, function(key, value) { 
                                toastr.error(value[0]); 
                            });
                        } else {
                            toastr.error("Please try again.");
                            submitBtn.prop('disabled', false).text('Submit');
                        }
                    }
                });
            } 
        });

       // Handle "Next" button
       $('.next').on('click', function () {
           if (isStepValid()) {
               currentStep++;
               if (currentStep <= totalSteps) {
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

       showStep(currentStep);
       
       let id_name_value = $("#current_city_id").val(); // Get the value of the city select dropdown
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
   
    //   mobile number validate
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
        } else {
            referralFields.style.display = 'none';
        }
    }
    
    function toggleSpouseFields() {
        const maritalStatusYes = document.getElementById('marital_status').checked;
        const spouseFields = document.getElementById('spouse');
    
        if (maritalStatusYes) {
            spouseFields.style.display = 'block';
        } else {
            spouseFields.style.display = 'none';
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
    document.getElementById('license_details_section').style.display = isLLR ? 'none' : 'block';
    document.getElementById('llr_details_section').style.display = isLLR ? 'block' : 'none';
    
        if (isLLR) {
        // Clear License Inputs
        $('#driving_license_front').val('');
        $('#driving_license_back').val('');
        $('#license_number').val('');

        // Clear License Previews
        $('#lisence_front_preview').attr('src', '').hide();
        $('#licence_back_preview').attr('src', '').hide();

        // Reset "No file chosen" message
        $('#license_upload_front').find('p').text('No file chosen, yet!').show();
        $('#license_upload_back').find('p').text('No file chosen, yet!').show();
    }
    
}
</script>

</x-app-layout>

