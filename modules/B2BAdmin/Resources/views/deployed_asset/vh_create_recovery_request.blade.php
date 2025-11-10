<x-app-layout>
<style>
    .attachment-preview {
        border: 1px dashed #ccc;
        border-radius: 8px;
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        position: relative;
        background-color: #fdfdfd;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .attachment-preview:hover {
        border-color: #007bff;
        background-color: #f9f9f9;
    }

    .preview-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        cursor: pointer;
        transition: transform 0.2s;
        border-radius: 4px;
    }

    .preview-image:hover {
        transform: scale(1.02);
    }

    .preview-pdf {
        width: 100%;
        height: 100%;
        border: none;
    }

    .d-none {
        display: none !important;
    }
</style>

<div class="main-content">

    <!-- Header Section -->
    <div class="my-4 bg-white rounded">
        <div class="p-3  rounded" >
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <!-- Title -->
                <h5 class="m-0 text-truncate custom-dark">
                    Create Recovery Request
                </h5>


                <!-- Back Button -->
                <a href="{{ route('b2b.admin.deployed_asset.list') }}"
                    class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">

            <form id="AdminrecoveryRequestForm" enctype="multipart/form-data">
                @csrf
                <div class="row">


                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="client_business_name">Date and Time of the Request</label>-->
                    <!--        <input type="datetime-local" class="form-control bg-white" name="datetime" id="datetime" style="padding:12px 20px;" >-->
                    <!--    </div>-->
                    <!--</div>-->
                    <?php
                      $recovery_reasons = \Modules\MasterManagement\Entities\RecoveryReasonMaster::where('status',1)->get();
                    ?>

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="reason_for_recovery">Reason For Recovery <span class="text-danger">*</span></label>
                            <select class="form-select custom-select2-field" name="reason_for_recovery" id="reason_for_recovery">
                                <option value="">Select</option>
                                @if(isset($recovery_reasons) && count($recovery_reasons) > 0)
                                        @foreach($recovery_reasons as $val)
                                        <option value="{{$val->id}}">{{$val->label_name}}</option>
                                        @endforeach
                                @else
                                   <option value="">No Data</option>
                                @endif
                            </select>
                        </div>
                    </div>



                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle Register Number</label>
                            <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{ $data['vehicle']['permanent_reg_number']}}" placeholder="Enter Vehicle ID" readonly>
                        </div>
                    </div>



                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number</label>
                            <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{ $data['vehicle']['chassis_number']}}" placeholder="Enter Chassis Number" readonly>
                        </div>
                    </div>


                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="rider_id">Rider ID</label>-->
                    <!--        <input type="text" class="form-control bg-white" name="rider_id" id="rider_id"  placeholder="Enter Rider ID">-->
                    <!--    </div>-->
                    <!--</div>-->
                    <input type="hidden" class="form-control bg-white" name="id" value="{{ $data['id']}}">
                    <input type="hidden" class="form-control bg-white" name="rider_id" id="rider_id" value="{{ $data['rider']['id']}}">
                    <input type="hidden" class="form-control bg-white" name="rider_mobile_no" value="{{ $data['rider']['mobile_no']}}">
                    <input type="hidden" class="form-control bg-white" name="zone_id" value="{{ $data['vehicleRequest']['zone_id']}}">
                    <input type="hidden" class="form-control bg-white" name="city_id" value="{{ $data['vehicleRequest']['city_id']}}">


                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="rider_name">Rider Name</label>
                            <input type="text" class="form-control bg-white" name="rider_name" id="rider_name" value="{{ $data['rider']['name']}}" placeholder="Enter Rider Name" readonly>
                        </div>
                    </div>


                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="client_business_name">Client Business Name</label>
                            <input type="text" class="form-control bg-white" name="client_business_name" id="client_business_name" value="{{ $data['rider']['customerLogin']['customer_relation']['name']}}" placeholder="Enter Client Business Name" readonly>
                        </div>
                    </div>


                    <!--  <div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="contact_person_name">Contact Person Name</label>-->
                    <!--        <input type="text" class="form-control bg-white" name="contact_person_name" id="contact_person_name"  placeholder="Enter Contact Person Name">-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="contact_no">Contact No</label>
                            <input type="text" class="form-control bg-white phone-input" name="contact_no" id="contact_no" value="{{ $data['rider']['customerLogin']['customer_relation']['phone'] }}" placeholder="Enter Contact No" readonly>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="contact_email">Contact Email</label>
                            <input type="email" class="form-control bg-white" name="contact_email" id="contact_email" value="{{ $data['rider']['customerLogin']['customer_relation']['email'] }}" placeholder="Enter Contact Email" readonly>
                        </div>
                    </div>


                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="description">Description</label>
                            <textarea class="form-control bg-white" name="description" id="description" rows="6" placeholder="Enter Description"></textarea>
                        </div>
                    </div>




                </div>
                <?php $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg'); ?>

                <!--    <div class="row" id="uploadContainer">-->
                <!-- First Upload Block -->
                <!--        <div class="col-md-6 mb-3 upload-block">-->
                <!--            <label class="mb-2">Accident Photos / Videos <span class="text-danger">*</span>-->
                <!--                (Note: Must cover all sides of vehicle photo)-->
                <!--            </label>-->
                <!--            <div class="attachment-preview text-center border rounded p-2" style="height: 200px; position: relative;">-->
                <!-- Image Preview -->
                <!--                <img class="preview-image d-none" style="max-height: 180px; max-width: 100%;" />-->
                <!-- PDF Preview -->
                <!--                <iframe class="preview-pdf d-none" style="width: 100%; height: 180px;" frameborder="0"></iframe>-->
                <!-- Video Preview -->
                <!--                <video class="preview-video d-none" style="max-height: 180px; max-width: 100%;" controls></video>-->
                <!-- Default Image -->
                <!--                <div class="placeholder-text">-->
                <!--                    <img src="<?= $defaultImage ?>" class="img-fluid" style="max-height: 180px;">-->
                <!--                </div>-->
                <!--            </div>-->
                <!--            <input type="file" name="files[]" accept="image/*,application/pdf,video/*" class="form-control mt-2 file-input">-->
                <!--            <div class="d-flex gap-2 mt-2">-->
                <!--                <button type="button" class="btn btn-primary btn-add">Add</button>-->
                <!--                <button type="button" class="btn btn-danger btn-remove d-none">Remove</button>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--</div>-->

                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="confirmCheckbox" name="terms_condition" required>
                            <label for="confirmCheckbox">
                                I confirm the details provided are correct to the best of my knowledge.
                            </label>
                        </div>
                    </div>

                </div>


                <!-- Button -->
                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-danger btn-back me-2 ">Reset</button>
                        <button type="submit" class="btn btn-primary btn-submit ">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    </div>


</div>

@section('script_js')

<script>

    const maxUploads = 15;
    const uploadContainer = document.getElementById('uploadContainer');
    const defaultImageSrc = "<?= $defaultImage ?>";

    // Function to refresh Add/Remove button visibility
    function refreshButtons() {
        const blocks = uploadContainer.querySelectorAll('.upload-block');
        blocks.forEach((block, index) => {
            const addBtn = block.querySelector('.btn-add');
            const removeBtn = block.querySelector('.btn-remove');

            // Only last block shows Add button
            addBtn.classList.toggle('d-none', index !== blocks.length - 1);

            // First block hides Remove, others show Remove
            removeBtn.classList.toggle('d-none', index === 0);
        });
    }

    // Function to handle file preview
    function handleFilePreview(input) {
        const block = input.closest('.upload-block');
        const previewImage = block.querySelector('.preview-image');
        const previewPDF = block.querySelector('.preview-pdf');
        const previewVideo = block.querySelector('.preview-video');
        const placeholder = block.querySelector('.placeholder-text');
        const file = input.files[0];

        if (!file) return;

        const fileURL = URL.createObjectURL(file);

        // Reset previews
        previewImage.classList.add('d-none');
        previewPDF.classList.add('d-none');
        previewVideo.classList.add('d-none');
        placeholder.style.display = 'block';

        if (file.type === 'application/pdf') {
            previewPDF.src = fileURL;
            previewPDF.classList.remove('d-none');
            placeholder.style.display = 'none';
        } else if (file.type.startsWith('image/')) {
            previewImage.src = fileURL;
            previewImage.classList.remove('d-none');
            placeholder.style.display = 'none';
        } else if (file.type.startsWith('video/')) {
            previewVideo.src = fileURL;
            previewVideo.classList.remove('d-none');
            placeholder.style.display = 'none';
        }
    }

    // Event delegation for Add/Remove buttons
    uploadContainer.addEventListener('click', function(e) {
        const target = e.target;

        // Add new upload block
        if (target.classList.contains('btn-add')) {
            const totalBlocks = uploadContainer.querySelectorAll('.upload-block').length;
            if (totalBlocks >= maxUploads) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached',
                    text: 'You can upload a maximum of 15 files only.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const firstBlock = uploadContainer.querySelector('.upload-block');
            const clone = firstBlock.cloneNode(true);

            // Reset cloned block content
            clone.querySelector('.preview-image').classList.add('d-none');
            clone.querySelector('.preview-pdf').classList.add('d-none');
            clone.querySelector('.preview-video').classList.add('d-none');
            const placeholder = clone.querySelector('.placeholder-text');
            placeholder.style.display = 'block';
            placeholder.querySelector('img').src = defaultImageSrc;
            clone.querySelector('.file-input').value = '';

            uploadContainer.appendChild(clone);
            refreshButtons();
        }

        // Remove an upload block
        if (target.classList.contains('btn-remove')) {
            target.closest('.upload-block').remove();
            refreshButtons();
        }
    });

    // Listen for file change for previews
    uploadContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('file-input')) {
            handleFilePreview(e.target);
        }
    });

    // Initialize button states
    refreshButtons();
</script>


<script>
    $(document).ready(function() {
        $('#AdminrecoveryRequestForm').on('submit', function(e) {
            e.preventDefault();

            // Show loading state
            $('.btn-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
            
            var get_recovery_reason = $("#reason_for_recovery");
            var get_recovery_reason_val = get_recovery_reason.val();
            var reason_for_recovery_txt = 'N/A';
            
            if (get_recovery_reason_val !== "") {
                reason_for_recovery_txt = get_recovery_reason.find("option:selected").text();
            }
            // Create FormData object to handle file uploads
            let formData = new FormData(this);
            formData.append('reason_for_recovery_txt', reason_for_recovery_txt);
            // Append all file inputs
            $('.file-input').each(function(index) {
                if (this.files[0]) {
                    formData.append('files[]', this.files[0]);
                }
            });
            $.ajax({
                url: "{{ route('b2b.admin.deployed_asset.store_recovery_request') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            window.location.href = "{{ route('b2b.admin.deployed_asset.list') }}";
                        });
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr) {
                    $('.btn-submit').prop('disabled', false).html('Submit Recovery Form');

                    if (xhr.status === 422) {
                        // Validation errors
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';

                        for (let field in errors) {
                            errorMessage += errors[field][0] + '\n';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: errorMessage,
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                }
            });
        });

    });
</script>

<!--phone number-->
<script>
    function sanitizeAndValidatePhone(input) {
        // Ensure the input starts with '+91'
        if (!input.value.startsWith('+91')) {
            input.value = '+91' + input.value.replace(/^\+?91/, '');
        }

        // Allow only digits after '+91'
        input.value = '+91' + input.value.substring(3).replace(/[^\d]/g, '');

        // Limit the total length to 13 characters (including '+91')
        if (input.value.length > 13) {
            input.value = input.value.substring(0, 13);
        }
    }

    // Apply to all inputs with class "phone-input"
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".phone-input").forEach(function(input) {
            input.addEventListener("input", function() {
                sanitizeAndValidatePhone(this);
            });
        });
    });
</script>

@endsection
</x-app-layout>