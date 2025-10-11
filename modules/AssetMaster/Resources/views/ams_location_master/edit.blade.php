<x-app-layout>
    <div class="container mt-5">
        <h2 class="mb-4">AMS Location Master Form</h2>
        <form action="{{route('admin.Green-Drive-Ev.asset-master.ams_location_master_update',[$AmsLocationMaster->id])}}" method="POST">
            @csrf <!-- Laravel CSRF Token -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="Name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="Name" name="Name" 
                           value="{{ old('Name',$AmsLocationMaster->Name) }}" placeholder="Enter Name" required>
                </div>
                <div class="col-md-6">
                    <label for="Country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="Country" name="Country" 
                           value="{{ old('Country',$AmsLocationMaster->Country) }}" placeholder="Enter Country" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="State" class="form-label">State</label>
                    <input type="text" class="form-control" id="State" name="State" 
                           value="{{ old('State',$AmsLocationMaster->State) }}" placeholder="Enter State" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="Address_line_1" class="form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="Address_line_1" name="Address_line_1" 
                           value="{{ old('Address_line_1',$AmsLocationMaster->Address_line_1) }}" placeholder="Enter Address Line 1" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="Address_line_2" class="form-label">Address Line 2</label>
                    <input type="text" class="form-control" id="Address_line_2" name="Address_line_2" 
                           value="{{ old('Address_line_2',$AmsLocationMaster->Address_line_2) }}" placeholder="Enter Address Line 2">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="Address_line_3" class="form-label">Address Line 3</label>
                    <input type="text" class="form-control" id="Address_line_3" name="Address_line_3" 
                           value="{{ old('Address_line_3',$AmsLocationMaster->Address_line_3) }}" placeholder="Enter Address Line 3">
                </div>
            </div>
            <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
