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
/*.form-control.is-invalid{*/
/*        border-color: none !important;*/
    /*padding-right: 0px;*/
    /* background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e); */
    /*background-repeat: no-repeat;*/
    /*background-position: right calc(.375em + .1875rem) center;*/
    /*background-size: calc(.75em + .375rem) calc(.75em + .375rem);*/
/*}*/

.is-invalid {
    /*border-color: #dc3545 !important;*/
    /*box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);*/
}


.select2-error-border {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

</style>

    
    <div class="main-content">

            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-4 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0">
                                   <!--<a href="" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>-->
                                   Vehicle Transfer
                              </div>
                        </div>
                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">
                            
                            <div class="text-center d-flex gap-2">
                                <a href="{{route('admin.asset_management.vehicle_transfer.log_and_history')}}" class="btn border-gray btn-md btn-padding-class"> Logs & History </a>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
            
            <div class="card my-3">
            

                <div class="card-header row pb-0 border-bottom-0 d-flex justify-content-between">
                    <div class="col-md-4">
                        <h6 class="text-muted"><i class="bi bi-arrow-left-right"></i> &nbsp;Transfer Details</h6>
                        <p class="text-muted">Fill in the transfer information</p>
                    </div>
                
                    <div class="col-md-8">
                        <div class="text-end">
                            <a href="#" class="btn btn-outline-danger btn-padding-class" id="btnShowTransfer">Return Transfer</a>
                        </div>
                        
                
                        <div id="transferDropdownGroup" class="d-none d-flex gap-2 align-items-start">
                                <select class="form-select custom-select2-field text-start" id="GetTransferID">
                                    <option value="">Select Transfer ID</option>
                                    @if(isset($transfer_ids))
                                       @foreach($transfer_ids as $val)
                                         <option value="{{$val->id}}">{{$val->id}}</option>
                                       @endforeach
                                    @endif
                                </select>
      
                            <a href="#" class="btn btn-outline-primary btn-padding-class"  onclick="PageRedirectToRT()">Apply</a>
                            <a href="#" class="btn btn-outline-danger btn-padding-class" id="btnCancelTransfer">Cancel</a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body pt-3">
                    <form id="VehicleTransferSubmitForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row" id="show_alert_section">

                        </div>
                      <div class="row">
                           <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="transferType">Transfer Type <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select custom-select2-field" id="transferType" name="transfer_type" >
                                         @if(isset($transfer_types))
                                           @foreach($transfer_types as $val)
                                             <option value="{{$val->id}}">{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message transfer_type_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="transferDate">Transfer Date <span class="text-danger fw-bold">*</span></label>
                                    <input type="date" id="transferDate" name="transfer_date" class="form-control" value="{{date('Y-m-d')}}" >
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3 CustomerTransfer_Type d-none">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="Customer_id">Customer ID <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select" id="Customer_id" name="customer_id" onchange="FetchCustomerDetail(this.value)">
                                        <option value="">Select</option>
                                        @if(isset($customers))
                                           @foreach($customers as $val)
                                             <option value="{{$val->id}}" data-customer_id="{{$val->trade_name}}" >{{$val->id}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message customer_id_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3 CustomerTransfer_Type d-none">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="CustomerName">Customer Name (Trade Name) <span class="text-danger fw-bold">*</span></label>

                                    <select class="form-select" id="CustomerName" name="customer_name" onchange="FetchCustomerDetail(this.value)">
                                        <option value="">Select</option>
                                         @if(isset($customers))
                                               @foreach($customers as $val)
                                                 <option value="{{$val->id}}" data-customer_id="{{$val->id}}" >{{$val->trade_name}}</option>
                                               @endforeach
                                         @endif
                                    </select>
                                </div>
                                <div class="custom-error-message customer_name_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="from_location">From Location (Source) <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select" id="from_location" name="from_location" >
                                        <option value="">Select</option>
                                        @if(isset($vehicle_transfer_status))
                                           @foreach($vehicle_transfer_status as $val)
                                             <option value="{{$val->id}}">{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message from_location_err"></div>
                            </div>
                            
                            <div class="col-md-6 col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="to_location">To Location (Destination) <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select" id="to_location" name="to_location" >
                                        <option value="">Select</option>
                                        @if(isset($vehicle_transfer_status))
                                           @foreach($vehicle_transfer_status as $val)
                                             <option value="{{$val->id}}">{{$val->name}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="custom-error-message to_location_err"></div>
                            </div>
                            
                         <div class="col-12 mb-3">
                            <div class="table-responsive">
                                <table class="table border border-1" id="InternalAndCustomerTableContainer">
                                    <thead class="bg-white">
                                        <tr>
                                            <th style="width:313px !important;">Chessis Number</th>
                                            <th style="width:313px !important;">Vehicle Type</th>
                                            <th style="width:313px !important;">Vehicle Model</th>
                                            <th style="width:60px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="InternalAndCustomerTypeTableBody">
                                        <tr>
                                            <td>
                                   
                                                <select class="form-select custom-select2-field" name="select_chessis_number[]" onchange="FetchChassisDetails(this)">
                                                    <option value="">Select</option>
                                                    
                                                     @if(isset($passed_chassis_nos))
                                                       @foreach($passed_chassis_nos as $val)
                                                         <option value="{{$val->id}}" data-chassis_number="{{$val->chassis_number}}">{{$val->chassis_number}}</option>
                                                       @endforeach
                                                    @endif
                                                    
                                                </select>
                                                
                                            </td>
                                            
                                            <td>
                                                <input type="text" class="form-control" name="vehicle_type[]" placeholder="Auto filled" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="vehicle_model[]" placeholder="Auto filled" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                   <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <button type="button" class="btn border-gray add-new-row" >Add a Line</button>
                                            </td>
                                            <td colspan="2" class="text-end">
                                                <button type="button" class="btn border-gray" onclick="VehicleTransferBulkUpload('InternalAndCustomerTableContainer')">Bulk Upload</button>
                                            </td>
                                        </tr>
                                    </tfoot>

                                </table>
                                
                                <table class="table border border-1 d-none" id="RiderTableContainer">
                                    <thead class="bg-white">
                                        <tr>
                                            <th style="width:188px !important;">Chessis Number</th>
                                            <th style="width:188px !important;">Vehicle Type</th>
                                            <th style="width:188px !important;">Vehicle Model</th>
                                            <th style="width:188px !important;">Rider ID</th>
                                            <th style="width:188px !important;">Rider Name</th>
                                            <th style="width:60px !important;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="RiderTypeTableBody">
                                        <tr>
                                            <td>
                                                <select class="form-select custom-select2-field"  name="select_chessis_number[]" onchange="FetchChassisDetails(this)">
                                                    <option value="">Select</option>
                                                    
                                                     @if(isset($passed_chassis_nos))
                                                       @foreach($passed_chassis_nos as $val)
                                                         <option value="{{$val->id}}" data-chassis_number="{{$val->chassis_number}}">{{$val->chassis_number}}</option>
                                                       @endforeach
                                                    @endif
                                                    
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="vehicle_type[]" placeholder="Auto filled" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"  name="vehicle_model[]" placeholder="Auto filled" readonly>
                                            </td>
                                           <td>
                                                @if(isset($deliverymans))
                                                    <select name="rider_id[]" class="form-control custom-select2-field" onchange="Rider_FetchDetails(this)">
                                                         <option value="">Select</option>
                                                        @foreach($deliverymans as $val)
                                                            <option value="{{ $val->id }}" data-driver_id="{{ $val->id}}">
                                                                {{ $val->emp_id }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>

                                            <td>
                                                <select class="form-select custom-select2-field" name="rider_name[]" onchange="Rider_FetchDetails(this)">
                                                    <option value="">Select</option>
                                                @if(isset($deliverymans))
                                                    @foreach($deliverymans as $val)
                                                            <option value="{{ $val->id }}" data-driver_id="{{ $val->id}}">
                                                                {{ $val->first_name . ' ' . $val->last_name }}
                                                            </option>
                                                    @endforeach
                                                @endif
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2">
                                                <button type="button" class="btn border-gray add-new-row">Add a Line</button>
                                            </td>
                                            <td colspan="4" class="text-end">
                                                <button type="button" class="btn border-gray" onclick="VehicleTransferBulkUpload('RiderTableContainer')">Bulk Upload</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>


                            
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="Remarks">Remarks (Optional)</label>
                                    <textarea class="form-control" name="remarks" id="Remarks" rows="5" placeholder="Enter Optional Remarks for this Transfer"></textarea>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success w-100 btn-lg" id="VehicleTransfersubmitBtn">Initiate Transfer</button>
                            </div>
                      </div>
                    </form>
                </div>
            </div>
         
        
    </div>
    
      <div class="modal fade" id="Bulk_upload_Vehicle_TransferModal" tabindex="-1" aria-labelledby="Bulk_upload_Vehicle_TransferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content"> <!-- Required wrapper -->
                <div class="modal-body p-md-3">
                    <div class="card my-3">
                        <div class="card-header pb-0 border-bottom-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel Export
                                </h5>
                                <p class="text-muted">
                                    Upload your Excel file for bulk data addition. The format will be validated during preview.
                                </p>
                            </div>
                            <div>
                                <a href="{{asset('public/EV/Vehicle_Transfer_Import.xlsx')}}" download class="btn btn-round me-1 btn-md px-4 btn-primary text-white">
                                        <i class="bi bi-download"></i> Import Excel
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <form action="javascript:void(0);" id="BulkUploadVehicleTransfer_Form" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="form-group">
                                            <label class="input-label mb-2 ms-1" for="excel_file">Select Excel File</label>
                                            <input type="file" class="form-control bg-white" name="excel_file" id="excel_file"
                                                accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                                placeholder="Select" required>
                                        </div>
                                    </div>
    
                                    <div class="col-12">
                                        <button type="submit" id="BulkUploadsubmitBtn" class="btn btn-success w-100 btn-lg">
                                            <i class="bi bi-upload me-2"></i> Upload File
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
    
                    <div class="card" style="background:#fefbea; border:1px solid #894414;">
                        <div class="card-header pb-0 border-bottom-0" style="background:#fefbea;">
                            <h6 style="color:#894414;">
                                <i class="bi bi-exclamation-circle"></i> Upload Guidelines
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <ul style="color:#894414;">
                                <li><small>Supported formats: Excel (.xlsx, .xls)</small></li>
                                <li><small>First row should contain column headers</small></li>
                                <li><small>First column must be Chassis Number</small></li>
                                <li><small>Rider ID column is optional</small></li>
                                <li><small>Chassis Number is mandatory for each record</small></li>
                                <li><small>Ensure data consistency before uploading</small></li>
                            </ul>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    </div>

        
      
@section('script_js')
<script>
    let chassisOptions = `{!! 
        collect($passed_chassis_nos ?? [])->map(function($val) {
            return '<option value="'.$val->id.'" data-chassis_number="'.$val->chassis_number.'">'.$val->chassis_number.'</option>';
        })->implode('')
    !!}`;
    
    let DriverIds = `{!! 
        collect($deliverymans ?? [])->map(function($val) {
            return '<option value="'.$val->id.'" data-driver_id="'.$val->id.'">'.$val->emp_id.'</option>';
        })->implode('')
    !!}`;
    
    let DriverNames = `{!! 
        collect($deliverymans ?? [])->map(function($val) {
            return '<option value="'.$val->id.'" data-driver_id="'.$val->id.'">'.$val->first_name.' '.$val->last_name.'</option>';
        })->implode('')
    !!}`;

$(document).ready(function(){
    $("#Customer_id").select2();
    $("#CustomerName").select2();
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
    // $("#Customer_Name").html('<option value="">Auto Filled</option>').val('').trigger('change');
    $("#CustomerName").val('').trigger('change');
    $("#from_location").val('').trigger('change');
    $("#to_location").val('').trigger('change');
    $("#Customer_id").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    // $("#Customer_Name").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#to_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
    $("#from_location").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
}


function FetchCustomerDetail(customerId) {

    if (customerId) {
        $('#CustomerName').val(customerId).select2();  
        $('#Customer_id').val(customerId).select2();  
    } else {
        $('#CustomerName').val('').select2();  
        $('#Customer_id').val('').select2();  
    }
}



function VehicleTransferBulkUpload(type){
    $('#BulkUploadVehicleTransfer_Form')[0].reset();
    $("#Bulk_upload_Vehicle_TransferModal").modal('show');
}
</script>

<script>

function RefereshSelect2Feidls() {
    $(".custom-select2-field").select2({
        width: '100%'
    });
    $('.custom-select2-field').each(function () {
        var value = $(this).val();
        var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

        if (!value || value === "") {
            $selectBox.addClass("select2-error-border");
        } else {
            $selectBox.removeClass("select2-error-border");
        }
    });

    $('input[name="vehicle_type[]"]').each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
        } else {
            $(this).removeClass("is-invalid");
        }
    });

    $('input[name="vehicle_model[]"]').each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
        } else {
            $(this).removeClass("is-invalid");
        }
    });
    
    var transferType = $("#transferType").val();
    
    if(transferType == 3){
        
        $('input[name="rider_id[]"]').each(function () {
            var value = $(this).val();
            var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');
    
            if (!value || value === "") {
                $selectBox.addClass("select2-error-border");
            } else {
                $selectBox.removeClass("select2-error-border");
            }
        });
        
        $('input[name="rider_name[]"]').each(function () {
            var value = $(this).val();
            var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');
    
            if (!value || value === "") {
                $selectBox.addClass("select2-error-border");
            } else {
                $selectBox.removeClass("select2-error-border");
            }
        });
    }
}


// function Rider_FetchDetails(currentSelect) {
//     let selectedValue = currentSelect.value;
    
//     let $selectBox = $(currentSelect).next('.select2-container').find('.select2-selection--single');
//     if (!selectedValue || selectedValue === "") {
//         $selectBox.addClass("select2-error-border");
//     } else {
//         $selectBox.removeClass("select2-error-border");
//     }
    
//     console.log("Selected:", selectedValue);
    
//     let parentRow = currentSelect.closest('tr');
//     const selectField = parentRow.querySelector('select[name="rider_name[]"]');

//     selectField.innerHTML = `<option value="">Select</option>`;
//     if(selectedValue == ""){
//         let $selectBox = $(selectField).next('.select2-container').find('.select2-selection--single');
//         $selectBox.addClass("select2-error-border");
//     }
//     if (!selectedValue) return;

//     let isDuplicate = false;

//     document.querySelectorAll('select[name="rider_id[]"]').forEach(select => {
//         if (select !== currentSelect && select.value === selectedValue) {
//             isDuplicate = true;
//         }
//     });

//     if (isDuplicate) {
//         toastr.error("This Deliveryman ID has already been chosen. Please choose another.");
//         currentSelect.value = '';
//         $(currentSelect).trigger('change');
//         return;
//     }
    

//     let $selectBox = $(selectField).next('.select2-container').find('.select2-selection--single');
//     if (selectedValue) {
//         selectField.value = selectedValue;
//         $selectBox.addClass("select2-error-border");
//     } else {
//         $selectBox.removeClass("select2-error-border");
//     }
// }

function Rider_FetchDetails(currentSelect) {
    
    let selectedValue = currentSelect.value;
    
    let parentRow = currentSelect.closest("tr");

    // Find both select boxes inside this row
    const selectId = parentRow.querySelector('select[name="rider_id[]"]');
    const selectName = parentRow.querySelector('select[name="rider_name[]"]');

    
    // Validation UI
    let $selectBox = $(currentSelect).next(".select2-container").find(".select2-selection--single");
    if (!selectedValue) {
        $selectBox.addClass("select2-error-border");
    } else {
        $selectBox.removeClass("select2-error-border");
    }
    
    
  // Reset both if cleared
    if (!selectedValue) {
        selectId.value = "";
        selectName.value = "";
        $(selectId).select2();
        $(selectName).select2();
        
        return;
    }

    // Prevent duplicate Rider selection across all rows
    let isDuplicate = false;
    document.querySelectorAll('select[name="rider_id[]"]').forEach(select => {
        if (select !== selectId && select.value === selectedValue && selectedValue !== "") {
            isDuplicate = true;
        }
    });
    document.querySelectorAll('select[name="rider_name[]"]').forEach(select => {
        if (select !== selectName && select.value === selectedValue && selectedValue !== "") {
            isDuplicate = true;
        }
    });

    if (isDuplicate) {
        toastr.error("This Rider has already been chosen. Please choose another.");
        currentSelect.value = "";
        $(currentSelect).trigger("change");
        return;
    }
    
    
  

    // --- Two-way Sync ---
    if (currentSelect === selectId) {
        // If Rider ID changed → update Rider Name
        selectName.value = selectedValue;
        $(selectName).select2(); 
    } else if (currentSelect === selectName) {
        // If Rider Name changed → update Rider ID
        selectId.value = selectedValue;
        $(selectId).select2(); 
    }


}



function FetchChassisDetails(currentSelect) {
    let selectedValue = currentSelect.value;
    console.log("Selected:", selectedValue);

    let parentRow = currentSelect.closest('tr');
    parentRow.querySelector('input[name="vehicle_type[]"]').value = '';
    parentRow.querySelector('input[name="vehicle_model[]"]').value = '';
    
    if (selectedValue == "") {
        $(parentRow.querySelector('input[name="vehicle_type[]"]')).addClass('is-invalid');
        $(parentRow.querySelector('input[name="vehicle_model[]"]')).addClass('is-invalid');
    }
            
    if (!selectedValue) return;

    let isDuplicate = false;

    document.querySelectorAll('select[name="select_chessis_number[]"]').forEach(select => {
        console.log("Comparing with:", select.value);
        if (select !== currentSelect && select.value === selectedValue) {
            isDuplicate = true;
        }
    });

    if (isDuplicate) {
        console.log("Duplicate found!");
        toastr.warning("This chassis number has already been chosen. Please choose another.");
        currentSelect.value = '';
        $(currentSelect).trigger('change');
    }else{

    
        $.ajax({
            url: "{{ route('admin.asset_management.vehicle_transfer.get_chassis_detail') }}",
            type: "GET",
            data: { vehicle_id: selectedValue },
            success: function(response) {
                if (response.success) {
                    let vehicleType = response.vehicle_type;
                    let vehicleModel = response.vehicle_model;
                    parentRow.querySelector('input[name="vehicle_type[]"]').value = vehicleType;
                    parentRow.querySelector('input[name="vehicle_model[]"]').value = vehicleModel;
                    if (vehicleType != "") {
                        $(parentRow.querySelector('input[name="vehicle_type[]"]')).removeClass('is-invalid');
                    }
                    if (vehicleModel != "") {
                        $(parentRow.querySelector('input[name="vehicle_model[]"]')).removeClass('is-invalid');
                    }

                } else {
                    toastr.warning(response.message || "No data found for this chassis.");
                }
            },
            error: function(xhr) {
                toastr.error("Something went wrong. Please try again.");
            }
        });
       
    }
}

  
$(document).ready(function () {
    function clearTableRows(tableBodySelector) {
        $(tableBodySelector).html(''); 
    }

    $('#transferType').on('change', function () {
        let selected = $(this).val();
        
      if (selected == 2 || selected == "3") {
            $(".CustomerTransfer_Type").addClass("d-block").removeClass("d-none");
        } else {
            $(".CustomerTransfer_Type").addClass("d-none").removeClass("d-block");
        }
        
        if(selected == "3"){
            $("#InternalAndCustomerTypeTableBody").html('');
        }else{
            $("#RiderTypeTableBody").html('');
        }

        if (selected == "1" || selected == "2") { // Customer or internal
            $('#InternalAndCustomerTableContainer').removeClass('d-none');
            $('#RiderTableContainer').addClass('d-none');
            clearTableRows('#RiderTypeTableBody');
            addCustomerRow(); 
        } else if (selected === "3") { // Rider
            $('#RiderTableContainer').removeClass('d-none');
            $('#InternalAndCustomerTableContainer').addClass('d-none');
            clearTableRows('#InternalAndCustomerTypeTableBody');
            addRiderRow(); 
        } else {
            $('#InternalAndCustomerTableContainer, #RiderTableContainer').addClass('d-none');
            clearTableRows('#InternalAndCustomerTypeTableBody');
            clearTableRows('#RiderTypeTableBody');
        }
    });

  function addCustomerRow() {
        $('#InternalAndCustomerTypeTableBody').append(`
            <tr>
                <td>
                    <select class="form-select custom-select2-field" name="select_chessis_number[]" onchange="FetchChassisDetails(this)">
                        <option value="">Select</option>
                        ${chassisOptions}
                    </select>
                </td>
                <td>
                  <input type="text" class="form-control" name="vehicle_type[]" placeholder="Auto Filled" readonly>
                </td>
                <td>
                  <input type="text" class="form-control" name="vehicle_model[]" placeholder="Auto Filled" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `);
         RefereshSelect2Feidls();
    }



    function addRiderRow() {
        $('#RiderTypeTableBody').append(`
            <tr>
                
                <td>
                    <select class="form-select custom-select2-field" name="select_chessis_number[]" onchange="FetchChassisDetails(this)">
                        <option value="">Select</option>
                        ${chassisOptions}
                    </select>
                <td>
                    <input type="text" class="form-control" name="vehicle_type[]" placeholder="Auto filled" readonly>
                </td>
                <td>
                   <input type="text" class="form-control" name="vehicle_model[]" placeholder="Auto filled" readonly>
                </td>
                <td>
                    <select class="form-select custom-select2-field" name="rider_id[]" onchange="Rider_FetchDetails(this)">
                        <option value="">Select</option>
                        ${DriverIds}
                    </select>
                    
                </td>
                <td>
                    <select class="form-select custom-select2-field" name="rider_name[]" onchange="Rider_FetchDetails(this)">
                        <option value="">Select</option>
                        ${DriverNames}
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `);
        RefereshSelect2Feidls();
    }

    $(document).on('click', '.add-new-row', function () {
        if ($('#InternalAndCustomerTableContainer').is(':visible')) {
            addCustomerRow();
        } else if ($('#RiderTableContainer').is(':visible')) {
            addRiderRow();
        }
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });
});
    
    
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

$("#VehicleTransferSubmitForm").submit(function(e) {
    e.preventDefault();
    var isValid = true;
    $("#show_alert_section").empty();
    
    var tr_type = $("#transferType").val();
    
    if(tr_type != "" && tr_type == "3"){
        var customer_id =  $("#Customer_id").val();
        var customer_name = $("#CustomerName").val();
        $("#Customer_id").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
        $("#CustomerName").next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
        
        if(customer_id == ""){
            toastr.error("Customer ID field is required");
            $("#Customer_id").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
            isValid = false;
        }
        
        if(customer_name == ""){
            toastr.error("Customer Name field is required");
            $("#CustomerName").next('.select2-container').find('.select2-selection--single').addClass("select2-error-border");
            isValid = false;
        }
    }
    

    
    var from_location = $("#from_location").val();
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

    $('select[name="select_chessis_number[]"]').each(function () {
        var value = $(this).val();
        if (value) {
            fetch_chassis_numbers.push(value);
        }
    });
    
    // console.log("Selected Chassis Numbers:", fetch_chassis_numbers);
    
    if (fetch_chassis_numbers.length === 0) {
        toastr.error("Please select at least one chassis number.");
        isValid = false;
    }




    var $submitBtn = $("#VehicleTransfersubmitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Submitting...");
    
    var get_chassisNumbers = [];
    
    $('select[name="select_chessis_number[]"]').each(function () {
        var selectedOption = $(this).find('option:selected');
        var selectedValue = selectedOption.val();
        
        if (!selectedValue) {
            $(this).addClass("is-invalid");
            toastr.error(`Please select a chassis number`);
            isValid = false;
        } else {
            $(this).removeClass("is-invalid");
            var chassisNumber = selectedOption.data('chassis_number');
            get_chassisNumbers.push(chassisNumber);
        }
    });

    console.log(get_chassisNumbers);
    console.log("hii check");
    $('input[name="vehicle_type[]"]').each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
            toastr.error(`Please select a chassis number`);
            isValid = false;
        } else {
            $(this).removeClass("is-invalid");
        }
    });

    $('input[name="vehicle_model[]"]').each(function () {
        if (!$(this).val()) {
            $(this).addClass("is-invalid");
            toastr.error(`Vehicle model is missing`);
            isValid = false;
        } else {
            $(this).removeClass("is-invalid");
        }
    });

    if (tr_type == "3") {
        $('input[name="rider_id[]"]').each(function () {
            var value = $(this).val();
            var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

            if (!value || value === "") {
                $selectBox.addClass("select2-error-border");
                toastr.error(`Please select a Rider ID`);
                isValid = false;
            } else {
                $selectBox.removeClass("select2-error-border");
            }
        });

        $('input[name="rider_name[]"]').each(function () {
            var value = $(this).val();
            var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

            if (!value || value === "") {
                $selectBox.addClass("select2-error-border");
                toastr.error(`Rider Name is missing`);
                isValid = false;
            } else {
                $selectBox.removeClass("select2-error-border");
            }
        });
    }
    

    if (!isValid) {
        $submitBtn.prop("disabled", false).html(originalText);
        return;
    }
    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");
    formData.append("get_chassis_numbers", get_chassisNumbers);
    
    if(isValid == true){
         $.ajax({
            url: "{{ route('admin.asset_management.vehicle_transfer.initiate_form') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
           success: function(response) {
            $submitBtn.prop("disabled", false).html(originalText);
            // console.log("Full Response:", response);
        
            if (response.success) {
                form.reset();
                ClearInputs();
                document.getElementById("excel_file").value = "";
        
                showToast('success', response.message);
        
                // Success message
                $("#show_alert_section").html(`
                    <div class="col-12">
                        <div class="alert alert-success text-white alert-dismissible fade show" role="alert" style="background:#17c653;">
                            <strong>Success!</strong> ${response.message}
                            <br>Transfer ID: <b>${response.transfer_id}</b>
                            <button type="button" class="btn-close" style="color:white !important;" data-bs-dismiss="alert" aria-label="Close" onclick="pageRefresh()"></button>
                        </div>
                    </div>
                `);
        
                // 🔥 If info_errors exist, show them also
                if (response.info_errors && Object.keys(response.info_errors).length > 0) {
                    let errorList = '';
                    $.each(response.info_errors, function(chassis, errorMsg) {
                        errorList += `<li>${errorMsg}</li>`;
                    });
        
                    $("#show_alert_section").append(`
                        <div class="col-12 mt-2">
                            <div class="alert alert-warning text-dark alert-dismissible fade show" role="alert">
                                <strong>Note:</strong> Some vehicles could not be processed:
                                <ul>${errorList}</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    `);
                }
        
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
                $("#VehicleTransferSubmitForm").find(".custom-error").removeClass("custom-error");
                $("#VehicleTransferSubmitForm").find(".custom-error-message").empty();
            
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
            
                    $.each(errors, function(field, messages) {
                        let fieldSelector = `[name="${field.replace(/\.(\d+)/g, '[$1]')}"]`;
                        let $input = $(`#VehicleTransferSubmitForm ${fieldSelector}`);
    
                        $input.addClass("custom-error");
    
                        const fieldErrorClass = `.${field.replace(/_/g, '-')}_err, .${field}_err`;
            
                        const $errorDiv = $(`#VehicleTransferSubmitForm ${fieldErrorClass}`);
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
     }else{
         toastr.error("Something went wrong. Please try again.");
     }
    
});

// $(document).on('change', 'select[name="select_chessis_number[]"]', function () {
//     $(this).next('.select2-container').find('.select2-selection--single').removeClass("select2-error-border");
// });

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

// $('#Customer_id').on('change', function () {
//     var value = $(this).val();
//     var $selectBox = $(this).next('.select2-container').find('.select2-selection--single');

//     if (!value) {
//         $selectBox.addClass("select2-error-border");
//     } else {
//         $selectBox.removeClass("select2-error-border");
//     }

//     var nameValue = $("#CustomerName").val();
//     var $nameInput = $("#CustomerName");

//     if ($nameInput.is('input')) {
//         if (!nameValue) {
//             $nameInput.addClass("is-invalid");
//         } else {
//             $nameInput.removeClass("is-invalid");
//         }
//     }

//     if ($nameInput.is('select')) {
//         var $nameSelectBox = $nameInput.next('.select2-container').find('.select2-selection--single');
//         if (!nameValue) {
//             $nameSelectBox.addClass("select2-error-border");
//         } else {
//             $nameSelectBox.removeClass("select2-error-border");
//         }
//     }
// });


$("#BulkUploadVehicleTransfer_Form").submit(function (e) {
    e.preventDefault();
    var transferType = $("#transferType").val();
    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");
    formData.append('transfer_type', transferType);

    var $submitBtn = $("#BulkUploadsubmitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Uploading...");

    $.ajax({
        url: "{{ route('admin.asset_management.vehicle_transfer.get_bulk_detail') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $submitBtn.prop("disabled", false).html(originalText);

            if (response.success && response.data && response.data.length > 0) {
                var tableBody = '';
                var containerId = '';

                // Choose target table and container
                if (response.transfer_type == "3") {
                    containerId = '#RiderTableContainer';
                    tableBody = '#RiderTypeTableBody';
                } else {
                    containerId = '#InternalAndCustomerTableContainer';
                    tableBody = '#InternalAndCustomerTypeTableBody';
                }

                // Show the relevant table
                $(containerId).removeClass('d-none');

                // Clear existing rows (optional)
                // $(tableBody).empty();
                    if (response.warnings_message && response.warnings_message != "") {
                    
                        showToast('warning', response.warnings_message); 

                    }else{
                   
                    }
                response.data.forEach(function (item) {
                    
                    // Prevent duplicate chassis number rows
                    var exists = $(tableBody + ' select[name="select_chessis_number[]"] option:selected')
                        .filter(function () {
                            return $(this).data('chassis_number') == item.chassis_number;
                        }).length;

                    if (exists) {
                        // toastr.warning('Duplicate chassis number: ' + item.chassis_number);
                         showToast('warning', 'The chassis number "' + item.chassis_number + '" already exists and was not added again.');
                        return; 
                    }
                  
                    

                    let row = `<tr>
                        <td>
                            <select class="form-select custom-select2-field" name="select_chessis_number[]" onchange="FetchChassisDetails(this)">
                                <option value="${item.vehicle_id}" selected data-chassis_number="${item.chassis_number}">${item.chassis_number}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="vehicle_type[]" value="${item.vehicle_type}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="vehicle_model[]" value="${item.vehicle_model}" readonly>
                        </td>`;

                    if (response.transfer_type == "3") {
                        row += `
                            <td>
                                <select class="form-select custom-select2-field" name="rider_id[]" onchange="Rider_FetchDetails(this)">
                                    <option value="${item.rider_id}" selected data-dm_name="${item.rider_name}">${item.emp_id}</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-select custom-select2-field" name="rider_name[]">
                                    <option value="${item.rider_name}" selected>${item.rider_name}</option>
                                </select>
                            </td>`;
                    }

                    row += `
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;

                    $(tableBody).append(row);
                });

                RefereshSelect2Feidls();
                $("#Bulk_upload_Vehicle_TransferModal").modal('hide');
                $('#BulkUploadVehicleTransfer_Form')[0].reset();
            } else {
                toastr.warning("No valid data found in uploaded sheet.");
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
            toastr.error("Please ensure your Excel follows the guidelines: the first column must be Chassis Number, Rider ID is optional, and Chassis Number is required for each row.");

            }
        }
    });
});

function pageRefresh(){
    window.location.reload();
}

document.getElementById('btnShowTransfer').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('transferDropdownGroup').classList.remove('d-none');
    this.classList.add('d-none');
});

document.getElementById('btnCancelTransfer').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('transferDropdownGroup').classList.add('d-none');
    document.getElementById('btnShowTransfer').classList.remove('d-none');
});

function PageRedirectToRT() {
    var transfer_id = $("#GetTransferID").val();
    var $selectBox = $("#GetTransferID").next('.select2-container').find('.select2-selection--single');
    if (!transfer_id) {
        $selectBox.addClass("select2-error-border");
        toastr.error("Transfer ID field is required. Please select a Transfer ID.");
        return;
    } else {
        $selectBox.removeClass("select2-error-border");
    }
    var url = "{{ route('admin.asset_management.vehicle_transfer.return_vehicle_view') }}";
    const redirect_url = new URL(url, window.location.origin);
    redirect_url.searchParams.set('transfer_id', transfer_id);
    window.location.href = redirect_url.toString();
}

</script>


@endsection
</x-app-layout>
