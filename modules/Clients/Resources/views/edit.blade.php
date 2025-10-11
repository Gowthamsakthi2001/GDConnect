<x-app-layout>
    <style>
        #map {
            width: 100%;
            height: 400px;
        }
        @media (max-width: 768px) {
            #map {
                height: 300px;
            }
        }
        @media (max-width: 576px) {
            #map {
                height: 250px;
            }
        }
</style>
<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <img src="{{asset('admin-assets/icons/custom/green-city.png')}}" class="img-fluid rounded"><span class="ps-2">Edit Client</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <!-- Assuming you are passing a $Client object to this view -->
                    <form action="{{ route('admin.Green-Drive-Ev.clients.update', $Client->id) }}" method="post" class="row g-3 p-3">
                    @csrf

                    <div class="form-group col-md-6">
                        <label for="client_name">Client Name:</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name', $Client->client_name) }}" required placeholder="Ex: john">
                    </div>
                    
                    <!-- Client Zone -->
                    <div class="form-group col-md-6">
                        <label for="client_zone">Client Zone:</label>
                        <select class="form-control basic-single" id="client_zone" name="client_zone">
                            @foreach($zones as $data)
                                <option value="{{ $data->id }}" {{ old('client_zone', $Client->client_zone) == $data->id ? 'selected' : '' }}>
                                    {{ $data->name }}
                                </option>
                            @endforeach 
                        </select>
                    </div>
                    
                    <!-- Client Location -->
                    <div class="form-group col-md-6">
                        <label for="client_location">Client Location:</label>
                        <input type="text" class="form-control" id="client_location" name="client_location" value="{{ old('client_location', $Client->client_location) }}" placeholder="Ex: Banglore" required>
                    </div>
                    
                    <!-- Hub Name -->
                    <div class="form-group col-md-6">
                        <label for="hub_name">Hub Name:</label>
                        <input type="text" class="form-control" id="hub_name" name="hub_name" value="{{ old('hub_name', $Client->hub_name) }}" placeholder="Ex: Hub Name" required>
                    </div>
                     <div class="form-group col-md-12">
                                <label class="input-label mb-2 ms-1" >Search Here</label>
                                <input id="searchInput" class="form-control mb-2" type="text" placeholder="Search for a location">
                                <div id="map" style="height: 250px;"></div>
                            </div>
                             <input type="hidden" name="client_coordinate" id="zoneInput">
                
                    <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                        <button type="submit" class="btn btn-success btn-round">Update Client</button>
                    </div>
                    
                </form>

                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
  <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_KEY')}}&libraries=drawing,geometry,places"></script>
    @if(isset($json) && $json != "[]")
    <script>
        let map;
        let drawingManager;
        let allowedZone = null;

        function initMap() {
            // Initialize the map
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 21.0000, lng: 78.0000 }, // Geographical center of India
                zoom: 5, // Adjust zoom level as needed
            });


            // Initialize the polygon for India
let indiaCoordinates = @json($json); // Assuming $json is a valid array in your controller

// Ensure that indiaCoordinates is an array
if (typeof indiaCoordinates === 'string') {
    indiaCoordinates = JSON.parse(indiaCoordinates); // Parse it if it's a string
}

if (Array.isArray(indiaCoordinates)) {
    // Initialize the polygon for India
    allowedZone = new google.maps.Polygon({
        paths: indiaCoordinates,
        map: map,
        editable: true,
        draggable: true,
        fillColor: '#00FF00',
        fillOpacity: 0.4,
        strokeWeight: 2,
    });

    // Fit map to the polygon
    const bounds = new google.maps.LatLngBounds();
    indiaCoordinates.forEach(coord => bounds.extend(coord));
    map.fitBounds(bounds);
} else {
    console.error("India coordinates are not in the expected format!");
}


            // Store the polygon coordinates in hidden input
            updateZoneInput(allowedZone);

            // Add listeners for polygon modifications
            google.maps.event.addListener(allowedZone.getPath(), 'set_at', () => updateZoneInput(allowedZone));
            google.maps.event.addListener(allowedZone.getPath(), 'insert_at', () => updateZoneInput(allowedZone));

            // Initialize drawing manager
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: ['polygon'],
                },
                polygonOptions: {
                    fillColor: '#FF0000',
                    fillOpacity: 0.4,
                    strokeWeight: 2,
                    editable: true,
                    zIndex: 1,
                },
                drawingMode: null,
            });

            drawingManager.setMap(map);

            // Listen for new polygons
            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                if (event.type === 'polygon') {
                    const newZone = event.overlay;

                    // Remove the old polygon, if any
                    if (allowedZone) {
                        allowedZone.setMap(null);
                    }

                    // Set the new polygon
                    allowedZone = newZone;

                    // Update hidden input with the new polygon coordinates
                    updateZoneInput(newZone);

                    // Add listeners for path changes
                    google.maps.event.addListener(newZone.getPath(), 'set_at', () => updateZoneInput(newZone));
                    google.maps.event.addListener(newZone.getPath(), 'insert_at', () => updateZoneInput(newZone));
                }
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchBox = new google.maps.places.Autocomplete(searchInput);
            searchBox.bindTo('bounds', map);

            google.maps.event.addListener(searchBox, 'place_changed', function() {
                const place = searchBox.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                    new google.maps.Marker({
                        position: place.geometry.location,
                        map: map,
                    });
                } else {
                    alert("No details available for input: '" + place.name + "'");
                }
            });
        }

        // Update hidden input with polygon coordinates
        function updateZoneInput(polygon) {
            const coordinates = polygon.getPath().getArray().map(coord => ({
                lat: coord.lat(),
                lng: coord.lng(),
            }));
            document.getElementById('zoneInput').value = JSON.stringify(coordinates);
        }

        window.onload = initMap;
    </script>
    @else
     <script>
        let map;
        let drawingManager;
        let allowedZone = null;
        let searchBox;

        function initMap() {
            // Initialize the map
                const map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 21.0000, lng: 78.0000 }, // Geographical center of India
                    zoom: 5, // Adjust zoom level as needed
                });


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

                if (allowedZone) {
                    allowedZone.setMap(null);
                }

                allowedZone = newZone;

                const zonePath = newZone.getPath().getArray().map(coord => ({
                    lat: coord.lat(),
                    lng: coord.lng(),
                }));

                document.getElementById('zoneInput').value = JSON.stringify(zonePath);
            });

            // Search functionality
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
                } else {
                    alert("No details available for input: '" + place.name + "'");
                }
            });
        }

        window.onload = initMap;
        
        

        
        
    </script>
    @endif
</x-app-layout>
