<x-app-layout>
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.vehicle_model_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Update Vehicle Model
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
                    <form action="{{ route('admin.asset_management.vehicle_model_master.update_data') }}" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="brand_model">Brand Model</label>
                                <select class="form-control bg-white custom-select2-field" name="brand_model" id="brand_model" required>
                                    <option value="">Select Brand Model</option>
                                     @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ $vehicle->brand == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <select class="form-control bg-white custom-select2-field" name="vehicle_type" id="vehicle_type" required>
                                    <option>Select Vehicle Type</option>
                                     @foreach($vehicle_types as $type)
                                     <option value="{{$type->id}}" {{ $vehicle->vehicle_type == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                         <input type="hidden" class="form-control bg-white" name="vehicle_id"  value="{{$vehicle->id}}">
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Vehicle Model</label>
                                <input type="text" class="form-control bg-white" name="vehicle_model" id="vehicle_model"  value="{{$vehicle->vehicle_model}}" placeholder="Enter Vehicle Model" required>
                            </div>
                        </div>
                        
                  <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Make</label>
                                <input type="text" class="form-control bg-white" name="make" id="make"  value="{{$vehicle->make}}"  placeholder="Enter Make" required>
                                  @error('make')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_model">Variant</label>
                                <input type="text" class="form-control bg-white" name="variant" id="variant"  value="{{$vehicle->variant}}"  placeholder="Enter Variant" required>
                                  @error('variant')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_color">Vehicle Color</label>
                                <input type="text" class="form-control bg-white" name="color" id="color"  value="{{$vehicle->color}}"  placeholder="Enter Color" required>
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
                        <!--            <option selected>Chennai</option>-->
                        <!--            <option>Coimbatore</option>-->
                        <!--            <option>Bangalore</option>-->
                        <!--              <option>Hyderabad</option>-->
                        <!--                <option>Mumbai</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                         
                         
                        
                        <div class="col-12 text-end gap-2">
                            <button type="reset" class="btn btn-danger px-6 p-2">Reset</button>
                            <button type="submit" class="btn btn-success px-6 p-2">Update</button>
                        </div>
               
                    </div>
                    </form>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script>

 
</script>
@endsection
</x-app-layout>
