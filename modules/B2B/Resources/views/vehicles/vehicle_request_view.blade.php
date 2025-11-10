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
        
         .attachment-preview {
    border: 1px dashed #ccc;
    border-radius: 8px;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    position: relative;
    background-color: #fdfdfd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.attachment-preview:hover {
    border-color: #007bff;
    background-color: #f9f9f9;
}

.preview-image {
    width: 80%;
    height: 80%;
    /*object-fit: contain;*/
    cursor: pointer;
    transition: transform 0.2s;
    border-radius: 4px;
}

.preview-image:hover {
    transform: scale(1.02);
}

.preview-pdf {
    width: 100%;
    height: 100%;
    border: none;
}

.d-none {
    display: none !important;
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
                        View Request Details
                    </h5>
        
                    <!-- Back Button -->
                    <a href="{{ route('b2b.vehicle_request.vehicle_request_list') }}" 
                       class="btn btn-dark btn-md mt-2 mt-md-0">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

            <div class="row mt-2">
                <div class="col-md-6 mb-2" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border : #005D27 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#005D27">
                                Opened
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                               {{ \Carbon\Carbon::parse($request->created_at)->format('d M y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($request))
                @if($request->status == 'completed')
                <div class="col-md-6 mb-2" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border:#A61D1D 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#A61D1D;">
                                Closed  
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                                 {{ \Carbon\Carbon::parse($request->completed_at)->format('d M y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
                
                
            </div>
            
         <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="riderTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="rider-info-tab" data-bs-toggle="tab" data-bs-target="#rider-info" type="button" role="tab" aria-controls="rider-info" aria-selected="true">
                Rider Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="kyc-info-tab" data-bs-toggle="tab" data-bs-target="#kyc-info" type="button" role="tab" aria-controls="kyc-info" aria-selected="false">
                KYC Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="deployed-info-tab" data-bs-toggle="tab" data-bs-target="#deployed-info" type="button" role="tab" aria-controls="deployed-info" aria-selected="false">
                Deployed Info
            </button>
        </li>
        @if(!empty($vehicle))
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vehicle-info-tab" data-bs-toggle="tab" data-bs-target="#vehicle-info" type="button" role="tab" aria-controls="vehicle-info" aria-selected="false">
                Vehicle Info
            </button>
        </li>
        @endif
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="riderTabContent">

        <!-- Rider Info Tab -->
        <div class="tab-pane fade show active" id="rider-info" role="tabpanel" aria-labelledby="rider-info-tab">
            <div class="shadow-sm card p-3">
                <div class="row">
                     <div class="col-md-6 mb-3"> <!-- Updated by Gowtham.S-->
                        <label class="form-label">Zone Name</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->rider->zone->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->rider->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile No</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->rider->mobile_no ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email ID</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->rider->email ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">DOB</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->rider->dob ?? 'N/A' }}" readonly>
                    </div>
                </div>

            </div>
        </div>
        
        
                <!-- KYC Info Tab -->
        <div class="tab-pane fade" id="kyc-info" role="tabpanel" aria-labelledby="kyc-info-tab">
            <div class="shadow-sm card p-3">
                
                                <div class="row g-3">
                    <!-- Aadhaar Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Aadhaar Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($request->rider->adhar_front))
                                @php
                                    $frontExtension = pathinfo($request->rider->adhar_front, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/aadhar_images/'.$request->rider->adhar_front);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Aadhaar Front"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
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
                        @if(!empty($request->rider->adhar_back))
                            @php
                                $backExtension = pathinfo($request->rider->adhar_back, PATHINFO_EXTENSION);
                                $backPath = asset('b2b/aadhar_images/'.$request->rider->adhar_back);
                            @endphp
            
                            @if(in_array(strtolower($backExtension), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $backPath }}"
                                     class="img-fluid rounded"
                                     alt="Aadhaar Back"
                                     onclick="OpenImageModal('{{ $backPath }}')">
                            @elseif(strtolower($backExtension) === 'pdf')
                                       
                                 <iframe src="{{ $backPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
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
                                <input type="text" class="form-control bg-white" name="adhar_number" id="adhar_number" value="{{$request->rider->adhar_number ?? 'N/A'}}" placeholder="Enter Adhar Number" readonly>
                            </div>
                        </div>

                </div>
                
                
                <div class="row g-3">
                    <!-- Pan Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($request->rider->pan_front))
                                @php
                                    $panFrontExtension = pathinfo($request->rider->pan_front, PATHINFO_EXTENSION);
                                    $panFrontPath = asset('b2b/pan_images/'.$request->rider->pan_front);
                                @endphp
                
                                @if(in_array(strtolower($panFrontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panFrontPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Front"
                                         onclick="OpenImageModal('{{ $panFrontPath }}')">
                                @elseif(strtolower($panFrontExtension) === 'pdf')
                                           
                                <iframe src="{{ $panFrontPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
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
                            @if(!empty($request->rider->pan_back))
                                @php
                                    $panBackExtension = pathinfo($request->rider->pan_back, PATHINFO_EXTENSION);
                                    $panBackPath = asset('b2b/pan_images/'.$request->rider->pan_back);
                                @endphp
                
                                @if(in_array(strtolower($panBackExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panBackPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Back"
                                         onclick="OpenImageModal('{{ $panBackPath }}')">
                                @elseif(strtolower($panBackExtension) === 'pdf')
                                <iframe src="{{ $panBackPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
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
                                <input type="text" class="form-control bg-white" name="pan_number" id="pan_number" value="{{$request->rider->pan_number ?? 'N/A'}}" placeholder="Enter Pan Number" readonly>
                            </div>
                        </div>

                </div>
                
            
                @if(!empty($request->rider->driving_license_number))
                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Front</label>
                        <div class="file-preview-container">
                        @php
                            $file = $request->rider->driving_license_front ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                        @if(pathinfo($request->rider->driving_license_front, PATHINFO_EXTENSION) === 'pdf')
                                 
                            <iframe src="{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_front) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_front) }}')">
                                    </div>
                            
                        @else
                            <img src="{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_front) }}"
                                 class="img-fluid rounded"
                                 alt="Driving License Front"
                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_front) }}')">
                        @endif
                            @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License">
                        @endif
                        </div>
                    </div>
                
                    <!-- Driving License Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Back</label>
                        <div class="file-preview-container">
                        @php
                            $file = $request->rider->driving_license_back ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($request->rider->driving_license_back, PATHINFO_EXTENSION) === 'pdf')
                                       
                                <iframe src="{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_back) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                               <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_back) }}')">
                                    </div>
                            @else
                                <img src="{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_back) }}"
                                     class="img-fluid rounded"
                                     alt="Driving License Back"
                                     onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$request->rider->driving_license_back) }}')">
                            @endif
                                @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License">
                        @endif
                        </div>
                    </div>
                </div>
                
                
                 <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Number</label>
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$request->rider->driving_license_number ?? 'N/A'}}" placeholder="Enter License Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Expiry Date</label>
                                <input type="text" class="form-control bg-white"  value="{{$request->rider->dl_expiry_date ?? 'N/A'}}" readonly>
                            </div>
                        </div>
                </div>
            
            @elseif(!empty($request->rider->llr_number))

                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LLR</label>
                        <div class="file-preview-container">
                        @php
                            $file = $request->rider->llr_image ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($request->rider->llr_image, PATHINFO_EXTENSION) === 'pdf')

                                       
                         <iframe src="{{ asset('b2b/llr_images/'.$request->rider->llr_image) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                               <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$request->rider->llr_image) }}')">
                                    </div>
                            @else
                                <img src="{{ asset('b2b/llr_images/'.$request->rider->llr_image) }}"
                                     class="img-fluid rounded"
                                     alt="LLR Image"
                                     onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$request->rider->llr_image) }}')">
                            @endif
                            
                                @else
                                {{-- Show default image when no file --}}
                                <img src="{{ $defaultImage }}"
                                     class="img-fluid rounded"
                                     alt="Default Driving License">
                            @endif
                        </div>
                    </div>
                
                </div>
                
                
                
            
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">LLR Number</label>
                                <input type="text" class="form-control bg-white" name="llr_number" id="llr_number" value="{{ $request->rider->llr_number ?? 'N/A' }}" placeholder="Enter LLR Number" readonly>
                            </div>
                        </div>
                </div>

                
                
            @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body p-4 text-center">
                    
                                @if(!empty($request->rider->terms_condition) && $request->rider->terms_condition == 1)
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
        
        <div class="tab-pane fade" id="deployed-info" role="tabpanel" aria-labelledby="deployed-info-tab">
            <div class="shadow-sm card p-3">
                <div class="row">
                     <div class="col-md-6 mb-3"> <!-- Updated by Gowtham.S-->
                        <label class="form-label">Vehicle Type</label>
                        <input type="text" class="form-control bg-white" value="{{ $request->vehicle_type_relation->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Battery Type</label>
                        <input type="text" class="form-control bg-white" value="{{ ($request->battery_type == '1' ) ?'Self-Charging':'Portable' ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control bg-white" value="{{ $request->start_date ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control bg-white" value="{{ $request->end_date ?? 'N/A' }}" readonly>
                    </div>
                   
                    @if(isset($request))
                    @if($request->status == 'completed')
                    
                    @if($vehicle->closed_by)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Closed By</label>
                        <input type="text" class="form-control bg-white" value="{{ $vehicle->closed_by->name ?? 'N/A' }}" readonly>
                    </div>
                    @endif
                    <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="odometer_value">Odometer Value <span style="color:red;">*</span></label>
                        <input type="number" class="form-control bg-white" name="odometer_value" id="odometer_value" value="{{$request->assignment->odometer_value ?? 'N/A'}}" readonly>
                    </div>
                </div>
                
                
                <div class="col-md-6 mb-3">
                        <label class="form-label">Odometer Image</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->odometer_image))
                                @php
                                    $frontExtension = pathinfo($request->assignment->odometer_image, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/odometer_images/'.$request->assignment->odometer_image);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                        
                                  <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>
                                           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Front</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_front))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_front, PATHINFO_EXTENSION);
                                    $VehiclefrontPath = asset('b2b/vehicle_front/'.$request->assignment->vehicle_front);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $VehiclefrontPath }}"
                                         class="img-fluid rounded"
                                         alt="Vehicle Front"
                                         onclick="OpenImageModal('{{ $VehiclefrontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $VehiclefrontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $VehiclefrontPath }}')">
                                    </div>         
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Vehicle Front"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Back</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_back))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_back, PATHINFO_EXTENSION);
                                    $vehiclebackPath = asset('b2b/vehicle_back/'.$request->assignment->vehicle_back);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $vehiclebackPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $vehiclebackPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $vehiclebackPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                        
                                   <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $vehiclebackPath }}')">
                                    </div>       
                                           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Top</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_top))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_top, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_top/'.$request->assignment->vehicle_top);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                        
                                   <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>   
                                           
                                           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Bottom</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_bottom))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_bottom, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_bottom/'.$request->assignment->vehicle_bottom);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                   <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>          
                                           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Right</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_right))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_right, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_right/'.$request->assignment->vehicle_right);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                     <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>            
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Left</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_left))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_left, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_left/'.$request->assignment->vehicle_left);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                 <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Battery</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_battery))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_battery, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_battery/'.$request->assignment->vehicle_battery);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                 <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>        
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Charger</label>
                        <div class="file-preview-container">
                            @if(!empty($request->assignment->vehicle_charger))
                                @php
                                    $frontExtension = pathinfo($request->assignment->vehicle_charger, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/vehicle_charger/'.$request->assignment->vehicle_charger);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Odometer Image"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                  <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $frontPath }}')">
                                    </div>       
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Odometer Image"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                @endif
                @endif    
                </div>

            </div>
        </div>
        
        @if(!empty($vehicle))
        <div class="tab-pane fade" id="vehicle-info" role="tabpanel" aria-labelledby="vehicle-info-tab">
        <div class="shadow-sm card p-3">
               
                <div class="card-body">
                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id" value="{{$request->assignment->asset_vehicle_id ?? 'N/A'}}"  readonly>
                            </div>
                        </div>
                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_number">Vehicle No</label>
                                <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{$request->assignment->vehicle->permanent_reg_number ?? 'N/A'}}"  placeholder="Vehicle NO" readonly>
                            </div>
                        </div>



                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number</label>
                                <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{$request->assignment->vehicle->chassis_number ?? 'N/A'}}" placeholder="Chassis Number" readonly>
                            </div>
                        </div>
                        
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="imei_number">IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="imei_number" id="imei_number" value="{{$request->assignment->vehicle->telematics_imei_number ?? 'N/A'}}" placeholder="IMEI Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number" value="{{$request->assignment->vehicle->motor_number ?? 'N/A'}}"  placeholder="Engine Number/Motor Number" readonly>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city">City</label>
                                <!--<input type="text" class="form-control bg-white" name="city" id="city"  placeholder="City">-->
                             <input type="text" class="form-control bg-white" name="city_id" id="city_id" value="{{$request->city->city_name ?? 'N/A'}}"  placeholder="city_id" readonly>
                            
                                
                            </div>
                        </div>
                        
                        
                        <!--<div class="col-md-6 mb-3">-->
                        <!--        <div class="form-group">-->
                        <!--            <label class="input-label mb-2 ms-1" for="hub">HUB</label>-->
                        <!--            <input type="text" class="form-control bg-white" name="hub" id="hub" style="padding:12px 20px;"  placeholder="HUB">-->
                        <!--        </div>-->
                        <!--</div>-->
                        
                        
                                                
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <input type="text" class="form-control bg-white" value="{{ $request->assignment->vehicle->vehicle_type_relation->name ?? 'N/A' }}" readonly> 
                            </div>
                        </div>
                        
                        
                      <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="model">Model</label>
                                    <input type="text" class="form-control bg-white" name="model" id="model" value="{{$request->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'}}" placeholder="Model" readonly>
                                </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="color">Color</label>
                                    <input type="text" class="form-control bg-white" name="color" id="color"  placeholder="Color" value="{{$request->assignment->vehicle->color_relation->name ?? 'N/A'}}" readonly>
                                </div>
                        </div>
                       
                       <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                    <input type="date" class="form-control bg-white" name="vehicle_delivery_date" value="{{$request->assignment->vehicle->vehicle_delivery_date ?? 'N/A'}}" id="vehicle_delivery_date" readonly>
                                </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                                <input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="{{$vehicle->vehicleStatus ?? 'N/A' }}" readonly>
                                
                            </div>
                        </div> 
                        
                                <?php
                        
                                $RCAttachment = $request->assignment->vehicle->reg_certificate_attachment ?? '';
                                $defaultImage = asset('b2b/img/default_image.png');
                                $RCFilePath = !empty($RCAttachment)
                                    ? asset("EV/asset_master/reg_certificate_attachments/{$RCAttachment}")
                                    : '';
                            
                                $isRCPDF = $RCFilePath && \Illuminate\Support\Str::endsWith($RCAttachment, '.pdf');
                                $RCImageSrc = (!$RCFilePath || $isRCPDF) ? $defaultImage : $RCFilePath;
                            ?>
                        
                        
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Registration Certificate Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center"  >
                                        <!-- Image Preview -->
                                        <img id="Rc_Image"
                                             src="{{ $RCImageSrc }}"
                                             alt="Registration Certificate Attachment"
                                             class="preview-image {{ $isRCPDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $RCImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="Rc_PDF"
                                                src="{{ $isRCPDF ? $RCFilePath : '' }}"
                                                class="preview-pdf {{ !$isRCPDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                              @if($isRCPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $RCFilePath }}')">
                                            </div>
                                        @endif
                                                
                                                
                                    </div>
                                </div>

                            </div>
                            
                            
                        <?php
                        
                                $insuranceAttachment = $request->assignment->vehicle->insurance_attachment ?? '';
                                $defaultImage = asset('b2b/img/default_image.png');
                                $insuranceFilePath = !empty($insuranceAttachment)
                                    ? asset("EV/asset_master/insurance_attachments/{$insuranceAttachment}")
                                    : '';
                            
                                $isInsurancePDF = $insuranceFilePath && \Illuminate\Support\Str::endsWith($insuranceAttachment, '.pdf');
                                $insuranceImageSrc = (!$insuranceFilePath || $isInsurancePDF) ? $defaultImage : $insuranceFilePath;
                            ?>
                        
                        
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Insurance Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center"  > 
                                        <!-- Image Preview -->
                                        <img id="insurance_Image"
                                             src="{{ $insuranceImageSrc }}"
                                             alt="Insurance Attachment"
                                             class="preview-image {{ $isInsurancePDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $insuranceImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="insurance_PDF"
                                                src="{{ $isInsurancePDF ? $insuranceFilePath : '' }}"
                                                class="preview-pdf {{ !$isInsurancePDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                          @if($isInsurancePDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $insuranceFilePath }}')">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        
                        
                        @php
                            $hsrpAttachment = $request->assignment->vehicle->hsrp_copy_attachment ?? '';
                            $defaultImage = asset('b2b/img/default_image.png');
                            $hsrpFilePath = $hsrpAttachment
                                ? asset("EV/asset_master/hsrp_certificate_attachments/{$hsrpAttachment}")
                                : '';
                            $isHsrpPDF = $hsrpAttachment && \Illuminate\Support\Str::endsWith($hsrpAttachment, '.pdf');
                            $hsrpImageSrc = (!$hsrpFilePath || $isHsrpPDF) ? $defaultImage : $hsrpFilePath;
                        @endphp
                        
                        <div class="col-md-12 mb-4 my-4">
                            <label class="mb-2">HSRP Copy Attachment</label>
                            <div class="attachment-preview">
                                <div class="col-12 text-center"  >
                                    <!-- Image Preview -->
                                    <img id="hsrp_certificate_Image"
                                         src="{{ $hsrpImageSrc }}"
                                         alt="HSRP Certificate Attachment"
                                         class="preview-image {{ $isHsrpPDF ? 'd-none' : '' }}"
                                         onclick="OpenImageModal('{{ $hsrpImageSrc }}')">
                        
                                    <!-- PDF Preview -->
                                    <iframe id="hsrp_certificate_PDF"
                                            src="{{ $isHsrpPDF ? $hsrpFilePath : '' }}"
                                            class="preview-pdf {{ !$isHsrpPDF ? 'd-none' : '' }}"
                                            frameborder="0"></iframe>
                                            
                                            
                                          @if($isHsrpPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $hsrpFilePath }}')">
                                            </div>
                                        @endif
                                </div>
                            </div>

                        </div>

                        
                        <?php
                            
                        $fcAttachment = $request->assignment->vehicle->fc_attachment ?? '';
                        $defaultImage = asset('b2b/img/default_image.png');
                        $fcFilePath = !empty($fcAttachment)
                            ? asset("EV/asset_master/fc_attachments/{$fcAttachment}")
                            : '';
                    
                        $isFcPDF = $fcFilePath && \Illuminate\Support\Str::endsWith($fcAttachment, '.pdf');
                        $fcImageSrc = (!$fcFilePath || $isFcPDF) ? $defaultImage : $fcFilePath;
                            ?>
                         
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Fitness Certificate (FC) Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center"  >
                                        <!-- Image Preview -->
                                        <img id="fc_attachment_Image"
                                             src="{{ $fcImageSrc }}"
                                             alt="Fitness Certificate Attachment"
                                             class="preview-image {{ $isFcPDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $fcImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="fc_attachment_PDF"
                                                src="{{ $isFcPDF ? $fcFilePath : '' }}"
                                                class="preview-pdf {{ !$isFcPDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                                
                                          @if($isFcPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $fcFilePath }}')">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        
                        
                        <!--<div class="col-md-6 mb-3">-->
                        <!--        <div class="form-group">-->
                        <!--            <label class="input-label mb-2 ms-1" for="client_name">Client Name</label>-->
                        <!--            <input type="text" class="form-control bg-white" name="client_name" id="client_name"  placeholder="Client Name" readonly>-->
                        <!--        </div>-->
                        <!--</div>-->
                        
                        
                                                
                        
                        
                        
                    </div>
                    
            </div>
            </div>
            </div>
        @endif
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
