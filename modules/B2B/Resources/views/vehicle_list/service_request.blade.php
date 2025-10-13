    @extends('layouts.b2b')
    @section('css')
    @endsection
    
    
    @section('content')
    <div class="main-content">
    
            <!-- Header Section -->
            <div class="mb-4">
                <div class="p-3 rounded" style="background:#fbfbfb;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <!-- Title -->
                        <h5 class="m-0 text-truncate custom-dark">
                            Service Request
                        </h5>
                        
            
                        <!-- Back Button -->
                        <a href="{{ route('b2b.vehiclelist') }}" 
                           class="btn btn-dark btn-md mt-2 mt-md-0">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            
            <form id="serviceRequestForm" action="{{ route('b2b.service_request_functionality') }}" method="POST" enctype="multipart/form-data">
                @csrf
    
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                             
                             <input name="assign_id" type="hidden" value="{{$data->id}}">
                              <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="vehicle_number">Vehicle No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{$data->vehicle->permanent_reg_number ?? ''}}" style="padding:12px 20px;" placeholder="Enter Vehicle No" readonly>
                                </div>
                            </div>
    
    
    
                          <!--<div class="col-md-4 mb-3">-->
                          <!--      <div class="form-group">-->
                          <!--          <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>-->
                          <!--          <select class="form-control custom-select2-field" disabled>-->
                          <!--              <option value="">Select</option>-->
                          <!--              @if($vehicle_types)-->
                          <!--              @foreach($vehicle_types as $type)-->
                          <!--              <option value="{{$type->id}}" {{ $type->id == $data->vehicle->vehicle_type ? 'selected' : '' }}>{{$type->name}}</option>-->
                          <!--              @endforeach-->
                          <!--              @endif-->
                          <!--          </select>-->
                          <!--      </div>-->
                          <!--  </div>-->
                            
                            
                              <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="city">City<span class="text-danger">*</span></label>
                                  <select class="form-control custom-select2-field" name="city" id="city">
                                        <option value="">Select City</option>
                                         @if(isset($cities))
                                            @foreach($cities as $val)
                                            <option value="{{$val->id}}" selected>{{$val->city_name}}</option>
                                            @endforeach
                                          @endif
                                    </select>
                                </div>
                            </div>

                            
                              <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="zone">zone<span class="text-danger">*</span></label>
                                  <select class="form-control custom-select2-field" name="zone" id="zone" >
                                        <option value="">Select Zone</option>
                                        @if(isset($zones))
                                            @foreach($zones as $val)
                                            <option value="{{$val->id}}" {{ isset($data->vehicleRequest) && $data->vehicleRequest->zone_id == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                            @endforeach
                                          @endif
                                    </select>
                                </div>
                            </div>
                            
                            <!--<div class="col-md-4 mb-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="state">State<span class="text-danger">*</span></label>-->
                            <!--        <input type="text" class="form-control bg-white" name="state" id="state"  placeholder="Enter State">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
    
                            
                        </div>
                        
                        
                         <div class="row">
                             

    
    
                            <!--  <div class="col-md-4 mb-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="poc_number">POC Number<span class="text-danger">*</span></label>-->
                            <!--        <input type="text" class="form-control bg-white" name="poc_number" id="poc_number"  placeholder="Enter POC Number">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                            <!--<div class="col-md-4 mb-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label class="input-label mb-2 ms-1" for="contact_number">Contact No<span class="text-danger">*</span></label>-->
                            <!--        <input type="text" class="form-control bg-white phone-input" name="contact_number" id="contact_number"  placeholder="Enter Contact Number">-->
                            <!--    </div>-->
                            <!--</div>-->
                            
    
                            
                        </div>
                        
                        

                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Repair Type <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-4 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="repair_type" id="repair_breakdown" value="1" required>
                                        <label class="form-check-label" for="repair_breakdown">
                                            Breakdown Repair
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="repair_type" id="repair_running" value="2" required>
                                        <label class="form-check-label" for="repair_running">
                                            Running Repair
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <!--<div class="row">-->
                        <!--     <div class="col-md-12 mb-3">-->
                        <!--        <div class="form-group">-->
                        <!--            <label class="input-label mb-2 ms-1" for="address">Address<span class="text-danger">*</span></label>-->
                        <!--            <textarea class="form-control bg-white" name="address" id="address" rows="6" placeholder="Enter Location address where location is..."></textarea>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        

                    
                          <div class="row">
                        
                             <div class="col-12 mb-3">
                                <label for="location" class="form-label">Location (GPS Pin)</label>
                                <input type="text" id="location" name="gps_pin_address" class="form-control mb-2" placeholder="Search location here...">
                                <input type="hidden" id="latitude" name="latitude" value="">
                                <input type="hidden" id="longitude" name="longitude" value="">
                                <input type="hidden" id="full_address">
                                <div id="map" style="width: 100%; height: 250px;"></div>
                            </div>
                        </div>
                        
                        <!--<div class="row">-->
                        <!--     <div class="col-12 mb-3">-->
                        <!--        <label for="image" class="form-label">Upload Image</label>-->
                        <!--        <input type="file" id="image" name="image" class="form-control" accept="image/*">-->
                        <!--        <div class="small text-muted" id="fileInfo"></div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        
                        <div class="row">
                             <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="description">Description<span class="text-danger">*</span></label>
                                    <textarea class="form-control bg-white" name="description" id="description" rows="6" placeholder="Enter Description"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                            <!-- Button -->
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger btn-back me-2 ">Reset</button>
                                    <button type="button" id="submitBtn"  class="btn btn-primary btn-submit ">Submit</button> 
                            </div>
                        </div>
                        
                     
                       
                </div>
                </div>
                
            </form>    
    
    
    </div>
    

    @endsection
    
    @section('js')
     <!--Google Maps API -->
       <script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&libraries=places&callback=initMap" async defer></script>
       
       <script>
           
            let map, marker, autocomplete;
            
            function initMap() {
                // Initialize the map centered on some default world location (so it loads)
                const defaultLocation = { lat: 20.5937, lng: 78.9629 }; // center of India
                map = new google.maps.Map(document.getElementById("map"), {
                    center: defaultLocation,
                    zoom: 5,
                });
            
                // Initially clear inputs
                document.getElementById("latitude").value = "";
                document.getElementById("longitude").value = "";
                document.getElementById("location").value = "";
                document.getElementById("full_address").value = "";
            
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
      
      
          //phone number 
          function sanitizeAndValidatePhone(input) {
           
            if (!input.value.startsWith('+91')) {
                input.value = '+91' + input.value.replace(/^\+?91/, '');
            }
        
            input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, '');
        
            if (input.value.length > 13) {
                input.value = input.value.substring(0, 13);
            }
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".phone-input").forEach(function(input) {
                input.addEventListener("input", function() {
                    sanitizeAndValidatePhone(this);
                });
            });
        });
          
          
          
$(document).ready(function () {
    $('#submitBtn').on('click', function () {
        $('#serviceRequestForm').submit();
    });

    $('#serviceRequestForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);
        let btn = $('#submitBtn');

        // Change button text & disable
        btn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Service request submitted successfully!',
                    showConfirmButton: false,
                    timer: 3000
                });

                form.trigger("reset");

                setTimeout(() => {
                    window.location.href = "{{ route('b2b.vehiclelist') }}";
                }, 1500);
            },
            error: function (xhr) {
                let errors = xhr.responseJSON?.errors;
                let message = "Something went wrong!";
                if (errors) {
                    message = Object.values(errors).flat().join('<br>');
                }

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: message,
                    showConfirmButton: false,
                    timer: 4000
                });
            },
            complete: function () {
                // Reset button text & enable
                btn.prop('disabled', false).text('Submit');
            }
        });
    });
});


            function get_zone(city_id) {
                let formData = {
                    id: city_id,
                };

                if (city_id == "") {
                    $("#zone").html(
                        '<option value="">Select an Zone</option><option value="">Data Not Found</option>');
                } else {

                    $.ajax({
                        url: '{{ route('b2b.get_zones') }}',
                        method: 'GET',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                $("#zone").empty();
                                if (response.zones && response.zones.length > 0) {
                                    var option = '<option value="">Select an Zone</option>';
                                    response.zones.forEach(function(zone) {
                                        option += '<option value="' + zone.id + '">' + zone.name +
                                            '</option>';
                                    });

                                    $("#zone").html(option);
                                } else {
                                    $("#zone").html(
                                        '<option value="">Select an Zone</option><option value="">Data Not Found</option>'
                                        );
                                }
                            } else {
                                toastr.error(response.message || 'An error occurred.');
                                $("#zone").html(
                                    '<option value="">Select an Zone</option><option value="">Data Not Found</option>'
                                    );
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            $("#zone").html(
                                '<option value="">Select an Zone</option><option value="">Data Not Found</option>'
                                );
                        }
                    });
                }

            }

       </script>
    @endsection