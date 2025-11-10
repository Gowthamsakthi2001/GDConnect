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

        .custom-card-body {
            height: 500px;
            overflow-y: auto;
        }

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


        input[type="radio"]#approve:checked+label[data-type="approve"] {
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

        .circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: white;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .circle td {
            color: White !important;
        }

        .present {
            background-color: #4CAF50;

        }

        .absent {
            background-color: #f44336;

        }

        .current {
            background-color: #2196F3;

        }

        .join-date {
            background-color: #fbc02d;

        }

        .circle {
            color: black;
        }

        .circle.present,
        .circle.absent,
        .circle.join-date,
        .circle.current {
            color: white;
        }

        #status div .circle {
            width: 22px !important;
            height: 22px !important;
        }
    </style>

    <div class="main-content">

        <div class="card my-4">
            <div class="card-header">
                <!-- Change: Removed g-3 and added flex-nowrap to prevent wrapping -->
                <div class="row d-flex justify-content-between align-items-center flex-nowrap">

                    <!-- Change: Removed col-md-6 and col-12 for more fluid width -->
                    <div class="col-auto">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>

                                 <div class="col-12 text-center my-3">
                                    @php
                                    $defaultProfile = asset('admin-assets/icons/custom/person.png');
                                    $photoFile = public_path('EV/images/photos/' . $adhoc->photo);
                                    $profileImage = (!empty($adhoc->photo) && file_exists($photoFile))
                                    ? asset('EV/images/photos/' . $adhoc->photo)
                                    : $defaultProfile;
                                    @endphp

                                    <img src="{{ $profileImage }}"
                                        alt="Profile"
                                        width="70" height="70"
                                        class=" mb-2"
                                        style="border-radius: 50%; object-fit: cover;"
                                        onerror="this.src='{{ $defaultProfile }}'">

                                </div>
                            </div>
                            <div class="px-3 w-100">
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <div class="h5 fw-bold mb-0 text-nowrap"> <!-- Change: Added text-nowrap -->
                                        {{$adhoc->first_name}} {{$adhoc->last_name}}
                                    </div>
                                    <nav aria-label="breadcrumb" class="m-0">
                                        <!-- Change: Added flex-nowrap and gap-3 to the ol tag -->
                                        <ol class="breadcrumb mb-0 d-flex flex-nowrap gap-3">
                                            <li class="breadcrumb-item text-nowrap">
                                                <img src="{{ asset('admin-assets/icons/custom/profile_icon.png') }}"
                                                    alt="Profile" class="me-1" style="width:16px;">
                                                {{$adhoc->emp_id}}
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Change: Removed col-md-6 and col-12, added col-auto -->
                    <div class="col-auto">
                        <!-- Change: Added flex-nowrap -->
                        <div class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                            <button class="btn btn-primary edit-candidate-btn" onclick="edit_candidate()">
                                <i class="bi bi-pencil-square me-2"></i> Edit Candidate
                            </button>
                            <a href="{{ route('admin.Green-Drive-Ev.employee_categories.adhoc_list') }}"
                                class="btn btn-dark edit-candidate-btn px-5">
                                <i class="bi bi-arrow-left me-2"></i> Back
                            </a>
                            <button type="submit" form="employeeForm" class="btn btn-success d-none update-candidate">
                                <i class="bi bi-floppy me-2"></i>Save Changes
                            </button>
                            <button type="button" class="btn border-gray d-none update-candidate" onclick="cancelEdit()">
                                <i class="bi bi-x me-2"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card my-3">


            <div class="card-header" style="background:#f1f5f9;">
                <ul class="nav nav-pills row d-flex align-items-center" id="pills-tab" role="tablist">
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link active" id="pills-basic-information-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-basic-information" type="button" role="tab"
                            aria-controls="pills-basic-information" aria-selected="true">
                            <img src="{{ asset('admin-assets/icons/custom/person.png') }}" alt="image">
                            <span class="head-text" style="color:#adb3bb;">Basic Information</span>
                        </button>
                    </li>
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link" id="pills-kyc-doc-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-kyc-doc" type="button" role="tab"
                            aria-controls="pills-kyc-doc" aria-selected="false">
                            <img src="{{ asset('admin-assets/icons/custom/kyc_doc.png') }}" alt="image">
                            <span class="head-text" style="color:#adb3bb;">KYC Documents</span>
                        </button>
                    </li>
                    <li class="nav-item col-md-4" role="presentation">
                        <button class="nav-link" id="pills-query-comments-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-query-comments" type="button" role="tab"
                            aria-controls="pills-query-comments" aria-selected="false">
                            <img src="{{ asset('admin-assets/icons/custom/query.png') }}" alt="image">
                            <span class="head-text" style="color:#adb3bb;">Attendance</span>
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
                                <form id="employeeForm" method="POST" action="{{ route('admin.Green-Drive-Ev.employee_categories.adhoc_update', $adhoc->id) }}">
                                    @csrf
                                    @method('POST')
                                    <div class="row">

                                        <div class="col-12 text-center my-3">
                                            @php
                                            $profileImage = $adhoc->photo
                                            ? (file_exists(public_path('EV/images/photos/' . $adhoc->photo))
                                            ? asset('EV/images/photos/' . $adhoc->photo)
                                            : asset('admin-assets/icons/custom/person.png'))
                                            : asset('admin-assets/icons/custom/person.png');
                                            @endphp

                                            <img src="{{ $profileImage }}"
                                                alt="Profile"
                                                class=" mb-2"
                                                width="80"
                                                height="80"
                                                style="border-radius: 50%;"
                                                onerror="this.src='{{ asset('admin-assets/icons/custom/person.png') }}'">
                                            <p class="text-muted">Profile Picture</p>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="mobile_no">Contact No</label>
                                                <input type="tel" class="form-control bg-white" name="mobile_number"
                                                    id="mobile_no" oninput="sanitizeAndValidatePhone(this)"
                                                    value="{{ $adhoc->mobile_number ?? '' }}">

                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="f_name">First Name</label>
                                                <input type="text" class="form-control bg-white" name="first_name"
                                                    id="f_name" value="{{$adhoc-> first_name}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="l_name">Last Name</label>
                                                <input type="text" class="form-control bg-white" name="last_name"
                                                    id="l_name" value="{{$adhoc->last_name}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="email">Email</label>
                                                <input type="text" class="form-control bg-white" name="email"
                                                    id="email" value="{{$adhoc->email}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="gender">Gender</label>
                                                <input type="text" class="form-control bg-white" name="gender"
                                                    id="gender" value="{{$adhoc->gender}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="house_no">House No</label>
                                                <input type="text" class="form-control bg-white" name="house_no"
                                                    id="house_no" value="{{$adhoc->house_no}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="street_name">Street Name</label>
                                                <input type="text" class="form-control bg-white" name="street_name"
                                                    id="street_name" value="{{$adhoc->street_name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="current_city_id">Current City</label>
                                                <select class="form-control basic-single bg-white" id="current_city_id" name="current_city_id" disabled>
                                                    @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ $adhoc->current_city_id == $city->id ? 'selected' : '' }}>
                                                        {{ $city->city_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>



                                        <div class="col-md-6 mb-3">
                                            <div class="form-group mt-3">
                                                <label class="input-label mb-2 ms-1" for="interested_city_id">Interested City</label>
                                                <select class="form-control basic-single bg-white" id="interested_city_id" name="interested_city_id" disabled>
                                                    @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ $adhoc->interested_city_id == $city->id ? 'selected' : '' }}>
                                                        {{ $city->city_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="pincode">Pincode</label>
                                                <input type="text" class="form-control bg-white" name="pincode"
                                                    id="pincode" value="{{$adhoc->pincode}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="alternative_number">Alternative
                                                    Number</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="alternative_number" id="alternative_number" value="{{$adhoc->alternative_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="role">Role</label>
                                                <!-- <select class="form-control basic-single bg-white" id="role"
                                                    name="role">
                                                    <option value="chennai">Chennai</option>
                                                </select> -->
                                                <input type="text" class="form-control bg-white"
                                                    name="alternative_number" id="alternative_number" value="{{$adhoc->work_type}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="ac_holder_name">Account Holder
                                                    Name</label>
                                                <input type="text" class="form-control bg-white" name="account_holder_name"
                                                    id="ac_holder_name" value="{{$adhoc->account_holder_name}} " readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="bank_name">Bank Name</label>
                                                <input type="text" class="form-control bg-white" name="bank_name"
                                                    id="bank_name" value="{{$adhoc->bank_name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="ifsc_code">IFSC Code</label>
                                                <input type="text" class="form-control bg-white" name="ifsc_code"
                                                    id="ifsc_code" value="{{$adhoc->ifsc_code}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="bank_ac_no">Bank Account
                                                    No</label>
                                                <input type="text" class="form-control bg-white" name="account_number"
                                                    id="bank_ac_no" value="{{$adhoc->account_number}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="d_o_b">DOB</label>
                                                <input type="text" class="form-control bg-white" name="date_of_birth"
                                                    id="d_o_b" value="{{$adhoc->date_of_birth}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="present_address">Present
                                                    Address</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="present_address" id="present_address" value="{{$adhoc->present_address}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="permanent_address">Permanent
                                                    Address</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="permanent_address" id="permanent_address" value="{{$adhoc->permanent_address}}">
                                            </div>
                                        </div>
                                        @if ($adhoc->work_type != 'in-house' && $adhoc->work_type != '')
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="prev_rider_id">Pervious
                                                    Rider ID</label>
                                                <input type="text" class="form-control bg-white" name="prev_rider_id"
                                                    id="prev_rider_id" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="emp_prev_experience">Past
                                                    Experience</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="prev_company_experience" id="emp_prev_experience"
                                                    value="">
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="guardian_name">Father/ Mother/
                                                    Guardian Name</label>
                                                <input type="text" class="form-control bg-white" name="father_name"
                                                    id="guardian_name" value="{{$adhoc->father_name ?: $adhoc->mother_name ?: 'N/A' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="father_mobile_number">Father/ Mother/
                                                    Guardian Contact No</label>
                                                <input type="text" class="form-control bg-white" name="guardian_phone"
                                                    id="guardian_phone" value="{{$adhoc->father_mobile_number ?: $adhoc->mother_mobile_number ?: 'N/A' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="reference_name">Reference
                                                    Name</label>
                                                <input type="text" class="form-control bg-white" name="referal_person_name"
                                                    id="reference_name" value="{{$adhoc->referal_person_name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="reference_mobile">Reference
                                                    Contact No</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="referal_person_mobile" id="reference_mobile" value="{{$adhoc->referal_person_mobile}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1"
                                                    for="reference_relationship">Reference Relationship</label>
                                                <input type="text" class="form-control bg-white"
                                                    name="referal_person_relationship" id="reference_relationship"
                                                    value="{{$adhoc->referal_person_relationship}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="spouse_name">Spouse Name</label>
                                                <input type="text" class="form-control bg-white" name="spouse_name"
                                                    id="spouse_name" value="{{$adhoc->spouse_name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="spouse_mobile">Spouse Contact
                                                    No</label>
                                                <input type="text" class="form-control bg-white" name="spouse_mobile_number"
                                                    id="spouse_mobile" value="{{$adhoc->spouse_mobile_number}}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="blood_group">Blood Group</label>
                                                <input type="text" class="form-control bg-white" name="blood_group"
                                                    id="blood_group" value="{{ $adhoc->blood_group ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="social_links">Social
                                                    Link</label>
                                                <input type="text" class="form-control bg-white" name="social_links"
                                                    id="social_links" value="{{$adhoc->social_links}}">
                                            </div>
                                        </div>


                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="rider_type">Rider Type</label>
                                                <select class="form-control basic-single bg-white" id="rider_type" name="rider_type" disabled>
                                                    @foreach($riderTypes as $riderType)
                                                    <option value="{{ $riderType->id }}" {{ $adhoc->rider_type == $riderType->id ? 'selected' : '' }}>
                                                        {{ $riderType->type }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="input-label mb-2 ms-1" for="rider_type">Vehicle
                                                    Type</label>
                                                <!-- <select class="form-control basic-single bg-white" id="vehicle_type"
                                                    name="vehicle_type" value="{{$adhoc->vehicle_type}}" disabled> -->
                                                     <input type="text" class="form-control bg-white" name="vehicle_type"
                                                    id="vehicle_type" value="{{$adhoc->vehicle_type}}" readonly>

                                                </select>
                                            </div>
                                        </div>
                                        {{-- @endif  --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- KYC Documents-->
                    <div class="tab-pane fade" id="pills-kyc-doc" role="tabpanel"
                        aria-labelledby="pills-kyc-doc-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header" style="background:#edfcff;">
                                <h5 style="color:#1b4d5e;" class="fw-bold">KYC Documents</h5>
                                <p class="mb-0" style="color:#1b4d5e;">KYC Documents submitted on Application form
                                </p>
                            </div>


                            <div class="card-body custom-card-body">
                                <div class="card mb-3 shadow shadow-md">
                                    <div class="card-header">
                                        <div class="row d-flex justify-content-between g-3">
                                            <div class="col">
                                                <div class="card-title h5 fw-medium">Aadhar Card Front</div>
                                                <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025,
                                                    12:30:00 AM</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $adhoc->aadharVerifier->name ?? 'N/A' }}
                                                    </span>
                                                </p>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{$adhoc->aadhar_verify_date}}
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col text-end">

                                                <button class="btn btn-danger px-4"
                                                    onclick="status_change_alert(
                                                            '#', 
                                                            'Verified this Aadhar?', 
                                                            event
                                                        )">Verify</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container" >
                                                    <img src="{{ asset($adhoc->aadhar_card_front ? "/EV/images/aadhar/" . $adhoc->aadhar_card_front : "/EV/images/dummy.jpg") }}"
                                                        class="preview-image img-fluid"
                                                        onerror="this.src='/EV/images/dummy.jpg'"
                                                        style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ asset($adhoc->aadhar_card_front ? "/EV/images/aadhar/" . $adhoc->aadhar_card_front : "/EV/images/dummy.jpg") }}')"
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
                                                <div class="card-title h5 fw-medium">Aadhar Card Back</div>
                                                <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025,
                                                    12:30:00 AM</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $adhoc->aadharVerifier->name ?? 'N/A' }}
                                                    </span>
                                                </p>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{$adhoc->aadhar_verify_date}}
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col text-end">

                                                <button class="btn btn-danger px-4"
                                                    onclick="status_change_alert(
                                                            '#', 
                                                            'Verified this Aadhar?', 
                                                            event
                                                        )">Verify</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container" >
                                                    <img src="{{ asset($adhoc->aadhar_card_back ? "/EV/images/aadhar/" . $adhoc->aadhar_card_back : "/EV/images/dummy.jpg") }}"
                                                        class="preview-image img-fluid"
                                                        onerror="this.src='/EV/images/dummy.jpg'"
                                                        style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button  onclick="OpenImageModal('{{ asset($adhoc->aadhar_card_back ? "/EV/images/aadhar/" . $adhoc->aadhar_card_back : "/EV/images/dummy.jpg") }}')"
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
                                                <div class="card-title h5 fw-medium">PAN Card</div>
                                                <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025,
                                                    12:30:00 AM</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $adhoc->panVerifier->name ?? 'N/A' }}
                                                    </span>
                                                </p>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{$adhoc->pan_verify_date}}
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col text-end">

                                                <button class="btn btn-danger px-4"
                                                    onclick="status_change_alert(
                                                            '#', 
                                                            'Verified this Pan?', 
                                                            event
                                                        )">Verify</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-12 mt-3 mt-md-0 d-flex justify-content-center">
                                                <div class="image-container" >
                                                    <img src="{{ asset($adhoc->pan_card_front ? "/EV/images/pan/" . $adhoc->pan_card_front : "/EV/images/dummy.jpg")}}"
                                                        class="preview-image img-fluid"
                                                        onerror="this.src='/EV/images/dummy.jpg'"
                                                        style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ asset($adhoc->pan_card_front ? "/EV/images/pan/" . $adhoc->pan_card_front : "/EV/images/dummy.jpg") }}')"
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
                                                <div class="card-title h5 fw-medium">Bank Details</div>
                                                <div class="card-title text-muted mb-3">Uploaded at 24 ar 2025,
                                                    12:30:00 AM</div>

                                                <p class="mb-2 fw-medium">
                                                    Verified by:
                                                    <span class="text-success">
                                                        {{ $adhoc->bankVerifier->name ?? 'N/A' }}
                                                    </span>
                                                </p>

                                                <p class="fw-medium">Verified At:
                                                    <span class="text-success">
                                                        {{$adhoc->bank_verify_date ?? 'N/A'}}
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="col text-end">

                                                <button class="btn btn-danger px-4"
                                                    onclick="status_change_alert(
                                                            '#', 
                                                            'Verified this Bank Details?', 
                                                            event
                                                        )">Verify</button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row mt-5">
                                            <div class="col-md-4 mt-3 mt-md-0 d-flex justify-content-start">
                                                <div class="image-container" >
                                                    <img src="{{ asset($adhoc->bank_passbook ? "/EV/images/bank_passbook/" . $adhoc->bank_passbook : "/EV/images/dummy.jpg") }}"
                                                        class="preview-image img-fluid"
                                                        onerror="this.src='/EV/images/dummy.jpg'"
                                                        style="width: 350px; height: 230px; object-fit: cover; border-radius: 10px;">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3 mt-md-0">
                                                <h6 class="my-3"> Bank Holder Name: {{$adhoc->account_holder_name}} <span
                                                        class="text-secondary"></span>
                                                </h6>
                                                <h6 class="mb-3"> Bank Name: {{$adhoc->bank_name}} <span class="text-secondary"></span>
                                                </h6>
                                                <h6 class="mb-3"> IFSC Code: {{$adhoc->ifsc_code}} <span class="text-secondary"></span>
                                                </h6>
                                                <h6 class="mb-3"> Account Number: {{$adhoc->account_number}} <span
                                                        class="text-secondary"></span>
                                                </h6>
                                            </div>

                                            <div class="col-12 mt-5">
                                                <button onclick="OpenImageModal('{{ asset($adhoc->bank_passbook ? "/EV/images/bank_passbook/" . $adhoc->bank_passbook : "/EV/images/dummy.jpg") }}')"
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

                    <!--Attendance Tab-->
                    <div class="tab-pane fade" id="pills-query-comments" role="tabpanel"
                        aria-labelledby="pills-query-comments-tab" tabindex="0">
                        <div class="card">
                            <div class="card-header" style="background:#ffeded;">
                                <h5 style="color:#5e1b1b;" class="fw-bold">Attendance</h5>
                                <p class="mb-0" style="color:#5e1b1b;">Attendance Details</p>
                            </div>
                            <div class="card-body">
                                <!-- Month Switcher -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <button class="btn btn-sm" onclick="changeMonth(-1)">
                                        <i class="bi bi-chevron-left fs-5"></i>
                                    </button>
                                    <h5 id="calendarMonthYear" class="fw-bold mb-0 text-center">March 2025</h5>
                                    <button class="btn btn-sm" onclick="changeMonth(1)">
                                        <i class="bi bi-chevron-right fs-5"></i>
                                    </button>
                                </div>

                                <!-- Calendar -->
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center">
                                        <thead>
                                            <tr>
                                                <th>Mo</th>
                                                <th>Tu</th>
                                                <th>We</th>
                                                <th>Th</th>
                                                <th>Fr</th>
                                                <th>Sa</th>
                                                <th>Su</th>
                                            </tr>
                                        </thead>
                                        <tbody id="calendarBody">
                                            <!-- Dynamic rows injected by JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Legend -->
                                <div class="d-flex justify-content-around gap-4 mt-3 flex-wrap" id="status">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="circle present"></div><span>Present</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="circle absent"></div><span>Absent</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="circle current"></div><span>Current</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="circle join-date"></div><span>Join Date</span>
                                    </div>
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
                                                    <img id="aahaar_front_view" src="EV/images/dummy.jpg"
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
                                                    <img id="aahaar_back_view" src="EV/images/dummy.jpg"
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
                                                    <img id="pan_card_view" src="EV/images/dummy.jpg"
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
                                                    <img id="dl_front_view" src="EV/images/dummy.jpg"
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
                                                    <img id="dl_back_view" src="EV/images/dummy.jpg"
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
                                                <img id="dl_back_view" src="EV/images/dummy.jpg"
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
            {{-- <div class="card-header border-0 pb-0">
                <h5 class="fw-bold">Action Panel</h5>
            </div>
            <div class="card-body">

                <div class="mb-3 d-none" id="RemarkSection">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <label class="input-label" for="mobile_no">Remarks <span
                                class="text-danger fw-bold">*</span></label>
                        <button class="btn btn-md border-gray">Submit</button>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5"></textarea>
                        <input type="hidden" id="remark_type" value="">
                    </div>
                </div>

                <div class="row d-flex justify-content-evenly">
                    <div class="col-md-2 col-6 mb-3">
                        <input type="radio" name="status" id="approve" class="d-none"
                            onchange="UpdateCandidateStatus('approve_sent_to_hr02')" />
                        <label for="approve" class="btn btn-md border-gray action-btn" data-type="approve">
                            <i class="bi bi-check2-circle"></i> Approve - Employee
                        </label>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <input type="radio" name="status" id="ride" class="d-none"
                            onchange="UpdateCandidateStatus('ride')" />
                        <label for="ride" class="btn btn-md border-gray action-btn" data-type="ride">
                            <i class="bi bi-send"></i> Approve - Rider
                        </label>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <input type="radio" name="status" id="hold" class="d-none"
                            onchange="UpdateCandidateStatus('on_hold')" />
                        <label for="hold" class="btn btn-md border-gray action-btn" data-type="hold">
                            <i class="bi bi-clock"></i> Send Back to HR 01
                        </label>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <input type="radio" name="status" id="bgv" class="d-none"
                            onchange="UpdateCandidateStatus('bgv')" />
                        <label for="bgv" class="btn btn-md border-gray action-btn" data-type="bgv">
                            <i class="bi bi-x-circle"></i> Send Back to BGV
                        </label>
                    </div>

                    <div class="col-md-2 col-6 mb-3">
                        <input type="radio" name="status" id="rejected" class="d-none"
                            onchange="UpdateCandidateStatus('rejected')" />
                        <label for="rejected" class="btn btn-md border-gray action-btn" data-type="rejected">
                            <i class="bi bi-x-circle"></i> Rejected
                        </label>
                    </div>
                </div>



                <div class="row p-3 rounded" style="background:#eaeaea;">
                    <p class="mb-3 text-start fw-medium">Previous Remarks :</p>

                    <div class="col-12 border-gray p-3 mb-3">
        
                        <p class="d-flex justify-content-between align-items-center mb-0" style="color:#00000080;">
                            <span>Documents Verified</span>
                            <small class="fw-normal">12 May 2025, 10:00:00 AM</small>
                        </p>
                    </div>
                </div>
            </div>
        </div> --}}
        </div>
        
        
        <div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content rounded-4">

      <!-- Header with fixed control buttons -->
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

      <!-- Scrollable modal body -->
      <div class="modal-body text-center py-6" style="overflow: auto; max-height: 80vh;">
        <img src="" id="kyc_image" style="max-width: 100%; transition: transform 0.3s ease;">
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
        </script>

        <script>
            $(document).ready(function() {

                // 1. Select the KYC tab link by its ID.
                $('#pills-kyc-doc-tab').on('click', function() {

                    // 2. Find the 'Send Back to HR 01' radio button (which has the id="hold").
                    const holdRadioButton = $('#hold');

                    // 3. Set its 'checked' property to true.
                    holdRadioButton.prop('checked', true);

                    // 4. IMPORTANT: Trigger the 'change' event to run your onchange function.
                    // This makes the 'Remark Section' appear and changes the button color.
                    holdRadioButton.trigger('change');
                });

            });
        </script>


        <script>
            const presentDates = ['2025-03-10', '2025-03-11', '2025-03-12', '2025-04-15'];
            const absentDates = ['2025-03-09', '2025-04-16'];
            const currentDate = '2025-03-13';
            const joinDate = '2025-03-08';

            let currentMonth = 2; // March (0-based index)
            let currentYear = 2025;

            function renderCalendar(year, month) {
                const monthNames = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday
                const totalDays = new Date(year, month + 1, 0).getDate();

                const startDay = (firstDay === 0) ? 6 : firstDay - 1; // convert Sunday to 6

                const tbody = document.getElementById("calendarBody");
                tbody.innerHTML = "";

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    const row = document.createElement("tr");

                    for (let j = 0; j < 7; j++) {
                        const cell = document.createElement("td");

                        if (i === 0 && j < startDay) {
                            cell.innerHTML = "";
                        } else if (date > totalDays) {
                            cell.innerHTML = "";
                        } else {
                            const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${date
                        .toString().padStart(2, '0')}`;

                            const circle = document.createElement("div");
                            circle.innerText = date;
                            circle.classList.add("circle");

                            if (dateStr === currentDate) {
                                circle.classList.add("current");
                            } else if (presentDates.includes(dateStr)) {
                                circle.classList.add("present");
                            } else if (absentDates.includes(dateStr)) {
                                circle.classList.add("absent");
                            } else if (dateStr === joinDate) {
                                circle.classList.add("join-date");
                            }

                            cell.appendChild(circle);
                            date++;
                        }

                        row.appendChild(cell);
                    }

                    tbody.appendChild(row);
                    if (date > totalDays) break;
                }

                document.getElementById("calendarMonthYear").innerText = `${monthNames[month]} ${year}`;
            }

            function changeMonth(diff) {
                currentMonth += diff;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear -= 1;
                } else if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear += 1;
                }

                renderCalendar(currentYear, currentMonth);
            }

            // Auto render on page load
            document.addEventListener("DOMContentLoaded", () => {
                renderCalendar(currentYear, currentMonth);
            });
        </script>

        <script>
            function edit_candidate() {
                // Show save/cancel buttons
                $(".update-candidate").removeClass("d-none");
                $(".edit-candidate-btn").addClass("d-none");

                // Enable all form inputs
                $("#employeeForm :input").prop("readonly", false);
                $("#employeeForm select").prop("disabled", false);
            }

            function cancelEdit() {
                // Hide save/cancel buttons
                $(".update-candidate").addClass("d-none");
                $(".edit-candidate-btn").removeClass("d-none");

                // Disable all form inputs
                $("#employeeForm :input").prop("readonly", true);
                $("#employeeForm select").prop("disabled", true);

                // Optional: Reset form to original values
                $("#employeeForm")[0].reset();
            }

            // Initialize form to readonly state
            $(document).ready(function() {
                $("#employeeForm :input").prop("readonly", true);
                $("#employeeForm select").prop("disabled", true);
            });

            // Form submission handler
            $("#employeeForm").submit(function(e) {
                e.preventDefault();

                // Show loading state
                Swal.fire({
                    title: 'Saving Changes',
                    html: 'Please wait while we update the employee details',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload(); // Force page reload
                                }
                            });
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                            if (xhr.responseJSON.error_details) {
                                console.error('Server Error:', xhr.responseJSON.error_details);
                            }
                        }
                        showError(errorMsg);
                    }
                });
            });

            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
            }
            // [Keep your existing show_imagefunction, UpdateCandidateStatus, and calendar scripts...]
            
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


        @endsection
</x-app-layout>