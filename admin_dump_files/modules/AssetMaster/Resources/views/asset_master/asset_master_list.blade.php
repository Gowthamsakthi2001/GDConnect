<x-app-layout>

<style>
    .form-check-input:checked {
        background-color: #0f62fe !important;
        border-color: #0f62fe !important;
    }
    table thead th{
        background: white !important;
        color: #4b5563 !important;
    }
     .custom-dropdown-toggle::after {
        display: none !important;
      }
      
    textarea {
        text-align: left !important;
        direction: ltr !important;
    }
    
        .datatable-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .loading-spinner {
        width: 3rem;
        height: 3rem;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #0f62fe;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .table-container {
        position: relative;
        min-height: 200px;
    }
    
    /* Style DataTables Prev/Next buttons */
.dataTables_wrapper .dataTables_paginate .paginate_button.previous,
.dataTables_wrapper .dataTables_paginate .paginate_button.next {
    background-color: #0d6efd; /* Bootstrap primary color */
    color: white !important;
    border-radius: 6px;
    padding: 6px 12px;
    border: none;
}

/* Hover effect */
.dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover,
.dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
    background-color: #0b5ed7; /* Darker primary */
    color: white !important;
}

/* Disabled state */
.dataTables_wrapper .dataTables_paginate .paginate_button.previous.disabled,
.dataTables_wrapper .dataTables_paginate .paginate_button.next.disabled {
    background-color: #ccc;
    color: #666 !important;
    cursor: not-allowed;
}

</style>

    
    <div class="main-content">
            <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-2 d-flex align-items-center">
                        <div class="card-title h5 custom-dark m-0 d-flex align-items-center flex-wrap"> 
                            Asset Master 
                         
                            <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);" id="Asset_Filter_Count">{{ $totalRecords ?? 0 }}</span>
                        </div>
                    </div>

                        <div class="col-md-10 d-flex gap-2 align-items-center justify-content-end"> 
                            <div class="text-center d-flex gap-2">
                                 <a href="{{route('admin.asset_management.asset_master.inventory.list')}}" class="m-2 bg-white p-2 px-3 border-gray text-dark">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M18 11.25V2.25H6V11.25H2.25V21.75H21.75V11.25H18ZM6.75 3H17.25V11.25H6.75V3ZM21 21H3V12H21V21Z" fill="#4B5563"/>
                                    <path d="M9 6.75H15V7.5H9V6.75ZM9 8.25H15V9H9V8.25ZM14.25 16.5H9.75V15.75H9V17.25H15V15.75H14.25V16.5Z" fill="#4B5563"/>
                                    </svg>
                                     
                                      Inventory</a>
                                <a href="{{route('admin.asset_management.asset_master.logs_history')}}" class="m-2 bg-white p-2 px-3 border-gray text-dark"><i class="bi bi-clock-history fs-17 me-1"></i> Logs & History</a>
                                <a href="{{route('admin.asset_management.asset_master.bulk_upload_form')}}" class="m-2 bg-white p-2 px-3 border-gray text-dark"><i class="bi bi-upload fs-17 me-1"></i> Bulk Upload</a>
                                <a href="{{route('admin.asset_management.asset_master.add_vehicle')}}" class="m-2 btn btn-success p-2 px-3"><i class="bi bi-plus fs-17 me-1 fw-bold"></i> Add Vehicle</a>
                            </div>
                        </div>

                    </div>
                    <div class="row g-3">
                        <div class="col-12 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="BulkApproveorReject()"><i class="bi bi-check2-circle fs-17 me-1"></i>  Bulk Approve</div>
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="SelectExportFields()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <!-- End Page Header -->

        <div class="table-responsive table-container">
               <div id="loadingOverlay" class="datatable-loading-overlay">
        <div class="loading-spinner"></div>
    </div>
                    <table id="AssetMasterTable_List" class="table text-center" style="width: 100%;">
                        
                       
        
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">QC ID</th>
                              <th scope="col" class="custom-dark">Chessis No</th>
                              <th scope="col" class="custom-dark">Vehicle Model</th>
                              <th scope="col" class="custom-dark">Vehicle Type</th>
                              <th scope="col" class="custom-dark">Battery No</th>
                              <th scope="col" class="custom-dark">Telematics No</th>
                              <th scope="col" class="custom-dark">Last QC</th>
                              <th scope="col" class="custom-dark">Current Status</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                          
                        <tbody class="bg-white border border-white">
                    
                                 
                            @if(isset($lists))
                               @foreach($lists as $key => $val)
                   
                                    <tr>
                                       <td>
                                           <div class="form-check">
                                              <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="{{$val->id}}">
                                            </div>
                                       </td>
                                       <td>{{$val->qc_id}}</td>
                                       <td>{{$val->quality_check->chassis_number ?? 'N/A'}}</td>
                                       <td>{{$val->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A'}}</td>
                                       <td>{{$val->quality_check->vehicle_type_relation->name ?? 'N/A'}}</td>
                                       <td>{{$val->quality_check->battery_number ?? 'N/A'}}</td>
                                       
                                       <td>
                                          {{$val->quality_check->telematics_number ?? 'N/A'}}
                                       </td>
                                       
                                       <td>
                                           <?php
                                            //  dd($val->quality_check);
                                           ?>
                                           
                                          <div>{{ \Carbon\Carbon::parse($val->quality_check->updated_at)->format('d M Y') }},</div>
                                          <div><small>{{ \Carbon\Carbon::parse($val->quality_check->updated_at)->format('h:i:s A') }}</small></div>
                                       </td>
                                       <td>
                                           @if($val->is_status == "pending")
                                             <i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Pending Asset
                                             @elseif($val->is_status == "uploaded")
                                              <i class="bi bi-circle-fill" style="color:#1661c7;"></i> Asset Uploaded
                                            @elseif($val->is_status == "accepted")
                                              <i class="bi bi-circle-fill" style="color:#72cf72;"></i> Asset Accepted
                                            @elseif($val->is_status == "rejected")
                                              <i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Asset Rejected
                                           @endif
                                       </td>
                                       
                                      <td>
                                              <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                  <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <?php
                                                      $id_encode = encrypt($val->id);
                                                    ?>
                                                    <li>
                                                    @if($val->is_status == "rejected")
                                                       <a href="{{route('admin.asset_management.asset_master.reupload_vehicle_data',['id'=>$id_encode])}}" class="dropdown-item d-flex align-items-center justify-content-center">
                                                          <i class="bi bi-eye me-2 fs-5"></i> Preview
                                                        </a>
                                                    @else
                 
                                                        <a href="{{route('admin.asset_management.asset_master.view_asset_master',['id'=>$id_encode])}}" class="dropdown-item d-flex align-items-center justify-content-center">
                                                          <i class="bi bi-eye me-2 fs-5"></i> Preview
                                                        </a>
                                                    @endif
                                                    </li>
                                              @if($val->is_status != 'accepted')
                                               <li>
                                                <a href="javascript:void(0);" 
                                                   class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord('{{$val->id}}')">
                                                  <i class="bi bi-trash me-2"></i> Delete
                                                </a>
                                              </li>
                                              @endif
    
                                                </ul>
                                              </div>
                                          
                                        </td>

                                   </tr>
                               @endforeach
                            @endif
                        </tbody>
                        </table>
                </div>
    </div>
    
    
    
      <div class="modal fade" id="export_select_fields_modal" tabindex="-1" aria-labelledby="export_select_fields_modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-between">
                  <div>
                    <h1 class="h3 fs-5 text-center custom-dark" id="export_select_fields_modalLabel">Select Fields</h1>
                  </div>
                  <div>
                      <button type="button" class="btn text-white" style="background:#26c360;" onclick="ExportAssetMasterData()">Download</button>
                  </div>
                </div>
                <div class="modal-body p-md-3">
                  <div class="row px-4">
                      <div class="col-md-3 col-12 mb-3">
                          <div class="d-flex justify-content-between align-items-center">
                            <label class="form-check-label mb-0 text-dark fw-bold h6" for="field1">Select All</label>
                            <div class="form-check form-switch m-0">
                              <input class="form-check-input get-export-label" type="checkbox" id="field1" value="">
                            </div>
                          </div>
                        </div>
                  </div>
                  <div class="row p-4">
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field2">Chassis Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field2" value="chassis_number">
                        </div>
                      </div>
                    </div>
                
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field3">Vehicle Category</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field3" value="vehicle_category">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field4">Vehicle Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field4" value="vehicle_type">
                        </div>
                      </div>
                    </div>
                                        
                                        
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field6">Model</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field6" value="model">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field5">Make</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field5" value="make">
                        </div>
                      </div>
                    </div>
                    

                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field8">Variant</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field8" value="variant">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field9">Color</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field9" value="color">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field10">Engine Number/ <br>Motor Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field10" value="motor_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field11">Vehicle ID</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field11" value="vehicle_id">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field12">Tax Invoice Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field12" value="tax_invoice_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field13">Tax Invoice Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field13" value="tax_invoice_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field14">Invoice Value/<br>Purchase Price</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field14" value="tax_invoice_value">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3" >
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field15">Tax Invoice Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field15" value="tax_invoice_attachment">
                        </div>
                      </div>
                    </div>
                    
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field16">Location</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field16" value="location">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">City Code</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field16" value="city_code">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">GD Hub ID Allocated</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field16" value="gd_hub_id_allowcated">
                        </div>
                      </div>
                    </div>
                    
                   <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field16">GD Hub ID Existing</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field16" value="gd_hub_id_existing">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field17">Financing Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field17" value="financing_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field18">Asset Ownership</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field18" value="asset_ownership">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Master Lease Agreement</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field19" value="master_lease_agreement">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field20">Lease Start Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field20" value="lease_start_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field21">Lease End Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field21" value="lease_end_date">
                        </div>
                      </div>
                    </div>
                    

                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field23">EMI/Lease Amount</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field23" value="emi_lease_amount">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field24">Hypothecation</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field24" value="hypothecation">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field25">Hypothecated To</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field25" value="hypothecation_to">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Hypothecation Document</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field19" value="hypothecation_document">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field26">Insurer Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field26" value="insurer_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field27">Insurance Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field27" value="insurance_type">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field28">Insurance Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field28" value="insurance_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field29">Insurance Start Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field29" value="insurance_start_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field30">Insurance Expiry Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field30" value="insurance_expiry_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field31">Insurance Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field31" value="insurance_attachment">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field32">Registration Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field32" value="registration_type">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field33">Registration Status</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field33" value="registration_status">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Temproary Registration<br> Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field34" value="temproary_reg_number">
                        </div>
                      </div>
                    </div>
                    
                    
                  <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Temproary Registration<br> Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field34" value="temproary_reg_date">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Temproary Registration<br> Expiry Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field34" value="temproary_reg_expiry_date">
                        </div>
                      </div>
                    </div>
                    
                                        <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">Temporary Registration Certificate Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field19" value="temporary_registration_certificate_attachment">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field34">Permanent Registration<br> Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field34" value="permanent_reg_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field35">Permanent Registration<br> Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field35" value="permanent_reg_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field36">Registration Certificate <br>Expiry Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field36" value="reg_certificate_expiry_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field19">HSRP Copy Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field19" value="hsrp_copy_attachment">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3" >
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field37">Registration Certificate Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field37" value="reg_certificate_attachment">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field38">Fitness Certificate Expiry <br>Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field38" value="fc_expiry_date">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3" >
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field39">Fitness Certificate <br>Attachment</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field39" value="fc_attachment">
                        </div>
                      </div>
                    </div>
                    
                    
                  <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Servicing Dates</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="servicing_dates">
                        </div>
                      </div>
                    </div>
                    
                      <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Road Tax Applicable</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="road_tax_applicable">
                        </div>
                      </div>
                    </div>
                     
                     
                 <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Road Tax Amount</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="road_tax_amount">
                        </div>
                      </div>
                    </div>
                    
                        <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Road Tax Renewal <br> Frequency</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="road_tax_renewal_frequency">
                        </div>
                      </div>
                    </div>
                    
                 <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Road Tax Next Renewal <br> Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="road_tax_next_renewal_date">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_type">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3">-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field41">Battery Variant Name</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field41" value="battery_variant_name">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field42">Battery Serial Number <br>- Original</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field42" value="battery_serial_no">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Serial Number <br>- Replacement 1</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_serial_number_replacement_1">
                        </div>
                      </div>
                    </div>
                    
                       <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Serial Number <br>- Replacement 2</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_serial_number_replacement_2">
                        </div>
                      </div>
                    </div>
                    
                   <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Serial Number <br>- Replacement 3</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_serial_number_replacement_3">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Serial Number <br>- Replacement 4</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_serial_number_replacement_4">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field40">Battery Serial Number <br>- Replacement 5</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field40" value="battery_serial_number_replacement_5">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field43">Charger Variant Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field43" value="charger_variant_name">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Original</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_no">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Replacement 1</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_number_replacement_1">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Replacement 2</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_number_replacement_2">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Replacement 3</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_number_replacement_3">
                        </div>
                      </div>
                    </div>
                    
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Replacement 4</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_number_replacement_4">
                        </div>
                      </div>
                    </div>
                    
                   <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field44">Charger Serial Number -<br> Replacement 5</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field44" value="charger_serial_number_replacement_5">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field45">Telematics Variant Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field45" value="telematics_variant_name">
                        </div>
                      </div>
                    </div>
                    
                   <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field45">Telematics OEM</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field45" value="telematics_oem">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number - Original</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_no">
                        </div>
                      </div>
                    </div>
                    
                  <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics IMEI Number</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_imei_number">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number <br>- Replacement 1</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_number_replacement_1">
                        </div>
                      </div>
                    </div>
                    
                     <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number <br>- Replacement 2</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_number_replacement_2">
                        </div>
                      </div>
                    </div>
                    
                   <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number <br>- Replacement 3</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_number_replacement_3">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number <br>- Replacement 4</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_number_replacement_4">
                        </div>
                      </div>
                    </div>
                    
                  <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field46">Telematics Serial Number <br>- Replacement 5</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field46" value="telematics_serial_number_replacement_5">
                        </div>
                      </div>
                    </div>
                    
                    
                       
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field7">Client Name</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field7" value="client">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field22">Vehicle Delivery Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field22" value="vehicle_delivery_date">
                        </div>
                      </div>
                    </div>
                    
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field47">Vehicle Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field47" value="vehicle_status">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              
              </div>
            </form>
          </div>
        </div>
        
     <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Asset Master</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetMasterFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Asset Status</h6></div>
               </div>
               <div class="card-body">
                   <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_1" value="all" {{$status == "all" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_1">
                        All
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input"  type="radio" name="assetType" id="asset_type_2"  value="pending" {{$status == "pending" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_2">
                        Pending Asset
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_3" value="uploaded" {{$status == "uploaded" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_3">
                       Asset Uploaded
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_4" value="accepted" {{$status == "accepted" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_4">
                       Asset Accepted
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input" type="radio" name="assetType" id="asset_type_5" value="rejected" {{$status == "rejected" ? "checked" : ""}}>
                      <label class="form-check-label" for="asset_type_5">
                       Asset Rejected
                      </label>
                    </div>
                    
               </div>
           </div>
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select City</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">City</label>
                        <select name="city_id" id="city_id" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($locations))
                            @foreach($locations as $l)
                            <option value="{{$l->id}}" {{ $city == $l->id ? 'selected' : '' }}>{{$l->name." - ".$l->city_code}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
            </div>
            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Time Line</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1" value="today" {{$timeline == "today" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2" value="this_week" {{$timeline == "this_week" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3" value="this_month" {{$timeline == "this_month" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4" value="this_year" {{$timeline == "this_year" ? 'checked' : ''}}>
                      <label class="form-check-label" for="timeLine4">
                       This Year
                      </label>
                    </div>
               </div>
            </div>
            
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Date Between</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="FromDate">From Date</label>
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{$from_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" value="{{$to_date}}" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetMasterFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetMasterFilter()">Apply</button>
            </div>
            
          </div>
        </div>
        
        <div class="modal fade" id="BulkApproveorRejectmodal" tabindex="-1" aria-labelledby="BulkApproveorRejectmodalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header border-0 p-3 d-flex justify-content-center align-items-center">
                <h1 class="fs-5" id="BulkApproveorRejectmodalLabel">
                    Do you Really want to Bulk Update?
                </h1>
              </div>
              <div class="modal-body d-flex justify-content-center align-items-center gap-2 py-4">
                <button type="button" class="btn btn-danger px-5"
                    onclick="VehicleAcceptOrRejectStatus('{{ route('admin.asset_management.asset_master.bulk.vehicle_status_update') }}','You want to Reject these Asset Master Vehicles', 'rejected')">
                    Reject
                </button>
            
                <button type="button" class="btn btn-success px-5"
                    onclick="VehicleAcceptOrRejectStatus('{{ route('admin.asset_management.asset_master.bulk.vehicle_status_update') }}','You want to Accept these Asset Master Vehicles', 'accepted')">
                    Approve
                </button>
              </div>
            </div>
          </div>
        </div>
    

@section('script_js')


<script>
// Global variable for DataTable instance
// Global variables
let assetMasterDataTable = null;
let currentPage = 1;
let totalPages = 1;




function updatePaginationControls(pagination) {
    // Update page info
    $('#pageInfo').text(`Page ${pagination.current_page} of ${pagination.last_page}`);
    
    // Enable/disable previous button
    $('#prevPageBtn').prop('disabled', pagination.current_page === 1);
    
    // Enable/disable next button
    $('#nextPageBtn').prop('disabled', pagination.current_page === pagination.last_page);
}

function showLoadingState() {
    $('#loading-animation').show();
    $('#AssetMasterTable_List').hide();
}

function hideLoadingState() {
    $('#loading-animation').hide();
    $('#AssetMasterTable_List').show();
}

function populateTableBody(lists) {
    let tableBody = $('#asset-master-table-body');
    tableBody.empty();
    
    if (lists && lists.length > 0) {
        lists.forEach(function(val) {
            const id_encode = btoa(val.id);
            const previewUrl = val.is_status === "rejected" ? 
                "{{ route('admin.asset_management.asset_master.reupload_vehicle_data', ['id' => '']) }}" + id_encode :
                "{{ route('admin.asset_management.asset_master.view_asset_master', ['id' => '']) }}" + id_encode;
            
            const statusBadge = getStatusBadge(val.is_status);
            const lastQCDate = val.quality_check?.updated_at ? 
                new Date(val.quality_check.updated_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
            const lastQCTime = val.quality_check?.updated_at ? 
                new Date(val.quality_check.updated_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }) : '';
            
            const deleteButton = val.is_status !== 'accepted' ? `
                <li>
                    <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center justify-content-center" onclick="DeleteRecord('${val.id}')">
                        <i class="bi bi-trash me-2"></i> Delete
                    </a>
                </li>
            ` : '';
            
            tableBody.append(`
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="${val.id}">
                        </div>
                    </td>
                    <td>${val.qc_id || 'N/A'}</td>
                    <td>${val.quality_check?.chassis_number || 'N/A'}</td>
                    <td>${val.quality_check?.vehicle_model_relation?.vehicle_model || 'N/A'}</td>
                    <td>${val.quality_check?.vehicle_type_relation?.name || 'N/A'}</td>
                    <td>${val.quality_check?.battery_number || 'N/A'}</td>
                    <td>${val.quality_check?.telematics_number || 'N/A'}</td>
                    <td>
                        <div>${lastQCDate}</div>
                        <div><small>${lastQCTime}</small></div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="${previewUrl}" class="dropdown-item d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye me-2 fs-5"></i> Preview
                                    </a>
                                </li>
                                ${deleteButton}
                            </ul>
                        </div>
                    </td>
                </tr>
            `);
        });
    } else {
        tableBody.append('<tr><td colspan="10" class="text-center">No data found</td></tr>');
    }
}


$(document).ready(function () {
    $('#loadingOverlay').show();
    var table = $('#AssetMasterTable_List').DataTable({
        pageLength: 15,
        lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
        pagingType: "simple",
        dom: '<"top"lf>rt<"bottom"ip>',
        processing: true,
        serverSide: true,
        responsive: false,
        scrollX: true,
        ajax: {
            url: "{{ route('admin.asset_management.asset_master.list') }}",
            type: 'GET',
            data: function (d) {
                d.status = $('input[name="assetType"]:checked').val();
                d.timeline = $('input[name="STtimeLine"]:checked').val();
                d.from_date = $('#FromDate').val();
                d.to_date = $('#ToDate').val();
                d.city = $('#city_id').val();
            },
            beforeSend: function() {
                // Show loading overlay when AJAX starts
                $('#loadingOverlay').show();
            },
             complete: function() {
                // Hide loading overlay when AJAX completes
                $('#loadingOverlay').hide();
            },
             error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                $('#loadingOverlay').hide();
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    toastr.error(xhr.responseJSON.error);
                } else {
                    toastr.error('Failed to load data. Please try again.');
                }
            }
        },
        columns: [
            { data: 'checkbox', orderable: false },
            { data: 'qc_id' },
            { data: 'chassis_number' },
            { data: 'vehicle_model' },
            { data: 'vehicle_type' },
            { data: 'battery_number' },
            { data: 'telematics_number' },
            { data: 'last_qc' },
            { data: 'status' },
            { data: 'action', orderable: false }
        ],
        initComplete: function () {
            // Improved search with validation
            $('#loadingOverlay').hide();
            let searchDelay;
            let lastNotification;
            let lastSearchTerm = '';

            $('#AssetMasterTable_List_filter input')
                .off('keyup') // remove default DataTables handler
                .on('keyup', function () {
                    const searchTerm = this.value.trim();

                    clearTimeout(searchDelay);
                    if (lastNotification) {
                        toastr.clear(lastNotification);
                    }

                    if (searchTerm === lastSearchTerm) {
                        return;
                    }

                    if (searchTerm.length > 0 && searchTerm.length < 4) {
                        searchDelay = setTimeout(() => {
                            lastNotification = toastr.info(
                                "Please enter at least 4 characters for better results",
                                { timeOut: 2000 }
                            );
                        }, 500);
                        return;
                    }

                    searchDelay = setTimeout(() => {
                        lastSearchTerm = searchTerm;
                        table.search(searchTerm).draw();
                    }, 400);
                });
        },
        drawCallback: function(settings) {
            // Access the response JSON
            var response = settings.json;
            if (response) {
                // $('#totalRecordsSpan').text(response.recordsTotal);
                $('#Asset_Filter_Count').text(response.recordsFiltered);
            }
        }
    });
});



function getStatusBadge(status) {
    switch(status) {
        case 'pending': return '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Pending Asset';
        case 'uploaded': return '<i class="bi bi-circle-fill" style="color:#1661c7;"></i> Asset Uploaded';
        case 'accepted': return '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Asset Accepted';
        case 'rejected': return '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Asset Rejected';
        default: return 'N/A';
    }
}





function applyAssetMasterFilter() {
    // Get filter values using correct names
    const status = $('input[name="assetType"]:checked').val(); // Changed from 'status' to 'assetType'
    const timeline = $('input[name="STtimeLine"]:checked').val();
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();
    const city = $('#city_id').val(); // Changed from location_id to city_id

    // Get the DataTable instance
    var table = $('#AssetMasterTable_List').DataTable();
    
    // Reload with new parameters
    table.ajax.reload();
    
    // Close the offcanvas
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

function clearAssetMasterFilter() {
    // Reset all filter inputs
    $('input[name="assetType"][value="all"]').prop('checked', true);
    $('input[name="STtimeLine"]').prop('checked', false);
    $('#FromDate').val('');
    $('#ToDate').val('');
    $('#city_id').val('').trigger('change');
    
    // Reload DataTable with cleared filters
    $('#AssetMasterTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}


  $(document).ready(function () {
    $('#CSelectAllBtn').on('change', function () {
      $('.sr_checkbox').prop('checked', this.checked);
    });

    $('.sr_checkbox').on('change', function () {
      if (!this.checked) {
        $('#CSelectAllBtn').prop('checked', false);
      } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
        $('#CSelectAllBtn').prop('checked', true);
      }
    });
  });


function SelectExportFields() {
    $('#export_select_fields_modal').modal('show');
}

function ExportAssetMasterData() {
    const selectedStatus = document.querySelector('input[name="assetType"]:checked');
    const status = selectedStatus ? selectedStatus.value : 'all';
    const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
    const timeline = selectedTimeline ? selectedTimeline.value : '';
    const from_date = document.getElementById('FromDate').value;
    const to_date = document.getElementById('ToDate').value;
    const city = "{{ $city ?? '' }}";

    let req_ids = [];
    $('input[name="is_select[]"]:checked').each(function () {
        req_ids.push($(this).val());
    });

    let get_export_labels = [];
    $('.get-export-label:checked').each(function () {
        get_export_labels.push($(this).val());
    });

    if (get_export_labels.length === 0) {
        toastr.error("Please select at least one label Name.");
        return;
    }

    var form = $('<form>', {
        method: 'POST',
        action: "{{ route('admin.asset_management.asset_master.export.vehicle_detail') }}"
    });

    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: '{{ csrf_token() }}'
    }));

    req_ids.forEach(function (id) {
        form.append($('<input>', {
            type: 'hidden',
            name: 'get_ids[]',
            value: id
        }));
    });

    get_export_labels.forEach(function (label) {
        form.append($('<input>', {
            type: 'hidden',
            name: 'get_export_labels[]',
            value: label
        }));
    });

    form.append($('<input>', { type: 'hidden', name: 'status', value: status }));
    form.append($('<input>', { type: 'hidden', name: 'timeline', value: timeline }));
    form.append($('<input>', { type: 'hidden', name: 'from_date', value: from_date }));
    form.append($('<input>', { type: 'hidden', name: 'to_date', value: to_date }));
    form.append($('<input>', { type: 'hidden', name: 'city', value: city }));
    
    form.appendTo('body').submit();
}

function BulkApproveorReject() {
    let req_ids = [];
    
    $('input[name="is_select[]"]:checked').each(function() {
        req_ids.push($(this).val());
    });
    
    if(req_ids.length === 0) {
        toastr.error("Please select at least one Uploaded Asset Master Vehicle.");
        return;
    }
        
    $('#BulkApproveorRejectmodal').modal('show');
}

function VehicleAcceptOrRejectStatus(route, message, status, title = "Are you sure?") {
    let req_ids = [];
    
    $('input[name="is_select[]"]:checked').each(function() {
        req_ids.push($(this).val());
    });
    
    if(req_ids.length === 0) {
        toastr.error("Asset Master field is required. Please select at least one checkbox.");
        return;
    }
    
    $("#BulkApproveorRejectmodal").modal('hide');
    var redirect = "{{route('admin.asset_management.asset_master.list')}}";
    
    if (status == 'accepted') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $(".page-loader-wrapper").css("display","block");
                
                $.ajax({
                    url: route,
                    type: "POST",
                    data: {
                        get_ids: req_ids,
                        status: status,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $(".page-loader-wrapper").css("display","none");
                        if (response.success) {
                            var chassis_numbers = response.chassis_numbers;
                    
                            if (chassis_numbers.length > 0) {
                                let list = '<ol style="text-align: left; padding-left: 20px;">';
                                chassis_numbers.forEach(num => {
                                    list += `<li>${num}</li>`;
                                });
                                list += '</ol>';
                                Swal.fire({
                                    title: 'Asset Master Bulk Update Successful!',
                                    html: `
                                        <p><strong>Total Updated Chassis Numbers:</strong> ${chassis_numbers.length}</p>
                                        ${list}
                                    `,
                                    icon: 'success',
                                    showCloseButton: true,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    loadAssetMasterData(); // Refresh data via AJAX instead of page reload
                                });
                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'warning',
                                    title: response.message,
                                    showConfirmButton: false,
                                    showCloseButton: true,
                                    timer: false,
                                });
                            }
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: response.message,
                                showConfirmButton: false,
                                showCloseButton: true,
                                timer: false,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire("Error!",
                            "The network connection has failed. Please try again later",
                            "error");
                    }
                });
            }
        });
    } else {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Enter remarks here...',
            inputAttributes: {
                rows: 4
            },
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: "No",
            confirmButtonText: "Yes",
            reverseButtons: true,
            preConfirm: (remarks) => {
                if (!remarks) {
                    Swal.showValidationMessage('Reject Reason is required');
                }
                return remarks;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var Remarks = result.value;
                $(".page-loader-wrapper").css("display","block");
                $.ajax({
                    url: route,
                    type: "POST",
                    data: {
                        get_ids: req_ids,
                        status: status,
                        remarks: Remarks,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $(".page-loader-wrapper").css("display","none");
                        if (response.success) {
                            var chassis_numbers = response.chassis_numbers;
                            if (chassis_numbers.length > 0) {
                                let list = '<ol style="text-align: left; padding-left: 20px;">';
                                chassis_numbers.forEach(num => {
                                    list += `<li>${num}</li>`;
                                });
                                list += '</ol>';
                            
                                Swal.fire({
                                    title: 'Bulk Asset Master Vehicles Rejected Successfully!',
                                    html: `
                                        <p><strong>Total Rejected Chassis Numbers:</strong> ${chassis_numbers.length}</p>
                                        ${list}
                                    `,
                                    icon: 'success',
                                    showCloseButton: true,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    loadAssetMasterData(); // Refresh data via AJAX instead of page reload
                                });
                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'warning',
                                    title: 'Warning! ' + response.message,
                                    showConfirmButton: false,
                                    showCloseButton: true,
                                    timer: false,
                                });
                            }
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: response.message,
                                showConfirmButton: false,
                                showCloseButton: true,
                                timer: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error! The network connection has failed. Please try again later.',
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: false
                        });
                    }
                });
            }
        });
    }
}

function RightSideFilerOpen() {
    const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
    bsOffcanvas.show();
    
    document.getElementById('offcanvasRightHR01').addEventListener('shown.bs.offcanvas', function () {
        const selectFields = document.querySelectorAll('.custom-select2-field');
        if (selectFields.length > 0) {
            $(selectFields).select2({
                width: '100%',
                dropdownParent: $('#offcanvasRightHR01')
            });
        }
    }, { once: true });
}

function DeleteRecord(id, redirect = window.location.href) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want delete this asset vehicle",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Enter remarks here...',
        inputAttributes: {
            rows: 4
        },
        showCancelButton: true,
        cancelButtonColor: '#6c757d',
        confirmButtonColor: '#FC6A57',
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        reverseButtons: true,
        preConfirm: (remarks) => {
            if (!remarks || !remarks.trim()) {
                Swal.showValidationMessage('Remarks are required');
            }
            return remarks.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const Remarks = result.value;
            $.ajax({
                url: "{{ route('admin.asset_management.asset_master.destroy') }}",
                type: "POST",
                data: {
                    id: id,
                    remarks: Remarks,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Deleted! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 2000
                        }).then(() => {
                            loadAssetMasterData(); // Refresh data via AJAX
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Warning! ' + response.message,
                            showConfirmButton: false,
                            showCloseButton: true,
                            timer: 3000
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error! The network connection has failed. Please try again later.',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 3000
                    });
                }
            });
        }
    });
}


      document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('field1');
        const checkboxes = document.querySelectorAll('.get-export-label');
    
        selectAll.addEventListener('change', function () {
          checkboxes.forEach(checkbox => {
            if (checkbox !== selectAll) {
              checkbox.checked = selectAll.checked;
            }
          });
        });
    
        // Optional: Update "Select All" if any individual checkbox is unchecked
        checkboxes.forEach(checkbox => {
          if (checkbox !== selectAll) {
            checkbox.addEventListener('change', function () {
              const allChecked = Array.from(checkboxes)
                .filter(cb => cb !== selectAll)
                .every(cb => cb.checked);
              selectAll.checked = allChecked;
            });
          }
        });
      });
</script>
@endsection

</x-app-layout>
