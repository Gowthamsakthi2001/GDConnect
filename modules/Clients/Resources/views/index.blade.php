<x-app-layout>
    <style>
        #map {
            width: 100%;
            height: 250px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
    </style>
<div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <img src="{{asset('admin-assets/icons/custom/green-city.png')}}" class="img-fluid rounded"><span class="ps-2">Add Client</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{ route('admin.Green-Drive-Ev.clients.store') }}" method="post" class="row g-3 p-3">
                        @csrf
                        
                       
                            <div class="form-group col-md-6">
                                <label for="client_name">Client Name:</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required placeholder="Ex: john">
                            </div>
                            
                            <!-- Client Zone -->
                            <div class="form-group col-md-6">
                                <label for="client_zone">Client Zone:</label>
                                <select class="form-control basic-single" id="client_zone" name="client_zone">
                                    @foreach($zones as $data)
                                        <option value="{{ $data->id }}" {{ old('client_zone') == $data->id ? 'selected' : '' }}>
                                            {{ $data->name }}
                                        </option>
                                    @endforeach 
                                </select>
                            </div>
                            
                            <!-- Client Location -->
                            <div class="form-group col-md-6">
                                <label for="client_location">Client Location:</label>
                                <input type="text" class="form-control" id="client_location" name="client_location" placeholder="Ex: Banglore" required>
                            </div>
                            
                            <!-- Hub Name -->
                            <div class="form-group col-md-6">
                                <label for="hub_name">Hub Name:</label>
                                <input type="text" class="form-control" id="hub_name" name="hub_name" placeholder="Ex: Hub Name">
                            </div>
                            
                            <div class="form-group col-md-12">
                                <label class="input-label mb-2 ms-1" >Search Here</label>
                                <input id="searchInput" class="form-control mb-2" type="text" placeholder="Search for a location">
                                <div id="map" style="height: 250px;"></div>
                            </div>
                             <input type="hidden" name="client_coordinate" id="zoneInput">

                        <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                            <!--<button type="reset" class="btn btn-round text-white px-4 custom-bg-color">Reset</button>-->
                            <button type="submit" class="btn btn-success btn-round">Create Client</button>
                        </div>
                        
                    </form>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>

 <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_KEY')}}&libraries=drawing,geometry,places"></script>

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

</x-app-layout>