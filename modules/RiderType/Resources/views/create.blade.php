<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <span>Add Driver Type</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{route('admin.Green-Drive-Ev.rider-type.store')}}" method="post" class="row g-3 p-3">
                        @csrf
                        
                       
                        <div class="col-md-6">
                                    <label class="input-label mb-2 ms-1" for="city_name">{{ __('Rider Type') }}</label>
                                    <input type="text" name="type" id="type" class="form-control" placeholder="{{ __('Rider Type') }}" value="{{ old('rider_type') }}" maxlength="191">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                            
                            <div class="col-md-6">
                                    <label for="statusSelect" class="mb-2 ms-1">Status</label>
                                    <select class="form-control basic-single" id="statusSelect" name="status">
                                        <option value="1" >Active</option>
                                        <option value="0" >Inactive</option>
                                    </select>
                            </div>
                            


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

