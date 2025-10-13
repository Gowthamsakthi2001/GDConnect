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
        border-bottom: 2px solid #28a745; /* green underline */
        background-color: transparent;
    }

    .custom-tabs .nav-link:hover {
        color: #28a745;
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
            border-bottom:#12ae3a 2px solid ;
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
                    Deployment Request detail
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.admin.deployment_request.list') }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 mb-2" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border : #005D27 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#005D27">
                                Opened
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                               {{ \Carbon\Carbon::parse($data->created_at)->format('d M y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($data))
                @if($data->status == 'completed')
                <div class="col-md-6 mb-2" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border:#A61D1D 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#A61D1D;">
                                Closed  
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                                 {{ \Carbon\Carbon::parse($data->completed_at)->format('d M y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endif
                
                
            </div>
        </div>
    </div>
    <div class="card rounded mb-3">
                <!-- Tabs -->
                <ul class="nav nav-tabs custom-tabs mb-3 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#rider-info">Rider Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kyc-info">KYC Info</a>
                    </li>
                    @if(isset($data))
                    @if($data->status == 'completed')
                        <li class="nav-item">
                            <a class="nav-link " data-bs-toggle="tab" href="#vehicle-info">Vehicle Info</a>
                        </li>
                        
                        @endif
                    @endif
                
                   
                </ul>
    </div>
     
            <div class="tab-content">
                <div class="tab-pane fade show active" id="rider-info">
                <div class="card">
                    <div class="card-body">
                      <div class="row">
                          
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Name</label>
                                <input type="text" class="form-control bg-white" name="name" id="name"  value="{{$data->rider->name ?? ''}}" placeholder="Name" readonly>
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">Mobile No</label>
                                <input type="text" class="form-control bg-white" name="mobile_no" id="mobile_no"  value="{{$data->rider->mobile_no ?? ''}}" placeholder="Mobile No" readonly>
                            </div>
                        </div>
                        
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="email_id">Email ID</label>
                                <input type="text" class="form-control bg-white" name="email_id" id="email_id"  value="{{$data->rider->email ?? ''}}" placeholder="Email ID" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="dob">DOB</label>
                                <input type="date" class="form-control bg-white" name="dob" id="dob"  value="{{$data->rider->dob ?? ''}}" placeholder="DOB" readonly>
                            </div>
                        </div>
                        

                        
                        </div>
                        
                        <div class="row mb-2">
                            <h6 class="custom-dark">Request Form</h5>
                        </div>
                        <div class="row">
                            
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="from_date">Vehicle Duration From Date</label>
                                <input type="date" class="form-control bg-white" name="from_date" id="from_date"  value="{{$data->start_date ?? ''}}" placeholder="From date" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="to_date">Vehicle Duration To Date</label>
                                <input type="date" class="form-control bg-white" name="to_date" id="to_date"  value="{{$data->end_date ?? ''}}" placeholder="End Date" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                             <select class="form-control" name="vehicle_type" disabled>
                                <option value="">Select Vehicle Type</option>
                                @if(isset($vehicle_types))
                                @foreach($vehicle_types as $type)
                                <option value="{{$type->id}}" {{ $data->vehicle_type ==  $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                @endforeach
                                @endif
                              </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_type">Battery Type</label>
                                <select class="form-control" name="battery_type" disabled>
                              <option value="">Select Battery Type</option>
                              <option value="1" {{ ($data->battery_type == '1') ? 'selected' : '' }}>Self Charging</option>
                              <option value="2" {{ ($data->battery_type == '2') ? 'selected' : '' }}>Portable</option>
                            </select>
                            </div>
                        </div>
                        </div>
  
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="kyc-info">
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
                                <input type="text" class="form-control bg-white" name="pan_number" id="pan_number" value="{{$data->rider->pan_number ?? ''}}" placeholder="Enter Pan Number" readonly>
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
                                 alt="Default Driving License"
                                 onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
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
                                 alt="Default Driving License"
                                 onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                </div>
                
                
                 <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Number</label>
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$data->rider->driving_license_number ?? ''}}" placeholder="Enter License Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Expiry Date</label>
                                <input type="text" class="form-control bg-white"  value="{{$data->rider->dl_expiry_date ?? ''}}" readonly>
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
                                     alt="Default Driving License"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
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
            <div class="tab-pane fade" id="vehicle-info">
                <div class="card">
                    <div class="card-body">
                   <form id="ApproveAssetMasterVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                                                    <div class="col-md-6 mb-3">
                            <div class="form-group"> 
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number (VIN)</label>
                                <select class="form-select custom-select2-field form-control-sm bg-white"  disabled>
                                    <option value="">Select</option>
                                    @if(isset($passed_chassis_numbers))
                                       @foreach($passed_chassis_numbers as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->id ?? null) == $val->id ? 'selected' : '' }}>{{$val->chassis_number}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input id="chassis_number" name="chassis_number" value="{{$data->assignment->vehicle->chassis_number ?? ''}}" type="hidden">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_category">Vehicle Category</label>
                                <!--<input type="text" class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category"  value="{{$data->assignment->vehicle->vehicle_category ?? ''}}" placeholder="Enter Vehicle Category" >-->
                                <select class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category">
                                    <option value="">Select</option>
                                    <option value="regular_vehicle" {{ ($data->assignment->vehicle->vehicle_category ?? '') == 'regular_vehicle' ? 'selected' : '' }} >Regular Vehicle</option>
                                    <option value="low_speed_vehicle" {{ ($data->assignment->vehicle->vehicle_category ?? '') == 'low_speed_vehicle' ? 'selected' : '' }} >Low Speed Vehicle</option>
                                 </select>
                                
                                
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <select class="form-select custom-select2-field form-control-sm"  disabled>
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                       @foreach($vehicle_types as $type)
                                          <option value="{{$type->id}}" {{ ($data->assignment?->vehicle?->vehicle_type ?? null) == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="vehicle_type" name="vehicle_type" value="{{ $data->assignment?->vehicle?->vehicle_type ?? '' }}">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="model">Model</label>
                                <!--<input type="text" class="form-control bg-white" name="model" id="model"  value="{{$data->assignment->vehicle->model ?? ''}}" placeholder="Enter Model" >-->
                                
                                   <select class="form-select custom-select2-field form-control-sm"  disabled>
                                        <option value="">Select</option>
                                        @if(isset($vehicle_models))
                                           @foreach($vehicle_models as $type)
                                              <option value="{{$type->id}}"  data-id="{{$type->id}}" data-make="{{$type->make}}" data-variant="{{$type->variant}}" data-color="{{$type->color}}" {{ ($data->assignment?->vehicle?->model ?? null) == $type->id ? 'selected' : '' }}>{{$type->vehicle_model}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                            </div>
                        </div>
                         <input type="hidden" id="model" name="model" value="{{$data->assignment?->vehicle?->model}}">
                         
                    @php
                        $model_data = \Modules\AssetMaster\Entities\VehicleModelMaster::where('id', $data->assignment?->vehicle?->model)->first();
                    @endphp


                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="make">Make</label>
                                <!--<input type="text" class="form-control bg-white" style="padding: 12px 20px;" name="make" id="make"  value="{{$data->assignment->vehicle->make ?? ''}}" placeholder="Enter Make" >-->
                                <select class="form-select custom-select2-field form-control-sm"  disabled>
                                       @if($model_data)
                                            <option value="{{ $model_data->id }}" selected>{{ $model_data->make }}</option>
                                        @else
                                            <option value="">Select Make</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                         <input type="hidden" name="make" id="make" value="{{$model_data->make ?? ''}}">


                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="variant">Variant</label>
                                <!--<input type="text" class="form-control bg-white" name="variant" id="variant"  value="{{$data->assignment->vehicle->variant ?? ''}}" placeholder="Enter Variant" >-->
                             <select class="form-select custom-select2-field form-control-sm"  disabled>
                                       @if($model_data)
                                            <option value="{{ $model_data->id }}" selected>{{ $model_data->variant }}</option>
                                        @else
                                            <option value="">Select Variant</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="variant" id="variant" value="{{$model_data->variant ?? ''}}">
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="color">Color</label>
                                <!--<input type="text" class="form-control bg-white" name="color" id="color"  value="{{$data->assignment->vehicle->color ?? ''}}" placeholder="Enter Color" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="color" id="color">
                                    <option value="">Select</option>
                                     @if(isset($colors))
                                           @foreach($colors as $color)
                                              <option value="{{$color->id}}"  {{ ($data->assignment?->vehicle?->color ?? null) == $color->id ? 'selected' : '' }}>{{$color->name}}</option>
                                           @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>
                             
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number"  value="{{$data->assignment->vehicle->motor_number ?? ''}}" placeholder="Enter Engine Number/Motor Number" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id"  value="{{$data->assignment->vehicle->vehicle_id ?? ''}}" placeholder="Enter Vehicle ID" >
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_number">Tax Invoice Number</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_number" id="tax_invoice_number"  value="{{$data->assignment->vehicle->tax_invoice_number ?? ''}}" placeholder="Enter Tax Invoice Number" >
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_date">Tax Invoice Date</label>
                                <input type="date" class="form-control bg-white" name="tax_invoice_date" id="tax_invoice_date"  value="{{ $data->assignment?->vehicle?->tax_invoice_date ? date('Y-m-d', strtotime($data->assignment->vehicle->tax_invoice_date)) : '' }}"  placeholder="Enter Tax Invoice Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_value">Invoice Value/Purchase Price</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_value" id="tax_invoice_value"  value="{{$data->assignment->vehicle->tax_invoice_value ?? ''}}" onkeypress="return isNumberKeyNew(event)" placeholder="Enter Invoice Value/Purchase Price" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_attachment">Tax Invoice Attachment</label>
                                <input type="file" class="form-control bg-white" name="tax_invoice_attachment" id="tax_invoice_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'tax_invoice_Image')">
                            </div>
                        </div>
                         <?php
                            // $image_src4 = !empty($data->assignment->vehicle->tax_invoice_attachment)
                            //     ? asset("EV/asset_master/tax_invoice_attachments/{$data->assignment->vehicle->tax_invoice_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                           
                                $TaxInvoiceattachment = $data->assignment->vehicle->tax_invoice_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $TaxInvoicefilePath = !empty($TaxInvoiceattachment)
                                    ? asset("EV/asset_master/tax_invoice_attachments/{$TaxInvoiceattachment}")
                                    : '';
                                   
                                    
                            
                                $isTaxInvoicePDF = $TaxInvoicefilePath && \Illuminate\Support\Str::endsWith($TaxInvoiceattachment, '.pdf');
                                $TaxInvoiceimageSrc = (!$TaxInvoicefilePath || $isTaxInvoicePDF) ? $defaultImage : $TaxInvoicefilePath;


                            ?>
                        <div class="col-md-12 mb-4 my-4">
                         <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                            
                                <img id="tax_invoice_Image"
                                     src="{{ $TaxInvoiceimageSrc }}"
                                     alt="Tax Invoice Attachment"
                                     class="img-fluid rounded shadow border"
                                      style="max-height: 300px; object-fit: cover; {{ $isTaxInvoicePDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$TaxInvoiceimageSrc}}')">
                                     
                                     
                                                                              
                                <iframe id="tax_invoice_PDF"
                                         src="{{ $isTaxInvoicePDF ? $TaxInvoicefilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isTaxInvoicePDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>

                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="location">Location</label>
                                <select class="form-select custom-select2-field form-control-sm bg-white" id="location" name="location">
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->location ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city_code">City Code</label>
                                <select class="form-select custom-select2-field form-control-sm" disabled>
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->city_code ?? null) == $val->id ? 'selected' : '' }}>{{$val->city_name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <input id="city_code" name="city_code" type="hidden"  value="{{$data->assignment?->vehicle?->location}}">
                        
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id">GD Hub ID Allocated</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id" id="gd_hub_id"  value="{{$data->assignment->vehicle->gd_hub_name ?? ''}}" placeholder="Enter GD Hub ID Allocated" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id_exiting">GD Hub ID Existing</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id_exiting" id="gd_hub_id_exiting"  value="{{$data->assignment->vehicle->gd_hub_id ?? ''}}"  placeholder="Enter GD Hub ID Existing">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="financing_type">Financing Type</label>
                                <!--<input type="text" class="form-control bg-white" name="financing_type" id="financing_type"  value="{{$data->assignment->vehicle->financing_type ?? ''}}" placeholder="Financing Type" >-->
                             <select class="form-select custom-select2-field form-control-sm" name="financing_type" id="financing_type">
                                    <option value="">Select</option>
                                    @if(isset($financing_types))
                                       @foreach($financing_types as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->financing_type ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="asset_ownership">Asset Ownership</label>
                                <!--<input type="text" class="form-control bg-white" name="asset_ownership" id="asset_ownership"  value="{{$data->assignment->vehicle->asset_ownership ?? ''}}" placeholder="Enter Asset Ownership" >-->
                                
                                <select class="form-select custom-select2-field form-control-sm" name="asset_ownership" id="asset_ownership">
                                    <option value="">Select</option>
                                    @if(isset($asset_ownerships))
                                       @foreach($asset_ownerships as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->asset_ownership ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="master_lease_agreement">Master Lease Agreement</label>
                                <input type="file" class="form-control bg-white" name="master_lease_agreement" id="master_lease_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'master_lease_Image')">
                            </div>
                        </div>
                        
                        <?php
                        // $image_src = !empty($data->assignment->vehicle->master_lease_agreement)
                        //     ? asset("EV/asset_master/master_lease_agreements/{$data->assignment->vehicle->master_lease_agreement}")
                        //     : asset("admin-assets/img/defualt_upload_img.jpg");
                        
                        
                                $MasterLeaseattachment = $data->assignment->vehicle->master_lease_agreement ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $MasterLeasefilePath = !empty($MasterLeaseattachment)
                                    ? asset("EV/asset_master/master_lease_agreements/{$MasterLeaseattachment}")
                                    : '';
                                   
                                    
                            
                                $isMasterLeasePDF = $MasterLeasefilePath && \Illuminate\Support\Str::endsWith($MasterLeaseattachment, '.pdf');
                                $MasterLeaseimageSrc = (!$MasterLeasefilePath || $isMasterLeasePDF) ? $defaultImage : $MasterLeasefilePath;
                        ?>

                        
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
                                <img id="master_lease_Image"
                                     src="{{ $MasterLeaseimageSrc }}"
                                     alt="Master Lease Agreement"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isMasterLeasePDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$MasterLeaseimageSrc}}')">
                                     
                                                                                                              
                                <iframe id="master_lease_PDF"
                                         src="{{ $isMasterLeasePDF ? $MasterLeasefilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isMasterLeasePDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_start_date">Lease Start Date</label>
                                <input type="date" class="form-control bg-white" name="lease_start_date" id="lease_start_date"  value="{{ $data->assignment?->vehicle?->lease_start_date ? date('Y-m-d', strtotime($data->assignment->vehicle->lease_start_date)) : '' }}"  placeholder="Enter Lease Start Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_end_date">Lease End Date</label>
                                <input type="date" class="form-control bg-white" name="lease_end_date" id="lease_end_date"  value="{{ $data->assignment?->vehicle?->lease_end_date ? date('Y-m-d', strtotime($data->assignment->vehicle->lease_end_date)) : '' }}" placeholder="Enter Lease End Date" >
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="emi_lease_amount">EMI/Lease Amount</label>
                                <input type="text" class="form-control bg-white" name="emi_lease_amount" id="emi_lease_amount"  value="{{$data->assignment->vehicle->emi_lease_amount ?? ''}}" onkeypress="return isNumberKeyNew(event)" placeholder="Enter EMI/Lease Amount" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation">Hypothecation</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation" id="hypothecation"  value="{{$data->assignment->vehicle->hypothecation ?? ''}}" placeholder="Enter Hypothecation" >-->
                             <select class="form-control bg-white" name="hypothecation" style="padding:12px 20px;" id="hypothecation">
                                    <option>Select</option>
                                  <option value="yes" {{ (isset($data->assignment->vehicle->hypothecation) && $data->assignment->vehicle->hypothecation == 'yes') ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ (isset($data->assignment->vehicle->hypothecation) && $data->assignment->vehicle->hypothecation == 'no') ? 'selected' : '' }}>No</option>

                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation_to">Hypothecated To</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation_to" id="hypothecation_to"  value="{{$data->assignment->vehicle->hypothecation_to ?? ''}}" placeholder="Enter Hypothecation To" >-->
                                    <select class="form-select custom-select2-field form-control-sm" name="hypothecation_to" id="hypothecation_to">
                                    <option value="">Select</option>
                                    @if(isset($hypothecations))
                                       @foreach($hypothecations as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->hypothecation_to ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        
                          <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation_document">Hypothecation Document</label>
                                <input type="file" class="form-control bg-white" name="hypothecation_document" id="hypothecation_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hypothecation_Image')">
                            </div>
                        </div>
                        
                        <?php
                            //  $image_hypo = !empty($data->assignment->vehicle->hypothecation_document)
                            // ? asset("EV/asset_master/hypothecation_documents/{$data->assignment->vehicle->hypothecation_document}")
                            // : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                             $Hypothecationattachment = $data->assignment->vehicle->hypothecation_document ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $HypothecationfilePath = !empty($Hypothecationattachment)
                                    ? asset("EV/asset_master/hypothecation_documents/{$Hypothecationattachment}")
                                    : '';
                                   
                                    
                            
                                $isHypothecationPDF = $HypothecationfilePath && \Illuminate\Support\Str::endsWith($Hypothecationattachment, '.pdf');
                                $HypothecationimageSrc = (!$HypothecationfilePath || $isHypothecationPDF) ? $defaultImage : $HypothecationfilePath;
                            
                            
                        ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                     <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                            
                                <img id="hypothecation_Image"
                                     src="{{ $HypothecationimageSrc }}"
                                     alt="Hypothecation Document"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isHypothecationPDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$HypothecationimageSrc}}')">
                                     
                                    <iframe id="hypothecation_PDF"
                                         src="{{ $isHypothecationPDF ? $HypothecationfilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isHypothecationPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_type">Insurance Type</label>
                                <!--<input type="text" class="form-control bg-white" name="insurance_type" id="insurance_type"  value="{{$data->assignment->vehicle->insurance_type ?? ''}}" placeholder="Enter Insurance Type" >-->
                            <select class="form-select custom-select2-field form-control-sm" name="insurance_type" id="insurance_type">
                                    <option value="">Select</option>
                                    @if(isset($insurance_types))
                                       @foreach($insurance_types as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->insurance_type ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurer_name">Insurer Name</label>
                                <!--<input type="text" class="form-control bg-white" name="insurer_name" id="insurer_name"  value="{{$data->assignment->vehicle->insurer_name ?? ''}}" placeholder="Enter Insurer Name" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="insurer_name" id="insurer_name">
                                    <option value="">Select</option>
                                    @if(isset($insurer_names))
                                       @foreach($insurer_names as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->insurer_name ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        

                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_number">Insurance Number</label>
                                <input type="text" class="form-control bg-white" name="insurance_number" id="insurance_number"  value="{{$data->assignment->vehicle->insurance_number ?? ''}}" placeholder="Enter Insurance Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_start_date">Insurance Start Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_start_date" id="insurance_start_date"  value="{{ $data->assignment?->vehicle?->insurance_start_date ? date('Y-m-d', strtotime($data->assignment->vehicle->insurance_start_date)) : '' }}"  placeholder="Enter Insurance Start Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_expiry_date">Insurance Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_expiry_date" id="insurance_expiry_date"  value="{{ $data->assignment?->vehicle?->insurance_expiry_date ? date('Y-m-d', strtotime($data->assignment->vehicle->insurance_expiry_date)) : '' }}"  placeholder="Enter Insurance Expiry Date" >
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_attachment">Insurance Attachment</label>
                                <input type="file" class="form-control bg-white" name="insurance_attachment" id="insurance_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'insurance_Image')">
                            </div>
                        </div>
                        
                           <?php
                            // $image_src1 = !empty($data->assignment->vehicle->insurance_attachment)
                            //     ? asset("EV/asset_master/insurance_attachments/{$data->assignment->vehicle->insurance_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $insuranceAttachment = $data->assignment->vehicle->insurance_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $insuranceFilePath = !empty($insuranceAttachment)
                                    ? asset("EV/asset_master/insurance_attachments/{$insuranceAttachment}")
                                    : '';
                            
                                $isInsurancePDF = $insuranceFilePath && \Illuminate\Support\Str::endsWith($insuranceAttachment, '.pdf');
                                $insuranceImageSrc = (!$insuranceFilePath || $isInsurancePDF) ? $defaultImage : $insuranceFilePath;
                            ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                        <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
                            <img id="insurance_Image"
                                 src="{{ $insuranceImageSrc }}"
                                 alt="Insurance Attachment"
                                 class="img-fluid rounded shadow border"
                                 style="max-height: 300px; object-fit: cover; {{ $isInsurancePDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{ $insuranceImageSrc }}')">
                    
                            <iframe id="insurance_PDF"
                                    src="{{ $isInsurancePDF ? $insuranceFilePath : '' }}"
                                    style="width: 100%; height: 100%; {{ !$isInsurancePDF ? 'display: none;' : '' }} border: none;"
                                    frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_type">Registration Type</label>
                                <!--<input type="text" class="form-control bg-white" name="registration_type" id="registration_type"  value="{{$data->assignment->vehicle->registration_type ?? ''}}" placeholder="Enter Registration Type" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="registration_type" id="registration_type">
                                    <option value="">Select</option>
                                    @if(isset($registration_types))
                                       @foreach($registration_types as $val)
                                         
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->registration_type ?? null) == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_status">Registration Status</label>
                                <input type="text" class="form-control bg-white" name="registration_status" id="registration_status"  value="{{$data->assignment->vehicle->registration_status ?? ''}}" placeholder="Enter Registration Status" >
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_number">Temporary Registration Number</label>
                                <input type="text" class="form-control bg-white" name="temporary_registration_number" id="temporary_registration_number" value="{{$data->assignment->vehicle->temproary_reg_number ?? ''}}"  placeholder="Enter Temporary Registration Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_date">Temporary Registration Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_date" id="temporary_registration_date" value="{{$data->assignment->vehicle->temproary_reg_date ?? ''}}"  placeholder="Enter Temporary Registration Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_expiry_date">Temporary Registration Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_expiry_date" id="temporary_registration_expiry_date" value="{{$data->assignment->vehicle->temproary_reg_expiry_date ?? ''}}"  placeholder="Enter Temporary Registration Expiry Date">
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_certificate_attachment">Temporary Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="temporary_certificate_attachment" id="temporary_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'temporary_certificate_Image')">
                            </div>
                        </div>
                        
                                                
                           <?php
                            // $image_temproary = !empty($data->assignment->vehicle->temproary_reg_attachment)
                            //     ? asset("EV/asset_master/temporary_certificate_attachments/{$data->assignment->vehicle->temproary_reg_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $temporaryAttachment = $data->assignment->vehicle->temproary_reg_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $temporaryFilePath = !empty($temporaryAttachment)
                                    ? asset("EV/asset_master/temporary_certificate_attachments/{$temporaryAttachment}")
                                    : '';
                            
                                $isTemporaryPDF = $temporaryFilePath && \Illuminate\Support\Str::endsWith($temporaryAttachment, '.pdf');
                                $temporaryImageSrc = (!$temporaryFilePath || $isTemporaryPDF) ? $defaultImage : $temporaryFilePath;
                            ?>
          
                        <div class="col-md-12 mb-4 my-4">
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
                                <img id="temporary_certificate_Image"
                                     src="{{ $temporaryImageSrc }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isTemporaryPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $temporaryImageSrc }}')">
                        
                                <iframe id="temporary_certificate_PDF"
                                        src="{{ $isTemporaryPDF ? $temporaryFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isTemporaryPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                                                             
                                     
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_number">Permanent Registration Number</label>
                                <input type="text" class="form-control bg-white" name="permanent_reg_number" id="permanent_reg_number"  value="{{$data->assignment->vehicle->permanent_reg_number ?? ''}}" placeholder="Enter Permanent Registration Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_date">Permanent Registration Date</label>
                                <input type="date" class="form-control bg-white" name="permanent_reg_date" id="permanent_reg_date"  value="{{ $data->assignment?->vehicle?->permanent_reg_date ? date('Y-m-d', strtotime($data->assignment->vehicle->permanent_reg_date)) : '' }}"  placeholder="Enter Permanent Registration Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_expiry_date">Registration Certificate Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="reg_certificate_expiry_date" id="reg_certificate_expiry_date" value="{{ $data->assignment?->vehicle?->reg_certificate_expiry_date ? date('Y-m-d', strtotime($data->assignment->vehicle->reg_certificate_expiry_date)) : '' }}" placeholder="Enter Registration Certificate Expiry Date" >
                            </div>
                        </div>
                        
                                                
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hsrp_certificate_attachment">HSRP Copy Attachment</label>
                                <input type="File" class="form-control bg-white" name="hsrp_certificate_attachment" id="hsrp_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hsrp_certificate_Image')">
                            </div>
                        </div>
          
                                  <?php
                            // $image_hsrp = !empty($data->assignment->vehicle->hsrp_copy_attachment)
                            //     ? asset("EV/asset_master/hsrp_certificate_attachments/{$data->assignment->vehicle->hsrp_copy_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            $hsrpAttachment = $data->assignment->vehicle->hsrp_copy_attachment ?? '';
                            $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                            $hsrpFilePath = !empty($hsrpAttachment)
                                ? asset("EV/asset_master/hsrp_certificate_attachments/{$hsrpAttachment}")
                                : '';
                        
                            $isHsrpPDF = $hsrpFilePath && \Illuminate\Support\Str::endsWith($hsrpAttachment, '.pdf');
                            $hsrpImageSrc = (!$hsrpFilePath || $isHsrpPDF) ? $defaultImage : $hsrpFilePath;
                            ?>
                        <div class="col-md-12 mb-4 my-4">
                              <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                              
                                 <img id="hsrp_certificate_Image"
                                     src="{{ $hsrpImageSrc }}"
                                     alt="HSRP Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isHsrpPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $hsrpImageSrc }}')">
                        
                                <iframe id="hsrp_certificate_PDF"
                                        src="{{ $isHsrpPDF ? $hsrpFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isHsrpPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>

                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_attachment">Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="reg_certificate_attachment" id="reg_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'reg_certificate_Image')">
                            </div>
                        </div>
                        <?php
                            // $image_src2 = !empty($data->assignment->vehicle->reg_certificate_attachment)
                            //     ? asset("EV/asset_master/reg_certificate_attachments/{$data->assignment->vehicle->reg_certificate_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                                
                                
                        $regAttachment = $data->assignment->vehicle->reg_certificate_attachment ?? '';
                        $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                        $regFilePath = !empty($regAttachment)
                            ? asset("EV/asset_master/reg_certificate_attachments/{$regAttachment}")
                            : '';
                    
                        $isRegPDF = $regFilePath && \Illuminate\Support\Str::endsWith($regAttachment, '.pdf');
                        $regImageSrc = (!$regFilePath || $isRegPDF) ? $defaultImage : $regFilePath;
                                                ?>
          
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                               
                                <img id="reg_certificate_Image"
                                     src="{{ $regImageSrc }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isRegPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $regImageSrc }}')">
                        
                                <iframe id="reg_certificate_PDF"
                                        src="{{ $isRegPDF ? $regFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isRegPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_expiry_date">Fitness Certificate Expiry Date</label>
                                <input type="date" 
                                   class="form-control bg-white" 
                                   name="fc_expiry_date" 
                                   id="fc_expiry_date" 
                                   value="{{ $data->assignment?->vehicle?->fc_expiry_date ? date('Y-m-d', strtotime($data->assignment->vehicle->fc_expiry_date)) : '' }}"
                                   >

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_attachment">Fitness Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="fc_attachment" id="fc_attachment_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'fc_attachment_Image')">
                            </div>
                        </div>
                        <?php
                            // $image_src3 = !empty($data->assignment->vehicle->fc_attachment)
                            //     ? asset("EV/asset_master/fc_attachments/{$data->assignment->vehicle->fc_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            
                            
                    $fcAttachment = $data->assignment->vehicle->fc_attachment ?? '';
                    $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                    $fcFilePath = !empty($fcAttachment)
                        ? asset("EV/asset_master/fc_attachments/{$fcAttachment}")
                        : '';
                
                    $isFcPDF = $fcFilePath && \Illuminate\Support\Str::endsWith($fcAttachment, '.pdf');
                    $fcImageSrc = (!$fcFilePath || $isFcPDF) ? $defaultImage : $fcFilePath;
                            ?>
                         
                        <div class="col-md-12 mb-4 my-4">
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
                       <img id="fc_attachment_Image"
                         src="{{ $fcImageSrc }}"
                         alt="Fitness Certificate Attachment"
                         class="img-fluid rounded shadow border"
                         style="max-height: 300px; object-fit: cover; {{ $isFcPDF ? 'display: none;' : '' }}"
                         onclick="OpenImageModal('{{ $fcImageSrc }}')">
            
                    <iframe id="fc_attachment_PDF"
                            src="{{ $isFcPDF ? $fcFilePath : '' }}"
                            style="width: 100%; height: 100%; {{ !$isFcPDF ? 'display: none;' : '' }} border: none;"
                            frameborder="0"></iframe>
                                        </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="servicing_dates">Servicing Dates</label>
                                <input type="text" class="form-control bg-white" name="servicing_dates" id="servicing_dates"   value="{{$data->assignment->vehicle->servicing_dates ?? ''}}" placeholder="Enter Servicing Dates">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <label class="input-label mb-2 ms-1 d-block">Road Tax Applicable</label>
                            <select class="form-control bg-white" name="road_tax_applicable" id="road_tax_applicable">
                             <option value="yes" {{ (isset($data->assignment->vehicle->road_tax_applicable) && $data->assignment->vehicle->road_tax_applicable == 'yes') ? 'selected' : '' }}>Yes</option>
                             <option value="no" {{ (isset($data->assignment->vehicle->road_tax_applicable) && $data->assignment->vehicle->road_tax_applicable == 'no') ? 'selected' : '' }}>No</option>
                            </select>
                          </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_amount">Road Tax Amount</label>
                                <input type="text" class="form-control bg-white" name="road_tax_amount" id="road_tax_amount"  value="{{$data->assignment->vehicle->road_tax_amount ?? ''}}" placeholder="Enter Road Tax Amount">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_renewal_frequency">Road Tax Renewal Frequency</label>
                                <input type="text" class="form-control bg-white" name="road_tax_renewal_frequency" id="road_tax_renewal_frequency"  value="{{$data->assignment->vehicle->road_tax_renewal_frequency ?? ''}}"  placeholder="Enter Road Tax Frequency">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="next_renewal_date">If Yes Road Tax Next Renewal Date</label>
                                <input type="date" class="form-control bg-white" name="next_renewal_date" id="next_renewal_date"  value="{{$data->assignment->vehicle->road_tax_next_renewal_date ?? ''}}" placeholder="Enter If Yes Road Tax Next Renewal Date">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                         </div>
                     
                     
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_type">Battery Type</label>
                           
                                    <select name="battery_type" id="battery_type" class="form-control bg-white">
                                    <option value="">Select</option>
                                    <option value="1" {{ (isset($data->assignment->vehicle->battery_type) && $data->assignment->vehicle->battery_type == 1) ? 'selected' : '' }}>Self-Charging</option>
                                    <option value="2" {{ (isset($data->assignment->vehicle->battery_type) && $data->assignment->vehicle->battery_type == 2) ? 'selected' : '' }}>Portable</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_variant_name">Battery Variant Name</label>
                                <input type="text" class="form-control bg-white" name="battery_variant_name" id="battery_variant_name"  value="{{$data->assignment->vehicle->battery_variant_name ?? ''}}" placeholder="Enter Battery Variant Name" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no">Battery Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no" id="battery_serial_no"  value="{{$data->assignment->vehicle->battery_serial_no ?? ''}}" placeholder="Enter Battery Serial Number - Original" readonly>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement1">Battery Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement1" id="battery_serial_no_replacement1"  value="{{$data->assignment->vehicle->battery_serial_number1 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement2">Battery Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement2" id="battery_serial_no_replacement2"  value="{{$data->assignment->vehicle->battery_serial_number2 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement3">Battery Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement3" id="battery_serial_no_replacement3"  value="{{$data->assignment->vehicle->battery_serial_number3 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement4">Battery Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement4" id="battery_serial_no_replacement4"  value="{{$data->assignment->vehicle->battery_serial_number4 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement5">Battery Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement5" id="battery_serial_no_replacement5"  value="{{$data->assignment->vehicle->battery_serial_number5 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 5">
                            </div>
                        </div>
                     
                         <div class="col-md-6 mb-3">
                             </div>
                             
                             
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_variant_name">Charger Variant Name</label>
                                <!--<input type="text" class="form-control bg-white" name="charger_variant_name" id="charger_variant_name"  value="{{$data->assignment->vehicle->charger_variant_name ?? ''}}" placeholder="Enter Charger Variant Name" >-->
                                  <select name="charger_variant_name" id="charger_variant_name" class="form-control bg-white">
                                    <option value="">Select</option>
                                   <option value="ABC" {{ (isset($data->assignment->vehicle->charger_variant_name) && $data->assignment->vehicle->charger_variant_name == 'ABC') ? 'selected' : '' }}>ABC</option>
                                    <option value="XYZ" {{ (isset($data->assignment->vehicle->charger_variant_name) && $data->assignment->vehicle->charger_variant_name == 'XYZ') ? 'selected' : '' }}>XYZ</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no">Charger Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no" id="charger_serial_no"  value="{{$data->assignment->vehicle->charger_serial_no ?? ''}}" placeholder="Enter Charger Serial Number - Original" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement1">Charger Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement1" id="charger_serial_no_replacement1" value="{{$data->assignment->vehicle->charger_serial_number1 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement2">Charger Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement2" id="charger_serial_no_replacement2"  value="{{$data->assignment->vehicle->charger_serial_number2 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement3">Charger Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement3" id="charger_serial_no_replacement3"  value="{{$data->assignment->vehicle->charger_serial_number3 ?? ''}}"  placeholder="Enter Charger Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement4">Charger Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement4" id="charger_serial_no_replacement4"  value="{{$data->assignment->vehicle->charger_serial_number4 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement5">Charger Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement5" id="charger_serial_no_replacement5"  value="{{$data->assignment->vehicle->charger_serial_number5 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 5">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_variant_name">Telematics Variant Name</label>
                                <input type="text" class="form-control bg-white" name="telematics_variant_name" id="telematics_variant_name"  value="{{$data->assignment->vehicle->telematics_variant_name ?? ''}}" placeholder="Enter Telematics Variant Name" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_oem">Telematics OEM</label>
                                <select class="form-select custom-select2-field form-control-sm" name="telematics_oem" id="telematics_oem">
                                    <option value="">Select</option>
                                    @if(isset($telematics))
                                       @foreach($telematics as $val)
                                          <option value="{{$val->id}}" {{ ($data->assignment?->vehicle?->telematics_oem ?? null) == $val->id ? 'selected' : '' }} >{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no"> Telematics Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no" id="telematics_serial_no"  value="{{$data->assignment->vehicle->telematics_serial_no ?? ''}}" placeholder="Enter Telematics Serial Number - Original" readonly>
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_imei_no"> Telematics IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="telematics_imei_no" id="telematics_imei_no"  value="{{$data->assignment->vehicle->telematics_imei_number ?? ''}}" placeholder="Enter Telematics IMEI Number">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement1"> Telematics Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement1" id="telematics_serial_no_replacement1"  value="{{$data->assignment->vehicle->telematics_serial_number1 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement2"> Telematics Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement2" id="telematics_serial_no_replacement2"  value="{{$data->assignment->vehicle->telematics_serial_number2 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement3"> Telematics Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement3" id="telematics_serial_no_replacement3"  value="{{$data->assignment->vehicle->telematics_serial_number3 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement4"> Telematics Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement4" id="telematics_serial_no_replacement4"  value="{{$data->assignment->vehicle->telematics_serial_number4 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement5"> Telematics Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement5" id="telematics_serial_no_replacement5"  value="{{$data->assignment->vehicle->telematics_serial_number5 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 5">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client">Client Name</label>
                                <input type="text" class="form-control bg-white" name="client" id="client"      value="{{ optional($data->assignment?->vehicle?->customer_relation)->name ?? $data->assignment?->vehicle?->client ?? '' }}"  placeholder="Enter Client" >
                            </div>
                        </div>
                        
                        
                                                
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                <input type="date" class="form-control bg-white" name="vehicle_delivery_date" id="vehicle_delivery_date"  value="{{ $data->assignment?->vehicle?->vehicle_delivery_date ? date('Y-m-d', strtotime($data->assignment->vehicle->vehicle_delivery_date)) : '' }}"  placeholder="Enter Vehicle Delivery Date" >
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                                <!--<input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="{{$data->assignment->vehicle->vehicle_status ?? ''}}" placeholder="Enter Vehicle Status" >-->
                                
                                <select class="form-select custom-select2-field form-control-sm" name="vehicle_status" id="vehicle_status" disabled>  
                                    <option value="">Select</option>
                                    @if(isset($inventory_locations))
                                       @foreach($inventory_locations as $val)
                                         
                                          <option value="{{$val->id}}" {{ ($current_status ?? null) == $val->id ? 'selected' : '' }} >{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                
               
                    </div>
                    </form>
                </div>
                </div>
            </div>
            
            
        </div>
            
        </div>
        

       <!--Image Preview Section-->
    
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
    
    
             
   
@section('script_js')


<script>
function OpenImageModal(img_url) {
    $("#kyc_image").attr("src", ""); // Clear image first
    $("#BKYC_Verify_view_modal").modal('show'); // Corrected selector
    $("#kyc_image").attr("src", img_url); // Load new image
}

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
@endsection
</x-app-layout>
