@extends('components.ticket_portal.app')
@section('style_css')
<style>
    .submit-btn{
        background: #52c552;
        color:white;
    }
    .submit-btn:hover{
        background: white;
        color:#52c552;
        border: 1px solid #52c552;
    }
    
    button.submit-btn:active { 
        background: #52c552;
        color: white;
        border: 1px solid #52c552;
    }
    #map {
            width: 100%;
            height: 250px;
        }
    .is-invalid {
        border: 1px solid red !important;
    }

</style>
@endsection
@section('contents')
<div class="container-fluid px-xl-5 px-md-3">
    
    <div class="card my-4">
        <div class="card-header bg-white py-3">
            <div>
                <div class="card-title h4">Create New Ticket</div>
                <p>Fill out the form below to create a new support ticket</p>
            </div>
        </div>
        <div class="card-body p-4">
            <form id="ticketForm" class="ticket-form" autocomplete="off" enctype="multipart/form-data">

                @csrf
                <div class="row p-3 mb-3" style="background:#dcf3dc;">
                    <h5>Ticket Information</h5><br><br>
                    <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label" for="created_by">Created By</label>
                           <input type="text" class="form-control bg-white" name="created_by" id="created_by" value="Web Portal User" readonly>
                           <input type="hidden" name="form_type" value="web_portal_form">
                        </div>
                    </div>
                   <div class="col-md-6 col-12 mb-3">
                        <div class="form-group">
                            <label class="form-label" for="ticket_source">Ticket Source</label>
                           <input type="text" class="form-control bg-white" name="ticket_source" id="ticket_source" value="Web Portal" readonly>
                        </div>
                    </div>
                </div>

                    <div class="row mb-3">
                        <!-- Vehicle Number -->
                       <div class="col-md-4 col-12 mb-3">
                            <label for="vehicleNumber" class="form-label">Vehicle Number *</label>
                            <input type="text" id="vehicleNumber" name="vehicle_no" class="form-control"
                                   oninput="this.value = this.value.toUpperCase(); validateVehicleFormat(this.value);"
                                   placeholder="e.g., KA01AB1234">
                            <div class="text-danger small" id="vehicleNumberError"></div>
                        </div>
                        
                             <!-- Chassis Number -->
                        <!--<div class="col-md-4 col-12 mb-3">-->
                        <!--    <label for="chassisNumber" class="form-label">Chassis Number *</label>-->
                        <!--    <input type="text" id="chassisNumber" name="chassis_number" class="form-control"-->
                        <!--           placeholder="e.g., MH12AB3456CD789012" required>-->
                        <!--</div>-->
                        
                        <!-- Battery Number -->
                        <!--<div class="col-md-4 col-12 mb-3">-->
                        <!--    <label for="batteryNumber" class="form-label">Battery Number *</label>-->
                        <!--    <input type="text" id="batteryNumber" name="battery_number" class="form-control"-->
                        <!--           placeholder="e.g., BAT20250800123" required>-->
                        <!--</div>-->
                        
                        <!-- Telematics Number -->
                        <!--<div class="col-md-4 col-12 mb-3">-->
                        <!--    <label for="telematicsNumber" class="form-label">Telematics Number *</label>-->
                        <!--    <input type="text" id="telematicsNumber" name="telematics_number" class="form-control"-->
                        <!--           placeholder="e.g., TELGR1234567890" required>-->
                        <!--</div>-->

                        <!-- City -->
                        <div class="col-md-4 col-12 mb-3">
                            <label for="city" class="form-label">City *</label>
                            <select id="city" name="city_id" class="form-control custom-select2-field" onchange="getZones(this.value)">
                                <option value="">Select city</option>
                                 @if(isset($cities))
                                    @foreach($cities as $val)
                                    <option value="{{$val->id}}">{{$val->city_name}}</option>
                                    @endforeach
                                  @endif
                               
                            </select>
                            <div class="text-danger small" id="cityError"></div>
                        </div>
                
                        <!-- State -->
                        <div class="col-md-4 col-12 mb-3">
                            <label for="state" class="form-label">Select Zone</label>
                            <select id="state" name="area_id" class="form-control">
                                <option value="">Select Zone</option>
                            </select>
                            <div class="text-danger small" id="stateError"></div>
                        </div>
                
                        <!-- Vehicle Type -->
                        <div class="col-md-4 col-12 mb-3">
                            <label for="vehicleType" class="form-label">Vehicle Type *</label>
                            <select id="vehicleType" name="vehicle_type" class="form-control" >
                                <option value="">Select vehicle type</option>
                                @if($vehicle_types)
                                @foreach($vehicle_types as $type)
                                <option value="{{$type->id}}" {{ $type->id == 1 ? 'selected' : '' }}>{{$type->name ?? ''}}</option>
                                @endforeach
                                @endif
                            </select>
                            <div class="text-danger small" id="vehicleTypeError"></div>
                        </div>
                
                        <!-- POC Name -->
                        <div class="col-md-4 col-12 mb-3">
                            <label for="pocName" class="form-label">Driver Name *</label>
                            <input type="text" id="pocName" name="poc_name" class="form-control" placeholder="Driver name">
                            <div class="text-danger small" id="pocNameError"></div>
                        </div>
                        <!-- Contact Number -->
                        <!--<div class="col-md-4 col-6 mb-3">-->
                        <!--    <label for="contactNumber" class="form-label">Contact Number *</label>-->
                        <!--   <input type="tel" id="contactNumber" class="form-control" placeholder="10-digit mobile number"  -->
                        <!--           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10); validatePhoneFormat(this.value);">-->

                        <!--    <div class="text-danger small" id="contactNumberError"></div>-->
                        <!--</div>-->
                        <div class="col-md-4 col-12 mb-3">
                            <label for="contactNumber" class="form-label">Driver Mobile Number *</label>
                            <input type="text" id="contactNumber" name="poc_contact_no" class="form-control" placeholder="10-digit mobile number" oninput="sanitizeAndValidatePhone(this)">
                            <div class="text-danger small" id="contactNumberError"></div>
                        </div>

                    </div>
                
                    <!-- Issue Description -->
                    <div class="form-group mb-3">
                        <label for="issueDescription" class="form-label">Issue Description *</label>
                        <textarea id="issueDescription" class="form-control" name="issue_remarks" rows="4" placeholder="Describe the issue..."></textarea>
                        <div class="text-danger small" id="issueDescriptionError"></div>
                    </div>
                
                    <!-- Repair Type -->
                    <div class="form-group mb-3">
                        <label class="form-label">Repair Type *</label><br>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="repairType" id="breakdownRepair" value="1">
                            <label class="form-check-label" for="breakdownRepair">Breakdown Repair</label>
                        </div>
                    
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="repairType" id="runningRepair" value="2" >
                            <label class="form-check-label" for="runningRepair">Running Repair</label>
                        </div>
                    
                        <div class="text-danger small" id="repairTypeError"></div>
                    </div>

                
                    <!-- Address -->
                    <!--<div class="form-group mb-3">-->
                    <!--    <label for="address" class="form-label">Address *</label>-->
                    <!--    <textarea id="address" class="form-control" name="address" rows="3" placeholder="Vehicle location..." ></textarea>-->
                    <!--    <div class="text-danger small" id="addressError"></div>-->
                    <!--</div>-->
                
         
                    <div class="col-12 mb-3">
                        <label for="location" class="form-label">Location (GPS Pin)</label>
                        <input type="text" id="location" name="gps_pin_address" class="form-control mb-2" placeholder="Search location here...">
                        <input type="hidden" id="latitude" name="latitude" value="">
                        <input type="hidden" id="longitude" name="longitude" value="">
                        <input type="hidden" id="full_address">
                        <div id="map" style="width: 100%; height: 250px;"></div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="image" class="form-label">Upload Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <div class="small text-muted" id="fileInfo"></div>
                    </div>
                        
                    <br>
                    <!-- Date & Time -->
                    <div class="mb-3 text-muted">
                        <i class="fa-solid fa-calendar-days me-2"></i> Created On: 
                        <span>{{ \Carbon\Carbon::now()->format('d/m/Y, H:i:s') }}</span>
                        <input type="hidden" name="created_datetime" value="{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}">
                    </div><br>
                
                    
                    <!--<button type="button" class="btn btn-outline-secondary btn-lg w-100 mb-3 py-3" id="saveDraftBtn">-->
                    <!--   <i class="fa-regular fa-floppy-disk me-2"></i>  Save as Draft-->
                    <!--</button>-->
                    <button type="submit" class="btn submit-btn btn-lg w-100 mb-3 py-3">
                      <i class="fa-brands fa-telegram me-2"></i>  Submit Ticket
                    </button>
                    
               

            
             </form>
        </div>
     
    </div>
</div>

<!--<form action="/submit-location" method="POST" id="locationForm">-->
<!--  @csrf-->

<!--  <input type="hidden" name="latitude" id="latitude">-->
<!--  <input type="hidden" name="longitude" id="longitude">-->

<!--  <button type="submit">Submit Location</button>-->
<!--</form>-->
@endsection
@section('script_js')
<!-- Google Maps API -->
   <script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&libraries=places&callback=initMap" async defer></script>
   
    <!-- Location Picker plugin -->
   <!-- <script src="https://rawgit.com/Logicify/jquery-locationpicker-plugin/master/dist/locationpicker.jquery.js"></script>-->
        
        
    <script>
       
        
        // Remove error when typing or changing
        $('#city, #state, #vehicleType, #pocName, #issueDescription, #address').on('input change', function () {
            $(this).removeClass('is-invalid');
            const errorId = '#' + $(this).attr('id') + 'Error';
            $(errorId).text('');
        });
        
        // For radio buttons (repairType)
        $('input[name="repairType"]').on('change', function () {
            $('input[name="repairType"]').removeClass('is-invalid');
            $('#repairTypeError').text('');
        });


        function validateVehicleFormat(value) {
            const pattern = /^[A-Z]{2}\d{2}[A-Z]{2}\d{4}$/;
            if (!pattern.test(value)) {
                $("#vehicleNumber").addClass('is-invalid');
                $("#vehicleNumberError").text('Invalid format. Use: KA01AB1234');
                return false;
            } else {
                $("#vehicleNumberError").text('');
                $("#vehicleNumber").removeClass('is-invalid');
                return true;
            }
        }
        
        // $('#contactNumber').on('input', function () {
        //     let value = $(this).val().replace(/\D/g, '').slice(0, 10); 
        //     $(this).val(value); 

        //     const pattern = /^\d{10}$/;

        //     if (!pattern.test(value)) {
        //         $(this).addClass('is-invalid');
        //         $('#contactNumberError').text('Enter a valid 10-digit mobile number');
        //     } else {
        //         $(this).removeClass('is-invalid');
        //         $('#contactNumberError').text('');
        //     }
        // });


      $('#ticketForm').submit(function (e) {
        e.preventDefault(); // Stop default form submit
        let isValid = true;
        let firstErrorField = null;
    
        $('.text-danger').text('');
        $('.form-control, input, select, textarea').removeClass('is-invalid');
    
        const vehicleNumber = $('#vehicleNumber').val().trim();
        const city = $('#city').val();
        const state = $('#state').val();
        const vehicleType = $('#vehicleType').val();
        const pocName = $('#pocName').val().trim();
        const contactNumber = $('#contactNumber').val().trim();
        const issueDescription = $('#issueDescription').val().trim();
        // const address = $('#address').val().trim();
        const repairType = $('input[name="repairType"]:checked').val();

        if (!vehicleNumber) {
            $('#vehicleNumberError').text('Vehicle number is required');
            $('#vehicleNumber').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#vehicleNumber';
            isValid = false;
        } else if (!validateVehicleFormat(vehicleNumber)) {
            $('#vehicleNumber').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#vehicleNumber';
            isValid = false;
        }
    
        if (!city) {
            $('#cityError').text('City is required');
            $('#city').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#city';
            isValid = false;
        }
    
        if (!state) {
            $('#stateError').text('State is required');
            $('#state').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#state';
            isValid = false;
        }
    
        if (!vehicleType) {
            $('#vehicleTypeError').text('Vehicle type is required');
            $('#vehicleType').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#vehicleType';
            isValid = false;
        }
    
        if (!pocName) {
            $('#pocNameError').text('POC name is required');
            $('#pocName').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#pocName';
            isValid = false;
        }
    
        if (!contactNumber || contactNumber.length < 10) {
            $('#contactNumberError').text('Valid contact number is required');
            $('#contactNumber').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#contactNumber';
            isValid = false;
        }
    
        if (!issueDescription) {
            $('#issueDescriptionError').text('Issue description is required');
            $('#issueDescription').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = '#issueDescription';
            isValid = false;
        }
    
        if (!repairType) {
            $('#repairTypeError').text('Repair type is required');
            $('input[name="repairType"]').addClass('is-invalid');
            if (!firstErrorField) firstErrorField = 'input[name="repairType"]';
            isValid = false;
        }
    
        // if (!address) {
        //     $('#addressError').text('Address is required');
        //     $('#address').addClass('is-invalid');
        //     if (!firstErrorField) firstErrorField = '#address';
        //     isValid = false;
        // }

        if (!isValid && firstErrorField) {
            $(firstErrorField).focus();
            return;
        }
        const formData = new FormData(this);
        formData.append('repairType', repairType);
    
        $.ajax({
            url: "{{route('admin.web.vehicle-ticket.store')}}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('.submit-btn').prop('disabled', true).text('Submitting...');
            },
            success: function (response) {
               
                const ticketSubmittedUrl = "{{ route('vehicle.ticket.submitted') }}";
                if(response.success) {
                    $('#ticketForm')[0].reset();
                    toastr.success(response.message);
                    const encodedTicketId = btoa(response.ticket_id); // base64 encode
                    setTimeout(function(){
                        window.location.href = ticketSubmittedUrl + '?token=' + encodeURIComponent(encodedTicketId);
                    },1000);
                    
                } else {
                    toastr.error('Ticket creation failed. please try again');
                }
            },
            error: function (xhr) {
                 if (xhr.status === 422) { // Fix: Use xhr.status instead of xhr.statusCode
                    var errors = xhr.responseJSON.errors; // Get errors from response
                    $.each(errors, function(key, value) { // Loop through each error message
                        toastr.error(value[0]); // Display the first error message for each field
                    });
                } else {
                    toastr.error("Please try again.");
                }

            },
            complete: function () {
                $('.submit-btn').prop('disabled', false).html('<i class="fa-brands fa-telegram me-2"></i> Submit Ticket');
            }
        });
    });


    </script>
    
    <script>
        // alert("cdljjkj");
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
  
    //  function get_state(city_id) {
    //             let formData = {
    //                 id: city_id,
    //             };

    //             if (city_id == "") {
    //                 $("#state").html(
    //                     '<option value="">Select an State</option><option value="">Data Not Found</option>');
    //             } else {

    //                 $.ajax({
    //                     url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}',
    //                     method: 'GET',
    //                     data: formData,
    //                     success: function(response) {
    //                         if (response.status) {
    //                             $("#state").empty();
    //                             if (response.areas && response.areas.length > 0) {
    //                                 var option = '<option value="">Select an State</option>';
    //                                 response.areas.forEach(function(area) {
    //                                     option += '<option value="' + area.id + '">' + area.Area_name +
    //                                         '</option>';
    //                                 });

    //                                 $("#state").html(option);
    //                             } else {
    //                                 $("#state").html(
    //                                     '<option value="">Select an State</option><option value="">Data Not Found</option>'
    //                                     );
    //                             }
    //                         } else {
    //                             toastr.error(response.message || 'An error occurred.');
    //                             $("#state").html(
    //                                 '<option value="">Select an State</option><option value="">Data Not Found</option>'
    //                                 );
    //                         }
    //                     },
    //                     error: function(xhr) {
    //                         console.error(xhr);
    //                         $("#state").html(
    //                             '<option value="">Select an State</option><option value="">Data Not Found</option>'
    //                             );
    //                     }
    //                 });
    //             }

    //         }
            
//   window.onload = function() {
//     if (navigator.geolocation) {
//       navigator.geolocation.getCurrentPosition(function(position) {
//         document.getElementById('latitude').value = position.coords.latitude;
//         document.getElementById('longitude').value = position.coords.longitude;
//       }, function(error) {
//         alert('Location access denied or not available.');
//       });
//     } else {
//       alert('Browser geolocation support pannala.');
//     }
//   }



    function getZones(CityID) {
        let ZoneDropdown = $('#state');
    
        ZoneDropdown.empty().append('<option value="">Loading...</option>');
    
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">Select Zone</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                        });
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
        }
    }
    
    
    function sanitizeAndValidatePhone(input) {
            // Ensure the input starts with '+91' and lock the first 3 characters to '+91'
            if (!input.value.startsWith('+91')) {
                input.value = '+91' + input.value.replace(/^\+?91/, ''); // Ensure it starts with "+91"
            }
    
            // Allow only digits after '+91'
            input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, ''); 
    
            // Limit the total length to 13 characters (including '+91')
            if (input.value.length > 13) {
                input.value = input.value.substring(0, 13);
            }
    }
</script>


@endsection