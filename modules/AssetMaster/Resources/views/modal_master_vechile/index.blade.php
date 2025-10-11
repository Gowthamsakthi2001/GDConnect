<x-app-layout>
    <div class="container my-4">
        <h2>Vehicle Information Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.store')}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Vehicle Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="ex:Tesla Model S" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="manufacturer_name" class="form-label">Manufacturer Name</label>
                    <input type="text" id="manufacturer_name" name="manufacturer_name" value="{{ old('manufacturer_name') }}" class="form-control" placeholder="ex:Tesla" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="load_capacity_kg" class="form-label">Load Capacity (kg)</label>
                    <input type="number" id="load_capacity_kg" name="load_capacity_kg" value="{{ old('load_capacity_kg') }}" class="form-control" placeholder="ex:1000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rated_voltage" class="form-label">Rated Voltage</label>
                    <input type="number" id="rated_voltage" name="rated_voltage" value="{{ old('rated_voltage') }}" class="form-control" placeholder="ex:400" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rated_Ah" class="form-label">Rated Ah</label>
                    <input type="number" id="rated_Ah" name="rated_Ah" class="form-control" value="{{ old('rated_Ah') }}" placeholder="ex:100" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="max_speed_km_h" class="form-label">Max Speed (km/h)</label>
                    <input type="number" id="max_speed_km_h" name="max_speed_km_h" value="{{ old('max_speed_km_h') }}" class="form-control" placeholder="ex:200" required>
                </div>
                
                <?php
                    $tyreOptions = [
                        'clincher' => 'Clincher',
                        'radial' => 'Radial',
                        'tubeless' => 'Tubeless',
                        'tubular' => 'Tubular',
                    ];
                    
                    $vehicleOptions = [
                        '2_wheeler' => '2-Wheeler',
                        '3_wheeler' => '3-Wheeler',
                        '4_wheeler' => '4-Wheeler',
                    ];
                    
                    $speedOptions = [
                        'high_speed' => 'High Speed',
                        'low_speed' => 'Low Speed',
                    ];
                    
                ?>
                
                <div class="col-md-6 mb-3">
                    <label for="tyre_type" class="form-label">Tyre Type</label>
                    <select name="tyre_type" id="tyre_type" class="form-control">
                        @foreach ($tyreOptions as $key => $value)
                            <option value="{{ $key }}" {{ old('tyre_type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="front_tyre_dimensions" class="form-label">Front Tyre Dimensions</label>
                    <input type="text" id="front_tyre_dimensions" name="front_tyre_dimensions" value="{{ old('front_tyre_dimensions') }}" class="form-control" placeholder="ex:195/55 R16" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rear_tyre_dimensions" class="form-label">Rear Tyre Dimensions</label>
                    <input type="text" id="rear_tyre_dimensions" name="rear_tyre_dimensions" value="{{ old('rear_tyre_dimensions') }}" class="form-control" placeholder="ex:205/55 R16" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="vehicle_type" class="form-label">Vehicle Type</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-control">
                        @foreach ($vehicleOptions as $key => $value)
                            <option value="{{ $key }}" {{ old('vehicle_type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="range_km_noload" class="form-label">Range (No Load) (km)</label>
                    <input type="number" id="range_km_noload" name="range_km_noload" value="{{ old('range_km_noload') }}" class="form-control" placeholder="ex:500" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="range_km_fullload" class="form-label">Range (Full Load) (km)</label>
                    <input type="number" id="range_km_fullload" name="range_km_fullload" value="{{ old('range_km_fullload') }}" class="form-control" placeholder="ex:400" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="vehicle_mode" class="form-label">Vehicle Mode</label>
                    <select name="vehicle_mode" id="vehicle_mode" class="form-control">
                        @foreach ($speedOptions as $key => $value)
                            <option value="{{ $key }}" {{ old('vehicle_mode') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="motor_type" class="form-label">Motor Type</label>
                    <input type="text" id="motor_type" name="motor_type" class="form-control" value="{{ old('motor_type') }}" placeholder="ex:AC Induction" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="motor_max_rpm" class="form-label">Motor Max RPM</label>
                    <input type="number" id="motor_max_rpm" name="motor_max_rpm" value="{{ old('motor_max_rpm') }}" class="form-control" placeholder="ex:6000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="peak_power_watt" class="form-label">Peak Power (Watt)</label>
                    <input type="number" id="peak_power_watt" name="peak_power_watt" value="{{ old('peak_power_watt') }}" class="form-control" placeholder="ex:50000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="rated_power_watt" class="form-label">Rated Power (Watt)</label>
                    <input type="number" id="rated_power_watt" name="rated_power_watt" value="{{ old('rated_power_watt') }}" class="form-control" placeholder="ex:30000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="motor_can_enabled" class="form-label">Motor CAN Enabled</label>
                    <select id="motor_can_enabled" name="motor_can_enabled" class="form-control basic-single" required>
                        <option value="1" {{ old('motor_can_enabled') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('motor_can_enabled') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="peak_torque_nm" class="form-label">Peak Torque (Nm)</label>
                    <input type="number" id="peak_torque_nm" name="peak_torque_nm" class="form-control" value="{{ old('peak_torque_nm') }}" placeholder="ex:400" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="continuous_torque_nm" class="form-label">Continuous Torque (Nm)</label>
                    <input type="number" id="continuous_torque_nm" name="continuous_torque_nm" value="{{ old('continuous_torque_nm') }}" class="form-control" placeholder="ex: 0" required>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="front_suspension_type" class="form-label">Front Suspension Type</label>
                    <select id="front_suspension_type" name="front_suspension_type" class="form-control basic-single" required>
                        <option value="Hydraulic" {{ old('front_suspension_type') == 'Hydraulic' ? 'selected' : '' }}>Hydraulic</option>
                        <option value="Spring" {{ old('front_suspension_type') == 'Spring' ? 'selected' : '' }}>Spring</option>
                        <option value="Dual" {{ old('front_suspension_type') == 'Dual' ? 'selected' : '' }}>Dual</option>
                        <option value="Telescopic" {{ old('front_suspension_type') == 'Telescopic' ? 'selected' : '' }}>Telescopic</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="rear_suspension_type" class="form-label">Rear Suspension Type</label>
                    <select id="rear_suspension_type" name="rear_suspension_type" class="form-control basic-single" required>
                        <option value="Hydraulic" {{ old('rear_suspension_type') == 'Hydraulic' ? 'selected' : '' }}>Hydraulic</option>
                        <option value="Spring" {{ old('rear_suspension_type') == 'Spring' ? 'selected' : '' }}>Spring</option>
                        <option value="Dual" {{ old('rear_suspension_type') == 'Dual' ? 'selected' : '' }}>Dual</option>
                        <option value="Telescopic" {{ old('rear_suspension_type') == 'Telescopic' ? 'selected' : '' }}>Telescopic</option>
                    </select>
                </div>

            
                <div class="col-md-6 mb-3">
                    <label for="ground_clearance_mm" class="form-label">Ground Clearance (mm)</label>
                    <input type="number" id="ground_clearance_mm" name="ground_clearance_mm" value="{{ old('ground_clearance_mm') }}" class="form-control" placeholder="ex: 140" required>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="motor_ip_rating" class="form-label">Motor IP Rating</label>
                    <input type="text" id="motor_ip_rating" name="motor_ip_rating"  value="{{ old('motor_ip_rating') }}" class="form-control" placeholder="ex: IP65" required>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="throttle_type" class="form-label">Throttle Type</label>
                    <select id="throttle_type" name="throttle_type" class="form-control basic-single" required>
                        
                        <option value="Hall Effect" {{ old('throttle_type') == 'Hall Effect' ? 'selected' : '' }}>Hall Effect</option>
                        <option value="Potentiometer" {{ old('throttle_type') == 'Potentiometer' ? 'selected' : '' }}>Potentiometer</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="peak_curr_cntrlr" class="form-label">Peak Current Controller</label>
                    <input type="number" id="peak_curr_cntrlr" name="peak_curr_cntrlr" value="{{ old('peak_curr_cntrlr') }}" class="form-control" placeholder="ex: 26" required>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="cntrlr_can_enabled" class="form-label">Controller CAN Enabled</label>
                    <select id="cntrlr_can_enabled" name="cntrlr_can_enabled" class="form-control basic-single" required>
                        <option value="1" {{ old('cntrlr_can_enabled') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('cntrlr_can_enabled') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="acceleration_0to40_sec" class="form-label">Acceleration 0-40 (sec)</label>
                    <input type="number" id="acceleration_0to40_sec" name="acceleration_0to40_sec" value="{{ old('acceleration_0to40_sec') }}" class="form-control" placeholder="ex: 8" required>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="head_light_type" class="form-label">Head Light Type</label>
                    <select id="head_light_type" name="head_light_type" class="form-control basic-single" required>
                        
                        <option value="Filament" {{ old('head_light_type') == 'Filament' ? 'selected' : '' }}>Filament</option>
                        <option value="LED" {{ old('head_light_type') == 'LED' ? 'selected' : '' }}>LED</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="vehicle_reverse_mode" class="form-label">Vehicle Reverse Mode</label>
                    <select id="vehicle_reverse_mode" name="vehicle_reverse_mode" class="form-control basic-single" required>
                        <option value="1" {{ old('vehicle_reverse_mode') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('vehicle_reverse_mode') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="inbuilt_iot" class="form-label">Inbuilt IoT</label>
                    <select id="inbuilt_iot" name="inbuilt_iot" class="form-control basic-single" required>
                        <option value="1" {{ old('inbuilt_iot') == '1' ? 'selected' : '' }} >Yes</option>
                        <option value="0" {{ old('inbuilt_iot') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-control basic-single" required>
                        
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Continue with the remaining fields using similar structure -->

                <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                    <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
