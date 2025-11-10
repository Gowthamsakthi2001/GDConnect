@extends('layouts.b2b')
    
@section('css')
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
    height: 135px;
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
    
.card-purple { background: linear-gradient(45deg, #ABA1FC, #8C85C7);border-radius: 8px;color:#FFFFFF;
      padding: 10px; }
    .card-blue { background: linear-gradient(45deg, #00B0F5, #0093CD);border-radius: 8px; color:#FFFFFF;
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

.metric-badge.metric-up svg {
    width: 15px;
    height: 15px;
}

.metric-value {
    font-size: 32px;
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
}
    
  </style>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Top Metrics Row -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-header">
                                <span>Total RFD Count</span>
                                <span class="metric-badge metric-up">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                        <path d="M12.5 8.125V5H9.375" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.5 5L9.375 8.125C8.82337 8.67663 8.54762 8.95237 8.20912 8.98287C8.15312 8.98794 8.09688 8.98794 8.04088 8.98287C7.70238 8.95237 7.42663 8.67663 6.875 8.125C6.32337 7.57337 6.04759 7.29763 5.70911 7.26713C5.65315 7.26206 5.59685 7.26206 5.54089 7.26713C5.20241 7.29763 4.9266 7.57337 4.375 8.125L2.5 10" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    +5%
                                </span>
                            </div>
                            <div class="metric-value">2500</div>
                            <div class="metric-date">From Aug 01, 2025 to Aug 31, 2025</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-header">
                                <span>Total Deployment Count</span>
                                <span class="metric-badge metric-up">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                        <path d="M12.5 8.125V5H9.375" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.5 5L9.375 8.125C8.82337 8.67663 8.54762 8.95237 8.20912 8.98287C8.15312 8.98794 8.09688 8.98794 8.04088 8.98287C7.70238 8.95237 7.42663 8.67663 6.875 8.125C6.32337 7.57337 6.04759 7.29763 5.70911 7.26713C5.65315 7.26206 5.59685 7.26206 5.54089 7.26713C5.20241 7.29763 4.9266 7.57337 4.375 8.125L2.5 10" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    +10%
                                </span>
                            </div>
                            <div class="metric-value">1000</div>
                            <div class="metric-date">From Aug 01, 2025 to Aug 31, 2025</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-header">
                                <span>Total Return Count</span>
                                <span class="metric-badge metric-up">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                        <path d="M12.5 8.125V5H9.375" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.5 5L9.375 8.125C8.82337 8.67663 8.54762 8.95237 8.20912 8.98287C8.15312 8.98794 8.09688 8.98794 8.04088 8.98287C7.70238 8.95237 7.42663 8.67663 6.875 8.125C6.32337 7.57337 6.04759 7.29763 5.70911 7.26713C5.65315 7.26206 5.59685 7.26206 5.54089 7.26713C5.20241 7.29763 4.9266 7.57337 4.375 8.125L2.5 10" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    +2%
                                </span>
                            </div>
                            <div class="metric-value">700</div>
                            <div class="metric-date">From Aug 01, 2025 to Aug 31, 2025</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-header">
                                <span>Total Client Tickets</span>
                                <span class="metric-badge metric-up">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                        <path d="M12.5 8.125V5H9.375" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.5 5L9.375 8.125C8.82337 8.67663 8.54762 8.95237 8.20912 8.98287C8.15312 8.98794 8.09688 8.98794 8.04088 8.98287C7.70238 8.95237 7.42663 8.67663 6.875 8.125C6.32337 7.57337 6.04759 7.29763 5.70911 7.26713C5.65315 7.26206 5.59685 7.26206 5.54089 7.26713C5.20241 7.29763 4.9266 7.57337 4.375 8.125L2.5 10" stroke="#005D27" stroke-width="0.9375" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    +0%
                                </span>
                            </div>
                            <div class="metric-value">10</div>
                            <div class="metric-date">From Aug 01, 2025 to Aug 31, 2025</div>
                        </div>
                    </div>
                </div>

  <div class="row mb-2 align-items-stretch" >
        <!-- Clients Card -->
    <div class="col-md-7 d-flex flex-column mb-2">
        <!-- Row of Metric Cards -->
        <div class="row g-3 mb-3"> <!-- g-3 adds gap between columns -->
        <div class="col-md-6">
            <div class="card-purple ">
                <h6>Total No of Clients</h6>
                <h2>10</h2>
                <p class="m-0">From Aug 01, 2025 to Aug 31, 2025</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-blue ">
                <h6>Total No of Agents</h6>
                <h2>3</h2>
                <p class="m-0">From Aug 01, 2025 to Aug 31, 2025</p>
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
          <div>21:00-23:00</div><div class="cell level-1"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>18:00-21:00</div><div class="cell level-3"></div><div class="cell level-1"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div>
          <div>15:00-18:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>12:00-15:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>09:00-12:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>06:00-09:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>03:00-06:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <div>00:00-03:00</div><div class="cell level-1"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-3"></div><div class="cell level-2"></div><div class="cell level-1"></div><div class="cell level-2"></div>
          <!-- Add more rows as needed -->
          
          
        </div>
        
      </div>
    </div>
    
    
  </div>

    <div class="row g-3">
    <!-- Service Request -->
    <div class="col-md-6" >
      <div class="card-metric bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Service Request</h6>
          <span class="text-primary"><i class="bi bi-gear"></i></span>
        </div>
        <h2>700</h2>
        <p class="metric-growth">↑ +5.7% from last month</p>
        <div class="chart-container">
          <canvas id="serviceChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Return Request -->
    <div class="col-md-6">
      <div class="card-metric bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Return Request</h6>
          <span class="text-success"><i class="bi bi-arrow-repeat"></i></span>
        </div>
        <h2>300</h2>
        <p class="metric-growth">↑ +5.7% from last month</p>
        <div class="chart-container">
          <canvas id="returnChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Accident Request -->
    <div class="col-md-6">
      <div class="card-metric bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h6>Accident Request</h6>
          <span class="text-danger"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <h2>200</h2>
        <p class="metric-growth">↑ +5.7% from last month</p>
        <div class="chart-container">
          <canvas id="accidentChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Recovery Request -->
    <div class="col-md-6">
      <div class="card-metric bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <h6 style="color:black">Recovery Request</h6>
          <span class="text-warning"><i class="bi bi-truck"></i></span>
        </div>
        <h2>100</h2>
        <p class="metric-growth">↑ +5.7% from last month</p>
        <div class="chart-container">
          <canvas id="recoveryChart"></canvas>
        </div>
      </div>
    </div>
  </div>
    
 <!-- Zones Section -->
                <div class="zones-section mt-3">
                    <h5 class="mb-3">Zones</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="zone-card">
                                <div class="zone-name">Chennai</div>
                                <div class="zone-value">
                                    <span class="zone-count">1000</span>
                                    <span class="zone-label">Vehicle</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="zone-card">
                                <div class="zone-name">Bangalore</div>
                                <div class="zone-value">
                                    <span class="zone-count">1500</span>
                                    <span class="zone-label">Vehicle</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="zone-card">
                                <div class="zone-name">Mumbai</div>
                                <div class="zone-value">
                                    <span class="zone-count">500</span>
                                    <span class="zone-label">Vehicle</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="zone-card">
                                <div class="zone-name">Delhi</div>
                                <div class="zone-value">
                                    <span class="zone-count">1500</span>
                                    <span class="zone-label">Vehicle</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    


</div>

@endsection

@section('js')
<script>
  const ctx = document.getElementById('deploymentChart').getContext('2d');
  const deploymentChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: Array.from({length: 31}, (_, i) => i + 1),
      datasets: [
        {
          label: 'Deployed Vehicle',
          data: [1000,800,600,400,300,500,800,700,600,400,500,700,600,500,400,300,200,300,500,700,600,400,500,300,200,100,80,60,40,20,10],
          borderColor: 'green',
          fill: false
        },
        {
          label: 'Returned Vehicle',
          data: [50,80,120,150,200,300,400,600,500,400,450,480,490,470,500,600,700,800,750,700,650,700,800,850,900,920,950,970,990,1000,1100],
          borderColor: 'blue',
          fill: false
        },
        {
          label: 'Recovered Vehicle',
          data: [10,20,30,40,60,50,80,100,70,50,60,80,40,20,30,50,70,90,100,150,200,180,170,160,150,180,200,220,240,260,280],
          borderColor: 'orange',
          fill: false
        },
        {
          label: 'Accident',
          data: [5,10,20,30,50,100,150,200,180,160,140,120,100,90,120,150,180,200,220,240,260,280,300,320,340,360,380,400,420,440,460],
          borderColor: 'red',
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      },
      scales: {
        y: {
          type: 'logarithmic',
          min: 1,
          ticks: {
            callback: (val) => val
          }
        }
      }
    }
  });
  
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: { x: { display: false }, y: { display: false } }
  };

  // Service Request Chart (Line)
  new Chart(document.getElementById('serviceChart'), {
    type: 'line',
    data: {
      labels: Array.from({length: 20}, (_, i) => i),
      datasets: [{
        data: [5,6,7,8,6,9,12,11,10,9,12,14,12,13,11,9,10,12,11,10],
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13,110,253,0.3)',
        fill: true,
        tension: 0.4
      }]
    },
    options: chartOptions
  });

  // Return Request Chart (Bar)
  new Chart(document.getElementById('returnChart'), {
    type: 'bar',
    data: {
      labels: Array.from({length: 15}, (_, i) => i),
      datasets: [{
        data: [4,6,5,7,6,5,8,7,9,6,5,8,7,6,5],
        backgroundColor: '#20c997'
      }]
    },
    options: chartOptions
  });

  // Accident Request Chart (Bar)
  new Chart(document.getElementById('accidentChart'), {
    type: 'bar',
    data: {
      labels: Array.from({length: 15}, (_, i) => i),
      datasets: [{
        data: [3,5,4,6,5,7,6,5,7,6,5,8,6,7,5],
        backgroundColor: '#dc3545'
      }]
    },
    options: chartOptions
  });

  // Recovery Request Chart (Line)
  new Chart(document.getElementById('recoveryChart'), {
    type: 'line',
    data: {
      labels: Array.from({length: 20}, (_, i) => i),
      datasets: [{
        data: [2,3,4,3,5,6,5,7,6,5,8,7,6,7,5,6,7,6,5,4],
        borderColor: '#ffc107',
        backgroundColor: 'rgba(255,193,7,0.3)',
        fill: true,
        tension: 0.4
      }]
    },
    options: chartOptions
  });
</script>
@endsection