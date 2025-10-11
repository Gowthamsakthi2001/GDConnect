<x-app-layout>
    
@section('style_css')
<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">-->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>

    /* Dashboard Content */
    .dashboard-content {
        padding: 24px;
    }
    
    /* Metric Cards */
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
        }
        
    .card-purple { background: linear-gradient( #ABA1FC, #8C85C7);border-radius: 8px;color:#FFFFFF;
          padding: 10px; }
        .card-blue { background: linear-gradient( #00B0F5, #0093CD);border-radius: 8px; color:#FFFFFF;
          padding: 10px; }
     
    .heatmap {
      display: grid;
      grid-template-columns: auto repeat(7, 1fr); /* first col for time, 7 days */
      gap: 4px;
      font-size: 14px;
      width: 100%;
      height: 100%;
    }
    
    .heatmap .cell {
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 5px;
      color: #fff;
      font-size: 12px;
      text-align: center;
    }
        .level-1 { background: #b9fbc0; } /* 0-50 */
        .level-2 { background: #34d399; } /* 50-100 */
        .level-3 { background: #059669; } /* 100+ */
        
            .card-metric {
          border-radius: 8px;
          box-shadow: 0 2px 6px rgba(0,0,0,0.08);
          /*padding: 16px;*/
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
        .metric-growth {
          font-weight: 400;
          font-size: 14px;
          color: #16A34A;
        }
        .chart-container {
          max-height: 200px;
        }
        
        canvas{
            height:140px;
        }
        
    .metric-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 400;
    }
    
    .metric-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #d4efdf;
        color: #005d27;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 400;
    }
    
    .metric-up {
        background-color: #E6F4EA; /* light green */
        color: #005D27;           /* dark green text */
    }
    
    .metric-down {
        background-color: #FDECEA; /* light red */
        color: #D32F2F;           /* dark red text */
    }
    
    .metric-badge.metric-up svg {
        width: 15px;
        height: 15px;
    }
    
    .metric-badge.metric-down svg {
        width: 15px;
        height: 15px;
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
    
    .metric-date {
        font-size: 12px;
        color: rgba(0, 0, 0, 0.52);
        font-weight: 400;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .metric-date svg {
        width: 24px;
        height: 24px;
        opacity: 0.52;
    }
    
    /* Gradient Cards */
    .gradient-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
        height: 135px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: white;
    }
    
    .gradient-card.gradient-purple {
        background: var(--purple-gradient);
    }
    
    .gradient-card.gradient-blue {
        background: var(--blue-gradient);
    }
    
    .gradient-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 600;
    }
    
    .metric-badge.metric-up-white {
        background: transparent;
        border: 1px solid white;
        color: white;
    }
    
    .gradient-card-value {
        font-size: 32px;
        font-weight: 600;
        line-height: 1;
    }
    
    .gradient-card-date {
        font-size: 12px;
        font-weight: 500;
    }
    
    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 8px;
        /*padding: 12px 16px;*/
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
        /*height: 100%;*/
    }
    
    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }
    
    .chart-header h5 {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin: 0;
    }
    
    .chart-legend {
        display: flex;
        gap: 16px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #1a1a1a;
    }
    
    .legend-color {
        width: 14px;
        height: 8px;
        border-radius: 2px;
    }
    
    
    
    /* Statistics Cards */
    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
        height: 290px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .stat-header {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .stat-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
    }
    
    .stat-icon-blue {
        background: #aadcff;
        color: #1073B9;
    }
    
    .stat-icon-green {
        background: #deffe4;
        color: #10B981;
    }
    
    .stat-icon-red {
        background: #ffdede;
        color: #B91010;
    }
    
    .stat-icon-orange {
        background: #fff6de;
        color: #B99710;
    }
    
    .stat-title span {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
    }
    
    .recovery-title {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin-top: 8px;
    }
    
    .recovery-badges {
        display: flex;
        gap: 8px;
    }
    
    .recovery-badge {
        background: #f1f1f1;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #000;
    }
    
    .stat-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .stat-value {
        font-size: 32px;
        font-weight: 600;
        color: #1a1a1a;
    }
    
    .stat-growth {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        color: #16A34A;
    }
    
    .stat-growth svg {
        width: 10.5px;
        height: 14px;
    }
    
    /* Bar Chart */
    .bar-chart {
        display: flex;
        align-items: end;
        justify-content: space-between;
        height: 96px;
        gap: 2px;
    }
    
    .bar {
        width: 10px;
        background: #10B981;
        border-radius: 2px 2px 0 0;
        min-height: 10px;
    }
    
    .bar-chart.red .bar {
        background: #B91010;
    }
    
    /* Zones Section */
    .zones-section {
        background: white;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
    }
    
    .zones-section h5 {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
    }
    
    .zone-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.05);
        height: 88px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .zone-name {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 400;
    }
    
    .zone-value {
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    
    .zone-count {
        font-size: 16px;
        font-weight: 600;
        color: #1a1a1a;
    }
    
    .zone-label {
        font-size: 12px;
        color: rgba(26, 26, 26, 0.52);
    }
    
    @media (min-width: 992px) and (max-width: 1220px) {
       
    .metric-header {
        display:inline-block;
        font-size: 16px;
        color: #1a1a1a;
        
    } 
    
    .metric-badge {
        display: inline-block; /* shrink to fit text + SVG */
        padding: 0.25rem 0.25rem; /* optional: spacing inside badge */
        border-radius: 4px;      /* optional: rounded corners */
        font-size: -0.125rem;     /* optional: adjust text size */
        float: right;  /* aligns with text if inline with others */
    }
    }
    @media (max-width: 992px) {
      .heatmap {
        font-size: 12px;
        grid-template-columns: auto repeat(7, minmax(20px, 1fr));
         width: 100%;
        height: 100%;
      }
      .heatmap .cell {
        height: 25px;
        font-size: 11px;
      }
    }
    
    
    /* Responsive Design */
    @media (max-width: 768px) {
        
    .heatmap {
        grid-template-columns: auto repeat(7, minmax(18px, 1fr));
        gap: 2px;
        font-size: 11px;
         width: 100%;
      height: 100%;
      }
      .heatmap .cell {
        height: 20px;
        font-size: 10px;
      }
      .col-md-7,
      .col-md-5 {
        width: 100%; /* stack the left and right sections */
        padding-left: 0 !important;
      }
      .card {
        margin-bottom: 10px;
        margin-left:10px;
      }
      
      .card-row{
          margin-left:3px;
      }
      canvas{
            height:130px;
        }
        
        .span-icon{
            display:none;
        }
    }
    
    @media (max-width: 576px) {
        .heatmap {
        grid-template-columns: auto repeat(7, minmax(14px, 1fr));
        font-size: 10px;
         width: 100%;
      height: 100%;
      }
      .heatmap .cell {
        height: 16px;
        font-size: 9px;
      }
      canvas{
            height:125px;
        }
        
        .span-icon{
            display:none;
        }
    }
    
  </style>
@endsection

<div class="container-fluid p-0">
    <div class="col-12 d-flex align-items-center justify-content-between mt-3 card-header bg-white" >
    <!-- Left side: Dashboard Title -->
    <div>
        <h5 class="mb-0 card-title">B2B ADMIN Dashboard</h5>
    </div>

    <!-- Right side: Export & Filter Buttons -->
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="bg-white p-2 px-3 border-gray rounded text-center" onclick="RightSideFilerOpen()">
            <i class="bi bi-filter fs-17"></i> Filters
        </div>
    </div>
</div>

<div id="metrics-cards-container">
    @include('b2badmin::partials.metrics-cards', ['rfd_count' => $rfd_count,'deploy_count'=>$deploy_count,'return_count'=>$return_count,'client_count' => $client_count,'agent_count' => $agent_count, 'start_date_formatted' => $start_date_formatted, 'end_date_formatted' => $end_date_formatted])
</div>

<div class="row mb-2 align-items-stretch" >
        <!-- Clients Card -->
    <div class="col-md-7 d-flex flex-column mb-2">
        <!-- Row of Metric Cards -->
        <div class="row g-3 mb-3 card-row"> <!-- g-3 adds gap between columns -->
        <div class="col-md-6">
            <div class="card-purple ">
                <h6>Total No of Clients</h6>
                <h2 id="total-clients">{{ $client_count }}</h2>
                <p class="m-0" id="current-month-client"></p>
                <!--<p class="m-0" id="current-month-client">From {{ $start_date_formatted }} to {{ $end_date_formatted }}</p>-->
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-blue ">
                <h6>Total No of Agents</h6>
                <h2 id="total-agents">{{ $agent_count }}</h2>
                <p class="m-0" id="current-month-agent"></p>
                <!--<p class="m-0" id="current-month-agent">From {{ $start_date_formatted }} to {{ $end_date_formatted }}</p>-->
            </div>
        </div>
        </div>
    
        <!-- Chart Card -->
        <div class="card p-3 flex-grow-1">
            <h6>Client Wise Deployment</h6>
            <canvas id="deploymentChart" class="w-100 h-100"></canvas>
        </div>
    </div>
    
    <!-- Heatmap -->
    <div class="col-md-5 d-flex mb-2" style="padding-left:4px;">
      <div class="card p-3 w-100 h-100">
        
        <div class="d-flex justify-content-between mb-2">
        <h6 class="d-flex align-items-center m-0">Client Tickets</h6>
          <small class="d-flex align-items-center"><span class="level-1" style="display:inline-block;width:16px;height:16px;border-radius:50%;"></span ><span style="padding:5px">0-50</span></small>
          <small class="d-flex align-items-center"><span class="level-2" style="display:inline-block;width:16px;height:16px;border-radius:50%;"></span ><span style="padding:5px">50-100</span></small>
          <small class="d-flex align-items-center"><span class="level-3" style="display:inline-block;width:16px;height:16px;border-radius:50%;"></span ><span style="padding:5px">100+</span></small>
        </div>

        <div class="heatmap">
          <div></div>
          <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>

          <!-- Example rows -->
          <div>21:00-23:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>18:00-21:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>15:00-18:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>12:00-15:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>09:00-12:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>06:00-09:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>03:00-06:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <div>00:00-03:00</div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div><div class="cell level-1"></div>
          <!-- Add more rows as needed -->
          
          
        </div>
        
      </div>
    </div>
    
    
  </div>
  

    <div class="row g-3">
    <!-- Service Request -->
    <div class="col-md-6 col-sm-12">
        <div class="card-metric bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6>Service Request</h6>
                <div class="d-flex align-items-center gap-2">
                    <select name="service_status" id="service-status" class="form-control" style="width:100px;">
                    <option value="">Select</option>
                    <option value="unassigned">Unassigned</option>
                    <option value="inprogress">In Progress</option>
                    <option value="closed" selected>Closed</option>
                </select>
                <span class="text-primary span-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none" >
                            <rect width="27" height="27" rx="8" fill="#D8E4FE"/>
                            <path d="M13.9582 9.8335L11.6665 13.5002H15.3332L13.0415 17.1668" stroke="#2563EB" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.3335 11.6969V15.3028C6.95212 15.3028 8.65489 18.1471 7.32912 20.4053L10.5045 22.2082C11.1758 21.0648 12.3379 20.4055 13.5002 20.4053C14.6624 20.4055 15.8246 21.0648 16.4957 22.2082L19.6712 20.4053C18.3454 18.1471 20.0482 15.3028 22.6668 15.3028V11.6969C20.0482 11.6969 18.3439 8.85266 19.6698 6.59441L16.4944 4.7915C15.8234 5.93433 14.6619 6.62464 13.5002 6.62489C12.3385 6.62464 11.1769 5.93433 10.506 4.7915L7.33057 6.59441C8.65637 8.85266 6.95217 11.6969 4.3335 11.6969Z" stroke="#2563EB" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>    
                </span>
                 </div>
            </div>
            <h2 id="service-count">{{ $service_count['current'] }}</h2>
            <p class="metric-growth" id="service-compare">
                <!--@if($service_count['change_percent'] >= 0)-->
                <!--    <span class="text-success">↑ +{{ $service_count['change_percent'] }}% from past </span>-->
                <!--@else-->
                <!--    <span class="text-danger">↓ {{ $service_count['change_percent'] }}% from past </span>-->
                <!--@endif-->
            </p>
            <div class="chart-container">
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Return Request -->
    <div class="col-md-6 col-sm-12">
        <div class="card-metric bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6>Return Request</h6>
                <div class="d-flex align-items-center gap-2">
                <select name="return_status" id="return-status" class="form-control" style="width:100px;">
                    <option value="">Select</option>
                    <option value="opened">Opened</option>
                    <option value="closed" selected>Closed</option>
                </select>
                <span class="text-success span-icon"  >
                <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none" >
                            <rect width="27" height="27" rx="8" fill="#EEE9CA"/>
                            <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                            <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                            <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>    
                </span>
                </div>
            </div>
            <h2 id="return-count">{{ $return_count['current'] }}</h2>
            <p class="metric-growth" id="return-compare">
                <!--@if($return_count['change_percent'] >= 0)-->
                <!--    <span class="text-success">↑ +{{ $return_count['change_percent'] }}% from past </span>-->
                <!--@else-->
                <!--    <span class="text-danger">↓ {{ $return_count['change_percent'] }}% from past </span>-->
                <!--@endif-->
            </p>
            <div class="chart-container">
                <canvas id="returnChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Accident Request -->
    <div class="col-md-6 col-sm-12">
        <div class="card-metric bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6>Accident Request</h6>
                <div class="d-flex align-items-center gap-2">
                <select name="accident_status" id="accident-status" class="form-control" style="width:100px;">
                    <option value="">Select</option>
                    <option value="claim_initiated">Claim Initiated</option>
                    <option value="insurer_visit_confirmed">Insurer Visit Confirmed</option>
                    <option value="inspection_completed">Inspection Completed</option>
                    <option value="approval_pending">Approval Pending</option>
                    <option value="repair_started">Repair Started</option>
                    <option value="repair_completed">Repair Completed</option>
                    <option value="invoice_submitted">Invoice Submitted</option>
                    <option value="payment_approved">Payment Approved</option>
                    <option value="claim_closed" selected>Claim Closed</option>
                </select>
                <span class="text-danger span-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#FFC1BE"/>
                            <path d="M9.37516 20.8335C9.37516 21.8461 8.55435 22.6669 7.54183 22.6669C6.52931 22.6669 5.7085 21.8461 5.7085 20.8335M9.37516 20.8335C9.37516 19.8211 8.55435 19.0002 7.54183 19.0002C6.52931 19.0002 5.7085 19.8211 5.7085 20.8335M9.37516 20.8335H11.2085C11.7148 20.8335 12.1252 20.4232 12.1252 19.9169V17.1802C12.1252 16.8842 11.9822 16.6063 11.7413 16.4343L8.91683 14.4169M5.7085 20.8335H4.3335M8.91683 14.4169H4.3335M8.91683 14.4169L5.98222 10.2245C5.81067 9.97942 5.53032 9.83347 5.23117 9.8335L4.3335 9.83358" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.625 20.8335C17.625 21.8461 18.4458 22.6669 19.4583 22.6669C20.4709 22.6669 21.2917 21.8461 21.2917 20.8335M17.625 20.8335C17.625 19.8211 18.4458 19.0002 19.4583 19.0002C20.4709 19.0002 21.2917 19.8211 21.2917 20.8335M17.625 20.8335H15.7917C15.2854 20.8335 14.875 20.4232 14.875 19.9169V17.1802C14.875 16.8842 15.018 16.6063 15.2589 16.4343L18.0833 14.4169M21.2917 20.8335H22.6667M18.0833 14.4169L21.018 10.2245C21.1895 9.97942 21.4699 9.83347 21.769 9.8335L22.6667 9.83358M18.0833 14.4169H22.6667" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.2082 11.6668L8.9165 9.32514L10.7498 8.91677L9.45984 5.25629L12.5832 6.62511L13.9149 4.3335L14.8748 8.00011L18.0832 7.06822L16.2816 11.6669" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M13.9583 11.6668L13.5 9.8335" stroke="#DC2626" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                </span>
                </div>
            </div>
            <h2 id="accident-count">{{ $accident_count['current'] }}</h2>
            <p class="metric-growth" id="accident-compare">
                <!--@if($accident_count['change_percent'] >= 0)-->
                <!--    <span class="text-success">↑ +{{ $accident_count['change_percent'] }}% from past </span>-->
                <!--@else-->
                <!--    <span class="text-danger">↓ {{ $accident_count['change_percent'] }}% from past </span> -->
                <!--@endif-->
            </p>
            <div class="chart-container">
                <canvas id="accidentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recovery Request -->
    <div class="col-md-6 col-sm-12">
        <div class="card-metric bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 style="color:black">Recovery Request</h6>
                <div class="d-flex align-items-center gap-2">
                <select name="recovery_status" id="recovery-status" class="form-control" style="width:100px;">
                    <option value="">Select</option>
                    <option value="opened">Opened</option>
                    <option value="closed" selected>Closed</option>
                </select>
                <span class="text-warning span-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                            <rect width="27" height="27" rx="8" fill="#A1DBD0"/>
                            <path d="M8.68754 13.5C8.68754 13.7317 8.70404 13.9641 8.73636 14.1916L7.37511 14.3862C7.33305 14.0927 7.31214 13.7965 7.31254 13.5C7.31254 10.0879 10.0887 7.3125 13.5 7.3125C14.9046 7.3125 16.2803 7.7965 17.3741 8.67444L16.5127 9.74694C15.6605 9.05723 14.5963 8.68299 13.5 8.6875C10.8463 8.6875 8.68754 10.8463 8.68754 13.5ZM8.00004 16.25C8.00004 16.4323 8.07248 16.6072 8.20141 16.7361C8.33034 16.8651 8.50521 16.9375 8.68754 16.9375C8.86988 16.9375 9.04475 16.8651 9.17368 16.7361C9.30261 16.6072 9.37504 16.4323 9.37504 16.25C9.37504 16.0677 9.30261 15.8928 9.17368 15.7639C9.04475 15.6349 8.86988 15.5625 8.68754 15.5625C8.50521 15.5625 8.33034 15.6349 8.20141 15.7639C8.07248 15.8928 8.00004 16.0677 8.00004 16.25ZM13.5 5.25C18.0492 5.25 21.75 8.95081 21.75 13.5H23.125C23.125 8.1925 18.8075 3.875 13.5 3.875C12.3705 3.875 11.2636 4.06888 10.2104 4.45181L10.6806 5.74431C11.5844 5.41651 12.5386 5.24923 13.5 5.25ZM17.625 10.75C17.625 10.9323 17.6975 11.1072 17.8264 11.2361C17.9553 11.3651 18.1302 11.4375 18.3125 11.4375C18.4949 11.4375 18.6697 11.3651 18.7987 11.2361C18.9276 11.1072 19 10.9323 19 10.75C19 10.5677 18.9276 10.3928 18.7987 10.2639C18.6697 10.1349 18.4949 10.0625 18.3125 10.0625C18.1302 10.0625 17.9553 10.1349 17.8264 10.2639C17.6975 10.3928 17.625 10.5677 17.625 10.75ZM8.68754 6.625C8.86988 6.625 9.04475 6.55257 9.17368 6.42364C9.30261 6.2947 9.37504 6.11984 9.37504 5.9375C9.37504 5.75516 9.30261 5.5803 9.17368 5.45136C9.04475 5.32243 8.86988 5.25 8.68754 5.25C8.50521 5.25 8.33034 5.32243 8.20141 5.45136C8.07248 5.5803 8.00004 5.75516 8.00004 5.9375C8.00004 6.11984 8.07248 6.2947 8.20141 6.42364C8.33034 6.55257 8.50521 6.625 8.68754 6.625ZM5.25004 13.5C5.25004 11.2966 6.10804 9.22444 7.66661 7.66656L6.69379 6.69375C5.79699 7.58531 5.08606 8.646 4.6022 9.81434C4.11834 10.9827 3.87118 12.2354 3.87504 13.5C3.87504 18.8075 8.19254 23.125 13.5 23.125V21.75C8.95086 21.75 5.25004 18.0492 5.25004 13.5ZM22.0938 20.0312C22.0938 21.1684 21.1684 22.0938 20.0313 22.0938C18.8942 22.0938 17.9688 21.1684 17.9688 20.0312C17.9688 19.7136 18.0472 19.4166 18.175 19.1478L14.3835 15.3556C14.1154 15.4841 13.8177 15.5625 13.5 15.5625C12.3629 15.5625 11.4375 14.6371 11.4375 13.5C11.4375 12.3629 12.3629 11.4375 13.5 11.4375C14.6372 11.4375 15.5625 12.3629 15.5625 13.5C15.5625 13.8176 15.4849 14.1146 15.3563 14.3834L19.1479 18.1757C19.416 18.0471 19.7137 17.9688 20.0313 17.9688C21.1684 17.9688 22.0938 18.8941 22.0938 20.0312ZM13.5 14.1875C13.8789 14.1875 14.1875 13.8788 14.1875 13.5C14.1875 13.1212 13.8789 12.8125 13.5 12.8125C13.1212 12.8125 12.8125 13.1212 12.8125 13.5C12.8125 13.8788 13.1212 14.1875 13.5 14.1875ZM20.7188 20.0312C20.7187 19.8488 20.6461 19.6739 20.5171 19.545C20.388 19.416 20.213 19.3437 20.0306 19.3438C19.8482 19.3438 19.6733 19.4164 19.5443 19.5455C19.4154 19.6745 19.343 19.8495 19.3431 20.0319C19.3432 20.2144 19.4158 20.3893 19.5448 20.5182C19.6739 20.6471 19.8489 20.7195 20.0313 20.7194C20.2137 20.7193 20.3886 20.6468 20.5176 20.5177C20.6465 20.3887 20.7189 20.2137 20.7188 20.0312Z" fill="#14A388"/>
                            </svg>
                </span>
                </div>
            </div>
            <h2 id="recovery-count">{{ $recovery_count['current'] }}</h2>
            <p class="metric-growth" id="recovery-compare">
                <!--@if($recovery_count['change_percent'] >= 0)-->
                <!--    <span class="text-success">↑ +{{ $recovery_count['change_percent'] }}% from past </span>-->
                <!--@else-->
                <!--    <span class="text-danger">↓ {{ $recovery_count['change_percent'] }}% from past</span>-->
                <!--@endif-->
            </p>
            <div class="chart-container">
                <canvas id="recoveryChart"></canvas>
            </div>
        </div>
    </div>
</div>

    
 <!-- Zones Section -->
    
    <div id="cities-cards-container">
    @include('b2badmin::partials.cities-cards', ['zones' => $zones])
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
                        <select name="city_id" id="city_id_1" class="form-control custom-select2-field" onchange="getZones(this.value)">
                            <option value="">Select City</option>
                            @if(isset($cities))
                            @foreach($cities as $city)
                            <option value="{{$city->id}}" >{{$city->city_name}}</option>
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
                            <option value="">Select Zone</option>

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
    const deploymentCtx = document.getElementById('deploymentChart').getContext('2d');
    const deploymentChart = new Chart(deploymentCtx, {
        type: 'bar',
        data: {
            labels: @json($clientWiseDeploymentData->pluck('client_name')), // X-axis
            datasets: [{
                label: 'Deployments',
                data: @json($clientWiseDeploymentData->pluck('vehicle_count')), // Y-axis
                backgroundColor: '#3B82F6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: { size: 14, weight: 'bold' },
                        color: '#333'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Deployments vehicle counts'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 12 }
                    }
                }
            }
        }
    });
</script>

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

// Service Request Chart (Line)
const serviceChart = new Chart(document.getElementById('serviceChart'), {
  type: 'line',
  data: {
    labels: @json($labels),
    datasets: [{
      data: @json($serviceChartData),
      borderColor: '#072A43',
      backgroundColor: '#1073B9',
      fill: true,
      tension: 0.4,
      borderWidth: 1,      // thickness of the line
      pointRadius: 1,      // size of the points
      pointHoverRadius: 5 
    }]
  },
  options: chartOptions
});

// Return Request Chart (Bar)
const returnChart = new Chart(document.getElementById('returnChart'), {
  type: 'bar',
  data: {
    labels: @json($labels),
    datasets: [{
      data: @json($returnChartData),
      backgroundColor: '#10B981',
      borderWidth: 1,
      minBarLength: 2
    }]
  },
  options: chartOptions
});

// Accident Request Chart (Bar)
const accidentChart = new Chart(document.getElementById('accidentChart'), {
  type: 'bar',
  data: {
    labels: @json($labels),
    datasets: [{
      data: @json($accidentChartData),
      backgroundColor: '#B91010',
      borderWidth: 1,
      minBarLength: 2
    }]
  },
  options: chartOptions
});

// Recovery Request Chart (Line)
const recoveryChart = new Chart(document.getElementById('recoveryChart'), {
  type: 'line',
  data: {
    labels: @json($labels),
    datasets: [{
      data: @json($recoveryChartData),
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
        bsOffcanvas.show();
    }

    // Clear filters
$('#clearFilterBtn').on('click', function (e) {
    e.preventDefault();

    // Clear all filter inputs
    $('#quick_date_filter').val('');
    $('#FromDate').val('');
    $('#ToDate').val('');
    $('#city_id_1').val('').trigger('change');
    $('#zone_id_1').val('').trigger('change');
    let service_status = $('#service-status').val();
    let return_status = $('#return-status').val();
    let accident_status = $('#accident-status').val();
    let recovery_status = $('#recovery-status').val();

    // Prepare filters object
    let filters = {
        quick_date_filter: 'month',
        city_id: '',
        zone_id: '',
        service_status: service_status,
        return_status: return_status,
        accident_status: accident_status,
        recovery_status: recovery_status,
        from_date: '',
        to_date: ''
    };

    // Call the same filter function via AJAX
    $.ajax({
        url: "{{ route('b2b.admin.dashboard.filter') }}",
        type: "GET",
        data: filters,
        success: function (response) {
            // Update HTML
            $('#metrics-cards-container').html(response.metricsHtml);
            $('#cities-cards-container').html(response.citiesHtml);
            
            $('#total-clients').text(response.client_count);
            $('#total-agents').text(response.agent_count);
            $('#current-month-agent').text(response.current_month);
            $('#current-month-client').text(response.current_month);
            
            $('#service-count').text(response.service_count);
            $('#return-count').text(response.return_count);
            $('#accident-count').text(response.accident_count);
            $('#recovery-count').text(response.recovery_count);

            // Update charts
            if (deploymentChart) {
                deploymentChart.data.labels = response.deploymentData.labels;
                deploymentChart.data.datasets[0].data = response.deploymentData.values;
                deploymentChart.update();
            }

            serviceChart.data.labels = response.labels;
            serviceChart.data.datasets[0].data = response.charts.service;
            serviceChart.update();
            
            returnChart.data.labels = response.labels;
            returnChart.data.datasets[0].data = response.charts.return;
            returnChart.update();
            
            accidentChart.data.labels = response.labels;
            accidentChart.data.datasets[0].data = response.charts.accident;
            accidentChart.update();
            
            recoveryChart.data.labels = response.labels;
            recoveryChart.data.datasets[0].data = response.charts.recovery;
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
            service_status: $('#service-status').val(),
            return_status: $('#return-status').val(),
            accident_status: $('#accident-status').val(),
            recovery_status: $('#recovery-status').val(),
            from_date: from_date,
            to_date: to_date
        };
            
        
        
        // AJAX call to update dashboard cards
        $.ajax({
            url: "{{ route('b2b.admin.dashboard.filter') }}",
            type: "GET",
            data: filters,
            // beforeSend: function () {
            //     $('#metrics-cards-container').html('<p>Loading metrics...</p>');
            //     $('#cities-cards-container').html('<p>Loading cities...</p>');
            // },
            success: function (response) {
                console.log(response);
                $('#metrics-cards-container').html(response.metricsHtml);
                $('#cities-cards-container').html(response.citiesHtml);
                
                $('#total-clients').text(response.client_count);
                $('#total-agents').text(response.agent_count);
                // $('#current-month-agent').text(response.current_month);
                // $('#current-month-client').text(response.current_month);
                
                $('#service-count').text(response.service_count);
                $('#return-count').text(response.return_count);
                $('#accident-count').text(response.accident_count);
                $('#recovery-count').text(response.recovery_count);
                
                // $('#service-compare').text('');
                // $('#return-compare').text('');
                // $('#accident-compare').text('');
                // $('#recovery-compare').text('');
                
          
                    //     if (response.count.change_percent >= 0) {
                    //     $('#service-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
                    // } else {
                    //     $('#service-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
                    // }
                    
                   
                    //     if (response.count.change_percent >= 0) {
                    //     $('#recovery-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
                    // } else {
                    //     $('#recovery-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
                    // }
                    
                    
                    //     if (response.count.change_percent >= 0) {
                    //     $('#accident-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
                    // } else {
                    //     $('#accident-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
                    // }
                    
                 
                    //     if (response.count.change_percent >= 0) {
                    //     $('#return-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
                    // } else {
                    //     $('#return-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
                    // }
            
                if (deploymentChart) {
                    deploymentChart.data.labels = response.deploymentData.labels;
                    deploymentChart.data.datasets[0].data = response.deploymentData.values;
                    deploymentChart.update();
                }
                        serviceChart.data.labels = response.labels;
                        serviceChart.data.datasets[0].data = response.charts.service;
                        serviceChart.update();
                        
                        returnChart.data.labels = response.labels;
                        returnChart.data.datasets[0].data = response.charts.return;
                        returnChart.update();
                        
                        accidentChart.data.labels = response.labels;
                        accidentChart.data.datasets[0].data = response.charts.accident;
                        accidentChart.update();
                        
                        recoveryChart.data.labels = response.labels;
                        recoveryChart.data.datasets[0].data = response.charts.recovery;
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

<script>


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
            url: "{{ route('b2b.admin.dashboard.recoveryFilter') }}",
            type: "POST",
            data: data,
            success: function(response){
                // Update your chart or counts here
                console.log(response); // For debugging

                // Example: update recovery count
                $('#recovery-count').text(response.count.current);
            //     if (response.count.change_percent >= 0) {
            //     $('#recovery-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
            // } else {
            //     $('#recovery-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
            // }
                
                        recoveryChart.data.labels = response.labels;
                        recoveryChart.data.datasets[0].data = response.data;
                        recoveryChart.update();
            },
            error: function(xhr){
                console.error(xhr);
            }
        });
    }
    
    function fetchServiceData() {
        // Collect filter values only if selected
        var data = {};
        var quickDateFilter = $('#quick_date_filter').val();
        var cityId = $('#city_id_1').val();
        var zoneId = $('#zone_id_1').val();
        var fromDate = $('#FromDate').val();
        var toDate = $('#ToDate').val();
        var status = $('#service-status').val();

        if(quickDateFilter) data.quick_date_filter = quickDateFilter;
        if(cityId) data.city_id = cityId;
        if(zoneId) data.zone_id = zoneId;
        if(fromDate) data.from_date = fromDate;
        if(toDate) data.to_date = toDate;
        if(status) data.status = status;

        data._token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ route('b2b.admin.dashboard.serviceFilter') }}",
            type: "POST",
            data: data,
            success: function(response){
                // Update your chart or counts here
                console.log(response); // For debugging

                // Example: update recovery count
                $('#service-count').text(response.count.current);
            //     if (response.count.change_percent >= 0) {
            //     $('#service-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
            // } else {
            //     $('#service-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
            // }
                
                        serviceChart.data.labels = response.labels;
                        serviceChart.data.datasets[0].data = response.data;
                        serviceChart.update();
            },
            error: function(xhr){
                console.error(xhr);
            }
        });
    }
    
    function fetchAccidentData() {
        // Collect filter values only if selected
        var data = {};
        var quickDateFilter = $('#quick_date_filter').val();
        var cityId = $('#city_id_1').val();
        var zoneId = $('#zone_id_1').val();
        var fromDate = $('#FromDate').val();
        var toDate = $('#ToDate').val();
        var status = $('#accident-status').val();

        if(quickDateFilter) data.quick_date_filter = quickDateFilter;
        if(cityId) data.city_id = cityId;
        if(zoneId) data.zone_id = zoneId;
        if(fromDate) data.from_date = fromDate;
        if(toDate) data.to_date = toDate;
        if(status) data.status = status;

        data._token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ route('b2b.admin.dashboard.accidentFilter') }}",
            type: "POST",
            data: data,
            success: function(response){
                // Update your chart or counts here
                console.log(response); // For debugging

                // Example: update recovery count
                $('#accident-count').text(response.count.current);
            //     if (response.count.change_percent >= 0) {
            //     $('#accident-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
            // } else {
            //     $('#accident-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
            // }
                
                        accidentChart.data.labels = response.labels;
                        accidentChart.data.datasets[0].data = response.data;
                        accidentChart.update();
            },
            error: function(xhr){
                console.error(xhr);
            }
        });
    }
    
function fetchReturnData() {
    // Collect filter values only if selected
    var data = {};
    var quickDateFilter = $('#quick_date_filter').val();
    var cityId = $('#city_id_1').val();
    var zoneId = $('#zone_id_1').val();
    var fromDate = $('#FromDate').val();
    var toDate = $('#ToDate').val();
    var status = $('#return-status').val();

    if (quickDateFilter) data.quick_date_filter = quickDateFilter;
    if (cityId) data.city_id = cityId;
    if (zoneId) data.zone_id = zoneId;
    if (fromDate) data.from_date = fromDate;
    if (toDate) data.to_date = toDate;
    if (status) data.status = status;

    data._token = '{{ csrf_token() }}';

    $.ajax({
        url: "{{ route('b2b.admin.dashboard.returnFilter') }}",
        type: "POST",
        data: data,
        success: function(response) {
            // Update return count
            $('#return-count').text(response.count.current);

            // Update change % text
            // if (response.count.change_percent >= 0) {
            //     $('#return-compare').html(`<span class="text-success">↑ +${response.count.change_percent}% from past</span>`);
            // } else {
            //     $('#return-compare').html(`<span class="text-danger">↓ ${response.count.change_percent}% from past</span>`);
            // }

            // Update chart
            returnChart.data.labels = response.labels;
            returnChart.data.datasets[0].data = response.data;
            returnChart.update();
        },
        error: function(xhr) {
            console.error(xhr);
        }
    });
}
    
    // $('#quick_date_filter, #city_id_1, #zone_id_1, #FromDate, #ToDate').on('change', function(){
    //     fetchRecoveryData();
    //     fetchAccidentData();
    //     fetchReturnData();
    //     fetchServiceData();
    // });
    // Trigger AJAX when any filter select or input changes
    
    $(document).ready(function(){
    $('#recovery-status').on('change', function(){
        fetchRecoveryData();
    });
    
    $('#service-status').on('change', function(){
        fetchServiceData();
    });
    
    $('#accident-status').on('change', function(){
        fetchAccidentData();
    });
    
    $('#return-status').on('change', function(){
        fetchReturnData();
    });
    
        // $('#applyFilterBtn').on('click', function () {
        //     fetchRecoveryData();
        //     fetchServiceData();
        //     fetchAccidentData();
        //     fetchReturnData();
        // }
});
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

});
</script>
@endsection
</x-app-layout>