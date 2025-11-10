<x-app-layout>
   <div class="main-content">
    <!-- Page Header -->
             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.location_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Create Location
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
                    <form action="javascript:void(0);" id="StoreLocationMasterForm" method="post" autocomplete="off">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="name">Name</label>
                                    <input type="text" class="form-control bg-white" name="name" id="name" value="" placeholder="Enter Name">
                                </div>
                            </div>
                            
                             <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="city">City</label>
                                    <select class="form-control bg-white custom-select2-field" name="city" id="city">
                                        <option value="">Select City</option>
                                        @if(isset($city))
                                        @foreach($city as $c)
                                        <option value="{{$c->id}}">{{$c->city_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        <div class="row mb-3 mb-4">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="city_code">City Code</label>
                                    <input type="text" class="form-control bg-white" name="city_code" id="city_code" value="" placeholder="Enter City Code">
                                </div>
                            </div>
                            
                           <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="state">State</label>
                                    <!--<input type="text" class="form-control bg-white" name="state" id="state" value="" placeholder="Enter State">-->
                                 <select class="form-control bg-white custom-select2-field" name="state" id="state">
                                        <option value="">Select State</option>
                                        @if(isset($states))
                                        @foreach($states as $s)
                                        <option value="{{$s->id}}">{{$s->state_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                       </div>    
                       
                         <div class="row mb-3">
                             
                            <div class="col-6">
                                <div class="form-group">
                                    <h5 class="custom-dark">Add Muliple Hubs</h5>
                                   
                                </div>
                            </div>
                            
                             
                             
                                <div class="col-6 text-end">
                                <a href="javascript:void(0);" class="btn btn-success btn-sm p-2" id="add-hub">
                                    <svg class="svg-inline--fa fa-circle-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle-plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                        <path fill="currentColor" d="M256 512c141.4 0 256-114.6 256-256S397.4 0 256 0S0 114.6 0 256S114.6 512 256 512zM232 344V280H168c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V168c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H280v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"></path>
                                    </svg>
                                    Add Multiple
                                </a>
                            </div>
                            
                          </div>      
                       
                       
                       
                        
                                                    <!-- Hub Name Rows -->
                        <div class="row mb-3" id="hub_name_show_rows">
                          <div class="col-12 mb-3 hub-row">
                            <label class="input-label mb-2 ms-1">Hub 01</label>
                            <div class="input-group">
                              <input type="text" class="form-control" name="hub_name[]" placeholder="Enter Hub 01" required>
                              <button class="btn btn-danger" type="button">
                                <i class="bi bi-trash-fill text-white"></i>
                              </button>
                            </div>
                          </div>
                        </div>

                        
                        
                        <div class="col-12 text-end gap-4">
                            <button type="button" class="btn btn-danger px-6 p-2"  onclick="reset_call_function()">Reset</button>
                            <button type="submit" class="btn btn-success px-6 p-2">Create</button>
                        </div>
                        
                        

                        
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('script_js')
<script>
   $(document).ready(function () {
    let hubCount = 1;

    $('#add-hub').click(function () {
      hubCount++;
      const paddedHubNum = hubCount.toString().padStart(2, '0');
      const hubRow = `
        <div class="col-12 mb-3 hub-row">
          <label class="input-label mb-2 ms-1">Hub ${paddedHubNum}</label>
          <div class="input-group">
            <input type="text" class="form-control" name="hub_name[]" placeholder="Enter Hub ${paddedHubNum}" required>
            <button class="btn btn-danger remove-hub" type="button">
              <i class="bi bi-trash-fill text-white"></i>
            </button>
          </div>
        </div>
      `;
      $('#hub_name_show_rows').append(hubRow);
    });

    $(document).on('click', '.remove-hub', function () {
      $(this).closest('.hub-row').remove();

      // Reset label numbers
      hubCount = 0;
      $('#hub_name_show_rows .hub-row').each(function (i) {
        hubCount++;
        const num = hubCount.toString().padStart(2, '0');
        $(this).find('label').text('Hub ' + num);
        $(this).find('input').attr('placeholder', 'Enter Hub ' + num);
      });
    });
  });
  
    
    
    
    
    function reset_call_function(){
        window.location.reload();
    }
    
   $("#StoreLocationMasterForm").submit(function(e){
    e.preventDefault();

    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");
    var redirect = "{{ route('admin.asset_management.location_master.list') }}";

    var submitBtn = $(this).find('button[type="submit"]');
    var originalText = submitBtn.text();

    // Prevent multiple submissions
    submitBtn.prop('disabled', true).text('Submitting...');

    $.ajax({
        url: "{{ route('admin.asset_management.location_master.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success === true) {
                toastr.success(response.message);
                form.reset();
                $("#hub_name_show_rows").html("");
                setTimeout(function(){
                    window.location.href = redirect;
                }, 1000);
            } else {
                toastr.error(response.message || "Something went wrong.");
                submitBtn.prop('disabled', false).text(originalText);
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).text(originalText);
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

</script>
@endsection
</x-app-layout>

