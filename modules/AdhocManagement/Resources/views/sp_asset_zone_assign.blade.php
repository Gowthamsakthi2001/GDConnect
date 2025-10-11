<x-app-layout>

    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h2 class="page-header-title">            
               <div class="d-flex justify-content-between">
                    <div>
                        <img src="{{asset('admin-assets/icons/custom/list_of_adhoc.png')}}" class="img-fluid rounded"><span class="ps-2">Update a Client</span>
                    </div>
                    
               </div>
            </h2>
        </div>
  
        <div class="tile">
            <div class="card mb-4">
                    <div class="card-header">
                        Update Client
                    </div>
                    <div class="card-body">
                      <form action="{{ route('admin.Green-Drive-Ev.adhocmanagement.sp_asset_assign_store', ['id' => $dm]) }}" method="POST">
                            @csrf
                                <!-- Asset Select Box -->
                        <!--<div class="col-md-6">-->
                        <!--    <div class="form-group">-->
                        <!--        <label for="asset"> Chassis Serial No</label>-->
                        <!--        <select name="asset" id="asset" class="form-control @error('asset') is-invalid @enderror">-->
                        <!--            <option value="" disabled selected>Choose an Chassis Serial No</option>-->
                        <!--            @foreach($AssetMasterVehicle as $d)-->
                        <!--                <option value="{{ $d->Chassis_Serial_No }}" -->
                        <!--                    {{ (isset($existingData) && $existingData->Chassis_Serial_No == $d->Chassis_Serial_No) ? 'selected' : '' }}>-->
                        <!--                    {{ $d->Chassis_Serial_No }}-->
                        <!--                </option>-->
                        <!--            @endforeach-->
                        <!--        </select>-->
                        <!--        @error('asset')-->
                        <!--            <div class="invalid-feedback">{{ $message }}</div>-->
                        <!--        @enderror-->
                        <!--    </div>-->
                        <!--</div>-->

                        <!-- Zone Select Box -->
                        <!--<div class="col-md-6">-->
                        <!--    <div class="form-group">-->
                        <!--        <label for="zone">Zone</label>-->
                        <!--        <select name="zone" id="zone" class="form-control @error('zone') is-invalid @enderror">-->
                        <!--            <option value="" disabled selected>Choose a Zone</option>-->
                        <!--            @foreach($zones as $zone)-->
                        <!--                <option value="{{ $zone->id }}" -->
                        <!--                    {{ (isset($existingData) && $existingData->zone_id == $zone->id) ? 'selected' : '' }}>-->
                        <!--                    {{ $zone->name }}-->
                        <!--                </option>-->
                        <!--            @endforeach-->
                        <!--        </select>-->
                        <!--        @error('zone')-->
                        <!--            <div class="invalid-feedback">{{ $message }}</div>-->
                        <!--        @enderror-->
                        <!--    </div>-->
                        <!--</div>-->

                        <!-- Client Select Box -->
                        <div class="row">
                            <div div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label for="client" class="">Client <span class="text-danger">*</span></label>
                                    <select name="client" id="client" class="form-control @error('client') is-invalid @enderror" onchange="Get_hub(this)">
                                        <option value="">Choose a Client</option>
                                        @if(isset($Client))
                                            @foreach($Client as $c)
                                                <option value="{{ $c->id }}" 
                                                    {{ (isset($existingData) && $existingData->client_id == $c->id) ? 'selected' : '' }}>
                                                    {{ $c->client_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('client')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                             <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label for="hub">Choose a Hub</label>
                                    <select name="hub" id="hub" class="form-control @error('hub') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @if(isset($client_hubs))
                                             @foreach($client_hubs as $val)
                                                <option value="{{ $c->id }}" 
                                                    {{ (isset($existingData) && $existingData->hub_id == $val->id) ? 'selected' : '' }}>
                                                    {{ $val->hub_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('hub')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                            <div class="text-end">
                                <button type="submit" class="btn custom-btn-primary btn-sm">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
        </div>
@section('script_js')
<script>

function Get_hub(selectElement) {
        var id = $(selectElement).val();
        if (id != "") {
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.delivery-man.filter_hub') }}",
                type: "GET",
                data: { id: id },
                success: function(response) {
                    console.log(response);
                    var opt = '<option value="">Select</option>'; // Reset options
                    if (response.success && response.hubs.length > 0) {
                        $.each(response.hubs, function(key, val) {
                            opt += `<option value="${val.id}">${val.hub_name}</option>`;
                        });
                    } else {
                        opt += '<option value="">No Hub\'s Data</option>';
                    }
                    $("#hub").html(opt); 
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) { 
                        $.each(xhr.responseJSON.errors, function(key, value) { 
                            toastr.error(value[0]); 
                        });
                    } else {
                        toastr.error("Please try again.");
                    }
                }
            });
        }
    }
</script>
@endsection
</x-app-layout>
