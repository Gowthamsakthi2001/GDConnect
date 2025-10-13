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
                            <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.Green-Drive-Ev.rider_onboard.index')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Create Rider Onboarding</div>
                        </div>
                    </div>
                </div>
            </div>
        
        
            <div class="card">
                <div class="card-body">
                    
                    <form id="StoreRiderOnboardForm" method="POST">
                        @csrf
        
                        <div class="row g-4">
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Role Type <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field" id="role_type" name="role_type" onchange="FetchRole_User(this.value)">
                                    <option value="">Select</option>
                                    <option value="deliveryman">Rider</option>
                                    <option value="adhoc">Adhoc</option>
                                    <option value="helper">Helper</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Onboarded Date <span class="text-danger">*</span></label>
                                <input type="date" id="onboard_date" name="onboard_date" class="form-control" placeholder="DD-MM-YYYY" value="{{date('Y-m-d')}}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">ID <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field auto-fetch-field" id="id" name="id">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <?php
                            //  if(isset($deliveryman_data))
                            //  foreach($deliveryman_data as $data)
                            //  <option value="{{ $data->id }}"> {{ $data->first_name }}  {{ $data->last_name }} </option>
                            //  endforeach 
                            //  endif
                            ?>
        
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Name <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field auto-fetch-field" id="name" name="name">
                                    <option value="">Select</option>
                                   
                                </select>
                            </div>
        
                            <div class="col-md-6 mb-3">
                                <label class="input-label mb-2 ms-1">Client ID <span class="text-danger">*</span></label>
                                <select class="form-control bg-white custom-select2-field fetch-client-id" id="client_id" name="client_id" onchange="Fetchhubs(this.value)">
                                    <option value="">Select</option>
                                   @if(isset($customers))
                                        @foreach($customers as $data)
                                            <option value="{{ $data->id }}" data-fetch_client_id="{{$data->id}}"> {{ $data->id }}  </option>
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
                                            <option value="{{ $data->id }}"> {{ $data->trade_name }} </option>
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
                                    <option value="{{$city->id}}">{{$city->name}}</option>
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
                            <button type="reset" class="btn btn-danger px-5">Reset</button>
                            <button type="submit" class="btn btn-success px-5" id="submitBtn">Create</button>
                        </div>
                    </form>
                </div>
        
           
        </div>
        
    </div>

@section('script_js')

<script>
        
    $("#StoreRiderOnboardForm").submit(function(e) {
        e.preventDefault();
    
        var form = $(this)[0];
        var formData = new FormData(form);
        formData.append("_token", "{{ csrf_token() }}");
    
        var $submitBtn = $("#submitBtn");
        var originalText = $submitBtn.html();
        $submitBtn.prop("disabled", true).html("⏳ Submitting...");
    
        $.ajax({
            url: "{{route('admin.Green-Drive-Ev.rider_onboard.store')}}",
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
                                        hubOptions += `<option value="${hub.id}">${hub.hub_name}</option>`;
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
@endsection
</x-app-layout>
