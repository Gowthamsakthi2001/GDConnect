<x-app-layout>  
    <div class="container mt-5">
        <h2 class="mb-4">Asset Master Charger</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.asset_master_charger_update',[$AssetMasterCharger->id])}}" method="POST">
            @csrf <!-- Add CSRF token for security -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="AMS_Location" class="form-label">AMS Location</label>
                    <input type="text" class="form-control" id="AMS_Location" name="AMS_Location" 
                           value="{{ old('AMS_Location',$AssetMasterCharger->AMS_Location) }}" placeholder="Enter AMS Location" required>
                </div>
                <div class="col-md-6">
                    <label for="PO_ID" class="form-label">PO ID</label>
                    <input type="number" class="form-control" id="PO_ID" name="PO_ID" 
                           value="{{ old('PO_ID',$AssetMasterCharger->PO_ID) }}" placeholder="Enter PO ID" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Invoice_Number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="Invoice_Number" name="Invoice_Number" 
                           value="{{ old('Invoice_Number',$AssetMasterCharger->Invoice_Number) }}" placeholder="Enter Invoice Number">
                </div>
                <div class="col-md-6">
                    <label for="Charger_Model" class="form-label">Charger Model</label>
                    <input type="text" class="form-control" id="Charger_Model" name="Charger_Model" 
                           value="{{ old('Charger_Model',$AssetMasterCharger->Charger_Model) }}" placeholder="Enter Charger Model">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Serial_Number" class="form-label">Serial Number</label>
                    <input type="text" class="form-control" id="Serial_Number" name="Serial_Number" 
                           value="{{ old('Serial_Number',$AssetMasterCharger->Serial_Number) }}" placeholder="Enter Serial Number" required>
                </div>
                <div class="col-md-6">
                    <label for="Engraved_Serial_Num" class="form-label">Engraved Serial Number</label>
                    <input type="text" class="form-control" id="Engraved_Serial_Num" name="Engraved_Serial_Num" 
                           value="{{ old('Engraved_Serial_Num',$AssetMasterCharger->Engraved_Serial_Num) }}" placeholder="Enter Engraved Serial Number">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Sub_status" class="form-label">Sub Status</label>
                    <input type="text" class="form-control" id="Sub_status" name="Sub_status" 
                           value="{{ old('Sub_status',$AssetMasterCharger->Sub_status) }}" placeholder="Enter Sub Status">
                </div>
                <div class="col-md-6">
                    <label for="In_Use_Date" class="form-label">In Use Date</label>
                    <input type="date" class="form-control" id="In_Use_Date" name="In_Use_Date" 
                           value="{{ old('In_Use_Date',optional($AssetMasterCharger->In_Use_Date)->format('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Assigned_to" class="form-label">Assigned To</label>
                    <input type="text" class="form-control" id="Assigned_to" name="Assigned_to" 
                           value="{{ old('Assigned_to',$AssetMasterCharger->Assigned_to) }}" placeholder="Enter Assigned To">
                </div>
                <div class="col-md-6">
                    <label for="Status" class="form-label">Status</label>
                    <select class="form-control basic-single" id="Status" name="Status" required>
                        <option value="1" {{ old('Status',$AssetMasterCharger->Status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('Status',$AssetMasterCharger->Status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Status" class="form-label">Delivery Man</label>
                    <select class="form-control basic-single" id="dm_id" name="dm_id" required>
                        @foreach($delivery_man as $dm)
                        <option value="{{$dm->id}}" {{ old('dm_id',$AssetMasterCharger->dm_id) == $dm->id ? 'selected' : '' }}>{{$dm->first_name . ' ' . $dm->last_name}} </option>
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
                                {{ (isset($AssetMasterCharger) && $AssetMasterCharger->Chassis_Serial_No == $d->Chassis_Serial_No) ? 'selected' : '' }}>
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
