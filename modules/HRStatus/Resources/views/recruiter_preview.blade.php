<x-app-layout>
    <div class="main-content">

           <div class="card bg-transparent my-4">
                <div class="card-header d-flex align-items-center justify-content-between" style="background:#fbfbfb;">
                    <div>
                        <div class="card-title h4 fw-bold">View Documents</div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}">Recruiters</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">View Documents</a></li>
                            </ol>
                        </nav>
                    </div>
            
                    <!-- Role Selector -->
                     <div class="d-flex align-items-center gap-2">
                         @if(empty($dm->emp_id) && $dm->approved_status == 1)
                            <button class="btn btn-primary btn-md" onclick="GenerateGDMID()">Generate GDM ID</button>
                         @endif
                           <a href="{{route('admin.Green-Drive-Ev.hr_status.index')}}" class="btn btn-dark btn-md">Back</a>
                          
                    </div>
                </div>
            </div>

            <?php
             $work_type = $dm->work_type ?? '';
            ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">{{'GDM ID'}}</label>
                                <input type="tel" class="form-control bg-white" name="emp_id" id="emp_id" oninput="sanitizeAndValidatePhone(this)"  value="{{$dm->emp_id ?? 'Still Under Review'}}" readonly>

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">{{'Contact No'}}</label>
                                <input type="tel" class="form-control bg-white" name="mobile_number" id="mobile_no" oninput="sanitizeAndValidatePhone(this)"  value="{{$dm->mobile_number ?? ''}}" readonly>

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="f_name">{{'First Name'}}</label>
                               <input type="text" class="form-control bg-white" name="first_name" id="f_name" value="{{$dm->first_name ?? '' }}" readonly>
                            </div>
                        </div>
                            
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="l_name">{{'Last Name'}}</label>
                               <input type="text" class="form-control bg-white" name="last_name" id="l_name" value="{{$dm->last_name ?? ''}}" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="email">{{'Email'}}</label>
                               <input type="text" class="form-control bg-white" name="email" id="email" value="{{$dm->email ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gender">{{'Gender'}}</label>
                               <input type="text" class="form-control bg-white" name="gender" id="gender" value="{{ucfirst($dm->gender) ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="house_no">{{'House No'}}</label>
                               <input type="text" class="form-control bg-white" name="house_no" id="house_no" value="{{$dm->house_no ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="street_name">{{'Street Name'}}</label>
                               <input type="text" class="form-control bg-white" name="street_name" id="street_name" value="{{$dm->street_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="current_city_id">City</label>
                                <select class="form-control basic-single bg-white" id="current_city_id" name="current_city_id" onchange="get_area('current_city_id')" disabled>
                                @if($cities)
                                    @foreach($cities as $data)
                                        <option value="{{ $data->id }}" {{ old('current_city_id', $dm->current_city_id) == $data->id ? 'selected' : '' }}>
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
                                <select class="form-control basic-single bg-white" id="interested_city_id" name="interested_city_id" disabled>
                                @if($areas)
                                    @foreach($areas as $data)
                                        <option value="{{ $data->id }}" {{ old('interested_city_id', $dm->interested_city_id) == $data->id ? 'selected' : '' }}>
                                            {{ $data->Area_name }}
                                        </option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="pincode">{{'Pincode'}}</label>
                               <input type="text" class="form-control bg-white" name="pincode" id="pincode" value="{{$dm->pincode ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="alternative_number">{{'Alternative Number'}}</label>
                               <input type="text" class="form-control bg-white" name="alternative_number" id="alternative_number" value="{{$dm->alternative_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="role">Role</label>
                                <select class="form-control basic-single bg-white" id="role" name="role" disabled>
                                    <option value="deliveryman" {{ $dm->work_type == "deliveryman" ? 'selected' : '' }}>Rider</option>
                                    <option value="in-house" {{ $dm->work_type == "in-house" ? 'selected' : '' }}>Employee</option>
                                    <option value="adhoc" {{ $dm->work_type == "adhoc" ? 'selected' : '' }}>Adhoc</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="ac_holder_name">{{'Account Holder Name'}}</label>
                               <input type="text" class="form-control bg-white" name="ac_holder_name" id="ac_holder_name" value="{{$dm->account_holder_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="bank_name">{{'Bank Name'}}</label>
                               <input type="text" class="form-control bg-white" name="bank_name" id="bank_name" value="{{$dm->bank_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="ifsc_code">{{'IFSC Code'}}</label>
                               <input type="text" class="form-control bg-white" name="ifsc_code" id="ifsc_code" value="{{$dm->ifsc_code ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="bank_ac_no">{{'Bank Account No'}}</label>
                               <input type="text" class="form-control bg-white" name="bank_ac_no" id="bank_ac_no" value="{{$dm->account_number ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="d_o_b">{{'DOB'}}</label>
                               <input type="text" class="form-control bg-white" name="d_o_b" id="d_o_b" value="{{$dm->date_of_birth ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="present_address">{{'Present Address'}}</label>
                               <input type="text" class="form-control bg-white" name="present_address" id="present_address" value="{{$dm->present_address ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_address">Permanent Address</label>
                               <input type="text" class="form-control bg-white" name="permanent_address" id="permanent_address" value="{{$dm->permanent_address ?? ''}}" readonly>
                            </div>
                        </div>
                        @if($dm->work_type != "in-house" && $dm->work_type != "")
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="prev_rider_id">Pervious Rider ID</label>
                               <input type="text" class="form-control bg-white" name="prev_rider_id" id="prev_rider_id" value="{{$dm->emp_prev_company_id ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="prev_company_experience">Past Experience</label>
                               <input type="text" class="form-control bg-white" name="prev_company_experience" id="prev_company_experience" value="{{$dm->emp_prev_experience ?? ''}}" readonly>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="guardian_name">Father/ Mother/ Guardian Name</label>
                               <input type="text" class="form-control bg-white" name="guardian_name" id="guardian_name" value="{{$dm->father_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="guardian_phone">Father/ Mother/ Guardian Contact No</label>
                               <input type="text" class="form-control bg-white" name="guardian_phone" id="guardian_phone" value="{{$dm->father_mobile_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reference_name">Reference Name</label>
                               <input type="text" class="form-control bg-white" name="reference_name" id="reference_name" value="{{$dm->referal_person_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reference_mobile">Reference Contact No</label>
                               <input type="text" class="form-control bg-white" name="reference_mobile" id="reference_mobile" value="{{$dm->referal_person_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reference_relationship">Reference Relationship</label>
                               <input type="text" class="form-control bg-white" name="reference_relationship" id="reference_relationship" value="{{$dm->referal_person_relationship ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="spouse_name">Spouse Name</label>
                               <input type="text" class="form-control bg-white" name="spouse_name" id="spouse_name" value="{{$dm->spouse_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="spouse_mobile">Spouse Contact No</label>
                               <input type="text" class="form-control bg-white" name="spouse_mobile" id="spouse_mobile" value="{{$dm->spouse_mobile_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="emerency_person_name1">Emerency Person Name 01</label>-->
                        <!--       <input type="text" class="form-control bg-white" name="emerency_person_name1" id="emerency_person_name1" value="{{$dm->emergency_contact_person_1_name ?? ''}}" readonly>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="emerency_person_mobile1">Emerency Person Contact 01</label>-->
                        <!--       <input type="text" class="form-control bg-white" name="emerency_person_mobile1" id="emerency_person_mobile1" value="{{$dm->emergency_contact_person_1_mobile ?? ''}}" readonly>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!-- <div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="emerency_person_name2">Emerency Person Name 02</label>-->
                        <!--       <input type="text" class="form-control bg-white" name="emerency_person_name2" id="emerency_person_name2" value="{{$dm->emergency_contact_person_2_name ?? ''}}" readonly>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="emerency_person_mobile2">Emerency Person Contact 01</label>-->
                        <!--       <input type="text" class="form-control bg-white" name="emerency_person_mobile2" id="emerency_person_mobile2" value="{{$dm->emergency_contact_person_2_mobile ?? ''}}" readonly>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="blood_group">Blood Group</label>
                               <input type="text" class="form-control bg-white" name="blood_group" id="blood_group" value="{{$dm->blood_group ?? ''}}" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="social_links">Social Link</label>
                               <input type="text" class="form-control bg-white" name="social_links" id="social_links" value="{{$dm->social_links ?? ''}}" readonly>
                            </div>
                        </div>
                        @if($work_type != "in-house")
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="rider_type">Rider Type</label>
                                <select class="form-control basic-single bg-white" id="rider_type" name="rider_type" disabled>
                                @if($rider_types)
                                    @foreach($rider_types as $data)
                                        <option value="{{ $data->id }}" {{ $dm->rider_type == $data->id ? 'selected' : '' }}>
                                            {{ $data->type }}
                                        </option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                        <?php
                            $vehicleTypes = ['2W', '3W', '4W', '8W','Rental'];
                        ?>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="rider_type">Vehicle Type</label>
                                <select class="form-control basic-single bg-white" id="vehicle_type" name="vehicle_type" disabled>
                                @if($vehicleTypes)
                                    @foreach($vehicleTypes as $type)
                                        <option value="{{ $type }}" {{ old('vehicle_type',$dm->vehicle_type) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script>

     function GenerateGDMID(title = "Are you sure?") {
        Swal.fire({
        title: title,
        text: "Do you want to generate the GDM ID?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Yes, generate it",
        cancelButtonText: "No, cancel",
        confirmButtonColor: "#28a745",  // Green
        cancelButtonColor: "#dc3545",   // Red
        reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                        url: "{{ route('admin.Green-Drive-Ev.delivery-man.generate_gdmid', $dm->id) }}",
                        type: "GET",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire("Accepted!", response.message, "success");
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire("Warning!", response.message ?? "Update failed.", "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error!", "The network connection has failed. Please try again later", "error");
                        }
                    });
            }
        });
    }
</script>
@endsection
</x-app-layout>
