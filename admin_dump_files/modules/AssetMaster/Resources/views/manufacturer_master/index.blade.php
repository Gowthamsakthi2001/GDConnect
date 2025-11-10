<x-app-layout>
    <div class="container mt-5">
        <h2 class=" mb-4">Manufacturer Master Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.manufacturer_master_store')}}" method="POST">
            @csrf <!-- Laravel CSRF Token -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="manufacturer_name" class="form-label">Manufacturer Name</label>
                    <input type="text" class="form-control" id="manufacturer_name" name="manufacturer_name" 
                           value="{{ old('manufacturer_name') }}" placeholder="Enter Manufacturer Name" required>
                </div>
                <div class="col-md-6">
                    <label for="Address_line_1" class="form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="Address_line_1" name="Address_line_1" 
                           value="{{ old('Address_line_1') }}" placeholder="Enter Address Line 1" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Address_line_2" class="form-label">Address Line 2</label>
                    <input type="text" class="form-control" id="Address_line_2" name="Address_line_2" 
                           value="{{ old('Address_line_2') }}" placeholder="Enter Address Line 2">
                </div>
                <div class="col-md-6">
                    <label for="Address_line_3" class="form-label">Address Line 3</label>
                    <input type="text" class="form-control" id="Address_line_3" name="Address_line_3" 
                           value="{{ old('Address_line_3') }}" placeholder="Enter Address Line 3">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="Country" name="Country" 
                           value="{{ old('Country') }}" placeholder="Enter Country" required>
                </div>
                <div class="col-md-6">
                    <label for="State" class="form-label">State</label>
                    <input type="text" class="form-control" id="State" name="State" 
                           value="{{ old('State') }}" placeholder="Enter State" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="Phone" name="Phone" 
                           value="{{ old('Phone') }}" oninput="validateMobileNumber(this)" placeholder="Enter Phone Number" required>
                           <div id="validationMessage"></div>
                </div>
                <div class="col-md-6">
                    <label for="Contact_Name" class="form-label">Contact Name</label>
                    <input type="text" class="form-control" id="Contact_Name" name="Contact_Name" 
                           value="{{ old('Contact_Name') }}" placeholder="Enter Contact Name" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Status" class="form-label">Status</label>
                    <select class="form-control" id="Status" name="Status" required>
                        <option value="1" {{ old('Status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('Status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="Web_site_URL" class="form-label">Website URL</label>
                    <input type="url" class="form-control" id="Web_site_URL" name="Web_site_URL" 
                           value="{{ old('Web_site_URL') }}" placeholder="Enter Website URL" required>
                </div>
            </div>
           <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
    <script>
      function validateMobileNumber(input) {
        const mobile = input.value;
        const validationMessage = document.getElementById('validationMessage');
    
        // Regular expression for validating the mobile number
        const mobileRegex = /^\+91\d{10}$/;
    
        if (mobile.length > 0 && !mobileRegex.test(mobile)) {
          validationMessage.textContent =
            'Invalid mobile number. Format: +91 followed by 10 digits.';
          validationMessage.className = 'error';
        } else {
          validationMessage.textContent = '';
        }
      }
    </script>
</x-app-layout>
