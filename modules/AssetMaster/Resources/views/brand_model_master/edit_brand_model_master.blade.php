<x-app-layout>
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.asset_management.brand_model_master.list') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Update Brand Model
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{ route('admin.asset_management.brand_model_master.list') }}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>
          
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.asset_management.brand_model_master.update_data') }}" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chessis_number">Brand Model</label>
                                <input type="text" class="form-control bg-white" name="brand_model" id="brand_model" value="{{$brand->brand_name}}" placeholder="Enter Brand Model">

                            </div>
                        </div>
                         <input type="hidden" class="form-control bg-white" name="brand_id"  value="{{$brand->id}}">
                      
                        
                        <div class="col-12 text-end gap-4">
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
