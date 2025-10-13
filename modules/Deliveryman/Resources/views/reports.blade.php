<x-app-layout>
    <style>
    .add-new {
        background-image: linear-gradient(310deg, #7928ca, #ff0080) !important;
    }
    
    .add-new:hover {
        background-image: linear-gradient(310deg, #ff0080, #7928ca) !important;
    }

    @media screen and (max-width: 476px) {
        .icons-cls a i {
            font-size: 12px;    
        }
    }
</style>

<div class="main-content">
    <div class="page-header mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <h2 class="page-header-title mb-3">
                <span>{{ 'Deliveryman List' }}</span>
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
               <div class="row align-items-center mb-3">
                    <div class="col-md-4 col-12 mb-3">
                        <h5 class="card-title mb-0">
                            Deliveryman Table 
                            <span class="badge bg-dark ms-2" id="itemCount">{{ count($dm) }}</span>
                        </h5>
                    </div>
                  
                    <div class="col-12">
                        <div class="row g-2">
                                <div class="col-md-3 col-xl-2 col-12">
                                
                                        <label for="reportSelect" class="form-label">Select Report</label>
                                           <select class="form-select custom-select2-field form-select-sm" id="summarySelect"  style="padding:8px 25px;" onchange="EmpLogExportFilter()">
                                                <option value="all" {{$summary_type == 'all' ? 'selected' : ''}}>Total Logged Time</option>
                                                <option value="last_month" {{$summary_type == 'last_month' ? 'selected' : ''}}>Last Month Logged</option>
                                                <option value="this_month" {{$summary_type == 'this_month' ? 'selected' : ''}}>This Month Logged</option>
                                                <option value="last_week" {{$summary_type == 'last_week' ? 'selected' : ''}}>Last Week Logged</option>
                                                <option value="this_week" {{$summary_type == 'this_week' ? 'selected' : ''}}>This Week Logged</option>
                                                <option value="yesterday" {{$summary_type == 'yesterday' ? 'selected' : ''}}>Yesterday Logged</option> //updated by logesh
                                                <option value="daily" {{$summary_type == 'daily' ? 'selected' : ''}}>Today Logged</option>
                                                
                                                <option value="period"{{$summary_type == 'period' ? 'selected' : ''}}>Period</option>
                                            </select>
                                    </div>
                                    <div class="col-md-3 col-xl-2  col-12">
                                        <label for="city_id_filter" class="form-label">City</label>
                                        <select class="form-select custom-select2-field form-select-sm" id="city_id_filter" onchange="EmpLogExportFilter()">
                                            <option value="">Select City</option>
                                            @if(isset($cities))
                                                @foreach($cities as $val)
                                                    <option value="{{ $val->id }}" {{ $city_id == $val->id ? 'selected' : '' }}>
                                                        {{ $val->city_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-xl-2">
                                        <label for="zone_id" class="form-label">Zone</label>
                                            <select id="zone_id" class="form-control custom-select2-field" onchange="EmpLogExportFilter()">
                                                <option value="">Select Zone</option>
                                                
                                                @if(isset($zones))
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone->id }}" {{ $zone_id == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    
                                        <div class="col-md-3 col-xl-2 ">
                                            <label for="client_id" class="form-label">Client</label>
                                            <select id="client_id" class="form-control custom-select2-field" onchange="EmpLogExportFilter()">
                                                <option value="">Select Client</option>
                                                @if(isset($clients))
                                                    @foreach ($clients as $client)
                                                        <option value="{{ $client->id }}" {{ $client_id == $client->id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                  
                        
                                    <!--<div class="col-md-3 col-xl-2  col-12 d-flex align-items-end">-->
                                    <!--    <a href="{{route('admin.Green-Drive-Ev.delivery-man.export_dm_log_list',['city_id'=>$city_id,'zone_id'=>$zone_id,'client_id'=>$client_id,'summary_type'=>$summary_type,'from_date'=>$from_date,'to_date'=>$to_date])}}" class="btn btn-dark w-100" style="padding-top: 0.6rem !important;padding-bottom: 0.6rem !important;">Export</a>-->
                                    <!--</div>-->
                                    
                                    <div class="col-md-3 col-xl-2  col-12 d-flex align-items-end">
                                        <button id="exportBtn" class="btn btn-dark w-100 py-2">
                                            <span id="btnText">Export</span>
                                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                        </button>
                                    </div>
                            </div>
                        <div class="row {{$summary_type == 'period' ? 'my-3' : ''}}">
                            <div class="col-md-3 col-xl-2 col-12 period-field d-none">
                                        <div class="">
                                            <label for="FromDate" class="form-label mb-1">From Date</label>
                                            <input type="date" name="from_date" id="FromDate" class="form-control form-control-sm" max="{{date('Y-m-d')}}" value="{{$from_date ?? old('from_date')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-2 col-12 period-field d-none">
                                        <div class="">
                                            <label for="ToDate" class="form-label mb-1">To Date</label>
                                            <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date ?? old('to_date')}}">
                                        </div>
                                    </div>
                                     <div class="col-md-3 col-xl-2 col-12 period-field d-none">
                                         <label class="form-label mb-1 text-white">Apply</label>
                                       <div class="">
                                            <button class="btn btn-dark w-100" type="button" style="padding:8px 25px;" onclick="EmpLogExportFilter()">Apply</button>
                                        </div>
                                    </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table id="dm_log_table_list" class="table text-start" style="width: 100%;">
                        <thead class="bg-success rounded">
                            <tr>
                                <th scope="col" class="text-white">SL</th>
                                <th scope="col" class="text-white">Name</th>
                                <th scope="col" class="text-white">City</th>
                                <th scope="col" class="text-white">Zone</th>
                                <th scope="col" class="text-white">Client</th>
                                 @if($summary_type == 'daily'|| $summary_type == 'yesterday')
                                <th scope="col" class="text-white text-center">Date</th>
                                <th scope="col" class="text-white text-center">In Time</th>
                                <th scope="col" class="text-white text-center">Out Time</th>
                                @endif
                                <th scope="col" class="text-white">Total Online Hours</th>
                                <th scope="col" class="text-white">Status</th>
                                <th scope="col" class="text-white">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white border border-white">
                            @foreach($dm as $index => $data)
                               @if($data->first_name != "" && $data->last_name != "")
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <p style="font-weight: bold;">{{ $data->first_name }} {{ $data->last_name }}</p>
                                        </td>
                                        <td>{{ $data->city_name ?? 'N/A' }}</td>
                                        <td>{{ $data->zone_name ?? '-' }}</td>
                                        <td>{{ $data->client_name ?? '-' }}</td>
                                         @if($summary_type == 'daily')
                                            <?php
                                            
                                                $today_log = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                                                ->where('user_id', $data->user_id)
                                                ->whereDate('punched_in', \Carbon\Carbon::today())
                                                ->orderBy('id', 'desc')
                                                ->first();
    
                                            ?>
                                            <td>{{ $today_log && $today_log->punched_in ? date('d-m-Y', strtotime($today_log->punched_in)) : '-' }}</td>
                                            <td>{{ $today_log && $today_log->punched_in ? date('H:i:s', strtotime($today_log->punched_in)) : '-' }}</td>
                                            <td>{{ $today_log && $today_log->punched_out ? date('H:i:s', strtotime($today_log->punched_out)) : '-' }}</td>
                                        @endif
                                        @if($summary_type == 'yesterday')
                                            <?php
                                            
                                                $today_log = \Illuminate\Support\Facades\DB::table('ev_delivery_man_logs')
                                                ->where('user_id', $data->user_id)
                                                ->whereDate('punched_in', \Carbon\Carbon::yesterday())
                                                ->orderBy('id', 'desc')
                                                ->first();
    
                                            ?>
                                            <td>{{ $today_log && $today_log->punched_in ? date('d-m-Y', strtotime($today_log->punched_in)) : '-' }}</td>
                                            <td>{{ $today_log && $today_log->punched_in ? date('H:i:s', strtotime($today_log->punched_in)) : '-' }}</td>
                                            <td>{{ $today_log && $today_log->punched_out ? date('H:i:s', strtotime($today_log->punched_out)) : '-' }}</td>
                                        @endif
                                        <td>{{ $data->total_time }}</td>
                                        <td>
                                            <span class="badge text-white {{ $data->rider_status === 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ $data->rider_status === 0 ? 'Offline' : 'Online' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a class="me-1 icon-btn" href="{{ route('admin.Green-Drive-Ev.delivery-man.login-logs.preview', [$data->user_id]) }}" title="View log">
                                                 <img src="{{asset('public/admin-assets/img/yellow_eye.jpg')}}" class="rounded icon-btn" alt="Image">
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript functions for handling status change and table filtering
    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = url;
            }
        });
    }
    
    function route_alert(route, message, title = "Are you sure?") {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                location.href = route;
            }
        });
    }

    let typingTimer;
    const debounceTime = 300; // Delay in milliseconds
    
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(filterTable, debounceTime);
    });
    
    function filterTable() {
        var searchValue = document.getElementById('searchInput').value.toLowerCase();
        var table = document.getElementById('datatable');
        var rows = table.getElementsByTagName('tr');

        if (searchValue === '') {
            for (var i = 1; i < rows.length; i++) {
                rows[i].style.display = '';
            }
            return;
        }

        for (var i = 1; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName('td');
            var rowContainsSearchValue = false;

            for (var j = 0; j < cells.length; j++) {
                if (cells[j].innerText.toLowerCase().includes(searchValue)) {
                    rowContainsSearchValue = true;
                    break;
                }
            }

            rows[i].style.display = rowContainsSearchValue ? '' : 'none';
        }
    }
    
    document.querySelector('.location-reload-to-base').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        var rows = document.getElementById('datatable').getElementsByTagName('tr');
        
        for (var i = 1; i < rows.length; i++) {
            rows[i].style.display = '';
        }
    });

    function exportToExcel() {
        const table = document.getElementById("datatable");
        const ws = XLSX.utils.table_to_sheet(table);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws);

        const excelFileName = 'DeliveryManList.xlsx';
        XLSX.writeFile(wb, excelFileName);
    }
</script>
@section('script_js')
<script>
    
    $(document).ready(function () {
      $("#dm_log_table_list").DataTable({
        dom: "lfrtip",
        buttons: ["excel", "pdf", "print"],
        columnDefs: [{ orderable: false, targets: "_all" }],
        lengthMenu: [
          [10, 25, 50, 100, 250, -1],
          [10, 25, 50, 100, 250, "All"],
        ],
        responsive: false,
        scrollX: true,
        pageLength: 25,
      });

    });
    
    $(document).ready(function(){
        PeriodHandleList();
    });
    
    function PeriodHandleList(){
       
       var type = $("#summarySelect").val();
       if(type != "period"){
             document.querySelectorAll('.period-field').forEach(el => {
                el.classList.add('d-none');
                el.classList.remove('d-block');
            });
       }else{
           document.querySelectorAll('.period-field').forEach(el => {
                el.classList.remove('d-none');
                el.classList.add('d-block');
            });
       }
   }

    function EmpLogExportFilter(){
       var city_id = $("#city_id_filter").val();
       var zone_id = $("#zone_id").val();
       var client_id = $("#client_id").val();
       var type = $("#summarySelect").val();
       var city_id = $("#city_id_filter").val();
       var FromDate = $("#FromDate").val();
       var ToDate = $("#ToDate").val();
       var url = new URL(window.location.href);
       
       url.searchParams.set('city_id',city_id);
       url.searchParams.set('zone_id',zone_id);
       url.searchParams.set('client_id',client_id);
       url.searchParams.set('summary_type',type);
       url.searchParams.set('from_date',FromDate);
       url.searchParams.set('to_date',ToDate);
       
       window.location.href = url.toString();
   }
   

</script>
<script>
document.getElementById('exportBtn').addEventListener('click', function () {
    let btn = this;
    let btnText = document.getElementById('btnText');
    let btnSpinner = document.getElementById('btnSpinner');

    // disable + show spinner
    btn.disabled = true;
    btnText.innerText = "Exporting...";
    btnSpinner.classList.remove('d-none');

    // Get values directly from the inputs/selects
    let city_id      = document.getElementById('city_id_filter').value;
    let summary_type = document.getElementById('summarySelect').value;
    let from_date    = document.getElementById('FromDate').value;
    let to_date      = document.getElementById('ToDate').value;
    let zone_id      = document.getElementById('zone_id') ? document.getElementById('zone_id').value : '';
    let client_id    = document.getElementById('client_id') ? document.getElementById('client_id').value : '';

    // Build URL dynamically
    let exportUrl = "{{ route('admin.Green-Drive-Ev.delivery-man.export_dm_log_list') }}";
    exportUrl += `?city_id=${encodeURIComponent(city_id)}&summary_type=${encodeURIComponent(summary_type)}&from_date=${encodeURIComponent(from_date)}&to_date=${encodeURIComponent(to_date)}&zone_id=${encodeURIComponent(zone_id)}&client_id=${encodeURIComponent(client_id)}`;

    // Make the fetch call
    fetch(exportUrl)
        .then(response => {
            if (!response.ok) throw new Error("Server error");
            return response.blob();
        })
        .then(blob => {
            // trigger file download
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = "Employee_Log_list.xlsx";
            document.body.appendChild(a);
            a.click();
            a.remove();

            console.log("✅ Export completed successfully!");
        })
        .catch(() => {
            console.log("❌ Export failed. Please try again.");
        })
        .finally(() => {
            // restore button
            btn.disabled = false;
            btnText.innerText = "Export";
            btnSpinner.classList.add('d-none');
        });
});
</script>

@endsection
</x-app-layout>