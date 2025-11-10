<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">Charger Master Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.model_master_charger_update',[$ModelMasterCharger->id])}}" method="POST">
            @csrf <!-- Laravel CSRF Token -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Charger Name</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ old('name',$ModelMasterCharger->name) }}" placeholder="Enter Charger Name" required>
                </div>
                <div class="col-md-6">
                    <label for="manufacturer_name" class="form-label">Manufacturer Name</label>
                    <input type="text" class="form-control" id="manufacturer_name" name="manufacturer_name" 
                           value="{{ old('manufacturer_name',$ModelMasterCharger->manufacturer_name) }}" placeholder="Enter Manufacturer Name" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nominal_c_rating" class="form-label">Nominal C Rating</label>
                    <input type="number" step="0.1" class="form-control" id="nominal_c_rating" name="nominal_c_rating" 
                           value="{{ old('nominal_c_rating',$ModelMasterCharger->nominal_c_rating) }}" placeholder="Enter Nominal C Rating" required>
                </div>
                <div class="col-md-6">
                    <label for="charging_mode" class="form-label">Charging Mode</label>
                    <select class="form-control" id="charging_mode" name="charging_mode" required>
                        <option value="fast" {{ old('charging_mode',$ModelMasterCharger->charging_mode) == 'fast' ? 'selected' : '' }}>Fast</option>
                        <option value="slow" {{ old('charging_mode',$ModelMasterCharger->charging_mode) == 'slow' ? 'selected' : '' }}>Slow</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="output_voltage" class="form-label">Output Voltage</label>
                    <input type="number" step="0.1" class="form-control" id="output_voltage" name="output_voltage" 
                           value="{{ old('output_voltage',$ModelMasterCharger->output_voltage) }}" placeholder="Enter Output Voltage" required>
                </div>
                <div class="col-md-6">
                    <label for="output_current" class="form-label">Output Current</label>
                    <input type="number" step="0.1" class="form-control" id="output_current" name="output_current" 
                           value="{{ old('output_current',$ModelMasterCharger->output_current) }}" placeholder="Enter Output Current" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="input_voltage" class="form-label">Input Voltage</label>
                    <input type="number" step="0.1" class="form-control" id="input_voltage" name="input_voltage" 
                           value="{{ old('input_voltage',$ModelMasterCharger->input_voltage) }}" placeholder="Enter Input Voltage" required>
                </div>
                <div class="col-md-6">
                    <label for="input_current" class="form-label">Input Current</label>
                    <input type="number" step="0.1" class="form-control" id="input_current" name="input_current" 
                           value="{{ old('input_current',$ModelMasterCharger->input_current) }}" placeholder="Enter Input Current" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="connector_rating" class="form-label">Connector Rating</label>
                    <input type="text" class="form-control" id="connector_rating" name="connector_rating" 
                           value="{{ old('connector_rating',$ModelMasterCharger->connector_rating) }}" placeholder="Enter Connector Rating" required>
                </div>
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1" {{ old('status',$ModelMasterCharger->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status',$ModelMasterCharger->status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
