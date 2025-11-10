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
    ->get();
        


@endphp


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
        integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    
        .kanban-view{
            width:100%;
        }

        .kanban-board {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            width: 100%;
        }

        .kanban-column {
            min-width: calc(3.6 * (100% / 12));
            border: 1px solid #ddd;
            margin-left: 20px;
            height: 600px;
            padding: 0;
        }

        .kanban-cards {
            background-color: #f4f4f4;
            height: 555px;
            overflow-y: auto;
        }

        .card-header {
            color: #fff;
        }

        .card-inside {
            color: #a4a1a1;
            padding: 10px;
        }

        .lead-task {
            white-space: nowrap;
            width: 80%;
            overflow: hidden;
            text-overflow: ellipsis;
            
        }

        .task-card {
            background-color: #f4f4f4;
            height: auto;
            overflow-y: auto;
            cursor: move;
        }
        
        
          .task-hidden {
            display: none;
        }
        
        

    .content {
        /* display: flex; */
        /* flex-wrap: wrap; */
        /* margin-top: 60px; */
        /* padding: 0 30px; */
    }

    @media screen and (max-width:1025px){
        .kanban-column {
            min-width: calc(3.2 * (100% / 12));
            min-width: 400px;
        }

        .kanban-board{
            overflow-x:auto;
            width: 700px;
        }
    }


    @media screen and (max-width:769px){
        .kanban-column {
            min-width: calc(3.2 * (100% / 12));
            min-width: 300px;
        }

        .kanban-board{
            overflow-x:auto;
            width: 480px;
        }
    }


    @media screen and (max-width:426px){
        .kanban-column {
            min-width: calc(3.2 * (100% / 12));
            min-width: 300px;
        }

        .kanban-board{
            overflow-x:auto;
            width: 400px;
        }
    }

    @media screen and (max-width:376px){

        .kanban-column {
            min-width: 280px;
        }

        .kanban-board {
            overflow-x: auto;
            width: 350px;
        }
    }

    @media screen and (max-width:321px){

        .kanban-column {
            min-width: 280px;
        }

        .kanban-board{
            overflow-x:auto;
            width: 300px;
        }
    }
/* General Layout */
.comment-item {
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 10px;
  box-shadow: rgba(0, 0, 0, 0.1) 0 5px 15px;
  background-color: #fff;
}

/* Avatar Container */
.comment-item .v-avatar {
  width: 40px;  /* Avatar size */
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
}

/* Avatar Image */
.comment-item .v-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Layout for Avatar, Name, and Date */
.comment-item .d-flex {
  display: flex;
}

.comment-item .align-items-center {
  align-items: center;
}

/* Adjustments for Text */
.comment-item .displayName.title {
  font-size: 16px;
  font-weight:600;
  color: #333;
  margin-right: 10px;
}

.comment-item .displayName.caption {
  font-size: 13px;
  color: #0d6efd !important;
  font-weight:400;
}

.comment-item .mt-3 {
  margin-top: 1rem;
}

.comment-item p {
  line-height: 1.5;
  color: #555;
}
#cke_notifications_area_comment{
    display:none !important;
}
.cke_notifications_area{
  display:none !important;
}
.small-para{
    font-size:13px;
    color:rgb(155 170 191);
}
.small-para1{
    font-size:12px;
    color:blue;
}
.lead-heading{
    color:rgb(83 112 153) !important;
}
.hidden {
    display: none;
}

.show-more-btn {
    background: #f4f4f4;
    border: none;
    color: #007bff;
    cursor: pointer;
    text-align: center;
}

.show-more-btn:hover {
    text-decoration: underline;
}



/* General Toastr styles */
.toast {
    border-radius: 8px;
    padding: 15px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: opacity 0.3s ease;
}

/* Success message styling */
.toast-success {
    background-color: #28a745;
    color: white;
    border-left: 5px solid #218838;
}

/* Error message styling */
.toast-error {
    background-color: #dc3545;
    color: white;
    border-left: 5px solid #c82333;
}

/* Info message styling */
.toast-info {
    background-color: #17a2b8;
    color: white;
    border-left: 5px solid #138496;
}

/* Warning message styling */
.toast-warning {
    background-color: #ffc107;
    color: black;
    border-left: 5px solid #e0a800;
}

/* Toast close button */
.toast .toast-close-button {
    color: white;
    font-size: 18px;
    font-weight: bold;
    opacity: 1;
    transition: opacity 0.3s;
}

/* Make the close button appear on hover */
.toast:hover .toast-close-button {
    opacity: 1;
}

/* Customizing position */
.toast-top-right {
    top: 20px !important;
    right: 20px !important;
}

/* Make the toast message more readable */
.toast-message {
    font-weight: 500;
}

/* Adding spacing and shadow to the toast container */
#toast-container {
    margin-top: 30px !important;
    margin-right: 5px !important;
    z-index: 9999;
}
    </style>
    <body>
            <!--<div class="page-header">-->
                <!-- Page Heading -->
            <!--    <div class="d-flex flex-wrap align-items-center justify-content-between">-->
            <!--        <h2 class="page-header-title mb-3 mr-1">-->
            <!--            <span class="page-header-icon">-->
            <!--                <img src="{{ asset('public/assets/admin/img/role.png') }}" class="w--26" alt="">-->
            <!--            </span>-->
                        <!--<span>-->
                        <!--    {{ 'Leads List' }}-->
                        <!--</span>-->
            <!--        </h2>-->
            <!--    </div>-->
            <!--</div>-->
            
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


                {{-- kanban view --}}
            <div class="kanban-view" id="kanban-view">
                <div class="container-kanban">
                    
                        <div class="row align-items-center">
                            
                                <!-- New Lead Button -->
                                <div class="col-6 col-md-3 mb-3">
                                    <a href="#" class="btn btn-success btn-round w-100" data-bs-toggle="modal" data-bs-target="#staticBackdrop" id="new-button">
                                        <i class="bi bi-plus-lg"></i> New Lead
                                    </a>
                                </div>
                            
                                <!-- Bulk Demo Excel Button -->
                                <div class="col-6 col-md-3 mb-3">
                                    <!--<a href="{{ route('admin.Green-Drive-Ev.leads.excel') }}" class="btn btn-success btn-round w-100" id="new-button">-->
                                    <!--     <i class="bi bi-download"></i> Bulk Demo Excel -->
                                    <!--</a>-->
                                    <button class="btn btn-success btn-round w-100" onclick="createExcelFile()"><i class="bi bi-download"></i> Bulk Demo Excel </button>
                                </div>
                                
                                 <div class="col-6 col-md-3 mb-3">
                                     <form class="form-inline" action="{{ route('admin.Green-Drive-Ev.leads.uploadLeads') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                                    <div class="form-group mt-md-2">
                                        <input type="file" class="form-control mb-2" id="excel_file" name="excel_file" accept=".pdf,.doc,.docx,.txt,.jpg,.png,.csv,.xls,.xlsx">
                                    </div>
                                </div>
                            
                                <!-- File Upload Form -->
                                <div class="col-6 col-md-3 mb-3">
                                     <button type="submit" class="btn btn-success btn-round w-100"> <i class="bi bi-upload"></i> Import Leads </button>
                                     </form>
                                </div>
                            
                        </div>
                        
                        
                        
                                <div class="row mb-0">
                                    <div class="col-6 col-md-2 mb-3">
                                    </div>
                                    
                                    <div class="col-6 col-md-3 mb-3">
                                        <button class="btn btn-success btn-round w-100" id="userButton"><i class="bi bi-download"></i> Telecaller Excel </button>
                                    </div>
                                    
                                    <div class="col-6 col-md-3 mb-3">
                                         <button class="btn btn-success btn-round w-100" id="citybutton"><i class="bi bi-download"></i> City Excel </button>
                                    </div>
                                
                                    <div class="col-6 col-md-4 mb-3">
                                        <input type="input" class="form-control" id="search-bar" placeholder="search Ex: +91**********">
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

                    <div class="row kanban-board mt-3">
                        @foreach ($statuses as $key => $value)
                            <!-- Column for each status -->
                            <div class="col kanban-column card" id="{{ $key }}">
                                <p class="card-header" style="background-color: {{ $colors[$colorIndex] }}">
                                    @if($roles->name == 'Telecaller')
                                    <?php $count = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')->where('telecaller_status', $key)->where('Assigned', auth()->user()->id)->get()->count(); ?>
                                    @else
                                     <?php $count = \Illuminate\Support\Facades\DB::table('ev_tbl_leads')->where('telecaller_status', $key)->get()->count(); ?>
                                    @endif
                                    {{ $value }} - {{$count}} Leads
                                </p>
                                <div class="card-body p-0">
                                    <div class="kanban-cards">
                                        <!-- No leads found -->
                                        @php
                                            $hasLeads = false;
                                            $itemCount = 0;
                                        @endphp
                    
                                        @foreach ($list as $val)
                                            @if ($key == $val->telecaller_status)
                                                @php
                                                    $hasLeads = true;
                                                    $itemCount++;
                    
                                                    // Fetch telecaller details
                                                    $img = \Illuminate\Support\Facades\DB::table('users')->where('id', $val->assigned)->first();
                                                    $image_data_uri = null;
                                                    if ($img && $img->profile_photo_path) {
                                                        $image_data_uri = asset('admin-assets/users/'.$img->profile_photo_path);
                                                    }
                                                    $caller_name = null;
                                                    if ($img && $img->name) {
                                                        $caller_name = $img->name;
                                                    }
                                                    // Fetch city details
                                                    $city = \Illuminate\Support\Facades\DB::table('ev_tbl_city')->where('id', $val->current_city)->first();
                                                    $area_data = \Modules\City\Entities\Area::where('status', 1)->where('city_id',$val->current_city)->get();
                                                    $telecaller_comments = \Illuminate\Support\Facades\DB::table('telecaller_comments')->where('task_id', $val->id)->orderBy('id','desc')->get();
                                                    $source = \Illuminate\Support\Facades\DB::table('ev_tbl_lead_source')->where('id', $val->source)->first();
                                                    $source_name = 'Twitter';
                                                    
                                                    if(isset($source)){
                                                      $source_name = $source->source_name ?? 'Twitter'; 
                                                    }
                                                @endphp
                                                <div class="kanban-items m-1" id="item{{ $val->id }}" data-item_id="{{ $val->id }}" draggable="true">
                                                    
                                                    <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid {{ $colors[$colorIndex] }};" onclick="openModal({{ $val->id }},'current_city')">
                                                        <div class="card-body ">
                                                           <p class="mb-0 small-para fw-medium" style="color:{{ $colors[$colorIndex] }};"><span class="lead-heading">Name : </span>{{ Str::limit(ucfirst($val->f_name) . ' ' . ucfirst($val->l_name), 22, '...') }}</p>
                                                           <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Phone :</span> {{$val->phone_number ?? ''}}</p>
                                                           <p class="mb-0 small-para fw-medium"><span class="lead-heading">Source :</span> {{ucfirst($source_name)}}</p>
                                                           <p class="mb-0 small-para fw-medium"><span class="lead-heading">Last Updated :</span> {{ \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') }}</p>
                                                        </div>
                                                       <div class="card-footer px-3 py-2 d-flex justify-content-between">
                                                            <div>
                                                                @if ($image_data_uri)
                                                                    <img src="{{ $image_data_uri }}" class="d-inline" alt="Telecaller Image" width="25" height="25" style="border-radius: 50%;">
                                                                    <p class="mb-0 small-para fw-medium d-inline">{{$caller_name}}</p>
                                                                @else
                                                                    <p class="mb-0 small-para fw-medium d-inline text-danger"><i class="bi bi-people-fill"></i> Not Assigned</p>
                                                                @endif
                                                                
                                                            </div>
                                                            <p class="mb-0 small-para fw-medium"><i class="bi bi-chat-dots fw-bold text-warning"></i> {{count($telecaller_comments)}}</p>
                                                        </div>

                                                    </div>
                                                </div>
                    
                                                <!-- Modal -->
                                                <div class="modal fade" id="myModal_{{ $val->id }}" tabindex="-1" aria-labelledby="myModalLabel_{{ $val->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myModalLabel_{{ $val->id }}"><span style="color:{{ $colors[$colorIndex] }};">ID No {{$val->id}}</span> - Lead Information</h5>
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
                                                                        id="tele_status">
                                                                        @foreach ($statuses as $value => $label)
                                                                            <option value="{{ $value }}"
                                                                                {{ old('tele_status',$val->telecaller_status) == $value ? 'selected' : '' }}>
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
                                                                        id="Source">
                                                                         @foreach ($leadsource as $data)
                                                                            <option value="{{ $data->id }}"
                                                                                {{ old('Source',$val->source) == $data->id ? 'selected' : '' }}>
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
                                                                        id="Assigned" @if($val->assigned == "") onchange="OnTelecallerAssign('{{ $val->id }}',this.value)" @endif>
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                        if($roles->name == 'Telecaller'){
                                                                            $telecaller = \Illuminate\Support\Facades\DB::table('users')->where('id', auth()->user()->id)->get();
                                                                        }
                                                                        
                                                                        ?>
                                                                        @foreach ($telecaller as $data)
                                                                            <option value="{{ $data->id }}"
                                                                                {{ old('Assigned',$val->assigned) == $data->id ? 'selected' : '' }}>
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
                                                                    <input type="text" class="form-control" id="fname"
                                                                        name="fname" value="{{$val->f_name}}"
                                                                        onkeypress="OnlyStringValidate(event)">
                                                                </div>
                                                            </div>
                    
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="lname" class="form-label">Last Name</label>
                                                                    <input type="text" class="form-control" id="lname"
                                                                        name="lname" onkeypress="OnlyStringValidate(event)"
                                                                        value="{{$val->l_name}}">
                                                                </div>
                                                            </div>
                                                        </div>
                    
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="mobile" class="form-label">Mobile Number</label>
                                                                    <input type="text" class="form-control" id="mobile"
                                                                        name="mobile" oninput="sanitizeAndValidatePhone(this)"
                                                                        value="{{$val->phone_number}}">
                                                                </div>
                                                            </div>
                    
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="current_city" class="form-label">Current city</label>
                                                                    <select class="form-control basic-single"
                                                                        aria-label="Default select example" name="current_city"
                                                                        id="current_city_{{$val->id}}" onchange="get_area('{{$val->id}}',this.value)">
                                                                        <option value="">Select a City</option>
                                                                         @foreach ($City as $data)
                                                                            <option value="{{ $data->id }}"
                                                                                {{ $val->current_city == $data->id ? 'selected' : '' }}>
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
                                                                    <select class="form-control basic-single selected_area_lists" id="Interested_city_{{$val->id}}" name="Interested_city">
                                                                        <option value="">Select an Area</option>
                                                                         @foreach($area_data as $data)
                                                                            <option value="{{ $data->id }}"
                                                                                {{ $val->intrested_city == $data->id ? 'selected' : '' }}>
                                                                                {{ $data->Area_name }}
                                                                            </option>
                                                                         @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                    
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                                                    <select class="form-control basic-single" 
                                                                            aria-label="Default select example" 
                                                                            name="vehicle_type" 
                                                                            id="vehicle_type">
                                                                        <option value="">Select a Type</option>
                                                                        <option value="1" {{ $val->vehicle_type == 1 ? 'selected' : '' }}>2 wheeler</option>
                                                                        <option value="2" {{ $val->vehicle_type == 2 ? 'selected' : '' }}>3 wheeler</option>
                                                                        <option value="3" {{ $val->vehicle_type == 3 ? 'selected' : '' }}>4 wheeler</option>
                                                                        <option value="4" {{ $val->vehicle_type == 4 ? 'selected' : '' }}>Rental</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                    
                                                        </div>
                    
                                                        <div class="row">
                                                            <!--<div class="col-6">-->
                                                            <!--    <div class="mb-3">-->
                                                            <!--        <label for="Lead_Source" class="form-label">Lead Source</label>-->
                                                            <!--        <select class="form-control basic-single"-->
                                                            <!--            aria-label="Default select example" name="Lead_Source"-->
                                                            <!--            id="Lead_Source">-->
                                                            <!--             @foreach ($leadsource as $data)-->
                                                            <!--                <option value="{{ $data->id }}"-->
                                                            <!--                    {{ old('Lead_Source',$val->lead_sources) == $data->id ? 'selected' : '' }}>-->
                                                            <!--                    {{ $data->source_name }}-->
                                                            <!--                </option>-->
                                                            <!--             @endforeach-->
                                                            <!--        </select>-->
                                                            <!--    </div>-->
                                                            <!--</div>-->
                    
                                                            <!--<div class="col-6">-->
                                                            <!--    <div class="mb-3">-->
                                                            <!--        <label for="Register_Date" class="form-label">Register Date & Time</label>-->
                                                            <!--        <input type="datetime-local" class="form-control" id="Register_Date" -->
                                                            <!--            name="Register_Date" -->
                                                            <!--            value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">-->
                                                            <!--    </div>-->
                                                            <!--</div>-->
                                                            <!--<div class="col-6">-->
                                                            <!--    <div class="mb-3">-->
                                                            <!--        <label for="Status" class="form-label">Status</label>-->
                                                            <!--        <select class="form-control basic-single"-->
                                                            <!--            aria-label="Default select example" name="Status"-->
                                                            <!--            id="Status">-->
                                                            <!--            <option value="1" {{ $val->active_status == 1 ? 'selected' : '' }}>Active</option>-->
                                                            <!--            <option value="0" {{ $val->active_status == 0 ? 'selected' : '' }}>Inactive</option>-->
                                                            <!--        </select>-->
                                                            <!--    </div>-->
                                                            <!--</div>-->
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="task" class="form-label">Created at :</label>
                                                                    <input type="text" class="form-control" id="task"
                                                                        name="task" value="{{ \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="mb-3">
                                                                    <label for="task" class="form-label">Updated at :</label>
                                                                    <input type="text" class="form-control" id="task"
                                                                        name="task" value="{{ \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                    
                                                        <div class="row">
                                                        
                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label for="description" class="mb-3">Description</label>
                                                                    <textarea class="form-control" placeholder="Write a Descriptions" id="description" name="description" rows="4">{{$val->description}}</textarea>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                    
                                                       
                                                        <form  id="lead-comment-id" method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="mb-1">
                                                                        <label for="comment" class="mb-2 text-primary">Comments</label>
                                                                        <textarea class="form-control js-ck-description commentUpdate_{{$val->id}}" placeholder="Add Comments" id="commentUpdate_{{$val->id}}" name="comment" required></textarea>
                                                                        <input type="hidden" name="task_id" id="task_id" value="{{$val->id}}">
                                                                        <input type="hidden" name="user_role" id="user_role" value="{{auth()->user()->name}}">
                                                                        <input type="hidden" name="commenter_id" id="commenter_id" value="{{auth()->user()->name}}">
                                                                        <input type="hidden" name="existing_comment_id" id="existing_comment_id" class="existing_comment_id_{{$val->id}}" value="">
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                        
                                                            <div class="modal-footer border-0 m-0 p-0">
                                                                <!--<button type="button" class="btn btn-secondary"-->
                                                                <!--    data-bs-dismiss="modal">Cancel</button>-->
                                                                <button type="submit" class="btn btn-primary btn-round CommentAddUpdateBtn">Add Comment</button>
                                                            </div>
                                                         </form>
                                                       <div class="col-12 mt-1">
                                                            <div class="mb-3" id="comment_details" >
                                                                @forelse($telecaller_comments as $comments)
                                                                    <div class="comment-item mb-3 p-3 rounded shadow-sm bg-white" style="cursor: pointer;" data-comment-id="{{ $comments->id }}" data-task-id="{{ $comments->task_id }}">
                                                                        <!-- Avatar, Name, and Date in a Row -->
                                                                        <div class="d-flex align-items-center justify-content-between">
                                                                            <!-- Left Side (Avatar and Name) -->
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="v-avatar avatar mr-3">
                                                                                    <img src="{{ asset('admin-assets/img/comment_icon.png') }}" alt="Admin" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">
                                                                                </div>
                                                                                <div>
                                                                                    <p class="small-para1 px-2 mb-0">Commented on: {{ \Carbon\Carbon::parse($comments->created_at)->format('d M, Y H:i') }}</p>
                                                                                    <strong class="text-primary displayName title px-2">{{ $comments->commenter_id }}</strong>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Right Side (Date) -->
                                                                            <small class="text-muted displayName caption">
                                                                              <i class="bi bi-pencil-square text-primary fw-bold me-1" style="cursor: pointer;" onclick="OnEditComment('{{$comments->id}}','{{$comments->task_id}}','{{$val->id}}')"></i>
                                                                              <i class="bi bi-trash text-danger fw-bold" style="cursor: pointer;" onclick="return OndeleteComment('{{$comments->id}}','{{$comments->task_id}}')"></i>
                                                                            </small>
                                                                        </div>
                                                                        <!-- Comment Content Section -->
                                                                        <div class="mt-3">
                                                                            <p>{!! $comments->comment !!}</p>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <p>No comments yet.</p>
                                                                @endforelse
                                                            </div>
                                                        </div>
                    
                    
                                                    </div>
                                                </div>
                                           
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                    
                                        @if (!$hasLeads)
                                            <div class="text-center mt-5 card-inside" id="no-lead-{{ $key }}">
                                                <h4><i class="bi bi-opencollective"></i></h4>
                                                <h4>No Leads Found</h4>
                                            </div>
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                            @php
                                // Increment color index and cycle through the colors
                                $colorIndex = ($colorIndex + 1) % count($colors);
                            @endphp
                        @endforeach
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
                                                    id="current_city1" onchange="get_area1(this.value)">
                                                    <option value="">Select a City</option>
                                                     @foreach ($City as $data)
                                                        <option value="{{ $data->id }}"
                                                            {{ old('current_city') == $data->id ? 'selected' : '' }}>
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
                                        <!--<div class="col-6">-->
                                        <!--    <div class="mb-3">-->
                                        <!--        <label for="Lead_Source" class="form-label">Lead Source</label>-->
                                        <!--        <select class="form-control basic-single"-->
                                        <!--            aria-label="Default select example" name="Lead_Source"-->
                                        <!--            id="Lead_Source">-->
                                        <!--             @foreach ($leadsource as $data)-->
                                        <!--                <option value="{{ $data->id }}"-->
                                        <!--                    {{ old('Lead_Source') == $data->id ? 'selected' : '' }}>-->
                                        <!--                    {{ $data->source_name }}-->
                                        <!--                </option>-->
                                        <!--             @endforeach-->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->

                                  
                                        <!-- <div class="col-6">-->
                                        <!--    <div class="mb-3">-->
                                        <!--        <label for="Status" class="form-label">Status</label>-->
                                        <!--        <select class="form-control basic-single"-->
                                        <!--            aria-label="Default select example" name="Status"-->
                                        <!--            id="Status">-->
                                        <!--            <option value="1" selected>Active</option>-->
                                        <!--            <option value="0">Inactive</option>-->
                                        <!--        </select>-->
                                        <!--    </div>-->
                                        <!--</div>-->
                                        <div class="col-6">
                                            <input type="hidden" class="form-control" id="Register_Date" 
                                                    name="Register_Date" 
                                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                            <!--<div class="mb-3">-->
                                            <!--    <label for="task" class="form-label">Task Name</label>-->
                                            <!--    <input type="text" class="form-control" id="task"-->
                                            <!--        name="task" value="">-->
                                            <!--</div>-->
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
         <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>-->
    <script>
    
        // search filter
     document.getElementById('search-bar').addEventListener('input', function () {
                const searchValue = this.value.trim();
                const cards = document.querySelectorAll('.task-body');
    
                cards.forEach(card => {
                    const phoneNumber = card.querySelector('.phone-number').textContent;
                    if (phoneNumber.includes(searchValue)) {
                        card.classList.remove('task-hidden');
                    } else {
                        card.classList.add('task-hidden');
                    }
                });
            });
    
    
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
    
    
    
        // Paste the createExcelFile function here
        function createExcelFile() {
            // Include the column names
            const columnNames = [
                "Telecaller_id",
                "Rider_First_Name",
                "Rider_Last_Name",
                "Mobile_Number",
                "Current_City_id",
                "Interested_City_id",
                "Vehicle_Type",
                "Description"
            ];

            // Add example data (replace this with your dynamic data)
            const exampleData = [
                ["2", "Alex", "Smith", "+919876543210", "2", "5", "2", "Looking for a new vehicle"],
            ];

            // Combine column names and example data
            const data = [columnNames, ...exampleData];

            // Create a worksheet from the data
            const worksheet = XLSX.utils.aoa_to_sheet(data);
            worksheet['!cols'] = [
                { wpx: 100 }, // Telecaller_id (100px width)
                { wpx: 150 }, // Rider_First_Name (150px width)
                { wpx: 150 }, // Rider_Last_Name (150px width)
                { wpx: 120 }, // Mobile_Number (120px width)
                { wpx: 120 }, // Current_City_id (120px width)
                { wpx: 120 }, // Interested_City_id (120px width)
                { wpx: 80 },  // Vehicle_Type (80px width)
                { wpx: 200 }, // Description (200px width)
            ];

            // Create a new workbook and append the worksheet
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

            // Generate and trigger download for the Excel file
            XLSX.writeFile(workbook, "Telecaller_Data.xlsx");
        }
    </script>
        <script>
            // Kanban Item Dragging and Dropping
            const items = document.querySelectorAll('.kanban-items');
            const columns = document.querySelectorAll('.kanban-column');

            // Add drag event listeners to each kanban item
            items.forEach(item => {
                item.addEventListener('dragstart', dragStart);
                item.addEventListener('dragend', dragEnd);
            });

            // Add dragover and drop event listeners to each column
            columns.forEach(column => {
                column.addEventListener('dragover', dragOver);
                column.addEventListener('drop', drop);
            });

            // Function to handle drag start event
            function dragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.id); // Store the item's ID
                setTimeout(() => {
                    e.target.classList.add('hide'); // Hide the item while dragging
                }, 0);
            }

            // Function to handle drag end (make the item visible again)
            function dragEnd(e) {
                e.target.classList.remove('hide');
            }

            // Allow dropping by preventing default behavior
            function dragOver(e) {
                e.preventDefault();
            }

            // Handle the drop event
            function drop(e) {
                e.preventDefault();
                const itemId = e.dataTransfer.getData('text'); // Get the dragged item's ID
                const draggedElement = document.getElementById(itemId); // Find the dragged item
                const itemID = draggedElement.getAttribute('data-item_id');
                const dropZone = e.target.closest('.kanban-column');
                const parentID = dropZone.id;
                dropZone.querySelector('.kanban-cards').appendChild(draggedElement); // Append item to the new column
                updateNoLeadsMessage();
                id_send(itemID, parentID); // Send item id and column id
            }

            // Function to update "No Leads Found" message visibility
            function updateNoLeadsMessage() {
                columns.forEach(column => {
                    const noLeadMsg = column.querySelector('.card-inside');
                    const cards = column.querySelectorAll('.kanban-items');

                    // Check if noLeadMsg exists before modifying its style
                    if (noLeadMsg) {
                        // Show "No Leads Found" if no cards, hide otherwise
                        noLeadMsg.style.display = cards.length === 0 ? 'block' : 'none';
                    }
                });
            }

            // Initial check on page load
            updateNoLeadsMessage();

            // Function to send item and column ID via AJAX
            function id_send(itemID, parentID) {
                var data = {
                    task_id: itemID, // Store task ID (item ID)
                    tele_status: parentID // Store column ID (parent ID)
                };

                $.ajax({
                    url: "{{ route('admin.Green-Drive-Ev.leads.update') }}",
                    type: 'GET',
                    data: data,
                    success: function(res) {
                        console.log(res);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }

            // Validate name to accept only letters
            function OnlyStringValidate(event) {
                var regex = new RegExp("^[a-zA-Z]+$");
                var key = String.fromCharCode(event.which || event.keyCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            }

            // Sanitize and validate mobile number with country code +91
            function sanitizeAndValidatePhone(input) {
                // Ensure the input starts with '+91'
                if (!input.value.startsWith('+91')) {
                    input.value = '+91' + input.value.replace(/^\+?91/, ''); // Keep "+91" at the beginning
                }

                // Allow only digits after '+91'
                input.value = input.value.replace(/[^\d+]/g, ''); // Remove any non-digit, non-plus characters

                // Limit the length to 13 characters (including '+91')
                if (input.value.length > 13) {
                    input.value = input.value.substring(0, 13);
                }
            }

            // Initialize DataTable with custom settings
            document.addEventListener('DOMContentLoaded', function () {
               

                // Set minimum date for Register_Date input
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

            
           function openModal(itemId,id_name) {
                // Construct the modal ID dynamically based on the item ID
                const modalId = `#myModal_${itemId}`;
     
            
                // Select the modal and display it using Bootstrap's modal function
                const modalElement = document.querySelector(modalId);
            
                if (modalElement) {
                    const bootstrapModal = new bootstrap.Modal(modalElement);
                    bootstrapModal.show();
                } else {
                    console.error(`Modal with ID ${modalId} not found.`);
                }
            }
            
            document.addEventListener("DOMContentLoaded", function () {
                $('#lead-comment-id').on('submit', function (e) {
                    e.preventDefault(); // Prevent default form submission
            
                    // Serialize the form data
                    var formData = $(this).serialize();
            
                    $.ajax({
                        url: "{{ route('admin.Green-Drive-Ev.leads.addComment') }}", // Use the form's action attribute for the URL
                        type: 'POST',                // POST method
                        data: formData,              // Serialized form data
                        success: function (response) {
                            // Handle the successful response
                            if (response.message) {
                                // Show a success message (e.g., using Toastr)
                                toastr.success(response.message);
            
                                // Optionally, clear the comment textarea
                                $('#comment').val('');
            
                                // Add the new comment to the comment_details div
                                $('#comment_details').prepend(`
                                    <div class="border p-2 mb-2">
                                        <p class="mb-0"><strong>Role:</strong> ${response.data.user_role}</p>
                                        <p class="mb-0"><strong>Comment:</strong> ${response.data.comment}</p>
                                        <p class="text-muted mb-0"><small><strong>By:</strong> ${response.data.commenter_name} | <strong>At:</strong> ${response.data.created_at}</small></p>
                                    </div>
                                `);
                            }
                        },
                        error: function (xhr) {
                            // Handle validation errors or other errors
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
                });
            });
            
            // document.addEventListener('DOMContentLoaded', function () {
            //     const showMoreButtons = document.querySelectorAll('.show-more-btn');
            
            //     showMoreButtons.forEach(button => {
            //         button.addEventListener('click', function () {
            //             const status = button.getAttribute('data-status');
            //             const cards = document.querySelectorAll(`.kanban-cards[data-status="${status}"] .hidden`);
            
            //             // Show all hidden items
            //             cards.forEach(card => {
            //                 card.classList.remove('hidden');
            //             });
            
            //             // Hide the button after showing all items
            //             button.style.display = 'none';
            //         });
            //     });
            // });



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
                                if (CKEDITOR.instances['commentUpdate_' + comment_id]) {
                                    CKEDITOR.instances['commentUpdate_' + comment_id].setData(response.data.comment);
                                } else {
                                    console.error("CKEditor instance not found for:", 'commentUpdate_' + comment_id);
                                }
                                document.querySelectorAll('.cke_notifications_area').forEach(el => {
                                    el.style.display = 'none';
                                });
    
                               
                               $(".existing_comment_id_"+comment_id).val(response.data.id);
                                
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
    </body>
    </html>
</x-app-layout>
