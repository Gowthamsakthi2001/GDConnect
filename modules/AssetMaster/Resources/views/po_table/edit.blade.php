<x-app-layout>
    <div class="container mt-5">
        <h2 class=" mb-4">PO Table Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.po_table_update',[$PoTable->id])}}" method="POST">
            @csrf <!-- Laravel CSRF Token -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="AMS_Location" class="form-label">AMS Location</label>
                    <input type="text" class="form-control" id="AMS_Location" name="AMS_Location" 
                           value="{{ old('AMS_Location',$PoTable->AMS_Location) }}" placeholder="Enter AMS Location" required>
                </div>
                <div class="col-md-6">
                    <label for="PO_Number" class="form-label">PO Number</label>
                    <input type="text" class="form-control" id="PO_Number" name="PO_Number" 
                           value="{{ old('PO_Number',$PoTable->PO_Number) }}" placeholder="Enter PO Number" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Supplier_Name" class="form-label">Supplier Name</label>
                    <input type="text" class="form-control" id="Supplier_Name" name="Supplier_Name" 
                           value="{{ old('Supplier_Name',$PoTable->Supplier_Name) }}" placeholder="Enter Supplier Name" required>
                </div>
                <div class="col-md-6">
                    <label for="Quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="Quantity" name="Quantity" 
                           value="{{ old('Quantity',$PoTable->Quantity) }}" placeholder="Enter Quantity" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Manufacturer" class="form-label">Manufacturer</label>
                    <input type="text" class="form-control" id="Manufacturer" name="Manufacturer" 
                           value="{{ old('Manufacturer',$PoTable->Manufacturer) }}" placeholder="Enter Manufacturer">
                </div>
                <div class="col-md-6">
                    <label for="PO_Date" class="form-label">PO Date</label>
                    <input type="date" class="form-control" id="PO_Date" name="PO_Date" value="{{ old('PO_Date', $PoTable->PO_Date ? $PoTable->PO_Date->format('Y-m-d') : '') }}" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Other_Amount" class="form-label">Other Amount</label>
                    <input type="number" class="form-control" id="Other_Amount" name="Other_Amount" 
                           value="{{ old('Other_Amount',$PoTable->Other_Amount) }}" placeholder="Enter Other Amount">
                </div>
                <div class="col-md-6">
                    <label for="Tax_Amount" class="form-label">Tax Amount</label>
                    <input type="number" class="form-control" id="Tax_Amount" name="Tax_Amount" 
                           value="{{ old('Tax_Amount',$PoTable->Tax_Amount) }}" placeholder="Enter Tax Amount">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Delivery_Date" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="Delivery_Date" name="Delivery_Date" 
                           value="{{ old('Delivery_Date', $PoTable->Delivery_Date ? $PoTable->Delivery_Date->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="Status" class="form-label">Status</label>
                    <select class="form-control" id="Status" name="Status" required>
                        <option value="1" {{ old('Status',$PoTable->Status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('Status',$PoTable->Status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                
                <div class="col-md-6">
                    <label for="Description" class="form-label">Description</label>
                    <textarea class="form-control" id="Description" name="Description" 
                              placeholder="Enter Description" rows="2">{{ old('Description',$PoTable->Description) }}</textarea>
                </div>
            </div>
            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
