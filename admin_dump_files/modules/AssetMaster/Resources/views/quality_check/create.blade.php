<x-app-layout>
    
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
    background-color: #ffff;
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
       

        <!-- Header -->
        
        <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.quality_check.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Quality Control Inspection
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end d-none">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.asset_management.quality_check.list')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>
            
        


                <form id="qualityCheckForm"  enctype="multipart/form-data">
                    @csrf
                  <div class="row">
                    <!-- Left: Vehicle Info -->
                    <div class="col-lg-6 col-12">
                      <div class="qc-section">
                            <h5 class="text-body-secondary mb-3">Vehicle Information</h5>
                        <div class="row g-3">
                         <div class="col-md-6 mb-3">
                		 <div class="form-group">
                			  <label class="input-label  mb-2 ms-1">Vehicle Type</label>
                			  <select class="form-control bg-white custom-select2-field" name="vehicle_type" id="vehicle_type" required>
                				<option value="">Select Vehicle Type</option>
                              @if(isset($vehicle_types))
                              @foreach($vehicle_types as $type)
                              <option value="{{$type->id}}">{{$type->name}}</option>
                              @endforeach
                              @endif
                			  </select>
                			      @error('vehicle_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                			 </div> 
                			</div>
                
                              <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">Vehicle Model</label>
                            <select class="form-control bg-white custom-select2-field" name="vehicle_model" >
                              <option value="">Select Vehicle Model</option>
                              @if(isset($vehicles))
                              @foreach($vehicles as $vehicle)
                              <option value="{{$vehicle->id}}">{{$vehicle->vehicle_model}}</option>
                              @endforeach
                              @endif
                            </select>
                             @error('vehicle_model')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    
                			 </div>
                          </div>
                          
                          
                   <!--    <div class="col-md-6 mb-3">-->
                		 <!--<div class="form-group">-->
                   <!--         <label class="input-label mb-2 ms-1">Location</label>-->
                   <!--         <select class="form-control bg-white custom-select2-field" name="location" >-->
                   <!--           <option value="">Select Location</option>-->
                   <!--           @if(isset($location))-->
                   <!--           @foreach($location as $l)-->
                   <!--           <option value="{{$l->id}}">{{$l->name}}</option>-->
                   <!--           @endforeach-->
                   <!--           @endif-->
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
                                                    {{ $type->id == 1 ? 'checked' : '' }}
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



                        <div class="col-md-6 mb-3 d-none" id="CustomerSection">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="customer_id">Customer<span class="text-danger fw-bold">*</span></label>
                            <select class="form-control bg-white custom-select2-field" id="customer_id" name="customer_id" >
                              <option value="">Select Customer</option>
                              @if(isset($customers))
                              @foreach($customers as $customer)
                              <option value="{{$customer->id}}">{{$customer->trade_name}}</option>
                              @endforeach
                              @endif
                            </select>

                			 </div>
                          </div>
                          
                          
                    
                        <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="city_id">City<span class="text-danger fw-bold">*</span></label>
                            <select class="form-control bg-white custom-select2-field" id="city_id" name="location" onchange="getZones(this.value)">
                              <option value="">Select City</option>
                              @if(isset($cities))
                              @foreach($cities as $l)
                              <option value="{{$l->id}}">{{$l->city_name}}</option>
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
                                
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="zone_id" name="zone_id">
                                        <option value="">Select a city first</option>
                                      
                                    </select>
                              
                            </div>
                        </div>
                        
                          
                        <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">Chassis Number</label>
                            <input type="text" class="form-control bg-white" placeholder="Enter Chassis Number" name="chassis_number" required>
                        
                          </div>
                		     </div>
                		     
                		     
                		                  <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Is Recoverable</label>
                                <div class="form-check form-switch toggle-wrapper">
                                    <input class="form-check-input" type="checkbox" role="switch" id="isRecoverable" name="is_recoverable" value="1">
                                </div>
                            </div>
                        </div>
                            			 
                			 
                           <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">Battery Number</label>
                            <input type="text" class="form-control bg-white" placeholder="Enter Battery Number" name="battery_number" >
                                    @error('battery_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                          </div>
                		   </div>
                		   
                		   
                                <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">Telematics Number</label>
                            <input type="text" class="form-control bg-white" placeholder="Enter Telematics Number" name="telematics_number" required>
                                @error('telematics_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    
                          </div>
                		  </div>
                         <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">Motor Number</label>
                            <input type="text" class="form-control bg-white" placeholder="Enter Motor Number" name="motor_number" >
                                   @error('motor_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    
                          </div>
                		  	  </div>
                		  	  
                		  	  
                   <!--          <div class="col-md-6 mb-3">-->
                		 <!--<div class="form-group">-->
                   <!--         <label class="input-label mb-2 ms-1">Controller Number</label>-->
                   <!--         <input type="text" class="form-control bg-white" placeholder="Enter Controller Number">-->
                   <!--       </div>-->
                		 <!--  </div>-->
                		   
                		   
                            <div class="col-md-6 mb-3">
                		 <div class="form-group">
                            <label class="input-label mb-2 ms-1">QC Date and Time</label>
                            <input type="datetime-local" class="form-control" id="qcDateTime" name="datetime" >
                             @error('datetime')
                                        <div class="text-danger">{{ $message }}</div>
                              @enderror
                                    
                          </div>
                		     </div>
                			 
                			 
                    <!--      <div class="col-md-6 mb-3">-->
                		  <!-- <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1">Technician/Supervisor</label>-->
                    <!--        <input type="text" class="form-control bg-white" placeholder="Technician 001">-->
                    <!--      </div>-->
                		  <!--</div>-->
                		  
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative"
                                 style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                 
                                <button type="button" class="btn btn-sm btn-danger position-absolute"
                                        style="top: 10px; right: 10px; z-index: 10;"
                                        onclick="resetPreview('qc_Image', 'fileInput')">
                                    ✖
                                </button>
                        
                                <img id="qc_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Quality Check Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;"
                                     onclick="OpenImageModal(this.src)">
                        
                                <iframe id="qc_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                                        		  
                		   <div class="col-md-12">
                		   <div class="form-group">
                            <input type="file" class="form-control" name="file" onchange="showImagePreview(this, 'qc_Image')" id="fileInput" accept=".jpg,.jpeg,.png,.pdf">
                            @error('image')
                                        <div class="text-danger">{{ $message }}</div>
                              @enderror
                              
                          </div>
                		  </div>
                		  
                		  
                		 <div class="mb-4">
                         <label class="text-body-secondary mb-2">QC Result</label>
                          <div class="d-flex align-items-center gap-4">
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="result"
                                id="qcPass"
                                value="pass"
                              >
                              <label class="form-check-label" for="qcPass">Pass</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input
                                class="form-check-input"
                                type="radio"
                                name="result"
                                id="qcFail"
                                value="fail"
                              >
                              <label class="form-check-label" for="qcFail">Fail</label>
                            </div>
                          </div>
                           @error('result')
                                        <div class="text-danger">{{ $message }}</div>
                              @enderror
                          
                        </div>
                        
                        
                        
                        <div class="row" id="remarksRow" style="display: none;">
                          <div class="col-12">
                            <label class="input-label mb-2 ms-1">Remarks</label>
                            <textarea class="form-control bg-white remarks-textarea" placeholder="Enter Remarks" name="remarks"></textarea>
                          </div>
                        </div>
                
                

                    <!-- Buttons -->
                    <!-- Button Container -->
                    <div class="row mt-3" id="buttonContainer">
                      <!-- Submit QC -->
                      <div class="col-12 d-none" id="submitCol">
                        <button type="submit" class="btn btn-success w-100 mb-2" id="submitbtn1">
                          Submit QC
                        </button>
                      </div>
                    
                      <!-- Side-by-side buttons (shown only when Fail is selected) -->
                      <!--<div class="col-12 d-none" id="submitColHalf">-->
                      <!--  <button type="submit" class="btn btn-success w-100" id="submitbtnHalf">-->
                      <!--    Submit QC-->
                      <!--  </button>-->
                      <!--</div>-->
                      <!--<div class="col-6 d-none" id="initCol">-->
                      <!--  <button type="button" class="btn btn-danger w-100" id="initbtn">-->
                      <!--    ReInitiate QC-->
                      <!--  </button>-->
                      <!--</div>-->
                    </div>

                    </div>
                  </div>
                </div>



                    <!-- Right: QC Checklist -->
                    <div class="col-lg-6 col-12">
                      <div class="qc-section">
                        <h5 class="text-body-secondary mb-1">QC Checklist</h5>
                        <p style="color:gray;">Inspection Checklist</p>
                        <div id="qcChecklistContainer">
                          <p class="text-muted text-center">Please select a vehicle type to view checklist.</p>
                        </div>
                      </div>
                    </div>

          </div>


    </form>
      


    </div>


@section('script_js')



<script>
  const qcPass = document.getElementById("qcPass");
  const qcFail = document.getElementById("qcFail");

  const submitCol = document.getElementById("submitCol");
//   const submitColHalf = document.getElementById("submitColHalf");
//   const initCol = document.getElementById("initCol");
  const remarksRow = document.getElementById("remarksRow");

  qcPass.addEventListener("change", () => {
    if (qcPass.checked) {
      submitCol.classList.remove("d-none");        
    //   submitColHalf.classList.add("d-none");        
         remarksRow.style.display = "none";
    }
  });

  qcFail.addEventListener("change", () => {
    if (qcFail.checked) {
          submitCol.classList.remove("d-none");         // Full-width Submit QC
    //   submitColHalf.classList.add("d-none");    // Show 50% Submit
      remarksRow.style.display = "block";
    }
  });



    function getCurrentDateTimeLocal() {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000; // adjust for local timezone
    const localISOTime = new Date(now - offset).toISOString().slice(0, 16);
    return localISOTime;
  }

  // Set it as the value of the input
  document.getElementById("qcDateTime").value = getCurrentDateTimeLocal();
</script>

 <script>
     $(document).ready(function () {
    $('#vehicle_type').change(function () {
        var vehicleTypeId = $(this).val();
        var qcContainer = $('#qcChecklistContainer');

        qcContainer.html('<p class="text-muted">Loading checklist...</p>');

        if (vehicleTypeId) {
            $.ajax({
              url: '/admin/asset-management/quality-check/get-qc-checklist',
                type: 'GET',
                data: { vehicle_type_id: vehicleTypeId },
                success: function (response) {
                    
                    if (response.length > 0) {
                        let html = '';
                        response.forEach((item, index) => {
                            const radioName = 'qc_' + item.id;
                             const qcId = item.id;
                            html += `
                              <div class="qc-item mb-3" data-id="${qcId}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>${item.label_name}</span>
                                        <div class="qc-radio-group d-flex gap-2">
                                            <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-ok" value="ok" autocomplete="off">
                                            <label class="btn btn-outline-success" for="qc-${qcId}-ok">Ok</label>
                        
                                            <input type="radio" class="btn-check" name="qc[${qcId}]" id="qc-${qcId}-notok" value="not_ok" autocomplete="off">
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
    });
});

 </script>
 
 
 <script>
$(document).ready(function () {
    $('#qualityCheckForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this)[0];
        let formData = new FormData(form);
        let $submitBtn = $('#submitbtn1');

        // Disable button and change text
        $submitBtn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{ route('admin.asset_management.quality_check.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                toastr.success('Quality check submitted successfully!');

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = "{{ route('admin.asset_management.quality_check.list') }}";
                }, 1500);
            },
            error: function (xhr) {
                   $submitBtn.prop('disabled', false).text('Submit QC');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        toastr.error(val[0]);
                    });
                } else {
                      toastr.error(xhr.responseJSON?.error || 'Something went wrong. Please try again.');
                      
                }
            }
        });
    });
});
</script>
<script>
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


 @endsection
</x-app-layout>
