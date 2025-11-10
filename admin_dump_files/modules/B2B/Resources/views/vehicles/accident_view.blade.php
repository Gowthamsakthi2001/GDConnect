@extends('layouts.b2b')
 
 @section('css')   
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
            border-bottom: 1px solid rgba(0,0,0,0.07);
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

@section('content')

    <div class="main-content">
        <div class="">
        <div class="p-3 rounded" style="background:#fbfbfb;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <!-- Title -->
                <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
                    Accident In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.accident.list') }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
            
        </div>
    </div>
   
     
            <div>
    <div class="card">
        <div class="card-body">
           <div class="row">
    <div class="col-12">
    <div class="d-flex overflow-auto gap-3 pb-2 mb-2" 
         style="white-space: nowrap; overflow-x: scroll; -ms-overflow-style: none; scrollbar-width: none;">

        @php
            // Ordered list of statuses
            $statusConfig = [
                'claim_initiated'       => ['label' => 'Claimed Initiated', 'color' => '#580F0F'],
                'insurer_visit_confirmed' => ['label' => 'Insurer Visit Confirmed', 'color' => '#58490F'],
                'inspection_completed'    => ['label' => 'Inspection Completed', 'color' => '#56580F'],
                'approval_pending'        => ['label' => 'Approval Pending', 'color' => '#1E580F'],
                'repair_started'          => ['label' => 'Repair Started', 'color' => '#0F5847'],
                'repair_completed'        => ['label' => 'Repair Completed', 'color' => '#0F4858'],
                'invoice_submitted'       => ['label' => 'Invoice Submitted', 'color' => '#1A0F58'],
                'payment_approved'        => ['label' => 'Payment Approved', 'color' => '#580F4B'],
                'claim_closed'            => ['label' => 'Claim Closed (Settled)', 'color' => '#584F0F'],
            ];
        @endphp

        <div class="d-flex flex-row flex-nowrap gap-3 overflow-auto" style="white-space: nowrap; overflow-x: scroll; -ms-overflow-style: none; scrollbar-width: none;">
            @foreach($statusConfig as $status => $config)
                @php
                    // Find matching log from $data->logs
                    $log = $data->logs->firstWhere('status', $status);
                @endphp

                @if($log)
                    <div class="card shadow-sm border-0 flex-shrink-0" style="min-width:220px;">
                        <div class="p-3 rounded" style="border:1px solid {{ $config['color'] }};">
                            <div class="mb-1" style="font-weight:500; font-size:14px; color:{{ $config['color'] }};">
                                {{ $config['label'] }}
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                                {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

</div>


            
    <div class="row">
        <!-- Date and Time of the Request -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Date and Time of the Request</label>
                <input type="datetime-local" class="form-control bg-white" value="{{ $data->datetime ?? date('Y-m-d\TH:i') }}" disabled>
            </div>
        </div>

        <!-- Location Of Accident -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Location Of Accident</label>
                <input type="text" class="form-control bg-white" value="{{ $data->location_of_accident ?? '' }}" disabled>
            </div>
        </div>

        <!-- Accident Type -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Accident Type</label>
                <input type="text" class="form-control bg-white" value="{{ $data->accident_type ?? '' }}" disabled>
            </div>
        </div>

        <!-- Description -->
        <div class="col-md-12 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Description</label>
                <textarea class="form-control bg-white" rows="6" disabled>{{ $data->description ?? '' }}</textarea>
            </div>
        </div>

        <!-- Vehicle & Rider Info -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Vehicle No.</label>
                <input type="text" class="form-control bg-white" value="{{ $data->vehicle_number ?? '' }}" disabled>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Chassis Number</label>
                <input type="text" class="form-control bg-white" value="{{ $data->chassis_number ?? '' }}" disabled>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Rider Name</label>
                <input type="text" class="form-control bg-white" value="{{ $data->rider->name ?? '' }}" disabled>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Rider Contact Number</label>
                <input type="text" class="form-control bg-white" value="{{ $data->rider->mobile_no ?? '' }}" disabled>
            </div>
        </div>

        @php
            $licenseNumber = $data->rider->driving_license_number 
                ?? $data->rider->llr_number 
                ?? null;
        
            $label = !empty($data->rider->driving_license_number)
                ? 'Driving License Number'
                : (!empty($data->rider->llr_number)
                    ? 'LLR Number'
                    : '');
        @endphp


        @if($licenseNumber)
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1">{{ $label }}</label>
                    <input type="text" class="form-control bg-white" value="{{ $licenseNumber }}" disabled>
                </div>
            </div>
        @endif

        <!-- Damage -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Damage to Vehicle</label>
                <input type="text" class="form-control bg-white" value="{{ $data->vehicle_damage ?? '' }}" disabled>
            </div>
        </div>

        <!-- Rider Injuries -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Any Rider Injuries</label>
                <input type="text" class="form-control bg-white" value="{{ $data->rider_injury_description ? 'Yes':'No' }}" disabled>
            </div>
        </div>

        @if(!empty($data->rider_injury_description))
            <div class="col-md-12 mb-3">
                <label class="input-label mb-2 ms-1">Rider Injury Description</label>
                <textarea class="form-control bg-white" rows="4" disabled>{{ $data->rider_injury_description }}</textarea>
            </div>
        @endif

        <!-- Third Party Injuries -->
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="input-label mb-2 ms-1">Any Third Party Injuries</label>
                <input type="text" class="form-control bg-white" value="{{ $data->third_party_injury_description ? 'Yes':'No'}}" disabled>
            </div>
        </div>

        @if(!empty($data->third_party_injury_description))
            <div class="col-md-12 mb-3">
                <label class="input-label mb-2 ms-1">Third Party Injury Description</label>
                <textarea class="form-control bg-white" rows="4" disabled>{{ $data->third_party_injury_description ?? '' }}</textarea>
            </div>
        @endif

        <!-- Accident Attachments -->
        <div class="col-md-12 mb-3">
            <label class="input-label mb-2 ms-1">Accident Photos / Videos</label>
        
            @if(!empty($data->accident_attachments))
                @php
                    $attachments = is_string($data->accident_attachments)
                        ? json_decode($data->accident_attachments, true)
                        : $data->accident_attachments;
                @endphp
        
                <div class="row g-3">
                    @foreach($attachments as $file)
                        <div class="col-12 col-sm-6 col-md-4 ">
                            <div style="width:100%; height:200px; border:2px dotted #ccc;border-radius:5px; 
                                        background-size:cover; background-position:center; 
                                        background-image:url('{{ asset('b2b/accident_reports/attachments/'.$file) }}')"
                                        onclick="OpenImageModal('{{ asset('b2b/accident_reports/attachments/'.$file) }}')">
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No accident photos/videos uploaded.</p>
            @endif
        </div>


        <!-- Police Report -->
        <div class="col-md-12 mb-3 ">
            <label class="form-label">Police Report / FIR Copy</label>
            @if(!empty($data->police_report))
                @php
                    $attachment = is_string($data->police_report)
                        ? json_decode($data->police_report, true)
                        : $data->police_report;
        
                    $fileName = $attachment['name'] ?? null;
                    $filePath = asset('b2b/accident_reports/police_reports/'.$fileName);
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                @endphp
        
                @if($extension === 'pdf')
                    <!-- Show PDF in iframe -->
                    <iframe src="{{ $filePath }}" style="width:100%; height:400px;border:2px dotted #ccc;border-radius:5px;" frameborder="0"></iframe>
                @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <!-- Show Image -->
                    <img src="{{ $filePath }}" alt="Police Report" style="width:100%; height:400px; object-fit:contain; border:2px dotted #ccc;border-radius:5px;" onclick="OpenImageModal('{{ $filePath }}')">
                @else
                    <p>Unsupported file type: {{ $extension }}</p>
                @endif
            @else
                <p>No Police Report uploaded.</p>
            @endif
        </div>

        <!-- Client Info -->
        <div class="col-md-6 mb-3">
            <label class="input-label mb-2 ms-1">Client Business Name</label>
            <input type="text" class="form-control bg-white" value="{{ $data['rider']['customerLogin']['customer_relation']['name'] ?? '' }}" disabled>
        </div>

        <div class="col-md-6 mb-3">
            <label class="input-label mb-2 ms-1">Contact Number</label>
            <input type="text" class="form-control bg-white" value="{{ $data['rider']['customerLogin']['customer_relation']['phone'] ?? '' }}" disabled>
        </div>

        <div class="col-md-6 mb-3">
            <label class="input-label mb-2 ms-1">Contact Email</label>
            <input type="text" class="form-control bg-white" value="{{ $data['rider']['customerLogin']['customer_relation']['email'] ?? '' }}" disabled>
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
@endsection

