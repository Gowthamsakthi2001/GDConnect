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
                    Service In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.admin.service_request.list') }}" 
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
        // Status configuration for colors and labels
        $statusConfig = [
            'open' => ['label' => 'Open', 'color' => '#DC2626'],
            'assigned' => ['label' => 'Assigned', 'color' => '#2563EB'],
            'work_in_progress' => ['label' => 'Work In Progress', 'color' => '#0EA5E9'],
            'spare_requested' => ['label' => 'Spare Requested', 'color' => '#F59E0B'],
            'spare_approved' => ['label' => 'Spare Approved', 'color' => '#10B981'],
            'spare_collected' => ['label' => 'Spare Collected', 'color' => '#059669'],
            'estimate_requested' => ['label' => 'Estimate Requested', 'color' => '#8B5CF6'],
            'estimate_approved' => ['label' => 'Estimate Approved', 'color' => '#22C55E'],
            'closed' => ['label' => 'Closed', 'color' => '#6B7280'],
        ];

        // Sort logs by created_at ascending (oldest first)
        $sortedLogs = $data->logs->sortBy('created_at');
    @endphp

    <div class="d-flex flex-row flex-nowrap gap-3 overflow-auto"
         style="white-space: nowrap; overflow-x: scroll; -ms-overflow-style: none; scrollbar-width: none;">

        @foreach($sortedLogs as $log)
            @php
                $status = $log->current_status;
                $config = $statusConfig[$status] ?? ['label' => ucfirst($status), 'color' => '#6B7280'];
            @endphp

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
        @endforeach
    </div>
</div>

            
            </div>

           <div class="row">
            <!-- Vehicle No -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="vehicle_no">Vehicle No <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="vehicle_no" id="vehicle_no" value="{{$data->assignment->vehicle->permanent_reg_number ?? ''}}" placeholder="Enter Vehicle No" readonly>
                </div>
            </div>
        
            <!-- City -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="city">City <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="city" id="city" value="{{$data->assignment->VehicleRequest->city->city_name ?? ''}}" placeholder="Enter City" readonly>
                </div>
            </div>
        
            <!-- Zone -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="zone">Zone <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="zone" id="zone" value="{{$data->assignment->VehicleRequest->zone->name ?? ''}}" placeholder="Enter Zone" readonly>
                </div>
            </div>
        
        
            <!-- Vehicle Type -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type <span style="color:red;">*</span></label> 
                    <input type="text" class="form-control bg-white" name="vehicle_type" id="vehicle_type" value="{{$data->assignment->vehicle->vehicle_type_relation->name ?? ''}}" placeholder="Vehicle Type" readonly>
                </div>
            </div>
        
            <!-- POC Name -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="poc_name">Customer Name <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="poc_name" id="poc_name" value="{{$data->poc_name ?? ''}}" placeholder="POC Name" readonly>
                </div>
            </div>
        
            <!-- Contact No -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="contact_no">Customer Contact No <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="contact_no" id="contact_no" value="{{$data->poc_number ?? ''}}" placeholder="Contact No" readonly>
                </div>
            </div>
            
            
            <!-- Driver Name -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="driver_name">Driver Name <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="driver_name" id="driver_name" value="{{$data->driver_name ?? ''}}" placeholder="Driver Name" readonly>
                </div>
            </div>
        
        
            <!-- Driver Contact NO -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="driver_contact">Driver Contact No <span style="color:red;">*</span></label>
                    <input type="text" class="form-control bg-white" name="driver_contact" id="driver_contact" value="{{$data->driver_number ?? ''}}" placeholder="Driver Contact No" readonly>
                </div>
            </div>
            
            <!-- Description -->
            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="description">Description <span style="color:red;">*</span></label>
                    <textarea class="form-control bg-white" name="description" id="description" rows="8" placeholder="Enter Description" readonly>{{$data->description ?? ''}}</textarea>
                </div>
            </div>
        
            <!-- Repair Type -->
            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1">Repair Type <span style="color:red;">*</span></label>
                    <div class="d-flex flex-wrap gap-3">
                         @foreach($repair_types as $type)
                                    <div class="form-check">
                                        <input 
                                            class="form-check-input" 
                                            type="radio" 
                                            name="repair_type" 
                                            id="repair_type_{{ $type->id }}"  
                                            value="{{ $type->id }}" 
                                            disabled
                                            {{ isset($data->repair_type) && $data->repair_type == $type->id ? 'checked' : '' }}
                                        >
                                        <label 
                                            class="form-check-label" 
                                            style="font-weight:400;font-size:14px;" 
                                            for="repair_type_{{ $type->id }}">
                                            {{ $type->name }}
                                        </label>
                                    </div>
                                @endforeach
                    </div>
                </div>
            </div>
        
        
                    <div class="row">
                        
                             <div class="col-12 mb-3">
                                <label for="location" class="form-label">Location (GPS Pin)</label>
                                <input type="text" id="location" name="gps_pin_address" value="{{$data->gps_pin_address ?? ''}}" class="form-control mb-2" placeholder="Search location here...">
                                <input type="hidden" id="latitude" name="latitude" value="{{ $data->latitude ?? '' }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ $data->longitude ?? '' }}">
                                <input type="hidden" id="full_address" value="{{ $data->gps_pin_address ?? '' }}">
                                <div id="map" style="width: 100%; height: 250px;"></div>
                            </div>
                        </div>
                        

        
        
            <!-- Address -->
            <!--<div class="col-md-12 mb-3">-->
            <!--    <div class="form-group">-->
            <!--        <label class="input-label mb-2 ms-1" for="address">Address <span style="color:red;">*</span></label>-->
            <!--        <textarea class="form-control bg-white" name="address" id="address" rows="6" placeholder="Enter Address">No. 23, Anna Nagar 2nd Street, Chennai, Tamil Nadu - 600040</textarea>-->
            <!--    </div>-->
            <!--</div>-->
        </div>


        </div>
    </div>
</div>
            
        </div>
        

    
    
             
   
@section('script_js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&libraries=places&callback=initMap" async defer></script>
       
      <script>
    let map, marker, autocomplete;

    function initMap() {
        // Default center (India)
        const defaultLocation = { lat: 20.5937, lng: 78.9629 };

        // Get existing values from hidden inputs
        let existingLat = parseFloat(document.getElementById("latitude").value);
        let existingLng = parseFloat(document.getElementById("longitude").value);

        // If no existing values, fallback to default
        const center = (!isNaN(existingLat) && !isNaN(existingLng))
            ? { lat: existingLat, lng: existingLng }
            : defaultLocation;

        map = new google.maps.Map(document.getElementById("map"), {
            center: center,
            zoom: (!isNaN(existingLat) && !isNaN(existingLng)) ? 15 : 5,
        });

        // If existing values exist, set marker
        if (!isNaN(existingLat) && !isNaN(existingLng)) {
            setMarker(center);
        }

        // Autocomplete setup
        const input = document.getElementById("location");
        autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo("bounds", map);

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            const location = place.geometry.location;
            const lat = location.lat();
            const lng = location.lng();

            map.setCenter(location);
            map.setZoom(15);
            setMarker({ lat, lng });
            updateLatLngInputs(lat, lng);
            document.getElementById("full_address").value = place.formatted_address;
        });

        // On map click
        map.addListener("click", function (e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            setMarker({ lat, lng });
            updateLatLngInputs(lat, lng);
            getAddressFromLatLng(lat, lng);
        });
    }

    function setMarker(position) {
        if (marker) marker.setMap(null);

        marker = new google.maps.Marker({
            position,
            map,
            draggable: true,
        });

        marker.addListener("dragend", function () {
            const lat = this.getPosition().lat();
            const lng = this.getPosition().lng();
            updateLatLngInputs(lat, lng);
            getAddressFromLatLng(lat, lng);
        });
    }

    function updateLatLngInputs(lat, lng) {
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
    }

    function getAddressFromLatLng(lat, lng) {
        const geocoder = new google.maps.Geocoder();
        const latlng = { lat, lng };

        geocoder.geocode({ location: latlng }, (results, status) => {
            if (status === "OK" && results[0]) {
                document.getElementById("location").value = results[0].formatted_address;
                document.getElementById("full_address").value = results[0].formatted_address;
            }
        });
    }
</script>
@endsection
</x-app-layout>
