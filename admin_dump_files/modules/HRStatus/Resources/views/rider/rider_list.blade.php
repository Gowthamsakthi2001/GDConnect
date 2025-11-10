<x-app-layout>

    <style>
    .form-check-input:checked {
        background-color: #0f62fe !important;
        border-color: #0f62fe !important;
    }
    table thead th{
        background: white !important;
        color: #4b5563 !important;
    }
     .custom-dropdown-toggle::after {
        display: none !important;
      }

    .form-check-input[type="checkbox"] {
        width: 2.3rem;
        height: 1.2rem;
    }

    </style>


    <div class="main-content">
        {{-- <h1>Hello</h1> --}}
        <div class="card bg-transparent my-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-title h5 custom-dark m-0">
                                Riders
                            <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$riders->count()}}</span>
                        </div>

                    </div>

                    <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                        <div class="d-flex align-items-center gap-2">

                            <!-- Export Button -->
                            <div onclick="SelectExportFields()"
                                class="bg-white border border-gray px-3 d-flex align-items-center"
                                style="height: 42px; cursor: pointer;">
                                <i class="bi bi-download fs-6 me-2"></i> Export
                            </div>

                            <!-- Filter Button -->
                            <div onclick="RightSideFilerOpen()"
                                class="bg-white border border-gray px-3 d-flex align-items-center"
                                style="height: 42px; cursor: pointer;">
                                <i class="bi bi-filter fs-6 me-2"></i> Filter
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="table-responsive">
            <table class="table custom-table text-center" style="width: 100%;">
                <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                    <tr>
                        <th scope="col" class="custom-dark">
                            <div class="form-check">
                                <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value=""
                                    id="CSelectAllBtn">
                                <label class="form-check-label" for="CSelectAllBtn"></label>
                            </div>
                        </th>
                        <th scope="col" class="custom-dark">Rider ID</th>
                        <th scope="col" class="custom-dark">Rider Name</th>
                        <th scope="col" class="custom-dark">Email ID</th>
                        <th scope="col" class="custom-dark">Contact</th>
                        <th scope="col" class="custom-dark">Location</th>
                        <!--<th scope="col" class="custom-dark">Role Type</th>-->
                        <th scope="col" class="custom-dark">Joined Date</th>
                        <th scope="col" class="custom-dark">Status</th>
                        <th scope="col" class="custom-dark">Active/InActive</th>

                        <th scope="col" class="custom-dark">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white border border-white">
                    @if(isset($riders))
                    @foreach($riders as $employee)
                    
                                 <?php
                                    $status = $employee->rider_status;
                                    $colorClass = match ($employee->rider_status) {
                                        1 => 'text-success',
                                        0 => 'text-danger',
                                        default => 'text-secondary',
                                    };
                                     
                                    $statusName = $employee->rider_status == 1 ? 'Active' : 'Inactive';
                                ?>
                                
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;"
                                    type="checkbox" value="{{ $employee->id }}" id="empCheck{{ $employee->id }}">
                            </div>
                        </td>
                        <td>{{ $employee->emp_id ?? 'N/A' }}</td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name ?? '' }}</td>
                        <td>{{ $employee->email ?? 'N/A' }}</td>
                        <td>{{ $employee->mobile_number ?? 'N/A' }}</td>
                        <td>
                            {{ $employee->current_city->city_name ?? 'N/A' }}
                        </td>
                        <td>
                            @if($employee->register_date_time)
                            {{ \Carbon\Carbon::parse($employee->register_date_time)->format('d M Y') }}
                            @else
                            N/A
                            @endif
                        </td>
                           <td>
                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <i class="bi bi-circle-fill {{ $colorClass }}"></i>
                                            <span class="text-capitalize">{{ $statusName }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center m-0 p-0">
                                            <input class="form-check-input toggle-status" data-id="{{ $employee->id }}" type="checkbox" role="switch" id="toggleSwitch{{ $loop->index }}" {{$status == 1 ? 'checked' : ''}}>
                                        </div>
                                    </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                                    <li>
                                        <a href="{{ route('admin.Green-Drive-Ev.employee_categories.rider_view', $employee->id) }}"
                                            class="dropdown-item d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye me-2 fs-5"></i> View
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>

            </table>
        </div>
    </div>

    <div class="modal fade" id="export_select_fields_modal" tabindex="-1"
        aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form>
                <div class="modal-content rounded-4">
                    <div class="modal-header border-0 d-flex justify-content-between">
                        <div>
                            <h1 class="h3 fs-5 text-center custom-dark" id="export_select_fields_modalLabel">Select
                                Fields</h1>
                        </div>
                        <div>
                            <button class="btn text-white" style="background:#26c360;">Download</button>
                        </div>
                    </div>
                    <div class="modal-body p-md-3">
                        <div class="row p-4">
                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field1">Select All</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field2">First Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field2">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field3">Last Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field3">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field4">Email ID</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field4">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field5">Gender</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field5">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field6">Contact No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field6">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field7">House No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field7">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field8">Street Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field8">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field9">City</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field9">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field10">Area</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field10">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field11">Pincode</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field11">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field12">Alternative No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field12">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field13">Role</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field13">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field14">Account Holder Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field14">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field15">Bank Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field15">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field16">IFSC Code</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field16">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field16">Bank Account No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field16">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field17">DOB</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field17">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field18">Present Address</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field18">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field19">Premanent Address</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field19">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field20">Rider ID</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field20">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field21">Past Experience</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field21">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field22">Father/ Mother/
                                        Guardian</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field22">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field23">Father/ Mother/ Guardian
                                        Contact No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field23">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field24">Reference Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field24">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field25">Reference Contact No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field25">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field26">Rerence Relationship</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field26">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field27">Spouse Name</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field27">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field28">Spouse Contact No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field28">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field29">Blood Group</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field29">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field30">Social Media Link</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field30">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field31">Rider Type</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field31">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field32">Vehicle Type</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field32">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field33">Aadhaar No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field33">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field34">Aadhaar Front</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field34">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field35">Aadhaar Back</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field35">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field36">Pan No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field36">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field37">Pan Card</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field37">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field38">Driving license No</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field38">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field39">Driving license Front</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field39">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field40">Driving license Back</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field40">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field40">Bank Details</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field40">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-check-label mb-0" for="field40">Profile Photo</label>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="field40">
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01"
        aria-labelledby="offcanvasRightHR01Label">
        <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">HR Level 02 Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

            {{-- <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
            </div> --}}

            <div class="card mb-3">
                <div class="card-header p-2">
                    <div>
                        <h6 class="custom-dark">Select Role Type</h6>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="roleTypebtn" id="roleType1" checked>
                      <label class="form-check-label" for="roleType1">
                        All
                      </label>
                    </div> --}}
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
                    <div>
                        <h6 class="custom-dark">Select Time Line</h6>
                    </div>
                </div>
                <div class="card-body">

                    <div class="form-check mb-3">
                        <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                            id="timeLine1">
                        <label class="form-check-label" for="timeLine1">
                            This day
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                            id="timeLine2">
                        <label class="form-check-label" for="timeLine2">
                            This Week
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                            id="timeLine3">
                        <label class="form-check-label" for="timeLine3">
                            This Month
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input select_time_line" type="radio" name="STtimeLine"
                            id="timeLine4">
                        <label class="form-check-label" for="timeLine4">
                            This Year
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header p-2">
                    <div>
                        <h6 class="custom-dark">Date Between</h6>
                    </div>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control"
                            max="{{ date('Y-m-d') }}" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control"
                            max="{{ date('Y-m-d') }}" value="">
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50">Clear All</button>
                <button class="btn btn-success w-50">Apply</button>
            </div>

        </div>
    </div>


    @section('script_js')
        <script>
            $(document).ready(function() {
                $('#CSelectAllBtn').on('change', function() {
                    $('.sr_checkbox').prop('checked', this.checked);
                });

                $('.sr_checkbox').on('change', function() {
                    if (!this.checked) {
                        $('#CSelectAllBtn').prop('checked', false);
                    } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
                        $('#CSelectAllBtn').prop('checked', true);
                    }
                });
            });
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAll = document.getElementById('field1');
                const checkboxes = document.querySelectorAll('.form-check-input');

                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => {
                        if (checkbox !== selectAll) {
                            checkbox.checked = selectAll.checked;
                        }
                    });
                });

                // Optional: Update "Select All" if any individual checkbox is unchecked
                checkboxes.forEach(checkbox => {
                    if (checkbox !== selectAll) {
                        checkbox.addEventListener('change', function() {
                            const allChecked = Array.from(checkboxes)
                                .filter(cb => cb !== selectAll)
                                .every(cb => cb.checked);
                            selectAll.checked = allChecked;
                        });
                    }
                });
            });
        </script>

        <script>
            function SelectExportFields() {
                $("#export_select_fields_modal").modal('show');
            }

            function RightSideFilerOpen() {
                const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
                bsOffcanvas.show();
            }

            function RollTypeFiler(value) {
                var url = new URL(window.location.href);
                url.searchParams.set('roll_type', value);
                window.location.href = url.toString();
            }



        </script>
    @endsection
</x-app-layout>
