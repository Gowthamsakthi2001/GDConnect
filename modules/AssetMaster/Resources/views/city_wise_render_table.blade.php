
@if(isset($city_table_data) && isset($vehicle_types))

<div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
    <table id="CityWise_Table" class="table text-center table-bordered bg-light mb-0">

        <thead class="sticky-top" style="top: 0; z-index: 2;">
            <tr>
                <th rowspan="2" class="text-dark" style="background-color:#f0f0f0 !important;">
                    <small>City</small>
                </th>

                <th colspan="{{ count($vehicle_types) }}" class="text-dark" style="background-color:#f0f0f0 !important;">
                    <small>On Road</small>
                </th>

                <th colspan="{{ count($vehicle_types) }}" class="text-dark" style="background-color:#f0f0f0 !important;">
                    <small>Off Road</small>
                </th>

                <th rowspan="2" class="text-dark" style="background-color:#f0f0f0 !important;">
                    <small>Total Assets</small>
                </th>
            </tr>

            <tr>
                @foreach($vehicle_types as $type)
                    <th class="text-dark" style="background-color:#f0f0f0 !important;">
                        <small>{{ $type->name }}</small>
                    </th>
                @endforeach

                @foreach($vehicle_types as $type)
                    <th class="text-dark" style="background-color:#f0f0f0 !important;">
                        <small>{{ $type->name }}</small>
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
                @php
                    $grandOnRoad = [];
                    $grandOffRoad = [];
                    $grandTotal = 0;
                    $indexed = $city_table_data->groupBy(['city', 'vehicle_type']);
                @endphp
                
                @forelse($indexed as $city => $types)
                <tr>
                    <td><small>{{ $city }}</small></td>
                    @foreach($vehicle_types as $type)
                        @php
                            $row = $types[$type->id][0] ?? null;
                            $onroad = $row->onroad_count ?? 0;
                            $grandOnRoad[$type->id] = ($grandOnRoad[$type->id] ?? 0) + $onroad;
                        @endphp
                        <td><small>{{ $onroad }}</small></td>
                    @endforeach
                    @foreach($vehicle_types as $type)
                        @php
                            $row = $types[$type->id][0] ?? null;
                            $total = $row->total_assets ?? 0;
                            $onroad = $row->onroad_count ?? 0;
                            $offroad = abs($total - $onroad);
                
                            $grandOffRoad[$type->id] = ($grandOffRoad[$type->id] ?? 0) + $offroad;
                        @endphp
                        <td><small>{{ $offroad }}</small></td>
                    @endforeach
                
                    @php
                        $cityTotal = collect($types)->flatten()->sum('total_assets');
                        $grandTotal += $cityTotal;
                    @endphp
                    <td><small>{{ $cityTotal }}</small></td>
                </tr>
                
                @empty
                <tr>
                    <td colspan="{{ 2 + (count($vehicle_types) * 2) }}">
                        <small>No data available</small>
                    </td>
                </tr>
                @endforelse
                
                <tr style="font-weight:bold;">
                    <td style="background-color:#f0f0f0 !important;"><small>Total</small></td>
                
                    @foreach($vehicle_types as $type)
                        <td style="background-color:#f0f0f0 !important;">
                            <small>{{ number_format($grandOnRoad[$type->id] ?? 0) }}</small>
                        </td>
                    @endforeach
                
                    @foreach($vehicle_types as $type)
                        <td style="background-color:#f0f0f0 !important;">
                            <small>{{ number_format($grandOffRoad[$type->id] ?? 0) }}</small>
                        </td>
                    @endforeach
                
                    <td style="background-color:#f0f0f0 !important;">
                        <small>{{ number_format($grandTotal) }}</small>
                    </td>
                </tr>
                </tbody>


    </table>
</div>
@else
<p class="text-center text-muted"><small>No data found</small></p>
@endif
