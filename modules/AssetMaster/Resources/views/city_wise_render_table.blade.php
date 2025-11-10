
<?php

 $city_table_data = \DB::table('vehicle_qc_check_lists as qc')
            ->select(
                'qc.location',
                'lo.city_name as city',
                'qc.vehicle_type',
                DB::raw("SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) as onroad_count"),
                DB::raw("SUM(CASE WHEN inv.transfer_status = 6 THEN 1 ELSE 0 END) as accident_case_count"),
                DB::raw("SUM(CASE WHEN inv.transfer_status = 2 THEN 1 ELSE 0 END) as undermaintanance_count"),
                DB::raw("SUM(CASE WHEN inv.transfer_status != 1 THEN 1 ELSE 0 END) as offroad_count")
            )
             ->when($accountability_type_id === 'all', function ($query) {
                $query->leftJoin('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                      ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
            }, function ($query) {
                $query->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
                      ->join('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id');
            })
            ->leftJoin('ev_tbl_city as lo', 'lo.id', '=', 'qc.location')
            ->where('vh.delete_status', 0)
            ->when($location_id != "", function ($query) use ($location_id) {
                return $query->where('qc.location', $location_id);
            })
            ->when($vehicle_model != "", function ($query) use ($vehicle_model) {
                return $query->where('qc.vehicle_model', $vehicle_model);
            })
            ->when($vehicle_type != "", function ($query) use ($vehicle_type) {
                return $query->where('qc.vehicle_type', $vehicle_type);
            })
            ->when($timeline, function ($query) use ($timeline, &$from_date, &$to_date) {
                switch ($timeline) {
                    case 'today':
                        $query->whereDate('qc.created_at', today());
                        break;
        
                    case 'this_week':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfWeek(), now()->endOfWeek()
                        ]);
                        break;
        
                    case 'this_month':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfMonth(), now()->endOfMonth()
                        ]);
                        break;
        
                    case 'this_year':
                        $query->whereBetween('qc.created_at', [
                            now()->startOfYear(), now()->endOfYear()
                        ]);
                        break;
                }
        
                // reset manual dates
                $from_date = null;
                $to_date = null;
            })
            ->when(!$timeline && $from_date, function ($query) use ($from_date) {
                $query->whereDate('qc.created_at', '>=', $from_date);
            })
            ->when(!$timeline && $to_date, function ($query) use ($to_date) {
                $query->whereDate('qc.created_at', '<=', $to_date);
            })
            ->when($accountability_type_id !== 'all', function ($query) use ($accountability_type_id) {
                $query->where('qc.accountability_type', $accountability_type_id);
            })
            ->when($customer_id !== 'all' && $accountability_type_id == 2, function ($query) use ($customer_id) {
                $query->where('qc.customer_id', $customer_id);
            })
             ->when($customer_id !== 'all' && $accountability_type_id == 1, function ($query) use ($customer_id) {
                $query->where('vh.client', $customer_id);
            })
            
            ->groupBy('qc.location', 'lo.city_name', 'qc.vehicle_type')
            ->get();
            
?>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
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
                                            <small>{{ $type->name ?? '' }}</small>
                                        </th>
                                    @endforeach
                                    @foreach($vehicle_types as $type)
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">
                                            <small>{{ $type->name ?? '' }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            
                            <tbody>
                                @php
                                    $grandOnRoad = [];
                                    $grandOffRoad = [];
                                    $grandTotal = 0;
                                @endphp
                                @foreach($city_table_data->groupBy('city') as $city => $rows)
                                        <tr>
                                            <td><small>{{ $city }}</small></td>
                                
                                            <!-- On Road -->
                                            @foreach($vehicle_types as $type)
                                                @php
                                                    $data = $rows->firstWhere('vehicle_type', $type->id);
                                                    $onroad = $data->onroad_count ?? 0;
                                                    $grandOnRoad[$type->id] = ($grandOnRoad[$type->id] ?? 0) + $onroad;
                                                @endphp
                                                <td><small>{{ $onroad }}</small></td>
                                            @endforeach
                                
                                            <!-- Off Road -->
                                            @foreach($vehicle_types as $type)
                                                @php
                                                    $data = $rows->firstWhere('vehicle_type', $type->id);
                                                    $offroad = $data->offroad_count ?? 0;
                                                    $grandOffRoad[$type->id] = ($grandOffRoad[$type->id] ?? 0) + $offroad;
                                                @endphp
                                                <td><small>{{ $offroad }}</small></td>
                                            @endforeach
                                
                                            <!-- Total Assets -->
                                            @php
                                                $totalAssets = $rows->sum('onroad_count') + $rows->sum('offroad_count');
                                                $grandTotal += $totalAssets;
                                            @endphp
                                            <td><small>{{ $totalAssets }}</small></td>
                                        </tr>
                                @endforeach
                                <tr style="font-weight:bold;">
                                    <td style="background-color:#f0f0f0 !important;"><small>Total</small></td>
                                
                                    <!-- On Road Totals -->
                                    @foreach($vehicle_types as $type)
                                        <td style="background-color:#f0f0f0 !important;">
                                            <small>{{ number_format($grandOnRoad[$type->id] ?? 0, 0, '.', ',') }}</small>
                                        </td>
                                    @endforeach
                                
                                    <!-- Off Road Totals -->
                                    @foreach($vehicle_types as $type)
                                        <td style="background-color:#f0f0f0 !important;">
                                            <small>{{ number_format($grandOffRoad[$type->id] ?? 0, 0, '.', ',') }}</small>
                                        </td>
                                    @endforeach
                                
                                    <!-- Grand Total Assets -->
                                    <td style="background-color:#f0f0f0 !important;">
                                        <small>{{ number_format($grandTotal, 0, '.', ',') }}</small>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </div>