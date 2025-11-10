    <!-- Top Metrics Row -->
                <div class="row g-3 mb-3 mt-1">
    @php
        $metrics = [
            [
                'title' => 'Total RFD Count',
                'data' => $rfd_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' =>'#A1DBD0'
            ],
            [
                'title' => 'Total Deployed Count',
                'data' => $deploy_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' =>'#D8E4FE'
            ],
            [
                'title' => 'Total Returned Count',
                'data' => $return_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' =>'#EEE9CA'
            ],
            [
                'title' => 'Total Client Tickets',
                'data' => ['current'=>0,'change_percent'=>0],
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' =>'#FFC1BE'
            ]
        ];
    @endphp

    @foreach($metrics as $metric)
        @php
            $change = $metric['data']['change_percent'] ?? 0;
            $current = $metric['data']['current'] ?? $metric['data'];
            $isPositive = $change >= 0;
            $arrowColor = $isPositive ? $metric['color_up'] : $metric['color_down'];
        @endphp

        <div class="col-md-6 col-lg-3 col-sm-6">
            <div class="metric-card bg-white hover-card" style="border-top:6px solid {{ $metric['top-border'] }};">
                <div class="metric-header">
                    <span >{{ $metric['title'] }}</span>
                </div>
                <div class="metric-value">
                    <span>{{ $current }}</span>
                    <!--<span class="metric-badge {{ $isPositive ? 'metric-up' : 'metric-down' }}">-->
                    <!--    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">-->
                    <!--        @if($isPositive)-->
                                <!-- Upward green arrow -->
                    <!--            <path d="M12.5 8.125V5H9.375" stroke="{{ $arrowColor }}" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>-->
                    <!--            <path d="M12.5 5L9.375 8.125C8.82337 8.67663 8.54762 8.95237 8.20912 8.98287C8.15312 8.98794 8.09688 8.98794 8.04088 8.98287C7.70238 8.95237 7.42663 8.67663 6.875 8.125C6.32337 7.57337 6.04759 7.29763 5.70911 7.26713C5.65315 7.26206 5.59685 7.26206 5.54089 7.26713C5.20241 7.29763 4.9266 7.57337 4.375 8.125L2.5 10" stroke="{{ $arrowColor }}" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>-->
                    <!--        @else-->
                                <!-- Downward red arrow -->
                    <!--            <path d="M2.5 6.875V10H5.625" stroke="{{ $arrowColor }}" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>-->
                    <!--            <path d="M2.5 10L5.625 6.875C6.17663 6.32337 6.45237 6.04762 6.79087 6.01712C6.84687 6.01205 6.90312 6.01205 6.95912 6.01712C7.29762 6.04762 7.57337 6.32337 8.125 6.875C8.67663 7.42662 8.95241 7.70237 9.29089 7.73287C9.34685 7.73794 9.40315 7.73794 9.45911 7.73287C9.79759 7.70237 10.0734 7.42662 10.625 6.875L12.5 5" stroke="{{ $arrowColor }}" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>-->
                    <!--        @endif-->
                    <!--    </svg>-->
                    <!--    {{ $isPositive ? '+' : '' }}{{ $change }}%-->
                    <!--</span>-->
                    </div>
                    <!--<div class="metric-date"></div>-->
                <!--<div class="metric-date">From {{ $start_date_formatted }} to {{ $end_date_formatted }}</div>-->
            </div>
        </div>
    @endforeach
</div>


  
  
  