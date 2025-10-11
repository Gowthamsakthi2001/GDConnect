<x-app-layout>

<style>


</style>

    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-4 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Bulk Upload
                              </div>
                        </div>
                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                <a href="{{asset('public/EV/Import_Asset_Master_Vehicles.xlsx')}}" download  class="btn btn-dark btn-md"><i class="bi bi-download"></i> Demo Excel</a>
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
                    <form id="ImportAssetMasterVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
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
                                    <label class="input-label mb-2 ms-1" for="asset_vehicle_excel_file">Select Excel File</label>
                                    <input type="file" class="form-control bg-white" name="asset_vehicle_excel_file" id="asset_vehicle_excel_file"
                                           accept=".xls,.xlsx"
                                           placeholder="Select" required>
                                </div>
                            </div>

                            <div>
                                <button type="submit" id="AssetVehiclesubmitBtn" class="btn btn-success w-100 btn-lg"><i class="bi bi-upload me-2"></i> Upload File</button>
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

    $("#ImportAssetMasterVehicleForm").submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");

    var $submitBtn = $("#AssetVehiclesubmitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Uploading...");

    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.bulk_upload_form.import') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

            $submitBtn.prop("disabled", false).html(originalText);

            if (response.success) {
                // Swal.fire({
                //     icon: 'success',
                //     title: 'Updated!',
                //     text: response.message,
                //     timer: 1500,
                //     showConfirmButton: false
                // }).then(() => {
                //     window.location.href="{{route('admin.asset_management.asset_master.list')}}";
                // });
                
            //  let chassisList = response.updated_chassis_numbers.join(", ");
            // Swal.fire({
            //     icon: 'success',
            //     title: 'Bulk Upload Completed!',
            //     html: `<b>Total Updated:</b> ${response.updated_count}<br><b>Chassis Numbers:</b><br>${chassisList}`,
            //     confirmButtonText: 'OK'
            // }).then(() => {
            //     window.location.href = "{{ route('admin.asset_management.asset_master.list') }}";
            // });
            
            // if (response.updated_count > 0) {
            //     let chassisList = response.updated_chassis_numbers.join(", ");
        
            //     Swal.fire({
            //         icon: 'success',
            //         title: 'Bulk Upload Completed!',
            //         html: `<b>Total Updated:</b> ${response.updated_count}<br><b>Chassis Numbers:</b><br>${chassisList}`,
            //         confirmButtonText: 'OK'
            //     }).then(() => {
            //         window.location.href = "{{ route('admin.asset_management.asset_master.list') }}";
            //     });
            // } else {
            //     Swal.fire({
            //         icon: 'info',
            //         title: 'No Records Updated',
            //         text: 'The file was uploaded successfully, but no new updates were found. Please verify your data.',
            //         confirmButtonText: 'OK'
            //     });
            // }
    
        let message = `<b>Total Updated:</b> ${response.updated_count}<br><br>`;
    
        if (response.updated_chassis_numbers.length) {
            message += `<b>Chassis Numbers:</b><br>${response.updated_chassis_numbers.join(", ")}<br><br>`;
        }
    
        if (response.error_rows && response.error_rows.length > 0) {
            message += `<b>Rows Skipped (Invalid Values or Missing Values):</b><br>`;
            response.error_rows.forEach(err => {
                message += `Row ${err.row} (Chassis: ${err.chassis_number}) - <b>${err.fields.join(', ')}</b><br>`;
            });
        }

    Swal.fire({
        icon: 'info',
        title: 'Upload Summary',
        html: message,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'swal-wide'
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
