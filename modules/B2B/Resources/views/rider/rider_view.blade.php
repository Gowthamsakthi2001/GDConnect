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
                <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
                    In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.rider_list') }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
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
        
    @if(!empty($rider->terms_condition) && $rider->terms_condition == 1 && $rider->terms_condition_status != 1)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="resend-mail-tab" data-bs-toggle="tab" data-bs-target="#resend-mail" type="button" role="tab" aria-controls="resend-mail" aria-selected="false">
                Resend Mail
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
                        <input type="text" class="form-control bg-white" value="{{ $rider->zone->name ?? 'N/A' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control bg-white" value="{{ $rider->name ?? '' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile No</label>
                        <input type="text" class="form-control bg-white" value="{{ $rider->mobile_no ?? '' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email ID</label>
                        <input type="text" class="form-control bg-white" value="{{ $rider->email ?? '' }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">DOB</label>
                        <input type="text" class="form-control bg-white" value="{{ $rider->dob ?? '' }}" readonly>
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
                            @if(!empty($rider->adhar_front))
                                @php
                                    $frontExtension = pathinfo($rider->adhar_front, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/aadhar_images/'.$rider->adhar_front);
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
                        @if(!empty($rider->adhar_back))
                            @php
                                $backExtension = pathinfo($rider->adhar_back, PATHINFO_EXTENSION);
                                $backPath = asset('b2b/aadhar_images/'.$rider->adhar_back);
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
                                <input type="text" class="form-control bg-white" name="adhar_number" id="adhar_number" value="{{$rider->adhar_number ?? ''}}" placeholder="Enter Adhar Number" readonly>
                            </div>
                        </div>

                </div>
                
                
                <div class="row g-3">
                    <!-- Pan Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($rider->pan_front))
                                @php
                                    $panFrontExtension = pathinfo($rider->pan_front, PATHINFO_EXTENSION);
                                    $panFrontPath = asset('b2b/pan_images/'.$rider->pan_front);
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
                            @if(!empty($rider->pan_back))
                                @php
                                    $panBackExtension = pathinfo($rider->pan_back, PATHINFO_EXTENSION);
                                    $panBackPath = asset('b2b/pan_images/'.$rider->pan_back);
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
                                <input type="text" class="form-control bg-white" name="pan_number" id="pan_number" value="{{$rider->pan_number ?? ''}}" placeholder="Enter Pan Number" readonly>
                            </div>
                        </div>

                </div>
                
            
                @if(!empty($rider->driving_license_number))
                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Front</label>
                        <div class="file-preview-container">
                        @php
                            $file = $rider->driving_license_front ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                        @if(pathinfo($rider->driving_license_front, PATHINFO_EXTENSION) === 'pdf')
                                 
                    <iframe src="{{ asset('b2b/driving_license_images/'.$rider->driving_license_front) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                                    <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$rider->driving_license_front) }}')">
                                        </div>
                            
                        @else
                            <img src="{{ asset('b2b/driving_license_images/'.$rider->driving_license_front) }}"
                                 class="img-fluid rounded"
                                 alt="Driving License Front"
                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$rider->driving_license_front) }}')">
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
                            $file = $rider->driving_license_back ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($rider->driving_license_back, PATHINFO_EXTENSION) === 'pdf')
                                       
                                <iframe src="{{ asset('b2b/driving_license_images/'.$rider->driving_license_back) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                                   <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$rider->driving_license_back) }}')">
                                        </div>
                            @else
                                <img src="{{ asset('b2b/driving_license_images/'.$rider->driving_license_back) }}"
                                     class="img-fluid rounded"
                                     alt="Driving License Back"
                                     onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$rider->driving_license_back) }}')">
                                     
                                     
                                              
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
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$rider->driving_license_number ?? ''}}" placeholder="Enter License Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Expiry Date</label>
                                <input type="text" class="form-control bg-white"  value="{{$rider->dl_expiry_date ?? ''}}" readonly>
                            </div>
                        </div>
                </div>
            
            @elseif(!empty($rider->llr_number))

                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LLR</label>
                        <div class="file-preview-container">
                        @php
                            $file = $rider->llr_image ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($rider->llr_image, PATHINFO_EXTENSION) === 'pdf')

                                       
                         <iframe src="{{ asset('b2b/llr_images/'.$rider->llr_image) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                
                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                             style="cursor: pointer; background: transparent;"
                                             onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$rider->llr_image) }}')">
                                </div>
                            @else
                                <img src="{{ asset('b2b/llr_images/'.$rider->llr_image) }}"
                                     class="img-fluid rounded"
                                     alt="LLR Image"
                                     onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$rider->llr_image) }}')">
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
                                <input type="text" class="form-control bg-white" name="llr_number" id="llr_number" value="{{ $rider->llr_number ?? '' }}" placeholder="Enter LLR Number" readonly>
                            </div>
                        </div>
                </div>

                
        @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body p-4 text-center">
                    
                                @if(!empty($rider->terms_condition) && $rider->terms_condition == 1)
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
        
        
           @if(!empty($rider->terms_condition) && $rider->terms_condition == 1 && $rider->terms_condition_status != 1)
            <div class="tab-pane fade" id="resend-mail" role="tabpanel" aria-labelledby="resend-mail-tab">
                <div class="shadow-sm card p-4 text-center">
                    <h5 class="mb-3">Resend Terms & Conditions Mail</h5>
                    <p class="text-muted mb-4">
                        This will resend the Terms & Conditions mail to 
                        <a href="mailto:{{ $rider->customerLogin->customer_relation->email ?? '' }}" 
                           style="text-decoration: none; color: #0d6efd; font-weight: 500;">
                            {{ $rider->customerLogin->customer_relation->email ?? '' }}
                        </a>.
                        <br>
                        <span class="text-danger fw-semibold">
                            After resending, please contact 
                            <a href="mailto:{{ $rider->customerLogin->customer_relation->email ?? '' }}" 
                               style="text-decoration: none; color: inherit; font-weight: 600;">
                                {{ $rider->customerLogin->customer_relation->email ?? '' }}
                            </a> 
                            to confirm they received it.
                        </span>
                    </p>
            
                    <button class="btn btn-primary px-4"
                            id="resendMailBtn"
                            onclick="resendMail('{{ $rider->id }}')">
                        <i class="bi bi-envelope"></i> Resend Mail
                    </button>
            
                    <div id="resendMailMsg" class="mt-3 text-success fw-bold d-none"></div>
                </div>
            </div>
        @endif

            
            
    </div>
</div>


        
  @include('b2b::action_popup_modal') 
        
        



    

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


<script>
// Add interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const serviceCards = document.querySelectorAll('.service-card');
            
            serviceCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    
    // Filter functionality
    const filterSelects = document.querySelectorAll('.filter-section select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Add filter logic here
            console.log('Filter changed:', this.value);
        });
    });
    
    // Button click handlers
    document.addEventListener('click', function(e) {
        if (e.target.matches('.btn')) {
            const btnText = e.target.textContent.trim();
            
            switch(true) {
                case btnText.includes('Assign Technician'):
                    alert('Opening technician assignment modal...');
                    break;
                case btnText.includes('Start Service'):
                    alert('Starting service...');
                    break;
                case btnText.includes('Update Progress'):
                    alert('Opening progress update form...');
                    break;
                case btnText.includes('Generate Invoice'):
                    alert('Generating invoice...');
                    break;
                case btnText.includes('View Details'):
                    alert('Opening service details...');
                    break;
                case btnText.includes('New Service Request'):
                    alert('Opening new service request form...');
                    break;
                case btnText.includes('Resume Service'):
                    alert('Resuming service...');
                    break;
            }
        }
    });
});
</script>

<script>
function resendMail(riderId) {
    Swal.fire({
        title: 'Resend Mail?',
        text: "This will resend the Terms & Conditions mail to the customer.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, send it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("b2b.rider.resend_mail") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ rider_id: riderId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        title: 'Mail Sent Successfully!',
                        text: data.message,
                        icon: 'success',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    title: 'Failed',
                    text: 'Something went wrong!',
                    icon: 'error',
                    position: 'top-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }
    });
}
</script>

@endsection
