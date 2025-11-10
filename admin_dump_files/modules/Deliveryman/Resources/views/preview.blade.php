<x-app-layout>
    <div class="main-content">
        
        <!--navigation buttons start -->
        <div class="row d-flex justify-content-around">
            <div class="col-md-6">
                <div class="m-5">
                    @if ( $nextDm)
                        <a href="{{ route('admin.Green-Drive-Ev.delivery-man.preview_navigation', ['id' => $nextDm->id]) }}" class="btn btn-primary"> <i class="bi bi-arrow-left-circle-fill"></i> Previous</a>
                    @endif
                </div>
            </div>

            <div class="col-md-6 text-end">
                <div class="m-5">
                    @if ($prevDm)
                        <a href="{{ route('admin.Green-Drive-Ev.delivery-man.preview_navigation', ['id' => $prevDm->id]) }}" class="btn btn-primary px-4">Next <i class="bi bi-arrow-right-circle-fill"></i></a>
                    @endif
                    
                </div>
            </div>
        </div>
        <!--navigation buttons end -->
        
        
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
    
    <?php
      $user_work_type = '';
      if($dm->work_type == "in-house"){
          $user_work_type = 'Employee';
      }
      else if($dm->work_type == "deliveryman"){
          $user_work_type = 'Rider';
      }else if($dm->work_type == "adhoc"){
          $user_work_type = 'Adhoc';
      }
    ?>

    <!--page card-->
    <div class="row">
        <!-- Personal Details Card -->
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3><img src="{{ isset($dm->photo) ? asset('public/EV/images/photos/'.$dm->photo) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid rounded-circle" alt="Deliveryman Image" style="width: 100px; height: 100px; object-fit: cover;"> Profile Details</h3>
                    <div class="row mt-1">
                        
                        <div class="col-12 mt-1 mt-md-0">
                            <p><b>First Name : </b> {{ $dm->first_name }}</p>
                            <p><b>Last Name : </b> {{ $dm->last_name }}</p>
                            <p><b>Contact No : </b> {{ $dm->mobile_number }} </p>
                            <p><b>GDM ID : </b> {{ $dm->emp_id ?? '-' }} </p>
                             <p><b>Blood Group: </b> {{ $dm->blood_group ?? '-' }} </p>
                            <?php
                            //   dd($dm->current_city->city_name);
                            ?>
                            <p><b>Description : </b> {{ $dm->remarks }} </p>
                            <p><b>City : </b> {{ $dm->current_city->city_name ?? '' }} </p>
                            <!--<p><b>Lead Source : </b> {{ $dm->source_name }} </p>-->
                            <p><b> Area : </b> {{ $dm->interest_city->Area_name ?? '' }} </p>
                            <p><b> Pincode : </b> {{ $dm->pincode }} </p> 
                            <p><b> Role : </b> {{ $user_work_type }} </p> 
                            @if($dm->work_type != "in-house")
                            <p><b>Rider Type : </b> {{ $dm->RiderType->type ?? '-' }} </p>
                            <p><b>Vehicle Type : </b> {{ $dm->vehicle_type }} </p>
                            @endif
                            <?php
                              $hub = \Modules\Clients\Entities\ClientHub::where('client_id',$dm->client_id)->where('id',$dm->hub_id)->first();
                            ?>
                             @if($dm->work_type != "in-house")
                             <p><b>Zone : </b> {{ $hub->zone->name ?? ''}} </p>
                            <p><b>Client Name : </b> {{ $hub->client->client_name ?? ''}} </p>
                            <p><b>Hub Name : </b> {{ $hub->hub_name ?? ''}} </p>
                            @endif
                            
                            <!--<p><b>Job Apply Source: </b> {{ $dm->apply_job_source }} </p>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
         <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3> <img src="{{ isset($dm->photo) ? asset('public/EV/images/photos/'.$dm->photo) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid rounded-circle" alt="Deliveryman Image" style="width: 100px; height: 100px; object-fit: cover;"> Personal Details</h3>
                    <div class="row mt-1">
                      
                        <div class="col-12 mt-1 mt-md-0">
                            <p><b>First Name : </b> {{ $dm->first_name }}</p>
                            <p><b>DOB : </b> {{ $dm->date_of_birth ? \Carbon\Carbon::parse($dm->date_of_birth)->format('Y-m-d') : '' }}</p>
                            <p><b>Contact No : </b> {{ $dm->mobile_number }} </p>
                            <p><b>Email : </b> {{ $dm->email }} </p>
                            <p><b>Gender : </b> {{ucfirst($dm->gender) ?? ''}} </p>
                            <p><b>House No : </b> {{ $dm->house_no ?? ''}} </p>
                            <p><b>Street Name : </b> {{ $dm->street_name ?? ''}} </p>
                            <p><b>Alternative Number : </b> {{ $dm->alternative_number ?? ''}} </p>
                            <p><b>Father/ Mother/ Guardian Name : </b> {{ $dm->father_name }} </p>
                            <p><b>Father/ Mother/ Guardian Contact No: </b> {{ $dm->father_mobile_number }} </p>
                            <!--<p><b>Guardian Name : </b> {{ $dm->mother_name }} </p>-->
                            <!--<p><b>Guardian Mobile : </b> {{ $dm->mother_mobile_number }} </p>-->
                            
                            @if($dm->spouse_name != "")
                              <p><b>Spouse Name : </b> {{ $dm->spouse_name }} </p> 
                             @endif
                             @if($spouse_new_phone != "")
                               <p><b>Spouse Contact No : </b> {{ $spouse_new_phone }} </p>
                             @endif
                             @if($dm->present_address != "")
                               <p><b>Present Address : </b> {{ $dm->present_address }} </p>
                             @endif
                              @if($dm->permanent_address != "")
                               <p><b>Permanent Address : </b> {{ $dm->permanent_address }} </p>
                             @endif
                            <p><b>Pervious Rider ID: </b> {{ $dm->emp_prev_company_id ?? '-' }} </p>
                            <p><b>Past Experience: </b> {{ $dm->emp_prev_experience ?? '-' }} </p> 
                            <p><b>Reference Name: </b> {{ $dm->referal_person_name ?? '-' }} </p> 
                            <p><b>Reference Contact No: </b> {{ $dm->referal_person_number ?? '-' }} </p> 
                            <p><b>Reference Relationship: </b> {{ $dm->referal_person_relationship ?? '-' }} </p> 
                            <p><b>Social Link: </b> {{ $dm->social_links ?? '-' }} </p> 
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <!-- Aadhar Details Card -->
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3>Aadhar Details</h3>
                    @if($dm->aadhar_verify)
                    <div class="col-md-6">
                         <p><b>Verifed by : </b> <b class="text-primary">{{ ucfirst($dm->who_verify) ?? '' }}</b> </p>
                        <p><b>Verifed at:  </b>{{ $dm->aadhar_verify_date ? \Carbon\Carbon::parse($dm->aadhar_verify_date)->format('d-m-Y H:i:s') : 'N/A' }}</p>
                         <button class="btn btn-outline-info d-inline" onclick="rotateImage('aadhar-front')">Rotate Front</button>
                        <button class="btn btn-outline-info d-inline" onclick="rotateImage('aadhar-back')">Rotate Back</button>
                    </div>
                    @else
                    <div class="form-check form-switch" style="padding-left:0px;">
                        <!--<label class="toggle-switch" style="padding-left:0px;" for="verifiedStatusButton_{{ $dm->id }}">-->
                            
                        <!--    <button -->
                        <!--        type="button" -->
                        <!--        onclick="status_change_alert(-->
                        <!--            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->aadhar_verify ? 0 : 1, 'aadhar_verify']) }}', -->
                        <!--            '{{ $dm->aadhar_verify ? 'UnVerified' : 'Verified' }} this {{$user_work_type}}?', -->
                        <!--            event-->
                        <!--        )" -->
                        <!--        class="btn btn-{{ $dm->aadhar_verify == 1 ? 'danger' : 'success' }} toggle-btn" -->
                        <!--        id="verifiedStatusButton_{{ $dm->id }}">-->
                        <!--        {{ $dm->aadhar_verify == 1 ? 'UnVerified' : 'Verified' }}-->
                        <!--    </button>-->
                        <!--</label>-->
                         <button class="btn btn-outline-info " onclick="rotateImage('aadhar-front')">Rotate Front</button>
                        <button class="btn btn-outline-info " onclick="rotateImage('aadhar-back')">Rotate Back</button>
                    </div>
                    @endif



                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><b>Aadhar Number : </b> {{ $dm->aadhar_number }} </p>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="image-container popup-custom1">
                                        <img id="aadhar-front" src="{{ isset($dm->aadhar_card_front) ? asset('public/EV/images/aadhar/'.$dm->aadhar_card_front) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid" alt="Aadhar Front Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                        <!-- Rotation button -->
                                       
                                    </div>
                                </div>
                            
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="image-container popup-custom1">
                                        <img id="aadhar-back" src="{{ isset($dm->aadhar_card_back) ? asset('public/EV/images/aadhar/'.$dm->aadhar_card_back) : asset('public/EV/images/dummy.jpg') }}" class=" preview-image img-fluid" alt="Aadhar Back Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                        <!-- Rotation button -->
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pan Details Card -->
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3>Pan Details</h3>
                    @if($dm->pan_verify)
                    <div class="col-md-6">
                        <p><b>Verifed by : </b> <b class="text-primary">{{ ucfirst($dm->who_verify) ?? '' }}</b> </p>
                        <p><b>Verifed at:  </b>{{ $dm->pan_verify_date ? \Carbon\Carbon::parse($dm->pan_verify_date)->format('d-m-Y H:i:s') : 'N/A' }}</p>
                        <button class="btn btn-outline-info " onclick="rotateImage('pan-front')">Rotate Front</button>
                    </div>
                    @else
                    <div class="form-check form-switch" style="padding-left:0px;">
                        <!--<label class="toggle-switch" style="padding-left:0px;" for="verifiedStatusButton_{{ $dm->id }}">-->
                        <!--    <button -->
                        <!--        type="button" -->
                        <!--        onclick="status_change_alert(-->
                        <!--            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->pan_verify ? 0 : 1, 'pan_verify']) }}', -->
                        <!--            '{{ $dm->pan_verify ? 'UnVerified' : 'Verified' }} this {{$user_work_type}}?', -->
                        <!--            event-->
                        <!--        )" -->
                        <!--        class="btn btn-{{ $dm->pan_verify ? 'danger' : 'success' }} toggle-btn" -->
                        <!--        id="verifiedStatusButton_{{ $dm->id }}">-->
                        <!--        {{ $dm->pan_verify ? 'UnVerified' : 'Verified' }}-->
                        <!--    </button>-->
                        <!--</label>-->
                        <button class="btn btn-outline-info " onclick="rotateImage('pan-front')">Rotate Front</button>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><b>Pan Number : </b> {{ $dm->pan_number }} </p>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="image-container popup-custom1">
                                        <img id="pan-front" src="{{ isset($dm->pan_card_front) ? asset('public/EV/images/pan/'.$dm->pan_card_front) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid" alt="Pan Front Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                        <!-- Rotation button -->
                                        
                                    </div>
                                </div>

                                <!--<div class="col-md-6 mt-3 mt-md-0">-->
                                <!--    <img src="{{ isset($dm->pan_card_back) ? asset('public/EV/images/pan/'.$dm->pan_card_back) : asset('public/EV/images/dummy.jpg') }}" class="img-fluid" alt="Pan Back Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- License Details Card -->
        @if($dm->work_type != "" && $dm->work_type != "in-house")
            <div class="col-md-6 mt-3">
                <div class="card h-100">
                    <div class="card-body scrollable-content">
                        <h3>License Details</h3>
                        @if($dm->lisence_verify)
                        <div class="col-md-6">
                            <p><b>Verifed by : </b> <b class="text-primary">{{ ucfirst($dm->who_verify) ?? '' }}</b> </p>
                            <p><b>Verifed at:  </b> {{ $dm->lisence_verify_date ? \Carbon\Carbon::parse($dm->lisence_verify_date)->format('d-m-Y H:i:s') : 'N/A' }}</p>
                            
                             @if(empty($dm->driving_license_front) && empty($dm->driving_license_back))
                             
                            <button class="btn btn-outline-info " onclick="rotateImage('llr-image')">Rotate</button>
                            
                            @else
                            <button class="btn btn-outline-info " onclick="rotateImage('license-front')">Rotate Front</button>
                            <button class="btn btn-outline-info " onclick="rotateImage('license-back')">Rotate Back</button>
                            @endif
                        </div>
                        @else
                        <div class="form-check form-switch" style="padding-left:0px;">
                            <!--<label class="toggle-switch" style="padding-left:0px;" for="verifiedStatusButton_{{ $dm->id }}">-->
                            <!--    <button -->
                            <!--        type="button" -->
                            <!--        onclick="status_change_alert(-->
                            <!--            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->lisence_verify ? 0 : 1, 'lisence_verify']) }}', -->
                            <!--            '{{ $dm->lisence_verify ? 'UnVerified' : 'Verified' }} this {{$user_work_type}}?', -->
                            <!--            event-->
                            <!--        )" -->
                            <!--        class="btn btn-{{ $dm->lisence_verify ? 'danger' : 'success' }} toggle-btn" -->
                            <!--        id="verifiedStatusButton_{{ $dm->id }}">-->
                            <!--        {{ $dm->lisence_verify ? 'UnVerified' : 'Verified' }}-->
                            <!--    </button>-->
                            <!--</label>-->
                            @if(empty($dm->driving_license_front) && empty($dm->driving_license_back))
                             
                            <button class="btn btn-outline-info " onclick="rotateImage('llr-image')">Rotate</button>
                            
                            @else
                            <button class="btn btn-outline-info " onclick="rotateImage('license-front')">Rotate Front</button>
                            <button class="btn btn-outline-info " onclick="rotateImage('license-back')">Rotate Back</button>
                            
                            @endif
                        </div>
                        @endif
    
                        <div class="row mt-4">
                            <div class="col-md-6"></div>
                            <div class="col-md-12" style="margin-top:38px;">
                                <div class="row">
                                     <div class="col-md-12 mt-3 mt-md-0 p-4" style="margin-top:5px;">
                                    @if(!empty($dm->license_number))
                                        <p><b>License Number :</b> {{ $dm->license_number ?? ''}}</p>
                                    @elseif(!empty($dm->llr_number))
                                        <p><b>LLR Number :</b> {{ $dm->llr_number ?? '' }}</p>
                                    @else
                                        <p></p>
                                    @endif

                                        
                                    
                                    </div>
                                    <?php 
                                    
                                         $front1 = !empty($dm->driving_license_front) 
                                                ? asset('public/EV/images/driving_license/' . $dm->driving_license_front) 
                                                : null;
                                        
                                            $back1 = !empty($dm->driving_license_back) 
                                                ? asset('public/EV/images/driving_license/' . $dm->driving_license_back) 
                                                : null;
                                        
                                            $defaultImage = asset('public/EV/images/dummy.jpg');
                                    ?>
                                    
                                    @if(empty($front1) && empty($back1))
                    
                                        @php
                                            $llr_image = isset($dm->llr_image) ? asset('public/EV/images/llr_images/' . $dm->llr_image) : asset('public/EV/images/dummy.jpg');
                                            $fileUrl = $llr_image ?? null;
                                            $extension = $fileUrl ? strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION)) : null;
                                        @endphp
                    
                                        <div class="col-md-6 mt-3 mt-md-0">
                                            <div class="image-container popup-custom1">
                                                @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                                    <img id="llr-image"
                                                         src="{{ $llr_image }}"
                                                         class="preview-image img-fluid"
                                                         alt="License Front Image"
                                                         style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                                @elseif($extension === 'pdf')
                                                    <iframe src="{{ $llr_image }}"
                                                            width="250"
                                                            height="160"
                                                            style="border-radius: 10px; border: 1px solid #ccc;"></iframe>
                                                @else
                                                    <img id="llr-image"
                                                         src="{{ asset('public/EV/images/dummy.jpg') }}"
                                                         class="preview-image img-fluid"
                                                         alt="No Image Found"
                                                         style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                                @endif
                                            </div>
                                        </div>
                                    
                                    @else
                                    
                                    
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <div class="image-container popup-custom1">
                                            <img id="license-front" src="{{ isset($dm->driving_license_front) ? asset('public/EV/images/driving_license/'.$dm->driving_license_front) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid" alt="License Front Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                            <!-- Rotation button -->
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mt-3 mt-md-0">
                                        <div class="image-container popup-custom1">
                                            <img id="license-back" src="{{ isset($dm->driving_license_back) ? asset('public/EV/images/driving_license/'.$dm->driving_license_back) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid" alt="License Back Image" style="width: 250px; height: 160px; object-fit: cover; border-radius: 10px;">
                                            <!-- Rotation button -->
                                            
                                        </div>
                                    </div>
                                    
                                 @endif
                                    
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
         <!-- Bank Details Card -->
        <div class="col-md-6 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h3>Bank Details</h3>
                     @if($dm->bank_verify)
                    <div class="col-md-6">
                        <p><b>Verifed by : </b> <b class="text-primary">{{ ucfirst($dm->who_verify) ?? '' }}</b> </p>
                        <p><b>Verified at:</b> {{ $dm->bank_verify_date ? \Carbon\Carbon::parse($dm->bank_verify_date)->format('d-m-Y H:i:s') : 'N/A' }}</p>
                         <button class="btn btn-outline-info " onclick="rotateImage('bank-passbook')">Rotate</button>
                    </div>
                    @else
                    <div class="form-check form-switch" style="padding-left:0px;">
                        <!--<label class="toggle-switch" style="padding-left:0px;" for="bank_verify{{ $dm->id }}">-->
                        <!--    <button -->
                        <!--        type="button" -->
                        <!--        onclick="status_change_alert(-->
                        <!--            '{{ route('admin.Green-Drive-Ev.delivery-man.verification', [$dm->id, $dm->bank_verify ? 0 : 1, 'bank_verify']) }}', -->
                        <!--            '{{ $dm->bank_verify ? 'UnVerified' : 'Verified' }} this {{$user_work_type}}?', -->
                        <!--            event-->
                        <!--        )" -->
                        <!--        class="btn btn-{{ $dm->bank_verify ? 'danger' : 'success' }} toggle-btn" -->
                        <!--        id="bank_verify{{ $dm->id }}">-->
                        <!--        {{ $dm->bank_verify ? 'UnVerified' : 'Verified' }}-->
                        <!--    </button>-->
                        <!--</label>-->
                         <button class="btn btn-outline-info " onclick="rotateImage('bank-passbook')">Rotate</button>
                    </div>
                    @endif


                    <div class="row mt-4">
                        <div class="col-md-12 d-flex justify-content-center">
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="image-container popup-custom1">
                                    <img id="bank-passbook" src="{{ isset($dm->bank_passbook) ? asset('public/EV/images/bank_passbook/'.$dm->bank_passbook) : asset('public/EV/images/dummy.jpg') }}" class="preview-image img-fluid" alt="Bank Passbook" style="width: 350px; height: 200px;">
                                    <!-- Rotation button -->
                                </div>
                            </div>
                        </div>
                        
                            
                        
                        <div class="col-md-12 mt-3 mt-md-0 p-4" style="margin-top:38px;">
                             
                            <p><b>Account Holder Name : </b> {{ $dm->account_holder_name }} </p>
                            <p><b>Bank Name : </b> {{ $dm->bank_name }}</p>
                            <p><b>IFSC Code : </b> {{ $dm->ifsc_code }}</p>
                            <p><b>Bank Account No : </b> {{ $dm->account_number }} </p>
                        
                        </div>
                        
                        

                           
                        </div>
                    </div>
                </div>
            </div>
            
        <!--personal Details-->
        
        
            
        <div class="col-md-12 col-12 mt-3">
            <div class="card h-100">
                <div class="card-body scrollable-content">
                    <h4 class="mb-2">Leave Logs</h4>
                    <div class="row">
                            <div class="col-md-6  col-xl-3 mb-4">
                                <div class="card  border border-1" style="border-radius:10px;">
                                    <div class="card-body px-2">
                                        <a href="javascript:void(0);">
                                            <div class="row gx-1 justify-content-center align-items-center">
                                                <div class="col-4 text-center">
                                                    <i class="bi bi-person-lines-fill fs-4 p-2" style="color: #17c653;border-radius: 25%; box-shadow: 2px 3px 4px #17c653;"></i>
                                                </div>
                                                <div class="col-8">
                                                    <h5 style="color:#17c653; font-size:18px;" class="four-card mt-1">Total Leaves</h5>
                                                    <p>{{$totalLeaveDays}} Days</p>
                                                </div>
                                            </div>

                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3 mb-4">
                                <div class="card  border border-1" style="border-radius:10px;">
                                    <div class="card-body px-2">
                                        <a href="javascript:void(0);">
                                            <div class="row gx-1 justify-content-center align-items-center">
                                                <div class="col-4 text-center">
                                                    <i class="bi bi-window-dock fs-4 p-2" style="color: #17c653;border-radius: 25%; box-shadow: 2px 3px 4px #17c653;"></i>
                                                </div>
                                                <div class="col-8">
                                                    <h5 style="color:#17c653; font-size:18px;" class="four-card mt-1">Taken Leaves</h5>
                                                    <p>{{$total_taken_leaves}} Days</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6  col-xl-3 mb-4">
                                <div class="card  border border-1" style="border-radius:10px;">
                                    <div class="card-body px-2">
                                        <a href="javascript:void(0);">
                                             <div class="row gx-1 justify-content-center align-items-center">
                                                <div class="col-4 text-center">
                                                    <i class="bi bi-calendar2-range fs-4 p-2" style="color: #17c653;border-radius: 25%; box-shadow: 2px 3px 4px #17c653;"></i>
                                                </div>
                                                <div class="col-8">
                                                    <h5 style="color:#17c653; font-size:18px;" class="four-card mt-1">Balance Leaves</h5>
                                                    <p>{{$balance_leaves}} Days</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4 col-xl-3">
                                <div class="card  border border-1" style="border-radius:10px;">
                                    <div class="card-body px-2">
                                        <a href="javascript:void(0);">
                                            <div class="row gx-1 justify-content-center align-items-center">
                                                <div class="col-4 text-center">
                                                    <i class="bi bi-calendar-week fs-4 p-2" style="color: #17c653;border-radius: 25%; box-shadow: 2px 3px 4px #17c653;"></i>
                                                </div>
                                                <div class="col-8">
                                                    <h5 style="color:#17c653; font-size:18px;" class="four-card mt-1">Permissions</h5>
                                                    <p>{{$totalpermission_hr}} Hours</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                </div>
            </div>
        </div>
          
         <div class="col-md-12 col-12 mt-3">
              <div class="card h-100">
                    <div class="card-body scrollable-content">
                            <div class="d-flex justify-content-between">
                                <h4 class="mb-3">Leave Permissions</h4>
                                <form  action="{{ route('admin.Green-Drive-Ev.delivery-man.exportLeavePermissions') }}" method="GET">
                                    <input type="hidden" name="year" value="{{ date('Y') }}">
                                    <input type="hidden" name="deliveryman_id" value="{{ $dm->id }}">
                                    <button type="submit" class="btn btn-success">Excel Download</button>
                                </form>
                            </div>
                                 <div class="row">
                                    <div class="table-responsive">
                                        <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Permission Name</th>
                                          <th scope="col">Permission Date</th>
                                          <th scope="col">Start Time</th>
                                          <th scope="col">End Time</th>
                                          <th scope="col">Total Time</th>
                                          <th scope="col">Description</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                          <?php
                                            $sno = 1;
                                            // dd($get_permissions);
                                           ?>
                                          @if(isset($get_permissions) && $get_permissions->isNotEmpty())
                                                @foreach($get_permissions as $per)
                                                <tr>
                                                      <th scope="row">{{$sno++;}}</th>
                                                      <td>{{$per->leave->leave_name ?? ''}}</td>
                                                      <td>{{\Carbon\Carbon::parse($per->permission_date)->format('d-m-Y') ?? ''}}</td>
                                                      <td>{{\Carbon\Carbon::parse($per->start_time)->format('H:i:s') ?? ''}}</td>
                                                      <td>{{\Carbon\Carbon::parse($per->end_time)->format('H:i:s') ?? ''}}</td>
                                                     <td>{{ ($per->permission_hr ?? 0) . ' Hours' }}</td>
                                                     <td>{{$per->remarks ?? ''}}</td>
                                                </tr>
                                                @endforeach
                                         @else
                                            <tr class="text-center">
                                                <td colspan="6">No Permission available.</td>
                                            </tr>
                                          @endif
                                        
                                      </tbody>
                                    </table>
                                    </div>
                                </div>
                     </div>
               </div>
         </div> 
         
         <div class="col-md-12 col-12 mt-3">
              <div class="card h-100">
                    <div class="card-body scrollable-content">
                                <div class="d-flex justify-content-between">
                                    <h4 class="mb-3">Leave Days</h4>
                                    <form  action="{{ route('admin.Green-Drive-Ev.delivery-man.exportLeaveDays') }}" method="GET">
                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                        <input type="hidden" name="deliveryman_id" value="{{ $dm->id }}">
                                        <button type="submit" class="btn btn-success">Excel Download</button>
                                    </form>
                                </div>
                                 <div class="row">
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Leave Name</th>
                                          <th scope="col">Start Date</th>
                                          <th scope="col">End Date</th>
                                          <th scope="col">Apply Days</th>
                                          <th scope="col">Description</th>
                                        </tr>
                                      </thead>
                                      <tbody> 
                                       @if(isset($get_taken_leaves) && $get_taken_leaves->isNotEmpty())
                                            @foreach($get_taken_leaves as $leave)
                                                @if(optional($leave->leave)->leave_type === 'day')
                                                    <tr>
                                                        <th scope="row">{{ $loop->iteration }}</th>
                                                        <td>{{ $leave->leave->leave_name ?? '-' }}</td>
                                                        <td>{{ $leave->start_date ? \Carbon\Carbon::parse($leave->start_date)->format('d-m-Y') : '-' }}</td>
                                                        <td>{{ $leave->end_date ? \Carbon\Carbon::parse($leave->end_date)->format('d-m-Y') : '-' }}</td>
                                                        <td>{{ ($leave->apply_days ?? 0) . ' Days' }}</td>
                                                        <td>{{ $leave->remarks ?? '-' }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="6">No taken leaves available.</td>
                                            </tr>
                                        @endif

                                      </tbody>
                                    </table>
                                    </div>
                                </div>
                     </div>
               </div>
         </div>
         
         <div class="col-md-12 col-12 mt-3">
              <div class="card h-100">
                    <div class="card-body scrollable-content">
                                <div class="d-flex justify-content-between">
                                    <h4 class="mb-3">Rejected List</h4>
                                    <form  action="{{ route('admin.Green-Drive-Ev.delivery-man.exportLeaveRejectList') }}" method="GET">
                                        <input type="hidden" name="year" value="{{ date('Y') }}">
                                        <input type="hidden" name="deliveryman_id" value="{{ $dm->id }}">
                                        <button type="submit" class="btn btn-success">Excel Download</button>
                                    </form>
                                </div>
                                 <div class="row">
                                    <div class="table-responsive">
                                        <table class="table">
                                      <thead>
                                        <tr>
                                          <th scope="col">#</th>
                                          <th scope="col">Leave Name</th>
                                          <th scope="col">Start Date</th>
                                          <th scope="col">End Date</th>
                                          <th scope="col">Apply Days</th>
                                           <th scope="col">Permission Date</th>
                                          <th scope="col">Start Time</th>
                                          <th scope="col">End Time</th>
                                          <th scope="col">Permission Time</th>
                                          <th scope="col">Reject Reason</th>
                                        </tr>
                                      </thead>
                                      <tbody> 
                                      @if(isset($get_reject_list) && $get_reject_list->isNotEmpty())
                                        @foreach($get_reject_list as $reject)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $reject->leave->leave_name ?? '-' }}</td>
                                                <td>{{ isset($reject->start_date) && $reject->start_date != "" ? \Carbon\Carbon::parse($reject->start_date)->format('d-m-Y') : '-' }}</td>
                                                <td>{{ $reject->end_date ? \Carbon\Carbon::parse($reject->end_date)->format('d-m-Y') : '-' }}</td>
                                                <td>{{ $reject->apply_days ?? 0 }} Days</td>
                                                <td>{{ $reject->permission_date ? \Carbon\Carbon::parse($reject->permission_date)->format('d-m-Y') : '-' }}</td>
                                                <td>{{ $reject->start_time ? \Carbon\Carbon::parse($reject->start_time)->format('H:i:s') : '-' }}</td>
                                                <td>{{ $reject->end_time ? \Carbon\Carbon::parse($reject->end_time)->format('H:i:s') : '-' }}</td>
                                                <td>{{ $reject->permission_hr ?? 0 }} Hours</td>
                                                <td>{{ $reject->rejection_reason ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="text-center">
                                            <td colspan="10">No rejected leaves found.</td>
                                        </tr>
                                    @endif

                                      </tbody>
                                    </table>
                                    </div>
                                </div>
                     </div>
               </div>
         </div>
    </div>
        <div class="mt-4 text-end">
            <a href="{{ route('admin.Green-Drive-Ev.delivery-man.list') }}" class="btn btn-primary"><i class="bi bi-arrow-left-circle-fill"></i> Back</a>
        </div>
    </div>
    <!--<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">-->
    <!--    <div class="modal-dialog modal-dialog-centered">-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header">-->
                    <!--<h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>-->
    <!--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
    <!--            </div>-->
    <!--            <div class="modal-body text-center">-->
    <!--                <img -->
    <!--                    id="imagePreview" -->
    <!--                    src="" -->
    <!--                    class="img-fluid" -->
    <!--                    alt="Preview Image" -->
    <!--                    style="max-width: 100%; height: auto;"-->
    <!--                >-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
    <div class="show-custom1">
        <div class="overlay"></div>
        <div class="img-show-custom1">
            <span class="close-custom" title="Close">x</span>
            <span class="plus-custom" title="Zoom In">+</span>
            <span class="minus-custom" title="Zoom Out">−</span>
            <span class="reset-custom" title="Reset">⟲</span>
            <img src="">
        </div>
    </div>
    
    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     const previewImages = document.querySelectorAll('.preview-image');
        //     const modalImage = document.getElementById('imagePreview');
    
        //     previewImages.forEach(image => {
        //         image.addEventListener('click', function () {
        //             const imageSrc = this.src; // Get the clicked image's source
        //             modalImage.src = imageSrc; // Set the modal image source
        //             const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
        //             modal.show(); // Show the modal
        //         });
        //     });
        // });
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
        
      
        // Store the current rotation angle of the images
        let rotations = {
            'aadhar-front': 0,
            'aadhar-back': 0,
            'pan-front': 0,
            'license-front': 0,
            'license-back': 0,
            'llr-image': 0 ,
            'bank-passbook': 0  // Added rotation tracking for bank passbook image
        };
    
        // Function to rotate image
        function rotateImage(imageId) {
            // Get the image element
            const imgElement = document.getElementById(imageId);
    
            // Increment the current rotation angle
            rotations[imageId] = (rotations[imageId] + 90) % 360;
    
            // Apply the rotation to the image element
            imgElement.style.transform = `rotate(${rotations[imageId]}deg)`;
        }
    </script>
</x-app-layout>
