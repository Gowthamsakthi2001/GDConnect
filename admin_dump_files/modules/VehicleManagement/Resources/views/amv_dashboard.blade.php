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
        
        .px-6{
            padding-left: 3.3rem !important;
            padding-right: 3.3rem !important;
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
                            <div class="card-title h4 fw-bold">Vehicle Management</div>
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
        <div class="col-md-4 col-6 mb-4 summary-card">
            <a class="text-dark" href="#">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between align-items-start my-3"> 
                                <h5 class="ps-2 mb-0" style="border-left: 4px solid #1661c7;"><span style="color:#4b5563;">Total QC Inspections</span></h5>
                                <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                            </div>
                            <div class="my-4">
                                <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} </span> <img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"></h4>
                            </div>
        
                            <div class="mb-3">
                                <p class="text-muted">Shows all applications received for BGV</p>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </a>
        </div>

        <div class="col-md-4 col-6 mb-4 summary-card">
            <a class="text-dark" href="#">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div>
                            <div class="d-flex justify-content-between align-items-start my-3">
                                <h5 class="ps-2 mb-0" style="border-left: 4px solid #12ae3a;"><span style="color:#4b5563;">QC Pass Rate </span></h5>
                                <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                            </div>
                            <div class="my-4">
                                <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} %</span> <img
                                        src="{{ asset('public/admin-assets/icons/custom/Trending_down.png') }}"
                                        class="img-fluid"></h4>
                            </div>
        
                            <div class="mb-3">
                                <p class="text-muted">Shows Applications not yet reviewed</p>
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
                                
                                <h5 class="ps-2 mb-0" style="border-left: 4px solid #dc3545;"><span style="color:#4b5563;"> QC Fail Rate </span></h5>
                                <img src="{{ asset('public/admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                            </div>
                            <div class="my-4">
                                <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_hr_probation_count ?? 0}} %</span> <img
                                        src="{{ asset('public/admin-assets/icons/custom/Down_Icon.png') }}"
                                        class="img-fluid"></h4>
                            </div>
        
                            <div class="mb-3">
                                <p class="text-muted">Shows Applications not yet reviewed</p>
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
    
    <div class="card">
        <div class="card-header border-0 pb-0 mb-0">
            <h5 class="fw-bold">Recent Activities</h5>
             <p class="text-start text-muted mb-0">Latest actions across all modules</p>
        </div>
        <div class="card-body">
            <div class="row p-3 rounded">
                <div class="col-12 border-gray p-3 d-flex justify-content-between align-items-center" style="background:#eaeaea;">
                    
                    <div>
                        <p class="text-start mb-1" style="color:#00000080;">
                            Qc inspection completed for chassis CH100001
                        </p>

                        <div class="d-flex align-items-center">
                            <small class="fw-normal me-2">By Technician001</small>
                            <i class="bi bi-circle-fill text-muted" style="font-size: 6px;"></i>
                            <small class="fw-normal ms-2">2 hrs ago</small>
                        </div>
                    </div>

                    <div>
                        <button class="btn btn-success px-5">Pass</button>
                    </div>
                </div>
            </div>
            <div class="row p-3 rounded">
                <div class="col-12 border-gray p-3 d-flex justify-content-between align-items-center" style="background:#eaeaea;">
                    
                    <div>
                        <p class="text-start mb-1" style="color:#00000080;">Qc inspection failed for chassis CH100002</p>
                        
                         <div class="d-flex align-items-center">
                            <small class="fw-normal me-2">By Technician001</small>
                            <i class="bi bi-circle-fill text-muted" style="font-size: 6px;"></i>
                            <small class="fw-normal ms-2">2 hrs ago</small>
                        </div>
                    </div>
                    
                    <div>
                        <button class="btn btn-danger px-6">Fail</button>
                    </div>
                    
                </div>
            </div>
                        <div class="row p-3 rounded">
                <div class="col-12 border-gray p-3 d-flex justify-content-between align-items-center" style="background:#eaeaea;">
                    
                    <div>
                        <p class="text-start mb-1" style="color:#00000080;">
                            Qc inspection completed for chassis CH100003
                        </p>

                        <div class="d-flex align-items-center">
                            <small class="fw-normal me-2">By Technician001</small>
                            <i class="bi bi-circle-fill text-muted" style="font-size: 6px;"></i>
                            <small class="fw-normal ms-2">2 hrs ago</small>
                        </div>
                    </div>

                    <div>
                        <button class="btn btn-success px-5">Pass</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


   <div class="offcanvas offcanvas-end" tabindex="-1" id="DashoffcanvasRightAMV" aria-labelledby="DashoffcanvasRightAMVLabel">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="DashoffcanvasRightAMVLabel">Vehicle Management Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
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
    </script>
    


</script>
    @endsection
</x-app-layout>