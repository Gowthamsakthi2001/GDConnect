@if($zones->isNotEmpty())
                <div class="zones-section mt-3">
                    <h5 class="mb-3">Cities</h5>
                    <div class="d-flex flex-row flex-nowrap " style="overflow-x: auto;-ms-overflow-style: none;scrollbar-width: none; gap: 1rem; padding-bottom: 1rem;">
                        @foreach($zones as $zone)
                            <div class="zone-card" style="min-width: 200px; flex: 0 0 auto;">
                                <div class="zone-name">{{ $zone['city_name'] }}</div>
                                <div class="zone-value">
                                    <span class="zone-count">{{ $zone['vehicle_count'] }}</span>
                                    <span class="zone-label">Vehicle{{ $zone['vehicle_count'] > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif