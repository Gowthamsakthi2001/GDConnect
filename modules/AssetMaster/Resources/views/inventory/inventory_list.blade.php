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

    .form-check-input[type="checkbox"] {
        width: 2.3rem;
        height: 1.2rem;
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
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="card-title h5 custom-dark m-0"><a href="{{ route('admin.asset_management.asset_master.list') }}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a>Inventory
                             <span class="badge text-muted shadow ms-2 p-2" style="background:rgb(234,234,234);" id="Inventory_Filter_Count">{{ $totalRecords ?? 0 }}</span>
                            </div>
                          
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                           
                            <div class="text-center d-flex gap-2">
                                 <div class="m-2 bg-white p-2 px-3 border-gray">
                                     
                                    <button type="button" id="exportBtn" class="bg-white text-dark border-0">
                                          <i class="bi bi-download fs-17 me-1"></i> Export
                                    </button>
                                    
                                 </div>
                                 
                                <div class="m-2 bg-white p-2 px-3 border-gray" onclick="RightSideFilerOpen()"><i class="bi bi-filter fs-17"></i> Filter</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        



        <div class="table-responsive table-container">
            
                   <div id="loadingOverlay" class="datatable-loading-overlay">
                        <div class="loading-spinner"></div>
                    </div>
                    <table  id="InventoryTable_List" class="table text-left" style="width: 100%;">
                          <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                              <th scope="col" class="custom-dark">
                                  <div class="form-check">
                                      <input class="form-check-input" style="width:25px; height:25px;" type="checkbox" value="" id="CSelectAllBtn" title="Note : If you want to select all the tables in the list, first select 'All' from the dropdown above the table and then click. That will select the entire list.">
                                      <label class="form-check-label" for="CSelectAllBtn"></label>
                                    </div>
                                </th>
                              <th scope="col" class="custom-dark">Lot No</th>
                              <th scope="col" class="custom-dark">Chassis No</th>
                               <th scope="col" class="custom-dark">City</th>
                                <th scope="col" class="custom-dark">Zone</th>
                                 <th scope="col" class="custom-dark">Accountability Type</th>
                              <th scope="col" class="custom-dark">Vehicle Type</th>
                              <th scope="col" class="custom-dark">Vehicle Model</th>
                              <th scope="col" class="custom-dark">Vehicle ID</th>
                              <th scope="col" class="custom-dark">Battery No</th>
                              <th scope="col" class="custom-dark">Telematics No</th>
                              <th scope="col" class="custom-dark">Verified at</th>
                              <th scope="col" class="custom-dark">Current Status</th>
                                <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>

                        <tbody class="bg-white border border-white">
                       

                             
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
                      <button type="button" class="btn text-white InventoryExportBtn" style="background:#26c360;" onclick="ExportInventoryData()">Download</button>
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
                    
                    <div class="col-md-3 col-12 mb-3">
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
                        <label class="form-check-label mb-0" for="field16">City</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field16" value="city">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="zone">Zone</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field48" value="zone">
                        </div>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="accountability_type">Accountability Type</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field48" value="accountability_type">
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
                    
                    <div class="col-md-3 col-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check-label mb-0" for="field33">Registration Status</label>
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input get-export-label" type="checkbox" id="field33" value="registration_status">
                        </div>
                      </div>
                    </div>
                    
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
                    
                    <div class="col-md-3 col-12 mb-3">
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
            <h5 class="custom-dark" id="offcanvasRightHR01Label">Inventory</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
        
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearInventoryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyInventoryFilter()">Apply</button>
            </div>
         
           <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Status</h6></div>
               </div>
               <div class="card-body">
                    <!--<div class="form-check mb-3">-->
                    <!--  <input class="form-check-input" type="radio" name="status" id="status" value="all"  {{ request('status', 'all') == 'all' ? 'checked' : '' }}>-->
                    <!--  <label class="form-check-label" for="status">-->
                    <!--   All-->
                    <!--  </label>-->
                    <!--</div>-->
                    <!--@if(isset($inventory_locations))-->
                    <!--@foreach($inventory_locations as $data)-->
                    <!--   <div class="form-check mb-3">-->
                    <!--      <input class="form-check-input" type="radio" name="status"  id="status{{ $data->id }}"  value="{{$data->id}}"  {{ request('status') == $data->id ? 'checked' : '' }}>-->
                    <!--      <label class="form-check-label" for="status{{ $data->id }}">-->
                    <!--       {{$data->name}}-->
                    <!--      </label>-->
                    <!--    </div>-->
                    <!--@endforeach-->
                    <!--@endif-->
                    
                    <?php
                          $customers = \Modules\MasterManagement\Entities\CustomerMaster::where('status',1)->select('id','trade_name')->get();
                    ?>
                    

                     <div class="mb-3">
                        <label class="form-label" for="status">Select Status</label>
                        <select name="status" id="status" class="form-control custom-select2-field">
                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All</option>
                    
                            @if(isset($inventory_locations))
                                @foreach($inventory_locations as $data)
                                    <option value="{{ $data->id }}" {{ request('status') == $data->id ? 'selected' : '' }}>
                                        {{ $data->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
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
                        <select name="city_id" id="city_id" class="form-control custom-select2-field" onchange="getZones(this.value)">
                            <option value="">Select</option>
                            @if(isset($locations))
                            @foreach($locations as $l)
                            <option value="{{$l->id}}" {{ $city == $l->id ? 'selected' : '' }}>{{$l->city_name}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
            </div>


            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Zone</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="zone_id">Zone</label>
                        <select name="zone_id" id="zone_id" class="form-control custom-select2-field">
                            <option value="">Select a city first</option>
                        </select>
                    </div>
               </div>
            </div>
            
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Accountability Type</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="accountability_type_id">Accountability Type</label>
                        <select name="accountability_type_id" id="accountability_type_id" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($accountablity_types))
                            @foreach($accountablity_types as $type)
                            <option value="{{$type->id}}" {{ $accountability_type == $type->id ? 'selected' : '' }}>{{$type->name ?? ''}}</option>
                            @endforeach
                            @endif

                        </select>
                    </div>
               </div>
            </div>
            
            
            <div class="card mb-3">
               <div class="card-header p-2">
                   <div><h6 class="custom-dark">Select Customer</h6></div>
               </div>
               <div class="card-body">
 
                    <div class="mb-3">
                        <label class="form-label" for="accountability_type_id">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control custom-select2-field">
                            <option value="">Select</option>
                            @if(isset($customers))
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}" {{ $customer_id == $customer->id ? 'selected' : '' }}>{{$customer->trade_name ?? ''}}</option>
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
                      <input class="form-check-input select_time_line" type="radio" value="today" {{ request('timeline') == 'today' ? 'checked' : '' }} name="STtimeLine" id="timeLine1">
                      <label class="form-check-label" for="timeLine1">
                        Today
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_week" {{ request('timeline') == 'this_week' ? 'checked' : '' }} name="STtimeLine" id="timeLine2">
                      <label class="form-check-label" for="timeLine2">
                       This Week
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_month" {{ request('timeline') == 'this_month' ? 'checked' : '' }} name="STtimeLine" id="timeLine3">
                      <label class="form-check-label" for="timeLine3">
                       This Month
                      </label>
                    </div>
                    <div class="form-check mb-3">
                      <input class="form-check-input select_time_line" type="radio" value="this_year" {{ request('timeline') == 'this_year' ? 'checked' : '' }} name="STtimeLine" id="timeLine4">
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
                        <input type="date" name="from_date" id="FromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$from_date}}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="ToDate">To Date</label>
                        <input type="date" name="to_date" id="ToDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$to_date}}">
                    </div>
  
               </div>
            </div>
         
            <div class="d-flex gap-2 mb-3">
                <button class="btn btn-outline-secondary w-50" onclick="clearInventoryFilter()">Clear All</button>
                <button class="btn btn-success w-50" onclick="applyInventoryFilter()">Apply</button>
            </div>
            
          </div>
        </div>
    

@section('script_js')


<script>
    
$(document).ready(function () {
    
    $('#loadingOverlay').show();
        inventoryTable = $('#InventoryTable_List').DataTable({
            pageLength: 15,
            pagingType: "simple",
            processing: false,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.asset_management.asset_master.inventory.list') }}",
                type: "GET",
                data: function (d) {
                    // Pass filter data to the server
                    d.status = $('#status').val();
                    d.city = $('#city_id').val();
                    d.from_date = $('#FromDate').val();
                    d.to_date = $('#ToDate').val();
                    d.timeline = $('input[name="STtimeLine"]:checked').val();
                    d.zone = $('#zone_id').val();
                    d.customer = $('#customer_id').val();
                    d.accountability_type = $('#accountability_type_id').val();
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
                    console.error('DataTables error:', error, thrown);
                    toastr.error('Failed to load data. Please check the browser console for more details.');
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
                { data: 'id', name: 'id' },
                { data: 'chassis_no', name: 'assetVehicle.chassis_number' }, // Use relation for sorting
                { data: 'city', name: 'city' }, // Use relation for sorting
                { data: 'zone', name: 'zone' }, // Use relation for sorting
                { data: 'accountability_type', name: 'accountability_type' }, // Use relation for sorting
                { data: 'vehicle_type', name: 'vehicle_type' },
                { data: 'vehicle_model', name: 'vehicle_model' },
                { data: 'vehicle_id', name: 'assetVehicle.vehicle_id' }, // Use relation for sorting
                { data: 'battery_no', name: 'battery_no' },
                { data: 'telematics_no', name: 'telematics_no' },
                { data: 'verified_at', name: 'created_at' }, // Sort by the actual database column
                { data: 'current_status', name: 'current_status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
             lengthMenu: [[15, 25, 50, 100, 250, -1], [15, 25, 50, 100, 250, "All"]],
            responsive: true,
            scrollX: true,
            dom: '<"top"lf>rt<"bottom"ip><"clear">',
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: 'No data available in table',
                zeroRecords: 'No matching records found'
            },
            initComplete: function() {
                if ($.fn.select2) {
                    $('.custom-select2-field').select2({
                        width: '100%',
                        dropdownParent: $('#offcanvasRightHR01')
                    });
                }
                
            let searchDelay;
            let lastNotification;
            let lastSearchTerm = '';
            
            $('#InventoryTable_List_filter input')
                .off('keyup')
                .on('keyup', function()
                {
                    const searchTerm = this.value.trim();
                    
                    // Clear previous timeouts and notifications
                    clearTimeout(searchDelay);
                    if (lastNotification) {
                        toastr.clear(lastNotification);
                    }
                    
                    // Skip if same as last search
                    if (searchTerm === lastSearchTerm) {
                        return;
                    }
                    
                    // Validate search term length
                    if (searchTerm.length > 0 && searchTerm.length < 4) {
                        searchDelay = setTimeout(() => {
                            lastNotification = toastr.info(
                                "Please enter at least 4 characters for better results",
                                {timeOut: 2000}
                            );
                        }, 500);
                        return;
                    }
                    
                    // Perform search if valid length or empty
                    searchDelay = setTimeout(() => {
                        lastSearchTerm = searchTerm;
                        table.search(searchTerm).draw();
                    }, 400);
                });
            },
            drawCallback: function(settings) {

            var response = settings.json;
            if (response) {
                // $('#totalRecordsSpan').text(response.recordsTotal);
                console.log(response.recordsFiltered);
                $('#Inventory_Filter_Count').text(response.recordsFiltered);
            }
        }
        });
        
        // Error handling
    $.fn.dataTable.ext.errMode = 'none';
    $('#InventoryTable_List').on('error.dt', function(e, settings, techNote, message) {
        console.error('DataTables Error:', message);
        $('#loadingOverlay').hide();
        toastr.error('Error loading data. Please try again.');
    });

        // Handle master checkbox for selecting all rows on the current page
        $('#CSelectAllBtn').on('click', function() {
            var rows = inventoryTable.rows({ 'page': 'current' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        // Handle individual checkbox changes
        $('#InventoryTable_List tbody').on('change', 'input[type="checkbox"]', function() {
            if (!this.checked) {
                var el = $('#CSelectAllBtn').get(0);
                if (el && el.checked && ('indeterminate' in el)) {
                    el.indeterminate = true;
                }
            }
        });
    });

function applyInventoryFilter() {
    // Get filter values
    const status = $('input[name="status"]:checked').val();
    const timeline = $('input[name="STtimeLine"]:checked').val();
    const from_date = $('#FromDate').val();
    const to_date = $('#ToDate').val();
    const customer_id = $('#customer_id').val();
    const city = $('#city_id').val(); // Changed from location_id to city_id

    // Validate date range if either date is provided
    if (from_date || to_date) {
        if (!from_date || !to_date) {
            toastr.error("Both From Date and To Date are required when filtering by date");
            return;
        }
    }

    // Reload DataTable with new parameters
    $('#InventoryTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}

function clearInventoryFilter() {
    // Reset all filter inputs
    $('input[name="status"][value="all"]').prop('checked', true);
    $('input[name="STtimeLine"]').prop('checked', false);
    $('#FromDate').val('');
    $('#customer_id').val('');
    $('#ToDate').val('');
     $('#city_id').val('').trigger('change');
    $('#accountability_type_id').val('').trigger('change');
    $('#zone_id').val('').trigger('change');
    // Reload DataTable with cleared filters
    $('#InventoryTable_List').DataTable().ajax.reload();
    
    // Close the offcanvas filter panel
    const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasRightHR01'));
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }
}


    
    
    
    
    
    // function applyInventoryFilter() {
    //     const selectedStatus = document.querySelector('input[name="status"]:checked');
    //     const status = document.getElementById('status').value;
    //      const selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
    //      const timeline = selectedTimeline ? selectedTimeline.value : '';
    //     const from_date = document.getElementById('FromDate').value;
    //     const to_date = document.getElementById('ToDate').value;
    //     const city = document.getElementById('city_id').value;
        
    //     if(from_date != "" || to_date != ""){
    //         if(to_date == "" || from_date == ""){
    //             toastr.error("From Date and To Date is must be required");
    //             return;
    //         }
            
    //     }
        
    
    //     const url = new URL(window.location.href);
    //     url.searchParams.set('status', status);
    //      url.searchParams.set('city', city);
    //     // url.searchParams.set('from_date', from_date);
    //     // url.searchParams.set('to_date', to_date);
        
    // if (from_date && to_date) {
    //     // Use from_date and to_date, remove timeline
    //     url.searchParams.set('from_date', from_date);
    //     url.searchParams.set('to_date', to_date);
    //     url.searchParams.delete('timeline');
    // } else if (timeline) {
    //     // Use timeline, remove from_date and to_date
    //     url.searchParams.set('timeline', timeline);
    //     url.searchParams.delete('from_date');
    //     url.searchParams.delete('to_date');
    // }

    
    //     window.location.href = url.toString();
    // }


    
     
    // function clearInventoryFilter() {
    //     const url = new URL(window.location.href);
    //     url.searchParams.delete('status');
    //     url.searchParams.delete('from_date');
    //     url.searchParams.delete('to_date');
    //     url.searchParams.delete('timeline');
    //      url.searchParams.delete('city');
    //     window.location.href = url.toString();
    // }
    
    
    
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
    
    function SelectExportFields(){
        $("#export_select_fields_modal").modal('show');
    }
    
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportBtn').addEventListener('click', function () {
            let modal = new bootstrap.Modal(document.getElementById('export_select_fields_modal'));
            modal.show();
        });
    });
</script>


<script>

    function ExportInventoryData() {
        var $btn = $(".InventoryExportBtn"); 
        $btn.html('<i class="bi bi-hourglass-split"></i> Downloading...').prop("disabled", true);

        var get_export_labels = [];
        $('.get-export-label:checked').each(function () {
            get_export_labels.push($(this).val());
        });
    
        if (get_export_labels.length === 0) {
            toastr.error("Please select at least one label.");
            $btn.html('Download').prop("disabled", false);
            return;
        }

        var get_ids = [];
        $('input[name="is_select[]"]:checked').each(function () {
            get_ids.push($(this).val());
        });

        var status = document.getElementById('status').value;
        var selectedTimeline = document.querySelector('input[name="STtimeLine"]:checked');
        var timeline = selectedTimeline ? selectedTimeline.value : '';
        var from_date = document.getElementById('FromDate').value;
        var to_date = document.getElementById('ToDate').value;
        var city = document.getElementById('city_id').value;
        
        const zone = document.getElementById('zone_id').value;
        const customer = document.getElementById('customer_id').value;
        const accountability_type = document.getElementById('accountability_type_id').value;
    
        $.ajax({
            url: "{{ route('admin.asset_management.asset_master.export.inventory_detail') }}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                get_export_labels: get_export_labels,
                get_ids: get_ids,
                status: status,
                timeline: timeline,
                from_date: from_date,
                to_date: to_date,
                city: city,
                customer: customer ,
                zone : zone ,
                accountability_type : accountability_type
                
            },
            xhrFields: {
                responseType: 'blob' 
            },
            success: function (data) {
                var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "Inventory_{{ date('d-m-Y') }}.xlsx";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
    
                $btn.html('Download').prop("disabled", false);
            },
            error: function () {
                toastr.error("Network connection failed. Please try again.");
                $btn.html('Download').prop("disabled", false);
            }
        });
    }
    
    
function getZones(CityID) {
        let ZoneDropdown = $('#zone_id');
        ZoneDropdown.empty().append('<option value="">Loading...</option>');

           
        if (CityID) {
            $.ajax({
                url: "{{ route('global.get_zones', ':CityID') }}".replace(':CityID', CityID),
                type: "GET",
                success: function (response) {
                    ZoneDropdown.empty().append('<option value="">--Select Zone--</option>');
    
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function (key, zone) {
                            ZoneDropdown.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                        });
                        
                    } else {
                        ZoneDropdown.append('<option value="">No Zones available for this City</option>');
                    }
                },
                error: function () {
                    ZoneDropdown.empty().append('<option value="">Error loading zones</option>');
                }
            });
        } else {
            ZoneDropdown.empty().append('<option value="">Select a city first</option>');
            // ZoneWrapper.hide();
        }
       
       
    }
    
    
    $(document).ready(function () {
        const existingCity = "{{ $city ?? '' }}";
            if (existingCity) {
                getZones(existingCity);
            }
    });

</script>


@endsection
</x-app-layout>
