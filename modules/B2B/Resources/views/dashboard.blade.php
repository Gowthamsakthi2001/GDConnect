@extends('layouts.b2b')
    
@section('css')
<style>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
        body {
            font-family: 'Manrope', sans-serif;
        }
        
        .dashboard-card {
            background: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            height: 100%;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }
        .stat-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #1A1A1A;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            margin-right: 8px;
        }
        .stat-change {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .stat-comparison {
            font-size: 12px;
            color: #6c757d;
        }
        .quick-action-card {
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            padding: 5px 10px;
            margin-bottom: 8px;
            height: calc(20% - 8px);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .action-title {
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .action-desc {
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chart-container {
            position: relative;
            flex: 1;
            min-height: 200px;
        }
        @media (min-width: 992px) {
            .stat-header {
                font-size: 16px;
            }
            .stat-value {
                font-size: 1.4rem;
                font-weight: 600;
            }
            .action-title {
                font-size: 14px;
            }
            .action-desc {
                font-size: 12px;
            }
        }
        @media (max-width: 768px) {
            .quick-action-card {
                height: auto;
                min-height: 60px;
            }
            .stat-value {
                font-size: 1.4rem;
                font-weight: 600;
            }
        }
        
        .dashboard-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: pointer;
}

.offcanvas {
    overflow-y: auto !important;
}

.dashboard-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    transform: translateY(-4px);
}
.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
    </style>
@endsection

@section('content')
    
<div id="page-loader" style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255,255,255,0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;

">
    <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

    <div class="container-fluid">
            <div class="col-12 d-flex align-items-center justify-content-between mb-2 card-header" >
                <!-- Left side: Dashboard Title -->
                <div>
                    <h5 class="mb-0 card-title">B2B Dashboard</h5>
                </div>
            
                <!-- Right side: Export & Filter Buttons -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="bg-white p-2 px-3 border-gray rounded text-center" onclick="RightSideFilerOpen()">
                        <i class="bi bi-filter fs-17"></i> Filters
                    </div>
                </div>
            </div>
        <!-- Stats Row 1 -->
        <div class="mb-2">
            
            
      @php
            $accountabilityTypes = is_array($user->customer_relation->accountability_type_id) 
                ? $user->customer_relation->accountability_type_id 
                : json_decode($user->customer_relation->accountability_type_id, true) ?? [];
        @endphp
        <div class="row mb-2">
        
            <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="total_vehicles">{{$total_vehicles ?? 0}}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2 accountability-card-2 {{ !in_array(2, $accountabilityTypes) ? 'd-none' : '' }}" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total RFD</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="total_rfds">{{$total_rfd ?? 0}}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            
                <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Running Vehicle</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent1 = $assigned_vehicles['change_percent'];
                        $isPositive1 = $percent1 >= 0;
                        $symbol1 = $isPositive1 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="assigned_vehicle">{{ $assigned_vehicles['current'] ?? 0}}</span>
                            <!--<span class="stat-change" id="assigned_vehicle_change" style="color:#005D27; background:#D4EFDF;">-->
                            <!--   {{ $symbol1 }}{{ abs($percent1) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
            
            <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Under Maintenance</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent2 = $service_requests['change_percent'];
                        $isPositive2 = $percent2 >= 0;
                        $symbol2 = $isPositive2 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="service_request">{{ $service_requests['current'] ?? 0}}</span>
                            <!--<span class="stat-change" style="color:#F54900; background:#FFF7ED;">-->
                            <!--     {{ $symbol2 }}{{ abs($percent2) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Return Requested Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="return_requested">{{ $totalReturnRequests ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
             


            <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Returned Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent3 = $return_requests['change_percent'];
                        $isPositive3 = $percent3 >= 0;
                        $symbol3 = $isPositive3 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="returned">{{ $return_requests['current'] ?? 0}}</span>
                            <!--<span class="stat-change" id="return_request_change" style="color:#2563EB; background:rgba(37, 99, 235, 0.1);">-->
                            <!--     {{ $symbol3 }}{{ abs($percent3) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Recovery Requested Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent5 = $recovery_requests['change_percent'];
                        $isPositive5 = $percent5 >= 0;
                        $symbol5 = $isPositive5 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="recovery_request">{{ $recovery_requests['current'] ?? 0}}</span>
                            <!--<span class="stat-change" id="recovery_request_change" style="color:#005D27; background:#D4EFDF;">-->
                            <!--     {{ $symbol5 }}{{ abs($percent5) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Recovered Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent3 = $return_requests['change_percent'];
                        $isPositive3 = $percent3 >= 0;
                        $symbol3 = $isPositive3 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="recovered">{{ $totalRecovered ?? 0}}</span>
                            <!--<span class="stat-change" id="return_request_change" style="color:#2563EB; background:rgba(37, 99, 235, 0.1);">-->
                            <!--     {{ $symbol3 }}{{ abs($percent3) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
             <div class="col-lg-3 col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total Accident Vehicles</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    @php
                        $percent4 = $accident_report['change_percent'];
                        $isPositive4 = $percent4 >= 0;
                        $symbol4 = $isPositive4 ? '+' : '−'; // using minus sign
                    @endphp
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value" id="accident_report">{{ $accident_report['current'] ?? 0 }}</span>
                            <!--<span class="stat-change" id="service_request_change" style="color:#005D27; background:#D4EFDF;">-->
                            <!--    {{ $symbol4 }}{{ abs($percent4) }} %-->
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison">vs prior 30 days</div>-->
                    </div>
                </div>
            </div>
            
            <?php  $guard = Auth::guard('master')->check() ? 'master' : 'zone';?>
            @if($guard == 'master')
            <div class="col-md-3 col-sm-6 mb-2 mt-2" style="padding:0 8px">
                <div class="dashboard-card stat-card">
                    <div class="stat-header">
                        <div>Total No Zone</div>
                        <div>
                            <img src="{{ asset('b2b/img/error.svg') }}" alt="Error Icon" style="height:18px;">
                        </div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="stat-value">{{$totalZones ?? 0}}</span>
                            <!--<span class="stat-change" style="color:#005D27; background:#D4EFDF;">-->
                        
                            <!--</span>-->
                        </div>
                        <!--<div class="stat-comparison"></div>-->
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Charts Row -->
        <div class="row mb-2">
            <div class="col-lg-3 col-md-6 mb-2" style="padding:0 8px">
                <div class="dashboard-card">
                    <div class="mb-2">Rider Utilization</div>
                    <div class="chart-container">
                        <canvas style="height:90%;" id="riderUtilizationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 mb-2" style="padding:0 8px">
                <div class="dashboard-card">
                    <div class="mb-2">Service Request Insights</div>
                    <div class="chart-container">
                        <canvas style="height:100%" id="serviceRequestChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-12 mb-2" style="padding:0 8px">
                <div class="dashboard-card h-100">
                    <div class="mb-2">Quick Action</div>
                    <div class="h-100 d-flex flex-column">
                        
                        <div class="quick-action-card">
                            <a href="{{route('b2b.vehiclelist') }}" >
                            <div class="action-title" style="color:#FB2C36;">Report Accident</div>
                            </a>
                            <div class="action-desc">File an Accident Report</div>
                            
                        </div>
                        
                        <div class="quick-action-card">
                            <a href="{{route('b2b.vehiclelist') }}" >
                            <div class="action-title" style="color:#2B7FFF;">Service Request</div>
                            </a>
                            <div class="action-desc">Request Vehicle Maintenance</div>
                        </div>
                        <div class="quick-action-card">
                            <a href="{{route('b2b.vehiclelist') }}" >
                            <div class="action-title" style="color:#00C950;">Return Request</div>
                            </a>
                            <div class="action-desc">Submit Vehicle Return</div>
                        </div>
                        <div class="quick-action-card">
                            <a href="{{route('b2b.reports.index') }}" >
                            <div class="action-title" style="color:#AD46FF;">View Reports</div>
                            </a>
                            <div class="action-desc">Access Detailed Reports</div>
                        </div>
                        <div class="quick-action-card" style="margin-bottom: 0;">
                            <a href="{{route('b2b.vehicle_request.vehicle_request_list') }}" >
                            <div class="action-title" style="color:#FFA039;">New Vehicle Request</div>
                            </a>
                            <div class="action-desc">Add Vehicle Form</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Charts Row -->
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-3" style="padding:0 8px">
                <div class="dashboard-card">
                    <div class="mb-2">Accident Case Type</div>
                    <div class="chart-container">
                        <canvas id="accidentCaseChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 mb-3" style="padding:0 8px">
                <div class="dashboard-card">
                    <div class="mb-2">Return Type</div>
                    <div class="chart-container">
                        <canvas id="returnTypeChart"></canvas>
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
                            <option value="last15days">Last 15 days</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="mb-3 date-container">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">
                    </div>
                    
                    <div class="mb-3 date-container">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">
                    </div>
                </div>
            </div>
            
            <!--<div class="card mb-3">-->
            <!--   <div class="card-header p-2">-->
            <!--       <div><h6 class="custom-dark">Date Between</h6></div>-->
            <!--   </div>-->
            <!--   <div class="card-body">-->
 
            <!--        <div class="mb-3">-->
            <!--            <label class="form-label" for="FromDate">From Date</label>-->
            <!--            <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('from_date') }}">-->
            <!--        </div>-->
                    
            <!--        <div class="mb-3">-->
            <!--            <label class="form-label" for="ToDate">To Date</label>-->
            <!--            <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request('to_date') }}">-->
            <!--        </div>-->
  
            <!--   </div>-->
            <!--</div>-->
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Options</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                       <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="v_type">Accountability Type</label>
                        
                                <label class="mb-0">
                                    <input type="checkbox" id="accountability_type_select_all">
                                    Select All
                                </label>
                            </div>
                        <select name="accountability_type" id="accountability_type" class="form-control custom-select2-field" multiple>
                            @if(isset($accountability_types))
                            @foreach($accountability_types as $type)
                            <option value="{{$type->id}}" >{{$type->name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
                    
                    <!--<div class="mb-3">-->
                    <!--    <label class="form-label" for="FromDate">City</label>-->
                    <!--    <select name="city_id" id="city_id_1" class="form-control custom-select2-field" disabled>-->
                            <!--<option value="">Select City</option>-->
                    <!--        @if(isset($cities))-->
                    <!--        @foreach($cities as $city)-->
                    <!--        <option value="{{$city->id}}" >{{$city->city_name}}</option>-->
                    <!--        @endforeach-->
                    <!--        @endif-->

                    <!--    </select>-->
                    <!--</div>-->
                    @if($guard == 'master')
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="zone_id">Zone</label>
                        
                                <label class="mb-0">
                                    <input type="checkbox" id="zone_id_select_all">
                                    Select All
                                </label>
                            </div>
                        <select name="zone_id" id="zone_id_1" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select Zone</option>-->
                            @if(isset($zones))
                            @foreach($zones as $zone)
                            <option value="{{$zone->id}}" >{{$zone->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    @endif
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_type">Vehicle Type</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_type_select_all">
                                Select All
                            </label>
                        </div>
                    
                        <select name="v_type" id="v_type" class="form-control custom-select2-field" multiple>
                            @foreach($vehicle_types as $val)
                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    
                     <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_model">Vehicle Model</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_model_select_all">
                                Select All
                            </label>
                        </div>
                        <select name="v_model" id="v_model" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select</option>-->
                            @if(isset($vehicle_models))
                                @foreach($vehicle_models as $val)
                                <option value="{{$val->id}}" >{{$val->vehicle_model}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0" for="v_make">Vehicle Make</label>
                    
                            <label class="mb-0">
                                <input type="checkbox" id="v_make_select_all">
                                Select All
                            </label>
                        </div>
                        <select name="v_make" id="v_make" class="form-control custom-select2-field" multiple>
                            <!--<option value="">Select</option>-->
                            @if(isset($vehicle_makes))
                                @foreach($vehicle_makes as $val)
                                <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
               </div>
            </div>
            @if($guard == 'master')
            <!--<div class="card mb-3">-->
            <!--   <div class="card-header p-2">-->
            <!--       <div><h6 class="custom-dark">Select City</h6></div>-->
            <!--   </div>-->
            <!--   <div class="card-body">-->
 
            <!--        <div class="mb-3">-->
            <!--            <label class="form-label" for="FromDate">City</label>-->
            <!--            <select name="city_id" id="city_id_1" class="form-control custom-select2-field" disabled>-->
                            <!--<option value="">Select City</option>-->
            <!--                @if(isset($cities))-->
            <!--                @foreach($cities as $city)-->
            <!--                <option value="{{$city->id}}" >{{$city->city_name}}</option>-->
            <!--                @endforeach-->
            <!--                @endif-->

            <!--            </select>-->
            <!--        </div>-->
            <!--   </div>-->
            <!--</div>-->
            
            <!--<div class="card mb-3">-->
            <!--   <div class="card-header p-2">-->
            <!--       <div><h6 class="custom-dark">Select Zone</h6></div>-->
            <!--   </div>-->
            <!--   <div class="card-body">-->
 
            <!--        <div class="mb-3">-->
            <!--            <label class="form-label" for="zone_id">Zone</label>-->
            <!--            <select name="zone_id" id="zone_id_1" class="form-control custom-select2-field">-->
            <!--                <option value="">Select Zone</option>-->
            <!--                @if(isset($zones))-->
            <!--                @foreach($zones as $zone)-->
            <!--                <option value="{{$zone->id}}" >{{$zone->name}}</option>-->
            <!--                @endforeach-->
            <!--                @endif-->
            <!--            </select>-->
            <!--        </div>-->
            <!--   </div>-->
            <!--</div>-->
            
            @endif
           
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" id="clearFilterBtn">Clear All</button>
                <button class="btn btn-success w-50" id="applyFilterBtn">Apply</button>
            </div>
            
          </div>
        </div>
        
        @php
        
        $chartLabels = $returnData->keys();     // ["Contract End", "Performance Issue", ...]
        $chartValues = $returnData->values();   // [12, 5, 3, 9]
        $accidentLabels = $accidentData->keys();     // ["Collision", "Fall", "Fire", "Other"]
        $accidentValues = $accidentData->values();   // [8, 3, 1, 2]

        @endphp
@endsection
  
@section('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Rider Utilization (Donut)
        
        const riderUtilizationChart = new Chart(document.getElementById('riderUtilizationChart'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'In Active'],
                datasets: [{
                    data: [{{ $totalActiveRider }}, {{ $totalInactiveRider }}],
                    backgroundColor: ['#CAEDCE', '#EECACB'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: false,
                            boxWidth: 16,
                            boxHeight: 16
                        }
                    }
                }
            }
        });

        // Service Request Insights (Line)
        const serviceChartData = @json($serviceChartData);
        const labels = @json($labels);

    const serviceRequestChart = new Chart(document.getElementById('serviceRequestChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: serviceChartData
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: false,
                        boxWidth: 16,
                        boxHeight: 16
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 100 }
                }
            }
        }
    });

        // Accident Case Type (Bar)
        const accidentCaseChart = new Chart(document.getElementById('accidentCaseChart'), {
            type: 'bar',
            data: {
                labels: ['Collision', 'Fall', 'Fire', 'Other'],
                // labels: {!! json_encode($accidentLabels) !!},
                datasets: [{
                    label: 'Cases',
                    data: [{{ $collision }}, {{ $fall }}, {{ $fire }}, {{ $other }}],
                    // data: {!! json_encode($accidentValues) !!},
                    backgroundColor: '#D6CAED'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Return Type (Bar)
        const returnTypeChart = new Chart(document.getElementById('returnTypeChart'), {
            type: 'bar',
            data: {
                labels: ['Contract End', 'Performance Issue', 'Vehicle Issue', 'No Longer Needed'],
                // labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Returns',
                    data: [{{ $contractEnd }}, {{ $performanceIssue }}, {{ $vehicleIssue }}, {{ $noLongerNeeded }}],
                    // data: {!! json_encode($chartValues) !!},  // dynamic data
                    backgroundColor: '#EECACB'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: {
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    </script>
    <script>
    
    let types = @json($accountabilityTypes) ?? [];

    // Convert to numbers
    types = types.map(Number);
    
    // Decide final accountability_type
    let selectedAccountabilityType = types.includes(2) ? 2 : 1;

    function showLoader() {
        document.getElementById('page-loader').style.display = 'flex';
    }
    
    function hideLoader() {
        document.getElementById('page-loader').style.display = 'none';
    }

        function RightSideFilerOpen() {
            const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
            bsOffcanvas.show();
        }
        
//         $('#clearFilterBtn').on('click', function (e) {
//     e.preventDefault();

//     // Clear all filter inputs
//     $('#quick_date_filter').val('');
//     $('#FromDate').val('');
//     $('#ToDate').val('');
//     $('#city_id_1').val('').trigger('change');
//     $('#zone_id_1').val('').trigger('change');
//     let service_status = $('#service-status').val();
//     let return_status = $('#return-status').val();
//     let accident_status = $('#accident-status').val();
//     let recovery_status = $('#recovery-status').val();

//     // Prepare filters object
//     let filters = {
//         quick_date_filter: 'month',
//         city_id: '',
//         zone_id: '',
//         service_status: service_status,
//         return_status: return_status,
//         accident_status: accident_status,
//         recovery_status: recovery_status,
//         from_date: '',
//         to_date: ''
//     };

//     // Call the same filter function via AJAX
//     $.ajax({
//         url: "{{ route('b2b.admin.dashboard.filter') }}",
//         type: "GET",
//         data: filters,
//         success: function (response) {
//             // Update HTML
//             $('#metrics-cards-container').html(response.metricsHtml);
//             $('#cities-cards-container').html(response.citiesHtml);
            
//             $('#total-clients').text(response.client_count);
//             $('#total-agents').text(response.agent_count);
//             $('#current-month-agent').text(response.current_month);
//             $('#current-month-client').text(response.current_month);
            
//             $('#service-count').text(response.service_count);
//             $('#return-count').text(response.return_count);
//             $('#accident-count').text(response.accident_count);
//             $('#recovery-count').text(response.recovery_count);

//             // Update charts
//             if (deploymentChart) {
//                 deploymentChart.data.labels = response.deploymentData.labels;
//                 deploymentChart.data.datasets[0].data = response.deploymentData.values;
//                 deploymentChart.update();
//             }

//             serviceChart.data.labels = response.labels;
//             serviceChart.data.datasets[0].data = response.charts.service;
//             serviceChart.update();
            
//             returnChart.data.labels = response.labels;
//             returnChart.data.datasets[0].data = response.charts.return;
//             returnChart.update();
            
//             accidentChart.data.labels = response.labels;
//             accidentChart.data.datasets[0].data = response.charts.accident;
//             accidentChart.update();
            
//             recoveryChart.data.labels = response.labels;
//             recoveryChart.data.datasets[0].data = response.charts.recovery;
//             recoveryChart.update();

//             // Close offcanvas
//             const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
//             if (bsOffcanvas) bsOffcanvas.hide();
//         },
//         error: function () {
//             toastr.error('Failed to update dashboard');
//         }
//     });
// });


//     // Apply filters
//     $('#applyFilterBtn').on('click', function (e) {
//         const from_date = $('#FromDate').val();
//         const to_date   = $('#ToDate').val();

//         // Validation
//         if ((from_date && !to_date) || (!from_date && to_date)) {
//             e.preventDefault();
//             toastr.error("Both From Date and To Date are required");
//             return;
//         }

//         if (from_date && to_date && to_date < from_date) {
//             e.preventDefault();
//             toastr.error("To Date must be greater than or equal to From Date");
//             return;
//         }

//         // Gather filter values
//         let filters = {
//             quick_date_filter: $('#quick_date_filter').val(),
//             city_id: $('#city_id_1').val(),
//             zone_id: $('#zone_id_1').val(),
//             service_status: $('#service-status').val(),
//             return_status: $('#return-status').val(),
//             accident_status: $('#accident-status').val(),
//             recovery_status: $('#recovery-status').val(),
//             from_date: from_date,
//             to_date: to_date
//         };
            
        
        
//         // AJAX call to update dashboard cards
//         $.ajax({
//             url: "{{ route('b2b.admin.dashboard.filter') }}",
//             type: "GET",
//             data: filters,
//             // beforeSend: function () {
//             //     $('#metrics-cards-container').html('<p>Loading metrics...</p>');
//             //     $('#cities-cards-container').html('<p>Loading cities...</p>');
//             // },
//             success: function (response) {
//                 console.log(response);
//                 $('#metrics-cards-container').html(response.metricsHtml);
//                 $('#cities-cards-container').html(response.citiesHtml);
                
//                 $('#total-clients').text(response.client_count);
//                 $('#total-agents').text(response.agent_count);
//                 $('#current-month-agent').text(response.current_month);
//                 $('#current-month-client').text(response.current_month);
                
//                 $('#service-count').text(response.service_count);
//                 $('#return-count').text(response.return_count);
//                 $('#accident-count').text(response.accident_count);
//                 $('#recovery-count').text(response.recovery_count);
                
//                 // $('#service-compare').text('');
//                 // $('#return-compare').text('');
//                 // $('#accident-compare').text('');
//                 // $('#recovery-compare').text('');
                
//                 if (deploymentChart) {
//                     deploymentChart.data.labels = response.deploymentData.labels;
//                     deploymentChart.data.datasets[0].data = response.deploymentData.values;
//                     deploymentChart.update();
//                 }
//                         serviceChart.data.labels = response.labels;
//                         serviceChart.data.datasets[0].data = response.charts.service;
//                         serviceChart.update();
                        
//                         returnChart.data.labels = response.labels;
//                         returnChart.data.datasets[0].data = response.charts.return;
//                         returnChart.update();
                        
//                         accidentChart.data.labels = response.labels;
//                         accidentChart.data.datasets[0].data = response.charts.accident;
//                         accidentChart.update();
                        
//                         recoveryChart.data.labels = response.labels;
//                         recoveryChart.data.datasets[0].data = response.charts.recovery;
//                         recoveryChart.update();
        
//                 // Close offcanvas after success
//                 const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
//                 if (bsOffcanvas) {
//                     bsOffcanvas.hide();
//                 }
//             },
//             error: function () {
//                 toastr.error('Failed to update dashboard');
//             }
//         });

//     });
    </script>
    
    <script>
//     function getZones(CityID) {
//         let ZoneDropdown = $('#zone_id_1');
    
//         ZoneDropdown.empty().append('<option value="">Loading...</option>');
    
//         if (CityID) {
//             $.ajax({
//                 url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
//                 type: "GET",
//                 success: function (response) {
//                     ZoneDropdown.empty().append('<option value="">Select Zone</option>');
    
//                     if (response.data && response.data.length > 0) {
//                         $.each(response.data, function (key, zone) {
//                             ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
//                         });
//                     } else {
//                         ZoneDropdown.append('<option value="">No Zones available for this City</option>');
//                     }
//                 },
//                 error: function () {
//                     ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
//                 }
//             });
//         } else {
//             ZoneDropdown.empty().append('<option value="">Select a city first</option>');
//         }
//     }
 </script>

<script>
    // ✅ Function to update cards & charts
    function updateDashboard(data) {
        // Update stat cards
        $('#assigned_vehicle').text(data.assigned_vehicles.current);
        $('#service_request').text(data.service_requests.current);
        $('#returned').text(data.return_requests.current);
        $('#recovery_request').text(data.recovery_requests.current);
        $('#accident_report').text(data.accident_report.current);
        $('#return_requested').text(data.totalReturnRequests);
        $('#recovered').text(data.totalRecovered);
        
        $('#total_vehicles').text(data.total_vehicles);
        $('#total_rfds').text(data.total_rfd_vehicles);

        $('#assigned_vehicle_change').text(data.assigned_vehicles.change_percent.toFixed(2) + '%');
        $('#service_request_change').text(data.service_requests.change_percent.toFixed(2) + '%');
        $('#return_request_change').text(data.return_requests.change_percent.toFixed(2) + '%');
        $('#recovery_request_change').text(data.recovery_requests.change_percent.toFixed(2) + '%');
        $('#accident_request_change').text(data.accident_report.change_percent.toFixed(2) + '%');
    
        // Update comparison text (all vs past)
        // $('.stat-comparison').text('vs past');
        
        // Update Active/Inactive Rider chart
        riderUtilizationChart.data.datasets[0].data = [
            data.totalActiveRider, data.totalInactiveRider
        ];
        riderUtilizationChart.update();

        // Update Service Request Insights
        serviceRequestChart.data.labels = data.labels;
        serviceRequestChart.data.datasets = data.serviceChartData;
        serviceRequestChart.update();

        // Update Accident Chart
        accidentCaseChart.data.datasets[0].data = [
            data.collision, data.fall, data.fire, data.other
        ];
        accidentCaseChart.update();

        // Update Return Type Chart
        returnTypeChart.data.datasets[0].data = [
            data.contractEnd, data.performanceIssue,
            data.vehicleIssue, data.noLongerNeeded
        ];
        returnTypeChart.update();
        
        // // ===== Accident Case Chart =====
        // accidentCaseChart.data.labels = data.accidentLabels;     // Dynamic labels
        // accidentCaseChart.data.datasets[0].data = data.accidentValues; // Dynamic values
        // accidentCaseChart.update();
        
        // // ===== Return Type Chart =====
        // returnTypeChart.data.labels = data.returnLabels;     // Dynamic labels
        // returnTypeChart.data.datasets[0].data = data.returnValues; // Dynamic values
        // returnTypeChart.update();

    }

    // ✅ Clear filters
    $('#clearFilterBtn').on('click', function (e) {
        e.preventDefault();

        // Reset inputs
        $('#quick_date_filter').val('');
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#zone_id_1').val('').trigger('change');
        $('#accountability_type').val('').trigger('change');
        $('#service-status').val('');
        $('#return-status').val('');
        $('#accident-status').val('');
        $('#recovery-status').val('');
        $('#v_type').val(null).trigger('change');
        $('#v_model').val(null).trigger('change');
        $('#v_make').val(null).trigger('change');
        
        console.log(selectedAccountabilityType);
        //  Filters object (empty/defaults)
        let filters = {
            quick_date_filter: 'year',
            city_id: '',
            zone_id: '',
            service_status: '',
            return_status: '',
            accident_status: '',
            recovery_status: '',
            accountability_type: selectedAccountabilityType,
            from_date: '',
            to_date: '',
            vehicle_model:'',
            vehicle_type:'',
            vehicle_make: ''
        };

        // AJAX call
        $.ajax({
            url: "{{ route('b2b.dashboard_data') }}",
            type: "GET",
            data: filters,
            beforeSend: function () {
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
                showLoader();
            },
            success: function (response) {
                hideLoader(); 
                updateDashboard(response); // 🔥 use shared function
                 let defaultTypes = @json($accountabilityTypes).map(Number); // convert strings to numbers
                if(defaultTypes.includes(2)){
                    $('.accountability-card-2').removeClass('d-none');
                } else {
                    $('.accountability-card-2').addClass('d-none');
                }
                
                // Close filter offcanvas if open
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
                if (bsOffcanvas) bsOffcanvas.hide();
            },
            error: function () {
                hideLoader();
                toastr.error('Failed to update dashboard');
            }
        });
    });

    // ✅ Apply filters
    $('#applyFilterBtn').on('click', function (e) {
        e.preventDefault();

        let from_date = $('#FromDate').val();
        let to_date   = $('#ToDate').val();

        // Validation
        if ((from_date && !to_date) || (!from_date && to_date)) {
            toastr.error("Both From Date and To Date are required");
            return;
        }
        if (from_date && to_date && to_date < from_date) {
            toastr.error("To Date must be greater than or equal to From Date");
            return;
        }
        
        console.log(selectedAccountabilityType);
        // Filters object
        let filters = {
            quick_date_filter: $('#quick_date_filter').val(),
            city_id: $('#city_id_1').val(),
            zone_id: $('#zone_id_1').val(),
            service_status: $('#service-status').val(),
            return_status: $('#return-status').val(),
            accident_status: $('#accident-status').val(),
            recovery_status: $('#recovery-status').val(),
            accountability_type: $('#accountability_type').val() || selectedAccountabilityType,
            from_date: from_date,
            to_date: to_date,
            vehicle_model: $('#v_model').val(),
            vehicle_type: $('#v_type').val(),
            vehicle_make: $('#v_make').val(),
        };

        // AJAX call
        $.ajax({
            url: "{{ route('b2b.dashboard_data') }}",
            type: "GET",
            data: filters,
            beforeSend: function () {
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
                showLoader();
            },
            success: function (response) {
                hideLoader(); 
                updateDashboard(response); // 🔥 use shared function
                // Close filter offcanvas if open
                //  let selectedAccountability = $('#accountability_type').val(); // value from filter (string)
                //  let defaultTypes = @json($accountabilityTypes).map(Number); // convert to numbers
                    
                //     if(selectedAccountability == '2'){
                //         $('.accountability-card-2').removeClass('d-none');
                //     } else if(selectedAccountability === '' || selectedAccountability === null){
                //         // No filter selected, use default user types
                //         if(defaultTypes.includes(2)){
                //             $('.accountability-card-2').removeClass('d-none');
                //         } else {
                //             $('.accountability-card-2').addClass('d-none');
                //         }
                //     } else {
                //         // Selected other than 2
                //         $('.accountability-card-2').addClass('d-none');
                //     }
                
                let defaultTypes = (response.accountability_Type ?? []).map(Number); // convert strings to numbers
                // console.log(defaultTypes);
                if(defaultTypes.includes(2)){
                    $('.accountability-card-2').removeClass('d-none');
                } else {
                    $('.accountability-card-2').addClass('d-none');
                }
                
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
                if (bsOffcanvas) bsOffcanvas.hide();
            },
            error: function () {
                hideLoader();
                toastr.error('Failed to update dashboard');
            }
        });
    });
</script>

<script>
    $(document).ready(function () {

        function toggleDates() {
            if ($('#quick_date_filter').val() === 'custom') {
                $('.date-container').show();
            } else {
                $('.date-container').hide();
            }
        }

        // On change
        $('#quick_date_filter').on('change', toggleDates);

        // On page load (for old values)
        toggleDates();
    });
    
</script>

<script>
    function initSelectAll(selector, checkboxSelector) {

    // Select/Deselect all via checkbox
    $(checkboxSelector).on('change', function () {
        if (this.checked) {
            let values = [];
            $(selector + ' option').each(function () {
                values.push($(this).val());
            });
            $(selector).val(values).trigger('change');
        } else {
            $(selector).val(null).trigger('change');
        }
    });

    // Auto sync checkbox based on user actions
    $(selector).on('change', function () {
        let total = $(selector + ' option').length;
        let selected = $(selector).val() ? $(selector).val().length : 0;

        if (selected === total) {
            $(checkboxSelector).prop('checked', true);
        } else {
            $(checkboxSelector).prop('checked', false);
        }
    });
}

$(document).ready(function () {

    initSelectAll('#v_type', '#v_type_select_all');
    initSelectAll('#v_model', '#v_model_select_all');
    initSelectAll('#v_make', '#v_make_select_all');
    initSelectAll('#accountability_type', '#accountability_type_select_all');
    initSelectAll('#zone_id_1', '#zone_id_select_all');

});
</script>

@endsection
  
    