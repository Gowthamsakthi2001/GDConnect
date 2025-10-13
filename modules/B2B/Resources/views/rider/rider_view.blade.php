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
                            <div class="card-body text-center p-4">
                                @if(!empty($rider->terms_condition) && $rider->terms_condition == 1)
                                    <div class="form-check d-flex align-items-center p-2">
                                        <p class="mb-0">
                                            <input 
                                                type="checkbox" 
                                                disabled 
                                                {{ !empty($rider->terms_condition) && $rider->terms_condition == 1 ? 'checked' : '' }}
                                            >
                                            Terms & Conditions Accepted
                                        </p>
                                    </div>
                                @else
                                    <p class="mt-3 text-muted mb-0">
                                        The rider has not accepted the terms and conditions yet.
                                    </p>
                                @endif
                            </div>
                    </div>
                </div>
            @endif
                
                  </div>
        </div>
    </div>
</div>


        
         <!--  Image View Modal -->
                    <div class="modal fade" id="attachment_view_modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4" style="overflow:hidden;"> <!-- overflow hidden added -->
                                <div class="modal-header border-0 d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-dark" onclick="zoomIn()">
                                        <i class="bi bi-zoom-in"></i>
                                    </button>
                                    <button class="btn btn-sm btn-dark" onclick="zoomOut()">
                                        <i class="bi bi-zoom-out"></i>
                                    </button>
                                    <button class="btn btn-sm btn-dark" onclick="rotateImage()">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <button class="btn btn-sm btn-dark" onclick="downloadImage()">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="modal-body d-flex justify-content-center align-items-center" 
                                     style="overflow:auto; max-height:80vh; background:#f9f9f9;">
                                    <img src="" id="modal_preview_image" 
                                         style="max-width:100%; max-height:75vh; transition:transform 0.3s ease;">
                                </div>
                            </div>
                        </div>
                    </div>
        
        



    

@endsection

@section('js')

<script>
    let scale = 1;
    let rotation = 0;
    let currentImageUrl = ''; 
    
    function OpenImageModal(img_url) {
        scale = 1;
        rotation = 0;
        currentImageUrl = img_url;; 
        updateImageTransform();
        document.getElementById("modal_preview_image").src = img_url;
        $("#attachment_view_modal").modal('show');
    }
    
    function zoomIn() {
        scale += 0.1;
        updateImageTransform();
    }
    
    function zoomOut() {
        if (scale > 0.2) {
            scale -= 0.1;
            updateImageTransform();
        }
    }
    
    function rotateImage() {
        rotation = (rotation + 90) % 360;
        updateImageTransform();
    }
    
    function updateImageTransform() {
        const img = document.getElementById("modal_preview_image");
        img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
    }
    
    async function downloadImage() {
        console.log('started');
    if (!currentImageUrl) return;

        try {
            const response = await fetch(currentImageUrl, { mode: 'no-cors' });
            const blob = await response.blob();
            const blobUrl = window.URL.createObjectURL(blob);
    
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = currentImageUrl.split('/').pop() || 'image.jpg';
            document.body.appendChild(link);
            link.click();
    
            document.body.removeChild(link);
            window.URL.revokeObjectURL(blobUrl);
        } catch (error) {
            console.error('Error downloading image:', error);
            alert('Unable to download image. Please try opening it in a new tab and saving manually.');
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


@endsection
