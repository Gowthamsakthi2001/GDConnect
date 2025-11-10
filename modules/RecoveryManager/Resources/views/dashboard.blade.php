<x-app-layout>
    
@section('style_css')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
<style>
    
    .metric-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .card-metric {
          height:280px;
          color: #fff;
          border-radius: 8px;
          padding: 15px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }


    .card-metric h6 {
        font-weight: 600;
        font-size:18px;
        color:#111827;
        }
    .card-metric h2 {
         font-weight: 600;
         font-size:32px;
         color:#111827;
        }
        
    /*canvas{*/
    /*    height:140px;*/
    /*    }*/
        
    .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 400;
    }
    
    .metric-value {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 36px;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1;
    }
    
    .dashboard-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }
    @media (min-width: 992px) {
        canvas {
            height: 270px; /* example */
        }
        
        .card-metric {
          height:400px;
          color: #fff;
          border-radius: 8px;
          padding: 25px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
    }
     @media (max-width: 992px) {
        canvas{
           height: 270px;
        }
        
        .card-metric {
          height:400px;
          color: #fff;
          border-radius: 8px;
          padding: 25px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
     }
     
     @media (max-width: 768px) {
         canvas{
            height:160px;
        }
        
        .card-metric {
          height:280px;
          color: #fff;
          border-radius: 8px;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
     }
     
     @media (max-width: 576px) {
         canvas{
            height:155px;
        }
        
        .card-metric {
          height:280px;
          color: #fff;
          border-radius: 8px;
          padding: 15px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
     }
     
      @media (max-width: 330px) {
         canvas{
            height:155px;
        }
        
        .card-metric {
          height:280px;
          color: #fff;
          border-radius: 8px;
          padding: 15px;
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        
     }
     
</style>
@endsection

<div class="container-fluid p-0">
    <div class="col-12 d-flex align-items-center justify-content-between mt-3 card-header bg-white" >
    <!-- Left side: Dashboard Title -->
    <div>
        <h5 class="mb-0 card-title">Recovery Manager Dashboard</h5>
    </div>

    <!-- Right side: Export & Filter Buttons -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="bg-white p-2 px-3 border-gray rounded text-center" onclick="RightSideFilerOpen()">
            <i class="bi bi-filter fs-17"></i> Filters
        </div>
    </div>
</div>
    <div class="row g-3 mb-3 mt-1">
    @php
        $metrics = [
                    [
                'id' => 'total-count',
                'title' => 'Total Recovery Requests',
                'data' => $total_count,
                'color_up' => '#005D27', // Green for positive change
                'color_down' => '#D32F2F', // Red for negative change
                'top-border' => '#009688',
                'route' => route('admin.recovery_management.list',['type'=>'all'])
            ],
            [
                'id' => 'agent-count',
                'title' => 'Total Agents',
                'data' => $agent_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' => '#4DB6AC',
                'route' => route('admin.recovery_management.agent_list',['type'=>'all'])
            ],
            [
                'id' => 'opened-count',
                'title' => 'Total Opened Requests',
                'data' => $opened_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' => '#64B5F6',
                'route' => route('admin.recovery_management.list',['type'=>'pending'])
            ],
            [
                'id' => 'closed-count',
                'title' => 'Total Closed Requests',
                'data' => $closed_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' => '#81C784',
                'route' => route('admin.recovery_management.list',['type'=>'closed'])
            ],
            [
                'id' => 'agent-assigned-count',
                'title' => 'Total Agent Assigned Requests',
                'data' => $agent_assigned_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' => '#FFD54F',
                'route' => route('admin.recovery_management.list',['type'=>'agent-assigned'])
            ],
            [
                'id' => 'not-recovered-count',
                'title' => 'Total Not Recovered Requests',
                'data' => $not_recovered_count,
                'color_up' => '#005D27',
                'color_down' => '#D32F2F',
                'top-border' => '#E57373',
                'route' => route('admin.recovery_management.list',['type'=>'not-recovered'])
            ]
            
        ];
    @endphp

    @foreach($metrics as $metric)
       

        <div class="col-md-4 col-lg-4 col-sm-6">
            <a href="{{ $metric['route'] }}" class="text-decoration-none text-dark">
            <div class="metric-card bg-white dashboard-card" style="border-top:6px solid {{ $metric['top-border'] }};">
                <div class="metric-header">
                    <span >{{ $metric['title'] }}</span>
                </div>
                <div class="metric-value">
                    <span id="{{ $metric['id'] }}">{{ $metric['data'] }}</span>
                    </div>
            </div>
             </a>
        </div>
    @endforeach


</div>
    
    <div class="row mt-3">
        <div class="col-md-12 col-sm-12">
            <div class="card-metric bg-white dashboard-card">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 style="color:black">Recovery Request</h6>
                    <div class="d-flex align-items-center gap-2">
                    <select name="recovery_status" id="recovery-status" class="form-control" style="width:100px;">
                        <option value="">Select</option>
                        <option value="opened">Opened</option>
                        <option value="agent_assigned">Agent Assigned</option>
                        <option value="closed">Closed</option>
                        <option value="not_recovered">Not Recovered</option>
                        
                    </select>
                    <span class="text-warning span-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                <rect width="27" height="27" rx="8" fill="#A1DBD0"/>
                                <path d="M8.68754 13.5C8.68754 13.7317 8.70404 13.9641 8.73636 14.1916L7.37511 14.3862C7.33305 14.0927 7.31214 13.7965 7.31254 13.5C7.31254 10.0879 10.0887 7.3125 13.5 7.3125C14.9046 7.3125 16.2803 7.7965 17.3741 8.67444L16.5127 9.74694C15.6605 9.05723 14.5963 8.68299 13.5 8.6875C10.8463 8.6875 8.68754 10.8463 8.68754 13.5ZM8.00004 16.25C8.00004 16.4323 8.07248 16.6072 8.20141 16.7361C8.33034 16.8651 8.50521 16.9375 8.68754 16.9375C8.86988 16.9375 9.04475 16.8651 9.17368 16.7361C9.30261 16.6072 9.37504 16.4323 9.37504 16.25C9.37504 16.0677 9.30261 15.8928 9.17368 15.7639C9.04475 15.6349 8.86988 15.5625 8.68754 15.5625C8.50521 15.5625 8.33034 15.6349 8.20141 15.7639C8.07248 15.8928 8.00004 16.0677 8.00004 16.25ZM13.5 5.25C18.0492 5.25 21.75 8.95081 21.75 13.5H23.125C23.125 8.1925 18.8075 3.875 13.5 3.875C12.3705 3.875 11.2636 4.06888 10.2104 4.45181L10.6806 5.74431C11.5844 5.41651 12.5386 5.24923 13.5 5.25ZM17.625 10.75C17.625 10.9323 17.6975 11.1072 17.8264 11.2361C17.9553 11.3651 18.1302 11.4375 18.3125 11.4375C18.4949 11.4375 18.6697 11.3651 18.7987 11.2361C18.9276 11.1072 19 10.9323 19 10.75C19 10.5677 18.9276 10.3928 18.7987 10.2639C18.6697 10.1349 18.4949 10.0625 18.3125 10.0625C18.1302 10.0625 17.9553 10.1349 17.8264 10.2639C17.6975 10.3928 17.625 10.5677 17.625 10.75ZM8.68754 6.625C8.86988 6.625 9.04475 6.55257 9.17368 6.42364C9.30261 6.2947 9.37504 6.11984 9.37504 5.9375C9.37504 5.75516 9.30261 5.5803 9.17368 5.45136C9.04475 5.32243 8.86988 5.25 8.68754 5.25C8.50521 5.25 8.33034 5.32243 8.20141 5.45136C8.07248 5.5803 8.00004 5.75516 8.00004 5.9375C8.00004 6.11984 8.07248 6.2947 8.20141 6.42364C8.33034 6.55257 8.50521 6.625 8.68754 6.625ZM5.25004 13.5C5.25004 11.2966 6.10804 9.22444 7.66661 7.66656L6.69379 6.69375C5.79699 7.58531 5.08606 8.646 4.6022 9.81434C4.11834 10.9827 3.87118 12.2354 3.87504 13.5C3.87504 18.8075 8.19254 23.125 13.5 23.125V21.75C8.95086 21.75 5.25004 18.0492 5.25004 13.5ZM22.0938 20.0312C22.0938 21.1684 21.1684 22.0938 20.0313 22.0938C18.8942 22.0938 17.9688 21.1684 17.9688 20.0312C17.9688 19.7136 18.0472 19.4166 18.175 19.1478L14.3835 15.3556C14.1154 15.4841 13.8177 15.5625 13.5 15.5625C12.3629 15.5625 11.4375 14.6371 11.4375 13.5C11.4375 12.3629 12.3629 11.4375 13.5 11.4375C14.6372 11.4375 15.5625 12.3629 15.5625 13.5C15.5625 13.8176 15.4849 14.1146 15.3563 14.3834L19.1479 18.1757C19.416 18.0471 19.7137 17.9688 20.0313 17.9688C21.1684 17.9688 22.0938 18.8941 22.0938 20.0312ZM13.5 14.1875C13.8789 14.1875 14.1875 13.8788 14.1875 13.5C14.1875 13.1212 13.8789 12.8125 13.5 12.8125C13.1212 12.8125 12.8125 13.1212 12.8125 13.5C12.8125 13.8788 13.1212 14.1875 13.5 14.1875ZM20.7188 20.0312C20.7187 19.8488 20.6461 19.6739 20.5171 19.545C20.388 19.416 20.213 19.3437 20.0306 19.3438C19.8482 19.3438 19.6733 19.4164 19.5443 19.5455C19.4154 19.6745 19.343 19.8495 19.3431 20.0319C19.3432 20.2144 19.4158 20.3893 19.5448 20.5182C19.6739 20.6471 19.8489 20.7195 20.0313 20.7194C20.2137 20.7193 20.3886 20.6468 20.5176 20.5177C20.6465 20.3887 20.7189 20.2137 20.7188 20.0312Z" fill="#14A388"/>
                                </svg>
                    </span>
                    </div>
                </div>
                <h2 id="recovery-count">{{ $total_count ?? 0 }}</h2>
                <p class="metric-growth" id="recovery-compare">
                </p>
                <div class="chart-container">
                    <canvas id="recoveryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

 <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="card mb-3">
                <div class="card-header p-2">
                    <h6 class="custom-dark">Quick Date Filter</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="quick_date_filter">Select Date Range</label>
                        <select id="quick_date_filter" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select City</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id_1" class="form-control custom-select2-field" @if(empty($zones)) onchange="getZones(this.value)" @else disabled @endif>
                            <option value="">Select City</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}"  @if(!empty($zones)) selected @endif >{{$city->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Zone</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Zone</label>
                        <select name="zone_id" id="zone_id_1" class="form-control custom-select2-field">
                            
                            @if(isset($zones) && !empty($zones))
                            <option value="">Select Zone</option>
                            @foreach($zones as $zone)
                            
                            <option value="{{$zone->id}}">{{$zone->name}}</option>
                            @endforeach
                            @else
                            <option value="">Select City First</option>
                            @endif
                        </select>
                    </div>
               </div>
            </div>
            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Date Between</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" id="clearFilterBtn">Clear All</button>
                <button class="btn btn-success w-50" id="applyFilterBtn">Apply</button>
            </div>
            
          </div>
        </div>
        
@section('script_js')
<script>
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: { x: { display: false }, y: { beginAtZero: true,
      
      grace: '5%',      // adds breathing space above data
      ticks: {
        stepSize: 2     // optional: control interval
      } } }
};

    const recoveryChart = new Chart(document.getElementById('recoveryChart'), {
  type: 'line',
  data: {
    labels: @json($labels),
    datasets: [{
    data:  @json($recoveryChartData),
      borderColor: '#92770B',
      backgroundColor: '#B99710',
      fill: true,
      tension: 0.4,
      borderWidth: 1,      // thickness of the line
      pointRadius: 1,      // size of the points
      pointHoverRadius: 5 
    }]
  },
  options: chartOptions
});
</script>

<script>
    function RightSideFilerOpen() {
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
                $('.custom-select2-field').select2({
            dropdownParent: $('#offcanvasRightHR01') // Fix for offcanvas
        });
        bsOffcanvas.show();
    }

    // Clear filters
$('#clearFilterBtn').on('click', function (e) {
    e.preventDefault();

    // Clear all filter inputs
    $('#quick_date_filter').trigger('change');
    $('#FromDate').val('');
    $('#ToDate').val('');
    $('#city_id_1').val('').trigger('change');
    $('#zone_id_1').val('').trigger('change');
    $('#recovery-status').val('').trigger('change');
    
    let recovery_status = $('#recovery-status').val();

    // Prepare filters object
    let filters = {
        quick_date_filter: 'year',
        city_id: '',
        zone_id: '',
        recovery_status: recovery_status,
        from_date: '',
        to_date: ''
    };

    // Call the same filter function via AJAX
    $.ajax({
        url: "{{ route('admin.recovery_management.dashboard.filter') }}",
        type: "GET",
        data: filters,
        success: function (response) {
            // Update HTML
            $('#total-count').text(response.total_count);
            $('#closed-count').text(response.closed_count);
            $('#opened-count').text(response.opened_count);
            $('#agent-count').text(response.agent_count);

            $('#recovery-count').text(response.charts.recovery.recovery_count);
                        
            recoveryChart.data.labels = response.charts.recovery.labels;
            recoveryChart.data.datasets[0].data = response.charts.recovery.counts;
            recoveryChart.update();

            // Close offcanvas
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
            if (bsOffcanvas) bsOffcanvas.hide();
        },
        error: function () {
            toastr.error('Failed to update dashboard');
        }
    });
});


    // Apply filters
    $('#applyFilterBtn').on('click', function (e) {
        const from_date = $('#FromDate').val();
        const to_date   = $('#ToDate').val();
        
        // Validation
        if ((from_date && !to_date) || (!from_date && to_date)) {
            e.preventDefault();
            toastr.error("Both From Date and To Date are required");
            return;
        }

        if (from_date && to_date && to_date < from_date) {
            e.preventDefault();
            toastr.error("To Date must be greater than or equal to From Date");
            return;
        }
        
        
        
        // Gather filter values
        let filters = {
            quick_date_filter: $('#quick_date_filter').val(),
            city_id: $('#city_id_1').val(),
            zone_id: $('#zone_id_1').val(),
            recovery_status: $('#recovery-status').val(),
            from_date: from_date,
            to_date: to_date
        };
            
        
        
        // AJAX call to update dashboard cards
        $.ajax({
            url: "{{ route('admin.recovery_management.dashboard.filter') }}",
            type: "GET",
            data: filters,

            success: function (response) {
                console.log(response);
                $('#total-count').text(response.total_count);
                $('#closed-count').text(response.closed_count);
                $('#opened-count').text(response.opened_count);
                $('#agent-count').text(response.agent_count);
                $('#agent-assigned-count').text(response.agent_assigned_count);
                $('#not-recovered-count').text(response.not_recovered_count);
                $('#recovery-status').val('').trigger('change');
                
               $('#recovery-count').text(response.charts.recovery.recovery_count);
                        
                recoveryChart.data.labels = response.charts.recovery.labels;
                recoveryChart.data.datasets[0].data = response.charts.recovery.counts;
                recoveryChart.update();
                        
        
                // Close offcanvas after success
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            },
            error: function () {
                toastr.error('Failed to update dashboard');
            }
        });
        
        // fetchRecoveryData();
        // fetchAccidentData();
        // fetchReturnData();
        // fetchServiceData();
    });
    
        function fetchRecoveryData() {
        // Collect filter values only if selected
        var data = {};
        var quickDateFilter = $('#quick_date_filter').val();
        var cityId = $('#city_id_1').val();
        var zoneId = $('#zone_id_1').val();
        var fromDate = $('#FromDate').val();
        var toDate = $('#ToDate').val();
        var status = $('#recovery-status').val();

        if(quickDateFilter) data.quick_date_filter = quickDateFilter;
        if(cityId) data.city_id = cityId;
        if(zoneId) data.zone_id = zoneId;
        if(fromDate) data.from_date = fromDate;
        if(toDate) data.to_date = toDate;
        if(status) data.status = status;

        data._token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ route('admin.recovery_management.dashboard.recoveryFilter') }}",
            type: "POST",
            data: data,
            success: function(response){
                console.log(response); // For debugging

                $('#recovery-count').text(response.count);
                        recoveryChart.data.labels = response.labels;
                        recoveryChart.data.datasets[0].data = response.data;
                        recoveryChart.update();
            },
            error: function(xhr){
                console.error(xhr);
            }
        });
    }
</script>

<script>
    $(document).ready(function(){

    // When user selects a custom date
    $('#FromDate, #ToDate').on('change', function(){
        if($('#FromDate').val() || $('#ToDate').val()) {
            $('#quick_date_filter').val('').trigger('change'); // reset quick filter
        }
    });

    // When user selects a quick date filter
    $('#quick_date_filter').on('change', function(){
        if($(this).val()) {
            $('#FromDate').val('');
            $('#ToDate').val('');
        }
    });
    
    $('#recovery-status').on('change', function(){
        fetchRecoveryData();
    });

});
</script>

<script>
    function getZones(CityID) {
        let ZoneDropdown = $('#zone_id_1');
    
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
</script>


@endsection

</x-app-layout>