<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
        }
        .shadow-secondary {
            box-shadow: 0 0.5rem 1rem rgba(222, 223, 226, 0.5); 
        }
        
        .equal-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
        }
        
        .equal-card .card-body {
            flex-grow: 1;
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


    
     <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                       <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h4 fw-bold">HR Level 01 Dashboard</div>
                        </div>
                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="">
                               <div class="input-group border-gray">
                                    <button class="btn bg-white" type="button">
                                    <i class="fas fa-search"></i>
                                  </button>
                                  <input type="text" class="form-control border-0" id="HRL01_search" placeholder="Search here" aria-label="Search">
                                 
                                </div>
                            </div>
                             <div class="text-center gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="DashRightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
    

        <div class="row" id="HRL01_summaryCardBody">
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'total_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #dc3545;"><span style="color:#4b5563;"> Total
                                            Applications </span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_application_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows total no of submitted applications</p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'pending_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #12ae3a;"><span style="color:#4b5563;">Pending HR 01 </span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Down_Icon.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows new submissions awaiting HR 01 review</p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'sent_to_bgv'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #1661c7;"><span style="color:#4b5563;">Sent to BGV</span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows applications forwarded to BGV</p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'sent_to_hr_02'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #efd27b;"><span style="color:#4b5563;">Sent to HR 02 </span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows Applications forwarded for final approval</p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'on_hold'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #d165e1;"><span style="color:#4b5563;">On Hold</span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Down_Icon.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows applications on hold for document reupload</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'hr01_rejected'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #866518;"><span style="color:#4b5563;">Rejected</span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Down_Icon.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows rejected by HR 01</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'hr01_approved_employees'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #947bef;"><span style="color:#4b5563;">Approved - Employees (Live)</span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                            src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows Applications forwarded for final approval</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.hr_level_one.app_list',['type'=>'hr01_approved_riders'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #d79232;"><span style="color:#4b5563;">Approved - Riders (Probation)</span></h5>
                                    <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"></h4>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted">Shows moved to probation/training period</p>
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

            <!--<div class="col-md-8 col-12 mb-5">-->
            <!--  <div class="card p-3">-->
            <!--    <div class="d-flex justify-content-between align-items-center mb-3">-->
            <!--      <strong class="fs-18">Today Total No of Applications</strong>-->
            <!--      <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#dateModal">Select Date</button>-->
            <!--    </div>-->
            <!--    <div class="chart-container"><br>-->
            <!--      <canvas id="applicationsChart"></canvas><br>-->
            <!--    </div>-->
            <!--  </div>-->
            <!--</div>-->
            <!--<div class="col-md-4 col-6 mb-5">-->
            <!--   <div class="card p-3">-->
            <!--      <div style="display: flex; justify-content: center; align-items: center;">-->
            <!--        <strong class="fs-18">HR Status</strong>-->
            <!--      </div><br>-->
            <!--      <div class="bgv-dash-chart-container" style="display: flex; justify-content: center; align-items: center;">-->
            <!--        <canvas id="bgv-dash-bgvChart"></canvas>-->
            <!--        <div class="bgv-dash-chart-center-text">-->
            <!--          <div style="font-weight: bold; font-size: 18px;">100%</div>-->
            <!--          <div style="font-size: 14px; color: #666;">Application</div>-->
            <!--        </div>-->
            <!--      </div><br>-->
            <!--      <div class="bgv-dash-legend">-->
            <!--        <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #52c552;"></div>Approved</div>-->
            <!--        <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #ff2c2c;"></div>Rejected</div>-->
            <!--        <div class="bgv-dash-legend-item"><div class="bgv-dash-dot" style="background-color: #ffc327;"></div>Live</div>-->
            <!--      </div><br>-->
            <!--    </div>-->
            <!--</div>-->
            
        </div>
        
        
        <!-- Date Modal -->
        <!--<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">-->
        <!--  <div class="modal-dialog modal-dialog-centered">-->
        <!--    <div class="modal-content p-3">-->
        <!--      <h5>Select Date</h5>-->
        <!--      <input type="date" id="startDate" class="form-control mb-3" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" />-->
        <!--      <button class="btn btn-success w-100" onclick="updateChart()">Apply</button>-->
        <!--    </div>-->
        <!--  </div>-->
        <!--</div>-->
        
        
       <div class="offcanvas offcanvas-end" tabindex="-1" id="DashoffcanvasRightHR01" aria-labelledby="DashoffcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="DashoffcanvasRightHR01Label">HR Level 01 Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Role Type</h6></div>
               </div>
               <div class="card-body">
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1" checked>
                      <label class="form-check-label" for="roleType1">
                        All
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1">
                      <label class="form-check-label" for="roleType1">
                        Employee
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType2">
                      <label class="form-check-label" for="roleType2">
                       Rider
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType3">
                      <label class="form-check-label" for="roleType3">
                       Adhoc
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType4">
                      <label class="form-check-label" for="roleType4">
                       Helper
                      </label>
                    </div>
               </div>
           </div>
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2">
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3">
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4">
                      <label class="form-check-label" for="timeLine4">
                       This Year
                      </label>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
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
        document.getElementById('HRL01_search').addEventListener('keyup', function () {
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
           
        function DashRightSideFilerOpen(){
            const bsOffcanvas = new bootstrap.Offcanvas('#DashoffcanvasRightHR01');
            bsOffcanvas.show();
        }
    </script>
    

<script>
    // Doughnut Chart
    // var approve_percentage = parseFloat('{{$hr_approve_percentage}}');
    // var rejected_percentage = parseFloat('{{$hr_reject_percentage}}');
    // var live_percentage = parseFloat('{{$hr_live_percentage}}');

    // // Calculate remaining percentage
    // var remaining_percentage = 100 - (approve_percentage + rejected_percentage + live_percentage);

    // const ctx1 = document.getElementById('bgv-dash-bgvChart').getContext('2d');
    // new Chart(ctx1, {
    //     type: 'doughnut',
    //     data: {
    //         labels: ['Approved', 'Rejected', 'Live', 'Remaining'],
    //         datasets: [{
    //             data: [approve_percentage, rejected_percentage, live_percentage, remaining_percentage], 
    //             backgroundColor: ['#52c552', '#ff2c2c', '#ffc327', '#d3d3d3'], 
    //             borderWidth: 0,
    //             cutout: '55%',
    //             borderRadius: 10,
    //             spacing: 0
    //         }]
    //     },
    //     options: {
    //         responsive: true,
    //         plugins: {
    //             legend: { display: false },
    //             tooltip: {
    //                 callbacks: {
    //                     label: function(context) {
    //                         return context.label + ': ' + context.parsed + '%';
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // });
</script>

<script>
// Chart.register(window['chartjs-plugin-annotation']);
// const ctx = document.getElementById('applicationsChart').getContext('2d');
// let chart;

// // Generate 13 dates (centered around selected date)
// function generateDates(centerDate) {
//   const labels = [];
//   const date = new Date(centerDate);
//   date.setDate(date.getDate() - 6);
//   for (let i = 0; i < 13; i++) {
//     const dd = String(date.getDate()).padStart(2, '0');
//     const mm = String(date.getMonth() + 1).padStart(2, '0');
//     labels.push(`${dd}/${mm}`);
//     date.setDate(date.getDate() + 1);
//   }
//   return labels;
// }

// function generateSmoothData(todayCount) {
//   const data = [];
//   let start = 1;
//   for (let i = 0; i < 6; i++) {
//     data.push(start);
//     start += Math.floor(Math.random() * 2) + 1;
//     if (start > 10) start = 10; // cap at 10
//   }

//   data.push(todayCount);
//   start = 1;
//   for (let i = 0; i < 6; i++) {
//     data.push(start);
//     start += Math.floor(Math.random() * 2); 
//     if (start > 5) start = 5; // cap at 5
//   }

//   return data;
// }




// function drawChart(centerDate = new Date(), todayCount = null) {
//   const labels = generateDates(centerDate);
//   const highlightIndex = 6;

//   // Use today's count from hidden input if not provided
//   if (todayCount === null) {
//     todayCount = parseInt(document.getElementById('TodayApplicationChartCountData').value);
//   }

//   const values = generateSmoothData(todayCount);

//   const config = {
//     type: 'line',
//     data: {
//       labels: labels,
//       datasets: [{
//         label: 'Applications',
//         data: values,
//         borderColor: '#28a745',
//         backgroundColor: 'rgba(40, 167, 69, 0.1)',
//         pointRadius: 0,
//         pointHoverRadius: 0,
//         tension: 0.4,
//         fill: true,
//       }]
//     },
//     options: {
//       responsive: true,
//       animation: false,
//       plugins: {
//         legend: { display: false },
//         tooltip: {
//           enabled: true,
//           callbacks: {
//             label: function (ctx) {
//               return ctx.dataIndex === highlightIndex ? `${ctx.raw} Applications` : '';
//             }
//           },
//           backgroundColor: '#28a745',
//           titleColor: '#fff',
//           bodyColor: '#fff',
//           filter: (tooltipItem) => tooltipItem.dataIndex === highlightIndex
//         },
//         annotation: {
//           annotations: {
//             highlightLine: {
//               type: 'line',
//               xMin: highlightIndex,
//               xMax: highlightIndex,
//               borderColor: '#28a745',
//               borderWidth: 2,
//               borderDash: [5, 5],
//               label: {
//                 content: `${values[highlightIndex]} Applications`,
//                 enabled: true,
//                 position: 'start',
//                 backgroundColor: '#28a745',
//                 color: '#fff',
//                 font: { weight: 'bold' }
//               }
//             }
//           }
//         }
//       },
//       scales: {
//         y: {
//           beginAtZero: true,
//           min: 0,
//           max: 100,
//           ticks: {
//             stepSize: 25
//           }
//         }
//       }
//     }
//   };

//   if (chart) chart.destroy();
//   chart = new Chart(ctx, config);
// }

// function updateChart() {
//   const selectedDate = document.getElementById('startDate').value;
//   const city_id = document.getElementById('city_id_filter').value;
//   if (selectedDate) {
//     const date = new Date(selectedDate);

//     $.ajax({
//       url: '{{ route('get_today_application_count') }}',
//       type: "GET",
//       data: {
//         filter_date: selectedDate,
//         city_id:city_id
//       },
//       success: function (response) {
//         if (response.count !== undefined) {
//           drawChart(date, parseInt(response.count));
//         }
//       },
//       error: function (xhr) {
//         toastr.error("Please try again.");
//       },
//     });
//   }

//   const modal = bootstrap.Modal.getInstance(document.getElementById('dateModal'));
//   modal.hide();
// }

// // Initial draw
// drawChart();
</script>
    @endsection
</x-app-layout>