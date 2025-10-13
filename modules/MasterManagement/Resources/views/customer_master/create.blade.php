<x-app-layout>
<style>
.form-check-input {
    width: 20px;
    height: 20px;
    accent-color: #007bff; /* blue when checked */
    margin-top: 0.3rem;
}

.form-check-label {
    font-size: 1rem;
    margin-left: 0.4rem;
}

    /* Main single selection style */
.select2-container--default .select2-selection--single {
    border: none !important;
    border-bottom: 1px solid #ced4da !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    height: 38px !important; /* match Bootstrap */
    padding-left: 0.5rem !important;
    padding-right: 0.5rem !important;
    background-color: #fff !important;
    display: flex;
    align-items: center;
}

/* Arrow alignment */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 10px;
}

/* Text alignment */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    padding-left: 0 !important;
}

/* On focus border color (optional) */
.select2-container--default.select2-container--focus .select2-selection--single {
    border-bottom: 2px solid #3b82f6 !important; /* blue on focus */
}
table, tbody, tfoot, thead, tr, th, td {
    border: none !important;
}

table thead th {
    text-align: center !important;
    background: white !important;
    color: black !important;
}

</style>


  
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Create Customer Master
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="StoreCustomerMasterForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                
                       
                        <div class="col-12 mb-4">
                            <div class="form-group d-flex align-items-center flex-wrap">
                                <label class="me-3 mb-0 fw-semibold text-nowrap">
                                    Customer Type <span class="text-danger">*</span>
                                </label>
                                
                                <div class="form-check form-check-inline d-flex align-items-center me-4">
                                    <input class="form-check-input me-1" type="radio" name="customer_type" id="customerTypeIndividual" value="1">
                                    <label class="form-check-label" for="customerTypeIndividual">Individual</label>
                                </div>
                        
                                <div class="form-check form-check-inline d-flex align-items-center">
                                    <input class="form-check-input me-1" type="radio" name="customer_type" id="customerTypeCompany" value="2">
                                    <label class="form-check-label" for="customerTypeCompany">Company</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3 d-none" id="businessConsTypeSction">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="businessConsType">Business Constitution Type <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="businessConsType" name="business_constutition_type">
                                        <option value="">Select</option>
                                        @if(isset($constutition_types))
                                           @foreach($constutition_types as $type)
                                              <option value="{{$type->id}}">{{$type->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start " for="businessType">Business Type <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="businessType" name="business_type">
                                        <option value="">Select</option>
                                        <option value="1">Registered</option>
                                        <option value="2">Unregistered</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                       

                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="Name" class="col-12 col-md-4 col-form-label text-start "> Company Name <span class="text-danger">*</span> </label>
                                <div class="col-12 col-md-8">
                                    <input type="text"class="form-control border-0 border-bottom rounded-0 shadow-none" name="name" id="Name" placeholder="">
                                </div>
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="Name" class="col-12 col-md-4 col-form-label text-start "> Trade  Name<span class="text-danger">*</span> </label>
                                <div class="col-12 col-md-8">
                                    <input type="text"class="form-control border-0 border-bottom rounded-0 shadow-none" name="trade_name" id="trade_name" placeholder="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="Email" class="col-12 col-md-4 col-form-label text-start "> Email ID <span class="text-danger">*</span></label>
                                <div class="col-12 col-md-8">
                                    <input type="email" class="form-control border-0 border-bottom rounded-0 shadow-none" name="email" id="Email" placeholder="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="Contact_no" class="col-12 col-md-4 col-form-label text-start "> Contact No <span class="text-danger">*</span> </label>
                                <div class="col-12 col-md-8">
                                    <input type="text" value="+91" class="form-control border-0 border-bottom rounded-0 shadow-none" name="contact_no" oninput="sanitizeAndValidatePhone(this)" id="Contact_no" placeholder="">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 my-3">
                            <h6>Company Address Details</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="address">Address <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="address" id="searchInput" placeholder="Search Address">
                                <input type="hidden" name="client_coordinate" id="zoneInput">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="City">City <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="City" name="city">
                                        <option value="">Select</option>
                                        @if(isset($cities))
                                           @foreach($cities as $city)
                                              <option value="{{$city->id}}">{{$city->city_name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
  
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="State">State <span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="State" name="state">
                                        <option value="">Select</option>
                                        @if(isset($states))
                                           @foreach($states as $state)
                                              <option value="{{$state->id}}">{{$state->state_name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label class="col-12 col-md-4 col-form-label text-start" for="pincode">Pin code/ ZIP code<span class="text-danger fw-bold">*</span></label>
                                <div class="col-12 col-md-8">
                                <input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="pincode" id="pincode" placeholder="">
                                </div>
                            </div>
                        </div>
                        
                       <div class="col-12 my-3">
                            <h6>KYC Details</h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group row">
                                <label for="gst_no" class="col-12 col-md-4 col-form-label text-start "> GST No <span class="text-danger">*</span> </label>
                                <div class="col-12 col-md-8">
                                    <input type="text" class="form-control border-0 border-bottom rounded-0 shadow-none" name="gst_no" id="gst_no" placeholder="" >
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-md-6 mb-3" id="PanNumberSection">
                            <div class="form-group row">
                                <label for="pan_no" class="col-12 col-md-4 col-form-label text-start ">PAN No <span class="text-danger">*</span> </label>
                                <div class="col-12 col-md-8">
                                    <input type="text" class="form-control border-0 border-bottom rounded-0 shadow-none" name="pan_no" id="pan_no" placeholder=""   >  
                                </div>
                            </div>
                        </div>
                        
                       
                        <div class="col-md-6 my-3">
                            <div class="form-group">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox" id="addPocCheckbox" name="add_poc_details">
                                    <label class="form-check-label h5 mb-0" for="addPocCheckbox" 
                                           title="Select this if the customer has a Point of Contact (POC) such as a representative.">
                                        Add POC Details (if applicable)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                             <table class="table d-none mb-3" id="POC_Detail_TableContainer">

                                        <colgroup>
                                            <col style="width: 30%;">
                                            <col style="width: 30%;">
                                            <col style="width: 30%;">
                                            <col style="width: 10%;">
                                        </colgroup>
                                        <thead class="bg-white">
                                            <tr>
                                                <th>POC Name</th>
                                                <th>POC Email</th>
                                                <th>POC Phone</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    <tbody id="RiderTypeTableBody">
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_name[]" placeholder="Enter Name" >
                                            </td>
                                            <td>
                                               <input type="email" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_email[]" placeholder="Enter Email" >
                                            </td>
                                            <td>
                                               <input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_phone[]" placeholder="Enter Phone" >
                                            </td>
                                            
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-sm btn-danger remove-row">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button type="button" class="btn border-gray add-new-row">Add a Line</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                         </div>
                         

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input me-2" type="checkbox" id="toggleHubsSection" name="add_hubs_details">
                                    <label class="form-check-label h5 mb-0" for="toggleHubsSection"
                                           title="Select this if the customer has operational hubs.">
                                        Add Customer Hubs - Optional list of operational hubs
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 ">
                            <table class="table d-none mb-3" id="hubDetailsTable">
                                <colgroup>
                                    <col style="width: 90%;">
                                    <col style="width: 10%;">
                                </colgroup>
                                <thead class="bg-white">
                                    <tr>
                                        <th>Hub Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="hubDetailsTableBody">
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="hub_name[]" placeholder="Enter Hub Name" >
                                        </td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-sm btn-danger hub-remove-row">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <button type="button" class="btn btn-outline-secondary hub-add-row">Add a Line</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>


                        
                        <div class="col-md-6 mb-3" id="AdhaarFrontSection">
                            <div class="form-group position-relative mb-3">
                                <label class="input-label mb-2 ms-1" for="Adhaar_front_img">Upload Adhaar Front Image <span class="text-danger fw-bold">*</span></label>
                                <input type="file" class="form-control bg-white" name="adhaar_front_img" id="Adhaar_front_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'Adhaar_front_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('Adhaar_front_Image')">
                                    ✖
                                </button>
                        
                                <img id="Adhaar_front_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Adhaar Front Image"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
    
                                <iframe id="Adhaar_front_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
    
                        
                         <div class="col-md-6 mb-3" id="AdhaarBackSection">
                            <div class="form-group position-relative mb-3">
                                <label class="input-label mb-2 ms-1" for="Adhaar_back_img">Upload Adhaar Back Image <span class="text-danger fw-bold">*</span></label>
                                <input type="file" class="form-control bg-white" name="adhaar_back_img" id="Adhaar_back_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this, 'Adhaar_back_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('Adhaar_back_Image')">
                                    ✖
                                </button>
                        
                                <img id="Adhaar_back_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Adhaar Back Image"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
    
                                <iframe id="Adhaar_back_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>

                        
                        <div class="col-md-6 mb-3" id="PanImageSection">
                            <div class="form-group position-relative mb-3">
                               <label class="input-label mb-2 ms-1" for="pan_img">Upload PAN Image <span class="text-danger fw-bold">*</span></label>
                                <input type="file" class="form-control bg-white" name="pan_img" id="pan_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'PAN_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('PAN_Image')">
                                    ✖
                                </button>
                        
                                <img id="PAN_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="PAN Image"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
    
                                <iframe id="PAN_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                    
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group position-relative mb-3">
                                <label class="input-label mb-2 ms-1" for="gst_img">Upload GST Image <span class="text-danger fw-bold">*</span></label>
                                <input type="file" class="form-control bg-white" name="gst_img" id="gst_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'GST_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('GST_Image')">
                                    ✖
                                </button>
                        
                                <img id="GST_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="GST Image"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
    
                                <iframe id="GST_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group position-relative mb-3">
                               <label class="input-label mb-2 ms-1" for="other_business_proof">Upload Other Business Proof</label>
                                <input type="file" class="form-control bg-white" name="other_business_proof" id="other_business_proof" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'other_business_proof_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('other_business_proof_Image')">
                                    ✖
                                </button>
                        
                                <img id="other_business_proof_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Other Business Proof"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
    
                                <iframe id="other_business_proof_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        
                        
                                                <div class="col-md-6 mb-3">
                            <div class="form-group position-relative mb-3">
                                <label class="input-label mb-2 ms-1" for="profile_img">Profile Image</label>
                                <input type="file" class="form-control bg-white" name="profile_img" id="profile_img"
                                       accept="image/png,image/jpeg,image/jpg,application/pdf"
                                       onchange="showImagePreview(this,'profile_Image')">
                            </div>
                        
                            <div class="preview-container border rounded shadow overflow-hidden position-relative"
                                 style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                 
                                <!-- Reset button -->
                                <button type="button" class="btn btn-sm btn-danger position-absolute"
                                        style="top: 10px; right: 10px; z-index: 10;"
                                        onclick="resetPreview('profile_Image')">
                                    ✖
                                </button>
                        
                                <!-- Image preview -->
                                <img id="profile_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Profile Proof"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
                        
                                <!-- PDF preview (hidden by default) -->
                                <iframe id="profile_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; border: none; display: none;"></iframe>
                            </div>
                        </div>


                        <!-- Company Logo -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group position-relative mb-3">
                                <label class="input-label mb-2 ms-1" for="company_logo_img">Company Logo</label>
                                <input type="file" class="form-control bg-white" name="company_logo_img" id="company_logo_img"
                                       accept="image/png,image/jpeg,image/jpg,application/pdf"
                                       onchange="showImagePreview(this,'company_logo_Image')">
                            </div>
                    
                            <div class="preview-container border rounded shadow overflow-hidden position-relative"
                                 style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                 
                                <!-- Reset button -->
                                <button type="button" class="btn btn-sm btn-danger position-absolute"
                                        style="top: 10px; right: 10px; z-index: 10;"
                                        onclick="resetPreview('company_logo_Image')">
                                    ✖
                                </button>
                    
                                <!-- Image preview -->
                                <img id="company_logo_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Company Logo"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain; display: block;">
                    
                                <!-- PDF preview (hidden by default) -->
                                <iframe id="company_logo_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; border: none; display: none;"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end gap-2">
                            <button type="button" class="btn btn-danger px-3">Reset</button>
                            <button type="submit" id="submitBtn" class="btn btn-success px-3">Submit</button>
                        </div>
               
                    </div>
                    </form>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&libraries=places"></script>

<script>
    function initAutocompleteOnly() {
        const input = document.getElementById('searchInput');
        const coordinateInput = document.getElementById('zoneInput');

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'], // Or use ['address']
            componentRestrictions: { country: "in" }, // Optional: limit to India
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                const latLng = {
                    lat: place.geometry.location.lat(),
                    lng: place.geometry.location.lng()
                };

                // Store coordinates in hidden input
                coordinateInput.value = JSON.stringify(latLng);
            }
        });
    }

    window.onload = initAutocompleteOnly;
</script>


<script>
    $(document).ready(function () {
        $('input[name="customer_type"]').on('change', function () {
            if ($(this).val() == '2') {
                // Company selected
                $('#businessConsTypeSction').removeClass('d-none');
                
                $('#pan_img').val('');
                 $('#Adhaar_front_img').val('');
                 $('#Adhaar_back_img').val('');
                 $('#pan_no').val('');
                 
                $('#PAN_Image').attr('src', "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}").show();
                $('#PAN_PDF').attr('src', '').hide();
                
                $('#Adhaar_back_Image').attr('src', "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}").show();
                $('#Adhaar_back_PDF').attr('src', '').hide();
                
                $('#Adhaar_front_Image').attr('src', "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}").show();
                $('#Adhaar_front_PDF').attr('src', '').hide();
                
                 
                $('#PanImageSection').addClass('d-none');
                $('#AdhaarBackSection').addClass('d-none');
                $('#AdhaarFrontSection').addClass('d-none');
                $('#PanNumberSection').addClass('d-none');

            } else {
                // Individual selected
                $('#businessConsTypeSction').addClass('d-none');
                $('#businessConsType').val('');
                

                 
                $('#PanImageSection').removeClass('d-none');
                $('#AdhaarBackSection').removeClass('d-none');
                $('#AdhaarFrontSection').removeClass('d-none');
                $('#PanNumberSection').removeClass('d-none');
                

            }
        });
    });

    $(document).ready(function () {
        // Toggle table visibility based on checkbox
        $('#addPocCheckbox').on('change', function () {
            if ($(this).is(':checked')) {
                $('#POC_Detail_TableContainer').removeClass('d-none');
            } else {
                $('#POC_Detail_TableContainer').addClass('d-none');
                // Clear all rows except the first one
                $('#RiderTypeTableBody').html(`
                    <tr>
                        <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_name[]" placeholder="Enter Name"></td>
                        <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_email[]" placeholder="Enter Email"></td>
                        <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_phone[]" placeholder="Enter Phone"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            }
        });

        // Add new row
        $(document).on('click', '.add-new-row', function () {
            const newRow = `
                <tr>
                    <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_name[]" placeholder="Enter Name"></td>
                    <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_email[]" placeholder="Enter Email"></td>
                    <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="poc_phone[]"  placeholder="Enter Phone"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#RiderTypeTableBody').append(newRow);
        });

        // Remove row
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('toggleHubsSection');
    const table = document.getElementById('hubDetailsTable');
    const tbody = document.getElementById('hubDetailsTableBody');
    const addBtn = document.querySelector('.hub-add-row');

    // Show/Hide table when checkbox is toggled
    checkbox.addEventListener('change', function () {
        table.classList.toggle('d-none', !this.checked);
    });

    // Add new row
    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" class="form-control bg-white form-control border-0 border-bottom rounded-0 shadow-none" name="hub_name[]" placeholder="Enter Hub Name"></td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-danger hub-remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
    });

    // Remove row
    tbody.addEventListener('click', function (e) {
        if (e.target.closest('.hub-remove-row')) {
            e.target.closest('tr').remove();
        }
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
                    // Show PDF preview
                    pdfPreview.src = e.target.result;
                    pdfPreview.style.display = "block";
                    imgPreview.style.display = "none";
                } else {
                    // Show Image preview
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = "block";
                    pdfPreview.style.display = "none";
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
    function resetPreview(elementBaseID) {
        const imgPreview = document.getElementById(elementBaseID);
        const pdfPreview = document.getElementById(elementBaseID.replace("Image", "PDF"));
        const fileInput = document.getElementById(elementBaseID.replace("_Image", "_img")); // matches input ID like 'Adhaar_back_img'
    
        // Reset previews
        imgPreview.src = "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}";
        imgPreview.style.display = "block";
        pdfPreview.src = "";
        pdfPreview.style.display = "none";
    
        // Reset file input value
        fileInput.value = "";
    
        // Trigger file input click
        fileInput.click();
    }

</script>


<script>
    $("#StoreCustomerMasterForm").submit(function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Remove previous error classes
        $(this).find('input').removeClass('is-invalid');
        
        let email = $('#Email').val().trim();
        let contact = $('#Contact_no').val().replace(/\s/g, '').trim();
        let gst = $('#gst_no').val().trim();
        let pan = $('#pan_no').val().trim();
        
        // Validate Email if not empty
        let emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
        if (email && !emailRegex.test(email)) {
            $('#Email').addClass('is-invalid');
            toastr.error('Please enter a valid Gmail ID (example@gmail.com)');
            isValid = false;
        }
        
        // Validate Contact No if not empty
        let contactRegex = /^\+91[0-9]{10}$/;
        if (contact && !contactRegex.test(contact)) {
            $('#Contact_no').addClass('is-invalid');
            toastr.error('Invalid Contact Number. Example: +919876543210');
            isValid = false;
        }
        
        // Validate GST No if not empty
        let gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        if (gst && !gstRegex.test(gst)) {
            $('#gst_no').addClass('is-invalid');
             toastr.error('Invalid GST No. Example: 22AAAAA0000A1Z5');
            isValid = false;
        }
        
        // Validate PAN No if not empty
        let panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
        if (pan && !panRegex.test(pan)) {
            $('#pan_no').addClass('is-invalid');
            toastr.error('Invalid PAN No. Example: ABCDE1234F');
            isValid = false;
        }
        
        // Stop submission if invalid
        if (!isValid) {
            return;
        }

    
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");
    
        var $submitBtn = $("#submitBtn");
        var originalText = $submitBtn.html();
        $submitBtn.prop("disabled", true).html("⏳ Submitting...");
    
        $.ajax({
            url: "{{ route('admin.Green-Drive-Ev.master_management.customer_master.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
    
                $submitBtn.prop("disabled", false).html(originalText);
    
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href="{{route('admin.Green-Drive-Ev.master_management.customer_master.index')}}";
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                $submitBtn.prop("disabled", false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Please try again.");
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

</script>
@endsection
</x-app-layout>
