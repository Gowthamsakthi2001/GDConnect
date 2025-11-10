<x-app-layout>
@php
 

$cities = \Illuminate\Support\Facades\DB::table('ev_tbl_city')
    ->select('id', 'city_name')
    ->where('status', 1)
    ->get();


$users = \Illuminate\Support\Facades\DB::table('model_has_roles')
    ->join('users', 'model_has_roles.model_id', '=', 'users.id') // Join with users table
    ->select('users.id as user_id', 'users.name as user_name')   // Select required columns
    ->where('model_has_roles.role_id', 3)                       // Filter role_id = 3
    ->where('users.status','Active')
    ->get();
@endphp


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
        integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('public/EV/css/lead_page.css')}}"/>
 
<body>
           
    <?php 
        $db = \Illuminate\Support\Facades\DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();
            
        $roles = DB::table('roles')
        ->where('id', $db->role_id)
        ->first();
         
         if($roles->name == 'Telecaller'){
             // Assuming $list is an array or collection
            $list = collect($list)->filter(function ($item) use ($roles) {
                if($item->assigned == auth()->user()->id){
                    return $list = $item;
                }
            });
         }

    ?>

    <?php
       $get_telecallers =  \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->join('users', 'model_has_roles.model_id', '=', 'users.id') 
        ->select('users.id as user_id', 'users.name as user_name')   
        ->where('model_has_roles.role_id', 3)
        ->where('users.status', 'Active')
        ->get();
    ?>

     <!-- kanban view -->
    <div class="kanban-view" id="kanban-view">
        <div class="container-kanban">
            <div class="row mt-3 d-flex flex-wrap {{in_array($login_user_id, $get_approve_ids) ? 'justify-content-between' : 'justify-content-end'}} align-items-center">
                @if(in_array($login_user_id, $get_approve_ids))
                <div class="col-md-6 col-12 d-flex flex-wrap justify-content-start align-items-center">
                     <select class="form-control basic-single" style="width:370px;" onchange="GetTelecallerData(this,this.value)">
                        <option value="">Select Telecaller</option>
                        <option value="not-assigned" data-export_url="{{ route('admin.user.staff_export', ['id' => 0, 'user_role' => 'not-assigned']) }}">Not Assigned</option>
                        @if(isset($get_telecallers))
                           @foreach ($get_telecallers as $data)
                            <option value="{{ $data->user_id }}"  data-export_url="{{ route('admin.user.staff_export', ['id' => $data->user_id, 'user_role' => 'Telecaller']) }}">{{ $data->user_name }} </option>
                           @endforeach
                        @endif
                    </select>
                     <a href="javascript:void(0);" class="btn custom-btn btn-round btn-sm ms-2 px-3" id="telecaller_export_url">
                        <i class="bi bi-download"></i> Onboard
                    </a>
                </div>
                @endif
                
                <div class="col-md-6 col-12 d-flex flex-wrap justify-content-end align-items-center gap-2">
                      <!-- New Lead Button -->
                      <a href="javascript:void(0);" class="btn custom-btn btn-round btn-sm px-3" onclick="window.location.reload()">
                          <i class="bi bi-arrow-clockwise"></i> Refresh
                      </a>
                      <div>
                          <div class="input-group mb-md-0 mb-sm-3">
                              <input type="text" class="form-control form-control-sm" id="search-bar" style="width: 335px;"
                                  placeholder="Search Mobile Number Ex: +91**********"
                                  oninput="SearchLeadData(this.value,event)"
                                  aria-describedby="SearchHelpBlock" autocomplete="off">
                              <button class="btn custom-btn btn-sm searchOrcancel" type="button">
                                  <i class="bi bi-search searchOrcancelIcon"></i>
                              </button>
                          </div>
                          <div id="SearchHelpBlock" class="form-text text-danger fw-medium" style="text-align: left;"></div>
                      </div>
                  </div>

            </div>
            <div class="row mt-3">
                    <!-- First Row -->
                    <div class="col-md-6 col-12 d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn custom-btn btn-round btn-sm " href="{{ route('admin.Green-Drive-Ev.leads.Excel_download') }}">
                                <i class="bi bi-download"></i> Bulk Demo
                            </a>
                            <a href="{{ route('admin.Green-Drive-Ev.leads.lead_import_verify') }}" class="btn custom-btn btn-round btn-sm">
                                <i class="bi bi-eye"></i> Import Verify
                            </a>
                            <button class="btn custom-btn btn-round btn-sm" id="userButton">
                                <i class="bi bi-download"></i> Telecaller Export
                            </button>
                            <button class="btn custom-btn btn-round btn-sm" id="citybutton">
                                <i class="bi bi-download"></i> City Export
                            </button>
                        </div>
                    </div>
                
                   <!-- Second Row -->
                    <div class="col-md-6 col-12 d-flex justify-content-end align-items-center gap-2 mb-3">
                        <!-- New Lead Button -->
                        <a href="#" class="btn custom-btn btn-round btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="new-button">
                            <i class="bi bi-plus-lg"></i> New Lead
                        </a>
                    
                        <!-- Import Form -->
                        <form class="d-flex align-items-center gap-2" action="{{ route('admin.Green-Drive-Ev.leads.uploadLeads') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" class="form-control form-control-sm" id="excel_file" name="excel_file" accept=".csv,.xls,.xlsx">
                            <button type="submit" class="btn custom-btn btn-round btn-sm d-flex">
                                <i class="bi bi-upload me-2"></i>
                                Import
                            </button>
                        </form>
                    </div>

                </div>

            @php
                $statuses = [
                    'New' => 'New Lead',
                    'Contacted' => 'Contacting',
                    'Call_Back' => 'Call Back Request',
                    'Onboarded' => 'Onboarded',
                    'DeadLead'  => 'Dead Lead'
                ];

                $colors = ['#03a9f4', '#7cb342', '#fb8c00', '#c53da9','#f32f10'];
                $colorIndex = 0;
                $count = 0;
            @endphp

            <div class="row kanban-board mt-3" id="autoload_lead_data">
              
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Create New Lead</h1>
                    <button type="button" class="btn-close rounded px-3 border-0" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.Green-Drive-Ev.leads.add') }}" id="lead-form-id"
                        method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header text-black">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="tele_status" class="form-label">Telecaller Status <span class="text-danger">*</span></label>
                                            <select class="form-control basic-single"
                                                aria-label="Default select example" name="tele_status"
                                                id="tele_status">
                                                @foreach ($statuses as $value => $label)
                                                  
                                                    <option value="{{ $value }}"
                                                        {{ old('tele_status') == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="Source" class="form-label">Source <span class="text-danger">*</span></label>
                                            <select class="form-control basic-single"
                                                aria-label="Default select example" name="Source"
                                                id="Source">
                                                <option value="">Select a Source</option>
                                                 @foreach ($leadsource as $data)
                                                    <option value="{{ $data->id }}"
                                                        {{ old('Source') == $data->id ? 'selected' : '' }}>
                                                        {{ $data->source_name }}
                                                    </option>
                                                 @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="Assigned" class="form-label">Assigned</label>
                                            <select class="form-control basic-single"
                                                aria-label="Default select example" name="Assigned"
                                                id="Assigned">
                                                <option value="">Select a Telecaller</option>
                                                <?php 
                                                if($roles->name == 'Telecaller'){
                                                    $telecaller = \Illuminate\Support\Facades\DB::table('users')->where('id', auth()->user()->id)->get();
                                                }
                                                ?>

                                                @foreach ($telecaller as $data)
                                                    <option value="{{ $data->id }}"
                                                        {{ old('Assigned') == $data->id ? 'selected' : '' }}>
                                                        {{ $data->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="fname"
                                                name="fname" value=""
                                                onkeypress="OnlyStringValidate(event)">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="lname"
                                                name="lname" onkeypress="OnlyStringValidate(event)"
                                                value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="mobile"
                                                name="mobile" oninput="sanitizeAndValidatePhone(this)"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="current_city" class="form-label">Current city <span class="text-danger">*</span></label>
                                            <select class="form-control basic-single"
                                                aria-label="Default select example" name="current_city"
                                                id="current_city" onchange="get_area1(this.value)">
                                                <option value="">Select a City</option>
                                                 @foreach ($City as $data)
                                                    <option value="{{ $data->id }}">
                                                        {{ $data->city_name }}
                                                    </option>
                                                 @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="Interested_city">Interested Area <span class="text-danger">*</span></label>
                                            <select class="form-control basic-single selected_area_lists" id="Interested_cityNew1" name="Interested_city">
                                                <option value="">Select an Area</option>
                                                <option value="">Data Not Found</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="vehicle_type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                            <select class="form-control basic-single"
                                                aria-label="Default select example" name="vehicle_type"
                                                id="vehicle_type">
                                                <option value="">Select a Type</option>
                                                <option value="1">2 wheeler</option>
                                                <option value="2">3 wheeler</option>
                                                <option value="3">4 wheeler</option>
                                                <option value="4">Rental</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                  
                                    <div class="col-6">
                                        <input type="hidden" class="form-control" id="Register_Date" 
                                                name="Register_Date" 
                                                value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                       
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="description" class="mb-3">Description</label>
                                            <textarea class="form-control" placeholder="Write a Description" id="description" name="description" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0">
                                    <button type="button" class="btn bg-info text-white"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success btn-round">Create Lead</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
        
        
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><span style="color:green;">ID No Code</span> <span id="id_text" style="color:green;"></span>- Lead Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.Green-Drive-Ev.leads.addComment') }}" id="lead-form-id" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header text-black">
                            <div class="row">
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="tele_status" class="form-label">Telecaller Status</label>
                                        <select class="form-control basic-single"
                                            aria-label="Default select example" name="tele_status"
                                            id="tele_status1">
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
    
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="Source" class="form-label">Source</label>
                                        <select class="form-control basic-single"
                                            aria-label="Default select example" name="Source"
                                            id="Source1">
                                            <option value="">Select</option>
                                                @foreach ($leadsource as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->source_name }}
                                                </option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
    
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="Assigned" class="form-label">Assigned</label>
                                        <select class="form-control basic-single"
                                            aria-label="Default select example" name="Assigned"
                                            id="Assigned1">
                                            <option value="">Select</option>
                                            <?php
                                            if($roles->name == 'Telecaller'){
                                                
                                                $telecaller = \Illuminate\Support\Facades\DB::table('users')->where('id', auth()->user()->id)->get();
                                            }
                                            ?>
                                            @foreach ($telecaller as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="fname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="fname1"
                                            name="fname" value=""
                                            onkeypress="OnlyStringValidate(event)">
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="lname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lname1"
                                            name="lname" onkeypress="OnlyStringValidate(event)"
                                            value="">
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="mobile" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile1"
                                            name="mobile" oninput="sanitizeAndValidatePhone(this)"
                                            value="">
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="current_city" class="form-label">Current city</label>
                                        <select class="form-control basic-single"
                                            aria-label="Default select example" name="current_city"
                                            id="current_city1" onchange="get_area()">
                                            <option value="">Select a City</option>
                                                @foreach ($City as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->city_name }}
                                                </option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="Interested_city">Interested Area</label>
                                        <select class="form-control basic-single selected_area_lists" id="Interested_city1" name="Interested_city">
                                            <option value="">Select an Area</option>
                                            @if(isset($area_data))
                                                @foreach($area_data as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->Area_name }}
                                                </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
    
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                        <select class="form-control basic-single" 
                                                aria-label="Default select example" 
                                                name="vehicle_type" 
                                                id="vehicle_type1">
                                            <option value="">Select a Type</option>
                                            <option value="1" >2 wheeler</option>
                                            <option value="2" >3 wheeler</option>
                                            <option value="3" >4 wheeler</option>
                                            <option value="4" >Rental</option>
                                        </select>
                                    </div>
                                </div>
    
                            </div>
    
                            <div class="row">
                               
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="task" class="form-label">Created at :</label>
                                        <input type="text" class="form-control" id="created_at1"
                                            name="created_at" value="">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="task" class="form-label">Updated at :</label>
                                        <input type="text" class="form-control" id="updated_at1"
                                            name="updated_at" value="">
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                            
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="mb-3">Description</label>
                                        <textarea class="form-control" placeholder="Write a Descriptions" id="description1" name="description" rows="4"></textarea>
                                        
                                    </div>
                                </div>
                            </div>
    
                            
                            <form  id="lead-comment-id">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-1">
                                            <label for="comment" class="mb-2 text-primary">Comments</label>
                                            <textarea class="form-control js-ck-description commentUpdate_" placeholder="Add Comments" id="commentUpdate_" name="comment" required></textarea>
                                            <input type="hidden" name="task_id" id="task_id" value="">
                                            <input type="hidden" name="user_role" id="user_role" value="{{auth()->user()->name}}">
                                            <input type="hidden" name="commenter_id" id="commenter_id" value="{{auth()->user()->name}}">
                                            <input type="hidden" name="existing_comment_id" id="existing_comment_id" class="existing_comment_id_" value="">
                                        </div>
                                    </div>
                                    
                                </div>
    
                                <div class="modal-footer border-0 m-0 p-0">
                                    <button type="button" class="btn btn-danger" onclick="LeadCommandReset()">Reset</button>
                                    <button type="button" onclick="LeadAddComment()" class="btn btn-primary btn-round CommentAddUpdateBtn">Add Comment</button>
                                </div>
                                </form>
                            <div class="col-12 mt-1">
                                <div class="mb-3" id="comment_details" >
                                   
                                </div>
                            </div>
    
    
                        </div>
                    </div>
                    </form>
                                               
                </div>
            </div>
        </div>
    </div>
        
        
        
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>-->
    <script>
    
    // search filter
    // document.getElementById('search-bar').addEventListener('input', function () {
    //     const searchValue = this.value.trim();
    //     const cards = document.querySelectorAll('.task-body');

    //     cards.forEach(card => {
    //         const phoneNumber = card.querySelector('.phone-number').textContent;
    //         if (phoneNumber.includes(searchValue)) {
    //             card.classList.remove('task-hidden');
    //         } else {
    //             card.classList.add('task-hidden');
    //         }
    //     });
    // });
    
    
    // download city excel sheets 
    document.getElementById('citybutton').addEventListener('click', function() {
        // Dynamic cities data from the backend
        const cities = @json($cities);
    
        // Convert data to a worksheet
        const ws = XLSX.utils.json_to_sheet(cities.map(city => ({
          id: city.id,
          city_name: city.city_name
        })));
    
        // Create a new workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Cities');
    
        // Export the workbook to an Excel file
        XLSX.writeFile(wb, 'cities.xlsx');
    });
    
    
    
    
    document.getElementById('userButton').addEventListener('click', function() {
        // Dynamic users data from the backend
        const users = @json($users);
    
        // Convert data to a worksheet
        const ws = XLSX.utils.json_to_sheet(users.map(user => ({
          id: user.user_id,      // Use 'user_id' as ID column
          name: user.user_name   // Use 'user_name' as Name column
        })));
    
        // Create a new workbook
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Users');
    
        // Export the workbook to an Excel file
        XLSX.writeFile(wb, 'users.xlsx');
    });
        
        
    function createExcelFile() {
        // Include the column names
        const columnNames = [
            "Telecaller_Name",  
            "Rider_First_Name",
            "Rider_Last_Name",
            "Mobile_Number",
            "Current_City_id",
            "Interested_City_id",
            "Vehicle_Type",
            "Description"
        ];
        var telecallerData = {!! json_encode($telecaller) !!};
        var telecallerNames = telecallerData.map(item => item.name);
        const telecallerListString = `"${telecallerNames.join(",")}"`;
        const worksheet = XLSX.utils.aoa_to_sheet([columnNames]);
        for (let i = 2; i <= 100; i++) { 
            worksheet[`A${i}`] = { 
                t: "s", 
                v: "", 
                z: "", 
                l: { Target: "", Tooltip: "Select from dropdown" } 
            };
        }
        worksheet['!dataValidations'] = worksheet['!dataValidations'] || {};
        worksheet['!dataValidations']['A2:A100'] = {  
            type: "list",
            formula1: telecallerListString,
            showDropDown: true
        };

        worksheet['!cols'] = [
            { wpx: 150 }, // Telecaller_Name (150px width)
            { wpx: 150 }, // Rider_First_Name
            { wpx: 150 }, // Rider_Last_Name
            { wpx: 120 }, // Mobile_Number
            { wpx: 120 }, // Current_City_id
            { wpx: 120 }, // Interested_City_id
            { wpx: 80 },  // Vehicle_Type
            { wpx: 200 }, // Description
        ];
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
        XLSX.writeFile(workbook, "Telecaller_Data.xlsx");
    }

    </script>
    <script>
   
        function OnlyStringValidate(event) {
            var regex = new RegExp("^[a-zA-Z]+$");
            var key = String.fromCharCode(event.which || event.keyCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }
        
        function sanitizeAndValidatePhone(input) {
            if (!input.value.startsWith('+91')) {
                input.value = '+91' + input.value.replace(/^\+?91/, ''); // Keep "+91" at the beginning
            }
            input.value = input.value.replace(/[^\d+]/g, ''); // Remove any non-digit, non-plus characters
            if (input.value.length > 13) {
                input.value = input.value.substring(0, 13);
            }
        }

        // Initialize DataTable with custom settings
        document.addEventListener('DOMContentLoaded', function () {
            let now = new Date();
            let formattedDateTime = now.toISOString().slice(0, 16);
            document.getElementById("Register_Date").setAttribute("min", formattedDateTime);
            
            
            // let id_name_value = $("#current_city").val(); // Get the value of the city select dropdown
            // let formData = {
            //     id: id_name_value, // City ID to be sent
            // };
        
            // $.ajax({
            //     url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}', // Route to the controller
            //     method: 'GET', // Use GET if necessary, otherwise POST is preferred
            //     data: formData, // The data to send
            //     success: function(response) {
            //         if (response.status) {
            //             // Directly update the dropdown with the HTML string in response.data
            //             $("#Interested_city").html(response.data); 
            //         } else {
            //             alert(response.message); // Display message if no areas found
            //         }
            //     },
            //     error: function(xhr) {
            //         // Handle errors (e.g., validation errors)
            //         var errors = xhr.responseJSON.errors;
            //         if (errors) {
            //             // Display the error messages
            //             for (var key in errors) {
            //                 alert(errors[key].join(', ')); // Show the errors
            //             }
            //         }
            //     }
            // });
        });
        function get_area(lead_id, city_id) {
            // Prepare form data
            let formData = { id: city_id };
            
            // Clear the area dropdown if no city is selected
            if (city_id === "") {
                $("#Interested_city_" + lead_id).html('<option value="">Select an Area</option><option value="">Data Not Found</option>');
                return;
            }
            
            // AJAX request
            $.ajax({
                url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}', // Adjust if needed
                method: 'GET', // Ensure the backend route accepts GET
                data: formData,
                success: function(response) {
                    // Clear the dropdown
                    $("#Interested_city_" + lead_id).html('');
            
                    if (response.status && response.areas.length > 0) {
                        // Add default option
                        $("#Interested_city_" + lead_id).append('<option value="">Select an Area</option>');
            
                        // Append areas
                        response.areas.forEach(function(area) {
                            let option = $('<option></option>')
                                .attr('value', area.id)
                                .text(area.Area_name);
                            $("#Interested_city_" + lead_id).append(option);
                        });
                    } else {
                        // No areas found
                        $("#Interested_city_" + lead_id).html('<option value="">Select an Area</option><option>Data Not Found</option>');
                    }
                },
                error: function(xhr) {
                    // Handle error
                    $("#Interested_city_" + lead_id).html('<option value="">Select an Area</option><option>Data Not Found</option>');
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        for (let key in errors) {
                            alert(errors[key].join(', '));
                        }
                    }
                }
            });
            }

        
       function get_area1(city_id) {
           let formData = {
                id: city_id, 
            };
            
            if(city_id == ""){
                $("#Interested_cityNew1").html('<option value="">Select an Area</option><option value="">Data Not Found</option>');
            }else{
                
                 $.ajax({
                url: '{{ route('admin.Green-Drive-Ev.delivery-man.get-area') }}',
                method: 'GET', 
                data: formData, 
                success: function(response) {
                    if (response.status) {
                        $("#Interested_cityNew1").empty();
                        if (response.areas && response.areas.length > 0) {
                            var option = '<option value="">Select an Area</option>'; 
                            response.areas.forEach(function(area) {
                                option += '<option value="' + area.id + '">' + area.Area_name + '</option>';
                            });
                            
                            $("#Interested_cityNew1").html(option);
                        } else {
                            $("#Interested_cityNew1").html('<option value="">Select an Area</option><option value="">Data Not Found</option>');
                        }
                    } else {
                        toastr.error(response.message || 'An error occurred.');
                        $("#Interested_cityNew1").html('<option value="">Select an Area</option><option value="">Data Not Found</option>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    $("#Interested_cityNew1").html('<option value="">Select an Area</option><option value="">Data Not Found</option>');
                }
            });
            }
           
        }

    
            function LeadAddComment() {
                var form = $('#lead-comment-id')[0];
                var formData = new FormData(form);
                var token = '{{csrf_token()}}';
                formData.append('_token',token);
                formData.append('task_id', $('#task_id').val());
                formData.append('user_role', $('#user_role').val());
                formData.append('commenter_id', $('#commenter_id').val());
                if (CKEDITOR.instances['commentUpdate_']) {
                    var comment = CKEDITOR.instances['commentUpdate_'].getData(); 
                    formData.append('comment', comment); 
                } else {
                    formData.append('comment', $('#commentUpdate_').val()); 
                }
                formData.append('existing_comment_id', $('#existing_comment_id').val());
                console.log([...formData.entries()]); 

                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.addComment') }}", 
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false,  
                    success: function (response) {
                        if (response.status == true) {
                            toastr.success(response.message);
                            $('#comment').val('');
                            $('#comment_details').html(response.comment_html);
                        }
                       LeadCommandReset();//reset form
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            for (var field in errors) {
                                toastr.error(errors[field][0]);
                            }
                        } else {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    }
                });
            }

        
    </script>
    <!-- CKEditor Script -->
    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
    <script>
    
        document.querySelectorAll('.js-ck-description').forEach(function (textarea) {
            CKEDITOR.replace(textarea); 
        });
        
        //  document.querySelectorAll('.cke_notifications_area').forEach(el => {
        //      el.style.display = 'none';
        //  });
 
        
        CKEDITOR.on('instanceReady', function (e) {
            const editor = e.editor;
            editor.on('key', function () {
                console.log("Key press detected inside CKEditor!");
                document.querySelectorAll('.cke_notifications_area').forEach(el => {
                    el.style.display = 'none';
                });
            });
        });

        
      
        function OndeleteComment(id, task_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this comment?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Delete',
                confirmButtonColor: '#28a745',  
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.Green-Drive-Ev.leads.deleteComment') }}',
                        type: "POST",
                        data: {
                            id: id,
                            task_id: task_id,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                           if (response.status == true) {
                                toastr.success(response.message); 
                                $(`[data-comment-id="${id}"]`).remove(); 
                            } else {
                                toastr.error(response.message); 
                            }

                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'Please try again later.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
        
    function OnEditComment(id, task_id,comment_id) {
        console.log(comment_id);
        var btn = $(".CommentAddUpdateBtn");
    
        if (btn.length) { 
            btn.text("Update Comment") 
               .removeClass("btn-primary")
               .addClass("btn-success");
               
                // AJAX request to fetch the comment data
                 $.ajax({
                     url: '{{ route('admin.Green-Drive-Ev.leads.getComment') }}',
                     type: "GET",
                     data: {
                         id: id,
                         task_id: task_id,
                     },
                     success: function(response) {
                         if (response.status) {
                            if (CKEDITOR.instances['commentUpdate_']) {
                                CKEDITOR.instances['commentUpdate_'].setData(response.data.comment);
                            } else {
                                console.error("CKEditor instance not found for:", 'commentUpdate_');
                            }
                            document.querySelectorAll('.cke_notifications_area').forEach(el => {
                                el.style.display = 'none';
                            });

                           
                           $(".existing_comment_id_").val(response.data.id);
                            
                         } else {
                             toastr.error(response.message); 
                         }
                     },
                     error: function(xhr) {
                         toastr.error('Please try again'); 
                     }
                 });
               
        } else {
             btn.text("Add Comment") 
               .removeClass("btn-success")
               .addClass("btn-primary");
        }
    }

    function OnTelecallerAssign(lead_id, caller_id) {
            console.log("Task ID:", lead_id);
            console.log("Caller ID:", caller_id);
        
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to assign this telecaller?",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Assign',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.Green-Drive-Ev.leads.assignTelecaller') }}',
                        type: "POST",
                        data: {
                            lead_id: lead_id,
                            caller_id: caller_id,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message); 
                                setTimeout(function() {
                                    window.location.reload(); 
                                }, 2000);
                            } else {
                                toastr.error(response.message); 
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                            Swal.fire(
                                'Error!',
                                'Please try again later.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

    </script>
        
    <script>
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }).replace(',', '');
        }

        function openModal(id) {
           
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.get_popup_data', ['id' => '__ID__']) }}".replace('__ID__', id),
                    type: "GET",
                    success: function(res) {
                        console.log(res);
                         $("#myModal").modal('show');
                        if(res.status == true){
                            
                            var tele_status = res.lead.telecaller_status || ''; //get values
                            var source = res.source_name || '';
                            var assigned = res.lead.assigned || '';
                            var f_name = res.lead.f_name || '';
                            var l_name = res.lead.l_name || '';
                            var phone_number = res.lead.phone_number || '';
                            var current_city = res.lead.current_city || '';
                            var intrested_city = res.lead.intrested_city || '';
                            var vehicle_type = res.lead.vehicle_type || '';
                            var created_at = formatDate(res.lead.created_at) || '';
                            var updated_at = formatDate(res.lead.updated_at) || '';
                            var description = res.lead.description || '';
                            console.log(f_name);
                            
                            $("#id_text").text(res.lead_id);
                            $("#tele_status1").val(tele_status); //set values
                            if(source != ""){
                              $("#Source1").val(source);
                            }
                            if (res.assign_html) {
                                $("#Assigned1").attr('onchange', res.assign_html);
                            } else {
                                $("#Assigned1").removeAttr('onchange'); 
                            }
                            $("#Assigned1").val(assigned);
                            $("#fname1").val(f_name);
                            $("#lname1").val(l_name);
                            $("#mobile1").val(phone_number);
                            $("#current_city1").val(current_city);
                            if(res.area_html != ""){
                              $("#Interested_city1").html(res.area_html);
                            }else{
                                $("#Interested_city1").html('<option value="">Select an Area</option>');
                            }
                            $("#vehicle_type1").val(vehicle_type);
                            $("#created_at1").val(created_at);
                            $("#updated_at1").val(updated_at);
                            $("#description1").val(description);
                            $("#task_id").val(res.lead_id);
                            $("#comment_details").html(res.comments_html);
                            

                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        Swal.fire(
                            'Error!',
                            'Please try again later.',
                            'error'
                        );
                    }
                });
        }
        
        function LeadCommandReset(){
            CKEDITOR.instances['commentUpdate_'].setData('');
            $('#existing_comment_id').val('');
            var btn = $(".CommentAddUpdateBtn");
            btn.text("Add Comment").removeClass("btn-success").addClass("btn-primary");
        }
    
    </script>
        
        
    @section('script_js')
    <script>
        
        $(document).ready(function() {
            var status = '';
            var offset = 1;
            var limit = 3;
            get_lead_auto_data(status,offset,limit);
            
            $(document).on('click', '.lead-more-btn', function() {
                var status = $(this).data('status'); 
                var get_last_id = $(this).data('get_last_id'); 
                var get_tele_assign_id = $(this).data('get_tele_assign_id'); 
                get_lead_append_data(status,get_last_id,get_tele_assign_id,$(this));
            });
    
          $(document).on("dragstart", ".kanban-items", function(event) {
                event.originalEvent.dataTransfer.setData("item_id", $(this).attr("data-item_id"));
                event.originalEvent.dataTransfer.setData("old_status", $(this).closest(".kanban-column").attr("id"));
            });
    
            // Allow drag over columns
            $(document).on("dragover", ".kanban-column", function(event) {
                event.preventDefault(); // Required for drop to work
            });
    
            // Handle drop event
            $(document).on("drop", ".kanban-column", function(event) {
                event.preventDefault();
                var item_id = event.originalEvent.dataTransfer.getData("item_id");
                var old_status = event.originalEvent.dataTransfer.getData("old_status");
                var new_status = $(this).attr("id");
    
                if (new_status !== old_status) {
                    // Move item to the new column
                    $("#" + new_status + " .kanban-cards").append($("#item" + item_id));
                    updateLeadStatus(item_id, new_status, old_status);
                } else {
                    console.log("Dropped in the same column, no update.");
                }
            });
            function updateLeadStatus(item_id, new_status, old_status) {
                console.log("Updating status...");
                console.log("Item ID:", item_id);
                console.log("Old Status:", old_status);
                console.log("New Status:", new_status);
    
                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.update') }}", // Update this with your actual route
                    method: "POST",
                    data: {
                        task_id: item_id,
                        tele_status: new_status
                    },
                    success: function(response) {
                        console.log("Status updated successfully!");
                    },
                    error: function(error) {
                        console.log("Error updating status:", error);
                    }
                });
            }
            
        });
    
    
        function get_lead_auto_data(status, offset, limit, append = false, button = null) {
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.leads.auload_lead_data') }}",
                method: "GET",
                data: { status: status, offset: offset, limit: limit },
                success: function(data) {
                    console.log(data);
                    if (append) {
                        $("#" + status + " .kanban-cards").append(data.html_data);
                        if (data.html_data.trim() === '') {
                            button.remove(); // Remove button if no more data
                        }
                    } else {
                        $("#autoload_lead_data").html(data.html_data);
                    }
                }
            });
        }
    
    
        function get_lead_append_data(status, last_id,tele_id,element) {
            console.log(element);
            $(element).text('🔄 Loading...'); 
        
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.leads.append_lead_data') }}",
                method: "GET",
                data: { status: status, last_id: last_id, tele_id: tele_id },
                success: function(data) {
                    console.log(data);
                    $(element).remove(); 
                    $("#lead-more-"+status).remove();
                    $(".kanban-cards-" + data.status).append(data.html_data);
                },
                error: function() {
                    $(element).text('Load More'); 
                }
            });
        }
    
       function GetTelecallerData(element,value) {
           
           if(value != ""){
                var url = $(element).find(':selected').data('export_url'); 
                if (url) {
                    $("#telecaller_export_url").attr('href', url);
                } else {
                    $("#telecaller_export_url").removeAttr('href'); 
                }
           }else{
               $("#telecaller_export_url").removeAttr('href');
           }
           
           if(value != ""){
              var status = '';
               var offset = 1;
               var limit = 3;
               $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.auload_lead_data') }}",
                    method: "GET",
                    data: { status: status, offset: offset, limit: limit, tele_assign_id : value},
                    success: function(data) {
                        console.log(data);
                        $("#autoload_lead_data").html('');
                        $("#autoload_lead_data").html(data.html_data);
                    }
                }); 
           }
           
        }
        
        function SearchLeadData(value) {
            var search_data = value.trim();
            var input_value = search_data.replace(/[^+\d]/g, '');
            if (input_value.includes('+')) {
                input_value = '+' + input_value.replace(/\+/g, '');
            }
            document.getElementById("search-bar").value = input_value;
            
            var errorBlock = document.getElementById("SearchHelpBlock"); 
            var searchBtn = $(".searchOrcancel");
            var searchIcon = $(".searchOrcancelIcon");
            if (search_data.length > 5) {
               console.log("true");
               errorBlock.innerHTML = "";
               $(".searchOrcancel").addClass('btn-danger ClearSearchData').removeClass('custom-btn');
               $(".searchOrcancelIcon").removeClass('bi-search').addClass('bi-x');
               var status = '';
               var offset = 1;
               var limit = 50;
               $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.search_lead_data') }}",
                    method: "GET",
                    data: { status: status, offset: offset, limit: limit, tele_assign_id : value, search_data: search_data},
                    success: function(data) {
                        console.log(data);
                        $("#autoload_lead_data").html('');
                        $("#autoload_lead_data").html(data.html_data);
                    }
                }); 
                
            }
            else if(search_data.length  == 0){
                errorBlock.innerHTML = ""; 
                $(".searchOrcancel").addClass('custom-btn').removeClass('btn-danger ClearSearchData');
                $(".searchOrcancelIcon").removeClass('bi-x').addClass('bi-search');
                var status = '';
                var offset = 1;
                var limit = 3;
                get_lead_auto_data(status,offset,limit);
            }
            else {
                $(".searchOrcancel").addClass('custom-btn').removeClass('btn-danger ClearSearchData');
                $(".searchOrcancelIcon").removeClass('bi-x').addClass('bi-search');
               errorBlock.innerHTML = `<div class="form-text text-danger">
                    Minimum 6 characters required. Must start with +91.
                </div>`;
            }
        }

        $('.searchOrcancel').on('click', function() {
            var search_data = $("#search-bar").val().trim();
            if (search_data.length > 5) {
                $(".searchOrcancel").addClass('custom-btn').removeClass('btn-danger ClearSearchData');
                $(".searchOrcancelIcon").removeClass('bi-x').addClass('bi-search');
                $("#search-bar").val('');
                var status = '';
                var offset = 1;
                var limit = 3;
                get_lead_auto_data(status,offset,limit);
            }
        });
       

    </script>
    @endsection
        
    </body>
    </html>
</x-app-layout>
