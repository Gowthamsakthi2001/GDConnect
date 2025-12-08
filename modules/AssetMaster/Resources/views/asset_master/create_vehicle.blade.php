<x-app-layout>
    

  
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> Add Vehicle
                              </div>
                        </div>

                        <div class="col-md-6 d-flex gap-2 align-items-center justify-content-end">
                            <div class="text-center d-flex gap-2">
                                
                       <a href="{{route('admin.asset_management.asset_master.list')}}" class="btn btn-dark btn-md">Back</a>
                            </div>
                        </div>

                    </div>
                   
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form id="StoreAssetMasterVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group"> 
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number (VIN)</label>
                                <select class="form-select custom-select2-field form-control-sm" id="chassis_number" name="chassis_number" onchange="Get_QcData(this.value)">
                                    <option value="">Select</option>
                                    @if(isset($passed_chassis_numbers))
                                       @foreach($passed_chassis_numbers as $val)
                                         
                                          <option value="{{$val->id}}">{{$val->chassis_number}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_category">Vehicle Category</label>
                                <!--<input type="text" class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category"  value="" placeholder="Enter Vehicle Category">-->
                                <select class="form-control bg-white" name="vehicle_category" style="padding:12px 20px;" id="vehicle_category">
                                    <option value="">Select</option>
                                    <option value="regular_vehicle">Regular Vehicle</option>
                                    <option value="low_speed_vehicle">Low Speed Vehicle</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type<span class="text-danger fw-bold">*</span></label>
                                <select class="form-select custom-select2-field form-control-sm" id="vehicle_type" name="vehicle_type">
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                       @foreach($vehicle_types as $type)
                                          <option value="{{$type->id}}">{{$type->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="model">Model<span class="text-danger fw-bold">*</span></label>
                                <!--<input type="text" class="form-control bg-white" name="model" id="model"  value="" placeholder="Enter Model">-->
                                <select class="form-select custom-select2-field form-control-sm" id="model" name="model">
                                    <option value="">Select</option>
                                    @if(isset($vehicle_models))
                                       @foreach($vehicle_models as $type)
                                          <option value="{{$type->id}}" data-id="{{$type->id}}" data-make="{{$type->make}}" data-variant="{{$type->variant}}" data-color="{{$type->color}}">{{$type->vehicle_model}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="make">Make</label>
                                <!--<input type="text" class="form-control bg-white" style="padding: 12px 20px;" name="make" id="make"  value="" placeholder="Enter Make">-->
                                <select class="form-select custom-select2-field form-control-sm" name="make" id="make">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>


                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="variant">Variant</label>
                                <!--<input type="text" class="form-control bg-white" name="variant" id="variant"  value="" placeholder="Enter Variant">-->
                                <select class="form-select custom-select2-field form-control-sm" name="variant" id="variant">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="color">Color</label>
                                <!--<input type="text" class="form-control bg-white" name="color" id="color"  value="" placeholder="Enter Color">-->
                                 <select class="form-select custom-select2-field form-control-sm" name="color" id="color">
                                    <option value="">Select</option>
                                    @if(isset($colors))
                                       @foreach($colors as $color)
                                          <option value="{{$color->id}}">{{$color->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number<span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number"  value="" placeholder="Enter Engine Number/Motor Number">
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id"  value="" placeholder="Enter Vehicle ID">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_number">Tax Invoice Number</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_number" id="tax_invoice_number"  value="" placeholder="Enter Tax Invoice Number">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_date">Tax Invoice Date</label>
                                <input type="date" class="form-control bg-white" name="tax_invoice_date" id="tax_invoice_date"  value="" placeholder="Enter Tax Invoice Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_value">Invoice Value/Purchase Price</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_value" id="tax_invoice_value"  value="" onkeypress="return isNumberKeyNew(event)" placeholder="Enter Invoice Value/Purchase Price">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_attachment">Tax Invoice Attachment</label>
                                <input type="file" class="form-control bg-white" name="tax_invoice_attachment" id="tax_invoice_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'tax_invoice_Image')">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('tax_invoice_Image')">
                                    ✖
                                </button>
                                <img id="tax_invoice_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Tax Invoice Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                         
                                <iframe id="tax_invoice_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>

                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="location">Location</label>
                                <select class="form-select custom-select2-field form-control-sm" id="location" name="location">
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city_code">City<span class="text-danger fw-bold">*</span></label>
                                <select class="form-select custom-select2-field form-control-sm" id="city_code" name="city_code" onchange="getZones(this.value)">
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}">{{$val->city_name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="zone_id">Zone <span class="text-danger fw-bold">*</span></label>
                                
                                    <select class="form-select border-0 border-bottom border-1 rounded-0 shadow-none custom-select2-field" id="zone_id" name="zone_id">
                                        <option value="">Select a city first</option>
                                      
                                    </select>
                              
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id">GD Hub ID Allocated</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id" id="gd_hub_id"  value="" placeholder="Enter GD Hub ID Allocated">
                            </div>
                        </div>
                        
                                                
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id_exiting">GD Hub ID Existing</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id_exiting" id="gd_hub_id_exiting"  value="" placeholder="Enter GD Hub ID Existing">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="financing_type">Financing Type</label>
                                <!--<input type="text" class="form-control bg-white" name="financing_type" id="financing_type"  value="" placeholder="Financing Type">-->
                                <select class="form-select custom-select2-field form-control-sm" name="financing_type" id="financing_type">
                                    <option value="">Select</option>
                                    @if(isset($financing_types))
                                       @foreach($financing_types as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="asset_ownership">Asset Ownership</label>
                                <!--<input type="text" class="form-control bg-white" name="asset_ownership" id="asset_ownership"  value="" placeholder="Enter Asset Ownership">-->
                                
                                <select class="form-select custom-select2-field form-control-sm" name="asset_ownership" id="asset_ownership">
                                    <option value="">Select</option>
                                    @if(isset($asset_ownerships))
                                       @foreach($asset_ownerships as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="leasing_partner">Leasing Partner</label>
                                <select class="form-select custom-select2-field form-control-sm" name="leasing_partner" id="leasing_partner">
                                    <option value="">Select</option>
                                    @if(isset($leasing_partners))
                                       @foreach($leasing_partners as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="master_lease_agreement">Master Lease Agreement</label>
                                <input type="file" class="form-control bg-white" name="master_lease_agreement" id="master_lease_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'master_lease_Image')">
                            </div>
                        </div>
                        
                        <div class="col-md-12 mb-4 my-4">
                         <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('master_lease_Image')">
                                    ✖
                                </button>
                                <img id="master_lease_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Master Lease Agreement"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                      <iframe id="master_lease_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_start_date">Lease Start Date</label>
                                <input type="date" class="form-control bg-white" name="lease_start_date" id="lease_start_date"  value="" placeholder="Enter Lease Start Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_end_date">Lease End Date</label>
                                <input type="date" class="form-control bg-white" name="lease_end_date" id="lease_end_date"  value="" placeholder="Enter Lease End Date">
                            </div>
                        </div>
                        

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="emi_lease_amount">EMI/Lease Amount</label>
                                <input type="text" class="form-control bg-white" name="emi_lease_amount" id="emi_lease_amount"  value="" onkeypress="return isNumberKeyNew(event)" placeholder="Enter EMI/Lease Amount">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation">Hypothecation</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation" id="hypothecation"  value="" placeholder="Enter Hypothecation">-->
                                <select class="form-control bg-white" name="hypothecation" style="padding:12px 20px;" id="hypothecation">
                                    <option>Select</option>
                                    <option value-"yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation_to">Hypothecated To</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation_to" id="hypothecation_to"  value="" placeholder="Enter Hypothecation To">-->
                                <select class="form-select custom-select2-field form-control-sm" name="hypothecation_to" id="hypothecation_to">
                                    <option value="">Select</option>
                                    @if(isset($hypothecations))
                                       @foreach($hypothecations as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                        
                        
                         <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation_document">Hypothecation Document</label>
                                <input type="file" class="form-control bg-white" name="hypothecation_document" id="hypothecation_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hypothecation_Image')">
                            </div>
                        </div>
                        
                         <div class="col-md-12 mb-4 my-4">
                     <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('hypothecation_Image')">
                                    ✖
                                </button>
                                <img id="hypothecation_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Hypothecation Document"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                     
                                 <iframe id="hypothecation_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        
                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_type">Insurance Type</label>
                                <!--<input type="text" class="form-control bg-white" name="insurance_type" id="insurance_type"  value="" placeholder="Enter Insurance Type">-->
                                <select class="form-select custom-select2-field form-control-sm" name="insurance_type" id="insurance_type">
                                    <option value="">Select</option>
                                    @if(isset($insurance_types))
                                       @foreach($insurance_types as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurer_name">Insurer Name</label>
                                <!--<input type="text" class="form-control bg-white" name="insurer_name" id="insurer_name"  value="" placeholder="Enter Insurer Name">-->
                                 <select class="form-select custom-select2-field form-control-sm" name="insurer_name" id="insurer_name">
                                    <option value="">Select</option>
                                    @if(isset($insurer_names))
                                       @foreach($insurer_names as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        

                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_number">Insurance Number</label>
                                <input type="text" class="form-control bg-white" name="insurance_number" id="insurance_number"  value="" placeholder="Enter Insurance Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_start_date">Insurance Start Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_start_date" id="insurance_start_date"  value="" placeholder="Enter Insurance Start Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_expiry_date">Insurance Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_expiry_date" id="insurance_expiry_date"  value="" placeholder="Enter Insurance Expiry Date">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_attachment">Insurance Attachment</label>
                                <input type="file" class="form-control bg-white" name="insurance_attachment" id="insurance_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'insurance_Image')">
                            </div>
                        </div>
                        
                         <div class="col-md-12 mb-4 my-4">
                        <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('insurance_Image')">
                                    ✖
                                </button>
                                <img id="insurance_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Insurance Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                                                              
                                <iframe id="insurance_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_type">Registration Type</label>
                                <!--<input type="text" class="form-control bg-white" name="registration_type" id="registration_type"  value="" placeholder="Enter Registration Type">-->
                                
                                 <select class="form-select custom-select2-field form-control-sm" name="registration_type" id="registration_type">
                                    <option value="">Select</option>
                                    @if(isset($registration_types))
                                       @foreach($registration_types as $val)
                                         
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_status">Registration Status</label>
                                <input type="text" class="form-control bg-white" name="registration_status" id="registration_status"  value="" placeholder="Enter Registration Status">
                            </div>
                        </div>
                        
                        
                           <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_number">Temporary Registration Number</label>
                                <input type="text" class="form-control bg-white" name="temporary_registration_number" id="temporary_registration_number"   placeholder="Enter Temporary Registration Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_date">Temporary Registration Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_date" id="temporary_registration_date"   placeholder="Enter Temporary Registration Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_expiry_date">Temporary Registration Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_expiry_date" id="temporary_registration_expiry_date"   placeholder="Enter Temporary Registration Expiry Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_certificate_attachment">Temporary Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="temporary_certificate_attachment" id="temporary_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'temporary_certificate_Image')">
                            </div>
                        </div>
          
                        <div class="col-md-12 mb-4 my-4">
                            
                              <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('temporary_certificate_Image')">
                                    ✖
                                </button>
                                <img id="temporary_certificate_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                      <iframe id="temporary_certificate_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_number">Permanent Registration Number</label>
                                <input type="text" class="form-control bg-white" name="permanent_reg_number" id="permanent_reg_number"  value="" placeholder="Enter Permanent Registration Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_date">Permanent Registration Date</label>
                                <input type="date" class="form-control bg-white" name="permanent_reg_date" id="permanent_reg_date"  value="" placeholder="Enter Permanent Registration Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_expiry_date">Registration Certificate Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="reg_certificate_expiry_date" id="reg_certificate_expiry_date"  value="" placeholder="Enter Registration Certificate Expiry Date">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hsrp_certificate_attachment">HSRP Copy Attachment</label>
                                <input type="File" class="form-control bg-white" name="hsrp_certificate_attachment" id="hsrp_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hsrp_certificate_Image')">
                            </div>
                        </div>
          
                        <div class="col-md-12 mb-4 my-4">
                           <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('hsrp_certificate_Image')">
                                    ✖
                                </button>
                                <img id="hsrp_certificate_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                    <iframe id="hsrp_certificate_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_attachment">Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="reg_certificate_attachment" id="reg_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'reg_certificate_Image')">
                            </div>
                        </div>
          
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('reg_certificate_Image')">
                                    ✖
                                </button>
                                <img id="reg_certificate_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                   <iframe id="reg_certificate_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_expiry_date">Fitness Certificate Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="fc_expiry_date" id="fc_expiry_date">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_attachment">Fitness Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="fc_attachment" id="fc_attachment_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'fc_attachment_Image')">
                            </div>
                        </div>
                         
                        <div class="col-md-12 mb-4 my-4">
                       <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('fc_attachment_Image')">
                                    ✖
                                </button>
                                <img id="fc_attachment_Image"
                                     src="{{ asset('admin-assets/img/defualt_upload_img.jpg') }}"
                                     alt="Fitness Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover;" onclick="OpenImageModal(this.src)">
                                     
                                  <iframe id="fc_attachment_PDF"
                                        src=""
                                        style="width: 100%; height: 100%; display: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="servicing_dates">Servicing Dates</label>
                                <input type="text" class="form-control bg-white" name="servicing_dates" id="servicing_dates"  value="" placeholder="Enter Servicing Dates">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <label class="input-label mb-2 ms-1 d-block">Road Tax Applicable</label>
                            <select class="form-control bg-white" name="road_tax_applicable" id="road_tax_applicable">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                          </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_amount">Road Tax Amount</label>
                                <input type="text" class="form-control bg-white" name="road_tax_amount" id="road_tax_amount"  value="" placeholder="Enter Road Tax Amount">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_renewal_frequency">Road Tax Renewal Frequency</label>
                                <input type="text" class="form-control bg-white" name="road_tax_renewal_frequency" id="road_tax_renewal_frequency"  value="" placeholder="Enter Road Tax Frequency">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="next_renewal_date">If Yes Road Tax Next Renewal Date</label>
                                <input type="date" class="form-control bg-white" name="next_renewal_date" id="next_renewal_date"  value="" placeholder="Enter If Yes Road Tax Next Renewal Date">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                         </div>
                     
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_type">Battery Type</label>
                                <select name="battery_type" id="battery_type" class="form-control bg-white">
                                    <option value="">Select</option>
                                    <option value="1">Self-Charging</option>
                                    <option value="2">Portable</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_variant_name">Battery Variant Name</label>
                                <input type="text" class="form-control bg-white" name="battery_variant_name" id="battery_variant_name"  value="" placeholder="Enter Battery Variant Name">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no">Battery Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no" id="battery_serial_no"  value="" placeholder="Enter Battery Serial Number - Original">
                            </div>
                        </div>
                        
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement1">Battery Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement1" id="battery_serial_no_replacement1"  value="" placeholder="Enter Battery Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement2">Battery Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement2" id="battery_serial_no_replacement2"  value="" placeholder="Enter Battery Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement3">Battery Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement3" id="battery_serial_no_replacement3"  value="" placeholder="Enter Battery Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement4">Battery Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement4" id="battery_serial_no_replacement4"  value="" placeholder="Enter Battery Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement5">Battery Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement5" id="battery_serial_no_replacement5"  value="" placeholder="Enter Battery Serial Number - Replacement 5">
                            </div>
                        </div>
                     
                         <div class="col-md-6 mb-3">
                             </div>
                             
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_variant_name">Charger Variant Name</label>
                                <!--<input type="text" class="form-control bg-white" name="charger_variant_name" id="charger_variant_name"  value="" placeholder="Enter Charger Variant Name">-->
                                <select name="charger_variant_name" id="charger_variant_name" class="form-control bg-white">
                                    <option value="">Select</option>
                                    <option>ABC</option>
                                    <option>XYZ</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no">Charger Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no" id="charger_serial_no"  value="" placeholder="Enter Charger Serial Number - Original">
                            </div>
                        </div>
                        
                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement1">Charger Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement1" id="charger_serial_no_replacement1"  value="" placeholder="Enter Charger Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement2">Charger Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement2" id="charger_serial_no_replacement2"  value="" placeholder="Enter Charger Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement3">Charger Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement3" id="charger_serial_no_replacement3"  value="" placeholder="Enter Charger Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement4">Charger Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement4" id="charger_serial_no_replacement4"  value="" placeholder="Enter Charger Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement5">Charger Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement5" id="charger_serial_no_replacement5"  value="" placeholder="Enter Charger Serial Number - Replacement 5">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_variant_name">Telematics Variant Name</label>
                                <input type="text" class="form-control bg-white" name="telematics_variant_name" style="padding:12px 20px;" id="telematics_variant_name"  value="" placeholder="Enter Telematics Variant Name">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_oem">Telematics OEM</label>
                                <select class="form-select custom-select2-field form-control-sm" name="telematics_oem" id="telematics_oem">
                                    <option value="">Select</option>
                                    @if(isset($telematics))
                                       @foreach($telematics as $val)
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no"> Telematics Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no" id="telematics_serial_no"  value="" placeholder="Enter Telematics Serial Number - Original">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_imei_no"> Telematics IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="telematics_imei_no" id="telematics_imei_no"  value="" placeholder="Enter Telematics IMEI Number">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement1"> Telematics Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement1" id="telematics_serial_no_replacement1"  value="" placeholder="Enter  Telematics Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement2"> Telematics Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement2" id="telematics_serial_no_replacement2"  value="" placeholder="Enter  Telematics Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement3"> Telematics Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement3" id="telematics_serial_no_replacement3"  value="" placeholder="Enter  Telematics Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement4"> Telematics Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement4" id="telematics_serial_no_replacement4"  value="" placeholder="Enter  Telematics Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement5"> Telematics Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement5" id="telematics_serial_no_replacement5"  value="" placeholder="Enter  Telematics Serial Number - Replacement 5">
                            </div>
                        </div>
                     
                        
                          <div class="col-md-6 mb-3" >
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client">Client</label>
                                <!--<input type="text" class="form-control bg-white" name="client" id="client"  value="" placeholder="Enter Client Name">-->
                                <select class="form-select custom-select2-field form-control-sm" name="client" id="client">
                                    <option value="">Select Client</option>
                                    @if(isset($customers))
                                       @foreach($customers as $customer)
                                          <option value="{{$customer->id}}">{{$customer->trade_name ?? '-'}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3" >
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                <input type="date" class="form-control bg-white" name="vehicle_delivery_date" id="vehicle_delivery_date"  value="" placeholder="Enter Vehicle Delivery Date">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                                <!--<input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="" placeholder="Enter Vehicle Status">-->
                                <select class="form-select custom-select2-field form-control-sm" name="vehicle_status" id="vehicle_status">
                                    <option value="">Select</option>
                                    @if(isset($inventory_locations))
                                       @foreach($inventory_locations as $val)
                                         
                                          <option value="{{$val->id}}">{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end gap-2">
                            <button type="button" class="btn btn-danger px-3">Reset</button>
                            <button type="submit" id="submitBtn" class="btn btn-success px-3">Submit</button>
                        </div>
               
                    </div>
                    </form>
                </div>
            </div>
            
    </div>
    
        <!--Image Preview Section-->
    
<div class="modal fade" id="BKYC_Verify_view_modal" tabindex="-1" aria-labelledby="BKYC_Verify_viewLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content rounded-4">

      <!-- Header with fixed control buttons -->
      <div class="modal-header border-0 d-flex justify-content-end gap-1">
        <button class="btn btn-sm btn-dark" onclick="zoomIn()">
          <i class="bi bi-zoom-in"></i>
        </button>
        <button class="btn btn-sm btn-dark" onclick="zoomOut()">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button class="btn btn-sm btn-dark" onclick="rotateImage()">
          <i class="bi bi-arrow-repeat"></i>
        </button>
        <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <!-- Scrollable modal body -->
      <div class="modal-body text-center py-6" style="overflow: auto; max-height: 80vh;">
        <img src="" id="kyc_image" style="max-width: 100%; transition: transform 0.3s ease;">
      </div>

    </div>
  </div>
</div>
    
   
@section('script_js')
<script>
        // function showImagePreview(input,ElementID) {
        //     const file = input.files[0];
        //     if (file) {
        //         const reader = new FileReader();
        //         reader.onload = function(e) {
        //             const previewImage = document.getElementById(ElementID);
        //             previewImage.src = e.target.result;
        //             previewImage.style.display = 'block'; 
        //         };
        //         reader.readAsDataURL(file);
        //     }
        // }
        
    function showImagePreview(input, elementBaseID) {
        const file = input.files[0];
        const imgPreview = document.getElementById(elementBaseID);
        const pdfPreview = document.getElementById(elementBaseID.replace("Image", "PDF"));

        if (file) {
            const fileType = file.type;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (fileType === "application/pdf") {
                    // Show PDF preview
                    pdfPreview.src = e.target.result;
                    pdfPreview.style.display = "block";
                    imgPreview.style.display = "none";
                } else {
                    // Show Image preview
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = "block";
                    pdfPreview.style.display = "none";
                }
            };
            reader.readAsDataURL(file);
        }
    }
    
        function resetPreview(elementBaseID) {
        const imgPreview = document.getElementById(elementBaseID);
        const pdfPreview = document.getElementById(elementBaseID.replace("Image", "PDF"));
        const fileInput = document.getElementById(elementBaseID.replace("_Image", "_img")); // matches input ID like 'Adhaar_back_img'
    
        // Reset previews
        imgPreview.src = "{{ asset('admin-assets/img/defualt_upload_img.jpg') }}";
        imgPreview.style.display = "block";
        pdfPreview.src = "";
        pdfPreview.style.display = "none";
    
        // Reset file input value
        fileInput.value = "";
    
        // Trigger file input click
        fileInput.click();
    }
    </script>

<script>

    
    
    
    $("#StoreAssetMasterVehicleForm").submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");

    var $submitBtn = $("#submitBtn");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Submitting...");

    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.store_vehicle') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

            $submitBtn.prop("disabled", false).html(originalText);

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href="{{route('admin.asset_management.asset_master.list')}}";
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

function Get_QcData(id){

    if (id) {
        $.ajax({
            url: "{{ route('admin.asset_management.asset_master.get_qc_data') }}",
            type: "GET",
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    var res_data = response.data;
                    
                    $("#motor_number").val(res_data.quality_check.motor_number);
                    $("#vehicle_type").val(res_data.quality_check.vehicle_type).trigger("change");
                    $("#location").val(res_data.quality_check.location).trigger("change");
                    $("#model").val(res_data.quality_check.vehicle_model).trigger("change");
                    $("#battery_serial_no").val(res_data.quality_check.battery_number);
                    $("#telematics_serial_no").val(res_data.quality_check.telematics_number);
                    
                    let cityID = res_data.quality_check.location;
                    $("#city_code").val(cityID).trigger("change");

                    // 🔹 Pass zone_id so getZones can select it once loaded
                    getZones(cityID, res_data.quality_check.zone_id);
                    
                    
                } else {
                    toastr.warning("No data found.");
                }
            },
            error: function(xhr) {
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
    }
}


    
function getZones(CityID, selectedZoneID = null) {
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

                    // 🔹 If a zone_id is provided, select it AFTER loading options
                    if (selectedZoneID) {
                        ZoneDropdown.val(selectedZoneID).trigger('change');
                    }
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
    }
}


$(document).ready(function() {
    $('#model').on('change', function() {
        var selectedOption = $(this).find('option:selected');

        var make = selectedOption.data('make') || '';
        var variant = selectedOption.data('variant') || '';
        // var color = selectedOption.data('color') || '';
        var Id = selectedOption.data('id') || '';

        // Set dropdowns dynamically
        $('#make').html('<option value="' + Id + '" selected>' + make + '</option>');
        $('#variant').html('<option value="' + Id + '" selected>' + variant + '</option>');
        // $('#color').html('<option value="' + Id + '" selected>' + color + '</option>');
    });
});

function OpenImageModal(img_url) {
    $("#kyc_image").attr("src", ""); // Clear image first
    $("#BKYC_Verify_view_modal").modal('show'); // Corrected selector
    $("#kyc_image").attr("src", img_url); // Load new image
}

let scale = 1;
let rotation = 0;

function OpenImageModal(img_url) {
    scale = 1;
    rotation = 0;
    updateImageTransform();
    $("#kyc_image").attr("src", img_url);
    $("#BKYC_Verify_view_modal").modal('show');
}

function zoomIn() {
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}


</script>
@endsection
</x-app-layout>
