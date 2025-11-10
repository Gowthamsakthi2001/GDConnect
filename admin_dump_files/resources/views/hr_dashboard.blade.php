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
                            <div class="card-title h4 fw-bold">HR Status</div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Hi, Welcome back !</a></li>
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
                                        <input type="date" name="from_date" id="FromDate" max="{{date('Y-m-d')}}" class="form-control" 
                                        value="{{ !empty($from_date) ? \Carbon\Carbon::parse($from_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
            
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <div class="me-2">
                                        <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>
                                    </div>
                                    <div>
                                        <label for="ToDate">To Date</label>
                                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}"  value="{{ !empty($to_date) ? \Carbon\Carbon::parse($to_date)->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12 p-3 rounded d-flex align-items-center" style="background:#ffffff;">
                                    <!--<div class="me-2">-->
                                    <!--    <i class="bi bi-calendar p-2 fw-bold" style="background: #3BB54A12; border-radius: 25%; color:#68c674;"></i>-->
                                    <!--</div>-->
                                    <div>
                                        <label for="city_id_filter">City</label>
                                        <select class="form-select border-0" id="city_id_filter" style="width:100%;" onchange="CitywiseFilter(this.value)">
                                          <option value="">Select City</option>
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
                                    <a href="{{url('/')}}/admin/dashboard" class="btn btn-dark px-4">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    

        <div class="row">
          <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.hr_status.dashboard_filter_data', ['type' => 'approved_riders', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>HR Approved</p><br>
                                    <h4 class="mb-0">{{$total_hr_probation_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+{{$hr_probation_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/vector6.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.hr_status.dashboard_filter_data', ['type' => 'rejected_riders', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>HR Rejected</p><br>
                                    <h4 class="mb-0">{{$total_hr_reject_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+{{$hr_reject_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector7.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.hr_status.dashboard_filter_data', ['type' => 'probation_riders', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Probation Riders</p><br>
                                    <h4 class="mb-0">{{$total_hr_probation_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+{{$hr_probation_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector8.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.hr_status.dashboard_filter_data', ['type' => 'live_riders', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Live Riders</p><br>
                                    <h4 class="mb-0">{{$total_hr_live_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+{{$hr_live_percentage}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector9.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
             <div class="col-md-4 col-6 mb-5">
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'total_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                    <div class="card border-0">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p>Total Applications</p><br>
                                    <h4 class="mb-0">{{$total_application_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #C586A5; font-weight: 500;">+{{$total_application_percentage}}%</span><br><br>
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
                                    <h4 class="mb-0">{{$completed_application_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #FF8901; font-weight: 500;">+{{$completed_percentage ?? 0}}%</span><br><br>
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
                                    <h4 class="mb-0">{{$rejected_application_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #9747FF; font-weight: 500;">+{{$rejected_percentage ?? 0}}%</span><br><br>
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
                <a class="text-dark" href="{{ route('admin.Green-Drive-Ev.bgvvendor.dashboard_filter_data', ['type' => 'hold_application', 'from_date' => $from_date, 'to_date' => $to_date, 'city_id' => $city_id]) }}">
                 <div class="card border-0">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p>Total BGV Hold</p><br>
                                <h4 class="mb-0">{{$hold_application_count ?? 0}}</h4>
                            </div>
                            <div class="text-end">
                                <span style="color: #018DFF; font-weight: 500;">+{{$hold_percentage ?? 0}}%</span><br><br>
                                <div>
                                    <img src="{{ asset('public/admin-assets/icons/custom/Vector5.png') }}" class="img-fluid">
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
                                    <h4 class="mb-0">{{$pending_application_count ?? 0}}</h4>
                                </div>
                                <div class="text-end">
                                    <span style="color: #26C360; font-weight: 500;">+{{$pending_percentage ?? 0}}%</span><br><br>
                                    <div>
                                        <img src="{{ asset('public/admin-assets/icons/custom/Vector4.png') }}" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
        </div>


        <div class="row">
            <!--<div class="col-md-8 col-12 mb-5">-->
            <!--        <div class="card p-3">-->
            <!--              <div class="d-flex justify-content-between align-items-center mb-3">-->
            <!--                <h5 class="mb-0">Total No Applications</h5>-->
            <!--                <button class="btn btn-outline-dark btn-sm">Select Date</button>-->
            <!--              </div>-->
            <!--              <div class="chart-container">-->
            <!--                <canvas id="applicationsChart"></canvas>-->
            <!--              </div>-->
            <!--        </div>-->
            <!--</div>-->
            
            
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
                    <strong class="fs-18">HR Status</strong>
                  </div><br>
                  <div class="bgv-dash-chart-container" style="display: flex; justify-content: center; align-items: center;">
                    <canvas id="bgv-dash-bgvChart"></canvas>
                    <div class="bgv-dash-chart-center-text">
                      <div style="font-weight: bold; font-size: 18px;">100%</div>
                      <div style="font-size: 14px; color: #666;">Application</div>
                    </div>
                  </div><br>
                  <div class="bgv-dash-legend">
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #52c552;"></div>Approved</div>
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #ff2c2c;"></div>Rejected</div>
                    <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #ffc327;"></div>Live</div>
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
        
    @push('css')
    <link rel="stylesheet" href="{{ admin_asset('css/dashboard.min.css') }}">
    <style>
    </style>
    @endpush
    @push('js')

    @endpush
    @section('script_js')
   
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
    </script>
    

<script>
    // Doughnut Chart
    var approve_percentage = parseFloat('{{$hr_approve_percentage}}');
    var rejected_percentage = parseFloat('{{$hr_reject_percentage}}');
    var live_percentage = parseFloat('{{$hr_live_percentage}}');

    // Calculate remaining percentage
    var remaining_percentage = 100 - (approve_percentage + rejected_percentage + live_percentage);

    const ctx1 = document.getElementById('bgv-dash-bgvChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Rejected', 'Live', 'Remaining'],
            datasets: [{
                data: [approve_percentage, rejected_percentage, live_percentage, remaining_percentage], 
                backgroundColor: ['#52c552', '#ff2c2c', '#ffc327', '#d3d3d3'], 
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

  // Use today's count from hidden input if not provided
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
  const city_id = document.getElementById('city_id_filter').value;
  if (selectedDate) {
    const date = new Date(selectedDate);

    $.ajax({
      url: '{{ route('get_today_application_count') }}',
      type: "GET",
      data: {
        filter_date: selectedDate,
        city_id:city_id
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





    @endsection
</x-app-layout>