<x-app-layout>
    
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        color: #fff;
        background-color: #ffffff;
        box-shadow: none !important;
    }
    .nav-pills .nav-link.active .head-text {
        color: #0000009c !important;
    }

/* Hide scrollbar but keep scroll functionality */
.hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;     /* Firefox */
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;             /* Chrome, Safari, Opera */
}

</style>



                            @php
                                $previousUrl = request()->headers->get('referer');
                                $type = 'pending'; // default fallback
                            
                                if ($previousUrl) {
                                    $segments = explode('/', trim(parse_url($previousUrl, PHP_URL_PATH), '/'));
                                    $last = end($segments);
                                    if (in_array($last, ['pending', 'assigned', 'work_in_progress' ,'hold' ,'closed'])) {
                                        $type = $last;
                                    }
                                }
                            @endphp

            <div class="main-content">
            
                {{-- Header --}}
                <div class="card bg-transparent my-4">
                    <div class="card-header" style="background:#fbfbfb;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center text-truncate" style="white-space: nowrap; overflow: hidden;">
                                <a href="{{ route('admin.ticket_management.list', ['type' => $type]) }}" class="btn btn-sm shadow me-2">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                                <span class="fs-6 fs-sm-5 fs-md-4 fw-semibold custom-dark text-truncate">View Ticket Information</span>
                            </div>
                            <div class="d-flex justify-content-end mt-2 mt-md-0">
                                <a href="{{ route('admin.ticket_management.list', ['type' => $type]) }}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>
                    </div>
                </div>

            
                {{-- Tabs --}}
                <div class="card my-3">
                    <div class="card-header" style="background:#d1e7dd;">
                        <div class="overflow-auto hide-scrollbar">
                            <ul class="nav nav-pills row g-0 flex-nowrap" id="pills-tab" role="tablist" style="white-space: nowrap;">
                                <li class="nav-item col-auto" role="presentation">
                                    <button class="nav-link active d-flex align-items-center justify-content-center" 
                                            id="pills-basic-tab" data-bs-toggle="pill" data-bs-target="#pills-basic" type="button" 
                                            role="tab" aria-controls="pills-basic" aria-selected="true" style="color:#495057;">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Basic Information
                                    </button>
                                </li>
                    
                                <li class="nav-item col-auto" role="presentation">
                                    <button class="nav-link d-flex align-items-center justify-content-center" 
                                            id="pills-vehicle-tab" data-bs-toggle="pill" data-bs-target="#pills-vehicle" type="button" 
                                            role="tab" aria-controls="pills-vehicle" aria-selected="false" style="color:#495057;">
                                        <i class="bi bi-truck me-1"></i>
                                        Vehicle Information
                                    </button>
                                </li>
                    
                                <li class="nav-item col-auto" role="presentation">
                                    <button class="nav-link d-flex align-items-center justify-content-center" 
                                            id="pills-technician-tab" data-bs-toggle="pill" data-bs-target="#pills-technician" type="button" 
                                            role="tab" aria-controls="pills-technician" aria-selected="false" style="color:#495057;">
                                        <i class="bi bi-person-workspace me-1"></i>
                                        Technician & Assignment
                                    </button>
                                </li>
                    
                                <li class="nav-item col-auto" role="presentation">
                                    <button class="nav-link d-flex align-items-center justify-content-center" 
                                            id="pills-service-tab" data-bs-toggle="pill" data-bs-target="#pills-service" type="button" 
                                            role="tab" aria-controls="pills-service" aria-selected="false" style="color:#495057;">
                                        <i class="bi bi-gear me-1"></i>
                                        Additional Information
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>


            
                    <div class="tab-content p-3" id="pills-tabContent">
                        
                        @php
                            $statusLabel = match($datas['ticket_status'] ?? '') {
                                'pending' => 'Pending',
                                'assigned' => 'Assigned',
                                'work_in_progress' => 'Work In Progress',
                                'hold' => 'On Hold',
                                'closed' => 'Closed',
                                default => 'Unknown',
                            };
                        @endphp

                        
                        {{-- Tab 1: Basic Info --}}
                        <div class="tab-pane fade show active" id="pills-basic" role="tabpanel" aria-labelledby="pills-basic-tab">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3"><label>Ticket ID</label><input type="text" class="form-control" readonly value="{{ $datas['greendrive_ticketid'] ?? '' }}"></div>
                                
                                <div class="col-md-4 mb-3"><label>Ticket Status</label><input type="text" class="form-control" readonly value="{{ $statusLabel }}"></div>
                                <div class="col-md-4 mb-3"><label>Created At</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['createdat']) ? date('Y-m-d\TH:i', strtotime($datas['createdat'])) : '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Updated At</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['updatedat']) ? date('Y-m-d\TH:i', strtotime($datas['updatedat'])) : '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Customer Name</label><input type="text" class="form-control" readonly value="{{ $datas['customer_name'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Customer Number</label><input type="text" class="form-control" readonly value="{{ $datas['customer_number'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>State</label><input type="text" class="form-control" readonly value="{{ $datas['state'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>City</label><input type="text" class="form-control" readonly value="{{ $datas['city'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Contact Details</label><input type="text" class="form-control" readonly value="{{ $datas['contact_details'] ?? '' }}"></div>
                                
                                 <div class="col-md-4 mb-3"><label>Current Status</label><input type="text" class="form-control" readonly value="{{ $datas['current_status'] ?? '' }}"></div>
                                 
                                
                                 
                            </div>
                        </div>
            
                        {{-- Tab 2: Vehicle Info --}}
                        <div class="tab-pane fade" id="pills-vehicle" role="tabpanel" aria-labelledby="pills-vehicle-tab">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3"><label>Vehicle Type</label><input type="text" class="form-control" readonly value="{{ $datas['vehicle_type'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Vehicle Name</label><input type="text" class="form-control" readonly value="{{ $datas['vehicle_name'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Vehicle Number</label><input type="text" class="form-control" readonly value="{{ $datas['vehicle_number'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Vehicle ID</label><input type="text" class="form-control" readonly value="{{ $datas['vehicle_id'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Chassis Number</label><input type="text" class="form-control" readonly value="{{ $datas['chassis_number'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Battery</label><input type="text" class="form-control" readonly value="{{ $datas['battery'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Category</label><input type="text" class="form-control" readonly value="{{ $datas['category'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Telematics</label><input type="text" class="form-control" readonly value="{{ $datas['telematics'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3">
                                    <label>Location</label>
                                    <input type="text" class="form-control" readonly value="{{ isset($datas['location']) ? implode(', ', $datas['location']) : '' }}">
                                </div>
                                
                            @if(!empty($datas['image']))
                                
                            <div class="col-md-4 mb-3 mt-3 mt-md-0 justify-content-center gap-2">
                                <label>Image</label>
                                @if(!empty($datas['image']))
                                    @foreach((array) $datas['image'] as $img)
                                        <div class="image-container" onclick="OpenImageModal('{{ $img }}')">
                                            <img src="{{ $img }}" class="preview-image img-fluid"
                                                 alt="Ticket Image"
                                                 style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @endif

                            </div>
                        </div>
            
                        {{-- Tab 3: Technician & Assignment Info --}}
                        <div class="tab-pane fade" id="pills-technician" role="tabpanel" aria-labelledby="pills-technician-tab">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3"><label>Assigned Technician</label><input type="text" class="form-control" readonly value="{{ $datas['assigned_technician_id'] ?? 'Not Assigned' }}"></div>
                                <div class="col-md-4 mb-3"><label>Assigned By</label><input type="text" class="form-control" readonly value="{{ $datas['assigned_by'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Assigned At</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['assigned_at']) ? date('Y-m-d\TH:i', strtotime($datas['assigned_at'])) : '' }}"></div>
                                <div class="col-md-12 mb-3"><label>Technician Notes</label><textarea class="form-control" rows="2" readonly>{{ $datas['technician_notes'] ?? '' }}</textarea></div>
                                <div class="col-md-12 mb-3"><label>Final Technician Notes</label><textarea class="form-control" rows="2" readonly>{{ $datas['final_technician_notes'] ?? '' }}</textarea></div>
                                <div class="col-md-12 mb-3"><label>Task Performed</label><textarea class="form-control" rows="2" readonly>{{ $datas['task_performed'] ?? '' }}</textarea></div>
                                <div class="col-md-4 mb-3"><label>Role</label><input type="text" class="form-control" readonly value="{{ $datas['role'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Assignment Info</label><input type="text" class="form-control" readonly value="{{ $datas['assignment_info'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Point of Contact</label><input type="text" class="form-control" readonly value="{{ $datas['point_of_contact_info'] ?? '' }}"></div>
                            </div>
                        </div>
            
                        {{-- Tab 4: Service, Location & Additional Info --}}
                        <div class="tab-pane fade" id="pills-service" role="tabpanel" aria-labelledby="pills-service-tab">
                            <div class="row g-3">
                                <div class="col-md-4 mb-3"><label>Service Type</label><input type="text" class="form-control" readonly value="{{ $datas['service_type'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Priority</label><input type="text" class="form-control" readonly value="{{ $datas['priority'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Repair Type</label><input type="text" class="form-control" readonly value="{{ $datas['repair_type'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Odometer</label><input type="number" class="form-control" readonly value="{{ $datas['odometer'] ?? '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Labour Description</label><textarea class="form-control" rows="2" readonly>{{ $datas['labour_description'] ?? '' }}</textarea></div>
                                <div class="col-md-4 mb-3"><label>Issue Description</label><textarea class="form-control" rows="2" readonly>{{ $datas['issue_description'] ?? '' }}</textarea></div>
                                <div class="col-md-6 mb-3"><label>Started At</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['started_at']) ? date('Y-m-d\TH:i', strtotime($datas['started_at'])) : '' }}"></div>
                                <div class="col-md-6 mb-3"><label>Ended At</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['ended_at']) ? date('Y-m-d\TH:i', strtotime($datas['ended_at'])) : '' }}"></div>
                                <div class="col-md-6 mb-3"><label>Started Location</label><input type="text" class="form-control" readonly value="{{ isset($datas['started_location']) ? implode(', ', $datas['started_location']) : '' }}"></div>
                                <div class="col-md-6 mb-3"><label>Ended Location</label><input type="text" class="form-control" readonly value="{{ isset($datas['ended_location']) ? implode(', ', $datas['ended_location']) : '' }}"></div>
                                <div class="col-md-12 mb-3"><label>Address</label><textarea class="form-control" rows="2" readonly>{{ $datas['address'] ?? '' }}</textarea></div>

                                <div class="col-md-4 mb-3">
                                    <label>Service Charges</label>
                                    <input type="number" class="form-control" readonly value="{{ $datas['service_charges'] ?? '' }}">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label>Job Type</label>
                                    <input type="text" class="form-control" readonly value="{{ $datas['job_type'] ?? '' }}">
                                </div>

                                <div class="col-md-4 mb-3"><label>Sync</label><input type="text" class="form-control" readonly value="{{ isset($datas['sync']) ? ($datas['sync'] ? 'Yes' : 'No') : '' }}"></div>
                                <div class="col-md-4 mb-3"><label>Last Sync</label><input type="datetime-local" class="form-control" readonly value="{{ isset($datas['lastsync']) ? date('Y-m-d\TH:i', strtotime($datas['lastsync'])) : '' }}"></div>
                                <div class="col-md-12 mb-3"><label>Observation</label><textarea class="form-control" rows="2" readonly>{{ $datas['observation'] ?? '' }}</textarea></div>
                            </div>
                        </div>
            
                    </div>
                </div>
            </div>

    
 
 
<!-- Image View Modal (unchanged) -->
<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4">
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
                <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body text-center py-6" style="overflow: auto; max-height: 80vh;">
                <img src="" id="kyc_image" style="max-width: 100%; transition: transform 0.3s ease;">
            </div>
        </div>
    </div>
</div>
   
@section('script_js')

<script>
    //functionality for image view 

 function OpenImageModal(img_url) {
    $("#kyc_image").attr("src", ""); // Clear image first
    $("#BKYC_Verify_view_modal").modal('show'); // Corrected selector
    $("#kyc_image").attr("src", img_url); // Load new image
}

let scale = 1;
let rotation = 0;

function OpenImageModal(img_url) {
    scale = 1;
    rotation = 0;
    updateImageTransform();
    $("#kyc_image").attr("src", img_url);
    $("#BKYC_Verify_view_modal").modal('show');
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
    const img = document.getElementById("kyc_image");
    img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}


</script>

@endsection
</x-app-layout>
