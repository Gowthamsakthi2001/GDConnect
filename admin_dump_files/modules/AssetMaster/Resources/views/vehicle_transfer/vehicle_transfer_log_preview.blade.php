<x-app-layout>

<style>
table thead th {
    background: white !important;
    color: #4b5563 !important;
    border: 1px solid #e4e5e7 !important;
}
table tbody td {
    border: 1px solid #e4e5e7 !important;
}
/* Custom red border for error */
.custom-error {
    border: 1px solid #dc3545 !important;
    background-color: #fff0f0;
}

.btn-padding-class{
    padding: 10px 20px !important;
}

/* Custom error message */
.custom-error-message {
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
    display: block;
}

.select2-error-border {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
.custom-return-label {
    display: inline-block;
    padding: 10px 20px;
    border: 2px solid transparent;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease-in-out;
    background-color: #f1f1f1;
    color: #333;
    margin-right: 10px;
}

.custom-return-label.full-return {
    border-color: #28a745;
}

.custom-return-label.partial-return {
    border-color: #ffc107;
}

.btn-check:checked + .custom-return-label.full-return {
    background-color: #28a745;
    color: #fff !important;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
}

.btn-check:checked + .custom-return-label.partial-return {
    background-color: #ffc107;
    color: #fff !important;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.4);
}

.custom-return-label:hover {
    opacity: 0.85;
}

</style>


    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-10 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0">
                                   {{$vehicle_transfer->transferType->name ?? ''}} in detail view
                              </div>
                        </div>
                        
                        <div class="col-2 text-end">
                            <a href="{{route('admin.asset_management.vehicle_transfer.log_and_history')}}" class="btn btn-dark btn-padding-class">Back</a>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <div class="card my-3">


                <div class="card-header row pb-0 border-bottom-0 d-flex justify-content-between">
                    <div class="col-md-8">
                        <h6 class="text-muted"><i class="bi bi-arrow-left-right"></i> &nbsp;Transfer Details</h6>
                        <p class="text-muted">transfer information</p>
                    </div>
                    <?php
                      $return_vehicle_count = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('return_status',0)->count();
                      $total_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->count();
                      $return_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)
                        ->where('initial_status',1)->where('return_status',1)->count();
                        $last_transfer_vehicle = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->orderBy('id','desc')->first();
       
        
                        $transfer_date = '';
                        if($total_vehicles == $return_vehicles){
                            $transfer_date = $last_transfer_vehicle->return_transfer_date;
                        }else{
                             $transfer_date = $vehicle_transfer->transfer_date;
                        }
                        
                        $from_selected = '';
                        if($total_vehicles == $return_vehicles){
                            $from_selected = $last_transfer_vehicle->to_location_destination;
                        }else{
                             $from_selected = $vehicle_transfer->from_location_source;
                        }
                        
                        $to_selected = '';
                        if($total_vehicles == $return_vehicles){
                            $to_selected = $last_transfer_vehicle->from_location_source;
                        }else{
                             $to_selected = $vehicle_transfer->to_location_destination;
                        }
                    ?>
  
                </div>
                
                <div class="card-body pt-3">
                    <form id="ReturnVehicleTransferSubmitFormView" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" id="show_alert_section">
                            <!--<div class="col-12">-->
                            <!--   <div class="alert alert-success text-white alert-dismissible fade show" role="alert" style="background:#17c653;">-->
                            <!--      <strong>Holy guacamole!</strong> You should check in on some of those fields below.-->
                            <!--      <button type="button" class="btn-close" style="color:white !important; font-weight:700;" data-bs-dismiss="alert" aria-label="Close"></button>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                      <div class="row">
                           <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <input type="hidden" id="ReturnTF_id" value="{{$vehicle_transfer->id}}">
                                    <label class="input-label mb-2 ms-1" for="transferType">Transfer Type </label>
                                    <select class="form-select custom-select2-field" id="transferType" name="transfer_type" disabled>

                                         @if(isset($transfer_types))
                                           @foreach($transfer_types as $val)
                                             <option value="{{$val->id}}" {{$vehicle_transfer->transfer_type == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message transfer_type_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="transferDate">Transfer Date </label>
                                    <input type="date" id="transferDate" name="transfer_date" class="form-control" value="{{$transfer_date}}" >
                                </div>
                            </div>
                            
                              <div class="col-md-6 col-12 mb-3 CustomerTransfer_Type {{$vehicle_transfer->transfer_type == 1 ? 'd-none' : 'd-block'}}">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="Customer_id">Customer ID </label>
                                   
                                    <input type="text" class="form-control" value="{{$vehicle_transfer->transfer_type != 1 ? $vehicle_transfer->customerMaster->id : 'N/A'}}" readonly>
                                </div>
                                <div class="custom-error-message customer_id_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3 CustomerTransfer_Type {{$vehicle_transfer->transfer_type == 1 ? 'd-none' : 'd-block'}}">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="Customer_Name">Customer Name (Trade Name)</label>
                                    <input type="hidden" id="CustomerName" name="customer_name">
                                    <input type="text" class="form-control" value="{{$vehicle_transfer->transfer_type != 1 ? $vehicle_transfer->customerMaster->trade_name : 'N/A'}}" readonly>
                                </div>
                                <div class="custom-error-message customer_name_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="from_location">From Location (Source)</label>
                                    <input type="hidden" id="FromLocation" value="{{$vehicle_transfer->to_location_destination}}">
                                    <select class="form-select" id="from_location" name="from_location" disabled>
                                        <option value="">Select</option>
                                        @if(isset($vehicle_transfer_status))
                                           @foreach($vehicle_transfer_status as $val)
                                             <option value="{{$val->id}}" {{$from_selected == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message from_location_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="to_location">To Location (Destination)</label>
                                    <select class="form-select" id="to_location" name="to_location" disabled>
                                        <option value="">Select</option>
                                        @if(isset($vehicle_transfer_status))
                                           @foreach($vehicle_transfer_status as $val)
                                             <option value="{{$val->id}}"  {{$to_selected == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message to_location_err"></div>
                            </div>
                            
                         <div class="col-12 mb-3">
                            <div class="table-responsive">
                                <?php
                                  $transferType = $vehicle_transfer->transfer_type;
                                //   $transfer_vehicles =$vehicle_transfer->transfer_details ?? [];
                                //   dd($transfer_vehicles);
                                ?>
                                @if($transferType == 1 || $transferType == 2)
                                <table class="table border border-1" id="InternalAndCustomerTableContainer">
                                    <thead class="bg-white">
                                        <tr>
                                             <th style="width:60px !important;">
                                               #
                                             </th>
                                            <th style="width:313px !important;">Chessis Number</th>
                                            <th style="width:313px !important;">Vehicle Type</th>
                                            <th style="width:313px !important;">Vehicle Model</th>
                                            <th style="width:313px !important;">Tranfer Status</th>
                                            <th style="width:313px !important;">Vehicle Status</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody id="InternalAndCustomerTypeTableBody">
                                        <tr id="InternalTableLoader">
                                            <td colspan="6" class="text-center p-4">
                                                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2 mb-0">Loading vehicles...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                   

                                </table>
                                @endif
                                
                                @if($transferType == 3)
                                    <table class="table border border-1" id="RiderTableContainer">
                                        <thead class="bg-white">
                                            <tr>
                                                <th style="width:60px !important;">
                                                    #
                                                 </th>
                                                <th style="width:188px !important;">Chessis Number</th>
                                                <th style="width:188px !important;">Vehicle Type</th>
                                                <th style="width:188px !important;">Vehicle Model</th>
                                                <th style="width:188px !important;">Rider ID</th>
                                                <th style="width:188px !important;">Rider Name</th>
                                                <th style="width:313px !important;">Tranfer Status</th>
                                                <th style="width:313px !important;">Vehicle Status</th>
                                            </tr>
                                        </thead>
                                         <tbody id="RiderTypeTableBody">
                                           <tr id="RiderTableLoader">
                                                <td colspan="8" class="text-center p-4">
                                                    <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-2 mb-0">Loading vehicles...</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                        
                                    </table>
                                @endif

                            </div>
                        </div>
                            
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="Remarks">Remarks (Optional)</label>
                                    <textarea class="form-control" name="return_remarks" id="Remarks" rows="5" placeholder="Enter Optional Remarks for this Transfer">{{$vehicle_transfer->remarks}}</textarea>
                                </div>
                            </div>
                            <?php 
                            // dd($return_vehicle_count); 
                            ?>
                        
                      </div>
                    </form>
                </div>
            </div>
         
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0 mb-0 bg-white">
                <div class="card-title h6 custom-dark mb-2">
                   {{ $vehicle_transfer->id ?? '' }} Logs Summary
                </div>
                <p class="text-start text-muted mb-0">Detailed overview of activity logs and history</p>
            </div>
        
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="row g-3">
                    <?php
                     $logs = \Modules\AssetMaster\Entities\VehicleTransferLog::where('transfer_id',$vehicle_transfer->id)->orderBy('id','desc')->get();
                    ?>
                    @if(isset($logs) && count($logs) > 0)
                        @foreach($logs as $log)
                        <div class="col-12">
                            <div class="p-3 rounded border" style="background: #eaeaea30;">
                                <p class="text-muted mb-3">
                                    {{$log->remarks ?? ''}}
                                </p>
            
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-fill me-1 text-info"></i>
                                        <span>Transferred By: <strong class="">{{$log->CreatedBy->name ?? ''}} ({{$log->CreatedBy->get_role->name ?? 'N/A'}})</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-event me-1 text-info me-2"></i>
                                        <span>{{!empty($log->transfer_date) ? date('d M Y') : ''}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                     <div class="col-12 text-center">No Logs & History</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    
     
@section('script_js')
@if($transferType == 1 || $transferType == 2)
<script>
  function loadLogInternalTransferTable(url) {
        // Show loader
        $("#InternalAndCustomerTypeTableBody").html($("#InternalTableLoader")); // keep loader
        $("#InternalTableLoader").show();
    
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (res) {
                $("#InternalTableLoader").hide();

                if (res.status) {
                    $("#InternalAndCustomerTypeTableBody").html(res.table_body);
                } else {
                    $("#InternalAndCustomerTypeTableBody").html(
                        '<tr><td colspan="6" class="text-center text-danger p-3">No data available</td></tr>'
                    );
                }
            },
            error: function () {
                $("#InternalTableLoader").hide();
                $("#InternalAndCustomerTypeTableBody").html(
                    '<tr><td colspan="6" class="text-center text-danger p-3">Something went wrong</td></tr>'
                );
            }
        });
    }


    $(document).ready(function () {
        var route = "{{ route('admin.asset_management.vehicle_transfer.getLogInterTransferTablelist', $vehicle_transfer->id) }}";
        loadLogInternalTransferTable(route); 
    });
</script>
@endif

@if($transferType == 3)
<script>
  function loadLogRiderTransferTable(url) {
        // Show loader
        $("#RiderTypeTableBody").html($("#RiderTableLoader")); // keep loader
        $("#RiderTableLoader").show();
    
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (res) {
                $("#RiderTableLoader").hide();

                if (res.status) {
                    $("#RiderTypeTableBody").html(res.table_body);
                } else {
                    $("#RiderTypeTableBody").html(
                        '<tr><td colspan="8" class="text-center text-danger p-3">No data available</td></tr>'
                    );
                }
            },
            error: function () {
                $("#RiderTableLoader").hide();
                $("#RiderTypeTableBody").html(
                    '<tr><td colspan="8" class="text-center text-danger p-3">Something went wrong</td></tr>'
                );
            }
        });
    }


    $(document).ready(function () {
        var route = "{{ route('admin.asset_management.vehicle_transfer.getLogRiderTransferTablelist', $vehicle_transfer->id) }}";
        loadLogRiderTransferTable(route); 
    });
</script>
@endif

<script>
 $(document).ready(function () {
    // Select All Button Logic
    $('#CSelectAllBtn').on('change', function () {
        $('.sr_checkbox').prop('checked', this.checked);
    });

    // Individual Checkbox Logic
    $('.sr_checkbox').on('change', function () {
        if (!this.checked) {
            $('#CSelectAllBtn').prop('checked', false);
        } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
            $('#CSelectAllBtn').prop('checked', true);
        }
    });

    // Return Type Change Logic
    $('input[name="return_type"]').on('change', function () {
        if ($(this).val() == 'partial') {
            $('.sr_checkbox').prop('checked', false);
            $('#CSelectAllBtn').prop('checked', false); // also uncheck "select all"
        }else{
            $('.sr_checkbox').prop('checked', true);
            $('#CSelectAllBtn').prop('checked', true); 
        }
    });
});


$(document).ready(function(){
    $("#Customer_id").select2();
    $("#Customer_Name").select2();
    $("#from_location").select2();
    $("#to_location").select2();
    OnLoadtableBodyRefresh();
});

function OnLoadtableBodyRefresh(){
   var selected = $("#transferType").val();
    if(selected == "3"){
        $("#InternalAndCustomerTypeTableBody").html('');
    }else{
        $("#RiderTypeTableBody").html('');
    }

}

function ClearInputs() {
    $("#Customer_id").val('');

    $("#InternalAndCustomerTypeTableBody").html('');
    $("#RiderTypeTableBody").html('');
    $("#Customer_id").val('').trigger('change');
    $("#Customer_Name").html('<option value="">Auto Filled</option>').val('').trigger('change');
    $("#CustomerName").val('').trigger('change');
    $("#from_location").val('').trigger('change');
    $("#to_location").val('').trigger('change');
    $("#Customer_id").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#Customer_Name").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#to_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#from_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
}

</script>

<script>


function showToast(type, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: title,
        showConfirmButton: false,
        showCloseButton: true,
        timer: false,
    });
}

$("#ReturnVehicleTransferSubmitFormView").submit(function(e) {
    e.preventDefault();
    
    var isValid = true;

    $("#show_alert_section").empty();
    

    var from_location = $("#FromLocation").val();
    var to_location = $("#to_location").val();
    
    // Remove previous error borders
    $("#from_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#to_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $('.from_location_err').text('');
    $('.to_location_err').text('');
    if(from_location == ""){
        toastr.error("From Location field is required");
        $("#from_location").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
        isValid = false;
    }
    
    if(to_location == ""){
        toastr.error("To Location field is required");
        $("#to_location").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
        isValid = false;
    }
    
    if ((from_location != "" && to_location != "") && (from_location == to_location)) {
        toastr.error("From Location and To Location cannot be the same");

        $("#from_location").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
        $("#to_location").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
        isValid = false;
    }
    
    var fetch_chassis_numbers = [];
    var get_chassisNumbers = [];
    var pending_chassisNumbers = [];
    
    $('input[name="is_select[]"]').each(function () {
        var isChecked = $(this).is(':checked');
        var value = $(this).val();
        var chassisNumber = $(this).data('chassis_number');
    
        if (isChecked) {
            fetch_chassis_numbers.push(value);
            get_chassisNumbers.push(chassisNumber);
        } else {
            pending_chassisNumbers.push(chassisNumber);
        }
    });

console.log("Checked Values:", fetch_chassis_numbers);
console.log("Checked Chassis Numbers:", get_chassisNumbers);
console.log("Unchecked Chassis:", pending_chassisNumbers);


    console.log(get_chassisNumbers);
    if (fetch_chassis_numbers.length == 0) {
        toastr.error("Please select at least one chassis number.");
        isValid = false;
    }
    var return_transfer_date = $("#transferDate").val();
    var return_remarks = $("#Remarks").val();
    var transfer_id = $("#ReturnTF_id").val();
    var form = $(this)[0];
    var formData = new FormData();
    formData.append("_token", "{{ csrf_token() }}");
    formData.append("transfer_id",transfer_id);
    formData.append("to_location",to_location);
    formData.append("detail_ids", fetch_chassis_numbers);
    formData.append("return_remarks", return_remarks);
    formData.append("return_transfer_date", return_transfer_date);
    formData.append("get_chassis_numbers", get_chassisNumbers);
    formData.append("pending_chassis_numbers", pending_chassisNumbers);

    var $submitBtn = $("#ReturnVehicleTransfersubmitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Submitting...");

    if (!isValid) {
        $submitBtn.prop("disabled", false).html(originalText);
        return;
    }
     if(isValid == true){
         $.ajax({
            url: "{{ route('admin.asset_management.vehicle_transfer.return_form') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
    
                $submitBtn.prop("disabled", false).html(originalText);
    
                if (response.success) {
                    showToast('success', response.message);
                    $("#show_alert_section").html(`
                        <div class="col-12">
                            <div class="alert alert-success text-white alert-dismissible fade show" role="alert" style="background:#17c653;">
                                <strong>Success!</strong> ${response.message}
                                <button type="button" class="btn-close" style="color:white !important;" data-bs-dismiss="alert" aria-label="Close" onclick="pageRefresh()"></button>
                            </div>
                        </div>
                    `);
     
                } else {
                    showToast('warning', response.message);
                    $("#show_alert_section").html(`
                        <div class="col-12">
                            <div class="alert alert-warning text-dark alert-dismissible fade show" role="alert">
                                <strong>Warning!</strong> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    `);
                    
                }
            },
            error: function(xhr) {
                $submitBtn.prop("disabled", false).html(originalText);
                $("#show_alert_section").empty();
                $("#ReturnVehicleTransferSubmitFormView").find(".custom-error").removeClass("custom-error");
                $("#ReturnVehicleTransferSubmitFormView").find(".custom-error-message").empty();
            
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
            
                    $.each(errors, function(field, messages) {
                        let fieldSelector = `[name="${field.replace(/\.(\d+)/g, '[$1]')}"]`;
                        let $input = $(`#ReturnVehicleTransferSubmitFormView ${fieldSelector}`);
    
                        $input.addClass("custom-error");
    
                        const fieldErrorClass = `.${field.replace(/_/g, '-')}_err, .${field}_err`;
            
                        const $errorDiv = $(`#ReturnVehicleTransferSubmitFormView ${fieldErrorClass}`);
                        if ($errorDiv.length) {
                            $errorDiv.html(`<span>${messages[0]}</span>`);
                        }
    
                        toastr.error(messages[0]);
                    });
                } else {
                    toastr.error("An unexpected error occurred. Please try again.");
                }
            }
    
    
        });
     }
    
});

$(document).on('change', 'select[name="select_chessis_number[]"]', function () {
    let $selectBox = $(this).next('.select2-container').find('.select2-selection--single');
    if (!$(this).val()) {
        $selectBox.addClass("select2-error-border");
    } else {
        $selectBox.removeClass("select2-error-border");
    }
});


$('#from_location').on('change', function () {
    var value = $(this).val();
    var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

    if (!value) {
        $selectBox.addClass("select2-error-border");
    } else {
        $selectBox.removeClass("select2-error-border");
    }
});
$('#to_location').on('change', function () {
    var value = $(this).val();
    var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

    if (!value) {
        $selectBox.addClass("select2-error-border");
    } else {
        $selectBox.removeClass("select2-error-border");
    }
});

$('#Customer_id').on('change', function () {
    var value = $(this).val();
    var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

    if (!value) {
        $selectBox.addClass("select2-error-border");
    } else {
        $selectBox.removeClass("select2-error-border");
    }

    var nameValue = $("#Customer_Name").val();
    var $nameInput = $("#Customer_Name");

    if ($nameInput.is('input')) {
        if (!nameValue) {
            $nameInput.addClass("is-invalid");
        } else {
            $nameInput.removeClass("is-invalid");
        }
    }

    if ($nameInput.is('select')) {
        var $nameSelectBox = $nameInput.next('.select2-container').find('.select2-selection--single');
        if (!nameValue) {
            $nameSelectBox.addClass("select2-error-border");
        } else {
            $nameSelectBox.removeClass("select2-error-border");
        }
    }
});

function pageRefresh(){
    window.location.reload();
}


</script>


@endsection
</x-app-layout>
