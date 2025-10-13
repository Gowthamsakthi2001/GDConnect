<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">Battery Master Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.modal_master_battery_store')}}" method="POST">
            @csrf <!-- Laravel CSRF Token -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ old('name') }}" placeholder="Enter Battery Name" required>
                </div>
                <div class="col-md-6">
                    <label for="manufacturer_name" class="form-label">Manufacturer Name</label>
                    <input type="text" class="form-control" id="manufacturer_name" name="manufacturer_name" 
                           value="{{ old('manufacturer_name') }}" placeholder="Enter Manufacturer Name" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="current_rating_Ah" class="form-label">Current Rating (Ah)</label>
                    <input type="number" step="0.1" class="form-control" id="current_rating_Ah" name="current_rating_Ah" 
                           value="{{ old('current_rating_Ah') }}" placeholder="Enter Current Rating (Ah)" required>
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Type</label>
                    <input type="text" class="form-control" id="type" name="type" 
                           value="{{ old('type') }}" placeholder="Enter Battery Type" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cell_chemistry" class="form-label">Cell Chemistry</label>
                    <input type="text" class="form-control" id="cell_chemistry" name="cell_chemistry" 
                           value="{{ old('cell_chemistry') }}" placeholder="Enter Cell Chemistry" required>
                </div>
                <div class="col-md-6">
                    <label for="nominal_voltage" class="form-label">Nominal Voltage</label>
                    <input type="number" step="0.1" class="form-control" id="nominal_voltage" name="nominal_voltage" 
                           value="{{ old('nominal_voltage') }}" placeholder="Enter Nominal Voltage" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="max_discharge_rate_c" class="form-label">Max Discharge Rate (C)</label>
                    <input type="number" step="0.1" class="form-control" id="max_discharge_rate_c" name="max_discharge_rate_c" 
                           value="{{ old('max_discharge_rate_c') }}" placeholder="Enter Max Discharge Rate (C)" required>
                </div>
                <div class="col-md-6">
                    <label for="max_voltage" class="form-label">Max Voltage</label>
                    <input type="number" step="0.1" class="form-control" id="max_voltage" name="max_voltage" 
                           value="{{ old('max_voltage') }}" placeholder="Enter Max Voltage" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="min_voltage" class="form-label">Min Voltage</label>
                    <input type="number" step="0.1" class="form-control" id="min_voltage" name="min_voltage" 
                           value="{{ old('min_voltage') }}" placeholder="Enter Min Voltage" required>
                </div>
                <div class="col-md-6">
                    <label for="weight_kg" class="form-label">Weight (kg)</label>
                    <input type="number" step="0.1" class="form-control" id="weight_kg" name="weight_kg" 
                           value="{{ old('weight_kg') }}" placeholder="Enter Weight (kg)" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="connector_type" class="form-label">Connector Type</label>
                    <input type="text" class="form-control" id="connector_type" name="connector_type" 
                           value="{{ old('connector_type') }}" placeholder="Enter Connector Type" required>
                </div>
                <div class="col-md-6">
                    <label for="telematics_enabled" class="form-label">Telematics Enabled</label>
                    <select class="form-control" id="telematics_enabled" name="telematics_enabled" required>
                        <option value="1" {{ old('telematics_enabled') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('telematics_enabled') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="type_of_telematics" class="form-label">Type of Telematics</label>
                    <input type="text" class="form-control" id="type_of_telematics" name="type_of_telematics" 
                           value="{{ old('type_of_telematics') }}" placeholder="Enter Type of Telematics">
                </div>
                <div class="col-md-6">
                    <label for="smart_bms_available" class="form-label">Smart BMS Available</label>
                    <select class="form-control" id="smart_bms_available" name="smart_bms_available" required>
                        <option value="1" {{ old('smart_bms_available') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('smart_bms_available') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="smart_bms_features" class="form-label">Smart BMS Features</label>
                    <input type="text" class="form-control" id="smart_bms_features" name="smart_bms_features" 
                           value="{{ old('smart_bms_features') }}" placeholder="Enter Smart BMS Features">
                </div>
                <div class="col-md-6">
                    <label for="cell_structure" class="form-label">Cell Structure</label>
                    <input type="text" class="form-control" id="cell_structure" name="cell_structure" 
                           value="{{ old('cell_structure') }}" placeholder="Enter Cell Structure">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cell_model" class="form-label">Cell Model</label>
                    <input type="text" class="form-control" id="cell_model" name="cell_model" 
                           value="{{ old('cell_model') }}" placeholder="Enter Cell Model">
                </div>
                <div class="col-md-6">
                    <label for="ip_rating" class="form-label">IP Rating</label>
                    <input type="text" class="form-control" id="ip_rating" name="ip_rating" 
                           value="{{ old('ip_rating') }}" placeholder="Enter IP Rating">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dod_percentage" class="form-label">DOD Percentage</label>
                    <input type="number" step="0.1" class="form-control" id="dod_percentage" name="dod_percentage" 
                           value="{{ old('dod_percentage') }}" placeholder="Enter DOD Percentage">
                </div>
                <div class="col-md-6">
                    <label for="connector_rating" class="form-label">Connector Rating</label>
                    <input type="text" class="form-control" id="connector_rating" name="connector_rating" 
                           value="{{ old('connector_rating') }}" placeholder="Enter Connector Rating">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="warranty_expiry_cycles" class="form-label">Warranty Expiry Cycles</label>
                    <input type="number" class="form-control" id="warranty_expiry_cycles" name="warranty_expiry_cycles" 
                           value="{{ old('warranty_expiry_cycles') }}" placeholder="Enter Warranty Expiry Cycles">
                </div>
                <div class="col-md-6">
                    <label for="warranty_expiry_duration" class="form-label">Warranty Expiry Duration</label>
                    <input type="text" class="form-control" id="warranty_expiry_duration" name="warranty_expiry_duration" 
                           value="{{ old('warranty_expiry_duration') }}" placeholder="Enter Warranty Expiry Duration">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="warranty_expiry_param_priority" class="form-label">Warranty Expiry Param Priority</label>
                    <input type="number" class="form-control" id="warranty_expiry_param_priority" name="warranty_expiry_param_priority" 
                           value="{{ old('warranty_expiry_param_priority') }}" placeholder="Enter Warranty Expiry Param Priority">
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
