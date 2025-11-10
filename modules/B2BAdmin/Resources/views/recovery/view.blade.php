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

    <div class="main-content">
        <div class="">
        <div class="p-3 rounded" style="background:#fbfbfb;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <!-- Title -->
                <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
                    Recovery In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.admin.recovery_request.list') }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
            
        </div>
    </div>
     
            <div>
    <div class="card">
        <div class="card-body">
           <div class="row mb-4 g-2">
                <?php
                //updated by Gowtham.S
                $Request_User = null;
                $createdByName = 'N/A';
                $roleName = 'N/A';
                $clientName = 'N/A';
            
                if ($data->created_by_type === 'b2b-admin-dashboard') {
                    // Admin user
                    $Request_User = \App\Models\User::find($data->created_by);
            
                    if ($Request_User) {
                        $createdByName = $Request_User->name ?? 'N/A';
                        $roleName = $Request_User->get_role->name ?? 'N/A';
                    }
                } elseif ($data->created_by_type === 'b2b-web-dashboard') {
                    // Client user
                    $Request_User = \Modules\MasterManagement\Entities\CustomerLogin::find($data->created_by);
                    if ($Request_User) {
                        $createdByName = $Request_User->email ?? 'N/A';
                        $clientName = $Request_User->customer_relation->trade_name ?? 'N/A';
                    }
                }
            ?>
               <div class="col-12 mb-3"> <!-- updated by Gowtham.S -->
                    <div class="alert alert-info" role="alert">
                        @if($data->created_by_type === 'b2b-admin-dashboard')
                            Requested By:  {{ $createdByName }} ({{ $roleName }})
                        @elseif($data->created_by_type === 'b2b-web-dashboard')
                            Requested By:  {{ $createdByName }} <br>
                            Client Name: {{ $clientName }}
                        @else
                            <strong>Requested By:</strong> Unknown
                        @endif
                    </div>
                </div>
                
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

            <div class="row">
            
                <!-- Vehicle No -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="vehicle_no">Vehicle No </label>
                        <input type="text" class="form-control bg-white" name="vehicle_no" id="vehicle_no" value="{{$data->vehicle_number??''}}" placeholder="Enter Vehicle No" readonly>
                    </div>
                </div>
            
                <!-- Chassis Number -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="chassis_no">Chassis Number </label>
                        <input type="text" class="form-control bg-white" name="chassis_no" id="chassis_no" value="{{$data->chassis_number??''}}" placeholder="Enter Chassis Number" readonly>
                    </div>
                </div>
                  <?php
                     $recovery_reasons = \Modules\MasterManagement\Entities\RecoveryReasonMaster::where('status',1)->get();
                  ?>
                
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="reason">Reason For Recovery </label>
                        <!--<input type="text" class="form-control bg-white" name="reason" id="reason" value="{{$data->reason??''}}" placeholder="Enter Chassis Number" readonly>-->
                        <select class="form-select " name="reason_for_recovery" id="reason_for_recovery" disabled>
                            <option value="">Select</option>
                             @if(isset($recovery_reasons) && count($recovery_reasons) > 0)
                                @foreach($recovery_reasons as $val)
                                <option value="{{$val->id}}" {{ $data->reason == $val->id ? 'selected' : '' }}>{{$val->label_name}}</option>
                                @endforeach
                            @else
                               <option value="">No Data</option>
                            @endif
                        </select>
                    </div>
                </div>
                

                
                
                <!-- Rider Name -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="rider_name">Rider Name </label>
                        <input type="text" class="form-control bg-white" name="rider_name" id="rider_name" value="{{$data->rider_name??''}}" placeholder="Enter Rider Name" readonly>
                    </div>
                </div>
                
                
                
                <!-- Client Business Name -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="client_business">Client</label>
                        <input type="text" class="form-control bg-white" name="client" id="client" value="{{$data->client_name??''}}" placeholder="Enter Client Name" readonly>
                    </div>
                </div>
            
                <!-- Contact No -->
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="contact_no">Contact No</label>
                        <input type="text" class="form-control bg-white" name="contact_no" id="contact_no" value="{{$data->contact_no??''}}" placeholder="Enter Contact No" readonly>
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
