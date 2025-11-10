<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
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

    <!--<div class="container mt-5">-->
    <!--    <div class="card">-->
    <!--       <div class="card-header text-center h3 font-weight-medium p-5">BGV Vendor Dashboard Development Work Inprogress</div>-->
    <!--    </div>-->
    <!--</div>-->
    
     <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card-title h4 fw-bold">BGV Vendor</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Hi, welcome back !</a></li>
                                </ol>
                            </nav>
                        </div>

                        <div class="col-12">
                            <div class="row d-flex">
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="FromDate">From Date</label>
                                        <input type="date" name="from_date" id="FromDate" class="form-control" 
                                        value="{{ !empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
            
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="ToDate">To Date</label>
                                        <input type="date" name="to_date" id="ToDate" class="form-control"  value="{{ !empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <!--<div class="me-2">-->
                                    <!--    <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                                    <!--</div>-->
                                    <div>
                                        <label for="">City</label>
                                        <select class="form-select" id="city_id_filter" style="width:100%;padding: 10px 20px;" onchange="CitywiseFilter(this.value)">
                                          <option value="">Select City &nbsp;</option>
                                          @if(isset($cities))
                                            @foreach($cities as $val)
                                            <option value="{{$val->id}}" {{$city_id == $val->id ? 'selected' : ''}}>{{$val->city_name}}</option>
                                            @endforeach
                                          @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 p-3 rounded d-flex flex-column flex-sm-row align-items-center" style="background:#ffffff;">
                                    <button class="btn btn-dark me-2 px-4" onclick="DatewiseFiler()">Filter</button>
                                    <a href="{{route('admin.Green-Drive-Ev.bgvvendor.dashboard')}}" class="btn btn-dark px-4">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    

        <div class="row">
          <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'total_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Total Applications</p><br>
                                    <h4 class="mb-0">{{$total_application_count}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+100%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector1.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'complete_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Total BGV Completed</p><br>
                                    <h4 class="mb-0">{{$completed_application_count}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #FF8901; font-weight: 500;">+{{$completed_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector2.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 </a>   
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'rejected_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Total BGV Rejected</p><br>
                                    <h4 class="mb-0">{{$rejected_application_count}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #9747FF; font-weight: 500;">+{{$rejected_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector3.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'pending_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Total BGV Pending</p><br>
                                    <h4 class="mb-0">{{$pending_application_count}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #26C360; font-weight: 500;">+{{$pending_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector4.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'hold_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                 <div class="card border-0">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p>Total BGV Hold</p><br>
                                <h4 class="mb-0">{{$hold_application_count}}</h4>
                            </div>
                            <div class="text-end">
                                <span style="color: #018DFF; font-weight: 500;">+{{$hold_percentage}}%</span><br><br>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/Vector5.png') }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>


        <div class="row">
             
             <input type="hidden" id="TodayApplicationChartCountData" value="{{$todays_applications}}">
            <div class="col-md-8 col-12 mb-5">
                    <div class="card p-3">
                          <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong class="fs-18">Today Total No of Applications</strong>
                            <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#dateModal">Select Date</button>
                          </div>
                          <div class="chart-container"><br>
                            <canvas id="applicationsChart"></canvas><br>
                          </div>
                    </div>
            </div>
            
            <div class="col-md-4 col-6 mb-5">
               <div class="card p-3">
                  <div style="display: flex; justify-content: center; align-items: center;">
                    <strong class="fs-18">BGV Status</strong>
                    <span></span>
                  </div><br>
                  <div class="bgv-dash-chart-container" style="display: flex; justify-content: center; align-items: center;">
                    <canvas id="bgv-dash-bgvChart"></canvas>
                    <div class="bgv-dash-chart-center-text">
                      <div style="font-weight: bold; font-size: 18px;">100%</div>
                      <div style="font-size: 14px; color: #666;">Application</div>
                    </div>
                  </div><br>
                  <div class="bgv-dash-legend">
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #3B3BFF;"></div>Approved</div>
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #26D4A6;"></div>Rejected</div>
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #FF7AB3;"></div>Pending</div>
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #f1f4fa; border:1px solid #d3cfcf;"></div>Hold</div>
                  </div><br>
                </div>
            </div>
            
        </div>
        
         <!-- Date Modal -->
        <div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
              <h5>Select Date</h5>
              <input type="date" id="startDate" class="form-control mb-3" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" />
              <button class="btn btn-success w-100" onclick="updateChart()">Apply</button>
            </div>
          </div>
        </div>
        
        <div class="row">
            <div class="col-12 mb-5">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Total BGV Pending Applications</h5>
                        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#dateModalPendingChart">Select Date</button>
                    </div>
                    <input type="hidden" id="BGVPendingCount" value="{{$bgv_pending_count}}">
                    <input type="hidden" id="BGVAgeing_PendingCount" value="{{$bgv_ageing_pending_count}}">
                    <div class="chart-container">
                        <canvas id="PendingbgvChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

         <!-- Date Modal -->
        <div class="modal fade" id="dateModalPendingChart" tabindex="-1" aria-labelledby="dateModalPendingChartLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
              <h5>Select Date</h5>
              <input type="date" id="EndPendingChartDate" class="form-control mb-3" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" />
              <button class="btn btn-success w-100" onclick="updatePendingChart()">Apply</button>
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
    </script>
    

<script>
  // Doughnut Chart
  var completed_percentage = '{{$completed_percentage}}';
  var pending_percentage = '{{$pending_percentage}}';
  var rejected_percentage = '{{$rejected_percentage}}';
  var hold_percentage = '{{$hold_percentage}}';

  const ctx1 = document.getElementById('bgv-dash-bgvChart').getContext('2d');
  new Chart(ctx1, {
    type: 'doughnut',
    data: {
      labels: ['Approved', 'Rejected', 'Pending', 'Hold'],
      datasets: [{
        data: [completed_percentage, rejected_percentage, pending_percentage, hold_percentage],
        backgroundColor: ['#3B3BFF', '#26D4A6', '#FF7AB3', '#f1f4fa'],
        borderWidth: 0,
        cutout: '55%',
        borderRadius: 10,
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
Chart.register(window['chartjs-plugin-annotation']);
const ctx = document.getElementById('applicationsChart').getContext('2d');
let chart;

// Generate 13 dates (centered around selected date)
function generateDates(centerDate) {
  const labels = [];
  const date = new Date(centerDate);
  date.setDate(date.getDate() - 6);
  for (let i = 0; i < 13; i++) {
    const dd = String(date.getDate()).padStart(2, '0');
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    labels.push(`${dd}/${mm}`);
    date.setDate(date.getDate() + 1);
  }
  return labels;
}

function generateSmoothData(todayCount) {
  const data = [];
  let start = 1;
  for (let i = 0; i < 6; i++) {
    data.push(start);
    start += Math.floor(Math.random() * 2) + 1;
    if (start > 10) start = 10; // cap at 10
  }

  data.push(todayCount);
  start = 1;
  for (let i = 0; i < 6; i++) {
    data.push(start);
    start += Math.floor(Math.random() * 2); 
    if (start > 5) start = 5; // cap at 5
  }

  return data;
}




function drawChart(centerDate = new Date(), todayCount = null) {
  const labels = generateDates(centerDate);
  const highlightIndex = 6;

  if (todayCount === null) {
    todayCount = parseInt(document.getElementById('TodayApplicationChartCountData').value);
  }

  const values = generateSmoothData(todayCount);

  const config = {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Applications',
        data: values,
        borderColor: '#28a745',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        pointRadius: 0,
        pointHoverRadius: 0,
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      animation: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          enabled: true,
          callbacks: {
            label: function (ctx) {
              return ctx.dataIndex === highlightIndex ? `${ctx.raw} Applications` : '';
            }
          },
          backgroundColor: '#28a745',
          titleColor: '#fff',
          bodyColor: '#fff',
          filter: (tooltipItem) => tooltipItem.dataIndex === highlightIndex
        },
        annotation: {
          annotations: {
            highlightLine: {
              type: 'line',
              xMin: highlightIndex,
              xMax: highlightIndex,
              borderColor: '#28a745',
              borderWidth: 2,
              borderDash: [5, 5],
              label: {
                content: `${values[highlightIndex]} Applications`,
                enabled: true,
                position: 'start',
                backgroundColor: '#28a745',
                color: '#fff',
                font: { weight: 'bold' }
              }
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          min: 0,
          max: 100,
          ticks: {
            stepSize: 25
          }
        }
      }
    }
  };

  if (chart) chart.destroy();
  chart = new Chart(ctx, config);
}

function updateChart() {
  const selectedDate = document.getElementById('startDate').value;
  if (selectedDate) {
    const date = new Date(selectedDate);

    $.ajax({
      url: '{{ route('get_today_application_count') }}',
      type: "GET",
      data: {
        filter_date: selectedDate,
      },
      success: function (response) {
        if (response.count !== undefined) {
          drawChart(date, parseInt(response.count));
        }
      },
      error: function (xhr) {
        toastr.error("Please try again.");
      },
    });
  }

  const modal = bootstrap.Modal.getInstance(document.getElementById('dateModal'));
  modal.hide();
}

// Initial draw
drawChart();
</script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.4.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    const bgvPendingCount = parseInt(document.getElementById('BGVPendingCount').value);
    const bgvAgeingPendingCount = parseInt(document.getElementById('BGVAgeing_PendingCount').value);

    function generateDates(centerDate) {
        const labels = [];
        const date = new Date(centerDate);
        date.setDate(date.getDate() - 6);
        for (let i = 0; i < 13; i++) {
            const dd = String(date.getDate()).padStart(2, '0');
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            labels.push(`${dd}/${mm}`);
            date.setDate(date.getDate() + 1);
        }
        return labels;
    }

     const centerDate = new Date();
     const dateRange = generateDates(centerDate);
    
     const pendingData = [5, 10, 25, 50, 50, 60, bgvPendingCount, 7, 5, 3, 2, 1, 1];
     const ageingData = [2, 4, 8, 16, 32, 64, bgvAgeingPendingCount, 7, 5, 3, 2, 1, 1];
    
     const todayIndex = 6;

 const ctx_pending = document.getElementById('PendingbgvChart').getContext('2d');

const bgvChart = new Chart(ctx_pending, {
    type: 'line',
    data: {
        labels: dateRange,
        datasets: [
            {
                label: 'Pending',
                data: pendingData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                pointBackgroundColor: '#28a745',
                pointRadius: function(context) {
                    return context.dataIndex === todayIndex ? 4 : 0;
                },
                datalabels: {
                    display: function(context) {
                        return context.dataIndex === todayIndex;
                    },
                    align: 'top',
                    anchor: 'end',
                    backgroundColor: '#28a745',
                    borderRadius: 4,
                    color: 'white',
                    font: { weight: 'bold', size: 11 },
                    padding: 6,
                    formatter: function(value) {
                        return value + ' Applications';
                    }
                }
            },
           {
                label: 'Ageing',
                data: ageingData,
                borderColor: '#ff2c2c',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                pointBackgroundColor: '#ff2c2c',
                pointRadius: function(context) {
                    return context.dataIndex === todayIndex ? 4 : 0;
                },
                datalabels: {
                    display: function(context) {
                        return context.dataIndex === todayIndex;
                    },
                    align: 'bottom',
                    anchor: 'start',
                    backgroundColor: '#ff2c2c',
                    borderRadius: 4,
                    color: 'white',
                    font: { weight: 'bold', size: 11 },
                    padding: 6,
                    formatter: function(value) {
                        return value + ' Applications';
                    }
                }
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'BGV Pending Types',
                font: { size: 18 }
            },
            legend: {
                labels: { usePointStyle: true }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataIndex === todayIndex
                            ? `${context.dataset.label}: ${context.parsed.y} Applications`
                            : ''; // No tooltip for other points
                    }
                }
            },
            annotation: {
                annotations: {
                    line: {
                        type: 'line',
                        mode: 'vertical',
                        scaleID: 'x',
                        value: dateRange[todayIndex],
                        borderColor: 'rgba(0,0,0,0.3)',
                        borderWidth: 1,
                        borderDash: [5, 5]
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                type: 'linear',
                ticks: {
                    callback: function(value) {
                        const ticks = [0, 25, 50, 100, 200, 300, 400, 500];
                        return ticks.includes(value) ? value : '';
                    },
                    stepSize: 25,
                    min: 0,
                    max: 500
                },
                title: {
                    display: true,
                    text: 'Applications'
                }
            }
        }
    },
    plugins: [ChartDataLabels, Chart.registry.getPlugin('annotation')]
});

function updatePendingChart() {
  const selectedDate = document.getElementById('EndPendingChartDate').value;
  if (selectedDate) {
    const date = new Date(selectedDate);

    $.ajax({
      url: '{{ route('admin.Green-Drive-Ev.bgvvendor.get_today_pending_application_count') }}',
      type: "GET",
      data: {
        filter_date: selectedDate,
      },
      success: function (response) {
        // Update the chart data with the new response
        bgvChart.data.datasets[0].data[todayIndex] = response.bgv_pending_count;
        bgvChart.data.datasets[1].data[todayIndex] = response.bgv_ageing_pending_count;

        // Refresh the chart
        bgvChart.update();
      },
      error: function (xhr) {
        toastr.error("Please try again.");
      },
    });
  }

  const modal = bootstrap.Modal.getInstance(document.getElementById('dateModalPendingChart'));
  modal.hide();
}


</script>


    @endsection
</x-app-layout>