<x-app-layout>
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{ route('admin.asset_management.brand_model_master.list') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Create Brand Model
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
                    <form id="BrandModelForm" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chessis_number">Brand Model</label>
                                <input type="text" class="form-control bg-white" name="brand_model" id="brand_model" value="{{ old('brand_model') }}" placeholder="Enter Brand Model" required>
                                <div id="brand_model_error" class="text-danger"></div>
                            </div>
                        </div>
                      
                        
                        <div class="col-12 text-end gap-4">
                            <button type="reset" class="btn btn-danger px-6 p-2">Reset</button>
                            <button type="submit" id="submitBtn"  class="btn btn-success px-6 p-2">Create</button>
                        </div>
               
                    </div>
                    </form>
                </div>
            </div>
            
    </div>
    
   
@section('script_js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#BrandModelForm').submit(function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let submitBtn = $('#submitBtn');
        let originalText = submitBtn.text();

        // Clear previous errors
        $('#brand_model_error').text('');

        // Disable submit button
        submitBtn.prop('disabled', true).text('Submitting...');

        $.ajax({
            url: "{{ route('admin.asset_management.brand_model_master.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    form.reset();
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.asset_management.brand_model_master.list') }}";
                    }, 1200);
                } else {
                    toastr.error(response.message || 'Something went wrong.');
                    submitBtn.prop('disabled', false).text(originalText);
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false).text(originalText);

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors.brand_model) {
                    $('#brand_model_error').text(errors.brand_model[0]);
                    toastr.error(errors.brand_model[0]);
                }
            } else if (xhr.status === 409) {
                // 💡 Show the duplicate error from response
                toastr.error(xhr.responseJSON.message);
            } else {
                toastr.error('An unexpected error occurred.');
            }
            }
        });
    });
});
</script>
@endsection
</x-app-layout>
