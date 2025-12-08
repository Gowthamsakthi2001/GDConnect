<x-app-layout>

<style>
 .upload-guidelines-card {
        background: #fff;
        border: none;
    }

    .upload-guidelines-title {
        color: #b30000;
        font-weight: 600;
    }

    /* Right-side content box border only */
    .right-guidelines-box {
        border: 1px solid #b30000;
        border-radius: 8px;
        padding: 15px 20px;
        background: #fff4f4;
    }

    /* Video box shadow */
    .video-shadow-box {
        border-radius: 10px;
        background: #ffffff;
        padding: 10px;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
    }

    .upload-guidelines-text {
        color: #b30000;
        margin: 0;
        padding-left: 20px;
    }
</style>

    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-4 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.inventory.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Inventory Bulk Upload
                              </div>
                        </div>
                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                <a href="javascript:void(0);" download  class="btn btn-dark btn-md"><i class="bi bi-download"></i> Demo Excel</a>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
            <div class="card my-3">
                <div class="card-header pb-0 border-bottom-0">
                    <h5 class="text-muted"><i class="bi bi-file-earmark-spreadsheet"></i> Excel Export</h5>
                    <p class="text-muted">Upload your Excel file for bulk data addition. The format will be validated during preview.</p>
                </div>
                <div class="card-body pt-3">
                    <form id="ImportAssetMasterInventoryVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="asset_vehicle_excel_file">Select Excel File</label>
                                    <input type="file" class="form-control bg-white" name="asset_vehicle_excel_file" id="asset_vehicle_excel_file"
                                           accept=".xls,.xlsx"
                                           placeholder="Select" required>
                                </div>
                            </div>

                            <div>
                                <button type="submit" id="AssetVehicleInventorysubmitBtn" class="btn btn-success w-100 btn-lg"><i class="bi bi-upload me-2"></i> Upload File</button>
                            </div>
                      </div>
                    </form>
                </div>
            </div>
            
            <div class="card upload-guidelines-card">
                <div class="card-header pb-0 border-bottom-0">
                    <h6 class="upload-guidelines-title">
                        <i class="bi bi-exclamation-circle"></i> Upload Guidelines
                    </h6>
                </div>
                <div class="card-body pt-3">
                    <div class="row align-items-start">
                       <div class="col-md-6 mt-3 mt-md-0">
                            <div class="right-guidelines-box py-3 pb-4">
                                <ul class="upload-guidelines-text py-3 pb-4">
                                    <li class="mb-2"><small>Supported formats: Excel (.xlsx, .xls) and CSV files</small></li>
                                    <li class="mb-2"><small>Maximum file size: 10MB</small></li>
                                    <li class="mb-2"><small>First row should contain column headers</small></li>
                                    <li class="mb-2"><small>Ensure data consistency before uploading</small></li>
                                    <li class="mb-2"><small>Files will be validated during the preview stage</small></li>
                                </ul>
                            </div>
                        </div>
                       <div class="col-md-6">
                            <div class="video-shadow-box">
                                <video 
                                    id="videoPreview"
                                    width="100%"
                                    autoplay
                                    muted
                                    loop
                                    controls     <!-- FULL VIEW OPTION ENABLED -->
                                    playsinline
                                    class="rounded"
                                >
                                    <source src="{{ asset('public/uploads/bulk_upload/inventoty_bulk_upload_video.mp4') }}" type="video/mp4">
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
        <!-- End Page Header -->

        
    </div>
    
   
        
      
@section('script_js')
<script>

  $("#ImportAssetMasterInventoryVehicleForm").submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");

    var $submitBtn = $("#AssetVehicleInventorysubmitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Uploading...");

    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.inventory.bulk_upload_data') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $submitBtn.prop("disabled", false).html(originalText);
            // SUCCESS
            if (response.status === true) {
                let updated = [...new Set(response.updated_chassis || [])];
                let failed  = [...new Set(response.failed_chassis || [])];
            
                let htmlMessage = `<b>${response.message}</b>`;
            
                if (updated.length > 0) {
                        htmlMessage += `<br><br><b>Updated Chassis:</b><br>`;
                        htmlMessage += `<span style="color: green;">${updated.join("<br>")}</span>`;
                }
            
                if (failed.length > 0) {
                    htmlMessage += `<br><br><b>Failed Chassis:</b><br>`;
                    htmlMessage += `<span style="color: red;">${failed.join("<br>")}</span>`;
                }
            
                Swal.fire({
                    icon: 'success',
                    title: 'Upload Successful',
                    html: htmlMessage,
                    confirmButtonText: 'OK',
                    heightAuto: false
                });
            }



            // ERRORS → DOWNLOAD CSV
            else if (response.status === false && response.download_url) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Checked with Errors',
                    text: 'Some items need correction. Downloading error file...',
                    timer: 2800,
                    showConfirmButton: false,
                    heightAuto: false
                });

                // Auto download
                window.location.href = response.download_url;
            }

            else {
                Swal.fire({
                    icon: 'error',
                    title: 'Something Went Wrong',
                    text: 'Please try again.',
                    confirmButtonText: 'OK',
                    heightAuto: false
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

</script>
@endsection
</x-app-layout>
