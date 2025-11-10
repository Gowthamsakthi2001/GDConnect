<x-app-layout>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        @media screen and (min-width:768px) {
            .four-card {
                font-size: 18px;
            }
        }


    </style>


        <div class="main-content">
        
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.rider_onboard.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> View Rider Onboarding</div>
                        </div>
                    </div>
                </div>
            </div>
        
        
            <div class="card">
                <div class="card-body">
                    
                    <form action="#" method="POST">
                        @csrf
        
                        <div class="row g-4">
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Role Type <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="role_type" name="role_type" disabled>
                                    <option value="">Select</option>
                                    <option value="deliveryman" {{$edit_data->role_type == "deliveryman" ? 'selected' : ''}}>Rider</option>
                                    <option value="adhoc" {{$edit_data->role_type == "adhoc" ? 'selected' : ''}}>Adhoc</option>
                                    <option value="helper" {{$edit_data->role_type == "helper" ? 'selected' : ''}}>Helper</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Onboarded Date <span class="text-danger">*</span></label>
                                <input type="date" id="onboard_date" name="onboard_date" class="form-control" placeholder="DD-MM-YYYY" value="{{$edit_data->onboard_date}}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">ID <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="id" name="id" disabled>
                                    <option value="">Select</option>
                                    @if(isset($deliveryman_data))
                                        @foreach($deliveryman_data as $data)
                                            <option value="{{ $data->id }}" {{$edit_data->dm_id == $data->id ? 'selected' : ''}}>
                                                {{ $data->emp_id }}
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
        
        
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Name <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="name" name="name" disabled>
                                    <option value="">Select</option>
                                    @if(isset($deliveryman_data))
                                        @foreach($deliveryman_data as $data)
                                            <option value="{{ $data->id }}" {{$edit_data->dm_id == $data->id ? 'selected' : ''}}>
                                                {{ $data->first_name }}  {{ $data->last_name }}
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
        
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Client ID <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="client_id" name="client_id" disabled>
                                    <option value="">Select</option>
                                   @if(isset($customers))
                                        @foreach($customers as $data)
                                            <option value="{{ $data->id }}" {{$edit_data->customer_master_id == $data->id ? 'selected' : ''}}>
                                                {{ $data->id }}
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
        
        
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Client Name (Trade Name)<span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="client_name" name="client_name" disabled>
                                    <option value="">Select</option>
                                     @if(isset($customers))
                                        @foreach($customers as $data)
                                            <option value="{{ $data->id }}" {{$edit_data->customer_master_id == $data->id ? 'selected' : ''}}>
                                                {{ $data->trade_name }}
                                            </option>
                                        @endforeach 
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">City</label>
                                <select class="form-control bg-white custom-select2-field" name="city" id="city" disabled>
                                    <option value="">Select City</option>
                                    @if($cities)
                                    @foreach($cities as $city)
                                    <option value="{{$city->id}}"  {{$edit_data->city_id == $city->id ? 'selected' : ''}}>{{$city->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Hub</label>
                                <select class="form-control bg-white custom-select2-field" name="hub" id="hub" disabled>
                                    <option value="">Select Hub</option>
                                     @if($hubs)
                                    @foreach($hubs as $hub)
                                    <option value="{{$hub->id}}"  {{$edit_data->hub_id == $hub->id ? 'selected' : ''}}>{{$hub->hub_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
        
                            <!--<div class="col-md-6">-->
                            <!--    <label class="input-label mb-2 ms-1">Vehicle ID</label>-->
                            <!--    <select class="form-control bg-white">-->
                            <!--        <option >Select Vehicle ID</option>-->
                            <!--        <option value="1">Vehicle 1</option>-->
                            <!--        <option value="2">Vehicle 2</option>-->
                            <!--        <option value="3">Vehicle 3</option>-->
                            <!--         Add more vehicle options here -->
                            <!--    </select>-->
                            <!--</div>-->
        
        
                            <!--<div class="col-md-6">-->
                            <!--    <label class="input-label mb-2 ms-1">Vehicle Type</label>-->
                            <!--    <input type="text" class="form-control" placeholder="Enter Vehicle Type">-->
                            <!--</div>-->
        
                            <!--<div class="col-md-6">-->
                            <!--    <label class="input-label mb-2 ms-1">Vehicle Model</label>-->
                            <!--    <input type="text" class="form-control" placeholder="Enter Vehicle Model">-->
                            <!--</div>-->
        
                            <!--<div class="col-md-6">-->
                            <!--    <label class="input-label mb-2 ms-1">Vehicle Variant</label>-->
                            <!--    <input type="text" class="form-control" placeholder="Enter Vehicle Variant">-->
                            <!--</div>-->
        
                            
                        </div>
        
                    </form>
                </div>
        
            </div>
        </div>

        @section('script_js')





            <script></script>
        @endsection
</x-app-layout>
