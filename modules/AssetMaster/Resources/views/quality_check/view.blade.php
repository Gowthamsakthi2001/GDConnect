<x-app-layout>
    
<style>
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        color: #fff;
        background-color: #ffffff;
        box-shadow: none !important;
    }
    .nav-pills .nav-link.active .head-text {
        color: #0000009c !important;
    }



</style>


<style>
  .qc-section {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .form-control,
  .form-select {
    height: 50px;
  }

  .btn-check:checked + .btn-outline-success {
    background-color: #198754;
    color: #fff !important;
  }

  .btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    color: #fff !important;
  }

  .qc-item {
    padding: 20px;
  }

  .qc-radio-group .btn {
    width: 100px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* ✅ Default (unchecked) buttons - black border, white bg, black text */
  .btn-outline-success,
  .btn-outline-danger {
    color: #000;
    background-color: #fff;
    border-color: #6c757d;
  }

  /* ✅ Mobile View Enhancements ONLY */
  @media (max-width: 576px) {
    .form-control,
    .form-select {
      height: 45px;
      font-size: 14px;
    }

    .qc-radio-group .btn {
      width: 90px;
      height: 36px;
      font-size: 14px;
    }

    .qc-section {
      padding: 15px;
    }

    .qc-item {
      padding: 15px;
    }
  }

    .remarks-textarea {
    height: 150px; /* Desktop default */
  }

  @media (max-width: 576px) {
    .remarks-textarea {
      height: 120px; /* Slightly smaller for phones */
    }
  }
  
  
    /* Form Label Styling */
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

/* Toggle Switch Wrapper */
.toggle-wrapper {
    padding-left: 0;
    min-height: 42px; /* Match input field height */
    display: flex;
    align-items: center;
}

/* Toggle Switch Styling */
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    cursor: pointer;
    margin-left: 0;
    margin-top: 0;
    background-color: #fff;
    border: 1px solid #ced4da;
}

.form-switch .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-switch .form-check-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Match form control height */
.form-control {
    height: 42px;
    padding: 0.375rem 0.75rem;
    font-size: 14px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Form Group Spacing */
.form-group {
    margin-bottom: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .toggle-wrapper {
        min-height: 38px;
    }
    
    .form-control {
        height: 38px;
    }
}

@media (max-width: 576px) {
    .form-label {
        font-size: 13px;
    }
    
    .form-switch .form-check-input {
        width: 2.75em;
        height: 1.4em;
    }
}

</style>

    <div class="main-content">
       
        <div class="card my-4">
            <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>
                                
                                @php
                                    $technician_name = 'N/A';
                                    $profile_path = asset('admin-assets/img/person.png');
                                
                                    if (!empty($datas->technician)) {
                                        $technician = \App\Models\User::find($datas->technician);
                                        $technician_name = $technician->name ?? 'N/A';
                                        $profile_path = $technician?->profile_photo_path
                                            ? asset('uploads/users/' . $technician->profile_photo_path)
                                            : asset('images/default-user.png');
                                    } elseif (!empty($datas->dm_id)) {
                                        $technician = \Modules\Deliveryman\Entities\Deliveryman::find($datas->dm_id);
                                        $technician_name = trim(($technician->first_name ?? '') . ' ' . ($technician->last_name ?? ''));
                                        $profile_path = $technician?->photo
                                            ? asset('EV/images/photos/' . $technician->photo)
                                            : asset('images/default-user.png');
                                    }
                                @endphp
                                       

                                
                        <img src="{{ $profile_path }}" alt="Profile" width="80" height="80" style=" border-radius: 50%; object-fit: cover; border: 2px solid #f0f0f0; padding: 2px; ">
                            
                            </div>
                            <div class="px-3">
                                <div class="h4 fw-bold mt-2">{{$technician_name ?? '' }}</div>
                                    <div class="d-flex flex-nowrap align-items-center gap-4 small text-secondary mt-2 w-100">
                                        
                                        <!-- QC ID -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <i class="bi bi-card-list"></i>
                                        <span>QC ID: {{ $datas->id }}</span>
                                        </div>

                                        <!-- Verified By -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
      
                                        
                                        <i class="bi bi-gear-fill"></i>
                                        <span>
                                            Verified By:
                                             {{ $technician_name ?? '' }}
                                        </span>
                                        </div>

                                        <!-- Date and Time -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>Date and Time: {{ \Carbon\Carbon::parse($datas->datetime)->format('d M Y, h:i A') }}</span>
                                        </div>
                                        
                                    </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                        
                           @if(strtolower($datas->status) == 'fail')
                            <button class="btn btn-danger" id="reinitiateBtn">Reinitiate QC</button>
                            @endif
                            <a href="{{route('admin.asset_management.quality_check.list')}}" class="btn btn-dark  px-5"><i class="bi bi-arrow-left me-2"></i> Back</a>
                            

                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="card my-3">
            
            <div class="card-header" style="background:#f1f5f9;">
            <ul class="nav nav-pills row d-flex align-items-center" id="pills-tab" role="tablist">
            
                <!-- Tab 1 -->
                <li class="nav-item col-6" role="presentation">
                <button class="nav-link active w-100" id="pills-basic-information-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-basic-information"
                    type="button" role="tab" aria-controls="pills-basic-information" aria-selected="true">
                    <img src="{{asset('public/admin-assets/icons/custom/person.png')}}" alt="image">&nbsp;
                    <span class="head-text" style="color:#adb3bb;">QC Details</span>
                </button>
                </li>

                <!-- Tab 2 -->
                <li class="nav-item col-6" role="presentation">
                <button class="nav-link w-100" id="pills-query-comments-tab"
                    data-bs-toggle="pill" data-bs-target="#pills-query-comments"
                    type="button" role="tab" aria-controls="pills-query-comments" aria-selected="false">
                    <img src="{{asset('public/admin-assets/icons/custom/kyc_doc.png')}}" alt="image">&nbsp;
                    <span class="head-text" style="color:#adb3bb;">Logs/History</span>
                </button>
                
                </li>

            </ul>
            </div>

           <div  style="background:#fbfbfb;">
                <div class="tab-content" id="pills-tabContent">
                  <div class="tab-pane fade show active" id="pills-basic-information" role="tabpanel" aria-labelledby="pills-basic-information-tab" tabindex="0">
                      <div >
                         <div class="card-header" style="background:#eef2ff;">
                             <h5 style="color:#1e3a8a;" class="fw-bold">QC Details</h5>
                             <p class="mb-0" style="color:#1e3a8a;">Quality check in details</p>
                         </div>
                         <form id="reinitiateForm" enctype="multipart/form-data">
                         @csrf
                          <div  class="custom-card-body">
                            <div class="row">
                                <!-- Left: Vehicle Info -->
                                <div class="col-lg-6 col-12">
                                <div class="qc-section">
                                        <h5 class="text-body-secondary mb-3">Vehicle Information</h5>
                                    <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Vehicle Type</label>
                                        <select class="form-control bg-white custom-select2-field" name="vehicle_type" id="vehicle_type" disabled>
                                            <option value="">Select Vehicle Type</option>
                                            @if(isset($vehicle_types))
                                              @foreach($vehicle_types as $type)
                                                      <option value="{{ $type->id }}"
                                                    {{ $datas->vehicle_type == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                              @endforeach
                                              @endif
                                        </select>
                                        </div> 
                                        </div>

                                        <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Vehicle Model</label>
                                        <select class="form-control bg-white custom-select2-field" name="vehicle_model" disabled>
                                        <option value="">Select Vehicle Model</option>
                                        @if(isset($vehicles))
                                          @foreach($vehicles as $vehicle)
                                                  <option value="{{ $vehicle->id }}"
                                                {{ $datas->vehicle_model == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->vehicle_model }}
                                            </option>
                                          @endforeach
                                          @endif
                           
                                        </select>
                                        </div>
                                    </div>
                                    
                           <!--        <div class="col-md-6 mb-3">-->
                        		 <!--<div class="form-group">-->
                           <!--         <label class="input-label mb-2 ms-1">Location</label>-->
                           <!--         <select class="form-control bg-white custom-select2-field" name="location" required disabled>-->
                           <!--           <option value="">Select Location</option>-->
                           <!--            @if(isset($location))-->
                           <!--               @foreach($location as $l)-->
                           <!--                       <option value="{{ $l->id }}"-->
                           <!--                     {{ $datas->location == $l->id ? 'selected' : '' }}>-->
                           <!--                     {{ $l->name }}-->
                           <!--                 </option>-->
                           <!--               @endforeach-->
                           <!--               @endif-->
                                      
                           <!--         </select>-->
                                    
                           <!--            @error('location')-->
                           <!--                     <div class="text-danger">{{ $message }}</div>-->
                           <!--                 @enderror-->
                                            
                        			<!-- </div>-->
                           <!--       </div>-->
                           
                           
                                                               <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1 d-block" for="accountability_type">
                                                Accountability Type <span class="text-danger fw-bold">*</span>
                                            </label>
                                    
                                            <div class="d-flex flex-wrap gap-3">
                                                @if(isset($types))
                                                    @foreach($types as $type)
                                                        <div class="form-check form-check-inline">
                                                            <input 
                                                                class="form-check-input accountability-radio" 
                                                                type="radio" 
                                                                name="accountability_type" 
                                                                id="accountability_type_{{ $type->id }}" 
                                                                value="{{ $type->id }}"
                                                                disabled
                                                                {{ $datas->accountability_type == $type->id ? 'checked' : '' }} 
                                                            >
                                                            <label class="form-check-label" for="accountability_type_{{ $type->id }}">
                                                                {{ $type->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
            
            
            
                                    <div class="col-md-6 mb-3 {{ $datas->accountability_type == 2 ? '' : 'd-none' }}" id="CustomerSection">
                            		 <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="customer_id">Customer<span class="text-danger fw-bold">*</span></label>
                                        <select class="form-control bg-white custom-select2-field" id="customer_id" name="customer_id"  disabled>
                                          <option value="">Select Customer</option>
                                          @if(isset($customers))
                                          @foreach($customers as $customer)
                                          <option value="{{$customer->id}}" {{ $datas->customer_id == $customer->id ? 'selected' : '' }}>{{$customer->trade_name}}</option>
                                          @endforeach
                                          @endif
                                        </select>
            
                            			 </div>
                                      </div>
                                      
                                    <div class="col-md-6 mb-3">
                            		 <div class="form-group">
                                        <label class="input-label mb-2 ms-1" for="city_id">City<span class="text-danger fw-bold">*</span></label>
                                        <select class="form-control bg-white custom-select2-field" id="city_id" name="location" onchange="getZones(this.value)" disabled>
                                          <option value="">Select City</option>
                                          @if(isset($cities))
                                          @foreach($cities as $l)
                                          <option value="{{$l->id}}" {{ $datas->location == $l->id ? 'selected' : '' }}>{{$l->city_name}}</option>
                                          @endforeach
                                          @endif
                                        </select>
                                        
                                           @error('location')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                
                            			 </div>
                                      </div>
                                      
                                      
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="zone_id">Zone <span class="text-danger fw-bold">*</span></label>
                                            
                                                <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" disabled id="zone_id" name="zone_id">
                                                    <option value="">Select a city first</option>
                                                  
                                                </select>
                                          
                                        </div>
                                    </div>
                                  
                                  
                                  
                                  
                                  
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Chassis Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="{{$datas->chassis_number}}" name="chassis_number" placeholder="Enter Chassis Number">
                                    </div>
                                        </div>
                                        
                                        
                                <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">Is Recoverable</label>
                                            <div class="form-check form-switch toggle-wrapper">
                                                <input class="form-check-input" type="checkbox" role="switch" id="isRecoverable" name="is_recoverable" disabled value="1" {{ $datas->is_recoverable == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
    
                                        
                                        
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Battery Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="{{$datas->battery_number}}" name="battery_number" placeholder="Enter Battery Number">
                                    </div>
                                    </div>
                                    
                                    
                                            <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Telematics Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="{{$datas->telematics_number}}" name="telematics_number" placeholder="Enter Telematics Number">
                                    </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Motor Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="{{$datas->motor_number}}" name="motor_number" placeholder="Enter Motor Number">
                                    </div>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">QC Date and Time</label>
                                        <input type="datetime-local" readonly class="form-control" value="{{$datas->datetime}}" name="datetime" id="qcDateTime">
                                    </div>
                                        </div>
                                        
                                        <input type="hidden" name="qc_id" value="{{$datas->id}}">
                                        
                                        
                                     @php
                                        $imagePath = $datas->image ?? '';
                                        $fullPath = 'EV/images/quality_check/' . $imagePath;
                                        $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                        $hasFile = $imagePath && file_exists(public_path($fullPath));
                                        $isPDF = $hasFile && \Illuminate\Support\Str::endsWith($imagePath, '.pdf');
                                        $imageSrc = (!$hasFile || $isPDF) ? $defaultImage : asset($fullPath);
                                        $pdfSrc = ($isPDF) ? asset($fullPath) : '';
                                    @endphp

                                    <div class="col-md-12 mb-4 my-4">
                                        <div class="preview-container border rounded shadow overflow-hidden position-relative"
                                             style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                    
                                            <!--@if($datas->status == 'pass')-->
                                                <button type="button" class="btn btn-sm btn-danger position-absolute"
                                                        style="top: 10px; right: 10px; z-index: 10;"
                                                        onclick="resetPreview('qc_Image', 'imageInput')">
                                                    ✖
                                                </button>
                                            <!--@endif-->
                                    
                                            <img id="qc_Image"
                                                 src="{{ $imageSrc }}"
                                                 alt="Quality Check Image"
                                                 class="img-fluid rounded shadow border"
                                                 style="max-height: 300px; object-fit: cover; {{ $isPDF ? 'display: none;' : '' }}"
                                                
                                                     onclick="OpenImageModal('{{ $imageSrc }}')"
                                                
                                                 >
                                    
                                            <!--<iframe id="qc_PDF"-->
                                            <!--        src="{{ $pdfSrc }}"-->
                                            <!--        style="width: 100%; height: 100%; {{ !$isPDF ? 'display: none;' : '' }} border: none;"-->
                                            <!--        frameborder="0"></iframe>-->
                                                    
                                             @if($pdfSrc)
                                                    <div class="pdf-preview-wrapper position-relative w-100 h-100">
                                                        <iframe id="qc_PDF"
                                                                src="{{ $pdfSrc }}"
                                                                class="w-100 h-100 border-0"
                                                                style="pointer-events: none; border-radius: 0.5rem;">
                                                        </iframe>
                                        
                                                        <div class="position-absolute top-0 start-0 w-100 h-100"
                                                             style="cursor: pointer; background: transparent;"
                                                             onclick="OpenImageModal('{{ $pdfSrc }}')">
                                                        </div>
                                                    </div>
                                                @endif
                
                                                @if(!$pdfSrc)
                                                    <div class="position-absolute top-0 start-0 w-100 h-100"
                                                         style="cursor: pointer; background: transparent;"
                                                         onclick="OpenImageModal('{{ $imageSrc }}')">
                                                    </div>
                                                @endif
                                        </div>
                                    </div>

                                    
                                    {{-- Image Upload Field --}}
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="file" name="file" class="form-control" id="imageInput"  onchange="showImagePreview(this, 'qc_Image')" disabled readonly accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                    </div>

                                    
                                    
                                    <div class="mb-4">
                                <label class="text-body-secondary mb-2">QC Result</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="result" id="qcPass" value="pass" disabled required {{ $datas->status == 'pass' ? 'checked' : '' }}  >
                                    <label class="form-check-label" for="qcPass">Pass</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="result" id="qcFail" value="fail" disabled required {{ $datas->status == 'fail' ? 'checked' : '' }} >
                                    <label class="form-check-label" for="qcFail">Fail</label>
                                    </div>
                            </div>
                            </div>
                            
                            <div class="row {{ in_array(strtolower($datas->status), ['pass', 'qc_pending']) ? 'd-none' : '' }}" id="remarksRow" >
                            <div class="col-12">
                                <label class="input-label mb-2 ms-1">Remarks</label>
                                
                                <textarea class="form-control bg-white remarks-textarea" placeholder="Enter Remarks" id="remarks" name="remarks" readonly>{{ $datas->remarks ?? '' }}</textarea>
                            </div>
                            </div>
                            
                            
                             <div class="col-12 d-none" id="submitsection">
                                <button type="submit" class="btn btn-success w-100 mb-2" id="submitbtn">
                                  Submit QC
                                </button>
                              </div>
                      



                                    <!-- Buttons -->
                                    <!-- Button Container -->


                                    </div>
                                </div>
                                </div>

                                <!-- Right: QC Checklist -->
                                <div class="col-lg-6 col-12">
                                  <div class="qc-section">
                                    <h5 class="text-body-secondary mb-1">QC Checklist</h5>
                                    <p style="color:gray;">Inspection Checklist</p>
                                    <div id="qcChecklistContainer">
                                      <p class="text-muted">Please select a vehicle type to view checklist.</p>
                                    </div>
                                  </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                        
                        
                        </form>
                        
                      </div>
                   </div>
                   
                   
                   
                   
                   

                  <!--Queries Tab-->
                  <div class="tab-pane fade" id="pills-query-comments" role="tabpanel" aria-labelledby="pills-query-comments-tab" tabindex="0">
                        <div class="card">
                                <div class="card-header" style="background:#edfcff;">
                                    <h5 style="color:#5e1b1b;" class="fw-bold">Logs / History</h5>
                                    <p class="mb-0" style="color:#5e1b1b;">Logs and History of QC inspections</p>
                                </div>
                                <div class="card-body custom-card-body">
                                     <div class="row">
                                         <div class="col-12 my-4 text-center">
                                             <h5 class="fw-bold">QC ID : {{$datas->id}}</h5>
                                            
                                         </div>
                                    @if(isset($initiate_values) && $initiate_values->count() > 0)
                                            @foreach($initiate_values as $item)
                                        
                                                @php
                                                    
                                                     $initiated_by = 'N/A';

                                                      if (!empty($item->initiated_by)) {
                                                            $user = \App\Models\User::find($item->initiated_by);
                                                            $initiated_by = $user->name ?? 'N/A';
                                                
                                                    } elseif (!empty($item->dm_id)) {
                                                            $dm = \Modules\Deliveryman\Entities\Deliveryman::find($item->dm_id);
                                                            $initiated_by = trim(($dm->first_name ?? '') . ' ' . ($dm->last_name ?? '')) ?: 'N/A';
                                                
                                                        }
                                                    
                                                    $status = strtolower($item->status);
                                                    $isFail = $status === 'fail';
                                                    $isPass = $status === 'pass';
                                                    $isUpdated = $status === 'updated';
                                                       $buttonClass = match (true) {
                                                        $isFail => 'btn-danger',
                                                        $isPass => 'btn-success',
                                                        $isUpdated => 'btn-info', 
                                                        default => 'btn-secondary',
                                                    };
                                                    $statusText = ucfirst($status); // "Fail" or "Pass"
                                                @endphp
                                        
                                                <div class="col-12 mb-4">
                                                    <div class="p-3 rounded shadow-sm bg-white">
                                                        <!-- Header Section -->
                                                        <div class="d-flex justify-content-between flex-wrap">
                                                            <div class="d-flex flex-column p-4">
                                                                <h6 class="mb-0 fw-semibold text-secondary">
                                                                    Inspected By: {{ $initiated_by ?? '' }}
                                                                </h6>
                                                                <p class="mb-0 text-muted small">
                                                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') }}
                                                                </p>
                                                            </div>
                                                            @if($isFail || $isPass || $isUpdated)
                                                                <div>
                                                                    <button class="btn btn-sm {{ $buttonClass }}">{{ $statusText }}</button>
                                                                </div>
                                                            @endif
                                                        </div>
                                        

                                                        <div class="mt-3">
                                                                @php
                                                                    $remarks = trim($item->remarks);
                                                                    $lines = preg_split('/\r\n|\r|\n/', $remarks);
                                                                    $userRemarks = [];
                                                                    $updatedFields = [];
                                                            
                                                                    foreach ($lines as $line) {
                                                                        $line = trim($line);
                                                            
                                                                        if (stripos($line, 'Updated Fields:') !== false) {
                                                                            $fields = trim(substr($line, strpos($line, 'Updated Fields:') + strlen('Updated Fields:')));
                                                                            $updatedFields = array_map('trim', explode(',', $fields));
                                                                        } else {
                                                                            $line = preg_replace('/^\d+\)\s*/', '', $line);
                                                                            if (!empty($line)) {
                                                                                $userRemarks[] = $line;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                            
                                                            <div class="border p-3 rounded bg-light-subtle">
                                                                <div class="text-muted mb-0">
                                                                    @if(count($userRemarks))
                                                                        <div class="mb-2">
                                                                            <i class="fas fa-comment-dots text-primary me-1"></i>
                                                                            <strong>Remarks:</strong>
                                                                            {{ implode('; ', $userRemarks) }}.
                                                                        </div>
                                                                    @endif
                                                            
                                                                    @if(count($updatedFields))
                                                                        <div class="mt-2">
                                                                            <i class="fas fa-pen-to-square text-success me-1"></i>
                                                                            <strong>Updated Fields:</strong>
                                                                            {{ implode(', ', $updatedFields) }} were modified during this reinitiation.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                            </div>



                                                    </div>
                                                </div>
                                        
                                            @endforeach
                                            
                                            @else
                                                <div class="col-12 text-center text-muted mt-4">
                                                  <p class="fs-6">
                                                <i class="bi bi-clock-history me-2"></i> No QC history available.
                                                  </p>
                                            
                                                </div>
                                        @endif
                                    </div>
                                </div>
                        </div>
                  </div>
                  

                  
                </div>
           </div>
            
        </div>
        

    </div>
    
    
        <!--Image Preview Section-->
    
    
 @include('assetmaster::asset_master.action_popup_modal') 
   
@section('script_js')


<script>
    
    function edit_candidate() {
        $(".edit-candidate-btn").each(function () {
            $(this).addClass("d-none").removeClass("d-block");
        });
    
        $(".update-candidate").each(function () {
            $(this).addClass("d-block").removeClass("d-none");
        });
        
        $("input").attr("readonly", false);
    }
    
    function update_candidate() {
        $(".update-candidate").each(function () {
            $(this).addClass("d-none").removeClass("d-block");
        });
    
        $(".edit-candidate-btn").each(function () {
            $(this).addClass("d-block").removeClass("d-none");
        });
        
        $("input").attr("readonly", true);
    }

    
    $(document).ready(function () {
        // Initial state: Show Edit Candidate, hide Save and Cancel
        $('.update-candidate').addClass('d-none');
        $('.edit-candidate').removeClass('d-none');

        // Edit button click
        // $('.edit-candidate').on('click', function (e) {
        //     e.preventDefault();
        //     $('.edit-candidate').addClass('d-none');
        //     $('.update-candidate').removeClass('d-none');
        // });

        // // Save Changes or Cancel button click
        // $('.update-candidate').on('click', function (e) {
        //     if ($(this).hasClass('btn-success') || $(this).hasClass('border-gray')) {
        //         $('.update-candidate').addClass('d-none');
        //         $('.edit-candidate').removeClass('d-none');
        //     }
        // });
    });
</script>


<script>

function show_imagefunction(input,src){
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(src).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

   function UpdateCandidateStatus(type){
      if(type == "on_hold" || type == "rejected"){
          $("#RemarkSection").addClass("d-block").removeClass("d-none");
          var remark_type = type == "on_hold" ? 'on_hold' : 'rejected';
          $("#remark_type").val(remark_type);
      }else{
          $("#RemarkSection").addClass("d-none").removeClass("d-block");
          $("#remark_type").val('');
      }
   }
   
   
   
   function showImagePreview(input, elementBaseID) {
    const file = input.files[0];
    const imgPreview = document.getElementById(elementBaseID);
    const pdfPreview = document.getElementById(elementBaseID.replace("Image", "PDF"));

    if (file) {
        const fileType = file.type;

        const reader = new FileReader();
        reader.onload = function (e) {
            if (fileType === "application/pdf") {
                pdfPreview.src = e.target.result;
                pdfPreview.style.display = "block";
                imgPreview.style.display = "none";
            } else {
                imgPreview.src = e.target.result;
                imgPreview.style.display = "block";
                pdfPreview.style.display = "none";
            }
        };
        reader.readAsDataURL(file);
    }
}

function resetPreview(elementBaseID, inputID) {
    const imgPreview = document.getElementById(elementBaseID);
    const pdfPreview = document.getElementById(elementBaseID.replace("Image", "PDF"));
    const fileInput = document.getElementById(inputID);

    imgPreview.src = "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}";
    imgPreview.style.display = "block";
    pdfPreview.src = "";
    pdfPreview.style.display = "none";

    fileInput.value = "";
}

</script>

<script>
    $(document).ready(function () {
        $('#reinitiateBtn').on('click', function () {
            // Enable all disabled inputs, selects, and textareas
            $('input:disabled, select:disabled, textarea:disabled').prop('disabled', false);

 $('input[type="radio"]').prop('checked', false);
            // Remove readonly attribute from inputs and textareas
            $('input[readonly], textarea[readonly]').removeAttr('readonly');
             $('#submitsection').removeClass('d-none').addClass('d-block');
        });
    });
</script>

<script>
    $(document).ready(function () {
        function toggleQCSections() {
            const isPass = $('#qcPass').is(':checked');
            const isFail = $('#qcFail').is(':checked');

            if (isPass) {
                $('#remarksRow').addClass('d-none');
                $('#remarks').val('');
            } else if (isFail) {
                $('#remarksRow').removeClass('d-none');
            }
        }

        // Initial call to set visibility based on pre-checked value
        toggleQCSections();

        // On change of radio buttons
        $('#qcPass, #qcFail').on('change', function () {
            toggleQCSections();
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#reinitiateBtn').on('click', function () {
            // Enable input for image
            $('#imageInput').prop('disabled', false).removeAttr('readonly');

            $('#remarksRow').addClass('d-none');

            // Enable all fields (as before)
            $('input:disabled, select:disabled, textarea:disabled').prop('disabled', false);
            $('input[readonly], textarea[readonly]').removeAttr('readonly');

            // Show submit section
            $('#submitsection').removeClass('d-none').addClass('d-block');

            // Uncheck radio buttons
            $('input[type="radio"]').prop('checked', false);
        });

        // Live Image Preview
        $('#imageInput').on('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#previewImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>



<script>
    $(document).ready(function () {
   let qcChecklistData = @json(json_decode($datas->check_lists ?? '{}'));

if (typeof qcChecklistData === 'string') {
    qcChecklistData = JSON.parse(qcChecklistData);
}
        let allowEdit = false; // Default: readonly mode

        function loadChecklist(vehicleTypeId, checklistData = {}, editable = false) {
            const qcContainer = $('#qcChecklistContainer');
            qcContainer.html('<p class="text-muted">Loading checklist...</p>');

            if (vehicleTypeId) {
                $.ajax({
                    url: '/admin/asset-management/quality-check/get-qc-checklist',
                    type: 'GET',
                    data: { vehicle_type_id: vehicleTypeId },
                    success: function (response) {
                        if (response.length > 0) {
                            let html = '';
                            response.forEach((item) => {
                                const qcId = item.id;
                                const selected = checklistData[qcId]; // e.g. "ok", "not_ok"
                                const disabledAttr = editable ? '' : 'disabled';

                                html += `
                                    <div class="qc-item mb-3" data-id="${qcId}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>${item.label_name}</span>
                                            <div class="qc-radio-group d-flex gap-2">
                                                <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-ok" value="ok" autocomplete="off" ${selected === 'ok' ? 'checked' : ''}  ${disabledAttr}>
                                                <label class="btn btn-outline-success" for="qc-${qcId}-ok">Ok</label>

                                                <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-notok" value="not_ok" autocomplete="off" ${selected === 'not_ok' ? 'checked' : ''} ${disabledAttr}>
                                                <label class="btn btn-outline-danger" for="qc-${qcId}-notok">Not Ok</label>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            qcContainer.html(html);
                        } else {
                            qcContainer.html('<p class="text-muted text-center">No checklist found for this vehicle type.</p>');
                        }
                    },
                    error: function () {
                        qcContainer.html('<p class="text-danger">Failed to load checklist. Try again later.</p>');
                    }
                });
            } else {
                qcContainer.html('<p class="text-muted">Please select a vehicle type to view checklist.</p>');
            }
        }

        // Load on page load (readonly mode)
        const initialVehicleType = $('#vehicle_type').val();
        if (initialVehicleType) {
            loadChecklist(initialVehicleType, qcChecklistData, allowEdit);
        }

        // On vehicle type change — always reload, keep current edit state
        $('#vehicle_type').on('change', function () {
             if (!allowEdit) return;
             
            const selectedType = $(this).val();
            loadChecklist(selectedType, {}, allowEdit);
        });

        // Enable editing on reinitiate
        $('#reinitiateBtn').on('click', function () {
            allowEdit = true;
            const currentType = $('#vehicle_type').val();
            loadChecklist(currentType, qcChecklistData, allowEdit);
        });
        
        
        
    });
</script>
<script>
$(document).ready(function () {
    $('#reinitiateForm').on('submit', function (e) {
        e.preventDefault(); // prevent default form submit

        let form = $(this)[0];
        let formData = new FormData(form);
         let $submitBtn = $('#submitbtn');

        // Disable button and change text
        $submitBtn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{ route('admin.asset_management.quality_check.reinitiate') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
           
           
                 if (response.success) {
                    toastr.success(response.message);
                    setTimeout(function () {
                        window.location.href = "{{ route('admin.asset_management.quality_check.list') }}";
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Unexpected error.');
                }
                
            },
            error: function (xhr) {
                 $submitBtn.prop('disabled', false).text('Submit QC');
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.error || 'Something went wrong. Please try again.');
                }
            }
        });
    });
});



let scale = 1;
let rotation = 0;
let currentFileUrl = '';
let currentType = ''; 

function OpenImageModal(fileUrl) {
    currentFileUrl = fileUrl;
    const isPDF = fileUrl.toLowerCase().endsWith('.pdf');

    scale = 1;
    rotation = 0;
    updateImageTransform();

    if (isPDF) {
        $("#kyc_image").hide();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").hide(); // Hide image tools for PDF
        $("#kyc_pdf").attr("src", fileUrl).show();
        currentType = 'pdf';
    } else {
        $("#kyc_pdf").hide();
        $("#kyc_image").attr("src", fileUrl).show();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").show();
        currentType = 'image';
    }

    $("#downloadBtn").off("click").on("click", function () {
        const link = document.createElement("a");
        link.href = currentFileUrl;
        link.download = currentFileUrl.split('/').pop();
        link.click();
    });

    $("#BKYC_Verify_view_modal").modal("show");
}

function zoomIn() {
    if (currentType !== 'image') return;
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (currentType !== 'image') return;
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    if (currentType !== 'image') return;
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    if (img) {
        img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
    }
}


function getZones(CityID) {
        let ZoneDropdown = $('#zone_id');
        ZoneDropdown.empty().append('<option value="">Loading...</option>');

           
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">--Select Zone--</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                        });
                        
                        const selectedZone = "{{ $datas->zone_id ?? '' }}";
                        if (selectedZone) {
                        ZoneDropdown.val(selectedZone).trigger('change');
                        }
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
            // ZoneWrapper.hide();
        }
       
       
    }
    
    $(document).ready(function () {
    const existingCity = "{{ $datas->zone_id ?? '' }}";
        if (existingCity) {
            getZones(existingCity);
        }
    });
    
    
    
        
    
       $(document).ready(function() {
    function toggleCustomerSection() {
        let selectedVal = $('input[name="accountability_type"]:checked').val();
        if (selectedVal == '2') {
            $('#CustomerSection').removeClass('d-none').hide().fadeIn(200);
        } else {
            $('#CustomerSection').fadeOut(200, function() {
                $(this).addClass('d-none');
                $('#customer_id').val('').trigger('change');
            });
        }
    }

    // Run on page load
    toggleCustomerSection();

    // On change of radio
    $('input[name="accountability_type"]').on('change', function() {
        toggleCustomerSection();
    });
});

</script>
@if(isset($datas->status) && strtolower($datas->status) == 'qc_pending')
<script>
     let qcChecklistData = @json(json_decode($datas->check_lists ?? '{}'));
        let allowEdit = true; // Default: readonly mode

        function loadChecklistData(vehicleTypeId, checklistData = {}, editable = false) {
            const qcContainer = $('#qcChecklistContainer');
            qcContainer.html('<p class="text-muted">Loading checklist...</p>');

            if (vehicleTypeId) {
                $.ajax({
                    url: '/admin/asset-management/quality-check/get-qc-checklist',
                    type: 'GET',
                    data: { vehicle_type_id: vehicleTypeId },
                    success: function (response) {
                        if (response.length > 0) {
                            let html = '';
                            response.forEach((item) => {
                                const qcId = item.id;
                                const selected = checklistData[qcId]; // e.g. "ok", "not_ok"
                                const disabledAttr = editable ? '' : 'disabled';

                                html += `
                                    <div class="qc-item mb-3" data-id="${qcId}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>${item.label_name}</span>
                                            <div class="qc-radio-group d-flex gap-2">
                                                <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-ok" value="ok" autocomplete="off" ${selected === 'ok' ? 'checked' : ''}  ${disabledAttr}>
                                                <label class="btn btn-outline-success" for="qc-${qcId}-ok">Ok</label>

                                                <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-notok" value="not_ok" autocomplete="off" ${selected === 'not_ok' ? 'checked' : ''} ${disabledAttr}>
                                                <label class="btn btn-outline-danger" for="qc-${qcId}-notok">Not Ok</label>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            qcContainer.html(html);
                        } else {
                            qcContainer.html('<p class="text-muted text-center">No checklist found for this vehicle type.</p>');
                        }
                    },
                    error: function () {
                        qcContainer.html('<p class="text-danger">Failed to load checklist. Try again later.</p>');
                    }
                });
            } else {
                qcContainer.html('<p class="text-muted">Please select a vehicle type to view checklist.</p>');
            }
        }
    
    $(document).ready(function () {
        // Enable input for image
        const qcChecklistData = @json(json_decode($datas->check_lists ?? '{}'));
        $('#imageInput').prop('disabled', false).removeAttr('readonly');

        // Enable all fields
        $('input:disabled, select:disabled, textarea:disabled').prop('disabled', false);
        $('input[readonly], textarea[readonly]').removeAttr('readonly');

        // Uncheck all dynamically named QC radio buttons
        $('input[name^="qc["]').prop('checked', false);

        // Show submit section
        $('#submitsection').removeClass('d-none').addClass('d-block');
        
       let allowEdit= true
        const currentType = $('#vehicle_type').val();
        loadChecklistData(currentType, qcChecklistData, allowEdit);

        $('#vehicle_type').on('change', function () {
        
             
        const selectedType = $(this).val();
            loadChecklistData(selectedType, {}, allowEdit);
        });
        // Uncheck other radio buttons if any
        // $('input[type="radio"]').prop('checked', false);
    });
</script>
@endif





@endsection
</x-app-layout>
