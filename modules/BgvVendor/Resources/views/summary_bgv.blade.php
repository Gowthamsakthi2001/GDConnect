<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
        }
        .shadow-secondary {
            /* box-shadow: 0 0.5rem 1rem rgba(222, 223, 226, 0.5);  */
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
                            <div class="card-title h4 fw-bold">BGV</div>
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
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=>'total_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #0D66D0;"><span style="color:#4b5563;"> Total
                                            Applications </span></h5>
                                    <img src="{{ asset('admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$total_count ?? 0}} </span> <img
                                            src="{{ asset('admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows all applications received for BGV </p>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=>'pending_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #F2D06C;"><span style="color:#4b5563;">Pending  </span></h5>
                                    <img src="{{ asset('admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$pending_count ?? 0}} </span> <img
                                            src="{{ asset('admin-assets/icons/custom/Down_Icon.png') }}"
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
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=>'hold_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #d165e1;"><span style="color:#4b5563;">On Hold</span></h5>
                                    <img src="{{ asset('admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$hold_count ?? 0}} </span> <img
                                            src="{{ asset('admin-assets/icons/custom/Down_Icon.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows Applications on hold for document reupload</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=>'complete_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid  #12ae3a;"><span style="color:#4b5563;">Approved </span></h5>
                                    <img src="{{ asset('admin-assets/icons/custom/arrow.png') }}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$complete_count ?? 0}} </span> <img
                                            src="{{ asset('admin-assets/icons/custom/Trending_down.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows Applications Approved By BGV</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-6 mb-4 summary-card">
                <a class="text-dark" href="{{route('admin.Green-Drive-Ev.bgvvendor.bgv_list',['type'=>'reject_application'])}}">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-between align-items-start my-3">
                                    <h5 class="ps-2 mb-0" style="border-left: 4px solid #dc3545;"><span style="color:#4b5563;">Rejected</span></h5>
                                    <img src="{{asset('admin-assets/icons/custom/arrow.png')}}" class="img-fluid">
                                </div>
                                <div class="my-4">
                                    <h4 class="mb-0"><span style="color: #4b5563;" class="pe-2"> {{$reject_count ?? 0}} </span> <img
                                            src="{{ asset('admin-assets/icons/custom/Down_Icon.png') }}"
                                            class="img-fluid"></h4>
                                </div>
            
                                <div class="mb-3">
                                    <p class="text-muted">Shows Applications rejected by BGV </p>
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



        
        

        
        
       <div class="offcanvas offcanvas-end" tabindex="-1" id="DashoffcanvasRightHR01" aria-labelledby="DashoffcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="DashoffcanvasRightHR01Label">HR Level 01 Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearSummaryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applySummaryFilter()">Apply</button>
            </div>
           
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="today" name="STtimeLine"  id="timeLine1" {{ request()->timeline == 'today' ? 'checked' : '' }}>
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio"  value="this_day" name="STtimeLine" id="timeLine2" {{ request()->timeline == 'this_day' ? 'checked' : '' }}>
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_month" name="STtimeLine" id="timeLine3" {{ request()->timeline == 'this_month' ? 'checked' : '' }}>
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_year" name="STtimeLine" id="timeLine4" {{ request()->timeline == 'this_year' ? 'checked' : '' }}>
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request()->from_date }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{ request()->to_date }}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearSummaryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applySummaryFilter()">Apply</button>
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
          

           
    function applySummaryFilter() {
         const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
         const timeline = selectedTimeline ? selectedTimeline.value : '';
        const from_date = document.getElementById('FromDate').value;
        const to_date = document.getElementById('ToDate').value;
        
        if(from_date != "" || to_date != ""){
            if(to_date == "" || from_date == ""){
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

    
        window.location.href = url.toString();
    }


    
     
    function clearSummaryFilter() {
        const url = new URL(window.location.href);
        url.searchParams.delete('from_date');
        url.searchParams.delete('to_date');
        url.searchParams.delete('timeline');
        window.location.href = url.toString();
    }
    
    
        function DashRightSideFilerOpen(){
            const bsOffcanvas = new bootstrap.Offcanvas('#DashoffcanvasRightHR01');
            bsOffcanvas.show();
        }
    </script>
    


</script>
    @endsection
</x-app-layout>