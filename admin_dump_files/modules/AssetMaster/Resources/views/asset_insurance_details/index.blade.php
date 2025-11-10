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
        <h2 class="mb-4">EV Vehicle Insurance Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.asset_insurance_details_store')}}" method="POST" enctype="multipart/form-data">
            @csrf <!-- CSRF token for Laravel -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="vehicle_reg_no" class="form-label">Vehicle Registration Number</label>
                    <input type="text" class="form-control" id="vehicle_reg_no" name="vehicle_reg_no" 
                           value="{{ old('vehicle_reg_no') }}" placeholder="Enter Vehicle Registration Number" required>
                </div>
                <div class="col-md-6">
                    <label for="Insurance_Vendor_3rd_party" class="form-label">Insurance Vendor (3rd Party)</label>
                    <input type="text" class="form-control" id="Insurance_Vendor_3rd_party" name="Insurance_Vendor_3rd_party" 
                           value="{{ old('Insurance_Vendor_3rd_party') }}" placeholder="Enter Insurance Vendor (3rd Party)">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Policy_Number_3rd_party" class="form-label">Policy Number (3rd Party)</label>
                    <input type="text" class="form-control" id="Policy_Number_3rd_party" name="Policy_Number_3rd_party" 
                           value="{{ old('Policy_Number_3rd_party') }}" placeholder="Enter Policy Number (3rd Party)">
                </div>
                <div class="col-md-6">
                    <label for="Start_date_3rd_party" class="form-label">Start Date (3rd Party)</label>
                    <input type="date" class="form-control" id="Start_date_3rd_party" name="Start_date_3rd_party" 
                           value="{{ old('Start_date_3rd_party',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="End_date_3rd_party" class="form-label">End Date (3rd Party)</label>
                    <input type="date" class="form-control" id="End_date_3rd_party" name="End_date_3rd_party" 
                           value="{{ old('End_date_3rd_party',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label for="Declared_Value_3rd_party" class="form-label">Declared Value (3rd Party)</label>
                    <input type="number" class="form-control" id="Declared_Value_3rd_party" name="Declared_Value_3rd_party" 
                           value="{{ old('Declared_Value_3rd_party') }}" placeholder="Enter Declared Value (3rd Party)">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Policy_Number_OD" class="form-label">Policy Number (Own Damage)</label>
                    <input type="text" class="form-control" id="Policy_Number_OD" name="Policy_Number_OD" 
                           value="{{ old('Policy_Number_OD') }}" placeholder="Enter Policy Number (Own Damage)">
                </div>
                <div class="col-md-6">
                    <label for="Start_date_OD" class="form-label">Start Date (Own Damage)</label>
                    <input type="date" class="form-control" id="Start_date_OD" name="Start_date_OD" 
                           value="{{ old('Start_date_OD',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="End_date_OD" class="form-label">End Date (Own Damage)</label>
                    <input type="date" class="form-control" id="End_date_OD" name="End_date_OD" 
                           value="{{ old('End_date_OD',\Carbon\Carbon::now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-6">
                    <label for="Declared_Value_OD" class="form-label">Declared Value (Own Damage)</label>
                    <input type="number" class="form-control" id="Declared_Value_OD" name="Declared_Value_OD" 
                           value="{{ old('Declared_Value_OD') }}" placeholder="Enter Declared Value (Own Damage)">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Insurance_Status_OD" class="form-label">Insurance Status (Own Damage)</label>
                    <select class="form-control" id="Insurance_Status_OD" name="Insurance_Status_OD">
                        <option value="Active" {{ old('Insurance_Status_OD') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Expired" {{ old('Insurance_Status_OD') == 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Pending" {{ old('Insurance_Status_OD') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="asset">Select Chassis Serial No</label>
                    <select name="Chassis_Serial_No" id="Chassis_Serial_No" class="form-control @error('Chassis_Serial_No') is-invalid @enderror">
                        <option value="" disabled selected>Choose an Chassis Serial No</option>
                        @foreach($AssetMasterVehicle as $d)
                            <option value="{{ $d->Chassis_Serial_No }}"  {{ old('Chassis_Serial_No') == $d->Chassis_Serial_No ? 'selected' : '' }}>
                                {{ $d->Chassis_Serial_No }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label class="input-label mb-2 ms-1" for="insurance_file_upload">{{ 'Insurance File' }}</label>
                    <div class="upload-area" id="insurance_file_upload" onclick="document.getElementById('insurance_file').click();">
                        <input type="file" class="d-none" name="insurance_file" id="insurance_file" accept="image/*" onchange="previewImage(event, 'insurance_file_upload')">
                        <div class="upload-content">
                            <img id="insurance_file_preview" class="preview-img" src="" alt="Insurance Preview" />
                            <p>No file chosen, yet!</p>
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
