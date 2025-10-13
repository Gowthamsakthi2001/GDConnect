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
                            <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.rider_onboard.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Edit Rider Onboarding</div>
                        </div>
                    </div>
                </div>
            </div>
        
        
            <div class="card">
                <div class="card-body">
                    
                    <form id="UpdateRiderOnboardForm" action="#" method="POST">
                        @csrf
                         <input type="hidden" id="Edit_url" value="{{route('admin.Green-Drive-Ev.rider_onboard.update',$edit_data->id)}}">
                         <div class="row g-4">
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Role Type <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="role_type" name="role_type" onchange="FetchRole_User(this.value)">
                                    <option value="">Select</option>
                                    <option value="deliveryman" {{$edit_data->role_type == "deliveryman" ? 'selected' : ''}}>Rider</option>
                                    <option value="adhoc" {{$edit_data->role_type == "adhoc" ? 'selected' : ''}}>Adhoc</option>
                                    <option value="helper" {{$edit_data->role_type == "helper" ? 'selected' : ''}}>Helper</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Onboarded Date <span class="text-danger">*</span></label>
                                <input type="date" id="onboard_date" name="onboard_date" class="form-control" placeholder="DD-MM-YYYY" value="{{$edit_data->onboard_date}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">ID <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field auto-fetch-field" id="id" name="id">
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
                                <select class="form-control bg-white custom-select2-field auto-fetch-field" id="name" name="name">
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
                                <select class="form-control bg-white custom-select2-field fetch-client-id" id="client_id" name="client_id" onchange="Fetchhubs(this.value)">
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
                                <select class="form-control bg-white custom-select2-field fetch-client-id" id="client_name" name="client_name" onchange="Fetchhubs(this.value)">
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
                                <select class="form-control bg-white custom-select2-field" name="city" id="city">
                                    <option value="">Select City</option>
                                    @if($cities)
                                    @foreach($cities as $city)
                                    <option value="{{$city->id}}" {{$edit_data->city_id == $city->id ? 'selected' : ''}}>
                                        {{$city->name}}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Hub</label>
                                <select class="form-control bg-white custom-select2-field" name="hub" id="hub">
                                    <option value="">Select Hub</option>
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
        
                        <div class="col-12 text-end gap-2">
                            <button type="button" class="btn btn-danger px-5" onclick="window.location.href='{{route('admin.Green-Drive-Ev.rider_onboard.index')}}'">Cancel</button>
                            <button type="submit" class="btn btn-success px-5">Update</button>
                        </div>
                    </form>
                </div>
        
           
            </div>
       </div>

@section('script_js')

    <script>
        $("#UpdateRiderOnboardForm").submit(function(e) {
        e.preventDefault();
    
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");
    
        var $submitBtn = $("#submitBtn");
        var originalText = $submitBtn.html();
        $submitBtn.prop("disabled", true).html("⏳ Submitting...");
        var url = $("#Edit_url").val();
        $.ajax({
            url:url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
    
                $submitBtn.prop("disabled", false).html(originalText);
    
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href="{{route('admin.Green-Drive-Ev.rider_onboard.index')}}";
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                $submitBtn.prop("disabled", false).html(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Please try again.");
                }
            }
        });
    });
    
    function FetchRole_User(type) {
        if(type != "") {
            var formData = new FormData();
            formData.append("type", type);
            
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.rider_onboard.fetch_riderdetail') }}",
                type: "GET",
                data: {type:type},
                success: function(response) {
                    if(response.success) {
                        let idOptions = '<option value="">Select</option>';
                        let nameOptions = '<option value="">Select</option>';
                        if(response.data.length > 0){
                            response.data.forEach(function(item) {
                                idOptions += `<option value="${item.id}" data-fetch_select_id="${item.id}">${item.emp_id}</option>`;
                                nameOptions += `<option value="${item.id}" data-fetch_select_id="${item.id}">${item.first_name} ${item.last_name}</option>`;
                            });
        
                            $('#id').html(idOptions).trigger('change');
                            $('#name').html(nameOptions).trigger('change');
                        }else{
                            toastr.error("No data found.");
                            $("#id").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                            $("#name").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                        }
                    } else {
                        toastr.error(response.message || "No data found.");
                        $("#id").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                        $("#name").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Please try again.");
                }
            });
        } else {
            $("#id").html('<option value="">Select</option> ').trigger('change');
            $("#name").html('<option value="">Select</option>').trigger('change');
        }
    }
    
    const selectedCustomerId = "{{ $edit_data->customer_master_id }}";
    const selectedHubId = "{{ $edit_data->hub_id }}";
    function Fetchhubs(id) {
        if(id != "") {
            var formData = new FormData();
            formData.append("id", id);
            
            $.ajax({
                url: "{{ route('admin.Green-Drive-Ev.rider_onboard.fetch_hubsdetail') }}",
                type: "GET",
                data: {id:id},
                success: function(response) {
                    if(response.success) {
                        
                        let hubOptions = '<option value="">Select</option>';

                        if(response.data.length > 0){
                            let hubOptions = '<option value="">Select</option>';
                            response.data.forEach(function(item) {
                                if (Array.isArray(item.operational_hubs) && item.operational_hubs.length > 0) {
                                    item.operational_hubs.forEach(function(hub) {
                                    const selected = selectedHubId == hub.id ? 'selected' : '';
                                    hubOptions += `<option value="${hub.id}" ${selected}>${hub.hub_name}</option>`;
                                    });
                                }
                            });
                      $('#hub').html(hubOptions).trigger('change');
                        }else{
                            toastr.error("No data found.");
                            $("#hub").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                        }
                    } else {
                        toastr.error(response.message || "No data found.");
                        $("#hub").html('<option value="">Select</option><option value="">Data Not Found</option>').trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("Please try again.");
                }
            });
        } else {
            $("#hub").html('<option value="">Select</option> ').trigger('change');
        }
    }
    
  $(".auto-fetch-field").on('change', function () {
        let selectedValue = $(this).val();
        $(".auto-fetch-field").each(function () {
            if ($(this).val() !== selectedValue) {
                $(this).val(selectedValue).trigger('change.select2');
            }
        });
    });
    
    $(".fetch-client-id").on('change', function () {
        let selectedValue = $(this).val();
        $(".fetch-client-id").each(function () {
            if ($(this).val() !== selectedValue) {
                $(this).val(selectedValue).trigger('change.select2');
            }
        });
    });
    
    </script>
    
    <script>
    $(document).ready(function() {
        if (selectedCustomerId) {
            Fetchhubs(selectedCustomerId, selectedHubId);
        }
    });
</script>

@endsection
</x-app-layout>
