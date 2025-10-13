<x-app-layout>
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.vehicle_model_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Create Vehicle Model
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.asset_management.vehicle_model_master.list')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form  id="vehicleModelForm" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="brand_model">Brand Model</label>
                                <select class="form-control bg-white custom-select2-field p-4" name="brand_model" id="brand_model" required>
                                    <option value="">Select Brand Model</option>
                                     @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_model') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_model')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <select class="form-control bg-white custom-select2-field" name="vehicle_type" id="vehicle_type" required>
                                    <option value="">Select Vehicle Type</option>
                                     @foreach($vehicle_types as $type)
                                     <option value="{{$type->id}}">{{$type->name}}</option>
                                     @endforeach
                                </select>
                                  @error('vehicle_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Vehicle Model</label>
                                <input type="text" class="form-control bg-white" name="vehicle_model" id="vehicle_model"  value="{{ old('vehicle_model') }}"  placeholder="Enter Vehicle Model" required>
                                  @error('vehicle_model')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Make</label>
                                <input type="text" class="form-control bg-white" name="make" id="make"  value="{{ old('make') }}"  placeholder="Enter Make" required>
                                  @error('make')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Vehicle Variant</label>
                                <input type="text" class="form-control bg-white" name="variant" id="variant"  value="{{ old('variant') }}"  placeholder="Enter Variant" required>
                                  @error('variant')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_color">Vehicle Color</label>
                                <input type="text" class="form-control bg-white" name="color" id="color"  value="{{ old('color') }}"  placeholder="Enter Color" required>
                                  @error('color')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                        
                        <!--<div class="col-md-6 mb-3">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label mb-2 ms-1" for="location">Location</label>-->
                        <!--        <select class="form-control bg-white custom-select2-field" name="location" id="location">-->
                        <!--            <option>Select Location</option>-->
                        <!--            <option>Chennai</option>-->
                        <!--            <option>Coimbatore</option>-->
                        <!--            <option>Bangalore</option>-->
                        <!--              <option>Hyderabad</option>-->
                        <!--                <option>Mumbai</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                         
                         
                        
                        <div class="col-12 text-end gap-2">
                            <button type="reset" class="btn btn-danger px-6 p-2">Reset</button>
                            <button type="submit" id="submitBtn" class="btn btn-success px-6 p-2">Create</button>
                        </div>
               
                    </div>
                    </form>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script>
document.getElementById('vehicleModelForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');

    submitBtn.disabled = true;
    const originalText = submitBtn.innerText;
    submitBtn.innerText = 'Submitting...';


    fetch("{{ route('admin.asset_management.vehicle_model_master.store') }}", {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        'X-Requested-With': 'XMLHttpRequest' // 👈 Add this line
    },
    body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (response.ok) {
                toastr.success(data.message || 'Vehicle model created successfully.');
                setTimeout(() => {
                    window.location.href = "{{ route('admin.asset_management.vehicle_model_master.list') }}";
                }, 1500);
            } else {
                toastr.error(data.message || 'Something went wrong.');
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            }
        } else {
            throw new Error('Unexpected response format');
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        toastr.error('Error submitting the form.');
        submitBtn.disabled = false;
        submitBtn.innerText = originalText;
    });
});
</script>

@endsection
</x-app-layout>
