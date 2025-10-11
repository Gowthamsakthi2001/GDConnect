<x-app-layout>

    <style>
        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            color: #fff;
            background-color: #ffffff;
            box-shadow: none !important;
        }

        .nav-pills .nav-link.active .head-text {
            color: #0000009c !important;
        }

        /*.custom-card-body {*/
        /*    height: 500px;*/
        /*    overflow-y: auto;*/
        /*}*/

        .action-btn {
            width: 100%;
            height: 48px;
            font-weight: 500;
            display: flex;
            font-size: 14px;
            align-items: center;
            justify-content: center;
            gap: 5px;
            cursor: pointer;
            transition: 0.3s;
            border: 1px solid #ccc;
        }


        input[type="radio"]:not(:checked)+.action-btn {
            background-color: #fff;
            color: #000;
        }


        input[type="radio"]#approve_dynamic:checked+label[data-type="approve"] {
            background-color: #28a745;
            color: white !important;
            border: none;
        }

        input[type="radio"]#bgv:checked+label[data-type="bgv"] {
            background-color: #ffc107;
            color: white !important;
            border: none;
        }

        input[type="radio"]#hold:checked+label[data-type="hold"] {
            background-color: #17a2b8;
            color: white !important;
            border: none;
        }

        input[type="radio"]#rejected:checked+label[data-type="rejected"] {
            background-color: #dc3545;
            color: white !important;
            border: none;
        }

        input[type="radio"]#ride:checked+label[data-type="ride"] {
            background-color: rgb(25, 1, 25);
            color: white !important;
            border: none;
        }
    </style>

    <div class="main-content">



        <div class="card my-4">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <!-- Profile Info -->
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <div>
                    <?php
                    $image = $application->photo ? asset('public/EV/images/photos/' . $application->photo) : asset('public/admin-assets/img/person.png');
                    
                    $roll_type = '';
                    if ($application->work_type == 'deliveryman') {
                        $roll_type = 'Rider';
                    } elseif ($application->work_type == 'in-house') {
                        $roll_type = 'Employee';
                    } elseif ($application->work_type == 'adhoc') {
                        $roll_type = 'Adhoc';
                    } elseif ($application->work_type == 'helper') {
                        $roll_type = 'Helper';
                    } else {
                        $roll_type = '-';
                    }
                    ?>
                    <img src="{{ asset('admin-assets/icons/custom/profile_icon.png') }}" alt="Profile"
                        width="70" height="70" style="border-radius:50%;">
                </div>
                    <div class="px-3">
                    <div class="h5 fw-bold mb-1">{{ $application->first_name ?? '' }} {{ $application->last_name ?? '' }}</div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 flex-wrap">
                            <li class="breadcrumb-item text-nowrap">
                                <img src="{{ asset('admin-assets/icons/custom/profile_icon.png') }}"
                                    alt="Profile" class="me-1" style="width:16px;">
                                Application ID: {{ $application->reg_application_id ?? '' }}
                            </li>
                            <li class="breadcrumb-item text-nowrap">
                                <i class="bi bi-person fw-bold me-1"></i> Role: {{ $roll_type }}
                            </li>
                            <li class="breadcrumb-item text-nowrap">
                                <i class="bi bi-person fw-bold me-1"></i> Verified By: 
                                @if($application->hrleveltwo_assign->assigned_dep == 'hr_level_one') 
                                     HR Level 01
                                @endif
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
                                        @php
                                $previousUrl = request()->headers->get('referer');
                                $type = 'total_application'; // default fallback
                            
                                if ($previousUrl) {
                                    $segments = explode('/', trim(parse_url($previousUrl, PHP_URL_PATH), '/'));
                                    $last = end($segments);
                                    if (in_array($last, ['total_application', 'pending', 'sent_to_bgv', 'sent_to_hr1' ,'approved_employee' ,'approved_rider' ,'reject_by_hr2'])) {
                                        $type = $last;
                                    }
                                }
                            @endphp

            <!-- Buttons - Centered -->
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button class="btn btn-primary edit-candidate-btn" onclick="edit_candidate()">
                    <i class="bi bi-pencil-square me-2"></i> Edit Candidate
                </button>
                <a href="{{ route('admin.Green-Drive-Ev.hr_level_two.app_list', ['type' => $type]) }}" class="btn btn-dark edit-candidate-btn">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
                <button type="submit" form="UpdateForm" class="btn btn-success update-candidate d-none" onclick="update_candidate()">
                    <i class="bi bi-floppy me-2"></i>Save Changes
                </button>
                <button class="btn border-gray update-candidate d-none" onclick="update_candidate()">
                    <i class="bi bi-x me-2"></i> Cancel
                </button>
            </div>
        </div>
        
    </div>
        </div>

        <div class="card my-3">

            {{-- <div class="card-header" style="background:#f1f5f9;">
                <ul class="nav nav-pills row d-flex align-items-center" id="pills-tab" role="tablist">
                  <li class="nav-item col-md-3" role="presentation">
                    <button class="nav-link active" id="pills-basic-information-tab" data-bs-toggle="pill" data-bs-target="#pills-basic-information" type="button" role="tab" aria-controls="pills-basic-information" aria-selected="true"><img src="{{asset('public/admin-assets/icons/custom/person.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">Basic Information</span></button>
                  </li>
                  <li class="nav-item col-md-3" role="presentation">
                    <button class="nav-link" id="pills-kyc-doc-tab" data-bs-toggle="pill" data-bs-target="#pills-kyc-doc" type="button" role="tab" aria-controls="pills-kyc-doc" aria-selected="false"><img src="{{asset('public/admin-assets/icons/custom/kyc_doc.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">KYC Docuements</span></button>
                  </li>
                  <li class="nav-item col-md-3 " role="presentation">
                    <button class="nav-link" id="pills-query-comments-tab" data-bs-toggle="pill" data-bs-target="#pills-query-comments" type="button" role="tab" aria-controls="pills-query-comments" aria-selected="false"><img src="{{asset('public/admin-assets/icons/custom/query.png')}}" alt="image">&nbsp; <span class="head-text" style="color:#adb3bb;">Query</span></button>
                  </li>
                 
                </ul>
           </div> --}}

            <div class="card-header" style="background:#f1f5f9;">
                <ul class="nav nav-pills row d-flex align-items-center" id="pills-tab" role="tablist">
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link active" id="pills-basic-information-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-basic-information" type="button" role="tab"
                            aria-controls="pills-basic-information" aria-selected="true">
                            <img src="{{ asset('admin-assets/icons/custom/person.png') }}" alt="image">&nbsp;
                            <span class="head-text" style="color:#adb3bb;">Basic Information</span>
                        </button>
                    </li>
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link" id="pills-kyc-doc-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-kyc-doc" type="button" role="tab" aria-controls="pills-kyc-doc"
                            aria-selected="false">
                            <img src="{{ asset('admin-assets/icons/custom/kyc_doc.png') }}" alt="image">&nbsp;
                            <span class="head-text" style="color:#adb3bb;">KYC Documents</span>
                        </button>
                    </li>
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link" id="pills-query-comments-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-query-comments" type="button" role="tab"
                            aria-controls="pills-query-comments" aria-selected="false">
                            <img src="{{ asset('admin-assets/icons/custom/query.png') }}" alt="image">&nbsp;
                            <span class="head-text" style="color:#adb3bb;">Query</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body" style="background:#fbfbfb;">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-basic-information" role="tabpanel"
                        aria-labelledby="pills-basic-information-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header" style="background:#eef2ff;">
                                <h5 style="color:#1e3a8a;" class="fw-bold">Basic Information</h5>
                                <p class="mb-0" style="color:#1e3a8a;">basic information of your Application details
                                </p>
                            </div>
                            <div class="card-body custom-card-body">
                                   <form id="UpdateForm" method="POST" action="{{ route('admin.Green-Drive-Ev.hr_level_two.update_data') }}">
                                       @csrf
                                    <div class="row">
                                    <div class="col-12 text-center my-3">
                                        <img src="{{ $image }}" alt="Profile" class="img-fluid mb-2"
                                            width="80" height="80" style="border-radius: 50%;">
                                        <p class="text-muted">Profile Picture</p>
                                    </div>
                                    <input type="hidden" name="id" value="{{$application->id}}"> 
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="mobile_no">{{ 'Contact No' }}</label>
                                            <input type="tel" class="form-control bg-white" name="mobile_number" oninput="sanitizeAndValidatePhone(this)"
                                                id="mobile_no" 
                                                value="{{ $application->mobile_number ?? '' }}" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="f_name">{{ 'First Name' }}</label>
                                            <input type="text" class="form-control bg-white" name="first_name"
                                                id="f_name" value="{{ $application->first_name ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="l_name">{{ 'Last Name' }}</label>
                                            <input type="text" class="form-control bg-white" name="last_name"
                                                id="l_name" value="{{ $application->last_name ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="email">{{ 'Email' }}</label>
                                            <input type="text" class="form-control bg-white" name="email"
                                                id="email" value="{{ $application->email ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="gender">{{ 'Gender' }}</label>
                                            <!--<input type="text" class="form-control bg-white" name="gender"-->
                                            <!--    id="gender" value="{{ ucfirst($application->gender) ?? '' }}"-->
                                            <!--    readonly>-->
                                          <select class="form-control bg-white"  name="gender"
                                                id="gender">
                                            <option value="male" {{ $application->gender === 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ $application->gender === 'female' ? 'selected' : '' }}>Female</option>
                                        </select>

                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="house_no">{{ 'House No' }}</label>
                                            <input type="text" class="form-control bg-white" name="house_no"
                                                id="house_no" value="{{ $application->house_no ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="street_name">{{ 'Street Name' }}</label>
                                            <input type="text" class="form-control bg-white" name="street_name" style="padding:12px 20px;"
                                                id="street_name" value="{{ $application->street_name ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="current_city_id">City</label>
                                            <select class="form-control basic-single custom-select2-field bg-white" id="current_city_id" onchange="GetAreas(this.value)"
                                                name="current_city_id" onchange="get_area('current_city_id')"
                                                >
                                                @if ($cities)
                                                    @foreach ($cities as $data)
                                                        <option value="{{ $data->id }}"
                                                            {{ old('current_city_id', $application->current_city_id) == $data->id ? 'selected' : '' }}>
                                                            {{ $data->city_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="interested_city_id">Area</label>
                                            <select class="form-control basic-single custom-select2-field bg-white" id="interested_city_id" 
                                                name="interested_city_id" >
                                                @if ($areas)
                                                    @foreach ($areas as $data)
                                                        <option value="{{ $data->id }}"
                                                            {{ old('interested_city_id', $application->interested_city_id) == $data->id ? 'selected' : '' }}>
                                                            {{ $data->Area_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="pincode">{{ 'Pincode' }}</label>
                                            <input type="text" class="form-control bg-white" name="pincode" style="padding:12px 20px;"  oninput="sanitizeAndValidatePincode(this)"
                                                id="pincode" value="{{ $application->pincode ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="alternative_number">{{ 'Alternative Number' }}</label>
                                            <input type="text" class="form-control bg-white"
                                                name="alternative_number" id="alternative_number"
                                                value="{{ $application->alternative_number ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="role">Role</label>
                                            <select class="form-control basic-single bg-white" id="role"
                                                name="role" >
                                                <option value="deliveryman"
                                                    {{ $application->work_type == 'deliveryman' ? 'selected' : '' }}>
                                                    Rider</option>
                                                <option value="in-house"
                                                    {{ $application->work_type == 'in-house' ? 'selected' : '' }}>
                                                    Employee</option>
                                                <option value="adhoc"
                                                    {{ $application->work_type == 'adhoc' ? 'selected' : '' }}>Adhoc
                                                </option>
                                                <option value="helper"
                                                    {{ $application->work_type == 'helper' ? 'selected' : '' }}>Helper
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="ac_holder_name">{{ 'Account Holder Name' }}</label>
                                            <input type="text" class="form-control bg-white" name="ac_holder_name"
                                                id="ac_holder_name"
                                                value="{{ $application->account_holder_name ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="bank_name">{{ 'Bank Name' }}</label>
                                            <input type="text" class="form-control bg-white" name="bank_name"
                                                id="bank_name" value="{{ $application->bank_name ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="ifsc_code">{{ 'IFSC Code' }}</label>
                                            <input type="text" class="form-control bg-white" name="ifsc_code"
                                                id="ifsc_code" value="{{ $application->ifsc_code ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="bank_ac_no">{{ 'Bank Account No' }}</label>
                                            <input type="text" class="form-control bg-white" name="bank_ac_no"
                                                id="bank_ac_no" value="{{ $application->account_number ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="d_o_b">{{ 'DOB' }}</label>
                                            <input type="date" class="form-control bg-white" name="date_of_birth"
                                                id="d_o_b" value="{{ $application->date_of_birth?->toDateString() ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="present_address">{{ 'Present Address' }}</label>
                                            <input type="text" class="form-control bg-white"
                                                name="present_address" id="present_address"
                                                value="{{ $application->present_address ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="permanent_address">Permanent
                                                Address</label>
                                            <input type="text" class="form-control bg-white"
                                                name="permanent_address" id="permanent_address"
                                                value="{{ $application->permanent_address ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    @if ($application->work_type != 'in-house' && $application->work_type != '')
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="prev_rider_id">Pervious
                                                    Rider ID</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="prev_rider_id" id="prev_rider_id"
                                                    value="{{ $application->emp_prev_company_id ?? '' }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1"
                                                    for="prev_company_experience">Past Experience</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="prev_company_experience" id="prev_company_experience"
                                                    value="{{ $application->emp_prev_experience ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="guardian_name">Father/
                                                Guardian Name</label>
                                            <input type="text" class="form-control bg-white" name="guardian_name"
                                                id="guardian_name" value="{{ $application->father_name ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="guardian_phone">Father/
                                                Guardian Contact No</label>
                                            <input type="text" class="form-control bg-white" name="guardian_phone" oninput="sanitizeAndValidatePhone(this)"
                                                id="guardian_phone"
                                                value="{{ $application->father_mobile_number ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="reference_name">Reference
                                                Name</label>
                                            <input type="text" class="form-control bg-white" name="reference_name"
                                                id="reference_name"
                                                value="{{ $application->referal_person_name ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="reference_mobile">Reference
                                                Contact No</label>
                                            <input type="text" class="form-control bg-white"
                                                name="reference_mobile" id="reference_mobile"
                                                value="{{ $application->referal_person_number ?? '' }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1"
                                                for="reference_relationship">Reference Relationship</label>
                                            <input type="text" class="form-control bg-white"
                                                name="reference_relationship" id="reference_relationship"
                                                value="{{ $application->referal_person_relationship ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="spouse_name">Spouse Name</label>
                                            <input type="text" class="form-control bg-white" name="spouse_name"
                                                id="spouse_name" value="{{ $application->spouse_name ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="spouse_mobile">Spouse Contact
                                                No</label>
                                            <input type="text" class="form-control bg-white" name="spouse_mobile"
                                                id="spouse_mobile"
                                                value="{{ $application->spouse_mobile_number ?? '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="blood_group">Blood Group</label>
                                            <input type="text" class="form-control bg-white" name="blood_group" oninput="sanitizeAndValidateBlood(this)"
                                                id="blood_group" value="{{ $application->blood_group ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="social_links">Social
                                                Link</label>
                                            <input type="text" class="form-control bg-white" name="social_links"
                                                id="social_links" value="{{ $application->social_links ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <?php
                                    $work_type = $application->work_type ?? '';
                                    ?>
                                    @if ($work_type != 'in-house')
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="rider_type">Rider
                                                    Type</label>
                                                <select class="form-control basic-single custom-select2-field bg-white" id="rider_type"
                                                    name="rider_type">
                                                    @if ($rider_types)
                                                        @foreach ($rider_types as $data)
                                                            <option value="{{ $data->id }}"
                                                                {{ $application->rider_type == $data->id ? 'selected' : '' }}>
                                                                {{ $data->type }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                        $vehicleTypes = ['2W', '3W', '4W', '8W', 'Rental'];
                                        ?>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="rider_type">Vehicle
                                                    Type</label>
                                                <select class="form-control basic-single custom-select2-field bg-white" id="vehicle_type"
                                                    name="vehicle_type" >
                                                    @if ($vehicleTypes)
                                                        @foreach ($vehicleTypes as $type)
                                                            <option value="{{ $type }}"
                                                                {{ old('vehicle_type', $application->vehicle_type) == $type ? 'selected' : '' }}>
                                                                {{ $type }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- KYC Documents-->
                    <div class="tab-pane fade" id="pills-kyc-doc" role="tabpanel"
                        aria-labelledby="pills-kyc-doc-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center" style="background:#edfcff;">
                                <div>
                                    <h5 style="color:#1b4d5e;" class="fw-bold">KYC Documents</h5>
                                    <p class="mb-0" style="color:#1b4d5e;">KYC Documents submitted on Application form</p>
                                </div>
                            
                                    <a href="javascript:void(0);"
                                     onclick="downloadAllFiles()"
                                       class="btn"
                                       style="background: white; color: black; border: 1px solid #ccc;">
                                        Download All
                                    </a>
                            </div>

                            <?php

                            $user = \App\Models\User::find($application->who_aadhar_verify_id);
                            $verify_name = $user->name ?? '';
                            $verify_role = '';
                        
                            if ($user && $user->role) {
                                $verify_by = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user->role)->first();
                                $verify_role = $verify_by->name ?? '';
                            }
                            $front = isset($application->aadhar_card_front) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_front) : asset('public/EV/images/dummy.jpg');
                            $back = isset($application->aadhar_card_back) ? asset('public/EV/images/aadhar/' . $application->aadhar_card_back) : asset('public/EV/images/dummy.jpg');
                            ?>

                            <div class="card-body custom-card-body">
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Aadhar Card Front</div>
                                                <div class="card-title text-muted mb-3">   Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $verify_name ? $verify_name . ' (' . $verify_role . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                      {{ !empty($application->aadhar_verify_date) ? date('d M Y h:i:s A', strtotime($application->aadhar_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $front }}')">
                                                    <img id="" src="{{ $front }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $front }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>

                                <?php 

                                ?>
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Aadhar Card Back</div>
                                                <div class="card-title text-muted mb-3"> Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $verify_name ? $verify_name . ' (' . $verify_role . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{ !empty($application->aadhar_verify_date) ? date('d M Y h:i:s A', strtotime($application->aadhar_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $back }}')">
                                                    <img id="" src="{{ $back }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $back }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>

                                <?php
                                 
                           
                                                        $user1 = \App\Models\User::find($application->who_pan_verify_id);
                        $verify_name1 = $user1->name ?? '';
                        $verify_role1 = '';
                    
                        if ($user1 && $user1->role) {
                            $verify_by1 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user1->role)->first();
                            $verify_role1 = $verify_by1->name ?? '';
                        }
                                $pan_image = isset($application->pan_card_front) ? asset('public/EV/images/pan/' . $application->pan_card_front) : asset('public/EV/images/dummy.jpg');
                                ?>

                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">PAN Card</div>
                                                <div class="card-title text-muted mb-3"> Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                       {{ $verify_name1 ? $verify_name1 . ' (' . $verify_role1 . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{ !empty($application->pan_verify_date) ? date('d M Y h:i:s A', strtotime($application->pan_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $pan_image }}')">
                                                    <img id="" src="{{ $pan_image }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $pan_image }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>

                                <?php

                          $user3 = \App\Models\User::find($application->who_license_verify_id);
                        $verify_name3 = $user3->name ?? '';
                        $verify_role3 = '';
                    
                        if (!empty($user3?->role)) {
                            $verify_by3 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user3->role)->first();
                            $verify_role3 = $verify_by3->name ?? '';
                        }
                        
                            
                                $front1 = isset($application->driving_license_front) ? asset('public/EV/images/driving_license/' . $application->driving_license_front) : asset('public/EV/images/dummy.jpg');
                                $back1 = isset($application->driving_license_back) ? asset('public/EV/images/driving_license/' . $application->driving_license_back) : asset('public/EV/images/dummy.jpg');
                                $llr_image = isset($application->llr_image) ? asset('public/EV/images/llr/' . $application->llr_image) : asset('public/EV/images/dummy.jpg');
                            
                                // Determine role type
                                $role_type = match($application->work_type) {
                                    'deliveryman' => 'Rider',
                                    'in-house' => 'Employee',
                                    'adhoc' => 'Adhoc',
                                    'helper' => 'Helper',
                                    default => '-'
                                };
                            ?>
                            
                            @if($role_type !== 'Employee')
                             @if(!is_null($application->driving_license_front) && !is_null($application->driving_license_back))
                                 
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Driving License Front</div>
                                                <div class="card-title text-muted mb-3">Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                             {{ $verify_name3 ? $verify_name3 . ' (' . $verify_role3 . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                    {{ !empty($application->lisence_verify_date) ? date('d M Y h:i:s A', strtotime($application->lisence_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $front1 }}')">
                                                    <img id="" src="{{ $front1 }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $front1 }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                               


                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Driving License Back</div>
                                                <div class="card-title text-muted mb-3">    Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                           {{ $verify_name3 ? $verify_name3 . ' (' . $verify_role3 . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                       {{ !empty($application->lisence_verify_date) ? date('d M Y h:i:s A', strtotime($application->lisence_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $back1 }}')">
                                                    <img id="" src="{{ $back1 }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $back1 }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                                
                                @endif
                                
                                
                                @if(is_null($application->driving_license_front) && is_null($application->driving_license_back))
                                     <?php 
                                        $user4 = \App\Models\User::find($application->who_llr_verify_id);
                                        $verify_name4 = $user4->name ?? '';
                                        $verify_role4 = '';
                                    
                                        if (!empty($user4?->role)) {
                                            $verify_by4 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user3->role)->first();
                                            $verify_role4 = $verify_by4->name ?? '';
                                        }
                                    ?>
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">LLR</div>
                                                <div class="card-title text-muted mb-3">    Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                     {{ $verify_name4 ? $verify_name4 . ' (' . $verify_role4 . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{ !empty($application->llr_verify_date) ? date('d M Y h:i:s A', strtotime($application->llr_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $llr_image }}')">
                                                    <img id="" src="{{ $llr_image }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $llr_image }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                                
                                @endif
                                
                                 @endif

                                <?php
                                   
                         $user2 = \App\Models\User::find($application->who_bank_verify_id);
                        $verify_name2 = $user2->name ?? '';
                        $verify_role2 = '';
                    
                        if (!empty($user2?->role)) {
                            $verify_by2 = \Illuminate\Support\Facades\DB::table('roles')->where('id', $user2->role)->first();
                            $verify_role2 = $verify_by2->name ?? '';
                        }
                                
                                $bank_image = isset($application->bank_passbook) ? asset('public/EV/images/bank_passbook/' . $application->bank_passbook) : asset('public/EV/images/dummy.jpg');
                                ?>
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Bank Details</div>
                                                <div class="card-title text-muted mb-3">  Uploaded at {{ $application->created_at->format('d M Y, h:i:s A') ?? 'N/A' }}</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                          {{ $verify_name2 ? $verify_name2 . ' (' . $verify_role2 . ')' : '' }}
                                                    </span>
                                                    </h6>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{ !empty($application->bank_verify_date) ? date('d M Y h:i:s A', strtotime($application->bank_verify_date)) : '' }}
                                                    </span>
                                                    </h6>
                                            </div>

                                        
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="image-container"
                                                    onclick="OpenImageModal('{{ $bank_image }}')">
                                                    <img id="" src="{{ $bank_image }}"
                                                        class="preview-image img-fluid" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3 mt-md-0">
                                                <h6 class="my-3"> Bank Holder Name:&nbsp; <span
                                                        class="text-secondary">{{ $application->account_holder_name ?? '' }}</span>
                                                </h6>
                                                <h6 class="mb-3"> Bank Name: &nbsp;<span
                                                        class="text-secondary">{{ $application->bank_name ?? '' }}</span>
                                                </h6>
                                                <h6 class="mb-3"> IFSC Code:&nbsp; <span
                                                        class="text-secondary">{{ $application->ifsc_code ?? '' }}</span>
                                                </h6>
                                                <h6 class="mb-3"> Account Number:&nbsp; <span
                                                        class="text-secondary">{{ $application->account_number ?? '' }}</span>
                                                </h6>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ $bank_image }}')"
                                                    class="btn btn-md border-gray w-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-eye fs-5 me-2"></i> View
                                                </button>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Queries Tab-->
                    <div class="tab-pane fade" id="pills-query-comments" role="tabpanel"
                        aria-labelledby="pills-query-comments-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header" style="background:#ffeded;">
                                <h5 style="color:#5e1b1b;" class="fw-bold">Query</h5>
                                <p class="mb-0" style="color:#5e1b1b;">BGV Query sent by HR Team and BGV Team</p>
                            </div>
                            <div class="card-body custom-card-body">
                                <div class="row">
                                    <div class="col-12 my-4 text-center">
                                        <h5 class="fw-bold">Application ID :
                                            {{ $application->reg_application_id ?? '-' }}</h5>
                                        <p class="mb-0">Candidate Name : {{ $application->first_name ?? '' }}
                                            {{ $application->last_name ?? '' }}</p>
                                    </div>
                                    @if(isset($queries))
                                    @foreach($queries as $q)
                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-end">
                                                    <label class="input-label">{{ 'Comment' }}</label>
                                                </div>
                                                <div>
                                                    <small class="input-label">
                                                            @if($q->comment_type == 'hr_level_one')
                                                                Sent by HR 01
                                                            @elseif($q->comment_type == 'hr_level_two')
                                                                Sent by HR 02
                                                            @endif
                                                    </small><br>
                                                    <small class="input-label">{{ $q->created_at->format('d M Y h:i A') }}</small>
                                                </div>
                                            </div>
                                            <textarea class="form-control mb-3" rows="3" readonly>{{$q->remarks}}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                                    @else
                                    <div class="col-12">
                                        <div class="alert alert-warning mb-0">
                                            No comments found.
                                        </div>
                                    </div>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reupload Document -->
                    <div class="tab-pane fade" id="pills-edit-doc" role="tabpanel"
                        aria-labelledby="pills-edit-doc-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header" style="background:#edffee;">
                                <h5 style="color:#305e1b;" class="fw-bold">Reupload Document</h5>
                                <p class="mb-0" style="color:#305e1b;">Reupload Doc sent by Candidate</p>
                            </div>
                            <div class="card-body custom-card-body">
                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Aadhar Card Front</div>
                                                <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control"
                                                        name="aahaar_front_img" id="aahaar_front_img"
                                                        onchange="show_imagefunction(this, '#aahaar_front_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="">
                                                    <img id="aahaar_front_view" src="{{ $back }}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Aadhar Card Back</div>
                                                <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control"
                                                        name="aahaar_front_back" id="aahaar_front_back"
                                                        onchange="show_imagefunction(this, '#aahaar_back_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="">
                                                    <img id="aahaar_back_view" src="{{ $back }}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">PAN Card</div>
                                                <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="pan_card_img"
                                                        id="pan_card_img"
                                                        onchange="show_imagefunction(this, '#pan_card_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="">
                                                    <img id="pan_card_view" src="{{ $back }}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Driving License Front</div>
                                                <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="dl_front_img"
                                                        id="dl_front_img"
                                                        onchange="show_imagefunction(this, '#dl_front_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="">
                                                    <img id="dl_front_view" src="{{ $back }}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                </div>

                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Driving License Back</div>
                                                <div class="card-title text-muted">Uploaded at 24 ar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="dl_back_img"
                                                        id="dl_back_img"
                                                        onchange="show_imagefunction(this, '#dl_back_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="">
                                                    <img id="dl_back_view" src="{{ $back }}"
                                                        class="preview-image img-fluid border-gray" alt="Image"
                                                        style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="card rounded mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Bank Details</div>
                                                <div class="card-title text-muted">Uploaded at 24 Mar 2025, 12:30:00 AM
                                                </div>
                                            </div>

                                            <div class="col text-end">
                                                <button class="btn btn-success px-4">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">

                                            <div class="col-12 pt-1 pb-1 mb-3">
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="dl_back_img"
                                                        id="dl_back_img"
                                                        onchange="show_imagefunction(this, '#dl_back_view');"
                                                        placeholder="Select avatar image"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif">
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12 mt-3 mt-md-0 d-flex justify-content-start">
                                                <img id="dl_back_view" src="{{ $back }}"
                                                    class="preview-image img-fluid border-gray" alt="Image"
                                                    style="width: 270px; height: 180px; object-fit: cover; border-radius: 10px;">
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group mb-3">
                                                    <label class="input-label mb-2 ms-1"
                                                        for="updateBank_holder_name">Bank Holder Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="bank_holder_name"
                                                        class="form-control mb-3" id="updateBank_holder_name">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="input-label mb-2 ms-1"
                                                        for="updateBank_holder_name">Bank Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="bank_holder_name"
                                                        class="form-control mb-3" id="updateBank_holder_name">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="input-label mb-2 ms-1"
                                                        for="updateBank_holder_name">IFSC Code <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="bank_holder_name"
                                                        class="form-control mb-3" id="updateBank_holder_name">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="input-label mb-2 ms-1"
                                                        for="updateBank_holder_name">Account Number <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="bank_holder_name"
                                                        class="form-control mb-3" id="updateBank_holder_name">
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

     <div class="card">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold">Action Panel</h5>
    </div>
    <div class="card-body">
        @if(($application->hrleveltwo_assign && $application->hrleveltwo_assign->current_status != 'approved'))
        <!-- Show action buttons when:
             - Status exists and isn't approved OR
             - Status is null (hrleveltwo_assign doesn't exist) -->
        
        <!-- Remark Section -->
        <div class="mb-3 d-none" id="RemarkSection">
            <div class="d-flex align-items-center justify-content-between p-3 mb-3">
                <label class="input-label" for="remarks_input">Remarks <span class="text-danger fw-bold">*</span></label>
                <button class="btn btn-md btn-success border-gray" id="remarkSubmitBtn">Submit</button>
            </div>
            <div class="form-group p-2">
                <textarea class="form-control" rows="5" id="remarks_input" required></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row d-flex justify-content-evenly">
            @php
            $roll_type = '-';
            $status_value = '';
        
            switch ($application->work_type) {
                case 'deliveryman':
                    $roll_type = 'Rider';
                    $status_value = 'approve_rider';
                    break;
                case 'in-house':
                    $roll_type = 'Employee';
                    $status_value = 'approve_employee';
                    break;
                case 'adhoc':
                    $roll_type = 'Adhoc';
                    $status_value = 'approve_adhoc';
                    break;
                case 'helper':
                    $roll_type = 'Helper';
                    $status_value = 'approve_helper';
                    break;
            }
            @endphp
            
            <!-- Approve Button -->
               <!-- Approve Button -->
            <div class="col-md-2 col-6 mb-3">
                <input type="radio" name="status" id="approve_dynamic" class="d-none" />
                <label for="approve_dynamic"
                    onclick="hideRemarkSection(); SubmitCandidateStatusWithSwal('{{ $status_value }}')"
                    class="btn btn-md border-gray action-btn"
                    data-type="approve">
                    <i class="bi bi-check2-circle"></i> Approve - {{ $roll_type }}
                </label>
            </div>

            <!-- Send Back to HR 01 Button -->
            <div class="col-md-2 col-6 mb-3">
                <input type="radio" name="status" id="hold" class="d-none"/>
                <label for="hold" onclick="showRemarkSection('sent_back_to_hr1')"
                    class="btn btn-md border-gray action-btn" data-type="hold">
                    <i class="bi bi-clock"></i> Send Back to HR01
                </label>
            </div>

            <!-- Send Back to BGV Button -->
            <div class="col-md-2 col-6 mb-3">
                <input type="radio" name="status" id="bgv" class="d-none"/>
                <label for="bgv" 
                    class="btn btn-md border-gray action-btn" data-type="bgv">
                    <i class="bi bi-x-circle"></i> Send Back to BGV
                </label>
            </div>

            <!-- Rejected Button -->
            <div class="col-md-2 col-6 mb-3">
                <input type="radio" name="status" id="rejected" class="d-none"/>
                <label for="rejected" onclick="showRemarkSection('rejected')"
                    class="btn btn-md border-gray action-btn" data-type="rejected">
                    <i class="bi bi-x-circle"></i> Rejected
                </label>
            </div>
        </div>

        @endif

        <!-- Previous Remarks Section (always visible) -->
        <div class="row p-3 rounded" style="background:#eaeaea;">
            <p class="mb-3 text-start fw-medium">Previous Remarks :</p>
            
         @if($logs->isNotEmpty())
            <div style="max-height: 300px; overflow-y: auto;"> 
                @foreach($logs as $log)
                    <div class="col-12 border-gray p-3 mb-3">
                        <p class="d-flex justify-content-between align-items-center mb-0" style="color:#00000080;">
                            <span>{{ $log->remarks }}</span>
                            <small class="fw-normal">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y h:i A') }}
                            </small>
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted text-center fst-italic">No previous remarks found.</p>
        @endif
            
            
        </div>
    </div>
</div>

<!-- Image View Modal (unchanged) -->
<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 d-flex justify-content-end gap-1">
                <button class="btn btn-sm btn-dark" onclick="zoomIn()">
                    <i class="bi bi-zoom-in"></i>
                </button>
                <button class="btn btn-sm btn-dark" onclick="zoomOut()">
                    <i class="bi bi-zoom-out"></i>
                </button>
                <button class="btn btn-sm btn-dark" onclick="rotateImage()">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
                <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body text-center py-6" style="overflow: auto; max-height: 80vh;">
                <img src="" id="kyc_image" style="max-width: 100%; transition: transform 0.3s ease;">
            </div>
        </div>
    </div>
</div>
        </div>

        @section('script_js')
            <script>
                function edit_candidate() {
                    $(".edit-candidate-btn").each(function() {
                        $(this).addClass("d-none").removeClass("d-block");
                    });

                    $(".update-candidate").each(function() {
                        $(this).addClass("d-block").removeClass("d-none");
                    });

                    $("input").attr("readonly", false);
                }

                function update_candidate() {
                    $(".update-candidate").each(function() {
                        $(this).addClass("d-none").removeClass("d-block");
                    });

                    $(".edit-candidate-btn").each(function() {
                        $(this).addClass("d-block").removeClass("d-none");
                    });

                    $("input").attr("readonly", true);
                }
            </script>
            <script>
                $(document).ready(function() {
                    // Initial state: Show Edit Candidate, hide Save and Cancel
                    $('.update-candidate').addClass('d-none');
                    $('.edit-candidate').removeClass('d-none');


                });
            </script>


            <script>
                function show_imagefunction(input, src) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $(src).attr("src", e.target.result);
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                // Also, ensure your UpdateCandidateStatus function is ready
                function UpdateCandidateStatus(type) {
                    if (type == "on_hold" || type == "rejected" || type == "bgv") {
                        $("#RemarkSection").addClass("d-block").removeClass("d-none");
                        $("#remark_type").val(type);
                    } else {
                        $("#RemarkSection").addClass("d-none").removeClass("d-block");
                        $("#remark_type").val('');
                    }
                }

        function SubmitCandidateStatusWithSwal(type) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to change candidate status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                allowOutsideClick: false,
                preConfirm: () => {
                    const remarks = $("#remarks_input").val()?.trim();
        
                    // For these types, remarks are required
                    const requiresRemarks = ["rejected", "sent_back_to_hr1"];

                    if (requiresRemarks.includes(type) && !remarks) {
                        Swal.showValidationMessage("Remarks are required for this status");
                        return false;
                    }
        
        
                    return { remarks };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let remarks = result.value.remarks ?? '';
                    let data = {
                        _token: "{{ csrf_token() }}",
                        status: type,
                        id: {{ $application->id }},
                        remarks: remarks
                    };
        
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait while we update the status.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
        
                    $.ajax({
                        url: "{{ route('admin.Green-Drive-Ev.hr_level_two.candidate') }}",
                        method: "POST",
                        data: data,
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Success', res.message || 'Status updated successfully!', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', res.message || 'Failed to update status.', 'error');
                            }
                        },
                        error: function (err) {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        }





               $("#UpdateForm").submit(function (e) {
            e.preventDefault();
        
            Swal.fire({
                title: 'Saving Changes',
                html: 'Please wait while we update details',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: response.message
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let messages = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join('<br>');
        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: messages
                        });
                    } else {
                        let errorMsg = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                }
            });
        });
        
        
                function sanitizeAndValidatePhone(input) {
            // Ensure the input starts with '+91' and lock the first 3 characters to '+91'
            if (!input.value.startsWith('+91')) {
                input.value = '+91' + input.value.replace(/^\+?91/, ''); // Ensure it starts with "+91"
            }
    
            // Allow only digits after '+91'
            input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, ''); 
    
            // Limit the total length to 13 characters (including '+91')
            if (input.value.length > 13) {
                input.value = input.value.substring(0, 13);
            }
    }
    
             function sanitizeAndValidatePincode(input) {
    // Remove all non-digit characters
    input.value = input.value.replace(/[^\d]/g, '');

    // Limit to 10 digits
    if (input.value.length > 10) {
        input.value = input.value.substring(0, 10);
    }
}

      function sanitizeAndValidateBlood(input) {
    // Limit input to max 3 characters of any type
    if (input.value.length > 3) {
        input.value = input.value.substring(0, 3);
    }
}


            </script>

            <script>
                // $(document).ready(function() {

                //     // 1. Select the KYC tab link by its ID.
                //     $('#pills-kyc-doc-tab').on('click', function() {

                //         // 2. Find the 'Send Back to HR 01' radio button (which has the id="hold").
                //         const holdRadioButton = $('#hold');

                //         // 3. Set its 'checked' property to true.
                //         holdRadioButton.prop('checked', true);

                //         // 4. IMPORTANT: Trigger the 'change' event to run your onchange function.
                //         // This makes the 'Remark Section' appear and changes the button color.
                //         holdRadioButton.trigger('change');
                //     });

                // });
            </script>
            
            
<script>
    
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
        
        
function GetAreas(cityId) {
    
        if (!cityId) {
        return;
    }
    
    
    const areaSelect = document.getElementById('interested_city_id');
    const selectedAreaId = "{{ $application->interested_city_id ?? '' }}"; // default area

    areaSelect.innerHTML = '<option value="">Loading...</option>';

    fetch("{{ route('admin.Green-Drive-Ev.hr_level_two.get_areas') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": '{{ csrf_token() }}',
        },
        body: JSON.stringify({ city_id: cityId }),
    })
    .then(response => response.json())
    .then(data => {
        areaSelect.innerHTML = '<option value="">Select Area</option>';
        data.forEach(area => {
            const option = document.createElement("option");
            option.value = area.id;
            option.text = area.Area_name;

            // ✅ Mark as selected if matches the saved interested city
            if (area.id == selectedAreaId) {
                option.selected = true;
            }

            areaSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error("Error fetching areas:", error);
        areaSelect.innerHTML = '<option value="">Error loading areas</option>';
    });
}



//functionality for image view 

 function OpenImageModal(img_url) {
    $("#kyc_image").attr("src", ""); // Clear image first
    $("#BKYC_Verify_view_modal").modal('show'); // Corrected selector
    $("#kyc_image").attr("src", img_url); // Load new image
}

let scale = 1;
let rotation = 0;

function OpenImageModal(img_url) {
    scale = 1;
    rotation = 0;
    updateImageTransform();
    $("#kyc_image").attr("src", img_url);
    $("#BKYC_Verify_view_modal").modal('show');
}

function zoomIn() {
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}




</script>

@php
    $files = [];

    $firstName = preg_replace('/\s+/', '_', strtolower($application->first_name)); // clean and format first name

    if ($application->aadhar_card_front) {
        $files[] = [
            'url' => asset('public/EV/images/aadhar/' . $application->aadhar_card_front),
            'name' => "aadhar_front_{$firstName}." . pathinfo($application->aadhar_card_front, PATHINFO_EXTENSION),
        ];
    }

    if ($application->aadhar_card_back) {
        $files[] = [
            'url' => asset('public/EV/images/aadhar/' . $application->aadhar_card_back),
            'name' => "aadhar_back_{$firstName}." . pathinfo($application->aadhar_card_back, PATHINFO_EXTENSION),
        ];
    }

    if ($application->pan_card_front) {
        $files[] = [
            'url' => asset('public/EV/images/pan/' . $application->pan_card_front),
            'name' => "pan_front_{$firstName}." . pathinfo($application->pan_card_front, PATHINFO_EXTENSION),
        ];
    }

    if ($application->driving_license_front) {
        $files[] = [
            'url' => asset('public/EV/images/driving_license/' . $application->driving_license_front),
            'name' => "dl_front_{$firstName}." . pathinfo($application->driving_license_front, PATHINFO_EXTENSION),
        ];
    }

    if ($application->driving_license_back) {
        $files[] = [
            'url' => asset('public/EV/images/driving_license/' . $application->driving_license_back),
            'name' => "dl_back_{$firstName}." . pathinfo($application->driving_license_back, PATHINFO_EXTENSION),
        ];
    }

    if ($application->llr_image) {
        $files[] = [
            'url' => asset('public/EV/images/llr/' . $application->llr_image),
            'name' => "llr_{$firstName}." . pathinfo($application->llr_image, PATHINFO_EXTENSION),
        ];
    }

    if ($application->bank_passbook) {
        $files[] = [
            'url' => asset('public/EV/images/bank_passbook/' . $application->bank_passbook),
            'name' => "bank_passbook_{$firstName}." . pathinfo($application->bank_passbook, PATHINFO_EXTENSION),
        ];
    }

    // Remove any dummy files (if dummy logic used)
    $files = array_filter($files, function ($file) {
        return strpos($file['url'], 'dummy.jpg') === false;
    });
@endphp





    <script>
        function downloadAllFiles() {
            const files = @json($files);


        if (!files || files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No documents found',
                text: 'There are no downloadable files available.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        
            files.forEach((file, index) => {
                setTimeout(() => {
                    const a = document.createElement('a');
                    a.href = file.url;
                    a.download = file.name;
                    a.target = '_blank';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }, index * 300); // delay to prevent browser blocking multiple downloads
            });
        }
    </script>
    
    
   <!-- Add this script at the bottom of your existing scripts -->
<script>
    // Show remark section for actions that need it
    function showRemarkSection(action) {
        currentAction = action;
        $("#RemarkSection").removeClass("d-none").addClass("d-block");
        $("#remarkSubmitBtn").attr("onclick", `submitWithRemarks('${action}')`);
    }

    // Hide remark section
    function hideRemarkSection() {
        $("#RemarkSection").addClass("d-none").removeClass("d-block");
        currentAction = '';
    }

    // Handle submission with remarks
    function submitWithRemarks(action) {
        const remarks = $("#remarks_input").val().trim();
        
        if (!remarks) {
            Swal.fire({
                icon: 'error',
                title: 'Remarks Required',
                text: 'Please enter remarks before submitting',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        SubmitCandidateStatusWithSwal(action);
    }

    // // Your existing submission function
    // function SubmitCandidateStatusWithSwal(type) {
    //     const requiresRemarks = type === 'rejected' || type === 'sent_back_to_hr1';
    //     const remarks = requiresRemarks ? $("#remarks_input").val()?.trim() : '';
        
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: `Do you really want to ${type.replace(/_/g, ' ')} this candidate?`,
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: 'Confirm',
    //         cancelButtonText: 'Cancel',
    //         preConfirm: () => {
    //             if (requiresRemarks && !remarks) {
    //                 Swal.showValidationMessage('Remarks are required');
    //                 return false;
    //             }
    //             return { remarks: remarks };
    //         }
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax({
    //                 url: "{{ route('admin.Green-Drive-Ev.hr_level_two.candidate') }}",
    //                 method: "POST",
    //                 data: {
    //                     _token: "{{ csrf_token() }}",
    //                     status: type,
    //                     id: {{ $application->id }},
    //                     remarks: result.value?.remarks || ''
    //                 },
    //                 success: function(response) {
    //                     if (response.success) {
    //                         Swal.fire('Success', response.message, 'success')
    //                             .then(() => location.reload());
    //                     } else {
    //                         Swal.fire('Error', response.message, 'error');
    //                     }
    //                 },
    //                 error: function() {
    //                     Swal.fire('Error', 'Action failed', 'error');
    //                 }
    //             });
    //         }
    //     });
    // }
</script>


            
            
        @endsection
</x-app-layout>
