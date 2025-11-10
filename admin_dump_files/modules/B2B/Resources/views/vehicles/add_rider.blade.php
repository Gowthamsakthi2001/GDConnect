@extends('layouts.b2b')

@section('css')
    <style>
        .content-body-b2b{
            font-family:'Manrope', sans-serif;
        }
        
        .file-preview-container {
            border-radius:8px;
            margin-top:8px;
            border: 2px dotted #ccc;
            padding: 0;
            height: 220px;
            width: 100%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
                .invalid-feedback {
            animation: shake 0.5s ease;
        }
        
        
        .form-step {
          display: none;
        }
        
        .form-step.active {
          display: block;
        }
        
        .lrr-modal {
          display: none;
        }
        
        .lrr-modal.active {
          display: block;
        }
      .TypeSection {
  display: block;
}
.TypeSection.inactive {
  display: none !important; /* override bootstrap flex */
}
        .clear-btn {
        padding: 8px 18px;  /* controls height & horizontal space */
        font-size: 16px;     /* bigger text */
        border-radius: 8px;  /* smoother corners */
        width: 80px;        /* fixed width (optional) */
    }
          .card-option {
    cursor: pointer;
    border-radius: 8px;
    display: block;
  }
  .card-option .card-body {
    border-radius: 8px;
    background-color: #fff;
    transition: all 0.2s ease-in-out;
    border: 1px solid transparent;
    min-height: 120px; /* adjust as needed */
  }
  .card-option input:checked + .card-body {
    border: 1px solid #0d6efd; /* highlight selected */
    box-shadow: 0 0 8px rgba(13,110,253,0.3);
  }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center" style="width:100%;">
        <!--<div class="col-12">-->
            <!--<div class="form-container">-->
                <!--<p class="form-title" style="font-size:16px;font-weight:600;">Add Rider</p>-->
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="form-title" style="font-size:16px;font-weight:600;">Add Rider</p>
                <button type="button" class="btn btn-outline-danger clear-btn" id="btnClear">Clear</button>
            </div>

                
                <form id="riderForm" enctype="multipart/form-data">
                    @csrf

                
                <div>
                    
                    <!-- Name & Mobile -->
                    <div class="form-step active">
                        <div class="row mb-4" id="SelectRider" style="display:none;">
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label  " style="font-size:14px; font-weight:500;">Riders<span style="color:red;">*</span></label>
                                <select class="form-select custom-select2-field" id="rider" name="rider">
                                    <option value="">Select Rider</option>

    
                                </select>
                            </div>
                        </div>
                    
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="assign_zone" class="form-label  " style="font-size:14px; font-weight:500;">Assign Zone <span style="color:red;">*</span></label>
                            @if($login_type == 'master')
                                <select class="form-select custom-select2-field" id="assign_zone" name="assign_zone">
                                    <option value="">Select Zone</option>
                                    @if(isset($zones))
                                        @foreach($zones as $val)
                                            <option value="{{ $val->id }}" {{ $val->id == $zone_id ? 'selected' : '' }}>{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            
                            @elseif($zone_id && $login_type == 'zone')
                                <input type="hidden" id="assign_zone" name="assign_zone" value="{{ $zone_id }}">
                                <select class="form-select custom-select2-field" disabled>
                                    <option value="">Select Zone</option>
                                    @if(isset($zones))
                                        @foreach($zones as $val)
                                            <option value="{{ $val->id }}" {{ $val->id == $zone_id ? 'selected' : '' }}>{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            
                            @else
                                <select class="form-select custom-select2-field" id="assign_zone" name="assign_zone">
                                    <option value="">Select Zone</option>
                                </select>
                            @endif
                             <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name" class="form-label  " style="font-size:14px; font-weight:500;">Name <span style="color:red;">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your Name">
                             <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label for="mobile" class="form-label  " style="font-size:14px; font-weight:500;">Mobile No <span style="color:red;">*</span></label>
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile No" oninput="sanitizeAndValidatePhone(this)">
                             <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="email" class="form-label" style="font-size:14px; font-weight:500;">Email ID</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email ID">
                            <div class="invalid-feedback"></div>
                        </div>
                         <div class="col-12 col-md-6">
                            <label for="dob" class="form-label" style="font-size:14px; font-weight:500;">DOB</label>
                            <div class="input-group">
                                <input type="date" class="form-control " id="dob" name="dob" placeholder="DD/MM/YYYY">
                                 <div class="invalid-feedback"></div>
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email & DOB -->
                    <!--<div class="row mb-4">-->
                       
                    <!--</div>-->
                    
                
                        
                        
                  
                    
                    <div class="row">
                      <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-next">Next</button>
                      </div>
                    </div>
                    </div>
                    
                    <div class="form-step">
                    <div class="row mb-4">
                        @php
                            $defaultImage = asset('b2b/img/file_upload_dummy.png');
                        @endphp
                                
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="aadhaar_front form-label">Aadhar Card Front</label>
                                            <div id="aadhaarFrontPreview" class="file-preview-container">
                                                @if(isset($data->aadhaar_front))
                                                    @if(pathinfo($data->aadhaar_front, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->aadhaar_front) }}" class="file-preview">
                                                       
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->aadhaar_front) }}" class="file-preview" alt="aadhaar front">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="aadhaar_front" id="aadhaar_front"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'aadhaarFrontPreview')">
                                            
                                        </div>
                                    </div>
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="aadhaar_back">Aadhar Card Back</label>
                                            <div id="aadhaarBackPreview" class="file-preview-container" >
                                                @if(isset($data->aadhaar_back))
                                                    @if(pathinfo($data->aadhaar_back, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->aadhaar_back) }}" class="file-preview">
                                                     
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->aadhaar_back) }}" class="file-preview" alt="aadhaar back">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="aadhaar_back" id="aadhaar_back"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'aadhaarBackPreview')">
                                         
                                        </div>
                                    </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="aadhaar_number" class="form-label" style="font-size:14px; font-weight:500;">Aadhaar Number<span style="color:red;">*</span></label>
                            <input type="text" class="form-control " id="aadhaar_number" name="aadhaar_number" placeholder="Enter Aadhaar Number" oninput="ValidateAdharNumber(this)">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                       
                    </div>
                    
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-danger btn-back ">Back</button>
                            <button type="button" class="btn btn-primary btn-next ">Next</button>
                        </div>
                    </div>
                    </div>
                    
                    <div class="form-step">
                    <div class="row mb-4">
                        @php
                            $defaultImage = asset('b2b/img/file_upload_dummy.png');
                        @endphp
                                
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="pan_front form-label">Pan Card Front</label>
                                            <div id="panFrontPreview" class="file-preview-container">
                                                @if(isset($data->pan_front))
                                                    @if(pathinfo($data->pan_front, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->pan_front) }}" class="file-preview">
                                                        <!--<p class="file-name">{{ basename($data->rc_attachment) }}</p>-->
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->pan_front) }}" class="file-preview" alt="pan front">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="pan_front" id="pan_front"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'panFrontPreview')">
                                            <!--@if(isset($data->rc_attachment))-->
                                            <!--    <small class="text-muted current-file">Current file: {{ basename($data->rc_attachment) }}</small>-->
                                            <!--@endif-->
                                        </div>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="pan_back">Pan Card Back</label>
                                            <div id="panBackPreview" class="file-preview-container" >
                                                @if(isset($data->pan_back))
                                                    @if(pathinfo($data->pan_back, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->pan_back) }}" class="file-preview">
                                                        <!--<p class="file-name">{{ basename($data->rc_attachment) }}</p>-->
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->pan_back) }}" class="file-preview" alt="pan back">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="pan_back" id="pan_back"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'panBackPreview')">
                                            <!--@if(isset($data->rc_attachment))-->
                                            <!--    <small class="text-muted current-file">Current file: {{ basename($data->rc_attachment) }}</small>-->
                                            <!--@endif-->
                                        </div>
                                    </div>
                    </div>
                    
                                     <div class="row mb-4">
                                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                                            <label for="pan_number" class="form-label" style="font-size:14px; font-weight:500;">PAN Number</label>
                                            <input type="text" class="form-control" id="pan_number" name="pan_number" placeholder="Enter PAN Number" oninput="validatePAN(this)">
                                            <small id="pan_error" style="color:red; display:none;">Invalid PAN Number. Example: ABCDE1234F</small>
                                        </div>
                                    </div>

                    
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-danger btn-back ">Back</button>
                            <button type="button" class="btn btn-primary btn-next ">Next</button>
                        </div>
                    </div>
                    </div>
                    
                    <div class="form-step driving-form">
                    <div class="row mb-4">
                        @php
                            $defaultImage = asset('b2b/img/file_upload_dummy.png');
                        @endphp
                                
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="driving_front form-label">Driving Licence Front</label>
                                            <div id="drivingFrontPreview" class="file-preview-container">
                                                @if(isset($data->driving_front))
                                                    @if(pathinfo($data->driving_front, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->driving_front) }}" class="file-preview">
                                                       
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->driving_front) }}" class="file-preview" alt="RC Attachment">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="driving_front" id="driving_front"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'drivingFrontPreview')">
                                             <div class="invalid-feedback"></div>
                                            
                                        </div>
                                    </div>
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="driving_back">Driving Licence Back</label>
                                            <div id="drivingBackPreview" class="file-preview-container" >
                                                @if(isset($data->driving_back))
                                                    @if(pathinfo($data->driving_back, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->driving_back) }}" class="file-preview">
                                                        <!--<p class="file-name">{{ basename($data->driving_back) }}</p>-->
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->driving_back) }}" class="file-preview" alt="driving back">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="driving_back" id="driving_back"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'drivingBackPreview')">
                                                    <div class="invalid-feedback"></div>
                                            
                                        </div>
                                    </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="driving_licence_number"  class="form-label  " style="font-size:14px; font-weight:500;">Driving Licence Number<span style="color:red;">*</span></label>
                            <input type="text" class="form-control " id="driving_licence_number" name="driving_licence_number" placeholder="Enter Driving Licence Number">
                             <div class="invalid-feedback"></div>
                        </div>
                        
                         <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="driving_licence_expiry_date"  class="form-label  " style="font-size:14px; font-weight:500;">Driving Licence Expiry Date<span style="color:red;">*</span></label>
                            <input type="date" class="form-control " id="driving_license_expiry_date" name="driving_license_expiry_date" min="{{ date('Y-m-d') }}">
                             <div class="invalid-feedback"></div>
                        </div>
                       
                    </div>
                    
                    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <div>
                                <a href="javascript:void(0);" class="text-decoration-none btn-next" id="no_driving_licence" style="font-size:14px;font-weight:500">Don't Have Driving Licence</a>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-back me-2">Back</button>
                                <button type="button" class="btn btn-primary btn-submit" id="submitbtn1">Submit</button> 
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                    <div class="form-step" >
                    <div class="row mb-4">
                        @php
                            $defaultImage = asset('b2b/img/file_upload_dummy.png');
                        @endphp
                                
                         <div class="col-6 mt-2">
                                        <div class="form-group ">
                                            <label for="llr form-label">LLR</label>
                                            <div id="llrImagePreview" class="file-preview-container">
                                                @if(isset($data->llr_image))
                                                    @if(pathinfo($data->llr_image, PATHINFO_EXTENSION) === 'pdf')
                                                        <embed src="{{ asset('storage/'.$data->llr_image) }}" class="file-preview">
                                                       
                                                    @else
                                                        <img src="{{ asset('storage/'.$data->llr_image) }}" class="file-preview" alt="llr image">
                                                    @endif
                                                @else
                                                    <img src="{{ $defaultImage }}" class="file-preview" alt="Preview">
                                                @endif
                                            </div>
                                            <input type="file" class="form-control bg-white mt-2" name="llr_image" id="llr_image"  data-default="{{ $defaultImage }}"
                                                   accept="image/png,image/jpeg,image/jpg,application/pdf" 
                                                   onchange="showImagePreview(this, 'llrImagePreview')">
                                            <div class="invalid-feedback"></div>
                                            
                                        </div>
                                    </div>
                         
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="llr_number" class="form-label  " style="font-size:14px; font-weight:500;">LLR Number<span style="color:red;">*</span></label>
                            <input type="text" class="form-control " id="llr_number" name="llr_number" placeholder="Enter LLR Number">
                            <div class="invalid-feedback"></div>
                        </div>
                       
                    </div>
                    
                    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <div>
                                <a href="javascript:void(0);" class="text-decoration-none btn-next" style="font-size:14px;font-weight:500">Don't Have LLR</a>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-back me-2 ">Back</button>
                                <button type="button" class="btn btn-primary btn-submit" id="submitbtn2">Submit</button> 
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="form-step" >
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="terms_condition" class="form-label  " style="font-size:14px; font-weight:500;">Terms and Conditions for Rider Responsibility</label>
                           
                        </div>
                       
                    </div>
                    
                    <div class="p-2 border rounded" style="font-size:13px; color:#555; max-height:250px; overflow-y:auto;">
                        <!-- Replace this text with your actual terms -->
                        <div class="card-body p-3 text-start">

        
                            <p>
                                By registering as a rider on our platform, I acknowledge and confirm the following:
                            </p>
        
                            <ul class="ms-3">
                                <li>I do <strong>not</strong> possess a valid driving license or Learner's License (LLR).</li>
                                <li>I understand that operating a vehicle without a valid license is against the law.</li>
                                <li>I take <strong>full responsibility</strong> for any accidents, damages, or legal consequences that may arise while performing deliveries.</li>
                                <li>I agree to indemnify and hold harmless the company, its employees, and partners from any claims, losses, or liabilities resulting from my actions.</li>
                                <li>I confirm that I will follow all safety guidelines and traffic rules to the best of my ability.</li>
                            </ul>
        
                            <p class="mt-3">
                                By continuing, I acknowledge that I have read and understood these terms and accept the responsibility
                                for all outcomes arising from my participation on the platform.
                            </p>
                        </div>
                    </div>
                    
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" value="1" name="terms">
                                    <label class="form-check-label" for="agreeTerms" style="font-size:14px;">
                                        I have read and agree to the Terms and Conditions
                                    </label>
                                </div>
                            </div>
                        </div>
    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-back me-2">Back</button>
                                <button type="button" class="btn btn-primary btn-submit " id="submitbtn3">Submit & Send Email</button> 
                        </div>
                    </div>
                </div>
                
                
                </div>
                </form>
            </div>
      
      
    </div>
<!--</div>-->





@endsection

@section('js')
<script>
let currentStep = 0;
  
function resetWizard() {
        // Reset form
        const form = document.getElementById('riderForm');
        if (form) form.reset();
    
        // Reset wizard to first step
        const steps = document.querySelectorAll('.form-step');
        steps.forEach(step => step.classList.remove('active'));
        if (steps[0]) steps[0].classList.add('active');
        currentStep = 0;
        
        
        // Reset all file previews (images and PDFs)
        const fileInputs = form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            const previewIdMatch = input.getAttribute('onchange').match(/'(.*?)'/);
            const previewId = previewIdMatch ? previewIdMatch[1] : null;
            const previewContainer = previewId ? document.getElementById(previewId) : null;
            const defaultImage = input.getAttribute('data-default');
    
            if (previewContainer && defaultImage) {
                // Clear the current content (including <img> or <embed>)
                previewContainer.innerHTML = `
                    <img src="${defaultImage}" class="file-preview" alt="Preview">
                `;
            }
        });
        
        

    // Reset Driving License Expiry Date
    const dlExpiry = document.getElementById('driving_license_expiry_date');
    if (dlExpiry) {
        dlExpiry.value = "";
    }
        
}
    
document.addEventListener("DOMContentLoaded", function () {
  // Wizard Navigation
  
  

  
        let today = new Date();
        let minDate = new Date();
        minDate.setFullYear(today.getFullYear() - 18);
        let formattedDate = minDate.toISOString().split("T")[0];
        document.getElementById("dob").setAttribute("max", formattedDate);



     const form = document.getElementById('riderForm');
  const steps = document.querySelectorAll(".form-step");

  
  
  // Validation for Step 1
    async function validateStep1() {
        let valid = true;
        const assign_zone = document.getElementById('assign_zone');
        const name = document.getElementById('name');
        const mobile = document.getElementById('mobile');
        const dob = document.getElementById('dob');
        const email = document.getElementById('email');
    
        // Helper to set error
        function setError(field, message) {
            field.classList.add('is-invalid');
            let feedback = field.parentElement.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.innerText = message;
            }
            valid = false;
        }
        
        function clearError(field) {
            field.classList.remove('is-invalid');
            let feedback = field.parentElement.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.innerText = "";
            }
        }

        // Required field check
        function checkRequired(field, message) {
            if (!field.value.trim()) {
                setError(field, message);
            } else {
                clearError(field);
            }
        }
    
        checkRequired(assign_zone, "Assign Zone is required");
        
     
        // Validate name
        checkRequired(name, "Name is required");
    
        // Validate mobile
        // checkRequired(mobile, "Mobile number is required");
        
        // Validate mobile
        let cleanedMobile = "";
        let originalMobile = mobile.value.trim();  // keep exact input
        if (!originalMobile) {
            setError(mobile, "Mobile number is required");
            valid = false;
        } else {
            // remove spaces only for validation
            cleanedMobile = originalMobile.replace(/\s+/g, '');
        
            // Normalize for validation only (do NOT send this to backend)
            let normalized = cleanedMobile;
            if (normalized.startsWith("+91")) {
                normalized = normalized.slice(3);
            } else if (normalized.startsWith("91") && normalized.length > 10) {
                normalized = normalized.slice(2);
            }
        
            const mobilePattern = /^[0-9]{10}$/;
            if (!mobilePattern.test(normalized)) {
                setError(mobile, "Mobile number must be exactly 10 digits (excluding country code)");
                valid = false;
            } else {
                clearError(mobile);
            }
            
            

        }

    

        
        
        if (dob.value.trim() !== "") {   // only validate if user enters DOB
            const dobDate = new Date(dob.value);
            const today = new Date();
        
            if (dobDate >= today) {
                setError(dob, "DOB must be a past date");
            } else {
                // calculate age
                let age = today.getFullYear() - dobDate.getFullYear();
                const m = today.getMonth() - dobDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
        
                if (age < 18) {
                    setError(dob, "Age must be 18 years or above");
                } else {
                    clearError(dob);
                }
            }
        } else {
            clearError(dob); 
        }



    
        // Email (optional but must be valid if entered)
        if (email.value.trim() !== "") {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value.trim())) {
                setError(email, "Please enter a valid email address");
            } else {
                clearError(email);
            }
        } else {
            clearError(email); // optional and empty is fine
        }
    

 
    
    
        return valid;
    }



    // Step 2 validation
    function validateStep2() {
        let valid = true;
    
        const aadhaarNumber = document.getElementById('aadhaar_number');
        const aadhaarFront = document.getElementById('aadhaar_front');
        const aadhaarBack = document.getElementById('aadhaar_back');
    
        // Helper to set error
        function setError(field, message) {
            field.classList.add('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = message;
            valid = false;
        }
    
        // Helper to clear error
        function clearError(field) {
            field.classList.remove('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = "";
        }
    
        // Aadhaar number check (must be 12 digits only)
        if (!aadhaarNumber.value.trim()) {
            setError(aadhaarNumber, "Aadhaar number is required");
        } else if (!/^\d{12}$/.test(aadhaarNumber.value.trim())) {
            setError(aadhaarNumber, "Aadhaar number must be 12 digits");
        } else {
            clearError(aadhaarNumber);
        }
    
        // Aadhaar front image required
        // if (!aadhaarFront.value) {
        //     setError(aadhaarFront, "Aadhaar front image is required");
        // } else {
        //     clearError(aadhaarFront);
        // }
    
        // // Aadhaar back image required
        // if (!aadhaarBack.value) {
        //     setError(aadhaarBack, "Aadhaar back image is required");
        // } else {
        //     clearError(aadhaarBack);
        // }
    
        return valid;
    }




    // Step 3 validation
    function validateStep3() {
        let valid = true;
    
        const panNumber = document.getElementById('pan_number');
        const panFront = document.getElementById('pan_front');
        const panBack = document.getElementById('pan_back');
        const panError = document.getElementById('pan_error');
    
        // Clear previous invalid states
        // [panNumber].forEach(el => el.classList.remove('is-invalid'));
        panError.style.display = 'none';
    
        // Validate PAN number format
        // const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        // if(!panNumber.value.trim() || !panRegex.test(panNumber.value.trim().toUpperCase())) {
        //     // panNumber.classList.add('is-invalid');
        //     panError.style.display = 'block';
        //     valid = false;
        // }
        
        const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        const panValue = panNumber.value.trim().toUpperCase();
        
        if (panValue !== '') { 
            // Validate only if not empty
            if (!panRegex.test(panValue)) {
                panError.style.display = 'block';
                valid = false;
            } else {
                panError.style.display = 'none';
            }
        } else {
            // Hide error if empty
            panError.style.display = 'none';
        }

    
        // Validate PAN front file
        // if(!panFront.value) {
        //     panFront.classList.add('is-invalid');
        //     valid = false;
        // }
    
        // // Validate PAN back file
        // if(!panBack.value) {
        //     panBack.classList.add('is-invalid');
        //     valid = false;
        // }
    
        return valid;
    }
    
    


  // Next step
  document.querySelectorAll(".btn-next").forEach(btn => {
   btn.addEventListener("click", async () => {
        
        
        if (currentStep === 0) {
    
        const valid = await validateStep1();
            if (!valid) return;
        }
        
                // Only validate Step 1
        if(currentStep === 0 && !validateStep1()) {
            return; // stop going to next step if validation fails
        }


        // Step 2 validation
        if(currentStep === 1 && !validateStep2()) return;
        
        
         // Step 3 validation
        if(currentStep === 2 && !validateStep3()) return;
        
        

        

      if (steps[currentStep]) {
        steps[currentStep].classList.remove("active");
      }
      currentStep++;
      if (steps[currentStep]) {
        steps[currentStep].classList.add("active");
      }
    });
  });

  // Back step
  document.querySelectorAll(".btn-back").forEach(btn => {
    btn.addEventListener("click", () => {
      if (steps[currentStep]) {
        steps[currentStep].classList.remove("active");
      }
      currentStep--;
      if (steps[currentStep]) {
        steps[currentStep].classList.add("active");
      }
    });
  });

  // File Preview
  window.showImagePreview = function (input, previewId) {
    const previewContainer = document.getElementById(previewId);
    const defaultSrc = input.getAttribute("data-default");
    previewContainer.innerHTML = ""; // clear previous

    if (input.files && input.files[0]) {
      const file = input.files[0];
      const reader = new FileReader();

      reader.onload = function (e) {
        if (file.type === "application/pdf") {
          const embed = document.createElement("embed");
          embed.src = e.target.result;
          embed.classList.add("file-preview");
          previewContainer.appendChild(embed);
        } else {
          const img = document.createElement("img");
          img.src = e.target.result;
          img.classList.add("file-preview");
          previewContainer.appendChild(img);
        }
      };
      reader.readAsDataURL(file);
    } else {
      const img = document.createElement("img");
      img.src = defaultSrc;
      img.classList.add("file-preview");
      previewContainer.appendChild(img);
    }
  };
  
  
  
  



    document.getElementById("btnClear").addEventListener("click", function () {
        resetWizard();
    });

    // Common AJAX Submit
    function ajaxSubmit(button , type){
        const form = document.getElementById("riderForm");
        const disabledFields = form.querySelectorAll(':disabled');
        disabledFields.forEach(f => f.disabled = false);
        
        const formData = new FormData(form);
        
        // restore disabled fields
        disabledFields.forEach(f => f.disabled = true);

        button.disabled = true;
        formData.append('submission_type', type); // Add the type: driving, llr, or terms
        const originalText = button.innerHTML;
        button.innerHTML = "Submitting...";

            fetch("{{ route('b2b.store_rider') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                button.disabled = false;
                button.innerHTML = originalText;
                
                    const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    
    
            if(res.ok && data.success){
                // toastr.success(data.message);
                // setTimeout(()=>location.reload(), 1500);
                
                 Toast.fire({
                    icon: 'success',
                    title: data.message
                })

              // Reset form
              resetWizard();
               

        
            } else if(res.status === 422){
                    for(let k in data.errors){
                        Toast.fire({
                            icon: 'error',
                            title: data.errors[k][0]
                        });
                    }
            } else {
                 Toast.fire({
                    icon: 'error',
                    title: data.message || "Try again"
                });
            }
        })
        .catch(err => { button.disabled=false; button.innerHTML=originalText; 
        
                Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "Server error: " + err.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    }


    // Function to clear file previews
    function clearPreview(previewId, defaultSrc){
        const container = document.getElementById(previewId);
        container.innerHTML = `<img src="${defaultSrc}" class="file-preview" alt="Preview">`;
    }


    // Driving Validation
       function validateDriving() {
        let valid = true;
    
        const drivingFront = document.getElementById('driving_front');
        const drivingBack = document.getElementById('driving_back');
        const drivingNumber = document.getElementById('driving_licence_number');
        const dl_expiry_date = document.getElementById('driving_license_expiry_date');
        // Helper to set error
        function setError(field, message) {
            field.classList.add('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = message;
            valid = false;
        }
    
        // Helper to clear error
        function clearError(field) {
            field.classList.remove('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = "";
        }
        const dlPattern = /^[A-Z]{2}\d{13}$/; 
    
        // Driving License Number
        // if (!drivingNumber.value.trim()) {
        //     setError(drivingNumber, "Driving License Number is required");
        // } else {
        //     clearError(drivingNumber);
        // }
        
        if (!drivingNumber.value.trim()) {
            setError(drivingNumber, "Driving License Number is required");
        } 
        // else if (!dlPattern.test(drivingNumber.value.trim())) {
        //     setError(
        //         drivingNumber, 
        //         "Invalid Driving License Number. Example: TN1020201234567"
        //     );
        // } 
        else {
            clearError(drivingNumber);
        }
        
        if (!dl_expiry_date.value.trim()) {
            setError(dl_expiry_date, "Driving License Expiry Date is required");
        } else {
            clearError(dl_expiry_date);
        }
        
 
        // Driving License Front
        // if (!drivingFront.value) {
        //     setError(drivingFront, "Driving License Front is required");
        // } else {
        //     clearError(drivingFront);
        // }
    
        // // Driving License Back
        // if (!drivingBack.value) {
        //     setError(drivingBack, "Driving License Back is required");
        // } else {
        //     clearError(drivingBack);
        // }
        
    
        return valid;
    }


    // LLR Validation
    function validateLLR() {
        let valid = true;
    
        const llrImage = document.getElementById('llr_image');
        const llrNumber = document.getElementById('llr_number');
    
        // Helper to set error
        function setError(field, message) {
            field.classList.add('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = message;
            valid = false;
        }
    
        // Helper to clear error
        function clearError(field) {
            field.classList.remove('is-invalid');
            const feedback = field.parentElement.querySelector(".invalid-feedback");
            if (feedback) feedback.innerText = "";
        }
    
        // LLR Number
        if (!llrNumber.value.trim()) {
            setError(llrNumber, "LLR Number is required");
        } else {
            clearError(llrNumber);
        }
    
   
        // LLR Image
        // if (!llrImage.value) {
        //     setError(llrImage, "LLR Image is required");
        // } else {
        //     clearError(llrImage);
        // }
    
    
        return valid;
    }


    // Terms Validation
    function validateTerms(){
        let valid = true;
        const terms = document.getElementById('agreeTerms');
        terms.classList.remove('is-invalid');
        if(!terms.checked){ terms.classList.add('is-invalid'); toastr.error("You must agree to Terms."); valid=false; }
        return valid;
    }

    // Submit Buttons
    document.getElementById('submitbtn1').addEventListener('click', function(){
        // Clear LLR & Terms
        document.getElementById('llr_image').value='';
        document.getElementById('llr_number').value='';
        document.getElementById('agreeTerms').checked=false;
        clearPreview('llrImagePreview', document.getElementById('llr_image').dataset.default);
        if(validateDriving()) ajaxSubmit(this , 'license');
    });

    document.getElementById('submitbtn2').addEventListener('click', function(){
        // Clear Driving & Terms
        document.getElementById('driving_front').value='';
        document.getElementById('driving_back').value='';
        document.getElementById('driving_licence_number').value='';
         document.getElementById('driving_license_expiry_date').value='';
        document.getElementById('agreeTerms').checked=false;
            clearPreview('drivingFrontPreview', document.getElementById('driving_front').dataset.default);
    clearPreview('drivingBackPreview', document.getElementById('driving_back').dataset.default);
        if(validateLLR()) ajaxSubmit(this , 'llr');
    });

    document.getElementById('submitbtn3').addEventListener('click', function(){
        // Clear Driving & LLR
        document.getElementById('driving_front').value='';
        document.getElementById('driving_back').value='';
        document.getElementById('driving_licence_number').value='';
        document.getElementById('driving_license_expiry_date').value='';
        document.getElementById('llr_image').value='';
        document.getElementById('llr_number').value='';
        
            clearPreview('drivingFrontPreview', document.getElementById('driving_front').dataset.default);
        clearPreview('drivingBackPreview', document.getElementById('driving_back').dataset.default);
        clearPreview('llrImagePreview', document.getElementById('llr_image').dataset.default);
        if(validateTerms()) ajaxSubmit(this , 'terms');
    });
    
    
 
  
});

    
</script>

<script>
   
    
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
    
    
    
  
    // document.addEventListener('DOMContentLoaded', function () {
    //     const form = document.querySelector('form');
    //     const submitBtn = form.querySelector('button[type="submit"]');
    //     const originalText = submitBtn.innerHTML;
    
    //     form.addEventListener('submit', function (e) {
    //         e.preventDefault();
    
    //         const formData = new FormData(form);
    //         submitBtn.disabled = true;
    //         submitBtn.innerHTML = "Submitting...";
    
    //         fetch("{{ route('b2b.store_rider') }}", {
    //             method: "POST",
    //             headers: {
    //                 "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
    //             },
    //             body: formData
    //         })
    //         .then(async (response) => {
    //             const data = await response.json();
    //             submitBtn.disabled = false;
    //             submitBtn.innerHTML = originalText;
    
    //             if (response.ok && data.success) {
    //                 toastr.success(data.message);
    //                 setTimeout(() => {
    //                   location.reload();
    //                 }, 1500);
    //             } else if (response.status === 422) {
    //                 // Validation errors
    //                 const errors = data.errors;
    //                 for (let key in errors) {
    //                     toastr.error(errors[key][0]);
    //                 }
    //             } else {
    //                 toastr.error(data.message || "Please try again.");
    //             }
    //         })
    //         .catch((error) => {
    //             submitBtn.disabled = false;
    //             submitBtn.innerHTML = originalText;
    //             toastr.error("Server error: " + error.message);
    //         });
    //     });
    // });
    
    function ValidateAdharNumber(input) {
    // Remove any non-numeric characters
    input.value = input.value.replace(/\D/g, '');

    // Limit to 12 digits
    if (input.value.length > 12) {
        input.value = input.value.slice(0, 12);
    }
}



function validatePAN(input) {
    // Convert input to uppercase
    input.value = input.value.toUpperCase();

    const panRegex = /^[A-Z]{0,5}[0-9]{0,4}[A-Z]{0,1}$/; // Partial matching for typing
    const fullPanRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;   // Full PAN validation
    const errorMsg = document.getElementById('pan_error');

    // Remove any invalid characters immediately
    input.value = input.value.replace(/[^A-Z0-9]/g, '');

    // Trim to 10 characters max
    if(input.value.length > 10) {
        input.value = input.value.slice(0, 10);
    }

    // Show error if full PAN length reached and invalid
    if(input.value.length === 10) {
        if(!fullPanRegex.test(input.value)) {
            errorMsg.style.display = 'block';
        } else {
            errorMsg.style.display = 'none';
        }
    } else {
        // Hide error while typing less than 10 characters
        errorMsg.style.display = 'none';
    }
}

</script>
@endsection