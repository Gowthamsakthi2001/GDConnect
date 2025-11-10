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

</style>



    <div class="main-content">
       
        <div class="card my-4">
            <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>

                                
                               <img src="https://wallpapers.com/images/hd/professional-profile-pictures-1080-x-1080-460wjhrkbwdcp1ig.jpg" alt="Profile" width="70" height="70" style="border-radius:50%;">
                            </div>
                            <div class="px-3">
                                <div class="h4 fw-bold mt-2">Saravana</div>
                                    <div class="d-flex flex-nowrap align-items-center gap-4 small text-secondary mt-2 w-100">
                                        
                                        <!-- QC ID -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <i class="bi bi-card-list"></i>
                                        <span>QC ID: 100001</span>
                                        </div>

                                        <!-- Verified By -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <i class="bi bi-gear-fill"></i>
                                        <span>Verified By: Technician 001</span>
                                        </div>

                                        <!-- Date and Time -->
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                        <i class="bi bi-calendar-event"></i>
                                        <span>Date and Time: 20-06-2025</span>
                                        </div>
                                        
                                    </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <button class="btn btn-danger">Reinitiate QC</button>
                            <a href="{{route('admin.asset_management.quality_check.total_qc_list')}}" class="btn btn-dark  px-5"><i class="bi bi-arrow-left me-2"></i> Back</a>

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
                                        <select class="form-control bg-white" readonly>
                                            <option>Select Vehicle Type</option>
                                            <option value="2">2 Wheeler</option>
                                            <option value="3" selected>3 Wheeler</option>
                                            <option value="4">4 Wheeler</option>
                                        </select>
                                        </div> 
                                        </div>

                                        <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Vehicle Model</label>
                                        <select class="form-control bg-white" readonly>
                                        <option>Select Vehicle Model</option>
                                        <option value="ola">OLA</option>
                                        <option value="ola" selected>Ather</option>
                                        <option value="ola">Revolt</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Chassis Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="CH100001" placeholder="Enter Chassis Number">
                                    </div>
                                        </div>
                                        
                                        
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Battery Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="BTY100001" placeholder="Enter Battery Number">
                                    </div>
                                    </div>
                                    
                                    
                                            <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Telematics Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="9896686868" placeholder="Enter Telematics Number">
                                    </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Motor Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="98875875857" placeholder="Enter Motor Number">
                                    </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Controller Number</label>
                                        <input type="text" class="form-control bg-white" readonly value="0000011" placeholder="Enter Controller Number">
                                    </div>
                                    </div>
                                        <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">QC Date and Time</label>
                                        <input type="datetime-local" readonly class="form-control" value="2025-06-23T15:30" id="qcDateTime">
                                    </div>
                                        </div>
                                        
                                        
                                        
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="input-label mb-2 ms-1">Technician/Supervisor</label>
                                        <input type="text" readonly class="form-control bg-white" value="Technician 001" placeholder="Technician 001">
                                    </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="file" readonly class="form-control" placeholder="Technician 001">
                                    </div>
                                    </div>
                                    
                                    
                                    <div class="mb-4">
                            <label class="text-body-secondary mb-2">QC Result</label>
                            <div class="d-flex align-items-center gap-4">
                                <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="qcResult"
                                    id="qcPass"
                                    value="pass"
                                >
                                <label class="form-check-label" for="qcPass">Pass</label>
                                </div>
                                <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="qcResult"
                                    id="qcFail"
                                    value="fail"
                                    checked
                                >
                                <label class="form-check-label" for="qcFail">Fail</label>
                                </div>
                            </div>
                            </div>
                            <div class="row" id="remarksRow" >
                            <div class="col-12">
                                <label class="input-label mb-2 ms-1">Remarks</label>
                                <textarea class="form-control bg-white remarks-textarea" placeholder="Enter Remarks">the tenth letter of the Latin alphabet, used in the modern English alphabet, the alphabets of other western European languages and others worldwide. Its usual name in English is jay</textarea>
                            </div>
                            </div>



                                    <!-- Buttons -->
                                    <!-- Button Container -->


                                    </div>
                                </div>
                                </div>

                                <!-- Right: QC Checklist -->
                                <div class="col-lg-6 col-12">
                                <div class="qc-section">
                                        <h5 class="text-body-secondary mb-1">Qc Checklist</h5>

                                    <p style="color:gray;">Inspection Checklist</p>

                                
                                    
                                        <!-- QC Item 1 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Brake functionality test</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc1" id="qc1-ok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-success" style="color:white;" for="qc1-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc1" id="qc1-notok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-danger" for="qc1-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 2 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Battery voltage inspection</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc2" id="qc2-ok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-success" for="qc2-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc2" id="qc2-notok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-danger" for="qc2-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 3 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Controller connectivity check</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc3" id="qc3-ok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-success" for="qc3-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc3" id="qc3-notok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-danger" for="qc3-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 4 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Display unit test</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc4" id="qc4-ok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-success" for="qc4-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc4" id="qc4-notok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-danger" for="qc4-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 5 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Headlight and indicators</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc5" id="qc5-ok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-success" for="qc5-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc5" id="qc5-notok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-danger" for="qc5-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 6 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Horn operation</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc6" id="qc6-ok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-success" for="qc6-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc6" id="qc6-notok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-danger" for="qc6-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 7 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Wheel alignment check</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc7" id="qc7-ok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-success" for="qc7-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc7" id="qc7-notok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-danger" for="qc7-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 8 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Motor noise & vibration test</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc8" id="qc8-ok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-success" for="qc8-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc8" id="qc8-notok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-danger" for="qc8-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 9 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Chassis visual inspection</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc9" id="qc9-ok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-success" for="qc9-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc9" id="qc9-notok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-danger" for="qc9-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                            <!-- QC Item 10 -->
                            <div class="qc-item">
                                <div class="d-flex justify-content-between align-items-center">
                                <span>Final functional test</span>
                                <div class="qc-radio-group d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qc10" id="qc10-ok" autocomplete="off" disabled>
                                    <label class="btn btn-outline-success" for="qc10-ok">Ok</label>

                                    <input type="radio" class="btn-check" name="qc10" id="qc10-notok" autocomplete="off" checked disabled>
                                    <label class="btn btn-outline-danger" for="qc10-notok">Not Ok</label>
                                </div>
                                </div>
                            </div>

                                    <!-- Add more checklist items as needed -->

                                </div>
                                </div>
                            </div>
                        </div>
                      </div>
                   </div>
                   

                  <!--Queries Tab-->
                  <div class="tab-pane fade" id="pills-query-comments" role="tabpanel" aria-labelledby="pills-query-comments-tab" tabindex="0">
                        <div class="card">
                                <div class="card-header" style="background:#edfcff;">
                                    <h5 style="color:#5e1b1b;" class="fw-bold">Logs / History</h5>
                                    <p class="mb-0" style="color:#5e1b1b;">Logs and History of Qc inspections</p>
                                </div>
                                <div class="card-body custom-card-body">
                                     <div class="row">
                                         <div class="col-12 my-4 text-center">
                                             <h5 class="fw-bold">QC ID : QC100001</h5>
                                            
                                         </div>
                                            <div class="col-12 mb-4">
                                            <div class="p-3 rounded shadow-sm bg-white">
                                                <!-- Header Section -->
                                                <div class="d-flex justify-content-between flex-wrap">
                                            <div class="d-flex flex-column p-4">
                                            <h5 class="mb-0 fw-semibold text-secondary">Inspected By: Technician 001</h5>
                                            <p class="mb-0 text-muted small">10 May 2025, 7:45 PM</p>
                                            </div>
                                                <div>
                                                    <button class="btn btn-danger btn-sm">Fail</button>
                                                </div>
                                                </div>

                                                <!-- Remarks Textarea -->
                                                <div class="mt-3">
                                                <textarea class="form-control border h-100" rows="5" readonly>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates perspiciatis excepturi incidunt saepe et, earucumque, illo distinctio harum deleniti ipsam culpa hic.
                                                </textarea>
                                                </div>
                                            </div>
                                            </div>
                                            
                                            

                                            <div class="col-12 mb-4">
                                            <div class="p-3 rounded shadow-sm bg-white">
                                                <!-- Header Section -->
                                                <div class="d-flex justify-content-between flex-wrap">
                                            <div class="d-flex flex-column">
                                            <h5 class="mb-0 fw-semibold text-secondary">Inspected By: Technician 001</h5>
                                            <p class="mb-0 text-muted small">10 May 2025, 7:45 PM</p>
                                            </div>

                                                <div>
                                                    <button class="btn btn-danger btn-sm">Fail</button>
                                                </div>
                                                </div>

                                                <!-- Remarks Textarea -->
                                                <div class="mt-3" >
                                                <textarea class="form-control border h-100" rows="5" readonly>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Quod explicabo atque, soluta porro sed totam voluptates perspiciatis excepturi incidunt saepe et, earum cumque, illo distinctio harum deleniti ipsam culpa hic.
                                                </textarea>
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
   
   
</script>
@endsection
</x-app-layout>
