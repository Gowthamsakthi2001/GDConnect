@extends('layouts.b2b')

@section('css')
<!--<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">-->
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
        
    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center" style="width:100%;">
        <!--<div class="col-12">-->
            <!--<div class="form-container">-->
                <p class="form-title" style="font-size:16px;font-weight:600;">Add Vehicle</p>
                
                <form action="#" method="POST">
                    @csrf
                    
                    <!-- Name & Mobile -->
                    <div class="form-step active">
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="name" class="form-label required-field" style="font-size:14px; font-weight:500;">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Your Name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="mobile" class="form-label required-field" style="font-size:14px; font-weight:500;">Mobile No</label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile No" required>
                        </div>
                    </div>
                    
                    <!-- Email & DOB -->
                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="email" class="form-label" style="font-size:14px; font-weight:500;">Email ID</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email ID">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="dob" class="form-label" style="font-size:14px; font-weight:500;">DOB</label>
                            <div class="input-group">
                                <input type="date" class="form-control " id="dob" name="dob" placeholder="DD/MM/YYYY">
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vehicle Duration -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="duration" class="form-label required-field" style="font-size:14px; font-weight:500;">
                                Vehicle Duration
                            </label>
                            <select class="form-select" id="duration" name="duration" required>
                                <option value="" selected disabled>Select Duration</option>
                                <option value="day">1 Day</option>
                                <option value="week">1 Week</option>
                                <option value="month">1 Month</option>
                                <option value="year">1 Year</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    
                        <!-- Start & End Date Section -->
                        <div class="col-6" id="customDates" style="display: none;">
                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label required-field" style="font-size:14px; font-weight:500;">
                                        Start Date
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="start_date" name="start_date" placeholder="DD/MM/YYYY">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label required-field" style="font-size:14px; font-weight:500;">
                                        End Date
                                    </label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="end_date" name="end_date" placeholder="DD/MM/YYYY">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
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
                            <label for="aadhaar_number" class="form-label required-field" style="font-size:14px; font-weight:500;">Aadhaar Number</label>
                            <input type="text" class="form-control " id="aadhaar_number" name="aadhaar_number" placeholder="Enter Aadhaar Number" required>
                        </div>
                       
                    </div>
                    
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <button type="submit" class="btn btn-danger btn-back ">Back</button>
                            <button type="submit" class="btn btn-primary btn-next ">Next</button>
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
                            <label for="pan_number" class="form-label required-field" style="font-size:14px; font-weight:500;">Pan Number</label>
                            <input type="text" class="form-control " id="pan_number" name="pan_number" placeholder="Enter Pan Number" required>
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
                                            
                                        </div>
                                    </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="driving_licence_number"  class="form-label required-field" style="font-size:14px; font-weight:500;">Driving Licence Number</label>
                            <input type="text" class="form-control " id="driving_licence_number" name="driving_licence_number" placeholder="Enter Driving Licence Number" required>
                        </div>
                       
                    </div>
                    
                    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <div>
                                <a href="#" class="text-decoration-none btn-next" id="no_driving_licence" style="font-size:14px;font-weight:500">Don't Have Driving Licence</a>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-back me-2">Back</button>
                                <button type="submit" class="btn btn-primary btn-submit ">Submit</button> 
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
                                            
                                        </div>
                                    </div>
                         
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="llr_number" class="form-label required-field" style="font-size:14px; font-weight:500;">LLR Number</label>
                            <input type="text" class="form-control " id="llr_number" name="llr_number" placeholder="Enter LLR Number" required>
                        </div>
                       
                    </div>
                    
                    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                            <div>
                                <a href="#" class="text-decoration-none btn-next" style="font-size:14px;font-weight:500">Don't Have LLR</a>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger btn-back me-2 ">Back</button>
                                <button type="submit" class="btn btn-primary btn-submit ">Submit</button> 
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="form-step" >
                    
                    <div class="row mb-4">
                        <div class="col-12 col-md-12 mb-3 mb-md-0">
                            <label for="terms_condition" class="form-label required-field" style="font-size:14px; font-weight:500;">Terms and Conditions</label>
                           
                        </div>
                       
                    </div>
                    
                    <div class="p-2 border rounded" style="font-size:13px; color:#555; max-height:150px; overflow-y:auto;">
                        <!-- Replace this text with your actual terms -->
                        By proceeding, you agree to abide by the rules and regulations. 
                        Please ensure you read all terms carefully before accepting.
                    </div>
                    
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms" style="font-size:14px;">
                                        I have read and agree to the Terms and Conditions
                                    </label>
                                </div>
                            </div>
                        </div>
    
                    <!-- Button -->
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger btn-back me-2 ">Back</button>
                                <button type="submit" class="btn btn-primary btn-submit ">Submit & Send Email</button> 
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
document.addEventListener("DOMContentLoaded", function () {
  // Wizard Navigation
  const steps = document.querySelectorAll(".form-step");
  let currentStep = 0;

  // Next step
  document.querySelectorAll(".btn-next").forEach(btn => {
    btn.addEventListener("click", () => {
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
});
</script>

<script>
    document.getElementById("duration").addEventListener("change", function () {
        let customDates = document.getElementById("customDates");
        if (this.value === "custom") {
            customDates.style.display = "block";
        } else {
            customDates.style.display = "none";
        }
    });
</script>

@endsection