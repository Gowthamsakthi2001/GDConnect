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
                        <div class="col-md-4 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Logs & History
                                  <span id="LogHistory_Filter_Count" class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);">{{$total_count ?? 0}}</span>
                              </div>
                        </div>

                        <div class="col-md-8 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                               
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="SelectLogHistoryExportFields()"><i class="bi bi-download fs-17 me-1"></i> Export</div>
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
                    <table class="table custom-table text-center" style="width: 100%;" id="assetLogsTable">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="padding:0.7rem;" type="checkbox" value="" id="CSelectAllBtn">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Vehicle ID</th>
                              <th scope="col" class="custom-dark">Location</th>
                              <th scope="col" class="custom-dark">Chessis No</th>
                              <th scope="col" class="custom-dark">Telematics No</th>
                              <th scope="col" class="custom-dark">Vehicle Type</th>
                              <th scope="col" class="custom-dark">Vehicle Model</th>
                              <th scope="col" class="custom-dark">Last Update</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                          
                        <tbody class="bg-white border border-white">
                                   <!--@if(isset($asset_logs))-->
                               <!--@foreach($asset_logs as $key => $val)-->
                                   
                                   <tr>
                                    <!--   <td>-->
                                    <!--       <div class="form-check">-->
                                    <!--          <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="{{$val->assetVehicle->id}}">-->
                                    <!--        </div>-->
                                    <!--   </td>-->
                                    <!--   <td>{{$val->assetVehicle->id}}</td>-->
                                    <!--   <td>{{$val->assetVehicle->location_relation->name ?? 'N/A'}}</td>-->
                                    <!--   <td>{{$val->assetVehicle->chassis_number ?? 'N/A'}}</td>-->
                                    <!--   <td>-->
                                    <!--      {{$val->assetVehicle->telematics_serial_no ?? 'N/A'}}-->
                                    <!--   </td>-->
                                    <!--   <td>{{$val->assetVehicle->vehicle_type_relation->name ?? 'N/A'}}</td>-->
                                       <!--<td>{{$val->assetVehicle->vehicle_model_relation->vehicle_model ?? 'N/A'}}</td>-->
                                    <!--   <td>{{$val->assetVehicle->vehicle_model_relation->vehicle_model ?? 'N/A'}}</td>-->
                 
                                    <!-- <td>-->
                                    <!--    <div>{{ \Carbon\Carbon::parse($val->updated_at)->format('d M Y') }}</div>-->
                                    <!--    <div><small>{{ \Carbon\Carbon::parse($val->updated_at)->format('h:i:s A') }}</small></div>-->
                                    <!--</td>-->

                            <!--            <td>-->
                            <!--              <div class="dropdown">-->
                            <!--                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">-->
                            <!--                  <i class="bi bi-three-dots"></i>-->
                            <!--                </button>-->
                            <!--               <ul class="dropdown-menu dropdown-menu-end text-center p-1">-->
                            <!--                   <?php-->
                            <!--                     $log_id = encrypt($val->id);-->
                            <!--                     $asset_vehicle_id = encrypt($val->assetVehicle->id);-->
                            <!--                   ?>-->
                            <!--                  <li>-->
                            <!--                    <a href="{{route('admin.asset_management.asset_master.log_history.preview',['log_id'=>$log_id, 'asset_vehicle_id'=>$asset_vehicle_id])}}" class="dropdown-item d-flex align-items-center justify-content-center">-->
                            <!--                      <i class="bi bi-eye me-2 fs-5"></i> View-->
                            <!--                    </a>-->
                            <!--                  </li>-->
                            <!--                </ul>-->

                            <!--              </div>-->
                            <!--            </td>-->
                            <!--       </tr>-->
                            <!--   @endforeach-->
                            <!--@endif-->
                        </tbody>
                
                
                        </table>
                </div>
    </div>
    
    <div class="modal fade" id="export_label_log_history_modal" tabindex="-1" aria-labelledby="export_label_log_history_modalLabel" aria-hidden="true">
          <div class="modal-dialog modal-xl">
            <form>
              <div class="modal-content rounded-4">
                <div class="modal-header border-0 d-flex justify-content-between">
                  <div>
                    <h1 class="h3 fs-5 text-center custom-dark" id="export_label_log_history_modalLabel">Select Fields</h1>
                  </div>
                  <div>
                      <button type="button" class="btn text-white" style="background:#26c360;" onclick="ExportAssetLogHistoryData()">Download</button>
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
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field15">Tax Invoice Attachment</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field15" value="tax_invoice_attachment">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
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
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field19">Master Lease Agreement</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field19" value="master_lease_agreement">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
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
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field31">Insurance Attachment</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field31" value="insurance_attachment">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
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
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field37">Registration Certificate Attachment</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field37" value="reg_certificate_attachment">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field38">Fitness Certificate Expiry <br>Date</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field38" value="fc_expiry_date">
                        </div>
                      </div>
                    </div>
                    
                    <!--<div class="col-md-3 col-12 mb-3" hidden>-->
                    <!--  <div class="d-flex justify-content-between align-items-center">-->
                    <!--    <label class="form-check-label mb-0" for="field39">Fitness Certificate <br>Attachment</label>-->
                    <!--    <div class="form-check form-switch m-0">-->
                    <!--      <input class="form-check-input get-export-label" type="checkbox" id="field39" value="fc_attachment">-->
                    <!--    </div>-->
                    <!--  </div>-->
                    <!--</div>-->
                    
                    
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
        <?php
                //   dd($timeline);
        ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRightHR01" aria-labelledby="offcanvasRightHR01Label">
          <div class="offcanvas-header">
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Logs & History Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetLogHistoryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetLogHistoryFilter()">Apply</button>
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
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine1" value="today"  <?php if ($timeline == "today") echo 'checked'; ?>>
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine2" value="this_week" <?php if ($timeline == "this_week") echo 'checked'; ?> >
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine3" value="this_month" <?php if ($timeline == "this_month") echo 'checked'; ?> >
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" name="STtimeLine" id="timeLine4" value="this_year" <?php $timeline == "this_year" ? 'checked' : ''?> >
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" value="{{!empty($from_date) ? $from_date : ''}}" max="{{date('Y-m-d')}}" value="">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control"value="{{!empty($to_date) ? $to_date : ''}}" max="{{date('Y-m-d')}}" value="">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearAssetLogHistoryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyAssetLogHistoryFilter()">Apply</button>
            </div>
            
          </div>
        </div>
    

@section('script_js')


<script>
    // Initialize DataTable
    var assetLogsTable;

    $(document).ready(function() {
        initializeDataTable();
        
        // Initialize Select2 if needed
        $('.custom-select2-field').select2({
            width: '100%',
            dropdownParent: $('#offcanvasRightHR01')
        });
        });

    function initializeDataTable() {
        // Destroy existing instance if it exists
        if ($.fn.DataTable.isDataTable('#assetLogsTable')) {
            $('#assetLogsTable').DataTable().destroy();
        }
        
         $('#loadingOverlay').show();
        assetLogsTable = $('#assetLogsTable').DataTable({
            pageLength: 15,
            pagingType: "simple",
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.asset_management.asset_master.logs_history') }}",
                type: "GET",  // Changed from GET to POST for better security with filters
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    // Add filter parameters
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
                error: function(xhr, error, thrown) {
                    console.error('AJAX Error:', xhr.responseText);
                    // Display user-friendly error message
                    let errorMsg = 'Error loading data';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    $('#assetLogsTable').DataTable().clear().draw();
                    $('#assetLogsTable').find('tbody').html(
                        '<tr><td colspan="9" class="text-danger">'+errorMsg+'</td></tr>'
                    );
                }
            },
            columns: [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<div class="form-check">
                            <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                   name="is_select[]" type="checkbox" value="${row.asset_vehicle_id}">
                        </div>`;
                    }
                },
                { data: 'vehicle_id', name: 'vehicle_id' },
                { data: 'location', name: 'location' },
                { data: 'chassis_number', name: 'chassis_number' },
                { data: 'telematics_serial_no', name: 'telematics_serial_no' },
                { data: 'vehicle_type', name: 'vehicle_type' },
                { data: 'vehicle_model', name: 'vehicle_model' },
                { 
                    data: 'updated_at', 
                    name: 'updated_at',
                    render: function(data, type, row) {
                        if (!data) return 'N/A';
                        const date = new Date(data);
                        const formattedDate = date.toLocaleDateString('en-GB', { 
                            day: 'numeric', 
                            month: 'short', 
                            year: 'numeric' 
                        });
                        const formattedTime = date.toLocaleTimeString('en-US', { 
                            hour: '2-digit', 
                            minute: '2-digit', 
                            hour12: true 
                        });
                        return `<div>${formattedDate}</div><div><small>${formattedTime}</small></div>`;
                    }
                },
                {
    data: 'action',
    name: 'action',
    orderable: false,
    searchable: false,
    render: function(data, type, row) {
        const log_id = encodeURIComponent(row.encrypted_log_id);
        const asset_vehicle_id = encodeURIComponent(row.encrypted_asset_vehicle_id);
        const previewUrl = `/admin/asset-management/asset-master/log_history/preview/${log_id}/${asset_vehicle_id}`;
        
        return `
            <div class="dropdown">
                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                    <li>
                        <a href="${previewUrl}" 
                           class="dropdown-item d-flex align-items-center justify-content-center">
                            <i class="bi bi-eye me-2 fs-5"></i> View
                        </a>
                    </li>
                </ul>
            </div>`;
    }
}

            ],
        rowCallback: function(row, data, index) {
                // Initialize selectedRows array if it doesn't exist
                if (typeof selectedRows === 'undefined') {
                    selectedRows = [];
                }
                
                // Set checkbox state based on whether this row is selected
                $(row).find('input[type="checkbox"]').prop('checked', $.inArray(data.vehicle_id, selectedRows) !== -1);
                
                // Add click handler for the checkbox
                $(row).find('input[type="checkbox"]').on('change', function() {
                    var id = data.vehicle_id;
                    var index = $.inArray(id, selectedRows);
                    
                    if (this.checked && index === -1) {
                        selectedRows.push(id);
                    } else if (!this.checked && index !== -1) {
                        selectedRows.splice(index, 1);
                    }
                    
                    // Update "Select All" checkbox state
                    updateSelectAllCheckbox();
                });
                
                return row;
        },
    
            lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
            responsive: true,
            scrollX: true,

            // drawCallback: function(settings) {
            //     // Update total records count
            //     $('#totalRecordsCount').text(settings.json.recordsTotal);
                
            //     // Initialize checkbox handlers
            //     initCheckboxHandlers();
            // },
            initComplete: function () {
            $('#loadingOverlay').hide();
            let searchDelay;
            let lastNotification;
            let lastSearchTerm = '';

    $('#assetLogsTable_filter input')
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
                assetLogsTable.search(searchTerm).draw(); // ✅ Fixed here
            }, 400);
        });
},
            drawCallback: function(settings) {
                var response = settings.json;
                if (response) {
                    // $('#totalRecordsSpan').text(response.recordsTotal);
                    $('#LogHistory_Filter_Count').text(response.recordsFiltered);
                }
            }

        });
    }

    function initCheckboxHandlers() {
        // Handle select all checkbox
        $('#CSelectAllBtn').off('change').on('change', function() {
            $('.sr_checkbox').prop('checked', this.checked);
        });

        // Handle individual checkboxes
        $(document).off('change', '.sr_checkbox').on('change', '.sr_checkbox', function() {
            const allChecked = $('.sr_checkbox:checked').length === $('.sr_checkbox').length;
            $('#CSelectAllBtn').prop('checked', allChecked);
        });
    }

    function applyAssetLogHistoryFilter() {
       // Get filter values using correct names
    // const status = $('input[name="assetType"]:checked').val(); // Changed from 'status' to 'assetType'
    const timeline = $('input[name="STtimeLine"]:checked').val();
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();
    const city = $('#city_id').val(); // Changed from location_id to city_id

    // Get the DataTable instance
    var table = $('#assetLogsTable').DataTable();
    table.ajax.reload();

    
    // Close the offcanvas
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
    }

    function clearAssetLogHistoryFilter() {
        // Reset all filter inputs
        // $('input[name="assetType"][value="all"]').prop('checked', true);
        $('input[name="STtimeLine"]').prop('checked', false);
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id').val('').trigger('change');
        
        // Reload DataTable with cleared filters
        $('#assetLogsTable').DataTable().ajax.reload();
        
        // Close the offcanvas filter panel
        const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
        if (bsOffcanvas) {
            bsOffcanvas.hide();
        }
   }

    // function ExportAssetLogHistoryData() {

    //     const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
    //     const timeline = selectedTimeline ? selectedTimeline.value : '';
    
    //     const from_date = document.getElementById('FromDate').value;
    //     const to_date = document.getElementById('ToDate').value;
    //     const city = document.getElementById('city_id').value;
    
    //     let req_ids = [];
    //     $('input[name="is_select[]"]:checked').each(function () {
    //         req_ids.push($(this).val());
    //     });
    
    //     let get_export_labels = [];
    //     $('.get-export-label:checked').each(function () {
    //         get_export_labels.push($(this).val());
    //     });
    
    //     if (get_export_labels.length === 0) {
    //         toastr.error("Please select at least one label Name.");
    //         return;
    //     }
    
    //     // Create form
    //     var form = $('<form>', {
    //         method: 'POST',
    //         action: "{{ route('admin.asset_management.asset_master.export.vehicle_log_history') }}"
    //     });
    
    //     // CSRF Token
    //     form.append($('<input>', {
    //         type: 'hidden',
    //         name: '_token',
    //         value: '{{ csrf_token() }}'
    //     }));
    
    //     // Append selected IDs
    //     req_ids.forEach(function (id) {
    //         form.append($('<input>', {
    //             type: 'hidden',
    //             name: 'get_ids[]',
    //             value: id
    //         }));
    //     });
    
    //     // Append selected export labels
    //     get_export_labels.forEach(function (label) {
    //         form.append($('<input>', {
    //             type: 'hidden',
    //             name: 'get_export_labels[]',
    //             value: label
    //         }));
    //     });
    
    //     // Append filter values
    //     form.append($('<input>', { type: 'hidden', name: 'timeline', value: timeline }));
    //     form.append($('<input>', { type: 'hidden', name: 'from_date', value: from_date }));
    //     form.append($('<input>', { type: 'hidden', name: 'to_date', value: to_date }));
    //      form.append($('<input>', { type: 'hidden', name: 'city', value: city }));
    
    //     // Submit form
    //     form.appendTo('body').submit();
    // }
    
    function ExportAssetLogHistoryData() {
    const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
    const timeline = selectedTimeline ? selectedTimeline.value : '';
    const from_date = document.getElementById('FromDate').value;
    const to_date = document.getElementById('ToDate').value;
    const city = document.getElementById('city_id').value;

    // Get checked rows from DataTable
    let req_ids = [];
    $('#assetLogsTable tbody input[type="checkbox"]:checked').each(function() {
        const rowData = assetLogsTable.row($(this).closest('tr')).data();
        if (rowData) {
            req_ids.push(rowData.vehicle_id); // Make sure this matches your ID field
        }
    });

    let get_export_labels = [];
    $('.get-export-label:checked').each(function() {
        if (this.id !== 'field1') { // Exclude the "Select All" checkbox
            get_export_labels.push($(this).val());
        }
    });

    if (get_export_labels.length === 0) {
        toastr.error("Please select at least one label Name.");
        return;
    }

    // Create form
    var form = $('<form>', {
        method: 'POST',
        action: "{{ route('admin.asset_management.asset_master.export.vehicle_log_history') }}"
    });

    // CSRF Token
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: '{{ csrf_token() }}'
    }));

    // Append selected IDs
    req_ids.forEach(function(id) {
        form.append($('<input>', {
            type: 'hidden',
            name: 'get_ids[]',
            value: id
        }));
    });

    // Append selected export labels
    get_export_labels.forEach(function(label) {
        form.append($('<input>', {
            type: 'hidden',
            name: 'get_export_labels[]',
            value: label
        }));
    });

    // Append filter values
    form.append($('<input>', { type: 'hidden', name: 'timeline', value: timeline }));
    form.append($('<input>', { type: 'hidden', name: 'from_date', value: from_date }));
    form.append($('<input>', { type: 'hidden', name: 'to_date', value: to_date }));
    form.append($('<input>', { type: 'hidden', name: 'city', value: city }));

    // Submit form
    form.appendTo('body').submit();
}
    
    
    function SelectLogHistoryExportFields(){
        $('#export_label_log_history_modal').modal('show');
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
</script>


<script>
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

<script>
    
    // function SelectExportFields(){
    //     $("#export_label_log_history_modal").modal('show');
    // }
    
    function RightSideFilerOpen(){
        const bsOffcanvas = new bootstrap.Offcanvas('#offcanvasRightHR01');
        bsOffcanvas.show();
        
        // Wait for the offcanvas to be fully shown before initializing Select2
        document.getElementById('offcanvasRightHR01').addEventListener('shown.bs.offcanvas', function () {
            const selectFields = document.querySelectorAll('.custom-select2-field');
            if (selectFields.length > 0) {
                $(selectFields).select2({
                    width: '100%',
                    dropdownParent: $('#offcanvasRightHR01') // IMPORTANT
                });
    
                if (selectFields.length > 1) {
                    selectFields.forEach(function (el) {
                        el.classList.add('multi-select2'); 
                    });
                }
            }
        }, { once: true }); // run only once per open
    }
    
   
</script>
@endsection

</x-app-layout>
