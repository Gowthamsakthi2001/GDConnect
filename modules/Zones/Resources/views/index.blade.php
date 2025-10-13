<x-app-layout>
<style>
        #map {
            width: 100%;
            height: 300px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-control{
                padding: 12px 20px !important;
        }
    </style>

    <div class="main-content">

        <div class="card bg-transparent my-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3">
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="card-title h5 custom-dark m-0"> <a
                                href="{{route('admin.Green-Drive-Ev.zone.list')}}"
                                class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Create a Zone
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card my-3">

            <div class="card-body pt-3">
                <form action="javascript:void(0);" method="POST" id="zoneForm">
                    @csrf

                    <div class="row">
                        <!-- Left side -->
                        <div class="col-md-4">

                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">State <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select custom-select2-field form-control-sm" 
                                        name="state" id="state" onchange="getCities(this.value)">
                                    <option value="">Select</option>
                                    @if(isset($states))
                                        @foreach($states as $val)
                                            <option value="{{$val->id}}">{{$val->state_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">City <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select custom-select2-field form-control-sm" name="city" id="city">
                                    <option value="">Select a state first</option>
                                </select>
                            </div>


                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">Zone Name <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control" name="zone_name" id="zone_name"
                                       placeholder="">
                                <small id="zone_name_feedback" class="text-danger"></small>
                            </div>


                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">Latitude <span class="text-danger fw-bold">*</span></label>
                                <input type="number" class="form-control" name="latitude" id="lat" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">Longitude <span class="text-danger fw-bold">*</span></label>
                                <input type="number" class="form-control" name="longitude" id="long" readonly>
                            </div>
                        </div>

                        <!-- Right side -->
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="input-label mb-2 ms-1">Search Here <span class="text-danger fw-bold">*</span></label>
                                <input id="searchInput" name="search_address" class="form-control mb-2" type="text"
                                    placeholder="Search for a location">
                                <div id="map" style="height: 370px; border-radius: 8px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons Row -->
                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-success" id="ZoneCreateBtn">Save Zone</button>
                        </div>
                    </div>

                    <input type="hidden" name="zone" id="zoneInput">
                </form>
            </div>
        </div>




    </div>
    
    <?php
      $apiKey = \App\Models\BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
    ?>

    @section('script_js')
     <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{$apiKey}}&libraries=drawing,geometry,places"></script>

    <script>
        let map;
        let drawingManager;
        let allowedZone = null;
        let searchBox;
        let clickMarker = null; // store marker for click
        
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 13.010456533174729, lng: 77.93829395599366 },
                zoom: 8,
            });
        
            // Drawing manager
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['polygon'],
                },
                polygonOptions: {
                    fillColor: '#00FF00',
                    fillOpacity: 0.4,
                    strokeWeight: 2,
                    editable: true,
                    zIndex: 1,
                },
                drawingMode: null
            });
        
            drawingManager.setMap(map);
        
            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                const newZone = event.overlay;
                newZone.setEditable(false);
                drawingManager.setDrawingMode(null);
        
                if (allowedZone) allowedZone.setMap(null);
                allowedZone = newZone;
        
                const zonePath = newZone.getPath().getArray().map(coord => ({
                    lat: coord.lat(),
                    lng: coord.lng(),
                }));
        
                document.getElementById('zoneInput').value = JSON.stringify(zonePath);
            });
        
            // Search autocomplete
            const input = document.getElementById('searchInput');
            searchBox = new google.maps.places.Autocomplete(input);
            searchBox.bindTo('bounds', map);
        
            google.maps.event.addListener(searchBox, 'place_changed', function() {
                const place = searchBox.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
        
                    const marker = new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                    });
        
                    document.getElementById('lat').value = place.geometry.location.lat();
                    document.getElementById('long').value = place.geometry.location.lng();
                } else {
                    alert("No details available for input: '" + place.name + "'");
                }
            });
        
            // ✅ Map click listener
            map.addListener('click', function(event) {
                const clickedLocation = event.latLng;
        
                if (clickMarker) clickMarker.setMap(null);
        
                clickMarker = new google.maps.Marker({
                    position: clickedLocation,
                    map: map,
                });
        
                document.getElementById('lat').value = clickedLocation.lat();
                document.getElementById('long').value = clickedLocation.lng();
        
                // Optional: reverse geocode for address
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: clickedLocation }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('searchInput').value = results[0].formatted_address;
                        map.setCenter(clickedLocation);
                    }
                });
            });
        }
        
        window.onload = initMap;

    </script>

    <script>
        
        function getCities(stateId) {
            let cityDropdown = $('#city');
             cityDropdown.append('<option value="">Loading...</option>');
            cityDropdown.empty(); // clear old options
             
            if (stateId) {
                $.ajax({
                    url: "{{ route('global.get_cities', ':state_id') }}".replace(':state_id', stateId),
                    type: "GET",
                    success: function (response) {
                        cityDropdown.append('<option value="">Select</option>');
        
                        if (response.data && response.data.length > 0) {
                            $.each(response.data, function (key, city) {
                                cityDropdown.append('<option value="' + city.id + '">' + city.city_name + '</option>');
                            });
                        } else {
                            cityDropdown.append('<option value="">No cities available for this state</option>');
                        }
                    },
                    error: function () {
                        cityDropdown.append('<option value="">Error loading cities</option>');
                    }
                });
            } else {
                cityDropdown.append('<option value="">Select a state first</option>');
            }
        }

        $('#zone_name').on('keyup change', function () {
            let zoneName = $(this).val();
            let zoneId = '';
        
            if (zoneName.length > 0) {
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.zone.check-exist') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        zone_name: zoneName,
                        zone_id : zoneId
                    },
                    success: function (response) {
                        if (response.exists) {
                            $('#zone_name_feedback')
                                .text(response.message)
                                .removeClass('text-success')
                                .addClass('text-danger');
                        } else {
                            $('#zone_name_feedback')
                                .text(response.message)
                                .removeClass('text-danger')
                                .addClass('text-success');
                        }
                    }
                });
            } else {
                $('#zone_name_feedback').text('');
            }
        });

        

       $("#zoneForm").submit(function (e) {
            e.preventDefault();
        
            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append("_token", "{{ csrf_token() }}");
        
            var $submitBtn = $("#ZoneCreateBtn");
            var originalText = $submitBtn.html();
            $submitBtn.prop("disabled", true).html("⏳ Submitting...");
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.zone.save-zones') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $submitBtn.prop("disabled", false).html(originalText);

                    if (response.success) {
                        // ✅ Proper form reset
                        $("#zoneForm")[0].reset();
                        $('#zone_name_feedback').text('').removeClass('text-danger') .removeClass('text-success');

                        Swal.fire({
                            icon: 'success',
                            title: 'Created!',
                            html: response.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal-wide'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // ✅ Redirect to zone list
                                window.location.href = "{{ route('admin.Green-Drive-Ev.zone.list') }}";
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $submitBtn.prop("disabled", false).html(originalText);
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error("Please try again.");
                    }
                }
            });
        });


    </script>
    @endsection
</x-app-layout>