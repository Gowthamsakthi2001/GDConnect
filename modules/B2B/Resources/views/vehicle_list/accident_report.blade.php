@extends('layouts.b2b')
@section('css')

<style>
.attachment-preview {
    border: 1px dashed #ccc;
    border-radius: 8px;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    position: relative;
    background-color: #fdfdfd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.attachment-preview:hover {
    border-color: #007bff;
    background-color: #f9f9f9;
}

.preview-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    cursor: pointer;
    transition: transform 0.2s;
    border-radius: 4px;
}

.preview-image:hover {
    transform: scale(1.02);
}

.preview-pdf {
    width: 100%;
    height: 100%;
    border: none;
}

.d-none {
    display: none !important;
}
#uploadBox_1 .upload-box {
  height: 200px; /* fixed space */
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
}

.upload-box.dynamic {
  height: auto; /* shrink/grow with content */
  min-height: 200px; /* safety */
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
}

</style>
@endsection

@section('content')
<div class="main-content">

        <!-- Header Section -->
        <div class="mb-4">
            <div class="p-3 rounded" style="background:#fbfbfb;">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <!-- Title -->
                    <h5 class="m-0 text-truncate custom-dark">
                        Report Accident
                    </h5>
                    
        
                    <!-- Back Button -->
                    <a href="{{ route('b2b.vehiclelist') }}" 
                       class="btn btn-dark btn-md mt-2 mt-md-0">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

            
            <div class="card">
               
                <div class="card-body">
                <form id="accidentReportForm" action="{{ route('b2b.accident-report_functionality') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- <div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="accident_report">Accident Report Id <span style="color: red;">*</span></label>-->
                        <!--        <input type="text" class="form-control bg-white" name="accident_report_id" id="accident_report"  placeholder="Req-2025-08-13-5455"  required>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="date_time_request">Date and Time of the Request</label>
                                <input type="datetime-local" class="form-control bg-white" name="datetime" id="datetime" style="padding:12px 20px;" value="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        
                        <input type="hidden" class="form-control bg-white" name="id" value="{{ $data['id']}}">
                         <input type="hidden" class="form-control bg-white" name="rider_id" id="rider_id" value="{{ $data['rider']['id']}}">
    
                        <!--  <div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="DOA">Date Of Accident <span style="color: red;">*</span></label>-->
                        <!--        <input type="date" class="form-control bg-white" name="date_of_accident" id="DOA"  placeholder="DD / MM / YYYY" required>-->
                        <!--    </div>-->
                        <!--</div>-->


                        <!--  <div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="time">Time Of Accident <span style="color: red;">*</span></label>-->
                        <!--        <input type="time" class="form-control bg-white" name="time_of_accident" id="time"  placeholder="Enter Time" required>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="Location">Location Of Accident <span style="color: red;">*</span></label>
                                <input type="text" class="form-control bg-white" name="location_of_accident" id="Location"  placeholder="Enter Location" required>
                            </div>
                        </div>
                        
                       
                        
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="type">Accident Type <span style="color: red;">*</span> </label>
                                <select class="form-control bg-white" name="accident_type" id="type" required>
                                    <option value="">Select Accident Type</option>
                                    <option value="Collision">Collision</option>
                                    <option value="Fall">Fall</option>
                                    <option value="Fire">Fire</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="description">Description <span style="color: red;">*</span></label>
                                <textarea class="form-control bg-white" name="description" id="description" rows="6" placeholder="Enter Description" required></textarea>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle No. <span style="color: red;">*</span></label>
                                <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{ $data['vehicle']['permanent_reg_number']}}" placeholder=" Select Vehicle ID" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number <span style="color: red;">*</span></label>
                                <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{ $data['vehicle']['chassis_number']}}" placeholder="Enter Chassis Number" readonly>
                            </div>
                        </div>
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="rider_id">Rider ID <span style="color: red;">*</span> </label>-->
                        <!--        <input type="text" class="form-control bg-white" name="rider_id" id="rider_id"  placeholder="Select Rider ID" required >-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="rider_name">Rider Name <span style="color: red;">*</span> </label>
                                <input type="text" class="form-control bg-white" name="rider_name" id="rider_name" value="{{ $data['rider']['name']}}" placeholder="Enter Rider Name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="rider_contact_number">Rider Contact Number <span style="color: red;">*</span> </label>
                                <input type="text" class="form-control bg-white phone-input" name="rider_contact_number" id="rider_contact_number" value="{{ $data['rider']['mobile_no']}}" placeholder="Enter Rider Contact Number" readonly>
                            </div>
                        </div>
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="rider_license_number">Rider License Number <span style="color: red;">*</span></label>-->
                        <!--        <input type="text" class="form-control bg-white" name="rider_license_number" id="rider_license_number" value="{{ $data['rider']['driving_license_number']}}" placeholder="Enter Rider Lisense Number" readonly>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        @php
                            $licenseNumber = $data['rider']['driving_license_number'] ?? $data['rider']['llr_number'] ?? null;
                            $label = !empty($data['rider']['driving_license_number']) ? 'Driving License Number' : (!empty($data['rider']['llr_number']) ? 'LLR Number' : '');
                            $inputName = !empty($data['rider']['driving_license_number']) ? 'rider_license_number' : (!empty($data['rider']['llr_number']) ? 'rider_llr_number' : '');
                        @endphp
                        
                        @if($licenseNumber)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="{{ $inputName }}">
                                        {{ $label }} <span style="color: red;">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        class="form-control bg-white" 
                                        name="{{ $inputName }}" 
                                        id="{{ $inputName }}" 
                                        value="{{ $licenseNumber }}" 
                                        placeholder="Enter {{ $label }}" 
                                        readonly
                                    >
                                </div>
                            </div>
                        @endif

                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_damage">Damage to Vehicle <span style="color: red;">*</span></label>
                                <select class="form-control bg-white" name="vehicle_damage" id="vehicle_damage" required>
                                    <option value="">Select Accident Type</option>
                                    <option value="Minor">Minor</option>
                                    <option value="Moderate">Moderate</option>
                                    <option value="Severe">Severe</option>
                                    <option value="Total Loss">Total Loss</option>
                                </select>
                            </div>
                        </div>
                    
                    </div>    
                        
                   <div class="row">    
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch d-flex align-items-center">
                                <label class="form-check-label mb-0 me-5" for="riderInjuries">Any Rider Injuries </label>
                                <input class="form-check-input" type="checkbox" id="riderInjuries" name="" >
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3 d-none" id="RiderinjuryDescriptionBox">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="Rider_injury_description">Description</label>
                                <textarea class="form-control bg-white" name="rider_injury_description" id="Rider_injury_description" rows="6" placeholder="Enter Description"></textarea>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class='row'>
                     <div class="col-md-6 mb-3">
                            <div class="form-check form-switch d-flex align-items-center">
                                <label class="form-check-label mb-0 me-5" for="thirdPartyInjuries">Any Third Party Injuries</label>
                                <input class="form-check-input" type="checkbox" id="thirdPartyInjuries" name="">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3 d-none" id="ThirdPartyInjuryDescriptionBox">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="third_party_injury_description">Description</label>
                                <textarea class="form-control bg-white" name="third_party_injury_description" id="Third_party_injury_description" rows="6" placeholder="Enter Description"></textarea>
                            </div>
                        </div>
                    </div>  
                    
                    
                    <?php  $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg'); ?>
                    
               <div class="row">
                  <div class="container mt-4">
                    <div class="row" id="uploadContainer">
                          <label class="input-label mb-2 ms-1" for="accident_attachments">Accident Photos / Videos <span style="color: red;">*</span>  (Note :Must Cover all sides of vehicle Photos)</label>
                        <!-- Default upload box -->
                        <div class="col-md-6" id="uploadBox_1">
                            <div class="upload-box border rounded bg-light text-center d-flex justify-content-center align-items-center"
                                 style="border: 2px dashed #ccc; height: 200px; cursor: pointer;
                                        background-size: cover; background-repeat: no-repeat; background-position: center;">
                            </div>
                            <input class="form-control mt-2 file-input" type="file" accept=".pdf,.jpg,.jpeg,.png,.mp4,.mov,.avi,.webm" name="accident_attachments[]" id="accident_attachments" required>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" id="addBtn" style="width:100px">Add</button>
                        <button type="button" class="btn btn-danger" id="removeBtn">Remove</button>
                    </div>
                </div>
                    
                    
             
                   <!-- Police Report upload -->
                        <div class="container mt-4">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Police Report / FIR Copy <span style="color: red;">*</span></label>
                                <div id="policeReportPreviewBox" class="preview-container" style="height: 400px;">
                                    <!-- Default content will be set by JavaScript -->
                                </div>
                                <input class="form-control mt-2 file-input" type="file" id="policeReportFileInput" accept=".pdf,.jpg,.jpeg,.png" name="police_report" required>
                            </div>
                        </div>    
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client_business_name">Client Business Name</label>
                                <input type="text" class="form-control bg-white" name="client_business_name" id="client_name" value="{{ $data['rider']['customerLogin']['customer_relation']['name']}}" placeholder="Flipkart" readonly>
                            </div>
                      </div>
                      <!--<div class="col-md-6 mb-3">-->
                      <!--      <div class="form-group">-->
                      <!--          <label class="input-label mb-2 ms-1" for="contact_person_name">Contact Person Name</label>-->
                      <!--          <input type="text" class="form-control bg-white" name="contact_person_name" id="contact_person_name" placeholder="Enter Contact Person Name">-->
                      <!--      </div>-->
                      <!--</div>-->
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="contact_number">Contact Number</label>
                                <input type="text" class="form-control bg-white phone-input" name="contact_number" id="contact_number" value="{{ $data['rider']['customerLogin']['customer_relation']['phone'] }}" placeholder="Enter Contact Number" readonly>
                            </div>
                      </div>
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="contact_email">Contact Email</label>
                                <input type="text" class="form-control bg-white" name="contact_email" id="contact_email" value="{{ $data['rider']['customerLogin']['customer_relation']['email'] }}" placeholder="Enter Contact Email" readonly>
                            </div>
                      </div>
                      
                      <!--<div class="col-md-12 d-flex align-items-center">-->
                      <!--    <input type="checkbox" id="confirm" class="form-check-input me-2" name="terms_condition" required>-->
                      <!--    <label for="confirm" class="form-check-label small text-nowrap">-->
                      <!--      I confirm the details provided are correct to the best of my knowledge.-->
                      <!--    </label>-->
                      <!--</div>-->
                      
                      
                      <div class="col-12 mb-4">
                            <div class="d-flex align-items-start flex-wrap">
                                <input 
                                    type="checkbox" 
                                    id="confirm" 
                                    class="form-check-input me-2 mt-1" 
                                    name="terms_condition" 
                                    required
                                >
                        
                                <label for="confirm" class="form-check-label small">
                                    I confirm the details provided are correct to the best of my knowledge.
                                </label>
                            </div>
                        </div>


                       <div class="col-md-12 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                      </div>
                     
                     
                     </form>
                     
                </div>  
                                            
</div>

    

@endsection

@section('js')

<script>
    // Rider Injuries toggle
    const riderInjuriesToggle = document.getElementById('riderInjuries');
    const riderDescriptionBox = document.getElementById('RiderinjuryDescriptionBox');

    riderInjuriesToggle.addEventListener('change', function () {
        if (this.checked) {
            riderDescriptionBox.classList.remove('d-none'); // show
        } else {
            riderDescriptionBox.classList.add('d-none'); // hide
        }
    });

    // Third Party Injuries toggle
    const thirdPartyInjuriesToggle = document.getElementById('thirdPartyInjuries');
    const thirdPartyDescriptionBox = document.getElementById('ThirdPartyInjuryDescriptionBox');

    thirdPartyInjuriesToggle.addEventListener('change', function () {
        if (this.checked) {
            thirdPartyDescriptionBox.classList.remove('d-none'); // show
        } else {
            thirdPartyDescriptionBox.classList.add('d-none'); // hide
        }
    });
</script>

<script>
  const defaultImage = "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}";

  const uploadContainer = document.getElementById("uploadContainer");
  const addBtn = document.getElementById("addBtn");
  const removeBtn = document.getElementById("removeBtn");

  let uploadCount = 1;
  const maxUploads = 15;

  // Common preview handler
  function showPreview(file, boxDiv) {
    boxDiv.innerHTML = "";
    boxDiv.style.backgroundImage = "none";

    if (!file) {
      // reset to default - use cover instead of contain
      boxDiv.innerHTML = "";
      boxDiv.style.backgroundImage = `url('${defaultImage}')`;
      boxDiv.style.backgroundSize = "cover";
      boxDiv.style.backgroundRepeat = "no-repeat";
      boxDiv.style.backgroundPosition = "center";
      return;
    }

    if (file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = function(e) {
        boxDiv.style.backgroundImage = `url('${e.target.result}')`;
        boxDiv.style.backgroundSize = "cover";
        boxDiv.style.backgroundRepeat = "no-repeat";
        boxDiv.style.backgroundPosition = "center";
      }
      reader.readAsDataURL(file);
    } 
    else if (file.type === "application/pdf") {
      const iframe = document.createElement("iframe");
      iframe.src = URL.createObjectURL(file);
      iframe.style.width = "100%";
      iframe.style.height = "100%";
      boxDiv.appendChild(iframe);
    } 
    else if (file.type.startsWith("video/")) {
      const video = document.createElement("video");
      video.src = URL.createObjectURL(file);
      video.controls = true;
      video.style.width = "100%";
      video.style.height = "100%";
      video.style.objectFit = "contain";
      boxDiv.appendChild(video);
    } 
    else {
      alert("Unsupported file type!");
    }
  }

  function attachFilePreview(fileInput, boxDiv) {
    // default image - use cover instead of contain
    boxDiv.style.backgroundImage = `url('${defaultImage}')`;
    boxDiv.style.backgroundSize = "cover";
    boxDiv.style.backgroundRepeat = "no-repeat";
    boxDiv.style.backgroundPosition = "center";
    boxDiv.innerHTML = "";

    fileInput.addEventListener("change", function() {
      showPreview(this.files[0], boxDiv);
    });
  }

  function createUploadBox() {
    if (uploadCount >= maxUploads) {
      alert("You can only add up to 15 uploads.");
      return;
    }

    uploadCount++;
    const colDiv = document.createElement("div");
    colDiv.classList.add("col-md-6");
    colDiv.setAttribute("id", `uploadBox_${uploadCount}`);
     colDiv.style.marginBottom = "20px";

    colDiv.innerHTML = `
      <div class="upload-box border rounded bg-light text-center dynamic"
           style="border: 2px dashed #ccc; cursor: pointer;
                  background-image: url('${defaultImage}');
                  background-size: cover; background-repeat: no-repeat; background-position: center;">
      </div>
      <input class="form-control mt-2 file-input" type="file" accept=".pdf,.jpg,.jpeg,.png,.mp4,.mov,.avi,.mkv,.webm" name="accident_attachments[]">
    `;

    uploadContainer.appendChild(colDiv);

    attachFilePreview(colDiv.querySelector(".file-input"), colDiv.querySelector(".upload-box"));
  }

  function removeUploadBox() {
    if (uploadCount > 1) {
      const lastBox = document.getElementById(`uploadBox_${uploadCount}`);
      if (lastBox) {
        lastBox.remove();
        uploadCount--;
      }
    } else {
      alert("Default upload box cannot be removed.");
    }
  }

  function toggleRemoveBtn() {
    removeBtn.style.display = (uploadCount > 1) ? "inline-block" : "none";
  }

  // Init first accident upload box
  attachFilePreview(
    document.querySelector("#uploadBox_1 .file-input"),
    document.querySelector("#uploadBox_1 .upload-box")
  );

  addBtn.addEventListener("click", () => {
    createUploadBox();
    toggleRemoveBtn();
  });

  removeBtn.addEventListener("click", () => {
    removeUploadBox();
    toggleRemoveBtn();
  });

  toggleRemoveBtn();

  // Police Report Upload
  const policeReportPreviewBox = document.getElementById("policeReportPreviewBox");
  const policeReportFileInput = document.getElementById("policeReportFileInput");

  // Set default image for police report - use cover instead of contain
  policeReportPreviewBox.style.backgroundImage = `url('${defaultImage}')`;
  policeReportPreviewBox.style.backgroundSize = "cover";
  policeReportPreviewBox.style.backgroundRepeat = "no-repeat";
  policeReportPreviewBox.style.backgroundPosition = "center";
  policeReportPreviewBox.innerHTML = "";

  // Set up click event to trigger file input
  policeReportPreviewBox.addEventListener("click", function() {
    policeReportFileInput.click();
  });

  // Handle file selection for police report
  policeReportFileInput.addEventListener("change", function() {
    showPreview(this.files[0], policeReportPreviewBox);
  });

  // Function to open image in modal
  function openImageModal(src) {
    // Your modal implementation here
    console.log("Opening image modal with src:", src);
    // Example: $('#imageModal').modal('show'); and set modal image src
  }
  

      
</script>

    <!--phone number-->
    
    <script>
    function sanitizeAndValidatePhone(input) {
        // Ensure the input starts with '+91'
        if (!input.value.startsWith('+91')) {
            input.value = '+91' + input.value.replace(/^\+?91/, '');
        }
    
        // Allow only digits after '+91'
        input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, '');
    
        // Limit the total length to 13 characters (including '+91')
        if (input.value.length > 13) {
            input.value = input.value.substring(0, 13);
        }
    }
    
    // Apply to all inputs with class "phone-input"
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".phone-input").forEach(function(input) {
            input.addEventListener("input", function() {
                sanitizeAndValidatePhone(this);
            });
        });
    });
    </script>

<script>
$(document).ready(function () {
    $('#accidentReportForm').on('submit', function (e) {
        e.preventDefault();

        // Show loading state
        let submitBtn = $('.btn-submit');
        submitBtn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...'
        );

        // Prepare FormData
        let formData = new FormData(this);

        // Collect file inputs (accident_attachments + police report)
        // $('.file-input').each(function () {
        //     if (this.files[0]) {
        //         if ($(this).attr('name') === 'accident_attachments[]') {
        //             // Multiple accident attachments
        //             for (let i = 0; i < this.files.length; i++) {
        //                 formData.append('accident_attachments[]', this.files[i]);
        //             }
        //         } else {
        //             // Single police report
        //             formData.append($(this).attr('name'), this.files[0]);
        //         }
        //     }
        // });

        $.ajax({
            url: "{{ route('b2b.accident-report_functionality') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                submitBtn.prop('disabled', false).html('Submit Report Accident');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message ?? 'Accident report submitted successfully!',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = "{{ route('b2b.vehiclelist') }}";
                });
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).html('Submit Report Accident');

                if (xhr.status === 422) {
                    // Validation errors
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';

                    for (let field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage,
                        confirmButtonColor: '#3085d6'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            }
        });
    });
});
</script>



@endsection