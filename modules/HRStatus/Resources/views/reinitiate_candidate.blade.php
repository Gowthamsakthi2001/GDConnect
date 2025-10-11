<x-app-layout>
    <style>
        /* Modern styling with animations */
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 100%;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        
        .upload-area:hover {
            border-color: #6c757d;
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .upload-area.active {
            border-color: #0d6efd;
            background-color: #e7f1ff;
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
            transition: all 0.3s ease;
        }
        
        .upload-content p {
            margin-top: 15px;
            font-size: 14px;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .upload-content i {
            font-size: 32px;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        
        .upload-area:hover .upload-content i,
        .upload-area:hover .upload-content p {
            color: #495057;
        }
        
        .upload-area.active .upload-content i,
        .upload-area.active .upload-content p {
            color: #0d6efd;
        }
        
.preview-img,
.preview-pdf {
    /*display: none;*/
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
.preview-pdf {
    border: none;
}

        
.imgclose-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    font-size: 20px;
    width: 28px;
    height: 28px;
    line-height: 24px;
    text-align: center;
    border-radius: 50%;
    cursor: pointer;
    z-index: 10;
    /*display: none;*/
}
        
        .imgclose-btn:hover {
                display: block !important;
            background-color: #bb2d3b;
            transform: scale(1.1);
        }
        
        /* Progress bar styling */
        .progress {
            height: 10px;
            border-radius: 5px;
            margin-bottom: 30px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .progress-bar {
            background: linear-gradient(310deg, #17c653, #0d8a3f);
            transition: width 0.6s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }
        
        /* Step animation */
        .wizard-step {
            animation: fadeIn 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Form input styling */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .form-control.is-valid, .was-validated .form-control:valid {
            border-color: #198754;
            background-image: none;
            padding-right: 15px;
        }
        
        .form-control.is-invalid, .was-validated .form-control:invalid {
            border-color: #dc3545;
            background-image: none;
            padding-right: 15px;
        }
        
        /* Custom radio buttons */
        .custom-radio input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            position: relative;
            outline: none;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 8px;
        }
        
        .custom-radio input[type="radio"]:checked {
            border-color: #198754;
            background: linear-gradient(310deg, #17c653, #0d8a3f);
        }
        
        .custom-radio input[type="radio"]:checked::before {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }
        
        /* Button styling */
        .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(310deg, #0d6efd, #0b5ed7);
            border: none;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(310deg, #0b5ed7, #0a58ca);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(13, 110, 253, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(310deg, #dc3545, #bb2d3b);
            border: none;
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.2);
        }
        
        .btn-danger:hover {
            background: linear-gradient(310deg, #bb2d3b, #b02a37);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(310deg, #198754, #157347);
            border: none;
            box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2);
        }
        
        .btn-success:hover {
            background: linear-gradient(310deg, #157347, #146c43);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(25, 135, 84, 0.3);
        }
        
        /* Section headers */
        .section-header {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        
        .section-header::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(310deg, #0d6efd, #0b5ed7);
            border-radius: 3px;
        }
        
        /* Validation messages */
        .invalid-feedback {
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .upload-area {
                padding-bottom: 80%;
            }
            
            .section-header {
                font-size: 1.5rem;
            }
        }
        
        /* Floating labels effect */
        .form-floating > label {
            transition: all 0.3s ease;
        }
        
        /* Checkbox styling */
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-top: 0.2rem;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
        
        /* Card styling */
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
        }
        
        .card-body {
            padding: 30px;
        }
        
        /* Page header */
        .page-header {
            margin-bottom: 30px;
        }
        
        /* Success state for upload areas */
        .upload-success .upload-content i,
        .upload-success .upload-content p {
            color: #198754 !important;
        }
        
        .upload-success {
            border-color: #198754 !important;
            background-color: #e8f8f0 !important;
        }
        
        /* Error state for upload areas */
        .upload-error .upload-content i,
        .upload-error .upload-content p {
            color: #dc3545 !important;
        }
        
        .upload-error {
            border-color: #dc3545 !important;
            background-color: #fce8e8 !important;
        }
    </style>

    <!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="fa fa-user-check text-success me-2" style="font-size: 1.5rem;"></i>
        <h2 class="page-header-title mb-0">Edit Candidate</h2>
    </div>

    <a href="{{ route('admin.Green-Drive-Ev.hr_status.index') }}" class="btn btn-dark">
        <i class="fa fa-arrow-left me-1"></i> Back
    </a>
</div>

    <!-- End Page Header -->

    <!-- Wizard Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" id="wizardForm" class="g-3 p-3">
                @csrf
                <!-- Progress Bar -->
                <div class="progress mb-4">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                        style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                
                <div id="wizard">
                    <!-- Step 1: Rider Information -->
                    <div class="wizard-step">
                        <div class="section-header">
                            <h4 class="text-primary mb-0"><i class="fas fa-user-circle me-2"></i> Basic Information</h4>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="first_name" id="f_name" 
                                           value="{{$data->first_name ?? ''}}" placeholder="Ex: John" required>
                                    <label for="f_name">First Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid first name</div>
                                </div>
                            </div>
                            <input type="hidden" value="{{$data->id}}" name="dm_id">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="last_name" id="l_name" 
                                           value="{{$data->last_name ?? ''}}" placeholder="Ex: Doe" required>
                                    <label for="l_name">Last Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid last name</div>
                                </div>
                            </div>
                        </div> 
                        
                        
                         <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="male" {{ $data->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $data->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <label for="mobile_no">Gender<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="house_no" id="house_no"  value="{{$data->house_no ?? ''}}"
                                           placeholder=""  required>
                                    <label for="house_no">House No<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid house no</div>
                                </div>
                            </div>
                        </div>
                        
                     <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="street_name" id="street_name"  value="{{$data->street_name ?? ''}}"
                                           placeholder=""  required>
                                    <label for="street_name">Street Name<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid street name</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="pincode" placeholder="Enter 6-digit pincode"   oninput="sanitizeAndValidatePincode(this)"  id="pincode" value="{{$data->pincode ?? ''}}"
                                           placeholder=""  required>
                                    <label for="pincode">Pin Code<span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid pin code</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="mobile_number" id="mobile_no" 
                                           oninput="sanitizeAndValidatePhone(this)" value="{{$data->mobile_number ?? ''}}"
                                           placeholder="+917894561230" maxlength="13" required>
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid 10-digit mobile number</div>
                                    <small class="text-muted">Format: +91XXXXXXXXXX</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                         <input type="tel" class="form-control" name="alternative_number" id="alter_number" 
                                           oninput="sanitizeAndValidatePhone(this)"  value="{{$data->alternative_number ?? ''}}"
                                           placeholder="+917894561230" maxlength="13" required>
                                    <label for="alternative_number">Alternative No<span class="text-danger">*</span></label>
                                   <div class="invalid-feedback">Please provide a valid 10-digit mobile number</div>
                                    <small class="text-muted">Format: +91XXXXXXXXXX</small>
                                </div>
                            </div>
                        </div>
                        
                     <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email_id" id="email_id"  value="{{$data->email ?? ''}}"
                                           placeholder="abc@gmail.com"  required>
                                    <label for="email_id">Email ID <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide a valid email</div>
                                    <small class="text-muted">Format: abc@gmail.com</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="role" name="role"  required>
                                        <option value="in-house" {{ $data->work_type == 'in-house' ? 'selected' : '' }}>Employee</option>
                                        <option value="deliveryman" {{ $data->work_type == 'deliveryman' ? 'selected' : '' }}>Deliveryman</option>
                                        <option value="adhoc" {{ $data->work_type == 'adhoc' ? 'selected' : '' }}>Adhoc</option>
                                        <option value="helper" {{ $data->work_type == 'helper' ? 'selected' : '' }}>Helper</option>
                                    </select>
                                    <label for="role">Role<span class="text-danger">*</span></label>
                                   <div class="invalid-feedback">Please provide a valid role</div>
                                </div>
                            </div>
                        </div>
                        
                        
                            <div class="row mt-3" id="vehicle_rider_row">
                                                                     @php
                                            $vehicleTypes = ['2W', '3W', '4W','Rental'];
                                        @endphp
                             
                               <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="vehicle_type" name="vehicle_type"  required>
                                            @foreach($vehicleTypes as $type)
                                                <option value="{{ $type }}" {{ isset($data) && $data->vehicle_type == $type ? 'selected' : '' }} >
                                                {{ $type }}
                                            </option>
                                            @endforeach
                                    </select>
                                    <label for="vehicle_type">Vehicle Type<span class="text-danger">*</span></label>
                                </div>
                            </div>
                            
                                <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select" id="rider_type" name="rider_type"  required>
                                                        @foreach($rider_types as $type)
                                                            <option value="{{ $type->id }}" {{ isset($data) && $data->rider_type == $type->id ? 'selected' : '' }} >
                                                                {{ $type->type }}
                                                            </option>
                                                        @endforeach
                                    </select>
                                    <label for="rider_type">Rider Type<span class="text-danger">*</span></label>
                                </div>
                            </div>
                             
                         </div>  
                        
                        <div class="row mt-3">

                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select custom-select2-field" id="current_city_id" name="current_city_id" 
                                            onchange="get_area('current_city_id')" required>
                                        <option value="">Select</option>
                                         @foreach($city as $c)
                                           <option value="{{ $c->id }}" {{ ($data->current_city_id == $c->id) ? 'selected' : '' }}>
                                                {{ $c->city_name }}
                                            </option>
                                        @endforeach
                                      
                                    </select>
                                    <label for="current_city_id">City <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please select a city</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-select custom-select2-field" id="interested_city_id" name="interested_city_id" required>
                                        
                                    </select>
                                    <label for="interested_city_id">Area <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please select an area</div>
                                </div>
                            </div>
                        </div>
                         
                        
                        <div class="wizard-buttons d-flex justify-content-between align-items-center mt-4">
                            <div></div> <!-- Empty div for alignment -->
                            <button type="button" class="btn btn-primary next">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <?php 
                    
                                                 
                           $aadhar_front_path = isset($data->aadhar_card_front) 
                            ? asset('public/EV/images/aadhar/' . $data->aadhar_card_front) 
                            : asset('public/EV/images/dummy.jpg');
                        
                        $aadhar_back_path  = isset($data->aadhar_card_back)  
                            ? asset('public/EV/images/aadhar/' . $data->aadhar_card_back)  
                            : asset('public/EV/images/dummy.jpg');
                        
                        $is_aadhar_front_pdf = Str::endsWith(strtolower($aadhar_front_path), '.pdf');
                        $is_aadhar_back_pdf  = Str::endsWith(strtolower($aadhar_back_path), '.pdf');
                        
                        // PAN
                        $pan_path = isset($data->pan_card_front) 
                            ? asset('public/EV/images/pan/' . $data->pan_card_front) 
                            : asset('public/EV/images/dummy.jpg');
                        $is_pan_pdf = Str::endsWith(strtolower($pan_path), '.pdf');
                        
                        // License
                        $license_front_path = isset($data->driving_license_front) 
                            ? asset('public/EV/images/driving_license/' . $data->driving_license_front) 
                            : asset('public/EV/images/dummy.jpg');
                        
                        $license_back_path  = isset($data->driving_license_back)  
                            ? asset('public/EV/images/driving_license/' . $data->driving_license_back)  
                            : asset('public/EV/images/dummy.jpg');
                        
                        $is_license_front_pdf = Str::endsWith(strtolower($license_front_path), '.pdf');
                        $is_license_back_pdf  = Str::endsWith(strtolower($license_back_path), '.pdf');
                        
                        // LLR
                        $llr_path = isset($data->llr_image) 
                            ? asset('public/EV/images/llr_images/' . $data->llr_image) 
                            : asset('public/EV/images/dummy.jpg');
                        $is_llr_pdf = Str::endsWith(strtolower($llr_path), '.pdf');
                        
                        // Bank Passbook
                        $passbook_path = isset($data->bank_passbook) 
                            ? asset('public/EV/images/bank_passbook/' . $data->bank_passbook) 
                            : asset('public/EV/images/dummy.jpg');
                        $is_passbook_pdf = Str::endsWith(strtolower($passbook_path), '.pdf');
                        
                        // Bank Statement
                        $statement_path = isset($data->bank_statements) 
                            ? asset('public/EV/images/bank_statements/' . $data->bank_statements) 
                            : asset('public/EV/images/dummy.jpg');
                        $is_statement_pdf = Str::endsWith(strtolower($statement_path), '.pdf');
                        
                        // Profile Photo
                        $profile_path = isset($data->photo) 
                            ? asset('public/EV/images/photos/' . $data->photo) 
                            : asset('public/EV/images/dummy.jpg');
                        $is_profile_pdf = Str::endsWith(strtolower($profile_path), '.pdf');
                        
                    ?>
                    <!-- Step 3: KYC Information -->
                    <div class="wizard-step d-none">
                        <div class="section-header">
                            <h4 class="text-primary mb-0"><i class="fas fa-id-card me-2"></i> KYC Information</h4>
                            <p class="text-muted">Government identification documents</p>
                        </div>
                        
                        <div class="row">
                            <!-- Aadhar Card Section -->
                            <div class="col-12 mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-address-card me-2"></i> Aadhar Details</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label mb-2">Aadhar Front <span class="text-danger">*</span></label>
                                        <div class="upload-area" id="aadhar_upload_front" onclick="document.getElementById('aadhar_card_front').click();">
                                            <input type="file" class="d-none upload-img" name="aadhar_card_front" 
                                                   id="aadhar_card_front" accept="image/*,application/pdf" 
                                                   onchange="previewImage(event, 'aadhar_upload_front')" required>
                                            <button type="button" class="imgclose-btn" onclick="resetUpload('aadhar_card_front', 'aadhar_upload_front'); event.stopPropagation();">&times;</button>
                                            <div class="upload-content">
                                                <i class="fas fa-id-card"></i>
                                                <p>Upload front side</p>
                                                    <img id="aadhar_front_preview" 
                                                         class="preview-img" 
                                                         src="{{ !$is_aadhar_front_pdf ? $aadhar_front_path : asset('EV/images/dummy.jpg') }}" 
                                                         alt="Aadhar Front" 
                                                         style="{{ $is_aadhar_front_pdf ? 'display:none;' : '' }}">
                                    
                                                    {{-- PDF Preview --}}
                                                    <iframe class="preview-pdf" 
                                                            src="{{ $is_aadhar_front_pdf ? $aadhar_front_path : '' }}" 
                                                            style="{{ $is_aadhar_front_pdf ? '' : 'display:none;' }}">
                                                    </iframe>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Please upload Aadhar front image</div>
                                    </div>

                    
                                    <div class="col-md-4">
                                        <label class="form-label mb-2">Aadhar Back <span class="text-danger">*</span></label>
                                        <div class="upload-area" id="aadhar_upload_back" 
                                             onclick="document.getElementById('aadhar_card_back').click();">
                                            <input type="file" class="d-none upload-img" name="aadhar_card_back" 
                                                   id="aadhar_card_back" accept="image/*,application/pdf"  
                                                   onchange="previewImage(event, 'aadhar_upload_back')" required>
                                                    <button type="button" class="imgclose-btn" onclick="resetUpload('aadhar_upload_back', 'aadhar_upload_back'); event.stopPropagation();">&times;</button>
                                            <div class="upload-content">
                                                <i class="fas fa-id-card"></i>
                                                <p>Upload back side</p>
                                                <img id="aadhar_back_preview" class="preview-img"  src="{{ !$is_aadhar_back_pdf ? $aadhar_back_path : asset('EV/images/dummy.jpg') }}"  alt="Aadhar Back" style="{{ $is_aadhar_back_pdf ? 'display:none;' : '' }}"/>
                                                <iframe  src="{{ $is_aadhar_back_pdf ? $aadhar_back_path : '' }}"  class="preview-pdf" style="{{ $is_aadhar_back_pdf ? '' : 'display:none;' }}"></iframe>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Please upload Aadhar back image</div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="aadhar_number" id="aadhar_number" 
                                                   oninput="aadharNumber(this)" value="{{$data->aadhar_number}}" 
                                                   placeholder="ex: 1234 5678 9123" required>
                                            <label for="aadhar_number">Aadhar Number <span class="text-danger">*</span></label>
                                            <div class="invalid-feedback">Please enter a valid 12-digit Aadhar number</div>
                                            <small class="text-muted">Format: 1234 5678 9123</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <!-- PAN Card Section -->
                            <div class="col-12 mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-credit-card me-2"></i> PAN Details</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label mb-2">PAN Card <span class="text-danger">*</span></label>
                                        <div class="upload-area" id="pan_upload_front" 
                                             onclick="document.getElementById('pan_card_front').click();">
                                            <input type="file" class="d-none upload-img" name="pan_card_front" 
                                                   id="pan_card_front" accept="image/*,application/pdf"  
                                                   onchange="previewImage(event, 'pan_upload_front')" required>
                                                    <button type="button" class="imgclose-btn" onclick="resetUpload('pan_upload_front', 'pan_upload_front'); event.stopPropagation();">&times;</button>
                                            <div class="upload-content">
                                                <i class="fas fa-credit-card"></i>
                                                <p>Upload PAN card</p>
                                                <img id="pan_front_preview" src="{{ !$is_pan_pdf ? $pan_path : asset('EV/images/dummy.jpg') }}"  style="{{ $is_pan_pdf ? 'display:none;' : '' }}" class="preview-img"  alt="PAN Card" />
                                            <iframe class="preview-pdf" src="{{ $is_pan_pdf ? $pan_path : '' }}"  style="{{ $is_pan_pdf ? '' : 'display:none;' }}"></iframe>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Please upload PAN card image</div>
                                    </div>
                    
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="pan_number" id="pan_number" 
                                                   oninput="validatePAN()" value="{{$data->pan_number}}"  
                                                   placeholder="ex: ALWPG5809L" required>
                                            <label for="pan_number">PAN Number <span class="text-danger">*</span></label>
                                            <div class="invalid-feedback">Please enter a valid PAN number</div>
                                            <small class="text-muted">Format: ABCDE1234F</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        <div id="LicenseSection">
                            <!-- LLR Checkbox -->
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="llr_checkbox"   {{ !empty($data->llr_number) ? 'checked' : '' }}
                                           name="is_llr" value="1" onchange="toggleLicenseLlr()">
                                    <label class="form-check-label" for="llr_checkbox">
                                        <strong>I have LLR only (Learning License)</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- License Details Section -->
                            <div id="license_details_section" >
                                <div class="col-12 mb-4">
                                    <h5 class="text-primary mb-3"><i class="fas fa-id-card-alt me-2"></i> License Details</h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label mb-2">License Front <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="license_upload_front" 
                                                 onclick="document.getElementById('driving_license_front').click();">
                                                <input type="file" class="d-none upload-img" name="driving_license_front" 
                                                       id="driving_license_front" accept="image/*,application/pdf"  
                                                       onchange="previewImage(event, 'license_upload_front')">
                                                        <button type="button" class="imgclose-btn" onclick="resetUpload('license_upload_front', 'license_upload_front'); event.stopPropagation();">&times;</button>
                                                <div class="upload-content">
                                                    <i class="fas fa-id-card-alt"></i>
                                                    <p>Upload front side</p>
                                                    <img id="lisence_front_preview" class="preview-img" src="{{ !$is_license_front_pdf ? $license_front_path : asset('EV/images/dummy.jpg') }}"  style="{{ $is_license_front_pdf ? 'display:none;' : '' }}" alt="License Front" />
                                                     <iframe class="preview-pdf" src="{{ $is_license_front_pdf ? $license_front_path : '' }}"  style="{{ $is_license_front_pdf ? '' : 'display:none;' }}"></iframe>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback">Please upload license front image</div>
                                        </div>
                                
                                        <div class="col-md-4">
                                            <label class="form-label mb-2">License Back <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="license_upload_back" 
                                                 onclick="document.getElementById('driving_license_back').click();">
                                                <input type="file" class="d-none upload-img" name="driving_license_back" 
                                                       id="driving_license_back" accept="image/*,application/pdf"  
                                                       onchange="previewImage(event, 'license_upload_back')">
                                                        <button type="button" class="imgclose-btn"  onclick="resetUpload('license_upload_back', 'license_upload_back'); event.stopPropagation();">&times;</button>
                                                <div class="upload-content">
                                                    <i class="fas fa-id-card-alt"></i>
                                                    <p>Upload back side</p>
                                                    <img id="licence_back_preview" class="preview-img" src="{{ !$is_license_back_pdf ? $license_back_path : asset('EV/images/dummy.jpg') }}" style="{{ $is_license_back_pdf ? 'display:none;' : '' }}" alt="License Back" />
                                                    <iframe class="preview-pdf" src="{{ $is_license_back_pdf ? $license_back_path : '' }}" style="{{ $is_license_back_pdf ? '' : 'display:none;' }}"></iframe>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback">Please upload license back image</div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="license_number" 
                                                       id="license_number" oninput="validateLicense()" 
                                                       value="{{$data->license_number}}" 
                                                       placeholder="ex: ABC1234567">
                                                <label for="license_number">License Number <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter a valid license number</div>
                                                <small class="text-muted">Format: State code followed by numbers</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- LLR Details Section -->
                            <div id="llr_details_section" style="display: none;">
                                <div class="col-12 mb-4">
                                    <h5 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i> LLR Details</h5>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label mb-2">LLR Image <span class="text-danger">*</span></label>
                                            <div class="upload-area" id="llr_upload" 
                                                 onclick="document.getElementById('llr_image').click();">
                                                <input type="file" class="d-none upload-img" name="llr_image" 
                                                       id="llr_image" accept="image/*,application/pdf" 
                                                       onchange="previewImage(event, 'llr_upload')">
                                                        <button type="button" class="imgclose-btn" onclick="resetUpload('llr_upload', 'llr_upload'); event.stopPropagation();">&times;</button>
                                                <div class="upload-content">
                                                    <i class="fas fa-file-alt"></i>
                                                    <p>Upload LLR document</p>
                                                    <img id="llr_preview" class="preview-img" src="{{ !$is_llr_pdf ? $llr_path : asset('EV/images/dummy.jpg') }}" style="{{ $is_llr_pdf ? 'display:none;' : '' }}" alt="LLR Document" />
                                                    <iframe class="preview-pdf" src="{{ $is_llr_pdf ? $llr_path : '' }}" style="{{ $is_llr_pdf ? '' : 'display:none;' }}"></iframe>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback">Please upload LLR document</div>
                                            <small class="text-muted">Accepts JPG, PNG or PDF</small>
                                        </div>
                            
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="llr_number" 
                                                       id="llr_number" value="{{$data->llr_number}}" 
                                                       placeholder="ex: LLR1234567">
                                                <label for="llr_number">LLR Number <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter LLR number</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        </div>
                        
                        <div class="wizard-buttons d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary previous">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary next">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 4: Bank Information -->
                    <div class="wizard-step d-none">
                        <div class="section-header">
                            <h4 class="text-primary mb-0"><i class="fas fa-university me-2"></i> Bank Information</h4>
                            <p class="text-muted">Bank account details for salary processing</p>
                        </div>
                        
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="bank_name" id="bank_name" 
                                           value="{{$data->bank_name}}"  placeholder="Ex: Indian Overseas" required>
                                    <label for="bank_name">Bank Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide bank name</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" 
                                           value="{{$data->ifsc_code}}" placeholder="Ex: IOBA000VEP" required>
                                    <label for="ifsc_code">IFSC Code <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide valid IFSC code</div>
                                    <small class="text-muted">Format: 4 letters + 0 + 6 digits (e.g., SBIN0001234)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="account_number" id="account_number" 
                                           value="{{$data->account_number}}" placeholder="Ex: 74125896398" required>
                                    <label for="account_number">Bank Account No <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide account number</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="account_holder_name" id="account_holder_name" 
                                           value="{{$data->account_holder_name}}" placeholder="Ex: John Doe" required>
                                    <label for="account_holder_name">Account Holder Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide account holder name</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label mb-2">Bank Passbook <span class="text-danger">*</span></label>
                                <div class="upload-area" id="passbook_upload" 
                                     onclick="document.getElementById('bank_passbook').click();">
                                    <input type="file" class="d-none upload-img" name="bank_passbook" 
                                           id="bank_passbook" accept="image/*,application/pdf"  
                                           onchange="previewImage(event, 'passbook_upload')" required>
                                            <button type="button" class="imgclose-btn" onclick="resetUpload('passbook_upload', 'passbook_upload'); event.stopPropagation();">&times;</button>
                                    <div class="upload-content">
                                        <i class="fas fa-passport"></i>
                                        <p>Upload passbook first page</p>
                                        <img id="passbook_preview" class="preview-img" src="{{ !$is_passbook_pdf ? $passbook_path : asset('EV/images/dummy.jpg') }}" style="{{ $is_passbook_pdf ? 'display:none;' : '' }}" alt="Passbook Preview" />
                                        <iframe class="preview-pdf" src="{{ $is_passbook_pdf ? $passbook_path : '' }}" style="{{ $is_passbook_pdf ? '' : 'display:none;' }}"></iframe>
                                    </div>
                                    
                                </div>
                                <div class="invalid-feedback">Please upload bank passbook</div>
                                <small class="text-muted">First page with account details</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label mb-2">Bank Statement Last Three Months (Optional)</label>
                                <div class="upload-area" id="statement_upload" onclick="document.getElementById('bank_statements').click();">
                                    <input type="file" class="d-none upload-img" name="bank_statements" 
                                           id="bank_statements" accept="image/*,application/pdf"  
                                           onchange="previewImage(event, 'statement_upload')">
                                           <button type="button" class="imgclose-btn" onclick="resetUpload('statement_upload', 'statement_upload'); event.stopPropagation();">&times;</button>
                                    <div class="upload-content">
                                        <i class="fas fa-passport"></i>
                                        <p>Click to upload</p>
                                        <img id="statement_preview" class="preview-img" src="{{ !$is_statement_pdf ? $statement_path : asset('EV/images/dummy.jpg') }}"  style="{{ $is_statement_pdf ? 'display:none;' : '' }}" alt="Bank Statement Preview" />
                                        <iframe class="preview-pdf" src="{{ $is_statement_pdf ? $statement_path : '' }}" style="{{ $is_statement_pdf ? '' : 'display:none;' }}"></iframe>
                                    </div>

                                </div>
                                <div class="invalid-feedback">Please upload bank passbook</div>
                                <small class="text-muted">First page with account details</small>
                            </div>

                        </div>
                        
                        <div class="wizard-buttons d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary previous">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary next">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 5: Personal Information -->
                    <div class="wizard-step d-none">
                        <div class="section-header">
                            <h4 class="text-primary mb-0"><i class="fas fa-user-tie me-2"></i> Personal Information</h4>
                            <p class="text-muted">Additional personal details of the candidate</p>
                        </div>
                        
                        <div class="row gy-4">
                             <div class="col-md-6 mb-4">
                                <label class="form-label mb-2">Profile Picture <span class="text-danger">*</span></label>
                                <div class="upload-area" id="profile_picturs_upload" 
                                     onclick="document.getElementById('profile_pic').click();">
                                    <input type="file" class="d-none upload-img" name="photo" id="profile_pic" 
                                           accept="image/*,application/pdf"  onchange="previewImage(event, 'profile_picturs_upload')" required>
                                           <button type="button" class="imgclose-btn" onclick="resetUpload('profile_picturs_upload', 'profile_picturs_upload'); event.stopPropagation();">&times;</button>
                                    <div class="upload-content">
                                        <i class="fas fa-user-circle"></i>
                                        <p>Click to upload profile photo</p>
                                        <img id="profile_preview" class="preview-img" src="{{ !$is_profile_pdf ? $profile_path : asset('EV/images/dummy.jpg') }}" style="{{ $is_profile_pdf ? 'display:none;' : '' }}" alt="Profile Preview" />
                                        <iframe src="{{ $is_profile_pdf ? $profile_path : '' }}"  style="{{ $is_profile_pdf ? '' : 'display:none;' }}" class="preview-pdf"></iframe>
                                        
                                    </div>
                                    <button type="button" class="imgclose-btn">&times;</button>
                                </div>
                                <div class="invalid-feedback">Please upload a profile picture</div>
                                <small class="text-muted">Recommended size: 500x500 pixels</small>
                            </div>
                        
                        </div>
                        
                        
                        <div class="row gy-4">
                            <!-- Date of Birth -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" 
                                           value="{{ \Carbon\Carbon::parse($data->date_of_birth)->format('Y-m-d') }}" required>
                                    <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please select valid date of birth</div>
                                </div>
                            </div>
                    
                            <!-- Present Address -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="present_address" id="present_address" 
                                           value="{{$data->present_address}}" placeholder="Enter Present Address" required>
                                    <label for="present_address">Present Address <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide present address</div>
                                </div>
                            </div>
                    
                            <!-- Permanent Address -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="permanent_address" id="permanent_address" 
                                           value="{{$data->permanent_address}}" placeholder="Enter Permanent Address" required>
                                    <label for="permanent_address">Permanent Address <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide permanent address</div>
                                </div>
                            </div>
                            
                           <!-- Rider ID -->
                            <div class="col-md-6" id="previous_company_id">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="emp_prev_company_id" id="emp_prev_company_id" 
                                           value="{{$data->emp_prev_company_id}}" >
                                    <label for="rider_id">Rider ID (Optional) <span class="text-danger"></span></label>
                                </div>
                            </div>
                    
                    
                            <!-- past experience -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="emp_prev_experience" id="emp_prev_experience" 
                                           value="{{$data->emp_prev_experience}}" >
                                    <label for="permanent_address">Past Experience (Optional) <span class="text-danger"></span></label>
                                </div>
                            </div>
                            
                            
                            <!-- Father's Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="father_name" id="father_name" 
                                           value="{{$data->father_name}}" placeholder="Enter Father's Name" required>
                                    <label for="father_name">Father/ Mother/ Guardian Name <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide guardian name</div>
                                </div>
                            </div>
                    
                            <!-- Father's Mobile Number -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="father_mobile_number" id="father_mobile_number" 
                                           oninput="sanitizeAndValidatePhone(this)" value="{{$data->father_mobile_number}}"
                                           placeholder="Enter Father's Mobile Number" required>
                                    <label for="father_mobile_number">Father/ Mother/ Guardian Contact No <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please provide valid mobile number</div>
                                </div>
                            </div>
                            
                            
                          <!--Reference Details -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="referal_person_name" id="referal_person_name" 
                                           value="{{$data->referal_person_name}}" 
                                           >
                                    <label for="father_mobile_number">Reference Name</label>
                                </div>
                            </div>
                            
                             <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="referal_person_number" id="referal_person_number" 
                                        oninput="sanitizeAndValidatePhone(this)"   value="{{$data->referal_person_number}}"  
                                           >
                                    <label for="father_mobile_number">Reference Contact No</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="referal_person_relationship" id="referal_person_relationship" 
                                           value="{{$data->referal_person_relationship}}" 
                                           >
                                    <label for="father_mobile_number">Relationship with Reference Person</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="social_links" id="social_links" 
                                           value="{{$data->social_links}}"
                                           >
                                    <label for="father_mobile_number">Social Media Link</label>
                                </div>
                            </div>
                    
                            <!-- Marital Status -->
                            <div class="col-md-6">
                                <label class="form-label mb-2">Marital Status <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="marital_status" 
                                               id="marital_status_yes" value="1" 
                                               {{ $data->marital_status == '1' ? 'checked' : '' }} 
                                               onchange="toggleSpouseFields()">
                                        <label class="form-check-label" for="marital_status_yes">Married</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="marital_status" 
                                               id="marital_status_no" value="0" 
                                               {{ $data->marital_status == '0' ? 'checked' : '' }}  
                                               onchange="toggleSpouseFields()">
                                        <label class="form-check-label" for="marital_status_no">Single</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Spouse Fields (Conditional) -->
                            <div class="col-md-12" id="spouse" style="display: {{ $data->marital_status == '1' ? 'block' : 'none' }};">
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="spouse_name" id="spouse_name" 
                                                   placeholder="Enter Spouse's Name" value="{{$data->spouse_name}}">
                                            <label for="spouse_name">Spouse's Name</label>
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" name="spouse_mobile_number" 
                                                   id="spouse_mobile_number" oninput="sanitizeAndValidatePhone(this)" 
                                                   placeholder="Enter Spouse's Mobile Number" value="{{$data->spouse_mobile_number}}">
                                            <label for="spouse_mobile_number">Spouse's Mobile Number</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="wizard-buttons d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary previous">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary next">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 6: Emergency Details -->
                    <div class="wizard-step d-none">
                        <div class="section-header">
                            <h4 class="text-primary mb-0"><i class="fas fa-ambulance me-2"></i> Emergency Details</h4>
                            <p class="text-muted">Emergency contact and medical information</p>
                        </div>
                        
                        <div class="row gy-4">
                            @php($bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                            
                            <!-- Blood Group -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="blood_group" id="blood_group" required>
                                        <option value="" selected disabled>Select Blood Group</option>
                                        @foreach ($bloodGroups as $bloodGroup)
                                            <option value="{{ $bloodGroup }}" {{ $data->blood_group == $bloodGroup ? 'selected' : '' }} >{{ $bloodGroup }}</option>
                                        @endforeach
                                    </select>
                                    <label for="blood_group">Blood Group <span class="text-danger">*</span></label>
                                    <div class="invalid-feedback">Please select blood group</div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact 1 -->
                            <div class="col-md-6" style="display:none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="emergency_contact_person_1_name" 
                                           id="emergency_contact_person_1_name" placeholder="Enter Name" value="{{$data->emergency_contact_person_1_name}}">
                                    <label for="emergency_contact_person_1_name">Emergency Contact Name</label>
                                    <div class="invalid-feedback">Please provide emergency contact name</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6" style="display:none;">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="emergency_contact_person_1_mobile" 
                                           id="emergency_contact_person_1_mobile" oninput="sanitizeAndValidatePhone(this)" 
                                           placeholder="Enter Mobile Number" value="{{$data->emergency_contact_person_1_mobile}}">
                                    <label for="emergency_contact_person_1_mobile">Emergency Contact Number</label>
                                    <div class="invalid-feedback">Please provide valid mobile number</div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact 2 -->
                            <div class="col-md-6" style="display:none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="emergency_contact_person_2_name" 
                                           id="emergency_contact_person_2_name" placeholder="Enter Name" value="{{$data->emergency_contact_person_2_name}}">
                                    <label for="emergency_contact_person_2_name">Secondary Emergency Contact Name</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6" style="display:none;">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" name="emergency_contact_person_2_mobile" 
                                           id="emergency_contact_person_2_mobile" oninput="sanitizeAndValidatePhone(this)" 
                                           placeholder="Enter Mobile Number" value="{{$data->emergency_contact_person_2_mobile}}">
                                    <label for="emergency_contact_person_2_mobile">Secondary Emergency Contact Number</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="wizard-buttons d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary previous">
                                <i class="fas fa-arrow-left me-2"></i> Previous
                            </button>
                            <button type="submit" class="btn btn-success final-submit">
                                <i class="fas fa-check-circle me-2"></i> Approve Application
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        
        document.addEventListener("DOMContentLoaded", function() {
            get_area('current_city_id');
                toggleLicenseLlr();
        });

    document.addEventListener('DOMContentLoaded', function () {
        
        const roleSelect = document.getElementById('role');
        const vehicleRiderRow = document.getElementById('vehicle_rider_row');
        const previousCompanyId = document.getElementById('previous_company_id');
    
        function toggleFields() {
            const roleValue = roleSelect.value;
    
            if (roleValue === 'deliveryman' || roleValue === 'adhoc' || roleValue === 'helper') {
                vehicleRiderRow.style.display = '';
                 previousCompanyId.style.display = '';
            } else {
                vehicleRiderRow.style.display = 'none';
                previousCompanyId.style.display = 'none';
            }
        }
    
        toggleFields(); // run on page load
        roleSelect.addEventListener('change', toggleFields);
        
        
        
        
        
        // Set max date for date of birth (18 years ago)
        let today = new Date();
        let minDate = new Date();
        minDate.setFullYear(today.getFullYear() - 18);
        let formattedDate = minDate.toISOString().split("T")[0];
        document.getElementById("date_of_birth").setAttribute("max", formattedDate);

        // Wizard functionality
        let currentStep = 1;
        const totalSteps = $('.wizard-step').length;

        function updateProgressBar() {
            let progressPercentage = (currentStep / totalSteps) * 100;
            $('#progressBar').css('width', progressPercentage + '%');
            $('#progressBar').attr('aria-valuenow', progressPercentage);
        }

        function showStep(step) {
            $('.wizard-step').removeClass('animate__fadeIn').addClass('d-none');
            $(`.wizard-step:nth-of-type(${step})`).removeClass('d-none').addClass('animate__animated animate__fadeIn');
            updateProgressBar();
            
            // Scroll to top of form
            $('html, body').animate({
                scrollTop: $('.card').offset().top - 20
            }, 300);
        }

        // Validation functions for each step
        function step_one() {
            let fields = [
                'first_name', 
                'last_name', 
                'mobile_number', 
                'current_city_id',
                'interested_city_id',
                'gender',
                'house_no',
                'street_name',
                'email_id',
                'pincode',
                'alternative_number',
                'role'
            ];
        
            let valid = true;
            const phoneRegex = /^\+91[0-9]{10}$/; 
             const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Simple valid email format
        
            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let floatingLabel = inputField.siblings('label');
                
                if (field.includes('mobile_number')) {
                    if (!phoneRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid'); 
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); 
                        floatingLabel.removeClass('text-danger');
                    }
                } 
                else if (field === 'email_id') {
                    // Email validation
                    if (!emailRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
        
                } 
                
                else if (field === 'current_city_id' || field === 'interested_city_id' || 
                          field === 'vehicle_type' || field === 'rider_type') {
                    if (inputField.val() === '' || inputField.val() === null) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid'); 
                        floatingLabel.removeClass('text-danger');
                    }
                } else {
                    if (inputField.val() === '') {
                        inputField.addClass('is-invalid'); 
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                }
            });
        
            const rolevalue = $('#role').val();
                if (rolevalue === 'in-house') {
                    $('#LicenseSection').hide();
                    // Skip license validation if in-house
                    $('#driving_license_front, #driving_license_back, #license_number').removeClass('is-invalid').removeAttr('required');
                } else {
                    $('#LicenseSection').show();
                    $('#driving_license_front, #driving_license_back, #license_number').attr('required', true);
                }
    
    
            return valid; 
        }



        // function step_two() {
        //     let valid = true;

        //     // Validate Aadhar and PAN - always required
        //     let requiredFields = ['aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'pan_card_front', 'pan_number'];

        //     requiredFields.forEach(function (field) {
        //         let inputField = $(`[name="${field}"]`);
                
        //         if (field === 'aadhar_number') {
        //             if (!aadharNumber(document.getElementById('aadhar_number')) || inputField.val() === '') {
        //                 inputField.addClass('is-invalid');
        //                 inputField.siblings('label').addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid');
        //                 inputField.siblings('label').removeClass('text-danger');
        //             }
        //         } else if (field === 'pan_number') {
        //             if (!validatePAN(document.getElementById('pan_number')) || inputField.val() === '') {
        //                 inputField.addClass('is-invalid');
        //                 inputField.siblings('label').addClass('text-danger');
        //                 valid = false;
        //             } else {
        //                 inputField.removeClass('is-invalid').addClass('is-valid');
        //                 inputField.siblings('label').removeClass('text-danger');
        //             }
        //         }         else {
        //             let uploadAreaId = '';
        //             if (field === 'aadhar_card_front') uploadAreaId = 'aadhar_upload_front';
        //             if (field === 'aadhar_card_back') uploadAreaId = 'aadhar_upload_back';
        //             if (field === 'pan_card_front') uploadAreaId = 'pan_upload_front';
        
        //             let uploadArea = $(`#${uploadAreaId}`);
        
        //             if (inputField[0].files.length === 0) {
        //                 uploadArea.addClass('upload-error').removeClass('upload-success');
        //                 valid = false;
        //             } else {
        //                 uploadArea.addClass('upload-success').removeClass('upload-error');
        //             }
        //         }
        //     });

        //     // Validate LLR if checkbox is checked
        //     if ($('#llr_checkbox').is(':checked')) {
        //         let llrImageField = $('[name="llr_image"]');
        //         let llrNumberField = $('[name="llr_number"]');
        //         let uploadArea = $('#llr_upload');
                
        //         if (llrImageField.val() === '' || llrImageField[0].files.length === 0) {
        //             uploadArea.addClass('upload-error').removeClass('upload-success');
        //             valid = false;
        //         } else {
        //             uploadArea.addClass('upload-success').removeClass('upload-error');
        //         }

        //         if (llrNumberField.val().trim() === '') {
        //             llrNumberField.addClass('is-invalid');
        //             llrNumberField.siblings('label').addClass('text-danger');
        //             valid = false;
        //         } else {
        //             llrNumberField.removeClass('is-invalid').addClass('is-valid');
        //             llrNumberField.siblings('label').removeClass('text-danger');
        //         }
        //     } else {
        //         // Validate License if checkbox is not checked
        //         let licenseFields = ['driving_license_front', 'driving_license_back', 'license_number'];

        //         licenseFields.forEach(function (field) {
        //             let inputField = $(`[name="${field}"]`);
                    
        //             if (field === 'license_number') {
        //                 if (!validateLicense() || inputField.val() === '') {
        //                     inputField.addClass('is-invalid');
        //                     inputField.siblings('label').addClass('text-danger');
        //                     valid = false;
        //                 } else {
        //                     inputField.removeClass('is-invalid').addClass('is-valid');
        //                     inputField.siblings('label').removeClass('text-danger');
        //                 }
        //             } else {
        //                 let uploadAreaId = field.replace('driving_', '').replace('_front', '_upload_front').replace('_back', '_upload_back');
        //                 let uploadArea = $(`#${uploadAreaId}`);
                        
        //                 if (inputField.val() === '' || inputField[0].files.length === 0) {
        //                     uploadArea.addClass('upload-error').removeClass('upload-success');
        //                     valid = false;
        //                 } else {
        //                     uploadArea.addClass('upload-success').removeClass('upload-error');
        //                 }
        //             }
        //         });
        //     }

        //     return valid;
        // }
        
        
function step_two() {
    let valid = true;
    
    // Validate Aadhar and PAN - always required
    let requiredFields = ['aadhar_card_front', 'aadhar_card_back', 'aadhar_number', 'pan_card_front', 'pan_number'];

    requiredFields.forEach(function (field) {
        let inputField = $(`[name="${field}"]`);
        
        if (field === 'aadhar_number') {
            if (!aadharNumber(document.getElementById('aadhar_number')) || inputField.val() === '') {
                inputField.addClass('is-invalid');
                inputField.siblings('label').addClass('text-danger');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
                inputField.siblings('label').removeClass('text-danger');
            }
        } else if (field === 'pan_number') {
            if (!validatePAN(document.getElementById('pan_number')) || inputField.val() === '') {
                inputField.addClass('is-invalid');
                inputField.siblings('label').addClass('text-danger');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
                inputField.siblings('label').removeClass('text-danger');
            }
        } else {
            let uploadAreaId = '';
            if (field === 'aadhar_card_front') uploadAreaId = 'aadhar_upload_front';
            if (field === 'aadhar_card_back') uploadAreaId = 'aadhar_upload_back';
            if (field === 'pan_card_front') uploadAreaId = 'pan_upload_front';

            let uploadArea = $(`#${uploadAreaId}`);
            
            // Check if there's an existing file path (not dummy.jpg)
            let hasExistingValue = uploadArea.find('.preview-img').attr('src') && 
                                 !uploadArea.find('.preview-img').attr('src').includes('dummy.jpg');
            
            // OR if there's a PDF preview with content
            hasExistingValue = hasExistingValue || 
                             (uploadArea.find('.preview-pdf').attr('src') && 
                              uploadArea.find('.preview-pdf').attr('src') !== '');
            
            // OR if there's a new file selected
            let hasNewFile = inputField[0].files.length > 0;
            
            if (!hasExistingValue && !hasNewFile) {
                uploadArea.addClass('upload-error').removeClass('upload-success');
                valid = false;
            } else {
                uploadArea.addClass('upload-success').removeClass('upload-error');
            }
        }
    });

    // Rest of your validation code...
    return valid;
}
// Validate LLR
function validateLLR(valid) {
    let llrImageField = $('[name="llr_image"]');
    let llrNumberField = $('[name="llr_number"]');
    let uploadArea = $('#llr_upload');
    
    // Check for existing value
    let hasExistingLLR = uploadArea.find('.preview-img').attr('src').indexOf('dummy.jpg') === -1 || 
                       uploadArea.find('.preview-pdf').attr('src') !== '';
    
    if (!hasExistingLLR && (llrImageField.val() === '' || llrImageField[0].files.length === 0)) {
        uploadArea.addClass('upload-error').removeClass('upload-success');
        valid = false;
    } else {
        uploadArea.addClass('upload-success').removeClass('upload-error');
    }

    if (llrNumberField.val().trim() === '') {
        llrNumberField.addClass('is-invalid');
        llrNumberField.siblings('label').addClass('text-danger');
        valid = false;
    } else {
        llrNumberField.removeClass('is-invalid').addClass('is-valid');
        llrNumberField.siblings('label').removeClass('text-danger');
    }
    return valid;
}

// Validate License
function validateLicenseDetails(valid) {
    let licenseFields = ['driving_license_front', 'driving_license_back', 'license_number'];

    licenseFields.forEach(function (field) {
        let inputField = $(`[name="${field}"]`);
        
        if (field === 'license_number') {
            if (!validateLicense() || inputField.val() === '') {
                inputField.addClass('is-invalid');
                inputField.siblings('label').addClass('text-danger');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
                inputField.siblings('label').removeClass('text-danger');
            }
        } else {
            let uploadAreaId = '';
            if (field === 'driving_license_front') uploadAreaId = 'license_upload_front';
            if (field === 'driving_license_back') uploadAreaId = 'license_upload_back';
            
            let uploadArea = $(`#${uploadAreaId}`);
            
            // Check for existing value
            let hasExistingLicense = uploadArea.find('.preview-img').attr('src').indexOf('dummy.jpg') === -1 || 
                                   uploadArea.find('.preview-pdf').attr('src') !== '';
            
            if (!hasExistingLicense && (inputField.val() === '' || inputField[0].files.length === 0)) {
                uploadArea.addClass('upload-error').removeClass('upload-success');
                valid = false;
            } else {
                uploadArea.addClass('upload-success').removeClass('upload-error');
            }
        }
    });

    return valid;
}
function step_three() {
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
        
        if (field === 'bank_passbook') {
            let uploadArea = $('#passbook_upload');
            
            // Check for existing value (not dummy.jpg)
            let hasExistingValue = uploadArea.find('.preview-img').attr('src') && 
                                 !uploadArea.find('.preview-img').attr('src').includes('dummy.jpg');
            
            // OR if there's a PDF preview with content
            hasExistingValue = hasExistingValue || 
                             (uploadArea.find('.preview-pdf').attr('src') && 
                              uploadArea.find('.preview-pdf').attr('src') !== '');
            
            // OR if there's a new file selected
            let hasNewFile = inputField[0].files.length > 0;
            
            if (!hasExistingValue && !hasNewFile) {
                uploadArea.addClass('upload-error').removeClass('upload-success');
                valid = false;
            } else {
                uploadArea.addClass('upload-success').removeClass('upload-error');
            }
        } else {
            if (inputField.val() === '') {
                inputField.addClass('is-invalid');
                inputField.siblings('label').addClass('text-danger');
                valid = false;
            } else {
                inputField.removeClass('is-invalid').addClass('is-valid');
                inputField.siblings('label').removeClass('text-danger');
                
                // Additional validation for IFSC code
                if (field === 'ifsc_code') {
                    const ifscRegex = /^[A-Za-z]{4}0[A-Za-z0-9]{6}$/;
                    if (!ifscRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid');
                        inputField.siblings('label').addClass('text-danger');
                        inputField.siblings('.invalid-feedback').text('Please enter a valid IFSC code');
                        valid = false;
                    }
                }
            }
        }
    });

    return valid;
}
        
        function step_four() {
            let fields = [
                'date_of_birth', 
                'present_address', 
                'permanent_address', 
                'father_name', 
                'father_mobile_number',
                'marital_status'
            ];
            let valid = true;
        
            const phoneRegex = /^\+91[0-9]{10}$/;
            const today = new Date();
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 18);

            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let floatingLabel = inputField.siblings('label');
                
                if (field.includes('mobile_number')) {
                    if (!phoneRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                } else if (field === 'date_of_birth') {
                    const dob = new Date(inputField.val());
                    if (inputField.val() === '' || dob > minDate) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        inputField.siblings('.invalid-feedback').text('Candidate must be at least 18 years old');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                } else if (field === 'marital_status') {
                    if (!$('input[name="marital_status"]:checked').val()) {
                        $('input[name="marital_status"]').addClass('is-invalid');
                        $('label[for="marital_status_yes"]').addClass('text-danger');
                        valid = false;
                    } else {
                        $('input[name="marital_status"]').removeClass('is-invalid');
                        $('label[for="marital_status_yes"]').removeClass('text-danger');
                    }
                } else {
                    if (inputField.val() === '') {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                }
            });
        
            return valid;
        }

        function step_five() {
            let fields = [
                'blood_group'
            ];
            let valid = true;
        
            const phoneRegex = /^\+91[0-9]{10}$/;
        
            fields.forEach(function (field) {
                let inputField = $(`[name="${field}"]`);
                let floatingLabel = inputField.siblings('label');
                
                if (field.includes('mobile')) {
                    if (!phoneRegex.test(inputField.val())) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                } else if (field === 'blood_group') {
                    if (inputField.val() === '' || inputField.val() === null) {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                } else {
                    if (inputField.val() === '') {
                        inputField.addClass('is-invalid');
                        floatingLabel.addClass('text-danger');
                        valid = false;
                    } else {
                        inputField.removeClass('is-invalid').addClass('is-valid');
                        floatingLabel.removeClass('text-danger');
                    }
                }
            });
        
            return valid;
        }

        // Check if any field in the current step is empty or invalid
        function isStepValid() {
            let valid = true;

            if (currentStep === 1 && !step_one()) {
                valid = false; 
            } else if (currentStep === 2 && !step_two()) {
                valid = false; 
            } else if (currentStep === 3 && !step_three()) {
                valid = false; 
            } else if (currentStep === 4 && !step_four()){
                valid = false;
            } else if (currentStep === 5 && !step_five()){
                valid = false;
            } 
            return valid;
        }
        
        // Handle next button click
        $('.next').on('click', function () {
            if (isStepValid()) {
                currentStep++;
                if (currentStep <= totalSteps) {
                    showStep(currentStep);
                }
            } else {
                // Shake animation for invalid form
                $(`.wizard-step:nth-of-type(${currentStep})`).addClass('animate__animated animate__shakeX');
                setTimeout(() => {
                    $(`.wizard-step:nth-of-type(${currentStep})`).removeClass('animate__shakeX');
                }, 1000);
            }
        });

        // Handle previous button click
        $('.previous').on('click', function () {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Form submission
        $('.final-submit').on('click', function (event) {
            event.preventDefault();
            
            if (step_five()) {
                var form = $('#wizardForm')[0]; 
                var formData = new FormData(form);
            
                var submitBtn = $('.final-submit');
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Updating...');
    
                $.ajax({
                    url: "{{route('admin.Green-Drive-Ev.hr_status.update_approve_candidate', $data->id)}}",
                    type: "POST",
                    data: formData,
                    processData: false,  
                    contentType: false,  
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Success animation
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Application Updated & Approved Successfully!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 3000, // 3 seconds
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                }).then(() => {
                                    // Redirect after 3 seconds
                                    window.location.href = '{{ route("admin.Green-Drive-Ev.hr_status.index") }}';
                                });
                        } else {
                            toastr.error(response.message);
                            submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Update Application');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) { 
                            $.each(xhr.responseJSON.errors, function(key, value) { 
                                toastr.error(value[0]); 
                            });
                        } else {
                            toastr.error("An error occurred. Please try again.");
                        }
                        submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Update Application');
                    }
                });
            } else {
                // Shake animation for invalid form
                $(`.wizard-step:nth-of-type(${currentStep})`).addClass('animate__animated animate__shakeX');
                setTimeout(() => {
                    $(`.wizard-step:nth-of-type(${currentStep})`).removeClass('animate__shakeX');
                }, 1000);
            }
        });

        // Initialize the wizard
        showStep(currentStep);
     
    });




    function previewImage(event, uploadAreaId) {
        const file = event.target.files[0];
        const uploadArea = document.getElementById(uploadAreaId);
        const imgPreview = uploadArea.querySelector('.preview-img');
        const pdfPreview = uploadArea.querySelector('.preview-pdf');
        const closeBtn = uploadArea.querySelector('.imgclose-btn');
    
        imgPreview.style.display = 'none';
        pdfPreview.style.display = 'none';
    
        if (file) {
            closeBtn.style.display = 'block'; // show close button
            if (file.type === 'application/pdf') {
                pdfPreview.src = URL.createObjectURL(file);
                pdfPreview.style.display = 'block';
            } else if (file.type.startsWith('image/')) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.style.display = 'block';
            }
        }
    }
    
    function resetUpload(inputId, uploadAreaId) {
        const input = document.getElementById(inputId);
        const uploadArea = document.getElementById(uploadAreaId);
        const imgPreview = uploadArea.querySelector('.preview-img');
        const pdfPreview = uploadArea.querySelector('.preview-pdf');
        const closeBtn = uploadArea.querySelector('.imgclose-btn');
    
        input.value = ''; // reset file input
        imgPreview.style.display = 'none';
        pdfPreview.style.display = 'none';
        
    if (imgPreview) {
        imgPreview.style.display = 'none';
        imgPreview.src = ''; // clear image source
    }
        pdfPreview.src = '';
        closeBtn.style.display = 'none';
    
        // Immediately re-open file dialog
        input.click();
        
        step_two();
    }



$(document).on('click', '.imgclose-btn', function() {
    const uploadArea = $(this).closest('.upload-area');
    const inputField = uploadArea.find('.upload-img');
    const previewImg = uploadArea.find('.preview-img');
    const previewPdf = uploadArea.find('.preview-pdf');
    const uploadIcon = uploadArea.find('.upload-content i');
    const uploadText = uploadArea.find('.upload-content p');
    
    inputField.val('');
    previewImg.attr('src', '').hide();
    previewPdf.attr('src', '').hide();
    uploadIcon.show();
    uploadText.show();
    $(this).hide();
    uploadArea.removeClass('active upload-success upload-error');
});

    // Mobile number validation
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
        
        // Validate and update UI
        const phoneRegex = /^\+91[0-9]{10}$/;
        if (phoneRegex.test(input.value)) {
            $(input).removeClass('is-invalid').addClass('is-valid');
            $(input).siblings('label').removeClass('text-danger');
        } else {
            $(input).removeClass('is-valid').addClass('is-invalid');
            $(input).siblings('label').addClass('text-danger');
        }
    }
    
    function sanitizeAndValidatePincode(input) {
    // Remove all non-digit characters
    input.value = input.value.replace(/\D/g, '');
    
    // Limit to 6 digits
    if (input.value.length > 6) {
        input.value = input.value.substring(0, 6);
    }
    
    // Regex for valid pincode (starts 1–9, 6 digits total)
    const pincodeRegex = /^[1-9][0-9]{5}$/;
    
    // Validate and update UI
    if (pincodeRegex.test(input.value)) {
        $(input).removeClass('is-invalid').addClass('is-valid');
        $(input).siblings('label').removeClass('text-danger');
    } else {
        $(input).removeClass('is-valid').addClass('is-invalid');
        $(input).siblings('label').addClass('text-danger');
    }
}



    // Aadhar number validation
    function aadharNumber(input) {
        // Remove any characters that are not digits
        var sanitizedInput = input.value.replace(/[^\d]/g, '');
    
        // Limit the input to 12 digits
        if (sanitizedInput.length > 12) {
            sanitizedInput = sanitizedInput.substring(0, 12);
        }
    
        // Format with spaces every 4 digits
        if (sanitizedInput.length > 0) {
            sanitizedInput = sanitizedInput.match(/.{1,4}/g).join(' ');
        }
    
        // Set the cleaned input back to the input field
        input.value = sanitizedInput;
    
        // Validate if the input is exactly 12 digits
        if (sanitizedInput.replace(/\s/g, '').length === 12) {
            $(input).removeClass('is-invalid').addClass('is-valid');
            $(input).siblings('label').removeClass('text-danger');
            return true;
        } else {
            $(input).removeClass('is-valid').addClass('is-invalid');
            $(input).siblings('label').addClass('text-danger');
            return false;
        }
    }
    
    // PAN validation
    function validatePAN() { 
        var input = document.getElementById('pan_number'); 
        var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/; 
    
        if (regpan.test(input.value)) {
            $(input).removeClass('is-invalid').addClass('is-valid');
            $(input).siblings('label').removeClass('text-danger');
            return true;
        } else {
            $(input).removeClass('is-valid').addClass('is-invalid');
            $(input).siblings('label').addClass('text-danger');
            return false;
        }
    }
    
    // Toggle referral fields
    function toggleReferralFields() {
        const jobSource = document.getElementById('apply_job_source').value;
        const referralFields = document.getElementById('referralFields');
        
        if (jobSource === 'Referral') {
            referralFields.style.display = 'block';
            $('#referal_person_name').attr('required', true);
            $('#referal_person_number').attr('required', true);
        } else {
            referralFields.style.display = 'none';
            $('#referal_person_name').removeAttr('required');
            $('#referal_person_number').removeAttr('required');
            $('#referal_person_name').val('').removeClass('is-valid is-invalid');
            $('#referal_person_number').val('').removeClass('is-valid is-invalid');
        }
    }
    
    // Toggle spouse fields
    function toggleSpouseFields() {
        const maritalStatusYes = document.getElementById('marital_status_yes').checked;
        const spouseFields = document.getElementById('spouse');
    
        if (maritalStatusYes) {
            spouseFields.style.display = 'block';
            $('#spouse_name').attr('required', true);
            $('#spouse_mobile_number').attr('required', true);
        } else {
            spouseFields.style.display = 'none';
            $('#spouse_name').removeAttr('required').val('').removeClass('is-valid is-invalid');
            $('#spouse_mobile_number').removeAttr('required').val('').removeClass('is-valid is-invalid');
        }
    }
    
       // License validation
    function validateLicense() {
        const input = document.getElementById('license_number');
        const value = input.value;
    
        // Define validation regex for license number (varies by country/state)
        const licensePattern = /^[A-Za-z]{2}[0-9]{2}[A-Za-z]{0,2}[0-9]{4}$/; // Example: DL-0420110142349
    
        if (licensePattern.test(value)) {
            $(input).removeClass('is-invalid').addClass('is-valid');
            $(input).siblings('label').removeClass('text-danger');
            return true;
        } else {
            $(input).removeClass('is-valid').addClass('is-invalid');
            $(input).siblings('label').addClass('text-danger');
            return false;
        }
    }
    
    // Toggle between license and LLR sections
    function toggleLicenseLlr() {
        const isLLR = document.getElementById('llr_checkbox').checked;
        document.getElementById('license_details_section').style.display = isLLR ? 'none' : 'block';
        document.getElementById('llr_details_section').style.display = isLLR ? 'block' : 'none';
        
        // Set required attributes
        if (isLLR) {
            // Clear and unrequire license fields
            $('#driving_license_front').val('').removeAttr('required');
            $('#driving_license_back').val('').removeAttr('required');
            $('#license_number').val('').removeAttr('required');
            
            // Require LLR fields
            $('#llr_image').attr('required', true);
            $('#llr_number').attr('required', true);
            
            // Reset license upload areas
            $('#license_upload_front').removeClass('upload-success upload-error active');
            $('#license_upload_back').removeClass('upload-success upload-error active');
            $('#license_upload_front .preview-img').attr('src', '').hide();
            $('#license_upload_back .preview-img').attr('src', '').hide();
            $('#license_upload_front .upload-content i, #license_upload_front .upload-content p').show();
            $('#license_upload_back .upload-content i, #license_upload_back .upload-content p').show();
            $('#license_upload_front .imgclose-btn, #license_upload_back .imgclose-btn').hide();
        } else {
            // Require license fields
            $('#driving_license_front').attr('required', true);
            $('#driving_license_back').attr('required', true);
            $('#license_number').attr('required', true);
            
            // Clear and unrequire LLR fields
            $('#llr_image').val('').removeAttr('required');
            $('#llr_number').val('').removeAttr('required');
            
            // Reset LLR upload area
            $('#llr_upload').removeClass('upload-success upload-error active');
            $('#llr_upload .preview-img').attr('src', '').hide();
            $('#llr_upload .preview-pdf').attr('src', '').hide();
            $('#llr_upload .upload-content i, #llr_upload .upload-content p').show();
            $('#llr_upload .imgclose-btn').hide();
        }
    }
                            
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    
    






    function get_area(id_name) {
        var id_name_value = $("#" + id_name).val(); // Get the selected city ID
        let formData = {
            id: id_name_value,
        };

        $.ajax({
            url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}',
            method: 'GET',
            data: formData,
            success: function(response) {
                if (response.status) {
                    // Populate the interested city dropdown
                    $("#interested_city_id").html(response.data);
                
                 var savedInterestedCityId = '{{ $data->interested_city_id }}';
                if (savedInterestedCityId) {
                    $("#interested_city_id").val(savedInterestedCityId);
                }
            
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors;
                if (errors) {
                    for (var key in errors) {
                        alert(errors[key].join(', '));
                    }
                }
            }
        });
    }



</script>
</x-app-layout>