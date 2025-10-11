<x-app-layout>

<style>


</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-4 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.quality_check.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Bulk Upload
                              </div>
                        </div>
                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end d-none">
                            <div class="text-center d-flex gap-2">
                                <a href="{{route('admin.asset_management.quality_check.list')}}" class="btn btn-dark btn-md">Back </a>
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
                    <form id="QualityCheckBulkUploadForm" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                          <!--<div class="col-12 mb-3">-->
                          <!--      <div class="form-group">-->
                          <!--          <label class="input-label mb-2 ms-1" for="uploader_name">Uploader Name</label>-->
                          <!--          <input type="text" class="form-control bg-white" name="uploader_name" id="uploader_name" value="" placeholder="Enter Uploader Name">-->
    
                          <!--      </div>-->
                          <!--  </div>-->
                          <!--  <div class="col-12 mb-3">-->
                          <!--      <div class="form-group">-->
                          <!--          <label class="input-label mb-2 ms-1" for="location">Location</label>-->
                          <!--          <input type="text" class="form-control bg-white" name="location" id="location"  value="" placeholder="Enter Location">-->
                          <!--      </div>-->
                          <!--  </div>-->
                          
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="excel_file">Select Excel File</label>
                                    <input type="file" class="form-control bg-white" name="excel_file" id="excel_file"
                                           accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                           placeholder="Select">
                                </div>
                            </div>

                            <div>
                                    <button type="button" id="UploadFileBtn" class="btn btn-success w-100 btn-lg">
                                        <i class="bi bi-upload me-2"></i> Upload File
                                    </button>
                            </div>
                      </div>
                    </form>
                </div>
            </div>
            
            <div class="card" style="background:#fefbea; border:1px solid #894414;">
                <div class="card-header pb-0 border-bottom-0" style="background:#fefbea;">
                    <h6 style="color:#894414;"><i class="bi bi-exclamation-circle"></i> Upload Guidelines</h6>
                </div>
                <div class="card-body pt-3">
                    <ul style="color:#894414;">
                        <li><small>Supported formats: Excel (.xlsx, .xls) and CSV files</small></li>
                        <li><small>Maximum file size: 10MB</small></li>
                        <li><small>First row should contain column headers</small></li>
                        <li><small>Ensure data consistency before uploading</small></li>
                        <li><small>Files will be validated during the preview stage</small></li>
                    </ul>
                </div>
            </div>
        <!-- End Page Header -->

        
    </div>
    
   
        
      
@section('script_js')

<script>
    $('#UploadFileBtn').on('click', function (e) {
    e.preventDefault();

    var form = $('#QualityCheckBulkUploadForm')[0];
    var formData = new FormData(form);

    var $button = $(this);
    var originalText = $button.html();
    $button.prop('disabled', true).html('⏳ Uploading...');

    $.ajax({
        url: "{{ route('admin.asset_management.quality_check.bulk_upload_data') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $button.prop('disabled', false).html(originalText);

            if (response.success) {
                // if (response.inserted_count > 0) {
                //     let chassisList = response.chassis_numbers.join('<br>');
        
                //     Swal.fire({
                //         icon: 'success',
                //         title: `${response.inserted_count} Records Inserted Successfully`,
                //         html: `<strong>Chassis Numbers:</strong><br>${chassisList}`,
                //         confirmButtonText: 'OK',
                //         width: 600
                //     }).then(() => {
                //         location.href = "{{ route('admin.asset_management.quality_check.list') }}";
                //     });
                // } else {
                //     Swal.fire({
                //         icon: 'info',
                //         title: 'No Records Inserted',
                //         text: 'The file was uploaded successfully, but no new records were found to insert. Please check your Excel data.',
                //         confirmButtonText: 'OK'
                //     });
                // }
                
                    let htmlContent = '';

                // Inserted records
                if (response.inserted_count > 0) {
                    let chassisList = response.chassis_numbers.join('<br>');
                    htmlContent += `<b style="color:green">✅ ${response.inserted_count} Records Inserted:</b><br>${chassisList}<br><br>`;
                }
            
                // Skipped rows
                if (response.error_rows && response.error_rows.length > 0) {
                    htmlContent += `<b style="color:red">⚠️ Rows Skipped (Invalid/Incomplete Values):</b><br>`;
                    response.error_rows.forEach(err => {
                        const chassis = err.chassis_number || 'N/A';
                        htmlContent += `Row ${err.row} (Chassis: ${chassis}) - <b>${err.fields.join(', ')}</b><br>`;
                    });
                }
            
                // Show summary alert
                Swal.fire({
                    icon: response.inserted_count > 0 ? 'success' : 'info',
                    title: 'Upload Summary',
                    html: htmlContent,
                    width: 700,
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (response.inserted_count > 0) {
                        location.href = "{{ route('admin.asset_management.quality_check.list') }}";
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
        error: function (xhr) {
            $button.prop('disabled', false).html(originalText);

   if (xhr.status === 422) {
        let response = xhr.responseJSON;

        // Show generic message if `errors` is not available
        if (response.errors) {
            $.each(response.errors, function (key, value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: value[0],
                });
            });
        } else if (response.message) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Failed',
                text: response.message,
            });
        }
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong, please try again.',
        });
    }
        }
    });
});

</script>

@endsection
</x-app-layout>
