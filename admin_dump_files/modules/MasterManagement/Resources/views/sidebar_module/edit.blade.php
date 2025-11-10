<x-app-layout>
<style>
        #map {
            width: 100%;
            height: 300px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-control{
                padding: 12px 20px !important;
        }
    </style>

    <div class="main-content">

        <div class="card bg-transparent my-4">
            <div class="card-header" style="background:#fbfbfb;">
                <div class="row g-3">
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="card-title h5 custom-dark m-0"> <a
                                href="{{route('admin.Green-Drive-Ev.master_management.sidebar_module.index')}}"
                                class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Update a Module
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card my-3">

            <div class="card-body pt-3">
                <form action="javascript:void(0);" id="ModuleUpdateForm" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    
                    <input type="hidden" id="ModuleUpdateUrl" value="{{ route('admin.Green-Drive-Ev.master_management.sidebar_module.data.update',['id'=>$module->id]) }}">
                    <div class="row">

                            <div class="col-md-6 col-12 mb-3">
                                <label class="input-label mb-2 ms-1">Module Name <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control" name="module_name" id="module_name"
                                       placeholder="" value="{{$module->module_name ?? ''}}">
                            </div>


                            <div class="col-md-6 col-12 mb-3">
                                <label class="input-label mb-2 ms-1">Route Name <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control" name="route_name" id="route_name" value="{{$module->route_name ?? ''}}">
                            </div>
                           <div class="col-md-6 col-12 mb-3">
                                <label class="input-label mb-2 ms-1">
                                    Assign Roles <span class="text-danger fw-bold">*</span>
                                </label>
                                <select class="form-select custom-select2-field form-control-sm" 
                                        name="assign_roles[]" id="assign_roles" multiple>
                                    <option value="">Select</option>
                            
                                    @if(isset($roles))
                                        @foreach($roles as $val)
                                            <option value="{{ $val->id }}" 
                                                @if(in_array($val->id, $module->view_roles_id ?? [])) selected @endif>
                                                {{ $val->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            
                            <div class="col-md-6 col-12 mb-3">
                                <label for="image" class="form-label fw-semibold">
                                    Image <span class="text-danger">*</span>
                                </label>
                            
                                <!-- File input -->
                                <input type="file"
                                    class="form-control"
                                    id="image"
                                    name="image"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    onchange="showImagePreview(this)">
                            </div>

                            
                            
                            <div class="col-md-6 col-12 order-md-1 order-2 mb-3">
                                <label class="input-label mb-2 ms-1">Status <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select custom-select2-field form-control-sm" 
                                        name="status" id="status">
                                    <option value="1" {{$module->status == 1 ? 'selected' : ''}}>Active</option>
                                    <option value="0" {{$module->status == 0 ? 'selected' : ''}}>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-12 order-md-2 order-1 mb-3">
                                <!-- Image preview -->
                                <div class="mt-3 text-center">
                                    <img id="imagePreview"
                                         src="{{asset('admin-assets/sidebar_icon/'.$module->image)}}"
                                         alt=""
                                         class="img-fluid rounded shadow-sm border"
                                         style="max-width: 100%; width:50px; height: 50px; object-fit: cover;">
                                </div>
                            </div>
                    </div>

                    <!-- Buttons Row -->
                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-success" id="UpdateModuleBtn">Save Module</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

    @section('script_js')

    <script>
        function showImagePreview(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImage = document.getElementById('imagePreview');
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block'; 
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script>

       $("#ModuleUpdateForm").submit(function (e) {
            e.preventDefault();
         
            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append("_token", "{{ csrf_token() }}");
        
            var $submitBtn = $("#UpdateModuleBtn");
            var originalText = $submitBtn.html();
            $submitBtn.prop("disabled", true).html("⏳ Submitting...");
           var update_url  = $("#ModuleUpdateUrl").val();

            $.ajax({
                url: update_url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $submitBtn.prop("disabled", false).html(originalText);

                    if (response.success) {
                        // ✅ Proper form reset
                        // $("#ModuleUpdateForm")[0].reset();

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            html: response.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'swal-wide'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // ✅ Redirect to zone list
                                window.location.href = "{{ route('admin.Green-Drive-Ev.master_management.sidebar_module.index') }}";
                            }
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
                error: function (xhr, status, error) {
                    $submitBtn.prop("disabled", false).html(originalText);
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
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