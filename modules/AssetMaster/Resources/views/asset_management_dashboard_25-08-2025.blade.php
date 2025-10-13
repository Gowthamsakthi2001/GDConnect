<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    
    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
        }
        .shadow-secondary {
            box-shadow: 0 0.5rem 1rem rgba(222, 223, 226, 0.5); 
        }
        
        .px-6{
            padding-left: 3.3rem !important;
            padding-right: 3.3rem !important;
        }


#AMV_summaryCardBody .summary-card,
#AMV_summaryCardBody .equal-card {
  height: 100%;
}

        .summary-card-text {
          font-size: 14px;
        }

        @media (min-width: 1400px) {
          .summary-card-text  {
            font-size: 16px;
          }
        }

        .equal-height {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .summary-card .card {
            border-radius: 12px; 
            overflow: hidden;
        }
        
        #QcStatus_SummaryChart,
        #VehicleStatus_SummaryChart {
            max-width: 100% !important;
            width: 110px !important;
            height: 110px !important;
            margin: 0 auto;
        }
        
     
    #OEMChartType{
      width: 140px !important;
      height: 140px !important;
    }

    #indiaMapUnique {
      width: 100%;
      height: 400px;
    }

    @media screen and (min-width: 1400px) {
        #QcStatus_SummaryChart,
        #VehicleStatus_SummaryChart {
            width: 200px !important;
            height: 200px !important;
        }
        
        #OEMChartType{
          width: 190px !important;
          height: 190px !important;
        }
        
        #indiaMapUnique {
          width: 100%;
          height: 540px !important;
        }
        }
    #indiaMapUnique .land {
      fill: #f0f0f0;
      stroke: #fff;
      stroke-width: 1;
      filter: drop-shadow(0px 3px 4px rgba(0, 0, 0, 0.3));
    }

    #indiaMapUnique .city-dot {
      fill: url(#dotGradient);
      stroke: #008000;
      stroke-width: 8;
      filter: drop-shadow(0px 2px 3px rgba(0, 0, 0, 0.6));
      transition: r 0.6s ease;
    }

    #indiaMapUnique .city-dot:hover {
      r: 10;
      cursor: pointer;
    }

    #indiaMapUnique .pulse {
      fill: none;
      stroke: #008000;
      stroke-width: 2;
      opacity: 0.7;
    }

    #indiaMapUnique .connector {
      stroke: #008000;
      stroke-width: 2;
      stroke-dasharray: 5, 3;
      opacity: 0.9;
    }

    #indiaMapUnique .city-label-box {
      fill: #ffffff;         /* White background */
      stroke: #008000;       /* Green border */
      stroke-width: 1.5;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    #indiaMapUnique .city-label-box:hover {
      fill: #f5f5f5;         /* Light grey on hover */
      stroke: #006400;       /* Darker green border */
      filter: drop-shadow(0px 2px 6px rgba(0, 0, 0, 0.2));
    }
    
    #indiaMapUnique .city-label {
      font-size: 14px;       /* Bigger text */
      font-weight: 600;
      margin-bottom: 3px;
      fill: #333;
    }
    
    #indiaMapUnique .city-value {
      font-size: 18px;
      font-weight: 800;
      fill: #555;
    }

    .OEMChartType-legend {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        padding-right: 6px; /* space for scrollbar */
    }
    .OEMChartType-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        background: #f8f9fa;
        border-radius: 6px;
        padding: 4px 8px;
    }
    .OEMChartType-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
    }
    </style>

    <?php
    $db = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->where('model_id', auth()->user()->id)
        ->first();

    $roles = DB::table('roles')
        ->where('id', $db->role_id)
        ->first();
    ?>
    
    <!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
    <!--<script type="text/javascript">-->
    <!--google.charts.load('current', {-->
    <!--    packages: ['geochart'],-->
    <!--    mapsApiKey: '{{$map_api_key}}'-->
    <!--});-->
    
    <!--google.charts.setOnLoadCallback(drawRegionsMap);-->
    
    <!--function drawRegionsMap() {-->
    <!--     var data = google.visualization.arrayToDataTable(@json($MapchartData));-->
    
    <!--    var options = {-->
    <!--        region: 'IN', -->
    <!--        displayMode: 'markers', -->
    <!--        colorAxis: {colors: ['#008000', '#008000']}, -->
    <!--        tooltip: {isHtml: true},-->
    <!--        legend: 'none'-->
    <!--    };-->
    
    <!--    var chart = new google.visualization.GeoChart(document.getElementById('Mapregions_div'));-->
    <!--    chart.draw(data, options);-->
    <!--}-->
    
    
</script>


     <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                       <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h4 fw-bold">Asset Management</div>
                        </div>
                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="">
                               <div class="input-group border-gray">
                                    <button class="btn bg-white" type="button">
                                    <i class="fas fa-search"></i>
                                  </button>
                                  <input type="text" class="form-control border-0" id="AMV_search" placeholder="Search here" aria-label="Search">
                                 
                                </div>
                            </div>
                             <div class="text-center gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="AMVDashRightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    

    <div class="row" id="AMV_summaryCardBody">
        <div class="col-md-3 col-6 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between"> 
                                <div>
                                    <h6 class="mb-3"><span style="color:#a3a7af;">Total Assets</span></h6>
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold"> {{$total_asset_count ?? 0}} </h3> 
                                   
                                </div>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/hand_bag.png') }}" class="img-fluid">
                                </div>
                            </div>
                  
                            <div>
                                <p class="text-muted mt-3 summary-card-text"><img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"> <span style="color: #26C360; font-weight: 500;">+100%</span> VS. Last Period</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </a>
        </div>

        <div class="col-md-3 col-6 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between"> 
                                <div>
                                    <h6 class="mb-3"><span style="color:#a3a7af;">On Road Assets</span></h6>
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold"> {{$onRoad_asset_count ?? 0}} </h3> 
                                   
                                </div>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/tick_simple.png') }}" class="img-fluid">
                                </div>
                            </div>
                  
                            <div>
                                <p class="text-muted mt-3 summary-card-text"><img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"> <span style="color: #26C360; font-weight: 500;">+{{$onRoad_percentage}}%</span> VS. Last Period</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </a>
        </div>
        
        <div class="col-md-3 col-6 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between"> 
                                <div>
                                    <h6 class="mb-3"><span style="color:#a3a7af;">Off Road Assets</span></h6>
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold"> {{$offRoad_asset_count ?? 0}} </h3> 
                                   
                                </div>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/up_arrow.png') }}" class="img-fluid">
                                </div>
                            </div>
                  
                            <div>
                                <p class="text-muted mt-3 summary-card-text"><img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"> <span style="color: #26C360; font-weight: 500;">+{{$offRoad_percentage}}%</span> VS. Last Period</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </a>
        </div>
        
        <div class="col-md-3 col-6 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between"> 
                                <div>
                                    <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Under Maintainance </span></h6>
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold"> {{$total_clients ?? 0}} </h3> 
                                   
                                </div>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/up_arrow.png') }}" class="img-fluid">
                                </div>
                            </div>
                  
                            <div>
                                <p class="text-muted mt-3 summary-card-text"><img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"> <span style="color: #26C360; font-weight: 500;">+100%</span> VS. Last Period</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </a>
        </div>
        
      

       <div id="noResultsMessage" class="text-center text-muted my-4" style="display: none;">
           
            <div class="col-12">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-center align-items-center my-3">
                                    <i class="bi bi-emoji-frown fs-1 me-2" style="color:#4b5563;"></i>
                                    <h5 class="ps-2 mb-0" style="color:#4b5563;">No results found.</h5>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>


    </div>
    
    <div class="row mb-3">
    
        <div class="col-md-6 col-12 mb-3">
            <div class="card">
                <div class="card-header border-0 pb-0 mb-0">
                    City wise Breakdown
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <svg id="indiaMapUnique">
                      <!-- Gradients -->
                      <defs>
                        <radialGradient id="dotGradient" cx="50%" cy="50%" r="50%">
                          <stop offset="0%" stop-color="#38bdf8" stop-opacity="1" />
                          <stop offset="100%" stop-color="#0284c7" stop-opacity="1" />
                        </radialGradient>
                        <linearGradient id="labelGradient" x1="0" y1="0" x2="1" y2="1">
                          <stop offset="0%" stop-color="#1e40af" />
                          <stop offset="100%" stop-color="#1e3a8a" />
                        </linearGradient>
                      </defs>
                    </svg>
                </div>
            </div>
        </div>
        

    
        <div class="col-md-6 col-12 mb-4">
            <div class="row g-3">
               <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0 fw-semibold text-secondary">⚙️ OEM Type</h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Pie Chart Left -->
                                <div class="col-md-6 col-12 d-flex justify-content-center">
                                    <canvas id="OEMChartType" style="max-height: 200px;"></canvas>
                                </div>
                
                                <!-- Legend Right -->
                                <div class="col-md-6 col-12">
                                    <div class="OEMChartType-legend overflow-auto" id="OEMLegend" 
                                         style="max-height:140px;">
                                        <!-- Dynamic legend items will come here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        
                <!-- QC Status Summary -->
                <div class="col-md-6 col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0 fw-semibold text-secondary">📝 QC Status Summary</h6>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center pt-0">
                            <canvas id="QcStatus_SummaryChart" style="max-height: 200px;"></canvas>
                            <div class="d-flex justify-content-between mt-3 w-100">
                                <small class="d-flex align-items-center">
                                    <span style="width:12px;height:12px;background-color:#008000;border-radius:50%;margin-right:6px;"></span>
                                    Passed
                                </small>
                                <small class="d-flex align-items-center">
                                    <span style="width:12px;height:12px;background-color:#ff2c2c;border-radius:50%;margin-right:6px;"></span>
                                    Failed
                                </small>
                                <small class="d-flex align-items-center">
                                    <span style="width:12px;height:12px;background-color:#ffc327;border-radius:50%;margin-right:6px;"></span>
                                    Pending
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Vehicle Status -->
                <div class="col-md-6 col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0 fw-semibold text-secondary">🚘 Vehicle Status</h6>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center pt-0">
                            <canvas id="VehicleStatus_SummaryChart" style="max-height: 200px;"></canvas>
                            <div class="d-flex justify-content-between mt-3 w-100">
                                <small class="d-flex align-items-center">
                                    <span style="width:12px;height:12px;background-color:#008000;border-radius:50%;margin-right:6px;"></span>
                                    Registered
                                </small>
                                <small class="d-flex align-items-center">
                                    <span style="width:12px;height:12px;background-color:#ff2c2c;border-radius:50%;margin-right:6px;"></span>
                                    Unregistered
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!--<div class="col-md-3 col-12">-->
        <!--    <div class="row p-0 m-0">-->
        <!--        <div class="col-12 p-0 mb-2">-->
        <!--            <div class="card mb-2">-->
        <!--                <div class="card-header border-0 pb-0 mb-0">-->
        <!--                    Qc Status Summary-->
        <!--                </div>-->
        <!--                <div class="card-body">-->
        <!--                    <canvas id="QcStatus_SummaryChart"></canvas>-->
        <!--                    <div class="d-flex justify-content-between mt-2">-->
        <!--                        <small><span style="display:inline-block;width:10px;height:10px;background-color:#008000;border-radius:50%;margin-right:5px;"></span>Passed</small>-->
        <!--                        <small><span style="display:inline-block;width:10px;height:10px;background-color:#ff2c2c;border-radius:50%;margin-right:5px;"></span>Failed</small>-->
        <!--                        <small><span style="display:inline-block;width:10px;height:10px;background-color:#ffc327;border-radius:50%;margin-right:5px;"></span>Pending</small>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="col-12 p-0 m-0">-->
        <!--             <div class="card">-->
        <!--                <div class="card-header border-0 pb-0 mb-0">-->
        <!--                    Vehicle Status-->
        <!--                </div>-->
        <!--                <div class="card-body">-->
        <!--                    <canvas id="VehicleStatus_SummaryChart"></canvas>-->
        <!--                    <div class="d-flex justify-content-between mt-2">-->
        <!--                        <small>-->
        <!--                            <span style="display:inline-block;width:10px;height:10px;background-color:#008000;border-radius:50%;margin-right:5px;"></span>-->
        <!--                            Registered-->
        <!--                        </small>-->
        <!--                        <small>-->
        <!--                            <span style="display:inline-block;width:10px;height:10px;background-color:#ff2c2c;border-radius:50%;margin-right:5px;"></span>-->
        <!--                            Un Registered-->
        <!--                        </small>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
            
        <!--</div>-->
    </div>

    <div class="row mb-3">
    
        <div class="col-md-6 col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    City wise Table Breakdown
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table text-center table-bordered bg-light mb-0">
                            <thead class="sticky-top" style="top: 0; z-index: 2;">
                                <tr>
                                    <th class="text-dark" style="background-color:#f0f0f0 !important;">City</th>
                                    <th class="text-dark" style="background-color:#f0f0f0 !important;">Total Assets</th>
                                    <th class="text-dark" style="background-color:#f0f0f0 !important;">Active Assets</th>
                                    <th class="text-dark" style="background-color:#f0f0f0 !important;">Idle Assets</th>
                                </tr>
                            </thead>
                           <tbody>
                               
                               @if(isset($clientWiseTable))
                                 @foreach($clientWiseTable as $data)
                                   <tr>
                                        <td><small>{{$data->location_name ?? 'N/A'}}</small></td>
                                        <td><small>{{$data->total_assets ?? 0}}</small></td>
                                        <td><small>{{$data->active_assets ?? 0}}</small></td>
                                        <td><small>{{$data->idle_assets ?? 0}}</small></td>
                                    </tr>
                                 @endforeach
                               @endif
                               
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-md-3 col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    Revenue Generation
                </div>
                <div class="card-body">
                    <div style="max-width: 400px; margin: auto;">
    
                        <div class="mb-3 d-flex flex-column gap-2">
                            <div class="d-flex align-items-center my-3">
                                <div style="width: 30px; height: 10px; background: #00bcd4; border-radius: 4px; margin-right: 8px;"></div>
                                <small>Revenue Generated</small>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 30px; height: 10px; background: #d4e157; border-radius: 4px; margin-right: 8px;"></div>
                                <small>Non Revenue Generated</small>
                            </div>
                        </div>
    
                        <canvas id="revenueBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-12 mb-3">
            <div class="card equal-height">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr><th>Insurance Expiry</th><td>100</td></tr>
                                <tr><th>Lease Expiry</th><td>100</td></tr>
                                <tr><th>FC Expiry</th><td>100</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    City wise Table Breakdown
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                         <table class="table table-bordered text-center align-middle bg-light mb-0">
                              <thead class="table-light sticky-top" style="top: 0; z-index: 2;">
                                <tr>
                                  <th rowspan="2">City</th>
                                  <th colspan="2">Onroad</th>
                                  <th colspan="2">Off Road</th>
                                </tr>
                                <tr>
                                  <th>2W</th>
                                  <th>4W</th>
                                  <th>2W</th>
                                  <th>4W</th>
                                </tr>
                              </thead>
                              <tbody>
                                @if(isset($clientWiseTable))
                                 @foreach($clientWiseTable as $data)
                                   <tr>
                                        <td><small>{{$data->location_name ?? 'N/A'}}</small></td>
                                        <td><small>1</small></td>
                                        <td><small>2</small></td>
                                        <td><small>3</small></td>
                                        <td><small>3</small></td>
                                    </tr>
                                 @endforeach
                               @endif
                              </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
     <div class="row mb-3">
        <div class="col-md-8 col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    Client Wise Deployment
                </div>
                <div class="card-body">
                    <div style="">
                        <canvas id="ClientwisebarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="card h-100 shadow-sm">
                <div class="card-header border-0 pb-0 mb-0">
                    Recent Activity
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <p class="mb-1 text-muted">
                                QC inspection completed for chassis MD92N41SKCC639010
                            </p>
                            <div class="d-flex align-items-center small text-secondary">
                                <span>Warehouse A - Warehouse B</span>
                                <i class="bi bi-circle-fill mx-2" style="font-size: 6px;"></i>
                                <span>23 hours ago</span>
                            </div>
                        </div>
        
                        <div class="list-group-item border-0 px-0">
                            <p class="mb-1 text-muted">
                                QC inspection completed for chassis MD92N41SKCC639010
                            </p>
                            <div class="d-flex align-items-center small text-secondary">
                                <span>Warehouse A - Warehouse B</span>
                                <i class="bi bi-circle-fill mx-2" style="font-size: 6px;"></i>
                                <span>23 hours ago</span>
                            </div>
                        </div>
        
                        <div class="list-group-item border-0 px-0">
                            <p class="mb-1 text-muted">
                                QC inspection completed for chassis MD92N41SKCC639010
                            </p>
                            <div class="d-flex align-items-center small text-secondary">
                                <span>Warehouse A - Warehouse B</span>
                                <i class="bi bi-circle-fill mx-2" style="font-size: 6px;"></i>
                                <span>23 hours ago</span>
                            </div>
                        </div>
    
                    </div>
        
                    <div class="mt-3">
                        <button class="btn btn-primary w-100">View All History</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


   <div class="row mb-3">
        <div class="col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    Module wise split
                </div>
                <div class="card-body">
                    <div style="max-width: 100%; margin: auto; height: 400px; overflow-y: auto;">
                        <canvas id="AssetOwnershipChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    Client Wise Deployment
                </div>
                <div class="card-body">
                    <div style="max-width: 100%; margin: auto;">
                       <canvas id="ClientWisedeploymentChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    Insurance Summary
                </div>
                <div class="card-body">
                    <div style="max-width: 100%; margin: auto;">
                        <canvas id="InsuranceSummarybarChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">Inventory Latest Overview</div>
                <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <!-- Search Bar -->
                             <div class="input-group w-50 me-2 rounded" style="border:1px solid #ced4da;">
                                <input type="text" class="form-control border-0" id="inventory_sum_search"
                                       placeholder="Enter the Lot ID or Chassis Number" aria-label="Search">
                                <div id="search-error" class="invalid-feedback text-danger"></div>
                            </div>
                        
                            <!-- Filter Button -->
                            <div class="bg-white p-2 px-3 rounded" style="cursor:pointer;border:1px solid #ced4da;" onclick="InventorySummaryOpen()"><i class="bi bi-funnel"></i>  Filter
                            </div>
                        </div>
        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table id="inventorySummaryTable" class="table text-center table-bordered bg-light mb-0">
                                <thead class="sticky-top" style="top: 0; z-index: 2;">
                                    <tr>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Lot ID</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Chassis No</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Model</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Type</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Current Status</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Action</th>
                                    </tr>
                                </thead>
                               <tbody>
                                   @if(isset($inventory_summary))
                                     @foreach($inventory_summary as $inventory)
                                        <?php
                                            $id_encode = encrypt($inventory->id);
                                        ?>
                                         <tr>
                                            <td><small>{{$inventory->id}}</small></td>
                                            <td><small>{{$inventory->assetVehicle->chassis_number ?? 'N/A'}}</small></td>
                                            <td><small>{{$inventory->assetVehicle->vehicle_model_relation->vehicle_model ?? 'N/A'}}</small></td>
                                            <td><small>{{$inventory->assetVehicle->vehicle_type_relation->name ?? 'N/A'}}</small></td>
                                            <td><small>{{$inventory->inventory_location->name ?? 'N/A'}}</small></td>
                                           <td>
                                                <small>
                                                    <a href="{{ route('admin.asset_management.asset_master.inventory.view', ['id' => $id_encode]) }}">
                                                        <i class="bi bi-eye me-2 fs-5"></i>
                                                    </a>
                                                </small>
                                            </td>
        
                                        </tr>
                                     @endforeach
                                   @endif
                                    
                                   
                                </tbody>
        
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    
    <div class="card">
    <div class="card-header border-0 pb-0 mb-0">
        <h5 class="fw-bold">Recent Activities</h5>
        <p class="text-start text-muted mb-0">Recent Quality Check Activities Across All Modules</p>
    </div>

    <!-- Scrollable body -->
    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <div class="row p-3 rounded">

            @if(isset($values) && $values->count())
                @foreach($values as $value)
                    @php
                        $isPass = strtolower($value->status) === 'pass';
                        $message = $isPass
                            ? 'QC inspection completed for chassis ' . ($value->chassis_number ?? 'N/A')
                            : 'QC inspection failed for chassis ' . ($value->chassis_number ?? 'N/A');

                        $buttonClass = $isPass ? 'btn-success' : 'btn-danger';
                        $buttonText = ucfirst($value->status);
                    @endphp

                    <div class="col-12 border-gray p-3 d-flex justify-content-between align-items-center mb-4" style="background:#eaeaea;">
                        <div>
                            <p class="text-start mb-1" style="color:#00000080;">
                                {{ $message }}
                            </p>

                            <div class="d-flex align-items-center">
                                <!--<small class="fw-normal me-2">By {{ $value->created_by ?? 'Unknown' }}</small>-->
                                <i class="bi bi-circle-fill text-muted" style="font-size: 6px;"></i>
                                <small class="fw-normal ms-2">
                                    {{ $value->created_at ? \Carbon\Carbon::parse($value->created_at)->diffForHumans() : 'N/A' }}
                                </small>
                            </div>
                        </div>

                        <div>
                            <button class="btn {{ $buttonClass }} px-5">{{ $buttonText }}</button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center text-muted py-4">
                    <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                    <p class="mb-0">No Quality Check activities found.</p>
                </div>
            @endif

        </div>
    </div>
</div>



   <div class="offcanvas offcanvas-end" tabindex="-1" id="DashoffcanvasRightAMV" aria-labelledby="DashoffcanvasRightAMVLabel">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="DashoffcanvasRightAMVLabel">Asset Management Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDashboardFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyDashboardFilter()">Apply</button>
            </div>
         
           
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="today" {{ request('timeline') == 'today' ? 'checked' : '' }} name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_week" {{ request('timeline') == 'this_week' ? 'checked' : '' }} name="STtimeLine" id="timeLine2">
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_month" {{ request('timeline') == 'this_month' ? 'checked' : '' }} name="STtimeLine" id="timeLine3">
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_year" {{ request('timeline') == 'this_year' ? 'checked' : '' }} name="STtimeLine" id="timeLine4">
                      <label class="form-check-label" for="timeLine4">
                       This Year
                      </label>
                    </div>
                    
                    
               </div>
            </div>
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Option</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="location_id">Location</label>
                        <select name="location_id" id="location_id" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($location_data))
                                @foreach($location_data as $l)
                                <option value="{{$l->id}}" {{ $location_id == $l->id ? 'selected' : '' }}>{{$l->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                    <div class="mb-3">
                        <label class="form-label" for="v_type">Vehicle Type</label>
                        <select name="v_type" id="v_type" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                <option value="{{$val->id}}" {{ $vehicle_type == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_model">Vehicle Model</label>
                        <select name="v_model" id="v_model" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" {{ $vehicle_model == $val->id ? 'selected' : '' }}>{{$val->vehicle_model}}</option>
                                @endforeach
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDashboardFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyDashboardFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
    <div class="offcanvas offcanvas-start" tabindex="-1" id="InventorySummaryFil" aria-labelledby="InventorySummaryFilLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="InventorySummaryFilLabel">Inventory Summary Filter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                    <div class="mb-3">
                    <label class="form-label" for="status">Select Status</label>
                    <select name="asset_fil_status" id="asset_fil_status" class="form-control custom-select2-field" onchange="InSumFilter_Function(this.value)">
                        <option value="all">All</option>
                        @if(isset($inventory_locations))
                            @foreach($inventory_locations as $data)
                                <option value="{{ $data->id }}">
                                    {{ $data->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>


                    
               </div>
           </div>
      </div>
    </div>
        
    @push('css')
    <link rel="stylesheet" href="{{ admin_asset('css/dashboard.min.css') }}">
    <style>
    </style>
    @endpush
    @push('js')

    @endpush
    @section('script_js')
    
    <script>
        const width = 950, height = 650;
    
        const projection = d3.geoMercator()
          .center([78.9629, 22.5937]) // India center
          .scale(1200)
          .translate([width / 2, height / 2]);
    
        const path = d3.geoPath().projection(projection);
    
        const svg = d3.select("#indiaMapUnique")
          .attr("viewBox", `0 0 ${width} ${height}`);
    
        // Load only India geoJSON (lighter file)
        d3.json("https://raw.githubusercontent.com/geohacker/india/master/state/india_telengana.geojson").then(function (india) {
          
          // Draw India map
          svg.append("g")
            .selectAll("path")
            .data(india.features)
            .enter().append("path")
            .attr("d", path)
            .attr("class", "land");
    
        const cities = @json($MapchartData);
    
        // console.log(cities);
    
            const placedRight = [];
            const placedLeft = [];
            
            function getNonOverlappingPosSide(x, y, side = "right") {
              let lx, ly;
            
              if (side === "right") {
                lx = x + 140; // fixed right distance
                ly = y;
                // adjust vertically if overlapping
                for (const pos of placedRight) {
                  if (Math.abs(ly - pos) < 50) {
                    ly = pos + 60; // push down
                  }
                }
                placedRight.push(ly);
              } else {
                lx = x - 180; // fixed left distance
                ly = y;
                for (const pos of placedLeft) {
                  if (Math.abs(ly - pos) < 50) {
                    ly = pos + 60;
                  }
                }
                placedLeft.push(ly);
              }
            
              return [lx, ly];
            }
            
            cities.forEach((city, i) => {
              const [x, y] = projection(city.coords);
            
              // Alternate sides for balance
              const side = i % 2 === 0 ? "right" : "left";
            
              // Get label pos
              const [labelX, labelY] = getNonOverlappingPosSide(x, y, side);
            
              // ---- Dot ----
              svg.append("circle")
                .attr("cx", x)
                .attr("cy", y)
                .attr("r", 0)
                .attr("class", "city-dot")
                .transition()
                .delay(i * 300)
                .duration(800)
                .attr("r", 7);
            
              // ---- Pulse ----
              const pulseCircle = svg.append("circle")
                .attr("cx", x)
                .attr("cy", y)
                .attr("r", 7)
                .attr("class", "pulse");
            
              function pulse() {
                pulseCircle
                  .attr("r", 7)
                  .style("opacity", 0.8)
                  .transition()
                  .duration(1500)
                  .ease(d3.easeCubicOut)
                  .attr("r", 30)
                  .style("opacity", 0)
                  .on("end", pulse);
              }
              pulse();
            
              // ---- Connector (with bend) ----
              const midX = side === "right" ? x + 60 : x - 60;
            
              const connector = svg.append("path")
                .attr("d", `M${x},${y} L${x},${y}`)
                .attr("class", "connector");
            
              connector.transition()
                .delay(i * 300 + 300)
                .duration(800)
                .attr("d", `M${x},${y} L${midX},${y} L${labelX},${labelY}`);
            
              // ---- Label group ----
              const labelGroup = svg.append("g");
            
    
            
            // Rect with class
            labelGroup.append("rect")
              .attr("x", labelX - (side === "right" ? 0 : 120))
              .attr("y", labelY - 18)
              .attr("width", 130)
              .attr("height", 48)
              .attr("rx", 6) // rounded corners
              .attr("class", "city-label-box")
              .style("opacity", 0)
              .transition()
              .delay(i * 300 + 600)
              .duration(500)
              .style("opacity", 1);
            
            // City name
            labelGroup.append("text")
              .attr("x", labelX - (side === "right" ? 0 : 120) + 65) // center
              .attr("y", labelY)
              .attr("text-anchor", "middle")
              .attr("class", "city-label")
              .style("opacity", 0)
              .text(city.name)
              .transition()
              .delay(i * 300 + 700)
              .duration(500)
              .style("opacity", 1);
            
            // City value
            labelGroup.append("text")
              .attr("x", labelX - (side === "right" ? 0 : 120) + 65) // center
              .attr("y", labelY + 16)
              .attr("text-anchor", "middle")
              .attr("dy", "3px") 
              .attr("class", "city-value")
              .style("opacity", 0)
              .text(city.value.toLocaleString())
              .transition()
              .delay(i * 300 + 800)
              .duration(500)
              .style("opacity", 1);
      
            });
    
    
        });
  </script>
    
    <!-- Add Chart.js -->
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
<script>

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('revenueBarChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['', ''],
            datasets: [{
                data: [70, 30],
                backgroundColor: ['#00bcd4', '#d4e157'],
                borderSkipped: false, // allows full rounded corners
                borderRadius: 20,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.parsed.x + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: 0,
                    max: 100,
                    grid: { display: false },
                    border: { display: false },
                    ticks: { display: false }
                },
                y: {
                    grid: { display: false },
                    border: { display: false }, 
                    ticks: { display: false }
                }
            }
        },
        plugins: [{
            id: 'barLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = '#555';
                ctx.textAlign = 'right';
                chart.data.datasets[0].data.forEach((value, index) => {
                    const meta = chart.getDatasetMeta(0);
                    const rect = meta.data[index];
                    ctx.fillText(value + '%', chart.chartArea.right, rect.y + 4);
                });
                ctx.restore();
            }
        }]
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const labels = [
        'Green Drive',
        'Greaves Electric Mobility Pvt Ltd',
        'Log 9',
        'Revfi',
        'Okinawa Autotech',
        'Olectra Greentech Ltd',
        'Alab Technology',
        'Company 8',
        'Company 9',
        'Company 10',
        'Company 11',
        'Company 12',
    ];

    const actualData = [70, 30, 50, 80, 60, 40, 50, 55, 65, 45, 75, 35];
    const remainingData = actualData.map(v => 100 - v);

    const ctx = document.getElementById('AssetOwnershipChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Filled',
                    data: actualData,
                    backgroundColor: '#70a7d5',
                    borderSkipped: false,
                    borderRadius: {
                        topLeft: 10,
                        bottomLeft: 10
                    },
                    barThickness: 20
                },
                {
                    label: 'Remaining',
                    data: remainingData,
                    backgroundColor: '#eaeaea',
                    borderSkipped: false,
                    borderRadius: {
                        topRight: 10,
                        bottomRight: 10
                    },
                    barThickness: 20
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false, // Important for scrolling
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label === 'Filled'
                                ? context.parsed.x + '%'
                                : '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: 0,
                    max: 100,
                    stacked: true,
                    grid: { display: false },
                    border: { display: false },
                    ticks: { display: false }
                },
                y: {
                    stacked: true,
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        align: 'start',
                        padding: 5
                    }
                }
            }
        },
        plugins: [{
            id: 'barLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = '#555';
                ctx.textAlign = 'left';

                chart.data.datasets[0].data.forEach((value, index) => {
                    const meta = chart.getDatasetMeta(0);
                    const rect = meta.data[index];
                    ctx.fillText(value + '%', rect.x + 5, rect.y + 4);
                });

                ctx.restore();
            }
        }]
    });
});

document.addEventListener("DOMContentLoaded", function() {
  const in_ctx = document.getElementById('InsuranceSummarybarChart').getContext('2d');

  new Chart(in_ctx, {
    type: 'bar',
    data: {
      labels: [
        "January", "February", "March", "April", "May", "June", 
        "July", "August", "September", "October", "November", "December"
      ],
      datasets: [{
        data: [20, 12, 30, 25, 40, 35, 28, 45, 33, 50, 42, 38],
        backgroundColor: '#3bb79e'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});

document.addEventListener("DOMContentLoaded", function() {
  const clientwise_ctx = document.getElementById('ClientwisebarChart').getContext('2d');

  new Chart(clientwise_ctx, {
    type: 'bar',
    data: {
      labels: [
        "Amazon", "FlipKart", "Shadowfax", "Blinkit", "Dunzo", "Rapido"],
      datasets: [{
        data: [20, 12, 30, 25, 40, 35],
        backgroundColor: '#673bb7'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
});
</script>

    
<script>
    
    
   function applyDashboardFilter() {
        const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
        const timeline = selectedTimeline ? selectedTimeline.value : '';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        const location_id = document.getElementById('location_id').value;
        const v_type = document.getElementById('v_type').value;
        const v_model = document.getElementById('v_model').value;
        if (from_date != "" || to_date != "") {
            if (to_date == "" || from_date == "") {
                toastr.error("From Date and To Date is must be required");
                return;
            }
        }
    
        const url = new URL(window.location.href);
    
        if (from_date && to_date) {
            // Use from_date and to_date, remove timeline
            url.searchParams.set('from_date', from_date);
            url.searchParams.set('to_date', to_date);
            url.searchParams.delete('timeline');
        } else if (timeline) {
            // Use timeline, remove from_date and to_date
            url.searchParams.set('timeline', timeline);
            url.searchParams.delete('from_date');
            url.searchParams.delete('to_date');
        }

        url.searchParams.set('location_id', location_id);
        url.searchParams.set('vehicle_type', v_type);
        url.searchParams.set('vehicle_model', v_model);
        window.location.href = url.toString();
    }


    function clearDashboardFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        url.searchParams.delete('timeline');
        url.searchParams.delete('location_id');
        url.searchParams.delete('vehicle_type');
        url.searchParams.delete('vehicle_model');
        window.location.href = url.toString();
    }


    document.getElementById('AMV_search').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let cards = document.querySelectorAll('.summary-card');
        let anyVisible = false;

        cards.forEach(function(card) {
            let text = card.innerText.toLowerCase();

            if (text.includes(filter)) {
                card.style.display = 'block';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Show or hide the "no results" message
        document.getElementById('noResultsMessage').style.display = anyVisible ? 'none' : 'block';
    });
</script>


<script>
     $(document).ready(function() {
        $('#city_id_filter').select2({
          width: '100%' // Ensures Select2 adapts to 100% width
        });
      });
      
       function DatewiseFiler(){
           var fromDate = $("#FromDate").val();
           var toDate = $("#ToDate").val();
           
           if (!fromDate || !toDate) {
                toastr.error("From date and To date fields are required");
                return;
            }
           var url = new URL(window.location.href);
           url.searchParams.set('from_date',fromDate);
           url.searchParams.set('to_date',toDate);
           window.location.href = url.toString();
       }
   
    function CitywiseFilter(value){
           var fromDate = $("#FromDate").val();
           var toDate = $("#ToDate").val();
           var url = new URL(window.location.href);
           url.searchParams.set('from_date',fromDate);
           url.searchParams.set('to_date',toDate);
           url.searchParams.set('city_id',value);
           window.location.href = url.toString();
       }
       
    function AMVDashRightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#DashoffcanvasRightAMV');
        bsOffcanvas.show();
    }
    
    function InventorySummaryOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#InventorySummaryFil');
        bsOffcanvas.show();
    }
</script>
    


<script>
    var qc_pass_percentage    = {{ $qc_pass_percentage }};
    var qc_fail_percentage    = {{ $qc_fail_percentage }};
    var qc_pending_percentage = {{ $qc_pending_percentage }};
    // console.log(qc_pass_percentage, qc_fail_percentage, qc_pending_percentage);

    const qc_summaryctx1 = document.getElementById('QcStatus_SummaryChart').getContext('2d');
    new Chart(qc_summaryctx1, {
        type: 'doughnut',
        data: {
            labels: ['Passed', 'Failed', 'Pending'],
            datasets: [{
                data: [qc_pass_percentage, qc_fail_percentage, qc_pending_percentage], 
                backgroundColor: ['#008000', '#ff2c2c', '#ffc327'], 
                borderWidth: 0,
                cutout: '55%',
                borderRadius: 0, 
                spacing: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
</script>
<script>
    var registered_percentage = {{$assetReg_percentage}}; // example value
    var unregistered_percentage = {{$assetUnreg_percentage}}; // example value

    const VehicleStatus_ctx = document.getElementById('VehicleStatus_SummaryChart').getContext('2d');
    new Chart(VehicleStatus_ctx, {
        type: 'doughnut',
        data: {
            labels: ['Registered', 'Unregistered'],
            datasets: [{
                data: [registered_percentage, unregistered_percentage], 
                backgroundColor: ['#008000', '#ff2c2c'], 
                borderWidth: 0,
                cutout: '60%',
                borderRadius: 0
            }]
        },
        options: {
            rotation: -90, // start angle
            circumference: 180, // half circle
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
</script>
 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    // New statuses and their counts
    const VS_chartData = {
        rider: {
            labels: [
                "RFD - Ready for deployment",
                "Under Maintenance",
                "On Rent",
                "Unregistered",
                "DOC Issues",
                "Accident",
                "Warranty",
                "Police Station",
                "Spare Pending"
            ],
            counts: [19, 5, 10, 66, 8, 2, 4, 3, 7] // adjust counts as needed
        }
    };

    // Calculate total for percentages
    // const counts = VS_chartData.rider.counts;
    // const total = counts.reduce((a, b) => a + b, 0);

    // const ctx = document.getElementById('VehicleStatuspieChart').getContext('2d');

    // new Chart(ctx, {
    //     type: 'pie',
    //     data: {
    //         labels: VS_chartData.rider.labels,
    //         datasets: [{
    //             data: counts, // Use raw counts
    //             backgroundColor: [
    //                 '#4e79a7', '#f28e2b', '#e15759', '#76b7b2',
    //                 '#59a14f', '#edc949', '#af7aa1', '#ff9da7', '#9c755f'
    //             ],
    //             borderColor: '#fff',
    //             borderWidth: 2
    //         }]
    //     },
    //     options: {
    //         responsive: true,
    //         maintainAspectRatio: false,
    //         plugins: {
    //             legend: {
    //                 position: 'bottom',
    //             },
    //             tooltip: {
    //                 callbacks: {
    //                     label: function(context) {
    //                         const label = context.label;
    //                         const value = context.raw;
    //                         const percent = ((value / total) * 100).toFixed(1);
    //                         return `${label}: ${value} (${percent}%)`;
    //                     }
    //                 }
    //             },
    //             datalabels: {
    //                 display: false // hide labels on chart slices
    //             }
    //         }
    //     },
    //     plugins: [ChartDataLabels]
    // });
</script>

<script>
    // Laravel data encode to JS
    const chartDataFromLaravel = @json($v_models);

    // Extract labels & counts
    const chartLabels = chartDataFromLaravel.map(item => item.model_name);
    const chartCounts = chartDataFromLaravel.map(item => item.model_count);

    // Dark vibrant colors
    const chartColors = [
        "#e63946","#f1c40f","#2980b9","#27ae60","#e67e22","#8e44ad",
        "#16a085","#2c3e50","#d35400","#7f8c8d","#34495e","#c0392b",
        "#9b59b6","#1abc9c","#f39c12","#2ecc71"
    ];

    const ctx1 = document.getElementById('OEMChartType').getContext('2d');
    const oemChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartCounts,
                backgroundColor: chartColors,
                borderColor: "#fff",
                borderWidth: 2,
                cutout: '55%',
                borderRadius: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed;
                        }
                    }
                }
            }
        }
    });

    // Custom legend
    const legendContainer = document.getElementById("OEMLegend");
    chartLabels.forEach((label, i) => {
        const item = document.createElement("div");
        item.classList.add("OEMChartTypelegend-item");
        item.innerHTML = `
          <div class="OEMChartType-dot" style="background-color:${chartColors[i]}"></div>
          ${label}
        `;
        legendContainer.appendChild(item);
    });
</script>

<script>
function tablePreloaderOn(){
    $('#inventorySummaryTable tbody').html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `);
}

$('#inventory_sum_search').on('keyup', function () {
    let search = $(this).val();
    if (search.length != "" && search.length < 4) {
        $(this).addClass('is-invalid').removeClass('is-valid');
        $("#search-error").text('Please enter at least 4 characters.');
        return;
    } else {
        $(this).removeClass('is-invalid');
        $("#search-error").text('');
    }
    tablePreloaderOn();
    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.inventory_summary.filter') }}",
        type: 'GET',
        data: { search: search },
        success: function (data) {
            let rows = '';
            
            if (data.length > 0) {
                data.forEach(item => {
                    rows += `
                        <tr>
                            <td><small>${item.id}</small></td>
                            <td><small>${item.chassis_number}</small></td>
                            <td><small>${item.model}</small></td>
                            <td><small>${item.vehicle_type}</small></td>
                            <td><small>${item.location}</small></td>
                            <td>
                                <small>
                                    <a href="${item.url}">
                                        <i class="bi bi-eye me-2 fs-5"></i>
                                    </a>
                                </small>
                            </td>
                        </tr>
                    `;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No records found</td></tr>`;
            }

            $('#inventorySummaryTable tbody').html(rows);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
});

function InSumFilter_Function(status){
    $("#inventory_sum_search").val('');
    tablePreloaderOn();
    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.inventory_summary.filter') }}",
        type: 'GET',
        data: { status: status },
        success: function (data) {
            let rows = '';
            
            if (data.length > 0) {
                data.forEach(item => {
                    rows += `
                        <tr>
                            <td><small>${item.id}</small></td>
                            <td><small>${item.chassis_number}</small></td>
                            <td><small>${item.model}</small></td>
                            <td><small>${item.vehicle_type}</small></td>
                            <td><small>${item.location}</small></td>
                            <td>
                                <small>
                                    <a href="${item.url}">
                                        <i class="bi bi-eye me-2 fs-5"></i>
                                    </a>
                                </small>
                            </td>
                        </tr>
                    `;
                });
            } else {
                rows = `<tr><td colspan="6" class="text-center">No records found</td></tr>`;
            }

            $('#inventorySummaryTable tbody').html(rows);
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });
}

</script>
<script>
    // Get current month days dynamically
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const labels = Array.from({ length: daysInMonth }, (_, i) => String(i + 1).padStart(2, '0'));

    const ClientWisedeploymentChart_ctx = document.getElementById('ClientWisedeploymentChart').getContext('2d');

    new Chart(ClientWisedeploymentChart_ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Deployed Vehicle',
                    data: [10, 20, 30, 50, 80, 150, 300, 500, 400, 350, 300, 250, 200, 180, 400, 600, 800, 900, 850, 800, 750, 700, 950, 900, 850, 800, 750, 900, 950, 1000, 2050].slice(0, daysInMonth),
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    fill: false,
                    tension: 0.3,
                    borderWidth: 2
                },
                {
                    label: 'Returned Vehicle',
                    data: [5, 10, 15, 25, 50, 100, 250, 450, 380, 330, 280, 230, 180, 160, 380, 580, 750, 850, 800, 770, 720, 680, 900, 850, 800, 760, 720, 850, 900, 950, 990].slice(0, daysInMonth),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: false,
                    tension: 0.3,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    min: 0,
                    max: 1000,
                    ticks: {
                        stepSize: 100 // 1000, 900, 800, etc.
                    },
                    title: {
                        display: true,
                        text: 'Vehicle Count'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of the Month'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });
</script>
    @endsection
</x-app-layout>