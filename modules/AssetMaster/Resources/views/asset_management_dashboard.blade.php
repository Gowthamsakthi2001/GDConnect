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
          /*height: 190px !important;*/
        }
        
       #AMV_summaryCardBody .summary-card .equal-card .bg-white {
            border-radius: 16px; /* softer edges */
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        border 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        background 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #AMV_summaryCardBody .summary-card .equal-card .bg-white:hover {
            transform: translateY(-6px) scale(1.02); 
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15); 
            border: 1px solid rgba(38, 195, 96, 0.15); 
            background: linear-gradient(145deg, #ffffff, #f9fafb);
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
        
     
    /*#OEMChartType{*/
    /*  width: 140px !important;*/
    /*  height: 140px !important;*/
    /*}*/
    
    #mapLoader {
      display: none !important;
    }
    #mapLoader.active {
      display: flex !important;
    }
    
    #oem-chart-preloader {
        display: none;
    }
    #oem-chart-preloader.active {
        display: flex;
    }



    #vehicle-summary-chart-preloader {
        display: none !important;
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0; 
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        z-index: 10;
    
        /* Centering */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    #clientWise-chart-preloader {
        display: none;   /* hidden by default */
    }
    #clientWise-chart-preloader.active {
        display: flex !important;  /* show when active */
    }

    
    #vehicle-summary-chart-preloader:not(.active) {
        display: none !important;
    }
    
    #vehicle-summary-chart-preloader.active {
        display: flex !important;
    }



    #indiaMapUnique {
      width: 100%;
      height: 445px;
    }

    @media screen and (min-width: 1400px) {
        #QcStatus_SummaryChart,
        #VehicleStatus_SummaryChart {
            width: 200px !important;
            height: 200px !important;
        }
        
        /*#OEMChartType{*/
        /*  width: 190px !important;*/
        /*  height: 190px !important;*/
        /*}*/
        
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
      stroke-width: 1;
      stroke-dasharray: 1, 1;
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
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* auto responsive */
        gap: 8px 16px;
        font-size: 13px;
    }
    
    .OEMChartTypelegend-item {
        display: flex;
        align-items: center;
        white-space: nowrap;
        
    }
    .OEMChartType-dot {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        display: inline-block;
        margin-right: 5px;
    }
    .asset-ownership-scrollbar { /* show only 4 bars height */
      height: 224px; /* 4 rows × 56px (tweak as you like) */
    }
    .asset-ownership-scrollbar::-webkit-scrollbar {
      width: 6px;
    }
    .asset-ownership-scrollbar::-webkit-scrollbar-thumb {
      background: #bbb;
      border-radius: 10px;
    }
    
    #chartjs-tooltip {
      opacity: 0;
      position: absolute;
      background: rgba(0,0,0,0.85);
      color: #fff;
      border-radius: 8px;
      padding: 10px;
      pointer-events: none;
      font-size: 12px;
      max-width: 250px;
      white-space: normal;
      z-index: 9999;
    }
    .asset-ownership-scrollbar {
      scrollbar-width: thin;
      scrollbar-color: #70a7d5 #f1f1f1;
    }
    .asset-ownership-scrollbar::-webkit-scrollbar {
      width: 6px;
    }
    .asset-ownership-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    .asset-ownership-scrollbar::-webkit-scrollbar-thumb {
      background: #70a7d5;
      border-radius: 10px;
    }

    </style>


    
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
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Total Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="Total_count_percentage" data-target="{{ 100 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="totalAssets" data-target="{{ $total_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad2.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
   
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">On Road Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="onRoadPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="onRoadAssets" data-target="{{ $onRoad_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad2.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Off Road Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="offRoadPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="offRoadAssets" data-target="{{ $offRoad_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad3.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Under Maintainance</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="underMaintenancePercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="underMaintenanceAssets" data-target="{{ $undermaintance_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad4.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Accident Case</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="accidentPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="accidentAssets" data-target="{{ 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad5.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                   <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Total KM Driven</span> </h6>
                                   <small style="color: #26C360; font-weight: 500;" class="count-animation" id="total_km_driven_percentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="total_km_driven_count" data-target="{{ 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad6.png') }}" class="img-fluid">
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
            <div class="card shadow-sm">
                <div class="card-header border-0 pb-0 mb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                      <i class="bi bi-geo-fill me-2"></i> City wise Breakdown
                    </h6>
                    <button class="btn btn-danger btn-sm" title="Map Reset" onclick="ClearMapFilter()">
                        <i class="bi bi-arrow-repeat text-white"></i>
                    </button>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center position-relative">
                    <!-- Loader -->
                    <!--<div id="mapLoader" class="position-absolute d-flex flex-column justify-content-center align-items-center" style="display:none; z-index: 10; background: rgba(255,255,255,0.8); top:0; left:0; right:0; bottom:0;">-->
                    <!--    <div class="spinner-border text-primary" role="status">-->
                    <!--      <span class="visually-hidden">Loading...</span>-->
                    <!--    </div>-->
                    <!--    <small class="mt-2 text-muted">Processing Map...</small>-->
                    <!--</div>-->
                
                    <!-- Map -->
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
                    <div class="card shadow-sm border-0 rounded-3 h-100 position-relative">
                        <div class="card-header bg-white border-0 pb-md-0 pb-lg-3">
                            <h6 class="mb-0 fw-bold text-primary">
                              <i class="bi bi-gear-fill me-2"></i> OEM Type Distribution
                            </h6>

                        </div>
                        <div class="card-body position-relative" style="min-height:220px;">
                            <!-- Preloader -->
                            <!--<div id="oem-chart-preloader" -->
                            <!--     class="d-flex align-items-center justify-content-center"-->
                            <!--     style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.7); z-index:10; display:none;">-->
                            <!--    <div class="spinner-border text-primary" role="status">-->
                            <!--        <span class="visually-hidden">Loading...</span>-->
                            <!--    </div>-->
                            <!--</div>-->
                
                            <div class="row align-items-center g-3">
                                <div class="col-lg-7 col-md-6 col-12 d-flex justify-content-center">
                                    <canvas id="OEMChartType" style="max-height:200px; width:100%;"></canvas>
                                </div>
                                <div class="col-lg-5 col-md-6 col-12">
                                    <div class="OEMChartType-legend" id="OEMLegend" style="max-height:200px; overflow-y:auto;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                  <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-0 pb-md-0 pb-lg-3">
                        <h6 class="mb-0 fw-bold text-primary">
                          <i class="bi bi-bar-chart-line me-2"></i> Utilization
                        </h6>
                    </div>
                    <div class="card-body p-md-0 p-lg-4">
                      <div class="row align-items-center">
                        <!-- Pie Chart Left -->
                        <div class="col-md-7 col-12 d-flex justify-content-center position-relative">
  
                          <!-- Preloader -->
                          <div id="vehicle-summary-chart-preloader" 
                               class="d-flex align-items-center justify-content-center" 
                               style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.7); z-index:10;">
                            <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                            </div>
                          </div>
                        
                          <!-- Chart -->
                          <canvas id="VehicleStatus_SummaryChart" style="max-height:200px;"></canvas>
                        </div>

                        
                        <!-- Legend Right -->
                        <div class="col-md-5 col-12 OEMChartType-legend" style="max-height:100px; overflow-y:auto;" id="vehicle-summary-legend">
                           <!-- dynamic legend render here -->
                        </div>

                      </div>
                    </div>

                  </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $db = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->where('model_id', auth()->user()->id)
        ->first();

    $roles = DB::table('roles')
        ->where('id', $db->role_id)
        ->first();
        
    $vehicle_models = DB::table('ev_tbl_vehicle_models')->where('status',1)->select('id','vehicle_model','make')->get();
    $vehicle_types = \Modules\VehicleManagement\Entities\VehicleType::where('is_active', 1)->select('id','name')->get();
                                    

    
    $map_api_key = \App\Models\BusinessSetting::where('key_name', 'google_map_api_key')->value('value');

    ?>
    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    <h6 class="mb-0 fw-bold text-primary">
                      <i class="bi bi-table me-2"></i> City wise Table Breakdown
                    </h6>

                </div>
                <div class="card-body">
                   <div id="cityWiseTableContainer">
                        <div class="text-center p-3">
                            <small>Loading...</small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
    <!-- Document Validity Overview -->
    <div class="col-md-4 col-12 mb-3">
        <div class="card equal-height">
            <div class="card-header border-0 pb-0 mb-0">
                <h6 class="mb-0 fw-bold text-primary">
                  <i class="bi bi-shield-check me-2"></i> Document Validity Overview
                </h6>

            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table text-center table-bordered bg-light mb-0">
                        <thead class="sticky-top" style="top: 0; z-index: 2;">
                            <tr>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Status</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Active</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Expired</small></th>
                            </tr>
                        </thead>
                        <tbody id="documentValidityTableBody">
                            <tr>
                                <td colspan="3">
                                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Expiration Alerts -->
    <div class="col-md-8 col-12 mb-3">
        <div class="card equal-height">
            <div class="card-header border-0 pb-0 mb-0">
                    <h6 class="mb-0 fw-bold text-primary">
                      <i class="bi bi-alarm me-2"></i> Document Expiration Alerts
                    </h6>

            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                    <table class="table text-center table-bordered bg-light mb-0">
                        <thead class="sticky-top" style="top: 0; z-index: 2;">
                            <tr>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Document Type</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Within 1 Months</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Within 15 Days</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Within 7 Days</small></th>
                                <th class="text-dark" style="background-color:#f0f0f0 !important;"><small>Today</small></th>
                            </tr>
                        </thead>
                        <tbody id="documentAlertsTableBody">
                            <tr>
                                <td colspan="5">
                                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    
     <div class="row mb-3">
        <div class="col-md-6 col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    <h6 class="mb-0 fw-bold text-primary">
                      <i class="bi bi-car-front me-2"></i> Vehicle Status
                    </h6>
                </div>
                <div class="card-body position-relative">
                    
                    <!-- Preloader -->
                <!--<div id="clientWise-chart-preloader" -->
                <!--     class="d-flex align-items-center justify-content-center"-->
                <!--     style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.7); z-index:10;">-->
                <!--    <div class="spinner-border text-primary" role="status">-->
                <!--        <span class="visually-hidden">Loading...</span>-->
                <!--    </div>-->
                <!--</div>-->


        
                    <!-- Chart -->
                    <canvas id="ClientwisebarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-12 mb-3"> 
          <div class="card equal-height">
            <div class="card-header border-0 pb-0 mb-0">
              <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-people me-2"></i> Client Wise Deployment
              </h6>
            </div>
            <div class="card-body position-relative">
              <!-- Preloader -->
              <!--<div id="client-dep-chart-preloader" -->
              <!--     class="d-flex align-items-center justify-content-center"-->
              <!--     style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.7); z-index:10;">-->
              <!--  <div class="spinner-border text-primary" role="status">-->
              <!--    <span class="visually-hidden">Loading...</span>-->
              <!--  </div>-->
              <!--</div>-->
        
              <!-- Scrollable Wrapper -->
              <div class="asset-ownership-scrollbar" style="overflow-y:auto; max-height:300px;">
                <canvas id="AssetOwnershipChart"></canvas>
              </div>
            </div>
          </div>
        </div>



    </div>


    <div class="row mb-3">
        <div class="col-12 mb-3">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-diagram-3 me-2"></i> Client Wise Deployment
                    </h6>
                </div>
    
                <div class="card-body">
    
                    <!-- FIXED WRAPPER for preloader + canvas -->
                    <div id="chartWrapper" style="max-width: 100%; margin: auto; position:relative;">
                        <canvas id="clientDeployedReturnedChart" height="120"></canvas>
                    </div>
    
                </div>
            </div>
        </div>
    </div>


    <!--<div class="row mb-3">-->
    <!--    <div class="col-12">-->
    <!--        <div class="card equal-height">-->
    <!--            <div class="card-header border-0 pb-0 mb-0">-->
    <!--                Insurance Summary-->
    <!--            </div>-->
    <!--            <div class="card-body">-->
    <!--                <div style="max-width: 100%; margin: auto;">-->
    <!--                    <canvas id="InsuranceSummarybarChart" width="400" height="200"></canvas>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <div class="row mb-3">
        <div class="col-12">
            <div class="card equal-height">
                <div class="card-header border-0 pb-0 mb-0">
                    <h6 class="mb-0 fw-bold text-primary">
                      <i class="bi bi-table me-2"></i> Data Table
                    </h6>
                </div>
                <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <!-- Search Bar -->
                             <div class="input-group w-50 me-2 rounded" style="border:1px solid #ced4da;">
                                <input type="text" class="form-control border-0" id="inventory_sum_search"
                                       placeholder="Enter the Chassis Number or Vehicle Number" aria-label="Search">
                                <div id="search-error" class="invalid-feedback text-danger"></div>
                            </div>
                        
                            <div class="d-flex">
                                <div class="bg-white p-2 px-3 rounded me-2 DashboardExportBtn" style="cursor:pointer;border:1px solid #ced4da;" onclick="ExportDashboardDataTable()"><i class="bi bi-file-earmark-arrow-down"></i>  Export
                                </div>
                                <div class="bg-white p-2 px-3 rounded" style="cursor:pointer;border:1px solid #ced4da;" onclick="InventorySummaryOpen()"><i class="bi bi-funnel"></i>  Filter
                                </div>
                            </div>
                        </div>
        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table id="inventorySummaryTable" class="table text-center table-bordered bg-light mb-0">
                                <thead class="sticky-top" style="top: 0; z-index: 2;">
                                    <tr>
                                        <!--<th class="text-dark" style="background-color:#f0f0f0 !important;">Lot ID</th>-->
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Chassis No</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Type</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Number</th>
                                        <!--<th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Model</th>-->
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Make</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">City</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Zone</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Telematics IMEI</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Vehicle Status</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Client Name</th>
                                        <th class="text-dark" style="background-color:#f0f0f0 !important;">Action</th>
                                    </tr>
                                </thead>
                               <tbody>
                        
                                </tbody>
        
                            </table>
                        </div>
                        <!--<div class="d-flex justify-content-between align-items-center mt-2">-->
                        <!--    <button id="prevPageBtn" class="btn btn-sm btn-outline-primary" disabled>Previous</button>-->
                        <!--    <span id="pageInfo" class="fw-bold"></span>-->
                        <!--    <button id="nextPageBtn" class="btn btn-sm btn-outline-primary" disabled>Next</button>-->
                        <!--</div>-->
                    </div>
            </div>
        </div>
    </div>
    
  <?php
     $inventory_locations = \Modules\MasterManagement\Entities\InventoryLocationMaster::where('status',1)->select('id','name')->get();
     $location_data = \Illuminate\Support\Facades\DB::table('ev_tbl_city')->where('status', 1)->select('id','city_name')->get();
     $accountablity_types = \Modules\MasterManagement\Entities\EvTblAccountabilityType::where('status', 1)->get();
     $customers = \Modules\MasterManagement\Entities\CustomerMaster::where('status', 1)->select('id', 'trade_name')->get();  
  ?>


   <div class="offcanvas offcanvas-end" tabindex="-1" id="DashoffcanvasRightAMV" aria-labelledby="DashoffcanvasRightAMVLabel">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="DashoffcanvasRightAMVLabel">Asset Management Filter</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDashboardFilter()">Clear All</button>
                <button class="btn btn-success w-50 applyFilterBtn" onclick="applyFilter()">Apply</button>
            </div>
         
           
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <!--<div class="form-check mb-3">-->
                    <!--  <input class="form-check-input select_time_line" type="radio" value="today" {{ request('timeline') == 'today' ? 'checked' : '' }} name="STtimeLine" id="timeLine1">-->
                    <!--  <label class="form-check-label" for="timeLine1">-->
                    <!--    Today-->
                    <!--  </label>-->
                    <!--</div>-->
                    <!--<div class="form-check mb-3">-->
                    <!--  <input class="form-check-input select_time_line" type="radio" value="this_week" {{ request('timeline') == 'this_week' ? 'checked' : '' }} name="STtimeLine" id="timeLine2">-->
                    <!--  <label class="form-check-label" for="timeLine2">-->
                    <!--   This Week-->
                    <!--  </label>-->
                    <!--</div>-->
                    <!--<div class="form-check mb-3">-->
                    <!--  <input class="form-check-input select_time_line" type="radio" value="this_month" {{ request('timeline') == 'this_month' ? 'checked' : '' }} name="STtimeLine" id="timeLine3">-->
                    <!--  <label class="form-check-label" for="timeLine3">-->
                    <!--   This Month-->
                    <!--  </label>-->
                    <!--</div>-->
                    <!--<div class="form-check mb-3">-->
                    <!--  <input class="form-check-input select_time_line" type="radio" value="this_year" {{ request('timeline') == 'this_year' ? 'checked' : '' }} name="STtimeLine" id="timeLine4">-->
                    <!--  <label class="form-check-label" for="timeLine4">-->
                    <!--   This Year-->
                    <!--  </label>-->
                    <!--</div>-->
                    
                    <div class="mb-3">
                        <label class="form-label" for="quick_date_filter">Select Date Range</label>
                        <select id="quick_date_filter" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="last_15_days">Last 15 Days</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    
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
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Option</h6></div>
               </div>
               <div class="card-body">
                 
                    <div class="mb-3">
                        <label class="form-label" for="location_id">City</label>
                        <select name="location_id[]" id="location_id" class="form-control custom-select2-field" onchange="getZones()" multiple>
                            <option value="" disabled>Select City</option>
                            <option value="all">All</option>
                            @if(isset($location_data))
                                @foreach($location_data as $l)
                                <option value="{{$l->id}}" {{ $location_id == $l->id ? 'selected' : '' }}>{{$l->city_name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                    <div class="mb-3">
                        <label class="form-label" for="zoneId">Select Zone</label>
                        <select name="zone_id[]" id="zoneId" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select a city first</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="v_type">Vehicle Type</label>
                        <select name="v_type[]" id="v_type" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Type</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_types))
                                @foreach($vehicle_types as $val)
                                <option value="{{$val->id}}" {{ $vehicle_type == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="v_model">Vehicle Model</label>
                        <select name="v_model[]" id="v_model" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Model</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" {{ $vehicle_model == $val->id ? 'selected' : '' }}>{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label" for="v_make">Vehicle Make</label>
                        <select name="v_make[]" id="v_make" class="form-control custom-select2-field" multiple>
                            <option value="" disabled>Select Make</option>
                            <option value="all">All</option>
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" {{ $vehicle_model == $val->id ? 'selected' : '' }}>{{$val->make}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div> 
                    
                     <div class="mb-3">
                        <label class="form-label" for="accountability_type_id">Accountability Type</label>
                        <select name="accountability_type_id" id="accountability_type_id" class="form-control custom-select2-field">
                            <option value="" {{ $accountability_type_id == 'all' ? 'selected' : '' }}>All</option>
                            @if(isset($accountablity_types))
                                @foreach($accountablity_types as $type)
                                <option value="{{$type->id}}" {{ $accountability_type_id == $type->id ? 'selected' : '' }}>{{$type->name ?? ''}}</option>
                                @endforeach
                            @endif

                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="customer_id">Customer</label>
                        <select name="customer_id[]" id="customer_id" class="form-control custom-select2-field" multiple>
                           <option value="" disabled>Select Customer</option>
                            <option value="all">All</option>
                            @if(isset($customers))
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}" {{ $customer_id == $customer->id ? 'selected' : '' }}>{{$customer->trade_name ?? ''}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
                     <div class="mb-3">
                      <label class="form-label" for="vehicle_status">Vehicle Status</label>
                      <select name="vehicle_status[]" id="vehicle_status" class="form-select custom-select2-field" multiple>
                        <option value="" disabled>Select </option>
                        <option value="all">All</option>
                        @if(isset($inventory_locations))
                          @foreach($inventory_locations as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
               </div>
            </div>
            
            
           <!--<div class="card mb-3">-->
           <!--    <div class="card-header p-2">-->
           <!--        <div><h6 class="custom-dark">Date Between</h6></div>-->
           <!--    </div>-->
           <!--    <div class="card-body">-->
 
           <!--         <div class="mb-3">-->
           <!--             <label class="form-label" for="FromDate">From Date</label>-->
           <!--             <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">-->
           <!--         </div>-->
                    
           <!--         <div class="mb-3">-->
           <!--             <label class="form-label" for="ToDate">To Date</label>-->
           <!--             <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date}}">-->
           <!--         </div>-->
  
           <!--    </div>-->
           <!-- </div>-->
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDashboardFilter()">Clear All</button>
                <button class="btn btn-success w-50 applyFilterBtn" onclick="applyFilter()">Apply</button>
            </div>
            
          </div>
        </div>

        <div class="offcanvas offcanvas-start" data-bs-keyboard="false" tabindex="-1" id="InventorySummaryFil" aria-labelledby="InventorySummaryFilLabel">

          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="InventorySummaryFilLabel">Data Table Filter</h5>
            <!--<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>-->
          </div>
          <div class="offcanvas-body">
             <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearDataTbleFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="InSumFilter_Function()">Apply</button>
            </div>
            <div class="card mb-3">
              <div class="card-header p-2">
                <h6 class="custom-dark">Select Status</h6>
              </div>
              <div class="card-body">
                
                <div class="mb-3">
                  <label class="form-label" for="asset_fil_status">Select Status</label>
                  <select name="asset_fil_status" id="asset_fil_status" class="form-select">
                    <option value="all">All</option>
                    @if(isset($inventory_locations))
                      @foreach($inventory_locations as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="vehicle_make">Vehicle Make</label>
                  <select name="vehicle_make" id="vehicle_make" class="form-select">
                    <option value="all">All</option>
                    @if(isset($vehicle_models))
                      @foreach($vehicle_models as $data)
                        <option value="{{ $data->id }}">{{ $data->make }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
        
                <div class="mb-3 position-relative">
                  <label class="form-label" for="customer_search">Enter a Customer</label>
                  <input type="text" id="customer_search" class="form-control" placeholder="Enter a  customer name..." autocomplete="off">
                  <div id="customer_results" class="dropdown-menu w-100"></div>
                  <input type="hidden" name="customer_name" id="customer_name">
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
     function toggleDateFields() {
        let value = $("#quick_date_filter").val();

        if (value === "custom") {
            $("#FromDate").closest(".mb-3").show();
            $("#ToDate").closest(".mb-3").show();
        } else {
            $("#FromDate").closest(".mb-3").hide();
            $("#ToDate").closest(".mb-3").hide();
            $("#FromDate").val("");
            $("#ToDate").val("");
        }
    }

    toggleDateFields();

    $("#quick_date_filter").on("change", function () {
        toggleDateFields();
    });
    function initSelectAll(selector) {
          let internalChange = false;
          $(document).on("mousedown touchstart", selector, function () {
            const prev = $(this).val() || [];
            $(this).data("prevSelection", prev);
          });
          $(document).on("focus", selector, function () {
            const prev = $(this).val() || [];
            $(this).data("prevSelection", prev);
          });
          $(selector).on("change", function () {
            if (internalChange) return;
            const $el = $(this);
            let prev = $el.data("prevSelection") || [];
            let current = $el.val() || [];
            prev = prev.map(String);
            current = current.map(String);
            internalChange = true;
            if (prev.includes("all") && current.includes("all") && current.length > 1) {
              const cleaned = current.filter((v) => v !== "all");
              $el.val(cleaned).trigger("change.select2");
              $el.data("prevSelection", cleaned);
              internalChange = false;
              return;
            }
            if (!prev.includes("all") && current.includes("all")) {
              $el.val(["all"]).trigger("change.select2");
              $el.data("prevSelection", ["all"]);
              internalChange = false;
              return;
            }
            if (current.includes("all") && current.length > 1) {
              $el.val(["all"]).trigger("change.select2");
              $el.data("prevSelection", ["all"]);
              internalChange = false;
              return;
            }
            if (!current.includes("all")) {
              const cleaned = current.filter((v) => v !== "all");
              if (cleaned.length !== current.length) {
                $el.val(cleaned).trigger("change.select2");
                $el.data("prevSelection", cleaned);
                internalChange = false;
                return;
              }
            }
            $el.data("prevSelection", current);
            internalChange = false;
          });
    }
    $(document).ready(function () {
      initSelectAll("#v_type");
      initSelectAll("#v_model");
      initSelectAll("#v_make");
      initSelectAll("#v_model");
      initSelectAll("#v_make");
      initSelectAll("#location_id");
      initSelectAll("#zoneId");
      initSelectAll("#customer_id");
      initSelectAll("#vehicle_status");
    });
    function getZones() {
      let cityIds = $("#location_id").val();
      let ZoneDropdown = $("#zoneId");
      ZoneDropdown.empty().append('<option value="">Loading...</option>');
      if (cityIds && cityIds.length > 0) {
        $.ajax({
          url: "{{ route('global.get_multi_city_zones') }}",
          type: "GET",
          data: { city_id: cityIds }, // pass array
          success: function (response) {
            ZoneDropdown.empty()
              .append('<option value="" disabled>Select Zone</option>')
              .append('<option value="all">All</option>');
            if (response.data && response.data.length > 0) {
              $.each(response.data, function (key, zone) {
                ZoneDropdown.append(
                  `<option value="${zone.id}">${zone.name}</option>`
                );
              });
            } else {
              ZoneDropdown.append(
                '<option value="" disabled>No Zones available</option>'
              );
            }
          },
          error: function () {
            ZoneDropdown.empty().append(
              '<option value="" disabled>Error loading zones</option>'
            );
          },
        });
      } else {
        ZoneDropdown.empty().append(
          '<option value="" disabled>Select a city first</option>'
        );
      }
    }



   function get_filter_params(chartType) {
        let timeline = $("#quick_date_filter").val();
        let fromDate = $("#FromDate").val();
        let toDate = $("#ToDate").val();
    
        if (timeline === "custom") {
            timeline = '';
        } else {
            fromDate = '';
            toDate = '';
        }
        return {
            chart_type: chartType,
            timeline: timeline || '',
            from_date: fromDate || '',
            to_date: toDate || '',
            vehicle_type: $("#v_type").val() || [],
            vehicle_model: $("#v_model").val() || [],
            vehicle_make: $("#v_make").val() || [],
            location_id: $("#location_id").val() || [],
            zone_id: $("#zoneId").val() || [],
            accountability_type_id: $("#accountability_type_id").val() || '',
            customer_id: $("#customer_id").val() || [],
            vehicle_status: $("#vehicle_status").val() || [],
        };
    }
    function clearDashboardFilter() {
        $("#quick_date_filter").val('').trigger('change');
        toggleDateFields();
        $("#v_type").val([]).trigger('change');  
        $("#v_model").val([]).trigger('change');
        $("#v_make").val([]).trigger('change');
        $("#location_id").val([]).trigger('change');
        $("#zoneId").val([]).trigger('change');
        $("#accountability_type_id").val('').trigger('change');
        $("#customer_id").val([]).trigger('change');
        $("#vehicle_status").val([]).trigger('change');
        applyFilter();
        AMVDashRightSideFilerClose();
    }
    function applyFilter() {
        SummaryCard_ShowCount(),
        fetchMapData(),
        OEMChartFunction(),
        updateCityWiseTable(),
        VSummaryChartFunction(),
        fetchDocumentTables(),
        ClientwisebarChartFunction(),
        ClientwiseDeploymentFunction(),
        ClientwiseDeployedandReturnedFunction(),
        loadInventorySummary()
        
        AMVDashRightSideFilerClose();
       
    }
    
    async function SummaryCard_ShowCount() {

            $("#totalAssets, #onRoadAssets, #offRoadAssets, #underMaintenanceAssets, #accidentAssets,#total_km_driven_count").each(function () {
                $(this).text("");
                $(this).addClass('text-muted');
                $(this).append(' <i class="fas fa-spinner fa-spin ms-1"></i>');
            });
        
            $("#onRoadPercentage, #offRoadPercentage, #underMaintenancePercentage, #accidentPercentage,#Total_count_percentage,#total_km_driven_percentage").each(function () {
                $(this).text("");
                $(this).addClass('text-muted');
                $(this).append(' <i class="fas fa-spinner fa-spin ms-1"></i>');
            });
        
            try {
                const params = get_filter_params("SummaryCardcountShow");   
                const response = await axios.get("{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}",
                   { params }
                );
        
                let res = response.data;
        
                if (res.status) {
        
                    let data = res.count_data;
                    $("#totalAssets, #onRoadAssets, #offRoadAssets, #underMaintenanceAssets, #accidentAssets").find("i").remove();
                    $("#onRoadPercentage, #offRoadPercentage, #underMaintenancePercentage, #accidentPercentage").find("i").remove();
                    $(".text-muted").removeClass("text-muted");
        
                    var total_km_driven_count = 0;
                    var Total_count_percentage = 100;
                    var total_km_driven_percentage = 0;
                    animateCounter("#totalAssets", data.total_asset_count);
                    animateCounter("#onRoadAssets", data.onRoad_asset_count);
                    animateCounter("#offRoadAssets", data.offRoad_asset_count);
                    animateCounter("#underMaintenanceAssets", data.undermaintanance_asset_count);
                    animateCounter("#accidentAssets", data.accident_asset_count);
                    animateCounter("#total_km_driven_count", total_km_driven_count);
        
                    // Percentages
                    animateCounter("#Total_count_percentage", Total_count_percentage, "%");
                    animateCounter("#onRoadPercentage", data.onRoad_percentage, "%");
                    animateCounter("#offRoadPercentage", data.offRoad_percentage, "%");
                    animateCounter("#underMaintenancePercentage", data.undermaintanance_percentage, "%");
                    animateCounter("#accidentPercentage", data.accidentcase_percentage, "%");
                    animateCounter("#total_km_driven_percentage", total_km_driven_percentage, "%");
                }
        
            } catch (error) {
                console.error("SummaryCard Error:", error);
            }
        }

        function animateCounter(selector, value, suffix = '') {
            $({ Counter: 0 }).animate({ Counter: value }, {
                duration: 1200, // 1.2 seconds
                easing: 'swing',
                step: function (now) {
                    $(selector).text(Math.floor(now) + suffix);
                },
                complete: function() {
                    $(selector).text(value + suffix);
                }
            });
        }


       $("#customer_search").on("keyup", function() {
            let search = $(this).val();
            if(search.length > 1){
                $.ajax({
                    url: "{{ route('admin.asset_management.asset_master.inventory_summary.get_name') }}",
                    type: "GET",
                    data: { search: search },
                    success: function(data){
                        let options = "";
                        if(data.length > 0){
                            data.forEach(item => {
                                options += `<button class="dropdown-item" 
                                                onclick="selectCustomer('${item.id}','${item.trade_name}')">
                                                ${item.id} / ${item.trade_name}
                                            </button>`;
                            });
                        } else {
                            options = `<span class="dropdown-item disabled">No records found</span>`;
                        }
                        $("#customer_results").html(options).addClass("show");
                    }
                });
            } else {
                $("#customer_results").html('');
                $("#customer_name").val('');
                $("#customer_results").removeClass("show");
            }
        });

        function selectCustomer(id, name){
            $("#customer_search").val(name);
            $("#customer_name").val(id);
            $("#customer_results").removeClass("show");
        }

    </script>
    

    
    <script>

    function runCounters() {
        const counters = document.querySelectorAll('.count-animation');
        const speed = 200; // bigger = slower
    
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const hasPercent = counter.getAttribute("data-target-type") === "percent";
    
            const updateCount = () => {
                let count = +counter.innerText.replace(/[^0-9]/g, "");
                const inc = Math.max(target / speed, 1);
    
                if (count < target) {
                    count = Math.min(count + inc, target);
                    counter.innerText = hasPercent 
                        ? Math.floor(count).toLocaleString() + "%" 
                        : Math.floor(count).toLocaleString();
    
                    requestAnimationFrame(updateCount);
                } else {
                    counter.innerText = hasPercent 
                        ? target.toLocaleString() + "%" 
                        : target.toLocaleString();
                }
            };
    
            updateCount();
        });
    }

</script>


<script>

    const width = 950, height = 650;
    
    const projection = d3.geoMercator()
      .center([78.9629, 22.5937])
      .scale(1200)
      .translate([width / 2, height / 2]);
    
    const path = d3.geoPath().projection(projection);
    
    const svg = d3.select("#indiaMapUnique")
      .attr("viewBox", `0 0 ${width} ${height}`);
    const cityLayer = svg.append("g").attr("id", "cityLayer");

    async function loadIndiaMap() {
      try {
        const india = await d3.json(
          "https://raw.githubusercontent.com/geohacker/india/master/state/india_state.geojson"
        );
    
        svg.append("g")
          .selectAll("path")
          .data(india.features)
          .enter()
          .append("path")
          .attr("d", path)
          .attr("class", "land");
    
        // Fetch map data
        await fetchMapData();
    
      } catch (err) {
        console.error("Error loading India map:", err);
      }
    }

    async function fetchMapData() {
      try {
        $("#mapLoader").addClass("active");
    
        const params = get_filter_params("MapChart"); 
    
         const url = "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}?" 
                + new URLSearchParams(params).toString();
    
        const response = await fetch(url);
        const res = await response.json();
    
        if (res.status && res.map_data) {
          renderCities(res.map_data);
        }
    
      } catch (error) {
        console.error("Error fetching map data:", error);
    
      } finally {
        $("#mapLoader").removeClass("active");
      }
    }

    function renderCities(cities) {
          $("#mapLoader").removeClass("active");
            cityLayer.raise();
           cityLayer.selectAll("*").remove();
          const placedRight = [];
          const placedLeft = [];
        
          function getNonOverlappingPosSide(x, y, side = "right") {
            let lx, ly;
        
            if (side === "right") {
              lx = x + 140;
              ly = y;
        
              for (const pos of placedRight) {
                if (Math.abs(ly - pos) < 50) ly = pos + 60;
              }
        
              placedRight.push(ly);
        
            } else {
              lx = x - 180;
              ly = y;
        
              for (const pos of placedLeft) {
                if (Math.abs(ly - pos) < 50) ly = pos + 60;
              }
        
              placedLeft.push(ly);
            }
        
            return [lx, ly];
          }
        
          function getCityColor(i) {
            const colors = [
              "#2980b9", "#ff7f0e", "#2ca02c", "#d62728", "#f681a4",
              "#8e44ad", "#bd264b", "#7f7f7f", "#bcbd22", "#17becf",
              "#393b79", "#637939", "#8c6d31", "#843c39", "#7b4173",
              "#3182bd", "#31a354", "#756bb1", "#636363", "#e6550d",
              "#969696", "#cedb9c", "#8ca252", "#bd9e39", "#ad494a",
              "#a55194", "#6baed6", "#74c476", "#9e9ac8", "#bdbdbd"
            ];
            return colors[i % colors.length];
          }

          cities.forEach((city, i) => {
            const [x, y] = projection(city.coords);
            const side = i % 2 === 0 ? "right" : "left";
            const [labelX, labelY] = getNonOverlappingPosSide(x, y, side);
            const color = getCityColor(i);
        
            cityLayer.append("circle")
              .attr("cx", x)
              .attr("cy", y)
              .attr("r", 2)
              .attr("class", "city-dot")
              .style("stroke", color)
              .style("fill", color);
        
            const midX = side === "right" ? x + 60 : x - 60;
        
            cityLayer.append("path")
              .attr("d", `M${x},${y} L${midX},${y} L${labelX},${labelY}`)
              .attr("class", "connector")
              .style("stroke", color)
              .style("fill", "none");
        
            const labelGroup = cityLayer.append("g");
        
            labelGroup.append("rect")
              .attr("x", labelX - (side === "right" ? 0 : 120))
              .attr("y", labelY - 18)
              .attr("width", 130)
              .attr("height", 48)
              .attr("rx", 4)
              .style("fill", color)
              .style("stroke", color)
              .style("cursor", "pointer")
              .on("click", () => MapFilter(city.location_id));
        
            labelGroup.append("text")
              .attr("x", labelX - (side === "right" ? 0 : 120) + 65)
              .attr("y", labelY)
              .attr("text-anchor", "middle")
              .style("fill", "#fff")
              .text(city.name)
              .style("cursor", "pointer")
              .on("click", () => MapFilter(city.location_id));
        
            labelGroup.append("text")
              .attr("x", labelX - (side === "right" ? 0 : 120) + 65)
              .attr("y", labelY + 16)
              .attr("dy", "3px")
              .attr("text-anchor", "middle")
              .style("fill", "#fff")
              .text(city.value.toLocaleString())
              .style("cursor", "pointer")
              .on("click", () => MapFilter(city.location_id));
          });
    }

    function MapFilter(lo_id) {
      const locInput = document.getElementById('location_id');
      if (locInput) locInput.value = lo_id;
      applyFilter();
    }
    function ClearMapFilter() {
      const locInput = document.getElementById('location_id');
      if (locInput) locInput.value = '';
      applyFilter();
    }


    async function fetchDocumentTables() {
        $("#documentValidityTableBody").html(`
            <tr>
                <td colspan="3" class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `);
        $("#documentAlertsTableBody").html(`
            <tr>
                <td colspan="5" class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `);
        try {
            const params = get_filter_params("DocumentValidityTable"); 
    
            const url = "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}?" 
                        + new URLSearchParams(params).toString();
    
            const response = await fetch(url);
            if (!response.ok) throw new Error("Network error");
            const res = await response.json();
            if (res.document_alerts) {
                let validityRows = "";
                if (res.document_alerts.length > 0) {
                    let totalActiveValidity = 0;
                    let totalToday = 0;
    
                    res.document_alerts.forEach(doc => {
                        let active_validity = doc.within_1_month && res.document_validity_count
                            ? res.document_validity_count - doc.within_1_month
                            : res.document_validity_count;
    
                        totalActiveValidity += Number(active_validity || 0);
                        totalToday += Number(doc.within_1_month || 0);
    
                        validityRows += `
                            <tr>
                                <td><small>${doc.document_type || ''}</small></td>
                                <td><small>${active_validity ?? 0}</small></td>
                                <td><small>${doc.within_1_month ?? 0}</small></td>
                            </tr>
                        `;
                    });

                    validityRows += `
                        <tr style="font-weight:bold;">
                            <td style="background-color:#f0f0f0 !important;"><small>Total</small></td>
                            <td style="background-color:#f0f0f0 !important;"> <small>${totalActiveValidity.toLocaleString('en-IN')}</small></td>
                            <td style="background-color:#f0f0f0 !important;"><small>${totalToday.toLocaleString('en-IN')}</small></td>
                        </tr>`;
                } else {
                    validityRows = `<tr><td colspan="3" class="text-center">No Data Available</td></tr>`;
                }
    
                $("#documentValidityTableBody").html(validityRows);

                let alertRows = "";
    
                if (res.document_alerts.length > 0) {
                    let g_1m = 0, g_15d = 0, g_7d = 0, g_today = 0;
    
                    res.document_alerts.forEach(doc => {
                        g_1m += Number(doc.within_1_month || 0);
                        g_15d += Number(doc.within_15_days || 0);
                        g_7d += Number(doc.within_7_days || 0);
                        g_today += Number(doc.today || 0);
    
                        alertRows += `
                            <tr>
                                <td><small>${doc.document_type || ''}</small></td>
                                <td><small>${doc.within_1_month ?? 0}</small></td>
                                <td><small>${doc.within_15_days ?? 0}</small></td>
                                <td><small>${doc.within_7_days ?? 0}</small></td>
                                <td><small>${doc.today ?? 0}</small></td>
                            </tr>
                        `;
                    });

                    alertRows += `
                        <tr style="font-weight:bold;">
                            <td style="background-color:#f0f0f0 !important;"><small>Total</small></td>
                            <td style="background-color:#f0f0f0 !important;"><small>${g_1m.toLocaleString('en-IN')}</small></td>
                            <td style="background-color:#f0f0f0 !important;"><small>${g_15d.toLocaleString('en-IN')}</small></td>
                            <td style="background-color:#f0f0f0 !important;"><small>${g_7d.toLocaleString('en-IN')}</small></td>
                            <td style="background-color:#f0f0f0 !important;"><small>${g_today.toLocaleString('en-IN')}</small></td>
                        </tr>`;
                } else {
                    alertRows = `<tr><td colspan="5" class="text-center">No Data Available</td></tr>`;
                }
    
                $("#documentAlertsTableBody").html(alertRows);
            }
    
        } catch (error) {
            console.error("Error fetching document table data:", error);
    
            $("#documentValidityTableBody").html(
                `<tr><td colspan="3" class="text-center">Error Loading Data</td></tr>`
            );
    
            $("#documentAlertsTableBody").html(
                `<tr><td colspan="5" class="text-center">Error Loading Data</td></tr>`
            );
        }
    }




    async function VSummaryChartFunction() {

        $("#vehicle-summary-chart-preloader").addClass('active');
    
        try {
            const params = get_filter_params("VehicleStatusSummaryChart"); 
            const queryString = new URLSearchParams(params).toString();
            const url = "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}" + "?" + queryString;
    
            const response = await fetch(url);
            if (!response.ok) throw new Error("Network error");
    
            const data = await response.json();
    
            if (data.status) {
                let summary = data.vehicle_summary || {};
                let grandTotal = summary.total_assets || 0;
                let totalPct = summary.utilization ? summary.utilization : 0;
    
                const ctx = document.getElementById('VehicleStatus_SummaryChart').getContext('2d');
                if (window.VehicleStatusChart) window.VehicleStatusChart.destroy();
    
                let totalpctValue = totalPct;
    
                // Center text plugin
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw(chart) {
                        const { ctx, chartArea: { width, height } } = chart;
                        ctx.save();
                        ctx.font = "bold 16px Arial";
                        ctx.fillStyle = "#111827";
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.fillText(totalpctValue + "%", width / 2, height / 1.5);
                        ctx.restore();
                    }
                };
    
                // Build Chart
                window.VehicleStatusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ["On Road", "Off Road"],
                        datasets: [{
                            data: [summary.total_onroad || 0, summary.total_offroad || 0],
                            backgroundColor: ['#10b981', '#eaeaea'],
                            borderWidth: 0,
                            cutout: '60%'
                        }]
                    },
                    options: {
                        rotation: -90,
                        circumference: 180,
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    },
                    plugins: [centerTextPlugin]
                });
    
                // Build Legend
                let allTypes = Object.values(summary.types || {});
                let legendHtml = "";
    
                if (allTypes.length > 0) {
                    allTypes.forEach((v) => {
                        let pct = v.utilization ? v.utilization : 0;
                        legendHtml += `
                            <div class="mb-2 d-flex align-items-center">
                              <span style="width:14px;height:14px;background-color:#10b981;border-radius:50%;margin-right:8px;"></span>
                              <span style="margin-right:5px;">${v.vehicle_type_name}</span>
                              <span class="ms-2 text-secondary">${pct}%</span>
                            </div>
                        `;
                    });
                } else {
                    let fallbackTypes = @json($vehicle_types);
                    fallbackTypes.forEach((type) => {
                        legendHtml += `
                            <div class="mb-2 d-flex align-items-center">
                              <span style="width:14px;height:14px;background-color:#eaeaea;border-radius:50%;margin-right:8px;"></span>
                              <span style="margin-right:5px;">${type.name}</span>
                              <span class="ms-2 text-secondary">0%</span>
                            </div>
                        `;
                    });
                }
    
                $("#vehicle-summary-legend").html(legendHtml);
            }
    
        } catch (error) {
            console.error("Error loading Summary Chart:", error);
        } finally {
            $("#vehicle-summary-chart-preloader").removeClass('active');
        }
    }


    // function showPreloader() {
    //     if ($("#clientWise-chart-preloader").length === 0) {
    //         let preloader = `
    //             <div id="clientWise-chart-preloader" 
    //                  class="d-flex align-items-center justify-content-center"
    //                  style="position:absolute; top:0; left:0; right:0; bottom:0; 
    //                         background:rgba(255,255,255,0.7); z-index:10;">
    //                 <div class="spinner-border text-primary" role="status">
    //                     <span class="visually-hidden">Loading...</span>
    //                 </div>
    //             </div>`;
    //         $(".card-body.position-relative").append(preloader); 
    //     }
    // }
    
    // function hidePreloader() {
    //     $("#clientWise-chart-preloader").remove(); 
    // }

    async function ClientwisebarChartFunction() {

        // showPreloader();
        const params = get_filter_params("ClientwisebarChart"); 
    
        const url = "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}?" 
                    + new URLSearchParams(params).toString();
    
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Network Error");
            const data = await response.json();
    
            if (data.data && data.data.length > 0) {
                let labels = [...new Set(data.data.map(item => item.name))];
                let vehicleTypes = [...new Set(data.data.map(item => item.vehicle_type_name))];
                let vehicle_status_count = data.data.reduce(
                    (sum, item) => sum + (item.vehicle_count || 0), 0
                );
    
                console.log("Vehicle Status Count:", vehicle_status_count);
                let datasets = vehicleTypes.map((vt, idx) => ({
                    label: vt,
                    data: labels.map(lbl => {
                        let rec = data.data.find(item => item.name === lbl && item.vehicle_type_name === vt);
                        return rec ? rec.vehicle_count : 0;
                    }),
                    backgroundColor: getColor(idx)
                }));
                const ctx = document.getElementById('ClientwisebarChart').getContext('2d');
    
                if (window.clientwiseChart) {
                    window.clientwiseChart.destroy();
                }
                window.clientwiseChart = new Chart(ctx, {
                    type: 'bar',
                    data: { labels, datasets },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true, position: 'top' }
                        },
                        scales: {
                            x: { stacked: true },
                            y: { stacked: true, beginAtZero: true }
                        }
                    }
                });
            }
            else {
                const ctx = document.getElementById('ClientwisebarChart').getContext('2d');
                if (window.clientwiseChart) window.clientwiseChart.destroy();
    
                window.clientwiseChart = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true },
                    plugins: [{
                        id: 'noData',
                        afterDraw: (chart) => {
                            let ctx = chart.ctx;
                            let width = chart.width;
                            let height = chart.height;
                            chart.clear();
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.font = '16px sans-serif';
                            ctx.fillText('No data available', width / 2, height / 2);
                            ctx.restore();
                        }
                    }]
                });
            }
    
        } catch (error) {
            console.error("Clientwise Bar Chart Error:", error);
        } finally {
            // hidePreloader();
        }

        function getColor(i) {
            let colors = ['#3b82f6', '#f97316', '#10b981', '#e11d48', '#8b5cf6'];
            return colors[i % colors.length];
        }
    }
    
    function showOEMPreloader() {
        const container = document.querySelector('.card-body.position-relative');
        const oldLoader = document.getElementById("oem-chart-preloader");
        if (oldLoader) oldLoader.remove();
        const loader = document.createElement("div");
        loader.id = "oem-chart-preloader";
        loader.style.cssText = `
            position:absolute;
            top:0; left:0; right:0; bottom:0;
            background:rgba(255,255,255,0.7);
            z-index:10;
            display:flex;
            align-items:center;
            justify-content:center;
        `;
    
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
    
        container.appendChild(loader);
    }
    
    function hideOEMPreloader() {
        const loader = document.getElementById("oem-chart-preloader");
        if (loader) loader.remove();
    }


    async function OEMChartFunction() {
        showOEMPreloader(); 
        console.log("function called OEM Chart");
        try {
            const params = get_filter_params("OEMChart");
            const response = await axios.get(
                "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}",
                { params }
            );
            const res = response.data;
            const legendContainer = document.getElementById("OEMLegend");
            legendContainer.innerHTML = "";
            const canvas = document.getElementById("OEMChartType");
            const ctx = canvas.getContext("2d");
            if (window.oemChartInstance) {
                window.oemChartInstance.destroy();
                window.oemChartInstance = null;
            }

            if (!res.status || res.brandWiseData.length === 0 || res.total_vh_count === 0) {
                canvas.height = canvas.height;
                ctx.font = "bold 16px Arial";
                ctx.fillStyle = "#999";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText("No Data Available", canvas.width / 2, canvas.height / 2);
                legendContainer.innerHTML =
                    `<div class="text-muted text-center">No Data Available</div>`;
    
                return;
            }
            const chartData = res.brandWiseData;
            const chartLabels = chartData.map(x => x.brand);
            const chartCounts = chartData.map(x => x.total);
            const chartColors = [
                "#2980b9", "#f1c40f", "#e63946", "#27ae60", "#e67e22", "#2c3e50",
                "#16a085", "#8e44ad", "#d35400", "#7f8c8d", "#34495e", "#c0392b",
                "#9b59b6", "#1abc9c", "#f39c12", "#2ecc71"
            ];
            window.oemChartInstance = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartCounts,
                        backgroundColor: chartColors,
                        borderColor: "#fff",
                        borderWidth: 2,
                        cutout: "40%"
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            padding: 10,
                            displayColors: false,
                            backgroundColor: "rgba(0,0,0,0.8)",
                            callbacks: {
                                title: () => "",
                                label: function(context) {
                                    const item = chartData[context.dataIndex];
                                    const total = chartCounts.reduce((a,b)=>a+b,0);
                                    const percentage = ((item.total / total) * 100).toFixed(1);
    
                                    let details = [
                                        `Brand : ${item.brand}`,
                                        `Total : ${item.total} (${percentage}%)`
                                    ];
    
                                    if (item.details?.length) {
                                        item.details.forEach(d => {
                                            details.push(`• ${d.model} (${d.type}) : ${d.count}`);
                                        });
                                    }
                                    return details;
                                }
                            }
                        }
                    }
                }
            });
            chartData.forEach((item, i) => {
                const div = document.createElement("div");
                div.classList.add("OEMChartTypelegend-item");
                div.innerHTML = `
                    <div class="OEMChartType-dot" style="background:${chartColors[i]}"></div>
                    ${item.brand} - <span class="text-muted ms-2">${item.total}</span>
                `;
                legendContainer.appendChild(div);
            });
    
        } catch (err) {
            console.error("OEM Chart Error:", err);
        } finally {
            console.log("function called OEM Chart ended");
            setTimeout(() => {
                hideOEMPreloader(); 
            }, 50);
        }
    }

    function showCDPreloader() {
        // if ($("#client-dep-chart-preloader").length === 0) {
        //     let preloader = `
        //         <div id="clientWise-chart-preloader" 
        //              class="d-flex align-items-center justify-content-center"
        //              style="position:absolute; top:0; left:0; right:0; bottom:0; 
        //                     background:rgba(255,255,255,0.7); z-index:10;">
        //             <div class="spinner-border text-primary" role="status">
        //                 <span class="visually-hidden">Loading...</span>
        //             </div>
        //         </div>`;
        //     $(".card-body.position-relative").append(preloader); 
        // }
    }
    
    function hideCDPreloader() {
        // $("#client-dep-chart-preloader").remove();
    }

    async function ClientwiseDeploymentFunction() {
        try {
            // showCDPreloader();
            const params = get_filter_params("ClientwiseDeployment"); 
            const response = await $.ajax({
                url: "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}",
                type: "GET",
                data: params
            });

            
            if (response.data && response.data.length > 0) {
                const labels = response.data.map(item => item.client_name);
                const actualData = response.data.map(item => item.depployed_count);
                if (window.clientDeployChart) {
                    window.clientDeployChart.destroy();
                    window.clientDeployChart = null;
                }
                const defaultFill = actualData.map(() => 80);
                const remainingData = actualData.map(() => 20);
                const ROW_HEIGHT = 56;
                const wrapper = document.querySelector('.asset-ownership-scrollbar');
                wrapper.style.height = (Math.min(4, labels.length) * ROW_HEIGHT) + 'px';
    
                const canvas = document.getElementById('AssetOwnershipChart');
                canvas.height = labels.length * ROW_HEIGHT;
                canvas.width = wrapper.clientWidth;
    
                const ctx = canvas.getContext('2d');
                window.clientDeployChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Filled',
                                data: defaultFill,
                                backgroundColor: '#70a7d5',
                                borderSkipped: false,
                                borderRadius: { topLeft: 10, bottomLeft: 10 },
                                barThickness: 24
                            },
                            {
                                label: 'Remaining',
                                data: remainingData,
                                backgroundColor: '#eaeaea',
                                borderSkipped: false,
                                borderRadius: { topRight: 10, bottomRight: 10 },
                                barThickness: 24
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const count = actualData[ctx.dataIndex];
                                        return `Count: ${count}`;
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
                                ticks: { display: false } 
                            },
                            y: { 
                                stacked: true, 
                                grid: { display: false }, 
                                ticks: { align: 'start', padding: 5, font: { size: 12 } } 
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
                            chart.data.datasets[0].data.forEach((value, index) => {
                                const meta = chart.getDatasetMeta(0);
                                const rect = meta.data[index];
                                if (!rect) return;
                                ctx.fillText(actualData[index], rect.x + 5, rect.y + 4);
                            });
                            ctx.restore();
                        }
                    }]
                });
    
            } else {
                const canvas = document.getElementById('AssetOwnershipChart');
                const ctx_wise = canvas.getContext('2d');
    
                if (window.clientDeployChart) {
                    window.clientDeployChart.destroy();
                    window.clientDeployChart = null;
                }
                ctx_wise.clearRect(0, 0, canvas.width, canvas.height);
                ctx_wise.font = 'bold 16px Arial';
                ctx_wise.fillStyle = '#999';
                ctx_wise.textAlign = 'center';
                ctx_wise.textBaseline = 'middle';
                ctx_wise.fillText('No Data Available', canvas.width / 2, canvas.height / 2);
            }
    
        } catch (error) {
            console.error("Async/Await Error:", error);
        } finally {
            // hideCDPreloader();
        }
    }

    window.onload = function () {
      SummaryCard_ShowCount();
      loadIndiaMap();  // draw map
      OEMChartFunction();
      VSummaryChartFunction();
      updateCityWiseTable();
      fetchDocumentTables();
      ClientwisebarChartFunction();
      ClientwiseDeploymentFunction();
      ClientwiseDeployedandReturnedFunction();
      loadInventorySummary();
      
    };


</script>
    <!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
<script>
    
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
        $('.custom-select2-field').select2({
            dropdownParent: $('#DashoffcanvasRightAMV') // Fix for offcanvas
        });
        bsOffcanvas.show();
    }
    function AMVDashRightSideFilerClose(){
        const offcanvasEl = document.getElementById('DashoffcanvasRightAMV');
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
    }

    
    function InventorySummaryOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#InventorySummaryFil');
        $('.custom-select2-field').select2({
            dropdownParent: $('#InventorySummaryFil') // Fix for offcanvas
        });
        bsOffcanvas.show();
    }
    function InventorySummaryHide(){
        const bsOffcanvas = new bootstrap.Offcanvas('#InventorySummaryFil');
        bsOffcanvas.hide();
    }
</script>

 <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    async function loadInventorySummary() {
        try {
            // Show loader
            $("#inventorySummaryTable tbody").html(`
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);
            const params = get_filter_params("InventoryDataTable"); 
            // Await AJAX response
            const response = await $.ajax({
                url: "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}",
                type: "GET",
                data: params
            });
    
            // Render table HTML
            $("#inventorySummaryTable tbody").html(response.html);
    
        } catch (error) {
            console.error("Inventory Summary Error:", error);
            $("#inventorySummaryTable tbody").html(
                '<tr><td colspan="10" class="text-danger">Error loading data</td></tr>'
            );
        }
    }

    function tablePreloaderOn(){
        $('#inventorySummaryTable tbody').html(`
            <tr>
                <td colspan="10" class="text-center py-4">
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
                                <td><small>${item.chassis_number}</small></td>
                                <td><small>${item.vehicle_type}</small></td>
                                <td><small>${item.reg_number}</small></td>
                                <td><small>${item.make}</small></td>
                                <td><small>${item.location}</small></td>
                                <td><small>${item.hub}</small></td>
                                <td><small>${item.telematic_no}</small></td>
                                <td><small>${item.location_status}</small></td>
                                <td><small>${item.client_name}</small></td>
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
                    rows = `<tr><td colspan="10" class="text-center">No records found</td></tr>`;
                }
    
                $('#inventorySummaryTable tbody').html(rows);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });
    function InSumFilter_Function(){
        $("#inventory_sum_search").val('');
        var status = $("#asset_fil_status").val();
        var vehicle_make = $("#vehicle_make").val();
        var customer_name = $("#customer_name").val();
        tablePreloaderOn();
        $.ajax({
            url: "{{ route('admin.asset_management.asset_master.inventory_summary.filter') }}",
            type: 'GET',
            data: { status: status, customer_name: customer_name, vehicle_make: vehicle_make },
            success: function (data) {
                let rows = '';
                
                if (data.length > 0) {
                    data.forEach(item => {
                        rows += `
                            <tr>
                                <td><small>${item.chassis_number}</small></td>
                                <td><small>${item.reg_number}</small></td>
                                <td><small>${item.make}</small></td>
                                <td><small>${item.vehicle_type}</small></td>
                                <td><small>${item.location}</small></td>
                                <td><small>${item.hub}</small></td>
                                <td><small>${item.telematic_no}</small></td>
                                <td><small>${item.location_status}</small></td>
                                <td><small>${item.client_name}</small></td>
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
                    rows = `<tr><td colspan="10" class="text-center">No records found</td></tr>`;
                }
    
                $('#inventorySummaryTable tbody').html(rows);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    function clearDataTbleFilter(){
        $("#inventory_sum_search").val('');
        $("#asset_fil_status").val('all');
        $("#vehicle_make").val('all');
        $("#customer_name").val('');
        $("#customer_results").html('');
        InventorySummaryHide();
        InSumFilter_Function();
        
    }

</script>
<script>

    function showCWDRPreloader() {
        if ($("#clientDeployedReturnedChart-preloader").length === 0) {
            let preloader = `
            <div id="clientDeployedReturnedChart-preloader"
                 class="d-flex align-items-center justify-content-center"
                 style="position:absolute; top:0; left:0; right:0; bottom:0;
                        background:rgba(255,255,255,0.7); z-index:10;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`;
    
            $("#chartWrapper").append(preloader);
        }
    }
    
    function hideCWDRPreloader() {
        setTimeout(() => {
            const preloader = document.getElementById("clientDeployedReturnedChart-preloader");
            if (preloader) preloader.remove();
        }, 150); // delay required
    }
    async function ClientwiseDeployedandReturnedFunction() {
        try {
            showCWDRPreloader();
            const params = get_filter_params("clientDeployedReturnedChart");
            const response = await $.ajax({
                url: "{{ route('admin.asset_management.asset_master.dashboard.get_overall_data') }}",
                type: "GET",
                data: params
            });
            hideCWDRPreloader();
            if (response.data && response.data.length > 0) {
                const year = response.filterYear;
                const month = response.filterMonth;
                const daysInMonth = new Date(year, month, 0).getDate();
                const dataMap = {};
                response.data.forEach(item => {
                    const d = new Date(item.date);
                    const itemMonth = d.getMonth() + 1;
                    const itemDay = d.getDate();
                    if (itemMonth == month) {
                        dataMap[itemDay] = {
                            deployed: item.deployed_count,
                            returned: item.returned_count,
                            fullDate: item.date
                        };
                    }
                });
                const labels = [];
                const deployedData = [];
                const returnedData = [];
                const fullDates = [];
                for (let day = 1; day <= daysInMonth; day++) {
                    const dayLabel = String(day).padStart(2, '0');
                    labels.push(dayLabel);
                    fullDates.push(`${year}-${String(month).padStart(2, '0')}-${dayLabel}`);
                    deployedData.push(dataMap[day] ? dataMap[day].deployed : 0);
                    returnedData.push(dataMap[day] ? dataMap[day].returned : 0);
                }
                const maxY = Math.max(...deployedData, ...returnedData) + 5;
                const ctx = document.getElementById('clientDeployedReturnedChart').getContext('2d');
                if (window.clientDRChart) {
                    await new Promise(res => setTimeout(res, 50));
                    window.clientDRChart.destroy();
                }
                window.clientDRChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Deployed Vehicle',
                                data: deployedData,
                                borderColor: 'rgba(0, 123, 255, 1)',
                                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                                fill: false,
                                tension: 0.3,
                                borderWidth: 2
                            },
                            {
                                label: 'Returned Vehicle',
                                data: returnedData,
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
                                max: maxY,
                                ticks: { stepSize: Math.ceil(maxY / 5) },
                                title: { display: true, text: 'Vehicle Count' }
                            },
                            x: {
                                title: { display: true, text: 'Day' }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: { usePointStyle: true }
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return fullDates[context[0].dataIndex];
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: `Client-wise Deployed vs Returned (${String(month).padStart(2, '0')}/${year})`
                            }
                        }
                    }
                });
            } else {
                const canvas = document.getElementById('clientDeployedReturnedChart');
                const cw_ctx = canvas.getContext('2d');
                if (window.clientDRChart) {
                    window.clientDRChart.destroy();
                    window.clientDRChart = null;
                }
                cw_ctx.clearRect(0, 0, canvas.width, canvas.height);
                cw_ctx.font = 'bold 16px Arial';
                cw_ctx.fillStyle = '#999';
                cw_ctx.textAlign = 'center';
                cw_ctx.textBaseline = 'middle';
                cw_ctx.fillText('No Data Available', canvas.width / 2, canvas.height / 2);
            }
    
        } catch (error) {
            console.error("Async/Await Error:", error);
            hideCWDRPreloader();
        }
    }
    
   async function updateCityWiseTable() {
        try {
    
            // 1. Show loading immediately
            document.getElementById("cityWiseTableContainer").innerHTML = `
                <div class="text-center p-3">
                    <p>Loading...</p>
                </div>
            `;
    
            const params = get_filter_params("CityWiseTable");
            params.chart_type = "CityWiseTable";
    
            const url = "{{ route('admin.asset_management.asset_master.dashboard.fetch_city_wise_data') }}?" 
                            + new URLSearchParams(params).toString();
    
            const response = await fetch(url);
            const html = await response.text();
    
            // 2. Replace with actual HTML
            document.getElementById("cityWiseTableContainer").innerHTML = html;
    
        } catch (err) {
            console.error("Error updating City Wise Table:", err);
    
            document.getElementById("cityWiseTableContainer").innerHTML = `
                <div class="text-center text-danger p-3">
                    <small>Error loading data</small>
                </div>
            `;
        }
    }




</script>
<script>
    function ExportDashboardDataTable() {
        var $btn = $(".DashboardExportBtn"); 
        $btn.html('<i class="bi bi-hourglass-split"></i> Downloading...').prop("disabled", true);
        var get_export_labels = [
                'chassis_number',
                'vehicle_category',
                'vehicle_type',
                'model',
                'make',
                'variant',
                'color',
                'motor_number',
                'vehicle_id',
                'tax_invoice_number',
                'tax_invoice_date',
                'tax_invoice_value',
                'city_code',
                'gd_hub_id_allowcated',
                'gd_hub_id_existing',
                'financing_type',
                'asset_ownership',
                'lease_start_date',
                'lease_end_date',
                'emi_lease_amount',
                'hypothecation',
                'hypothecation_to',
                'insurer_name',
                'insurance_type',
                'insurance_number',
                'insurance_start_date',
                'insurance_expiry_date',
                'registration_type',
                'temproary_reg_number',
                'temproary_reg_date',
                'temproary_reg_expiry_date',
                'permanent_reg_number',
                'permanent_reg_date',
                'reg_certificate_expiry_date',
                'fc_expiry_date',
                'servicing_dates',
                'road_tax_applicable',
                'road_tax_amount',
                'road_tax_renewal_frequency',
                'road_tax_next_renewal_date',
                'battery_type',
                'battery_serial_no',
                'battery_serial_number_replacement_1',
                'battery_serial_number_replacement_2',
                'battery_serial_number_replacement_3',
                'battery_serial_number_replacement_4',
                'battery_serial_number_replacement_5',
                'charger_variant_name',
                'charger_serial_no',
                'charger_serial_number_replacement_1',
                'charger_serial_number_replacement_2',
                'charger_serial_number_replacement_3',
                'charger_serial_number_replacement_4',
                'charger_serial_number_replacement_5',
                'telematics_variant_name',
                'telematics_oem',
                'telematics_serial_no',
                'telematics_imei_number',
                'telematics_serial_number_replacement_1',
                'telematics_serial_number_replacement_2',
                'telematics_serial_number_replacement_3',
                'telematics_serial_number_replacement_4',
                'telematics_serial_number_replacement_5',
                'client',
                'vehicle_delivery_date',
                'vehicle_status',
            ];
        $.ajax({
            url: "{{ route('admin.asset_management.asset_master.export.inventory_detail') }}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                get_export_labels: get_export_labels
            },
            xhrFields: {
                responseType: 'blob' 
            },
            success: function (data) {
                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Inventory_{{ date('d-m-Y') }}.xlsx";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                // Reset button
                $btn.html('<i class="bi bi-file-earmark-arrow-down"></i> Export').prop("disabled", false);
            },
            error: function () {
                toastr.error("Network connection failed. Please try again.");
                $btn.html('<i class="bi bi-file-earmark-arrow-down"></i> Export').prop("disabled", false);
            }
        });
    }
</script>
@endsection
</x-app-layout>