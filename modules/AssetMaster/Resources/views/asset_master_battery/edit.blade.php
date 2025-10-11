<x-app-layout>
    
    <div class="container mt-5">
        <h2 class="mb-4">EV Asset Master Battery</h2>
        <form action="{{ route('admin.Green-Drive-Ev.asset-master.asset_master_battery_update', [$AssetMasterBattery->id]) }}" method="POST">
            @csrf <!-- CSRF token for Laravel -->
           
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="AMS_Location" class="form-label">AMS Location</label>
                    <input type="text" class="form-control" id="AMS_Location" name="AMS_Location"
                           value="{{ old('AMS_Location', $AssetMasterBattery->AMS_Location) }}" placeholder="Enter AMS Location" required>
                </div>
                <div class="col-md-6">
                    <label for="PO_ID" class="form-label">PO ID</label>
                    <input type="text" class="form-control" id="PO_ID" name="PO_ID"
                           value="{{ old('PO_ID', $AssetMasterBattery->PO_ID) }}" placeholder="Enter PO ID" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Invoice_Number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="Invoice_Number" name="Invoice_Number"
                           value="{{ old('Invoice_Number', $AssetMasterBattery->Invoice_Number) }}" placeholder="Enter Invoice Number">
                </div>
                <div class="col-md-6">
                    <label for="Battery_Model" class="form-label">Battery Model</label>
                    <input type="text" class="form-control" id="Battery_Model" name="Battery_Model"
                           value="{{ old('Battery_Model', $AssetMasterBattery->Battery_Model) }}" placeholder="Enter Battery Model">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Serial_Number" class="form-label">Serial Number</label>
                    <input type="text" class="form-control" id="Serial_Number" name="Serial_Number"
                           value="{{ old('Serial_Number', $AssetMasterBattery->Serial_Number) }}" placeholder="Enter Serial Number" required>
                </div>
                <div class="col-md-6">
                    <label for="Engraved_Serial_Num" class="form-label">Engraved Serial Number</label>
                    <input type="text" class="form-control" id="Engraved_Serial_Num" name="Engraved_Serial_Num"
                           value="{{ old('Engraved_Serial_Num', $AssetMasterBattery->Engraved_Serial_Num) }}" placeholder="Enter Engraved Serial Number">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Sub_status" class="form-label">Sub Status</label>
                    <input type="text" class="form-control" id="Sub_status" name="Sub_status"
                           value="{{ old('Sub_status', $AssetMasterBattery->Sub_status) }}" placeholder="Enter Sub Status">
                </div>
                <div class="col-md-6">
                    <label for="In_use_Date" class="form-label">In Use Date</label>
                    <input type="date" class="form-control" id="In_use_Date" name="In_use_Date"
                           value="{{ old('In_use_Date',optional($AssetMasterBattery->In_use_Date)->format('Y-m-d') ) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Assigned_To" class="form-label">Assigned To</label>
                    <input type="text" class="form-control" id="Assigned_To" name="Assigned_To"
                           value="{{ old('Assigned_To', $AssetMasterBattery->Assigned_To) }}" placeholder="Enter Assigned To">
                </div>
                <div class="col-md-6">
                    <label for="Status" class="form-label">Status</label>
                    <select class="form-control basic-single" id="Status" name="Status" required>
                        <option value="1" {{ old('Status', $AssetMasterBattery->Status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('Status', $AssetMasterBattery->Status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dm_id" class="form-label">Delivery Man</label>
                    <select class="form-control basic-single" id="dm_id" name="dm_id" required>
                        @foreach($delivery_man as $dm)
                            <option value="{{ $dm->id }}" {{ old('dm_id', $AssetMasterBattery->dm_id) == $dm->id ? 'selected' : '' }}>
                                {{ $dm->first_name . ' ' . $dm->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="asset">Select Chassis Serial No</label>
                    <select name="Chassis_Serial_No" id="Chassis_Serial_No" class="form-control @error('Chassis_Serial_No') is-invalid @enderror">
                        <option value="" disabled selected>Choose an Asset</option>
                        @foreach($AssetMasterVehicle as $d)
                            <option value="{{ $d->Chassis_Serial_No }}" 
                                {{ (isset($AssetMasterBattery) && $AssetMasterBattery->Chassis_Serial_No == $d->Chassis_Serial_No) ? 'selected' : '' }}>
                                {{ $d->Chassis_Serial_No }}
                            </option>
                        @endforeach
                    </select>
                    @error('asset')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
