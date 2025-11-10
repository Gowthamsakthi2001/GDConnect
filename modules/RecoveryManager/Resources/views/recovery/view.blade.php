<x-app-layout>
 
 @section('style_css')   
<style>
    /* Custom tab styles */
    .custom-tabs .nav-link {
        border: none;
        color: #6c757d; /* default gray */
        font-weight: 500;
    }

    .custom-tabs .nav-link.active {
        color: #28a745; /* green */
        border-bottom: 1px solid #28a745; /* green underline */
        background-color: transparent;
    }

    .custom-tabs .nav-link:hover {
        color: #28a745;
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
        
                .vehicle-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            padding: 12px 16px;
        }

        .vehicle-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        .vehicle-details h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .vehicle-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vehicle-meta span {
            font-size: 14px;
            color: #1a1a1a;
        }

        .dot-separator {
            width: 5px;
            height: 5px;
            background: #1a1a1a;
            border-radius: 50%;
        }

        .back-btn {
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
        }

        .nav-tabs {
            background: white;
            /*border-bottom: 1px solid rgba(0,0,0,0.07);*/
            padding: 12px 16px 0;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #1a1a1a;
            font-weight: 600;
            font-size: 16px;
            padding: 10px 10px 12px 0;
            margin-right: 24px;
            background: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #12ae3a;
            border-bottom: 1px solid #12ae3a;
        }

        .activity-logs-btn {
            background: rgba(18,174,58,0.1);
            color: #12ae3a;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .filter-section {
            padding: 16px;
            background: white;
        }

        .filter-section h5 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .date-picker {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 16px;
            width: 200px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #1a1a1a;
        }

        .status-columns {
            display: flex;
            gap: 16px;
            padding: 16px;
            overflow-x: auto;
            min-height: 650px;
        }

        .status-column {
            min-width: 376px;
            background: white;
            border-right: 1px solid rgba(0,0,0,0.07);
        }

        .status-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-header.pending {
            background: #ffbebe;
            color: #a61d1d;
            border: 0.8px solid #a61d1d;
        }

        .status-header.assigned {
            background: #d8e4fe;
            color: #2563eb;
            border: 0.8px solid #2563eb;
        }

        .status-header.in-progress {
            background: #f0d8fe;
            color: #7e25eb;
            border: 0.8px solid #7e25eb;
        }

        .status-header.hold {
            background: #fef5d8;
            color: #947b14;
            border: 0.8px solid #947b14;
        }

        .status-header.closed {
            background: #d4efdf;
            color: #005d27;
            border: 0.8px solid #005d27;
        }

        .count-badge {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 5px;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: black;
        }

        .cards-container {
            padding: 24px 16px;
            height: 524px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .service-card {
            background: white;
            border: 1px solid rgba(26,26,26,0.5);
            border-radius: 8px;
            padding: 12px 16px;
            width: 344px;
        }

        .card-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #1a1a1a;
        }

        .card-row:last-child {
            margin-bottom: 0;
        }

        .card-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .scrollable-content {
            max-height: 100vh;
            overflow: auto;
        }

        /* Custom scrollbar */
        .cards-container::-webkit-scrollbar {
            width: 6px;
        }

        .cards-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .status-columns::-webkit-scrollbar {
            height: 6px;
        }

        .status-columns::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .status-columns::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .status-columns::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
</style>
@endsection

    <div class="main-content">
        <div class="">
        <div class="p-3 rounded" style="background:#fbfbfb;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <!-- Title -->
                <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
                    Recovery In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <!--<a href="{{ route('admin.recovery_management.list',['type'=>($data->status =='opened') ?'pending' : $data->status]) }}" -->
                <!--   class="btn btn-dark btn-md mt-2 mt-md-0">-->
                <!--    <i class="bi bi-arrow-left me-1"></i> Back-->
                <!--</a>-->
            </div>
            
        </div>
    </div>
     
            <div>
    <div class="card">
        <div class="card-body">
           <div class="row mb-4 g-2">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border:#A61D1D 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#A61D1D">
                                Opened
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                               {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($data))
                @if($data->status == 'closed')
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border:#005D27 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#005D27;">
                                Closed  
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                               {{ $data->closed_at ? \Carbon\Carbon::parse($data->closed_at)->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            </div>
            
            <ul class="nav custom-tabs mt-3 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#recovery-info">Recovery Info</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rider-info">Rider Info</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rider-kyc">Rider KYC</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#vehicle-info">Vehicle Info</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#live-tracking">Live Tracking</a>
                    </li>
                    <!--<li class="nav-item">-->
                    <!--    <a class="nav-link" data-bs-toggle="tab" href="#agent-status">Recovery Details</a>-->
                    <!--</li>-->
                    
                </ul>
                
                
                <div class="tab-content">
                    <div class="tab-pane fade " id="rider-info">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                     <div class="col-md-6 mb-3"> <!-- Updated by Gowtham.S-->
                                        <label class="form-label">Zone Name</label>
                                        <input type="text" class="form-control bg-white" value="{{ $data->rider->zone->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control bg-white" value="{{ $data->rider->name ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mobile No</label>
                                        <input type="text" class="form-control bg-white" value="{{ $data->rider->mobile_no ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email ID</label>
                                        <input type="text" class="form-control bg-white" value="{{ $data->rider->email ?? 'N/A' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">DOB</label>
                                        <input type="text" class="form-control bg-white" value="{{ $data->rider->dob ?? 'N/A' }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>   
                      </div>
                </div>
                
            
            <div class="tab-content">
                <div class="tab-pane fade" id="rider-kyc">
                    <div class="card">
                        <div class="card-body">
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
                                <input type="text" class="form-control bg-white" name="adhar_number" id="adhar_number" value="{{$data->rider->adhar_number ?? 'N/A'}}" placeholder="Enter Adhar Number" readonly>
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
                                <input type="text" class="form-control bg-white" name="pan_number" id="pan_number" value="{{$data->rider->pan_number ?? 'N/A'}}" placeholder="Enter Pan Number" readonly>
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
                                 
                    <iframe src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                            
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
                                 alt="Default Driving License">
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
                                       
                                <iframe src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
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
                                 alt="Default Driving License">
                        @endif
                        </div>
                    </div>
                </div>
                
                
                 <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Number</label>
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$data->rider->driving_license_number ?? 'N/A'}}" placeholder="Enter License Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Expiry Date</label>
                                <input type="text" class="form-control bg-white"  value="{{$data->rider->dl_expiry_date ?? 'N/A'}}" readonly>
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

                                       
                         <iframe src="{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
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
                                     alt="Default Driving License">
                            @endif
                        </div>
                    </div>
                
                </div>
                
                
                
            
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">LLR Number</label>
                                <input type="text" class="form-control bg-white" name="llr_number" id="llr_number" value="{{ $data->rider->llr_number ?? 'N/A' }}" placeholder="Enter LLR Number" readonly>
                            </div>
                        </div>
                </div>

                
                
            @else
                <div class="row">
                    <div class="col-md-12">
                            <div class="card-body text-center p-4">
                                @if(!empty($data->rider->terms_condition) && $data->rider->terms_condition == 1)
                                    <div class="form-check d-flex align-items-center p-2">
                                        <p class="mb-0">
                                            <input 
                                                type="checkbox" 
                                                disabled 
                                                {{ !empty($data->rider->terms_condition) && $data->rider->terms_condition == 1 ? 'checked' : '' }}
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
                
            <div class="tab-content">
                <div class="tab-pane fade" id="vehicle-info">
                    <div class="card">
                        <div class="card-body">
                
                            <div class="row">
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id" value="{{$data->assignment->asset_vehicle_id ?? 'N/A'}}"  readonly>
                            </div>
                        </div>
                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_number">Vehicle No</label>
                                <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{$data->assignment->vehicle->permanent_reg_number ?? 'N/A'}}"  placeholder="Vehicle NO" readonly>
                            </div>
                        </div>



                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number</label>
                                <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{$data->assignment->vehicle->chassis_number ?? 'N/A'}}" placeholder="Chassis Number" readonly>
                            </div>
                        </div>
                        
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="imei_number">IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="imei_number" id="imei_number" value="{{$data->assignment->vehicle->telematics_imei_number ?? 'N/A'}}" placeholder="IMEI Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number" value="{{$data->assignment->vehicleRequest->vehicle->motor_number ?? 'N/A'}}"  placeholder="Engine Number/Motor Number" readonly>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city">City</label>
                                <!--<input type="text" class="form-control bg-white" name="city" id="city"  placeholder="City">-->
                             <input type="text" class="form-control bg-white" name="city_id" id="city_id" value="{{$data->assignment->vehicleRequest->city->city_name ?? 'N/A'}}"  placeholder="city_id" readonly>
                            
                                
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
                                <input type="text" class="form-control bg-white" value="{{ $data->assignment->vehicle->vehicle_type_relation->name ?? 'N/A' }}" readonly> 
                            </div>
                        </div>
                        
                        
                      <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="model">Model</label>
                                    <input type="text" class="form-control bg-white" name="model" id="model" value="{{$data->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'}}" placeholder="Model" readonly>
                                </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="color">Color</label>
                                    <input type="text" class="form-control bg-white" name="color" id="color"  placeholder="Color" value="{{$data->assignment->vehicle->color_relation->name ?? 'N/A'}}" readonly>
                                </div>
                        </div>
                       
                       <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                    <input type="date" class="form-control bg-white" name="vehicle_delivery_date" value="{{$data->assignment->vehicle->vehicle_delivery_date ?? 'N/A'}}" id="vehicle_delivery_date" readonly>
                                </div>
                        </div>
                        
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>-->
                        <!--        <input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="{{$vehicle->vehicleStatus ?? 'N/A' }}" readonly>-->
                                
                        <!--    </div>-->
                        <!--</div> -->
                        
                                <?php
                        
                                $RCAttachment = $data->assignment->vehicle->reg_certificate_attachment ?? '';
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
                                    <div class="col-12 text-center">
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
                                    </div>
                                </div>

                            </div>
                            
                            
                        <?php
                        
                                $insuranceAttachment = $data->assignment->vehicle->insurance_attachment ?? '';
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
                                    <div class="col-12 text-center">
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
                                    </div>
                                </div>

                            </div>
                        
                        
                        @php
                            $hsrpAttachment = $data->assignment->vehicle->hsrp_copy_attachment ?? '';
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
                                <div class="col-12 text-center">
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
                                </div>
                            </div>

                        </div>

                        
                        <?php
                            
                        $fcAttachment = $data->assignment->vehicle->fc_attachment ?? '';
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
                                    <div class="col-12 text-center">
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
                                    </div>
                                </div>
                            </div>

                    </div>
            
            </div>
                </div>   
                    </div>
                        </div>  
                
                
            <div class="tab-content">
                <div class="tab-pane fade show active" id="recovery-info">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
            
                <!-- Vehicle No -->
                <!--<div class="col-md-6 mb-3">-->
                <!--    <div class="form-group">-->
                <!--        <label class="input-label mb-2 ms-1" for="vehicle_no">Vehicle No </label>-->
                <!--        <input type="text" class="form-control bg-white" name="vehicle_no" id="vehicle_no" value="{{$data->vehicle_number??''}}" placeholder="Enter Vehicle No" readonly>-->
                <!--    </div>-->
                <!--</div>-->
            
                <!-- Chassis Number -->
                <!--<div class="col-md-6 mb-3">-->
                <!--    <div class="form-group">-->
                <!--        <label class="input-label mb-2 ms-1" for="chassis_no">Chassis Number </label>-->
                <!--        <input type="text" class="form-control bg-white" name="chassis_no" id="chassis_no" value="{{$data->chassis_number??''}}" placeholder="Enter Chassis Number" readonly>-->
                <!--    </div>-->
                <!--</div>-->
                
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="reason">Reason For Recovery </label>
                        <!--<input type="text" class="form-control bg-white" name="reason" id="reason" value="{{$data->reason??''}}" placeholder="Enter Chassis Number" readonly>-->
                        <select class="form-select " name="reason_for_recovery" id="reason_for_recovery" disabled>
                                    <option value="">Select</option>
                                    <option value="1" {{ $data->reason == 1 ? 'selected' : '' }}>Breakdown</option>
                                    <option value="2" {{ $data->reason == 2 ? 'selected' : '' }}>Battery Drain</option>
                                    <option value="3" {{ $data->reason == 3 ? 'selected' : '' }}>Accident</option>
                                    <option value="4" {{ $data->reason == 4 ? 'selected' : '' }}>Rider Unavailable</option>
                                    <option value="5" {{ $data->reason == 5 ? 'selected' : '' }}>Other</option>
                                </select>
                    </div>
                </div>
                

                
                
                <!-- Rider Name -->
                <!--<div class="col-md-6 mb-3">-->
                <!--    <div class="form-group">-->
                <!--        <label class="input-label mb-2 ms-1" for="rider_name">Rider Name </label>-->
                <!--        <input type="text" class="form-control bg-white" name="rider_name" id="rider_name" value="{{$data->rider_name??''}}"readonly>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!-- Rider Contact -->
                <!--<div class="col-md-6 mb-3">-->
                <!--    <div class="form-group">-->
                <!--        <label class="input-label mb-2 ms-1" for="rider_contact">Rider Contact </label>-->
                <!--        <input type="text" class="form-control bg-white" name="rider_contact" id="rider_contact" value="{{$data->rider_mobile_no??''}}"  readonly>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!-- Client Business Name -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="client_business">Client</label>
                        <input type="text" class="form-control bg-white" name="client" id="client" value="{{$data->client_name??''}}"readonly>
                    </div>
                </div>
            
                <!-- Contact No -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="contact_no">Client Contact No</label>
                        <input type="text" class="form-control bg-white" name="contact_no" id="contact_no" value="{{$data->contact_no??''}}" readonly>
                    </div>
                </div>
                
                
                
                
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="description">Description</label>
                        <textarea 
                            class="form-control bg-white" 
                            name="description" 
                            id="description" 
                            placeholder="Enter Description" 
                            rows="4" 
                            readonly
                        >{{ $data->description ?? '' }}</textarea>
                    </div>
                </div>
                
                @if($data->is_agent_assigned)
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="assigned_agent">Assigned Agent</label>
                                        <input type="text" class="form-control bg-white" name="assigned_agent" id="assigned_agent" value="{{ ($data->recovery_agent->first_name .' '. $data->recovery_agent->last_name) ?? 'N/A'}}"  readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="contact">Agent Contact</label>
                                        <input type="text" class="form-control bg-white" name="agent_contact" id="agent_contact" value="{{$data->recovery_agent->mobile_number ?? 'N/A'}}"  readonly>
                                    </div>
                                </div>
                                
                                  <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="vehicle_number">Agent Status </label>
                                        <input type="text" class="form-control bg-white" name="status" id="status" value="{{ ucfirst($data->agent_status)?? 'N/A'}}"  placeholder="Vehicle NO" readonly>
                                    </div>
                                </div>                                
                @endif 
                                                        @if($data->status == 'closed')
                <!-- Recovery Photos -->
                <div class="col-md-12 mb-3">
                    <label class="input-label mb-2 ms-1">Recovery Photos</label>
                
                    @if(!empty($data->images))
                        @php
                            $attachments = is_array($data->images)
                                ? $data->images
                                : json_decode($data->images, true);
                
                            if (!is_array($attachments)) {
                                $attachments = [$data->images];
                            }
                        @endphp
                
                        <div class="row g-3">
                            @foreach($attachments as $file)
                                @if(!empty($file))
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <div 
                                            style="width:100%; height:200px; border:2px solid #ccc;
                                                   background-size:cover; background-position:center;
                                                   background-image:url('{{ asset('b2b/recovery_comments/'.$file) }}'); cursor:pointer;"
                                            onclick="OpenImageModal('{{ asset('b2b/recovery_comments/'.$file) }}')">
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p>No recovery photos uploaded.</p>
                    @endif
                </div>
                
                <!-- Recovery Video / Document -->
                <div class="col-md-12 mb-3">
                    <label class="form-label">Recovery Video</label>
                
                    @if(!empty($data->video))
                        @php
                            $fileName = $data->video;
                            $filePath = asset('b2b/recovery_comments/' . $fileName);
                            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        @endphp
                
                        @if($extension === 'pdf')
                            <!-- Show PDF -->
                            <iframe src="{{ $filePath }}" style="width:100%; height:400px;" frameborder="0"></iframe>
                
                        @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <!-- Show Image -->
                            <img src="{{ $filePath }}" alt="Police Report"
                                 style="width:100%; height:400px; object-fit:contain; border:1px solid #ccc; cursor:pointer;"
                                 onclick="OpenImageModal('{{ $filePath }}')">
                
                        @elseif(in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                            <!-- Show Video -->
                            <video controls style="width:100%; height:400px; border:1px solid #ccc; border-radius:6px;">
                                <source src="{{ $filePath }}" type="video/{{ $extension }}">
                                Your browser does not support the video tag.
                            </video>
                
                        @else
                            <p>Unsupported file type: {{ $extension }}</p>
                        @endif
                    @else
                        <p>No Recovery Video uploaded.</p>
                    @endif
                
            </div>
            @endif
            
            </div>
            
            
            </div>
                </div>   
                    </div>
                        </div> 
        
            <div class="tab-content">
                <div class="tab-pane fade" id="live-tracking">
                    <div class="card">
                        <div class="card-body">
            
                            <!-- Refresh button -->
                            <div class="d-flex justify-content-end mb-2">
                                <button id="refreshMapBtn" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh Location
                                </button>
                            </div>
            
                            <!-- Map Section -->
                            <div class="map-container flex-grow-1 position-relative" style="height: 55vh;">
                                <div id="map" style="width: 100%; height: 100%; border-radius: 10px;"></div>
            
                                <!-- Dynamic Placeholder -->
                                <div id="mapPlaceholder"
                                     class="map-placeholder bg-white position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                     style="z-index: 10;">
                                    <div id="placeholderContent" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 mb-0">Loading vehicle location...</p>
                                    </div>
                                </div>
                            </div>
                            
                                            <!-- Vehicle Info Section -->
                        <div id="vehicleInfoContainer" class="mt-4 d-none">
                             <h6 class="fw-bold mb-3">Vehicle Information</h6>
                            <div class="border rounded p-3 bg-light">
                               
                                <div class="row">
                                    <div class="col-md-4"><strong>Vehicle Number:</strong> <span id="infoVehicleNumber">--</span></div>
                                    <div class="col-md-4"><strong>IMEI:</strong> <span id="infoIMEI">--</span></div>
                                    <div class="col-md-4"><strong>Type:</strong> <span id="infoType">--</span></div>
                                    <div class="col-md-4 mt-2"><strong>Speed:</strong> <span id="infoSpeed">--</span> km/h</div>
                                    <div class="col-md-4 mt-2"><strong>Ignition:</strong> <span id="infoIgnition">--</span></div>
                                    <div class="col-md-4 mt-2"><strong>Status:</strong> <span id="infoStatus">--</span></div>
                                </div>
                            </div>
                        </div>
                
                        </div>
                    </div>
                </div>
            </div>
            
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
    
        @php         
            $api_key = \App\Models\BusinessSetting::where('key_name', 'google_map_api_key')->value('value'); 
            $imei = $data->assignment->vehicle->telematics_imei_number ?? '';
        @endphp
@section('script_js')
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
let map, marker, infoWindow, bikeIcon;
let mapInitialized = false;
let refreshInterval = null;
// let imei = "{{ $imei ?? '100000000200001' }}"; // dynamically passed
let imei = "100000000200001";

// ✅ Initialize Google Map
function initMap() {
    if (mapInitialized) return;

    const indiaCenter = { lat: 20.5937, lng: 78.9629 };
    map = new google.maps.Map(document.getElementById("map"), {
        center: indiaCenter,
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        streetViewControl: true,
        fullscreenControl: false,
        styles: [
            { featureType: "poi", stylers: [{ visibility: "off" }] },
            { featureType: "transit", stylers: [{ visibility: "off" }] }
        ]
    });

    bikeIcon = {
        url: "{{ asset('admin-assets/img/bike_image.svg') }}",
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 20)
    };

    marker = new google.maps.Marker({
        map: map,
        position: indiaCenter,
        icon: bikeIcon,
        title: "India"
    });

    infoWindow = new google.maps.InfoWindow();
    mapInitialized = true;
}

// ✅ Show placeholder message dynamically
function showPlaceholder(type = "loading", message = "") {
    const placeholder = document.getElementById("mapPlaceholder");
    const content = document.getElementById("placeholderContent");

    let iconHTML = "";
    let text = "";

    switch (type) {
        case "loading":
            iconHTML = `<div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>`;
            text = "Loading vehicle location...";
            break;
        case "notfound":
            iconHTML = `<i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>`;
            text = message || "Vehicle data not found!";
            break;
        case "noimei":
            iconHTML = `<i class="bi bi-car-front-fill text-secondary" style="font-size: 3rem;"></i>`;
            text = message || "No Imei found the Vehicle ";
            break;
        default:
            iconHTML = `<i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>`;
            text = message || "No information available";
    }

    content.innerHTML = `${iconHTML}<p class="mt-2 mb-0">${text}</p>`;
    placeholder.classList.remove("d-none");
    
}

// ✅ Hide placeholder when data is ready
function hidePlaceholder() {
    document.getElementById("mapPlaceholder").classList.add("d-none");
}

// ✅ Fetch vehicle coordinates
function fetchVehicleCoordinates(imei) {
    if (!imei) {
        showPlaceholder("noimei");
        return;
    }

    showPlaceholder("loading");

    let urlJson = `{{ route('admin.recovery_management.get_vehicle_tracking') }}?imei=${imei}`;

    fetch(urlJson)
        .then(response => response.json())
        .then(res => {
            const vehicleData = res?.nodes?.[0];
            if (!vehicleData) {
                showPlaceholder("notfound", "No data found for this Vehicle");
                return;
            }

            const lat = parseFloat(vehicleData.latitude);
            const lng = parseFloat(vehicleData.longitude);

            if (isNaN(lat) || isNaN(lng)) {
                showPlaceholder("notfound", "Invalid location data");
                return;
            }

            const vehicleInfo = {
                vehicleNumber: vehicleData.vehicleNumber || "Unknown",
                imei: vehicleData.IMEINumber || imei,
                vehicleType: vehicleData.vehicleType || "",
                speed: vehicleData.lastSpeed ?? 0,
                ignition: vehicleData.lastIgnition === "1" ? "On" : "Off",
                status: vehicleData.vehicleStatus || "Unknown"
            };

            hidePlaceholder();
            updateMapWithCoordinates(lat, lng, vehicleInfo);
            updateVehicleInfo(vehicleInfo);
        })
        .catch(error => {
            console.error("Error fetching vehicle coordinates:", error);
            showPlaceholder("notfound", "Error fetching data");
        });
}

function updateVehicleInfo(info) {
    document.getElementById("infoVehicleNumber").textContent = info.vehicleNumber;
    document.getElementById("infoIMEI").textContent = info.imei;
    document.getElementById("infoType").textContent = info.vehicleType;
    document.getElementById("infoSpeed").textContent = info.speed;
    document.getElementById("infoIgnition").textContent = info.ignition;
    document.getElementById("infoStatus").textContent = info.status;

    document.getElementById("vehicleInfoContainer").classList.remove("d-none");
}

// ✅ Update map coordinates
function updateMapWithCoordinates(lat, lng, vehicleInfo = {}) {
    if (!mapInitialized) initMap();

    const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
    map.setCenter(position);
    map.setZoom(15);

    marker.setPosition(position);
    marker.setTitle(vehicleInfo.vehicleNumber);
    marker.setIcon(bikeIcon);

    const infoContent = `
        <div class="map-infowindow">
            <strong>${vehicleInfo.vehicleNumber}</strong><br>
            IMEI: ${vehicleInfo.imei}<br>
            Vehicle Type: ${vehicleInfo.vehicleType}<br>
            Status: ${vehicleInfo.status}<br>
            Location: ${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}
        </div>
    `;

    infoWindow.setContent(infoContent);
    infoWindow.open(map, marker);

    google.maps.event.clearListeners(marker, "click");

    // ✅ Open info window only when the marker is clicked
    marker.addListener("click", () => {
        infoWindow.open(map, marker);
    });
}


// ✅ Refresh Logic
document.addEventListener("DOMContentLoaded", function () {
    const liveTrackingTab = document.querySelector('a[href="#live-tracking"]');
    const refreshButton = document.getElementById("refreshMapBtn");

    if (liveTrackingTab) {
        liveTrackingTab.addEventListener("shown.bs.tab", function () {
            initMap();
            fetchVehicleCoordinates(imei);

            // Ensure map resizes properly after becoming visible
            setTimeout(() => google.maps.event.trigger(map, "resize"), 300);
        });
    }

    if (refreshButton) {
        refreshButton.addEventListener("click", function () {
            fetchVehicleCoordinates(imei);
        });
    }
});
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ $api_key }}&callback=initMap" async defer></script>
@endsection
</x-app-layout>
