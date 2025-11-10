<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <img src="{{asset('admin-assets/icons/custom/green-city.png')}}" class="img-fluid rounded"><span class="ps-2">Add City</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{ route('admin.Green-Drive-Ev.City.store') }}" method="post" class="row g-3 p-3">
                        @csrf
                       
                            <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="city_name">{{ __('City Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="city_name" id="city_name" class="form-control" placeholder="{{ __('City Name') }}" value="{{ old('name') }}" maxlength="191">
                                    @error('city_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                             <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="short_code">{{ __('Short Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="short_code" id="short_code" class="form-control"  maxlength="3" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')" placeholder="{{ __('Ex : BLR') }}" value="{{ old('short_code') }}">
                                    <small class="text-note text-muted">Only 3 letters allowed. Must be capital letters.</small>
                                    @error('short_code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                            <div class="col-md-6">
                                    <label for="statusSelect" class="mb-2 ms-1">Status <span class="text-danger">*</span></label>
                                    <select class="form-control basic-single" id="statusSelect" name="status">
                                        <option value="1" >Active</option>
                                        <option value="0" >Inactive</option>
                                    </select>
                            </div>
                            


                        <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                            <button type="reset" class="btn btn-round text-white px-4 custom-bg-color">Reset</button>
                            <button type="submit" class="btn btn-success btn-round">Create City</button>
                        </div>
                        
                    </form>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

