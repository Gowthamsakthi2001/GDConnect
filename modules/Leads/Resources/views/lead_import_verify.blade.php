<x-app-layout>
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title d-flex justify-content-between">            
               <div> <img src="{{asset('admin-assets/icons/custom/lead_verify.png')}}" class="img-fluid rounded"><span class="ps-2">Lead Import data's verify</span></div>
                <a class="btn btn-primary" href="{{route('admin.Green-Drive-Ev.leads.leads')}}">Back</a>
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
                            <h6>Telecaller Status </h6>
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
                                            <tr>
                                                <td>New Lead</td>
                                                <td>New</td>
                                            </tr>
                                            <tr>
                                                <td>Contacting</td>
                                                <td>Contacted</td>
                                            </tr>
                                            <tr>
                                                <td>Call Back Request</td>
                                                <td>Call_Back</td>
                                            </tr>
                                            <tr>
                                                <td>Onboarded</td>
                                                <td>Onboarded</td>
                                            </tr>
                                            <tr>
                                                <td>Dead Lead</td>
                                                <td>DeadLead</td>
                                            </tr>
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
                            <h6>Vehicle Types</h6>
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
                                            <tr>
                                                <td>2 wheeler</td>
                                                <td>1</td>
                                            </tr>
                                            <tr>
                                                <td>3 wheeler</td>
                                                <td>2</td>
                                            </tr>
                                            <tr>
                                                <td>4 wheeler</td>
                                                <td>3</td>
                                            </tr>
                                            <tr>
                                                <td>Rental</td>
                                                <td>4</td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mt-3">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Cities & Interested City</h6>
                            <div class="row mt-4">
                            @if(isset($cities) && count($cities) > 0)
                                @foreach($cities as $city)
                                  <div class="col-md-6 col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped text-center">
                                            <thead class="text-white" style="background:#17c653;">
                                                <tr>
                                                    <th colspan="2" class="text-center">City</th>
                                                </tr>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{$city->city_name}}</td>
                                                    <td>{{$city->id}}</td>
                                                </tr>
                                               
                                                <tr>
                                                    <th colspan="2" style="background:#17c653;" class="text-white">Interested Cities</th>
                                                </tr>
                                                <?php
                                                   $interested_cites = \Modules\City\Entities\Area::where('city_id',$city->id)->get();
                                                   
                                                ?>
                                                @if(isset($interested_cites) && count($interested_cites) > 0)
                                                    @foreach($interested_cites as $val)
                                                        <tr>
                                                            <td>{{$val->Area_name}}</td>
                                                            <td>{{$val->id}}</td>
                                                        </tr>
                                                    @endforeach
                                                 <tr>
                                                @else
                                                    <tr>
                                                        <td colspan="2">No Data Found</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endforeach
                             @else
                                 <tr>
                                    <td colspan="2">No Data Found</td>
                                </tr>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 mt-3">
           <div class="row">
               
               <div class="col-12 mb-3">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Telecallers</h6>
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
                                            @if(isset($telecallers) && count($telecallers) > 0)
                                               @foreach($telecallers as $telecaller)
                                                 <tr>
                                                     <td>{{$telecaller->telecaller_name}}</td>
                                                     <td>{{$telecaller->id}}</td>
                                                 </tr>
                                               @endforeach
                                            @else
                                             <tr>
                                                <td colspan="2">No Data Found</td>
                                            </tr>
                                            @endif
                                            
                                        </tbody>
                                    </table>
                               </div>
                            </div>
                        </div>
                    </div>
               </div>
               
               <div class="col-12">
                    <div class="card h-100">
                        <div class="card-body scrollable-content">
                            <h6>Source Names</h6>
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
                                            @if(isset($leadsource) && count($leadsource) > 0)
                                               @foreach($leadsource as $lead)
                                                 <tr>
                                                     <td>{{$lead->source_name}}</td>
                                                     <td>{{$lead->id}}</td>
                                                 </tr>
                                               @endforeach
                                            @else
                                             <tr>
                                                <td colspan="2">No Data Found</td>
                                            </tr>
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
    </div>
        
    </div>
@section('script_js')
<script>
    
</script>
@endsection
</x-app-layout>
