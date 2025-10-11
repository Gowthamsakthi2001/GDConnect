<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title d-flex justify-content-between">            
               <div> <img src="{{asset('admin-assets/icons/custom/lead_verify.png')}}" class="img-fluid rounded"><span class="ps-2">Quality Check Import data's verify</span></div>
                <a class="btn btn-dark  px-5" href="{{route('admin.asset_management.quality_check.list')}}"><i class="bi bi-arrow-left me-2"></i>Back</a>
            </h2>
        </div>
        <!-- End Page Header -->
        
        
            <!--page card-->
    <div class="row">
        <div class="col-md-8 col-12">
            <div class="row">
                 <div class="col-md-6 col-6 mt-3">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Vehicle Type </h6>
                            <div class="row mt-4">
                               <div class="table-responsive">
                                   <table class="table table-bordered table-striped">
                                        <thead class="text-white" style="background:#17c653;">
                                            <tr>
                                                <th>Name</th>
                                                <th>ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($vehicle_types))
                                            @foreach($vehicle_types as $type)
                                            <tr>
                                                <td>{{$type->name}}</td>
                                                <td>{{$type->id}}</td>
                                            </tr>
                                            @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-6 mt-3">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Vehicle Model</h6>
                            <div class="row mt-4">
                               <div class="table-responsive">
                                   <table class="table table-bordered table-striped">
                                        <thead class="text-white" style="background:#17c653;">
                                            <tr>
                                                <th>Name</th>
                                                <th>ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($vehicles))
                                            @foreach($vehicles as $vehicle)
                                            <tr>
                                                <td>{{$vehicle->vehicle_model}}</td>
                                                <td>{{$vehicle->id}}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            
                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                  <div class="col-md-6 col-6 mt-3">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Location</h6>
                            <div class="row mt-4">
                               <div class="table-responsive">
                                   <table class="table table-bordered table-striped">
                                        <thead class="text-white" style="background:#17c653;">
                                            <tr>
                                                <th>Name</th>
                                                <th>ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                              @if(isset($location))
                                            @foreach($location as $l)
                                            <tr>
                                                <td>{{$l->name}}</td>
                                                <td>{{$l->id}}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            
                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-12 mt-3">
           <div class="row">
               

               
           </div>
        </div>
    </div>
        
    </div>
@section('script_js')
<script>
    
</script>
@endsection
</x-app-layout>
