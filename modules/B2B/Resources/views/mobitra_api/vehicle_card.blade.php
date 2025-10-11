<!--<div class="card vehicle-card position-relative"-->
<!--     data-vehicle-id="{{ $vehicle['vehicleNumber'] }}"-->
<!--     data-vehicle-status="{{ strtolower($vehicle['vehicleStatus']) }}"-->
<!--     data-imei-number="{{ $vehicle['IMEINumber'] }}"-->
<!--     data-latitude="{{ $vehicle['latitude'] }}"-->
<!--     data-longitude="{{ $vehicle['longitude'] }}"-->
<!--     data-vehicle-type="{{ $vehicle['vehicleType'] }}"-->
<!--     data-last-updated="{{ $vehicle['lastDbTime'] }}"-->
<!--     data-last-speed="{{ $vehicle['lastSpeed'] }}"-->
<!--     data-distance-travelled="{{ $vehicle['distanceTravelled'] }}"-->
<!--     data-battery="{{ $vehicle['battery'] }}"-->
<!--     data-roleName="{{ $vehicle['roleName'] }}"-->
<!--     data-prRoleName="{{ $vehicle['prRoleName'] }}">-->

<!--    <span class="status-badge -->
<!--        @if($vehicle['vehicleStatus'] == 'running') status-running-->
<!--        @elseif($vehicle['vehicleStatus'] == 'stopped') status-stopped-->
<!--        @else status-offline @endif">-->
<!--        {{ ucfirst($vehicle['vehicleStatus']) }}-->
<!--    </span>-->

<!--    <div class="card-body">-->
<!--        <div class="d-flex justify-content-between align-items-center mb-2">-->
<!--            <h6 class="mb-0" style="font-size:12px">{{ $vehicle['vehicleNumber'] }}</h6>-->
<!--        </div>-->
<!--        <p class="text-muted small mb-2" style="font-size:12px">-->
<!--            Type: {{ $vehicle['vehicleType'] }} | -->
<!--            Last Updated On: {{ date('d M Y, H:i:s', $vehicle['lastDbTime'] / 1000000000) }}-->
<!--        </p>-->
<!--        <div class="d-flex justify-content-between mb-2">-->
<!--            <span class="px-2 py-1 {{ number_format($vehicle['lastSpeed'], 2) > 0.00 ? 'status-speed' : 'bg-white text-secondary border border-dark' }}" style="font-size:12px;border-radius:6.4px;">-->
<!--                Speed: {{ number_format($vehicle['lastSpeed'], 2) }} Km/h-->
<!--            </span>-->
<!--            <span class="px-2 py-1 {{ number_format($vehicle['distanceTravelled'], 2) > 0.00 ? 'status-distance' : 'bg-white text-secondary border border-dark' }}" style="font-size:12px;border-radius:6.4px;">-->
<!--                Today Distance: {{ number_format($vehicle['distanceTravelled'], 2) }} Km-->
<!--            </span>-->
<!--        </div>-->
<!--        <div class="d-flex justify-content-between align-items-center">-->
<!--            <button class="btn btn-link p-0 text-decoration-none text-primary view-location-btn" style="font-size:12px">-->
<!--                <img height=16 width=18 src="{{ asset('admin-assets/img/chevron_left.svg') }}"> View Vehicle Current Location-->
<!--            </button>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="card vehicle-card position-relative"
     data-vehicle-id="{{ $vehicle->permanent_reg_number }}"
     data-imei-number="{{ $vehicle->telematics_imei_number }}"
     data-vehicle-type="{{ $vehicle->vehicle_type_name }}"
     data-vehicle-model="{{ $vehicle->vehicle_model }}">
    <span class="status-badge status-running d-flex align-items-center">
            Running
            </span>
            
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0" style="font-size:12px">
                {{ $vehicle->permanent_reg_number }}
            </h6>
        </div>

        <p class="text-muted small mb-2" style="font-size:12px">
            Type: {{ $vehicle->vehicle_type_name }} |
            Model: {{ $vehicle->vehicle_model }}
        </p>

        <p class="text-muted small mb-2" style="font-size:12px">
            IMEI: {{ $vehicle->telematics_imei_number }}
        </p>

        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-link p-0 text-decoration-none text-primary view-location-btn"
                    data-imei="{{ $vehicle->telematics_imei_number }}"
                    data-vehicle="{{ $vehicle->permanent_reg_number }}"
                    style="font-size:12px">
                <img height="16" width="18" src="{{ asset('admin-assets/img/chevron_left.svg') }}">
                View Vehicle Current Location
            </button>
        </div>
    </div>
</div>





