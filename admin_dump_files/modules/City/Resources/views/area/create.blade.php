<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-header-title">            
            <span>Add Area</span>
        </h2>
    </div>
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form action="{{ route('admin.Green-Drive-Ev.Area.store') }}" method="post" class="row g-3 p-3">
                        @csrf
                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1"  for="city_id">City</label>
                                    <select class="form-control basic-single" id="city_id" name="city_id">
                                        @foreach($City as $data)
                                            <option value="{{ $data->id }}" {{ old('city_id') == $data->id ? 'selected' : '' }}>
                                                {{ $data->city_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                    <label class="input-label mb-2 ms-1" for="area_name">{{ __('Area Name') }}</label>
                                    <input type="text" name="area_name" id="area_name" class="form-control" placeholder="{{ __('Area Name') }}" value="{{ old('area_name') }}" maxlength="191">
                            </div>
                            
                            <div class="col-md-4">
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

