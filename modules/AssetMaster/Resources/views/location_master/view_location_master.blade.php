<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.location_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> View Location
                              </div>
                        </div>


                    </div>
                   
                </div>
            </div>
            
    <!-- End Page Header -->
    
    
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card h-100">
                <div class="card-body">
                    <form  method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="name">Name</label>
                                    <input type="text" class="form-control bg-white" name="name" id="name" value="{{$location->name ?? ''}}" placeholder="Enter Name">
                                </div>
                            </div>
                            
                             <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="city">City</label>
                                    <!--<input type="text" class="form-control bg-white" name="city" id="city" value="{{ \Modules\City\Entities\City::find($location->city)->city_name ?? '' }}"  placeholder="Enter City">-->
                                         <select class="form-control bg-white custom-select2-field" name="city" id="city" disabled>
                                            <option value="">Select City</option>
                                            @if(isset($city))
                                                @foreach($city as $c)
                                                    <option value="{{ $c->id }}" {{ (isset($location) && $location->city == $c->id) ? 'selected' : '' }}>
                                                        {{ $c->city_name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                   
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        <div class="row mb-3 mb-4">
                            
                                <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="name">City Code</label>
                                    <input type="text" class="form-control bg-white" name="name" id="name" value="{{$location->city_code ?? ''}}" placeholder="Enter Name">
                                </div>
                            </div>
                            
                           <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="state">State</label>
                                    <!--<input type="text" class="form-control bg-white" name="state" id="state" value="{{$location->state ?? ''}}" placeholder="Enter State">-->
                                    <select class="form-control bg-white custom-select2-field" name="state" id="state" disabled>
                                        <option value="">Select State</option>
                                        @if(isset($states))
                                            @foreach($states as $s)
                                                <option value="{{ $s->id }}" {{ (isset($location) && $location->state == $s->id) ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                       </div>    
                       
                         <div class="row mb-3">
                             
                            <div class="col-6">
                                <div class="form-group">
                                    <h5 class="custom-dark">List Of Hubs</h5>
                                   
                                </div>
                            </div>
                            
                            
                          </div>      
                       
                         @php
                            $hubs = $location->location_hubs ?? [];
                            $sno = 1;
                        @endphp
                        @if(isset($hubs) && count($hubs) > 0)
                            @foreach($hubs as $i => $hub)
                                <!-- Hub Name Rows -->
                                <div class="row mb-3" id="hub_name_show_rows">
                                  <div class="col-12 mb-3 hub-row">
                                    <label class="input-label mb-2 ms-1">Hub {{ str_pad($sno++, 2, '0', STR_PAD_LEFT) }}</label>
                                    <div class="input-group">
                                      <input type="text" class="form-control" value="{{ $hub->hub_name }}" name="hub_name[]" readonly>
                                    </div>
                                  </div>
                                </div>
                            @endforeach
                        @else
                           <div class="row mb-3" id="hub_name_show_rows">
                              <div class="col-12 mb-3 text-start">
                                <label class="input-label mb-2 ms-1">No Hubs </label>
                                
                              </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script_js')

@endsection
</x-app-layout>

