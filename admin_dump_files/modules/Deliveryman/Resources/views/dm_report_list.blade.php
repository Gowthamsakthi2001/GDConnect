<x-app-layout>
    
    <div class="main-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">{{ $dm->first_name }} {{ ' Preview' }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"> {{ 'Joined At' }} {{ $dm->created_at->format('Y-m-d h:i A') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3><img src="{{ asset('public/EV/images/photos/'.$dm->photo) }}" class="img-fluid rounded-circle" alt="Deliveryman Image" style="width: 150px; height: 150px; object-fit: cover;"> Profile Details</h3>
                    <div class="row mt-4">
                        <div class="col-12 mt-3 mt-md-0">
                            <p><b>First Name : </b> {{ $dm->first_name }}</p>
                            <p><b>Last Name : </b> {{ $dm->last_name }}</p>
                            <p><b>Mobile : </b> {{ $dm->mobile_number }} </p>
                            <p><b>Description : </b> {{ $dm->remarks }} </p>
                            <p><b>Current City : </b> {{ $dm->current_city->city_name ?? '' }} </p>
                            <!--<p><b>Lead Source : </b> {{ $dm->source_name }} </p>-->
                            <p><b>Intrested City: </b> {{ $dm->interest_city->Area_name ?? '' }} </p>
                            <!--<p><b>Job Apply Source: </b> {{ $dm->apply_job_source }} </p>-->
                           <?php
                              $hub = \Modules\Clients\Entities\ClientHub::where('client_id',$dm->client_id)->where('id',$dm->hub_id)->first();
                            ?>
                            <p><b>Vehicle Type : </b> {{ $dm->vehicle_type }} </p>
                            <p><b>Zone : </b> {{ $hub->zone->name ?? ''}} </p>
                            <p><b>Client Name : </b> {{ $hub->client->client_name ?? ''}} </p>
                            <p><b>Hub Name : </b> {{ $hub->hub_name ?? ''}} </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
                    $spouse_name = $dm->spouse_mobile_number;
                    $spouse_num_length = strlen($spouse_name);
                
                    $spouse_new_phone = ''; // Initialize the variable
                
                    if($spouse_num_length == 3) {
                        $spouse_new_phone = ''; // Set to empty string if length is 3
                    } else {
                        $spouse_new_phone = $spouse_name; // Set to the original value otherwise
                    }
                @endphp

        
        
        
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3><img src="{{ asset('public/EV/images/photos/'.$dm->photo) }}" class="img-fluid rounded-circle" alt="Deliveryman Image" style="width: 150px; height: 150px; object-fit: cover;">Personal Details</h3>
                    <div class="row mt-4">
                
                        <div class="col-12 mt-3 mt-md-0">
                            <p><b>First Name : </b> {{ $dm->first_name }}</p>
                            <p><b>Date OF Birth : </b> {{ $dm->date_of_birth }}</p>
                            <p><b>Mobile : </b> {{ $dm->mobile_number }} </p>
                            <p><b>Father/ Mother/ Guardian Name : </b> {{ $dm->father_name }} </p>
                            <p><b>Father/ Mother/ Guardian Contact No : </b> {{ $dm->father_mobile_number }} </p>
                            
                          

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>

    <div class="my-4">
        <h4 class="text-muted mb-1">{{ 'Deliveryman Logs Summary' }}</h4>
        <p>Detailed Overview of logged Timesheets and Hours</p>
    </div>
   
    
     <div class="row my-3 g-3">
        <!-- CARD 1 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('all')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">Total Logged Time</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-all text-primary">00:00</p>
            </div>
          </div>
        </div>
    
        <!-- CARD 2 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('last_month')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">Last Month Logged</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-last_month">00:00</p>
            </div>
          </div>
        </div>
    
        <!-- CARD 3 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('this_month')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">This Month Logged</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-this_month">00:00</p>
            </div>
          </div>
        </div>
    
        <!-- CARD 4 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('last_week')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">Last Week Logged</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-last_week">00:00</p>
            </div>
          </div>
        </div>
    
        <!-- CARD 5 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('this_week')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">This Week Logged</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-this_week">00:00</p>
            </div>
          </div>
        </div>
        
        <!-- CARD 6 -->
        <div class="col-6 col-md-4 col-xl-3 cursor-pointer" onclick="fetchReport('daily')">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3">
              <h6 class="text-muted mb-2">Today Logged</h6>
              <p class="mb-0 fs-5 fw-semibold card-time card-time-daily">00:00</p>
            </div>
          </div>
        </div>
      </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
               <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <!-- Left Controls -->
                <div class="d-flex align-items-end flex-wrap gap-3">
                    <!-- Filter Type Select -->
                    <div>
                        <label for="summarySelect" class="form-label mb-1">Select</label>
                        <select class="form-select" id="summarySelect" onchange="fetchReport(this.value)" style="padding:8px 25px;">
                            <option value="all">Total Logged Time</option>
                            <option value="last_month">Last Month Logged</option>
                            <option value="this_month">This Month Logged</option>
                            <option value="last_week">Last Week Logged</option>
                            <option value="this_week">This Week Logged</option>
                            <option value="daily">Today Logged</option>
                            <option value="period">Period</option>
                        </select>
                    </div>
            
                    <!-- From Date -->
                    <div class="period-field d-none">
                        <label for="FromDate" class="form-label mb-1">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control form-control-sm">
                    </div>
            
                    <!-- To Date -->
                    <div class="period-field d-none">
                        <label for="ToDate" class="form-label mb-1">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control">
                    </div>
            
                    <!-- Apply Button -->
                    <div class="d-flex align-items-end period-field d-none">
                        <button class="btn btn-dark" type="button" style="padding:8px 25px;" onclick="PeriodApply()">Apply</button>
                    </div>
                </div>
            
                <!-- Right Side: Export Button -->
                <div>
                    <button class="btn btn-dark" type="button" id="download-btn">Export</button>
                </div>
            </div>


                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead thead class="bg-success rounded">
                            <tr>
                                <th class="border-0 text-capitalize text-white">#</th>
                                <th class="border-0 text-capitalize text-white">{{'Date'}}</th>
                                <th class="border-0 text-capitalize text-white">{{'In Time'}}</th>
                                <th class="border-0 text-capitalize text-white">{{'Out Time'}}</th>
                                <th class="border-0 text-capitalize text-white">{{'Total Online Hours'}}</th>
                                <th class="border-0 text-capitalize text-white">{{'Action'}}</th>
                            </tr>
                        </thead>
                        <tbody id="set-rows" class="bg-white border border-white">
                            <!-- Dynamic rows go here -->
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <!-- Additional footer content if needed -->
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="log_edit_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="" id="log_edit_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit a Log</h1>
                            <button type="button" class="btn-close rounded px-3 border-0" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="edit_date" class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="edit_date" name="edit_date" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="edit_in_time" class="form-label">In Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit_in_time" name="edit_in_time" value="">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="mb-3">
                                        <label for="edit_out_time" class="form-label">Out Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="edit_out_time" name="edit_out_time" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="saveLogChanges()">Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
  
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script type="text/javascript">
    
       document.getElementById('download-btn').addEventListener('click', function() {
            const table = document.getElementById('datatable'); 
            const cloneTable = table.cloneNode(true);
            const actionIndex = Array.from(cloneTable.querySelectorAll('th')).findIndex(th => th.textContent.trim() === 'Action');
            cloneTable.querySelectorAll('tr').forEach(row => {
                if (row.children[actionIndex]) {
                    row.removeChild(row.children[actionIndex]);
                }
            });
            const wb = XLSX.utils.table_to_book(cloneTable, { sheet: 'Sheet1' });
            XLSX.writeFile(wb, 'Deliveryman_log_list.xlsx'); 
        });

    
        $(document).ready(function() {
    
            $.ajax({
                url: '{{ route('admin.Green-Drive-Ev.delivery-man.login-logs.report-list',[$dm->id]) }}',  
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        populateTable(response.data,response.view_status);
                    }
                    console.log(response.summary.all);
                    $(".card-time-all").text(response.summary.all);
                    $(".card-time-last_month").text(response.summary.last_month);
                    $(".card-time-this_month").text(response.summary.this_month);
                    $(".card-time-last_week").text(response.summary.last_week);
                    $(".card-time-this_week").text(response.summary.this_week);
                    $(".card-time-daily").text(response.summary.daily);
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching data:", error);
                }
            });
            
        });
        
        function formatDateIndian(dateString) {
            let date = new Date(dateString);
            let day = String(date.getDate()).padStart(2, '0'); 
            let month = String(date.getMonth() + 1).padStart(2,
            '0'); 
            let year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
        
        function populateTable(data,show_status) {
            let rows = '';
            let totalTime = 0;
            let totalInMinutes = 0;
            let totalOutMinutes = 0;
            if (data.length > 0) {
                $("#itemCount").text(data.length);
                $.each(data, function(index, report) {
                    let inTime = report.punched_in ? new Date(report.punched_in) : null;
                    let outTime = report.punched_out ? new Date(report.punched_out) : null;
                    let diffMins = 0;
        
                    if (inTime && outTime) {
                        let diffMs = outTime - inTime;
                        diffMins = Math.floor(diffMs / 60000);
                        totalTime += diffMins;
                        totalInMinutes += (inTime.getHours() * 60) + inTime.getMinutes();
                        totalOutMinutes += (outTime.getHours() * 60) + outTime.getMinutes();
                    }
        
                    let hours = Math.floor(diffMins / 60);
                    let minutes = diffMins % 60;
        
                    rows += `<tr>
                        <td>${index + 1}</td>
                        <td>${inTime ? formatDateIndian(report.punched_in) : '-'}</td>
                        <td>${report.punched_in ? report.punched_in.split(' ')[1] : '-'}</td>  <!-- In time -->
                        <td>${report.punched_out ? report.punched_out.split(' ')[1] : '-'}</td> <!-- Out time -->
                        <td>${hours} hours ${minutes} minutes</td>  <!-- Duration -->
                       <td>${
                            show_status == 1 
                                ? `<a class="btn btn-outline-info mx-1" href="javascript:void(0);" onclick="LogEditable(${report.id})" title="Edit">
                                       <i class="fa-solid fa-pen-to-square"></i> 
                                   </a>`
                                : `-`
                        }</td>  
                    </tr>`;
                });
        
                let totalHours = Math.floor(totalTime / 60);
                let totalMinutes = totalTime % 60;
                let totalInHours = Math.floor(totalInMinutes / 60);
                let totalInMins = totalInMinutes % 60;
                let totalOutHours = Math.floor(totalOutMinutes / 60);
                let totalOutMins = totalOutMinutes % 60;
        
                rows += `<tr style="font-weight: bold;">
                    <td colspan="1" class="text-center">Grand Total:</td>
                    <td>${data.length} Times</td> 
                    <td></td>
                    <td></td>
                    <td>${totalHours} hours ${totalMinutes} minutes</td>  
                </tr>`;
        
            } else {
                rows += `<tr><td colspan="6" class="text-center">No data available</td></tr>`;
            }
        
            // Populate the table
            $('#set-rows').html(rows);  
        }

    
         function fetchReport(filterType) {
            document.querySelectorAll('.card-time').forEach(el => {
                el.classList.remove('text-primary');
            });
        
            if (filterType !== "period") {
                $(".card-time-" + filterType).addClass("text-primary");
                $("#summarySelect").val(filterType);
        
                document.querySelectorAll('.period-field').forEach(el => {
                    el.classList.add('d-none');
                    el.classList.remove('d-block');
                });
        
                $.ajax({
                    url: '{{ route('admin.Green-Drive-Ev.delivery-man.login-logs.report-list',[$dm->id]) }}',
                    method: 'GET',
                    data: { filter_type: filterType },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            populateTable(response.data, response.view_status);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching data:", error);
                    }
                });
        
            } else {
                document.querySelectorAll('.period-field').forEach(el => {
                    el.classList.remove('d-none');
                    el.classList.add('d-block');
                });
            }
        }
        
        function PeriodApply(){
          var filterType = $("#summarySelect").val();
          var from_date =  $("#FromDate").val();
          var to_date =  $("#ToDate").val();
          
          if(from_date == "" || to_date == ""){
              toastr.error("The From date and To date fields are required"); 
              return;
          }
          
          $.ajax({
                url: '{{ route('admin.Green-Drive-Ev.delivery-man.login-logs.report-list',[$dm->id]) }}',
                method: 'GET',
                data: { filter_type: filterType,from_date: from_date, to_date: to_date},
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        populateTable(response.data, response.view_status);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching data:", error);
                }
            });
        }

    
        function exportToExcel() {
            const table = document.getElementById("columnSearchDatatable");  
        
            if (!table) {
                alert('The table does not exist.');
                return;
            } 
        
            let tbody = $("#set-rows").text(); 
        
            if (tbody == "") {
                    Swal.fire({
                        text: 'Record Not Found',
                        type: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#00868f',
                        confirmButtonText: '{{'Ok'}}',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $('#tbody').html('');
                        }
                    });
                return;
            } 
         
            const ws = XLSX.utils.table_to_sheet(table);
            ws['!cols'] = [
              { width: 12 },
              { width: 10 }, 
              { width: 15 },
              { width: 15 }, 
              { width: 16 }
            ];
        
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws);
            const today_Date = new Date();
            const formattedDate = today_Date.toISOString().split('T')[0];
            const excelFileName = `report_${formattedDate}.xlsx`;
            
            // Save the Excel file
            XLSX.writeFile(wb, excelFileName);
            $('#set-rows').html('');
            
            $("#from_date").val(formattedDate);
            $("#to_date").val(formattedDate);
        }
        
       function LogEditable(id) {
            const url = "{{ route('admin.Green-Drive-Ev.delivery-man.single_log_edit', ['id' => '__id__']) }}".replace('__id__', id);
        
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $("#log_edit_modal").modal('show');
        
                        // Set the values in the modal
                        $("#edit_date").val(response.date);
                        $('#edit_in_time').val(response.in_time); 
                        $("#edit_out_time").val(response.out_time); 
                        $('#log_edit_form').data('log-id', response.id);
                    } else {
                        toastr.error(response.message); 
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText); 
                }
            });
        }

        function saveLogChanges() {
            const logId = $('#log_edit_form').data('log-id'); 
            const date = $('#edit_date').val();
            const inTime = $('#edit_in_time').val();
            const outTime = $('#edit_out_time').val();
        
            // Ensure all necessary fields are provided
            if (!date || !inTime || !outTime) {
                toastr.error("Please fill all fields.");
                return;
            }
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.delivery-man.single_log_update') }}", 
                type: 'POST',
                data: {
                    id: logId,
                    date: date,
                    in_time: inTime,
                    out_time: outTime,
                    _token: '{{ csrf_token() }}', 
                },
                success: function(response) {
                    if (response.status) {
                        render_log_data(response.user_id);
                        toastr.success(response.message); 
                        $("#log_edit_modal").modal('hide'); 
                    } else {
                        toastr.error(response.message); 
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText); 
                }
            });
        }


        function render_log_data(user_id) {
            $.ajax({
                url: `{{ route('admin.Green-Drive-Ev.delivery-man.login-logs.report-list', ['id' => '__id__']) }}`.replace('__id__', user_id),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        populateTable(response.data,response.view_status);
                    }
                    
                },
                error: function(xhr, status, error) {
                    console.log("Error fetching data:", error);
                }
            });
        }


        
    </script>
    
</x-app-layout>