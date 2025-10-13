<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <span>Edit City</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{ route('admin.Green-Drive-Ev.City.update', ['id' => $city->id]) }}" method="post" class="row g-3 p-3">
                        @csrf
                        
                        <!-- City Name Field -->
                        <div class="col-md-6">
                            <label class="input-label mb-2 ms-1" for="city_name">{{ __('City Name') }}</label>
                            <input type="text" name="city_name" id="city_name" class="form-control" placeholder="{{ __('City Name') }}" value="{{ old('city_name', $city->city_name) }}" maxlength="191">
                            @error('city_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                      
                         <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="short_code">{{ __('Short Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="short_code" id="short_code" class="form-control" maxlength="3" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')" placeholder="{{ __('Ex : BLR') }}" value="{{ old('short_code', $city->short_code) }}">
                                    <small class="text-note text-muted">Only 3 letters allowed. Must be capital letters.</small>
                                    @error('short_code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        
                        <!-- Status Dropdown -->
                        <div class="col-md-6">
                            <label for="statusSelect" class="mb-2 ms-1">Status</label>
                            <select class="form-control basic-single" id="statusSelect" name="status">
                                <option value="1" {{ $city->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $city->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-md-flex d-flex d-grid align-items-center justify-content-end text-white gap-3">
                            <button type="submit" class="btn btn-success btn-round">{{ __('Submit') }}</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
