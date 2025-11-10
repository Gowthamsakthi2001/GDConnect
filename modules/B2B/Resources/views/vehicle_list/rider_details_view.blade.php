@extends('layouts.b2b')
@section('css')

<style>
    
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
</style>
@endsection

@section('content')
<div class="main-content">

        <!-- Header Section -->
        <div class="mb-4">
            <div class="p-3 rounded" style="background:#fbfbfb;">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <!-- Title -->
                    <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1.5rem);">
                        View Rider Details
                    </h5>
        
                    <!-- Back Button -->
                    <a href="{{ route('b2b.vehiclelist') }}" 
                       class="btn btn-dark btn-md mt-2 mt-md-0">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>


        <!-- Card Section -->
        <div class="shadow-sm">
            <div class="card-body">
                
                <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Name</label>
                                <input type="text" class="form-control bg-white" name="name" id="name" value="{{$data->rider->name ?? ''}}" placeholder="Enter Name" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_number">Mobile Number</label>
                                <input type="text" class="form-control bg-white" name="mobile_number" id="mobile_number" value="{{$data->rider->mobile_no ?? ''}}" readonly placeholder="Enter Mobile Number">
                            </div>
                        </div>
                </div>
                
                <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Email ID</label>
                                <input type="text" class="form-control bg-white" name="email" id="email" value="{{$data->rider->email ?? ''}}" placeholder="Enter Email ID" readonly>
                            </div>
                        </div>

                </div>
                
                
                
                <div class="row g-3">
                    <!-- Aadhaar Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Aadhaar Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->adhar_front))
                                @php
                                    $frontExtension = pathinfo($data->rider->adhar_front, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/aadhar_images/'.$data->rider->adhar_front);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Aadhaar Front"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                    <embed src="{{ $frontPath }}"
                                           type="application/pdf"
                                           class="file-preview"
                                           style="width:100%; height:200px;" />
                                           
                                      <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ $frontPath }}')">
                                        </div>
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Aadhaar Front"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                
                    <!-- Aadhaar Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Aadhaar Card Back</label>
                        <div class="file-preview-container">
                        @if(!empty($data->rider->adhar_back))
                            @php
                                $backExtension = pathinfo($data->rider->adhar_back, PATHINFO_EXTENSION);
                                $backPath = asset('b2b/aadhar_images/'.$data->rider->adhar_back);
                            @endphp
            
                            @if(in_array(strtolower($backExtension), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $backPath }}"
                                     class="img-fluid rounded"
                                     alt="Aadhaar Back"
                                     onclick="OpenImageModal('{{ $backPath }}')">
                            @elseif(strtolower($backExtension) === 'pdf')
                                <embed src="{{ $backPath }}"
                                       type="application/pdf"
                                       class="file-preview"
                                       style="width:100%; height:200px;" />
                                       
                                    <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ $backPath }}')">
                                        </div>
                            @endif
                        @else
                            <img src="{{ asset('b2b/img/default_image.png') }}"
                                 class="img-fluid rounded"
                                 alt="Aadhaar Back"
                                 onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                </div>


                <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="adhar">Adhar Card Number</label>
                                <input type="text" class="form-control bg-white" name="adhar_number" id="adhar_number" value="{{$data->rider->adhar_number ?? ''}}" placeholder="Enter Adhar Number" readonly>
                            </div>
                        </div>

                </div>
                
                
                <div class="row g-3">
                    <!-- Pan Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->pan_front))
                                @php
                                    $panFrontExtension = pathinfo($data->rider->pan_front, PATHINFO_EXTENSION);
                                    $panFrontPath = asset('b2b/pan_images/'.$data->rider->pan_front);
                                @endphp
                
                                @if(in_array(strtolower($panFrontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panFrontPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Front"
                                         onclick="OpenImageModal('{{ $panFrontPath }}')">
                                @elseif(strtolower($panFrontExtension) === 'pdf')
                                    <embed src="{{ $panFrontPath }}"
                                           type="application/pdf"
                                           class="file-preview"
                                           style="width:100%; height:200px;" />
                                           
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ $panFrontPath }}')">
                                        </div>
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="PAN Front"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                        </div>
                    </div>
                
                    <!-- Pan Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Back</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->pan_back))
                                @php
                                    $panBackExtension = pathinfo($data->rider->pan_back, PATHINFO_EXTENSION);
                                    $panBackPath = asset('b2b/pan_images/'.$data->rider->pan_back);
                                @endphp
                
                                @if(in_array(strtolower($panBackExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panBackPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Back"
                                         onclick="OpenImageModal('{{ $panBackPath }}')">
                                @elseif(strtolower($panBackExtension) === 'pdf')
                                    <embed src="{{ $panBackPath }}"
                                           type="application/pdf"
                                           class="file-preview"
                                           style="width:100%; height:200px;" />
                                           
                                    <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ $panBackPath }}')">
                                        </div>
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="PAN Back"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                        </div>
                    </div>
                </div>
                
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="pan">Pan Card Number</label>
                                <input type="text" class="form-control bg-white" name="pan_number" value="{{$data->rider->pan_number ?? ''}}" id="pan_number" placeholder="Enter Pan Number" readonly>
                            </div>
                        </div>

                </div>
                
            
                @if(!empty($data->rider->driving_license_number))
                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Front</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->driving_license_front ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                        
                        @if(pathinfo($data->rider->driving_license_front, PATHINFO_EXTENSION) === 'pdf')
                            <embed src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}"
                                   class="file-preview"
                                   type="application/pdf"
                                   width="100%" height="200px">
                                   
                             <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}')">
                                        </div>
                        @else
                            <img src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}"
                                 class="img-fluid rounded"
                                 alt="Driving License Front"
                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}')">
                        @endif
                         @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License" onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                
                    <!-- Driving License Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Back</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->driving_license_back ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                            @if(pathinfo($data->rider->driving_license_back, PATHINFO_EXTENSION) === 'pdf')
                                <embed src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}"
                                       class="file-preview"
                                       type="application/pdf"
                                       width="100%" height="200px">
                                       
                             <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}')">
                                        </div>
                            @else
                                <img src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}"
                                     class="img-fluid rounded"
                                     alt="Driving License Back"
                                     onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}')">
                            @endif
                        @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License" onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                </div>
                
                
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Number</label>
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$data->rider->driving_license_number ?? ''}}" placeholder="Enter License Number" readonly>
                            </div>
                        </div>
                </div>
            
            @elseif(!empty($data->rider->llr_number))

                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LLR</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->llr_image ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                            @if(pathinfo($data->rider->llr_image, PATHINFO_EXTENSION) === 'pdf')
                                <embed src="{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}"
                                       class="file-preview"
                                       type="application/pdf"
                                       width="100%" height="200px">
                                       
                             <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}')">
                                        </div>
                            @else
                                <img src="{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}"
                                     class="img-fluid rounded"
                                     alt="LLR Image"
                                     onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}')">
                            @endif
                        @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License" onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                
                </div>
                
                
                
            
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">LLR Number</label>
                                <input type="text" class="form-control bg-white" name="llr_number" id="llr_number" value="{{ $data->rider->llr_number ?? '' }}" placeholder="Enter LLR Number" readonly>
                            </div>
                        </div>
                </div>

                
                
            @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body p-4 text-center">
                    
                                @if(!empty($data->rider->terms_condition) && $data->rider->terms_condition == 1)
                                    <!-- Checkbox (Accepted) -->
                                    <div class="form-check d-flex align-items-center mb-3">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input me-2" 
                                            checked 
                                            disabled
                                            id="termsAccepted"
                                        >
                                        <label class="form-check-label fw-semibold" for="termsAccepted">
                                            Terms & Conditions Accepted
                                        </label>
                                    </div>
                    
                                    <!-- Terms & Conditions Content -->
                                    <div class="card-body p-3 text-start">
                                        <h4 class="custom-dark mb-3">
                                            Terms and Conditions for Rider Responsibility
                                        </h4>
                    
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
                                @else
                                    <!-- If not accepted -->
                                    <p class="mt-3 mb-0 text-muted">
                                        The rider has not accepted the terms and conditions yet.
                                    </p>
                                @endif
                    
                            </div>
                        </div>
                    </div>
            @endif


            </div>
        </div>
        
         @include('b2b::action_popup_modal') 
        

</div>

    

@endsection

@section('js')

<script>
let scale = 1;
let rotation = 0;
let currentFileUrl = '';
let currentType = ''; 

function OpenImageModal(fileUrl) {
    currentFileUrl = fileUrl;
    const isPDF = fileUrl.toLowerCase().endsWith('.pdf');

    scale = 1;
    rotation = 0;
    updateImageTransform();

    if (isPDF) {
        $("#kyc_image").hide();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").hide(); // Hide image tools for PDF
        $("#kyc_pdf").attr("src", fileUrl).show();
        currentType = 'pdf';
    } else {
        $("#kyc_pdf").hide();
        $("#kyc_image").attr("src", fileUrl).show();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").show();
        currentType = 'image';
    }

    $("#downloadBtn").off("click").on("click", function () {
        const link = document.createElement("a");
        link.href = currentFileUrl;
        link.download = currentFileUrl.split('/').pop();
        link.click();
    });

    $("#BKYC_Verify_view_modal").modal("show");
}

function zoomIn() {
    if (currentType !== 'image') return;
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (currentType !== 'image') return;
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    if (currentType !== 'image') return;
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    if (img) {
        img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
    }
}
    </script>



@endsection
