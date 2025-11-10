<x-app-layout>
    <style>
    .upload-area {
        border: 2px dashed #107980;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.3s ease;
        position: relative;
        width: 100%; /* Full width */
        height: 0; /* Setting height to 0 to maintain aspect ratio */
        padding-bottom: 40%; /* Makes the box square */
        overflow: hidden; /* Hide overflow */
    }
    
    .upload-area:hover {
        border-color:  #24bac3; /* Change color on hover */
    }
    
    .upload-content {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .upload-content p {
        margin-top: 80px;
    }
    
    .preview-img {
        display: none; /* Hide image preview by default */
        width: 100%; /* Fill the width of the box */
        height: 100%; /* Fill the height of the box */
        object-fit: cover; /* Maintain aspect ratio and cover the box */
        position: absolute; /* Position image absolutely to cover the entire box */
        top: 0;
        left: 0;
    }
    
    .upload-content p {
        z-index: 1; /* Ensure text is above the image */
        color: #999;   /* Light color for the text */
    }
    
    .text-danger {
        color: red; /* Error color */
    }
    
    .text-success {
        color: green; /* Success color */
    }
    
    .btn-grd-primary:hover{background-image: linear-gradient(310deg,#ff0080, #7928ca) !important;}
    .progress {
      height: 20px;
      border-radius: 5px;
    }
    .custom-radio input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ccc;
        border-radius: 50%;
        position: relative;
        outline: none;
        cursor: pointer;
        transition: border-color 0.3s;
    }

    /* Style the radio button when checked */
    .custom-radio input[type="radio"]:checked {
        border-color: transparent;
        background: linear-gradient(310deg, #17c653, #0d8a3f);
    }

    /* Add an inner circle to show selection */
    .custom-radio input[type="radio"]:checked::before {
        content: "";
        position: absolute;
        top: 2px;
        left: 2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
    }

</style>
    <div class="container mt-5">
        <h2 class="mb-4">EV Asset Master Vehicle</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.asset_master_vehicle_store')}}" method="POST" enctype="multipart/form-data">
            @csrf <!-- CSRF token for Laravel -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Reg_No" class="form-label">Registration Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Reg_No" name="Reg_No" 
                           value="{{ old('Reg_No') }}" placeholder="Enter Registration Number" required>
                </div>
                <div class="col-md-6">
                    <label for="Model" class="form-label">Model <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Model" name="Model" 
                           value="{{ old('Model') }}" placeholder="Enter Model" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Manufacturer" class="form-label">Manufacturer <span class="text-danger">*</span> </label>
                    <input type="text" class="form-control" id="Manufacturer" name="Manufacturer" 
                           value="{{ old('Manufacturer') }}" placeholder="Enter Manufacturer">
                </div>
                <div class="col-md-6">
                    <label for="Original_Motor_ID" class="form-label">Original Motor ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Original_Motor_ID" name="Original_Motor_ID" 
                           value="{{ old('Original_Motor_ID') }}" placeholder="Enter Original Motor ID">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Chassis_Serial_No" class="form-label">Chassis Serial Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="Chassis_Serial_No" name="Chassis_Serial_No" 
                           value="{{ old('Chassis_Serial_No') }}" placeholder="Enter Chassis Serial Number">
                </div>
                <div class="col-md-6">
                    <label for="Purchase_order_ID" class="form-label">Purchase Order ID <span class="text-danger">*</span> </label>
                    <input type="number" class="form-control" id="Purchase_order_ID" name="Purchase_order_ID" 
                           value="{{ old('Purchase_order_ID') }}" placeholder="Enter Purchase Order ID">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Warranty_Kilometers" class="form-label">Warranty Kilometers</label>
                    <input type="number" class="form-control" id="Warranty_Kilometers" name="Warranty_Kilometers" 
                           value="{{ old('Warranty_Kilometers') }}" placeholder="Enter Warranty Kilometers">
                </div>
                <div class="col-md-6">
                    <label for="Hub" class="form-label">Hub</label>
                    <input type="text" class="form-control" id="Hub" name="Hub" 
                           value="{{ old('Hub') }}" placeholder="Enter Hub">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Client" class="form-label">Client</label>
                    <input type="text" class="form-control" id="Client" name="Client" 
                           value="{{ old('Client') }}" placeholder="Enter Client">
                </div>
                <div class="col-md-6">
                    <label for="Colour" class="form-label">Colour</label>
                    <input type="text" class="form-control" id="Colour" name="Colour" 
                           value="{{ old('Colour') }}" placeholder="Enter Colour">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Asset_In_Use_Date" class="form-label">Asset In Use Date</label>
                    <input type="date" class="form-control" id="Asset_In_Use_Date" name="Asset_In_Use_Date" 
                           value="{{ old('Asset_In_Use_Date',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label for="Deployed_To" class="form-label">Deployed To</label>
                    <input type="text" class="form-control" id="Deployed_To" name="Deployed_To" 
                           value="{{ old('Deployed_To') }}" placeholder="Enter Deployed To">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Emp_ID" class="form-label">Employee ID</label>
                    <input type="text" class="form-control" id="Emp_ID" name="Emp_ID" 
                           value="{{ old('Emp_ID') }}" placeholder="Enter Employee ID">
                </div>
                <div class="col-md-6">
                    <label for="Procurement_Lease_Start_Date" class="form-label">Procurement Lease Start Date</label>
                    <input type="date" class="form-control" id="Procurement_Lease_Start_Date" name="Procurement_Lease_Start_Date" 
                           value="{{ old('Procurement_Lease_Start_Date',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Lease_Rental_End_Date" class="form-label">Lease Rental End Date</label>
                    <input type="date" class="form-control" id="Lease_Rental_End_Date" name="Lease_Rental_End_Date" 
                           value="{{ old('Lease_Rental_End_Date',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label for="PO_Description" class="form-label">PO Description</label>
                    <textarea class="form-control" id="PO_Description" name="PO_Description" 
                              placeholder="Enter PO Description">{{ old('PO_Description') }}</textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Registration_Type" class="form-label">Registration Type</label>
                    <input type="text" class="form-control" id="Registration_Type" name="Registration_Type" 
                           value="{{ old('Registration_Type') }}" placeholder="Enter Registration Type">
                </div>
                <div class="col-md-6">
                    <label for="Ownership_Type" class="form-label">Ownership Type</label>
                    <input type="text" class="form-control" id="Ownership_Type" name="Ownership_Type" 
                           value="{{ old('Ownership_Type') }}" placeholder="Enter Ownership Type">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Lease_Value" class="form-label">Lease Value</label>
                    <input type="number" class="form-control" id="Lease_Value" name="Lease_Value" 
                           value="{{ old('Lease_Value') }}" placeholder="Enter Lease Value">
                </div>
                <div class="col-md-6">
                    <label for="AMS_Location" class="form-label">AMS Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="AMS_Location" name="AMS_Location" 
                           value="{{ old('AMS_Location') }}" placeholder="Enter AMS Location">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Parking_Location" class="form-label">Parking Location</label>
                    <input type="text" class="form-control" id="Parking_Location" name="Parking_Location" 
                           value="{{ old('Parking_Location') }}" placeholder="Enter Parking Location">
                </div>
                <div class="col-md-6">
                    <label for="Asset_Status" class="form-label">Asset Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="Asset_Status" name="Asset_Status">
                        @if(isset($asset_status))
                          @foreach($asset_status as $status)
                            <option value="{{$status->id}}" {{ old('Asset_Status') == $status->id ? 'selected' : '' }}>{{$status->status_name}}</option>
                          @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Sub_Status" class="form-label">Sub Status</label>
                    <input type="text" class="form-control" id="Sub_Status" name="Sub_Status" 
                           value="{{ old('Sub_Status') }}" placeholder="Enter Sub Status">
                </div>
                <div class="col-md-6">
                    <label for="is_swappable" class="form-label">Is Swappable <span class="text-danger">*</span></label>
                    <select class="form-control" id="is_swappable" name="is_swappable">
                        <option value="1" {{ old('is_swappable') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_swappable') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <!--<div class="col-md-6">-->
                <!--    <label for="Status" class="form-label">Delivery Man</label>-->
                <!--    <select class="form-control basic-single" id="dm_id" name="dm_id" required>-->
                <!--        @foreach($delivery_man as $dm)-->
                <!--        <option value="{{$dm->id}}" {{ old('dm_id') == $dm->id ? 'selected' : '' }}>{{$dm->first_name . ' ' . $dm->last_name}} </option>-->
                <!--        @endforeach-->
                <!--    </select>-->
                <!--</div>-->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="rc_book_file_upload">{{ 'RC Book File' }} <span class="text-danger">*</span></label>
                        <div class="upload-area" id="rc_book_file_upload" onclick="document.getElementById('rc_book_file').click();">
                            <input type="file" class="d-none" name="rc_book_file" id="rc_book_file" accept="image/*" onchange="previewImage(event, 'rc_book_file_upload')">
                            <div class="upload-content">
                                <img id="rc_book_file_preview" class="preview-img" src="" alt="RC Book Preview" />
                                <p>No file chosen, yet!</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
    <script>
        function previewImage(event, uploadAreaId) {
           const file = event.target.files[0];
           const uploadArea = document.getElementById(uploadAreaId);
           const previewImg = uploadArea.querySelector('.preview-img');
           const uploadContent = uploadArea.querySelector('p');
    
           if (file) {
               const reader = new FileReader();
    
               reader.onload = function (e) {
                   previewImg.src = e.target.result; // Set the source of the image to the uploaded file
                   previewImg.style.display = 'block'; // Show the preview image
                   uploadContent.textContent = file.name; // Show the name of the file chosen
               };
    
               reader.readAsDataURL(file); // Read the file as a data URL
           } else {
               previewImg.src = ''; // Reset the preview image
               previewImg.style.display = 'none'; // Hide the preview image
               uploadContent.textContent = 'No file chosen, yet!'; // Reset text if no file is chosen
           }
       }
    </script>
</x-app-layout>
