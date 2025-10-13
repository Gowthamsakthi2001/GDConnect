<x-app-layout>
    
    <div class="main-content">

             <div class="card bg-transparent my-4">
                <div class="card-header" style="background:#fbfbfb;">
                    <div class="row g-3">
                        <div class="col-md-6 d-flex align-items-center">
                              <div class="card-title h5 custom-dark m-0"> <a href="{{route('admin.asset_management.asset_master.list')}}" class="btn btn-sm shadow me-2"><i class="bi bi-arrow-left"></i> </a> View Asset Details
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
               <div class="card-header" style="background:#eef2ff;">
                     <h5 style="color:#1e3a8a;" class="fw-bold">Asset details</h5>
                     <p class="mb-0" style="color:#1e3a8a;">Asset in details</p>
                 </div>
                <div class="card-body">

                   <form id="ApproveAssetMasterVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                        @csrf
                      <div class="row">
                                                    <div class="col-md-6 mb-3">
                            <div class="form-group"> 
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number (VIN)</label>
                                <select class="form-select custom-select2-field form-control-sm bg-white"  disabled>
                                    <option value="">Select</option>
                                    @if(isset($passed_chassis_numbers))
                                       @foreach($passed_chassis_numbers as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->id == $val->id ? 'selected' : ''}}>{{$val->chassis_number}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input id="chassis_number" name="chassis_number" value="{{$vehicle_data->chassis_number ?? ''}}" type="hidden">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_category">Vehicle Category</label>
                                <!--<input type="text" class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category"  value="{{$vehicle_data->vehicle_category ?? ''}}" placeholder="Enter Vehicle Category" >-->
                                <select class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category">
                                    <option value="">Select</option>
                                    <option value="regular_vehicle" {{ ($vehicle_data->vehicle_category ?? '') == 'regular_vehicle' ? 'selected' : '' }} >Regular Vehicle</option>
                                    <option value="low_speed_vehicle" {{ ($vehicle_data->vehicle_category ?? '') == 'low_speed_vehicle' ? 'selected' : '' }} >Low Speed Vehicle</option>
                                 </select>
                                
                                
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <select class="form-select custom-select2-field form-control-sm"  disabled>
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                       @foreach($vehicle_types as $type)
                                          <option value="{{$type->id}}" {{$vehicle_data->vehicle_type == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="vehicle_type" name="vehicle_type" value="{{$vehicle_data->vehicle_type}}">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="model">Model</label>
                                <!--<input type="text" class="form-control bg-white" name="model" id="model"  value="{{$vehicle_data->model ?? ''}}" placeholder="Enter Model" >-->
                                
                                   <select class="form-select custom-select2-field form-control-sm"  disabled>
                                        <option value="">Select</option>
                                        @if(isset($vehicle_models))
                                           @foreach($vehicle_models as $type)
                                              <option value="{{$type->id}}"  data-id="{{$type->id}}" data-make="{{$type->make}}" data-variant="{{$type->variant}}" data-color="{{$type->color}}" {{$vehicle_data->model == $type->id ? 'selected' : ''}}>{{$type->vehicle_model}}</option>
                                           @endforeach
                                        @endif
                                    </select>
                            </div>
                        </div>
                         <input type="hidden" id="model" name="model" value="{{$vehicle_data->model}}">
                         
                    @php
                        $model_data = \Modules\AssetMaster\Entities\VehicleModelMaster::where('id', $vehicle_data->model)->first();
                    @endphp


                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="make">Make</label>
                                <!--<input type="text" class="form-control bg-white" style="padding: 12px 20px;" name="make" id="make"  value="{{$vehicle_data->make ?? ''}}" placeholder="Enter Make" >-->
                                <select class="form-select custom-select2-field form-control-sm"  disabled>
                                       @if($model_data)
                                            <option value="{{ $model_data->id }}" selected>{{ $model_data->make }}</option>
                                        @else
                                            <option value="">Select Make</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                         <input type="hidden" name="make" id="make" value="{{$model_data->make ?? ''}}">


                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="variant">Variant</label>
                                <!--<input type="text" class="form-control bg-white" name="variant" id="variant"  value="{{$vehicle_data->variant ?? ''}}" placeholder="Enter Variant" >-->
                             <select class="form-select custom-select2-field form-control-sm"  disabled>
                                       @if($model_data)
                                            <option value="{{ $model_data->id }}" selected>{{ $model_data->variant }}</option>
                                        @else
                                            <option value="">Select Variant</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="variant" id="variant" value="{{$model_data->variant ?? ''}}">
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="color">Color</label>
                                <!--<input type="text" class="form-control bg-white" name="color" id="color"  value="{{$vehicle_data->color ?? ''}}" placeholder="Enter Color" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="color" id="color">
                                    <option value="">Select</option>
                                     @if(isset($colors))
                                           @foreach($colors as $color)
                                              <option value="{{$color->id}}"  {{$vehicle_data->color == $color->id ? 'selected' : ''}}>{{$color->name}}</option>
                                           @endforeach
                                        @endif
                                </select>
                            </div>
                        </div>
                             
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number"  value="{{$vehicle_data->motor_number ?? ''}}" placeholder="Enter Engine Number/Motor Number" readonly>
                            </div>
                        </div>
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id"  value="{{$vehicle_data->vehicle_id ?? ''}}" placeholder="Enter Vehicle ID" >
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_number">Tax Invoice Number</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_number" id="tax_invoice_number"  value="{{$vehicle_data->tax_invoice_number ?? ''}}" placeholder="Enter Tax Invoice Number" >
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_date">Tax Invoice Date</label>
                                <input type="date" class="form-control bg-white" name="tax_invoice_date" id="tax_invoice_date"  value="{{ $vehicle_data->tax_invoice_date ? date('Y-m-d', strtotime($vehicle_data->tax_invoice_date)) : '' }}" placeholder="Enter Tax Invoice Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_value">Invoice Value/Purchase Price</label>
                                <input type="text" class="form-control bg-white" name="tax_invoice_value" id="tax_invoice_value"  value="{{$vehicle_data->tax_invoice_value ?? ''}}" onkeypress="return isNumberKeyNew(event)" placeholder="Enter Invoice Value/Purchase Price" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="tax_invoice_attachment">Tax Invoice Attachment</label>
                                <input type="file" class="form-control bg-white" name="tax_invoice_attachment" id="tax_invoice_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'tax_invoice_Image')">
                            </div>
                        </div>
                         <?php
                            // $image_src4 = !empty($vehicle_data->tax_invoice_attachment)
                            //     ? asset("EV/asset_master/tax_invoice_attachments/{$vehicle_data->tax_invoice_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                           
                                $TaxInvoiceattachment = $vehicle_data->tax_invoice_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $TaxInvoicefilePath = !empty($TaxInvoiceattachment)
                                    ? asset("EV/asset_master/tax_invoice_attachments/{$TaxInvoiceattachment}")
                                    : '';
                                   
                                    
                            
                                $isTaxInvoicePDF = $TaxInvoicefilePath && \Illuminate\Support\Str::endsWith($TaxInvoiceattachment, '.pdf');
                                $TaxInvoiceimageSrc = (!$TaxInvoicefilePath || $isTaxInvoicePDF) ? $defaultImage : $TaxInvoicefilePath;


                            ?>
                        <div class="col-md-12 mb-4 my-4">
                         <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('tax_invoice_Image')">
                                    ✖
                                </button>
                                <img id="tax_invoice_Image"
                                     src="{{ $TaxInvoiceimageSrc }}"
                                     alt="Tax Invoice Attachment"
                                     class="img-fluid rounded shadow border"
                                      style="max-height: 300px; object-fit: cover; {{ $isTaxInvoicePDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$TaxInvoiceimageSrc}}')">
                                     
                                     
                                                                              
                                <iframe id="tax_invoice_PDF"
                                         src="{{ $isTaxInvoicePDF ? $TaxInvoicefilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isTaxInvoicePDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>

                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="location">Location</label>
                                <select class="form-select custom-select2-field form-control-sm bg-white" id="location" name="location">
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->location == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city_code">City Code</label>
                                <select class="form-select custom-select2-field form-control-sm" disabled>
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->location == $val->id ? 'selected' : ''}}>{{$val->name . ' - ' . $val->city_code}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <input id="city_code" name="city_code" type="hidden"  value="{{$vehicle_data->location}}">
                        
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id">GD Hub ID Allocated</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id" id="gd_hub_id"  value="{{$vehicle_data->gd_hub_name ?? ''}}" placeholder="Enter GD Hub ID Allocated" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="gd_hub_id_exiting">GD Hub ID Existing</label>
                                <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id_exiting" id="gd_hub_id_exiting"  value="{{$vehicle_data->gd_hub_id ?? ''}}"  placeholder="Enter GD Hub ID Existing">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="financing_type">Financing Type</label>
                                <!--<input type="text" class="form-control bg-white" name="financing_type" id="financing_type"  value="{{$vehicle_data->financing_type ?? ''}}" placeholder="Financing Type" >-->
                             <select class="form-select custom-select2-field form-control-sm" name="financing_type" id="financing_type">
                                    <option value="">Select</option>
                                    @if(isset($financing_types))
                                       @foreach($financing_types as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->financing_type == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="asset_ownership">Asset Ownership</label>
                                <!--<input type="text" class="form-control bg-white" name="asset_ownership" id="asset_ownership"  value="{{$vehicle_data->asset_ownership ?? ''}}" placeholder="Enter Asset Ownership" >-->
                                
                                <select class="form-select custom-select2-field form-control-sm" name="asset_ownership" id="asset_ownership">
                                    <option value="">Select</option>
                                    @if(isset($asset_ownerships))
                                       @foreach($asset_ownerships as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->asset_ownership == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
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
                        
                        <?php
                        // $image_src = !empty($vehicle_data->master_lease_agreement)
                        //     ? asset("EV/asset_master/master_lease_agreements/{$vehicle_data->master_lease_agreement}")
                        //     : asset("admin-assets/img/defualt_upload_img.jpg");
                        
                        
                                $MasterLeaseattachment = $vehicle_data->master_lease_agreement ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $MasterLeasefilePath = !empty($MasterLeaseattachment)
                                    ? asset("EV/asset_master/master_lease_agreements/{$MasterLeaseattachment}")
                                    : '';
                                   
                                    
                            
                                $isMasterLeasePDF = $MasterLeasefilePath && \Illuminate\Support\Str::endsWith($MasterLeaseattachment, '.pdf');
                                $MasterLeaseimageSrc = (!$MasterLeasefilePath || $isMasterLeasePDF) ? $defaultImage : $MasterLeasefilePath;
                        ?>

                        
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('master_lease_Image')">
                                    ✖
                                </button>
                                <img id="master_lease_Image"
                                     src="{{ $MasterLeaseimageSrc }}"
                                     alt="Master Lease Agreement"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isMasterLeasePDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$MasterLeaseimageSrc}}')">
                                     
                                                                                                              
                                <iframe id="master_lease_PDF"
                                         src="{{ $isMasterLeasePDF ? $MasterLeasefilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isMasterLeasePDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_start_date">Lease Start Date</label>
                                <input type="date" class="form-control bg-white" name="lease_start_date" id="lease_start_date"  value="{{ $vehicle_data->lease_start_date ? date('Y-m-d', strtotime($vehicle_data->lease_start_date)) : '' }}" placeholder="Enter Lease Start Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="lease_end_date">Lease End Date</label>
                                <input type="date" class="form-control bg-white" name="lease_end_date" id="lease_end_date"  value="{{ $vehicle_data->lease_end_date ? date('Y-m-d', strtotime($vehicle_data->lease_end_date)) : '' }}" placeholder="Enter Lease End Date" >
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="emi_lease_amount">EMI/Lease Amount</label>
                                <input type="text" class="form-control bg-white" name="emi_lease_amount" id="emi_lease_amount"  value="{{$vehicle_data->emi_lease_amount ?? ''}}" onkeypress="return isNumberKeyNew(event)" placeholder="Enter EMI/Lease Amount" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation">Hypothecation</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation" id="hypothecation"  value="{{$vehicle_data->hypothecation ?? ''}}" placeholder="Enter Hypothecation" >-->
                             <select class="form-control bg-white" name="hypothecation" style="padding:12px 20px;" id="hypothecation">
                                    <option>Select</option>
                                  <option value="yes" {{ (isset($vehicle_data->hypothecation) && $vehicle_data->hypothecation == 'yes') ? 'selected' : '' }}>Yes</option>
                                <option value="no" {{ (isset($vehicle_data->hypothecation) && $vehicle_data->hypothecation == 'no') ? 'selected' : '' }}>No</option>

                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hypothecation_to">Hypothecated To</label>
                                <!--<input type="text" class="form-control bg-white" name="hypothecation_to" id="hypothecation_to"  value="{{$vehicle_data->hypothecation_to ?? ''}}" placeholder="Enter Hypothecation To" >-->
                                    <select class="form-select custom-select2-field form-control-sm" name="hypothecation_to" id="hypothecation_to">
                                    <option value="">Select</option>
                                    @if(isset($hypothecations))
                                       @foreach($hypothecations as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->hypothecation_to == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
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
                        
                        <?php
                            //  $image_hypo = !empty($vehicle_data->hypothecation_document)
                            // ? asset("EV/asset_master/hypothecation_documents/{$vehicle_data->hypothecation_document}")
                            // : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                             $Hypothecationattachment = $vehicle_data->hypothecation_document ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $HypothecationfilePath = !empty($Hypothecationattachment)
                                    ? asset("EV/asset_master/hypothecation_documents/{$Hypothecationattachment}")
                                    : '';
                                   
                                    
                            
                                $isHypothecationPDF = $HypothecationfilePath && \Illuminate\Support\Str::endsWith($Hypothecationattachment, '.pdf');
                                $HypothecationimageSrc = (!$HypothecationfilePath || $isHypothecationPDF) ? $defaultImage : $HypothecationfilePath;
                            
                            
                        ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                     <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('hypothecation_Image')">
                                    ✖
                                </button>
                                <img id="hypothecation_Image"
                                     src="{{ $HypothecationimageSrc }}"
                                     alt="Hypothecation Document"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isHypothecationPDF ? 'display: none;' : '' }}" onclick="OpenImageModal('{{$HypothecationimageSrc}}')">
                                     
                                    <iframe id="hypothecation_PDF"
                                         src="{{ $isHypothecationPDF ? $HypothecationfilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isHypothecationPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_type">Insurance Type</label>
                                <!--<input type="text" class="form-control bg-white" name="insurance_type" id="insurance_type"  value="{{$vehicle_data->insurance_type ?? ''}}" placeholder="Enter Insurance Type" >-->
                            <select class="form-select custom-select2-field form-control-sm" name="insurance_type" id="insurance_type">
                                    <option value="">Select</option>
                                    @if(isset($insurance_types))
                                       @foreach($insurance_types as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->insurance_type == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurer_name">Insurer Name</label>
                                <!--<input type="text" class="form-control bg-white" name="insurer_name" id="insurer_name"  value="{{$vehicle_data->insurer_name ?? ''}}" placeholder="Enter Insurer Name" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="insurer_name" id="insurer_name">
                                    <option value="">Select</option>
                                    @if(isset($insurer_names))
                                       @foreach($insurer_names as $val)
                                          <option value="{{$val->id}}" {{$vehicle_data->insurer_name == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        

                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_number">Insurance Number</label>
                                <input type="text" class="form-control bg-white" name="insurance_number" id="insurance_number"  value="{{$vehicle_data->insurance_number ?? ''}}" placeholder="Enter Insurance Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_start_date">Insurance Start Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_start_date" id="insurance_start_date"  value="{{ $vehicle_data->insurance_start_date ? date('Y-m-d', strtotime($vehicle_data->insurance_start_date)) : '' }}" placeholder="Enter Insurance Start Date" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_expiry_date">Insurance Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="insurance_expiry_date" id="insurance_expiry_date"  value="{{ $vehicle_data->insurance_expiry_date ? date('Y-m-d', strtotime($vehicle_data->insurance_expiry_date)) : '' }}" placeholder="Enter Insurance Expiry Date" >
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="insurance_attachment">Insurance Attachment</label>
                                <input type="file" class="form-control bg-white" name="insurance_attachment" id="insurance_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'insurance_Image')">
                            </div>
                        </div>
                        
                           <?php
                            // $image_src1 = !empty($vehicle_data->insurance_attachment)
                            //     ? asset("EV/asset_master/insurance_attachments/{$vehicle_data->insurance_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $insuranceAttachment = $vehicle_data->insurance_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $insuranceFilePath = !empty($insuranceAttachment)
                                    ? asset("EV/asset_master/insurance_attachments/{$insuranceAttachment}")
                                    : '';
                            
                                $isInsurancePDF = $insuranceFilePath && \Illuminate\Support\Str::endsWith($insuranceAttachment, '.pdf');
                                $insuranceImageSrc = (!$insuranceFilePath || $isInsurancePDF) ? $defaultImage : $insuranceFilePath;
                            ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                        <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('insurance_Image')">
                                    ✖
                                </button>
                            <img id="insurance_Image"
                                 src="{{ $insuranceImageSrc }}"
                                 alt="Insurance Attachment"
                                 class="img-fluid rounded shadow border"
                                 style="max-height: 300px; object-fit: cover; {{ $isInsurancePDF ? 'display: none;' : '' }}">
                    
                            <iframe id="insurance_PDF"
                                    src="{{ $isInsurancePDF ? $insuranceFilePath : '' }}"
                                    style="width: 100%; height: 100%; {{ !$isInsurancePDF ? 'display: none;' : '' }} border: none;"
                                    frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_type">Registration Type</label>
                                <!--<input type="text" class="form-control bg-white" name="registration_type" id="registration_type"  value="{{$vehicle_data->registration_type ?? ''}}" placeholder="Enter Registration Type" >-->
                                 <select class="form-select custom-select2-field form-control-sm" name="registration_type" id="registration_type">
                                    <option value="">Select</option>
                                    @if(isset($registration_types))
                                       @foreach($registration_types as $val)
                                         
                                          <option value="{{$val->id}}" {{$vehicle_data->registration_type == $val->id ? 'selected' : ''}}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="registration_status">Registration Status</label>
                                <input type="text" class="form-control bg-white" name="registration_status" id="registration_status"  value="{{$vehicle_data->registration_status ?? ''}}" placeholder="Enter Registration Status" >
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_number">Temporary Registration Number</label>
                                <input type="text" class="form-control bg-white" name="temporary_registration_number" id="temporary_registration_number" value="{{$vehicle_data->temproary_reg_number ?? ''}}"  placeholder="Enter Temporary Registration Number">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_date">Temporary Registration Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_date" id="temporary_registration_date" value="{{$vehicle_data->temproary_reg_date ?? ''}}"  placeholder="Enter Temporary Registration Date">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_registration_expiry_date">Temporary Registration Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="temporary_registration_expiry_date" id="temporary_registration_expiry_date" value="{{$vehicle_data->temproary_reg_expiry_date ?? ''}}"  placeholder="Enter Temporary Registration Expiry Date">
                            </div>
                        </div>
                        
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="temporary_certificate_attachment">Temporary Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="temporary_certificate_attachment" id="temporary_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'temporary_certificate_Image')">
                            </div>
                        </div>
                        
                                                
                           <?php
                            // $image_temproary = !empty($vehicle_data->temproary_reg_attachment)
                            //     ? asset("EV/asset_master/temporary_certificate_attachments/{$vehicle_data->temproary_reg_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $temporaryAttachment = $vehicle_data->temproary_reg_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $temporaryFilePath = !empty($temporaryAttachment)
                                    ? asset("EV/asset_master/temporary_certificate_attachments/{$temporaryAttachment}")
                                    : '';
                            
                                $isTemporaryPDF = $temporaryFilePath && \Illuminate\Support\Str::endsWith($temporaryAttachment, '.pdf');
                                $temporaryImageSrc = (!$temporaryFilePath || $isTemporaryPDF) ? $defaultImage : $temporaryFilePath;
                            ?>
          
                        <div class="col-md-12 mb-4 my-4">
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('temporary_certificate_Image')">
                                    ✖
                                </button>
                                <img id="temporary_certificate_Image"
                                     src="{{ $temporaryImageSrc }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isTemporaryPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $temporaryImageSrc }}')">
                        
                                <iframe id="temporary_certificate_PDF"
                                        src="{{ $isTemporaryPDF ? $temporaryFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isTemporaryPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                                                             
                                     
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_number">Permanent Registration Number</label>
                                <input type="text" class="form-control bg-white" name="permanent_reg_number" id="permanent_reg_number"  value="{{$vehicle_data->permanent_reg_number ?? ''}}" placeholder="Enter Permanent Registration Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="permanent_reg_date">Permanent Registration Date</label>
                                <input type="date" class="form-control bg-white" name="permanent_reg_date" id="permanent_reg_date"  value="{{ $vehicle_data->permanent_reg_date ? date('Y-m-d', strtotime($vehicle_data->permanent_reg_date)) : '' }}" placeholder="Enter Permanent Registration Number" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_expiry_date">Registration Certificate Expiry Date</label>
                                <input type="date" class="form-control bg-white" name="reg_certificate_expiry_date" id="reg_certificate_expiry_date"  value="{{ $vehicle_data->reg_certificate_expiry_date ? date('Y-m-d', strtotime($vehicle_data->reg_certificate_expiry_date)) : '' }}" placeholder="Enter Registration Certificate Expiry Date" >
                            </div>
                        </div>
                        
                                                
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="hsrp_certificate_attachment">HSRP Copy Attachment</label>
                                <input type="File" class="form-control bg-white" name="hsrp_certificate_attachment" id="hsrp_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hsrp_certificate_Image')">
                            </div>
                        </div>
          
                                  <?php
                            // $image_hsrp = !empty($vehicle_data->hsrp_copy_attachment)
                            //     ? asset("EV/asset_master/hsrp_certificate_attachments/{$vehicle_data->hsrp_copy_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            $hsrpAttachment = $vehicle_data->hsrp_copy_attachment ?? '';
                            $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                            $hsrpFilePath = !empty($hsrpAttachment)
                                ? asset("EV/asset_master/hsrp_certificate_attachments/{$hsrpAttachment}")
                                : '';
                        
                            $isHsrpPDF = $hsrpFilePath && \Illuminate\Support\Str::endsWith($hsrpAttachment, '.pdf');
                            $hsrpImageSrc = (!$hsrpFilePath || $isHsrpPDF) ? $defaultImage : $hsrpFilePath;
                            ?>
                        <div class="col-md-12 mb-4 my-4">
                              <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('hsrp_certificate_Image')">
                                    ✖
                                </button>
                                 <img id="hsrp_certificate_Image"
                                     src="{{ $hsrpImageSrc }}"
                                     alt="HSRP Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isHsrpPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $hsrpImageSrc }}')">
                        
                                <iframe id="hsrp_certificate_PDF"
                                        src="{{ $isHsrpPDF ? $hsrpFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isHsrpPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>

                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="reg_certificate_attachment">Registration Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="reg_certificate_attachment" id="reg_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'reg_certificate_Image')">
                            </div>
                        </div>
                        <?php
                            // $image_src2 = !empty($vehicle_data->reg_certificate_attachment)
                            //     ? asset("EV/asset_master/reg_certificate_attachments/{$vehicle_data->reg_certificate_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                                
                                
                        $regAttachment = $vehicle_data->reg_certificate_attachment ?? '';
                        $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                        $regFilePath = !empty($regAttachment)
                            ? asset("EV/asset_master/reg_certificate_attachments/{$regAttachment}")
                            : '';
                    
                        $isRegPDF = $regFilePath && \Illuminate\Support\Str::endsWith($regAttachment, '.pdf');
                        $regImageSrc = (!$regFilePath || $isRegPDF) ? $defaultImage : $regFilePath;
                                                ?>
          
                        <div class="col-md-12 mb-4 my-4">
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('reg_certificate_Image')">
                                    ✖
                                </button>
                                <img id="reg_certificate_Image"
                                     src="{{ $regImageSrc }}"
                                     alt="Registration Certificate Attachment"
                                     class="img-fluid rounded shadow border"
                                     style="max-height: 300px; object-fit: cover; {{ $isRegPDF ? 'display: none;' : '' }}"
                                     onclick="OpenImageModal('{{ $regImageSrc }}')">
                        
                                <iframe id="reg_certificate_PDF"
                                        src="{{ $isRegPDF ? $regFilePath : '' }}"
                                        style="width: 100%; height: 100%; {{ !$isRegPDF ? 'display: none;' : '' }} border: none;"
                                        frameborder="0"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_expiry_date">Fitness Certificate Expiry Date</label>
                                <input type="date" 
                                   class="form-control bg-white" 
                                   name="fc_expiry_date" 
                                   id="fc_expiry_date" 
                                   value="{{ $vehicle_data->fc_expiry_date ? date('Y-m-d', strtotime($vehicle_data->fc_expiry_date)) : '' }}" 
                                   >

                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="fc_attachment">Fitness Certificate Attachment</label>
                                <input type="File" class="form-control bg-white" name="fc_attachment" id="fc_attachment_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'fc_attachment_Image')">
                            </div>
                        </div>
                        <?php
                            // $image_src3 = !empty($vehicle_data->fc_attachment)
                            //     ? asset("EV/asset_master/fc_attachments/{$vehicle_data->fc_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            
                            
                    $fcAttachment = $vehicle_data->fc_attachment ?? '';
                    $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                    $fcFilePath = !empty($fcAttachment)
                        ? asset("EV/asset_master/fc_attachments/{$fcAttachment}")
                        : '';
                
                    $isFcPDF = $fcFilePath && \Illuminate\Support\Str::endsWith($fcAttachment, '.pdf');
                    $fcImageSrc = (!$fcFilePath || $isFcPDF) ? $defaultImage : $fcFilePath;
                            ?>
                         
                        <div class="col-md-12 mb-4 my-4">
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 10px; right: 10px; z-index: 10;" onclick="resetPreview('fc_attachment_Image')">
                                    ✖
                                </button>
                       <img id="fc_attachment_Image"
                         src="{{ $fcImageSrc }}"
                         alt="Fitness Certificate Attachment"
                         class="img-fluid rounded shadow border"
                         style="max-height: 300px; object-fit: cover; {{ $isFcPDF ? 'display: none;' : '' }}"
                         onclick="OpenImageModal('{{ $fcImageSrc }}')">
            
                    <iframe id="fc_attachment_PDF"
                            src="{{ $isFcPDF ? $fcFilePath : '' }}"
                            style="width: 100%; height: 100%; {{ !$isFcPDF ? 'display: none;' : '' }} border: none;"
                            frameborder="0"></iframe>
                                        </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="servicing_dates">Servicing Dates</label>
                                <input type="text" class="form-control bg-white" name="servicing_dates" id="servicing_dates"   value="{{$vehicle_data->servicing_dates ?? ''}}" placeholder="Enter Servicing Dates">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                          <div class="form-group">
                            <label class="input-label mb-2 ms-1 d-block">Road Tax Applicable</label>
                            <select class="form-control bg-white" name="road_tax_applicable" id="road_tax_applicable">
                             <option value="yes" {{ (isset($vehicle_data->road_tax_applicable) && $vehicle_data->road_tax_applicable == 'yes') ? 'selected' : '' }}>Yes</option>
                             <option value="no" {{ (isset($vehicle_data->road_tax_applicable) && $vehicle_data->road_tax_applicable == 'no') ? 'selected' : '' }}>No</option>
                            </select>
                          </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_amount">Road Tax Amount</label>
                                <input type="text" class="form-control bg-white" name="road_tax_amount" id="road_tax_amount"  value="{{$vehicle_data->road_tax_amount ?? ''}}" placeholder="Enter Road Tax Amount">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="road_tax_renewal_frequency">Road Tax Renewal Frequency</label>
                                <input type="text" class="form-control bg-white" name="road_tax_renewal_frequency" id="road_tax_renewal_frequency"  value="{{$vehicle_data->road_tax_renewal_frequency ?? ''}}"  placeholder="Enter Road Tax Frequency">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="next_renewal_date">If Yes Road Tax Next Renewal Date</label>
                                <input type="date" class="form-control bg-white" name="next_renewal_date" id="next_renewal_date"  value="{{$vehicle_data->road_tax_next_renewal_date ?? ''}}" placeholder="Enter If Yes Road Tax Next Renewal Date">
                            </div>
                        </div>
                     <div class="col-md-6 mb-3">
                         </div>
                     
                     
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_type">Battery Type</label>
                           
                                    <select name="battery_type" id="battery_type" class="form-control bg-white">
                                    <option value="">Select</option>
                                    <option value="1" {{ (isset($vehicle_data->battery_type) && $vehicle_data->battery_type == 1) ? 'selected' : '' }}>Self-Charging</option>
                                    <option value="2" {{ (isset($vehicle_data->battery_type) && $vehicle_data->battery_type == 2) ? 'selected' : '' }}>Portable</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3" hidden>
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_variant_name">Battery Variant Name</label>
                                <input type="text" class="form-control bg-white" name="battery_variant_name" id="battery_variant_name"  value="{{$vehicle_data->battery_variant_name ?? ''}}" placeholder="Enter Battery Variant Name" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no">Battery Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no" id="battery_serial_no"  value="{{$vehicle_data->battery_serial_no ?? ''}}" placeholder="Enter Battery Serial Number - Original" readonly>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement1">Battery Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement1" id="battery_serial_no_replacement1"  value="{{$vehicle_data->battery_serial_number1 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement2">Battery Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement2" id="battery_serial_no_replacement2"  value="{{$vehicle_data->battery_serial_number2 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement3">Battery Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement3" id="battery_serial_no_replacement3"  value="{{$vehicle_data->battery_serial_number3 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement4">Battery Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement4" id="battery_serial_no_replacement4"  value="{{$vehicle_data->battery_serial_number4 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement5">Battery Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="battery_serial_no_replacement5" id="battery_serial_no_replacement5"  value="{{$vehicle_data->battery_serial_number5 ?? ''}}" placeholder="Enter Battery Serial Number - Replacement 5">
                            </div>
                        </div>
                     
                         <div class="col-md-6 mb-3">
                             </div>
                             
                             
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_variant_name">Charger Variant Name</label>
                                <!--<input type="text" class="form-control bg-white" name="charger_variant_name" id="charger_variant_name"  value="{{$vehicle_data->charger_variant_name ?? ''}}" placeholder="Enter Charger Variant Name" >-->
                                  <select name="charger_variant_name" id="charger_variant_name" class="form-control bg-white">
                                    <option value="">Select</option>
                                   <option value="ABC" {{ (isset($vehicle_data->charger_variant_name) && $vehicle_data->charger_variant_name == 'ABC') ? 'selected' : '' }}>ABC</option>
                                    <option value="XYZ" {{ (isset($vehicle_data->charger_variant_name) && $vehicle_data->charger_variant_name == 'XYZ') ? 'selected' : '' }}>XYZ</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no">Charger Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no" id="charger_serial_no"  value="{{$vehicle_data->charger_serial_no ?? ''}}" placeholder="Enter Charger Serial Number - Original" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement1">Charger Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement1" id="charger_serial_no_replacement1" value="{{$vehicle_data->charger_serial_number1 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement2">Charger Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement2" id="charger_serial_no_replacement2"  value="{{$vehicle_data->charger_serial_number2 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement3">Charger Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement3" id="charger_serial_no_replacement3"  value="{{$vehicle_data->charger_serial_number3 ?? ''}}"  placeholder="Enter Charger Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement4">Charger Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement4" id="charger_serial_no_replacement4"  value="{{$vehicle_data->charger_serial_number4 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement5">Charger Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="charger_serial_no_replacement5" id="charger_serial_no_replacement5"  value="{{$vehicle_data->charger_serial_number5 ?? ''}}" placeholder="Enter Charger Serial Number - Replacement 5">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_variant_name">Telematics Variant Name</label>
                                <input type="text" class="form-control bg-white" name="telematics_variant_name" id="telematics_variant_name"  value="{{$vehicle_data->telematics_variant_name ?? ''}}" placeholder="Enter Telematics Variant Name" >
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_oem">Telematics OEM</label>
                                <select class="form-select custom-select2-field form-control-sm" name="telematics_oem" id="telematics_oem">
                                    <option value="">Select</option>
                                    @if(isset($telematics))
                                       @foreach($telematics as $val)
                                          <option value="{{$val->id}}" {{ (isset($vehicle_data->telematics_oem) && $vehicle_data->telematics_oem == $val->id) ? 'selected' : '' }} >{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no"> Telematics Serial Number - Original</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no" id="telematics_serial_no"  value="{{$vehicle_data->telematics_serial_no ?? ''}}" placeholder="Enter Telematics Serial Number - Original" readonly>
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_imei_no"> Telematics IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="telematics_imei_no" id="telematics_imei_no"  value="{{$vehicle_data->telematics_imei_number ?? ''}}" placeholder="Enter Telematics IMEI Number">
                            </div>
                        </div>
                        
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement1"> Telematics Serial Number - Replacement 1</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement1" id="telematics_serial_no_replacement1"  value="{{$vehicle_data->telematics_serial_number1 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 1">
                            </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement2"> Telematics Serial Number - Replacement 2</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement2" id="telematics_serial_no_replacement2"  value="{{$vehicle_data->telematics_serial_number2 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 2">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement3"> Telematics Serial Number - Replacement 3</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement3" id="telematics_serial_no_replacement3"  value="{{$vehicle_data->telematics_serial_number3 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 3">
                            </div>
                        </div>
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement4"> Telematics Serial Number - Replacement 4</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement4" id="telematics_serial_no_replacement4"  value="{{$vehicle_data->telematics_serial_number4 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 4">
                            </div>
                        </div>
                        
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement5"> Telematics Serial Number - Replacement 5</label>
                                <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement5" id="telematics_serial_no_replacement5"  value="{{$vehicle_data->telematics_serial_number5 ?? ''}}" placeholder="Enter  Telematics Serial Number - Replacement 5">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client">Client Name</label>
                                <input type="text" class="form-control bg-white" name="client" id="client"  value="{{ optional($vehicle_data->customer_relation)->name ?? $vehicle_data->client ?? '' }}" placeholder="Enter Client" >
                            </div>
                        </div>
                        
                        
                                                
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                <input type="date" class="form-control bg-white" name="vehicle_delivery_date" id="vehicle_delivery_date"  value="{{ $vehicle_data->vehicle_delivery_date ? date('Y-m-d', strtotime($vehicle_data->vehicle_delivery_date)) : '' }}" placeholder="Enter Vehicle Delivery Date" >
                            </div>
                        </div>
                        
                        
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                                <!--<input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="{{$vehicle_data->vehicle_status ?? ''}}" placeholder="Enter Vehicle Status" >-->
                                
                                <select class="form-select custom-select2-field form-control-sm" name="vehicle_status" id="vehicle_status">
                                    <option value="">Select</option>
                                    @if(isset($inventory_locations))
                                       @foreach($inventory_locations as $val)
                                         
                                          <option value="{{$val->id}}" {{ (isset($vehicle_data->vehicle_status) && $vehicle_data->vehicle_status == $val->id) ? 'selected' : '' }} >{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                        
                        
                        @if($vehicle_data->is_status == 'uploaded')
                        <div class="col-12 text-end gap-4 d-block" id="status_update_btn_section">
                            <button type="button" class="btn btn-danger px-5"
                                onclick="VehicleAcceptOrRejectStatus('{{ route('admin.asset_management.asset_master.vehicle_status_update') }}','{{$vehicle_data->id}}', 'You want to Reject this Asset Master Vehicle', 'rejected')">
                                Reject
                            </button>
                        
                            <button type="submit" id="submitBtn" class="btn btn-success px-5"
                                onclick="VehicleAcceptOrRejectStatus('{{ route('admin.asset_management.asset_master.vehicle_status_update') }}','{{$vehicle_data->id}}', 'You want to Accept this Asset Master Vehicle', 'accepted')">
                                Approve
                            </button>
                        </div>
                        @elseif($vehicle_data->is_status == 'pending')
                            <div class="col-12 text-end gap-4 d-block" id="">
                            <button type="reset" class="btn btn-danger px-5">
                                Reset
                            </button>
                            
                            <button type="button" id="UpdateVehicle" class="btn btn-success px-5">
                                Update
                            </button>
                        </div>
                        @endif
               
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
        
        function showImagePreview(input, imageElementID) {
    const file = input.files[0];
    const previewImg = document.getElementById(imageElementID);
    const previewPDF = document.getElementById(imageElementID.replace('Image', 'PDF'));

    if (file) {
        const fileType = file.type;

        if (fileType.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = "block";
                previewPDF.style.display = "none";
                previewImg.setAttribute('onclick', `OpenImageModal('${e.target.result}')`);
            };
            reader.readAsDataURL(file);
        } else if (fileType === 'application/pdf') {
            const pdfUrl = URL.createObjectURL(file);
            previewPDF.src = pdfUrl;
            previewPDF.style.display = "block";
            previewImg.style.display = "none";
            previewImg.removeAttribute('onclick');
        }
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
    
    // $("#ApproveAssetMasterVehicleForm").submit(function(e) {
        
    //     e.preventDefault();
    
    //     var form = $(this)[0];
    //     var formData = new FormData(form);
    //     formData.append("_token", "{{ csrf_token() }}");
    
    //     var $submitBtn = $("#submitBtn");
    //     var originalText = $submitBtn.html();
    //     $submitBtn.prop("disabled", true).html("⏳ Submitting...");
    
    //     $.ajax({
    //         url: "{{ route('admin.asset_management.asset_master.store_vehicle') }}",
    //         type: "POST",
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(response) {
    
    //             $submitBtn.prop("disabled", false).html(originalText);
    
    //             if (response.success) {
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Updated!',
    //                     text: response.message,
    //                     timer: 1500,
    //                     showConfirmButton: false
    //                 }).then(() => {
    //                     window.location.href="{{route('admin.asset_management.asset_master.list')}}";
    //                 });
    //             } else {
    //                 Swal.fire({
    //                     icon: 'warning',
    //                     title: 'Warning!',
    //                     text: response.message,
    //                     confirmButtonText: 'OK'
    //                 });
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             $submitBtn.prop("disabled", false).html(originalText);
    //             if (xhr.status === 422) {
    //                 var errors = xhr.responseJSON.errors;
    //                 $.each(errors, function(key, value) {
    //                     toastr.error(value[0]);
    //                 });
    //             } else {
    //                 toastr.error("Please try again.");
    //             }
    //         }
    //     });
    // });


// Trigger this ONLY on button click, not on form submit
$("#UpdateVehicle").on("click", function(e) {
    e.preventDefault();

    var form = $("#ApproveAssetMasterVehicleForm")[0];
    var formData = new FormData(form);
    formData.append("_token", "{{ csrf_token() }}");

    const id = `{{$vehicle_data->id}}`; // Blade will render the ID value here
    formData.append('id', id); // Append the ID explicitly to the formData

    var $submitBtn = $("#UpdateVehicle");
    var originalText = $submitBtn.html();
    $submitBtn.prop("disabled", true).html("⏳ Updating...");

    $.ajax({
        url: "{{ route('admin.asset_management.asset_master.update') }}",
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
                    window.location.href = "{{ route('admin.asset_management.asset_master.list') }}";
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


 function VehicleAcceptOrRejectStatus(route, id, message, status, title = "Are you sure?") {
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
                            $.ajax({
                                url: route,
                                type: "POST",
                                data: {
                                    id: id,
                                    status: status,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                               success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end', 
                                            icon: 'success',
                                            title: response.message,
                                            showConfirmButton: false,
                                            showCloseButton: true,
                                            timer: false, 
                                        });
                                        $("#status_update_btn_section").addClass('d-none').removeClass('d-block');
                                         setTimeout(function() {
                                        window.location.href = redirect;
                                    }, 1000);
                                    
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
                                        $("#status_update_btn_section").addClass('d-block').removeClass('d-none');
                                    }
                                },

                                error: function() {
                                    Swal.fire("Error!",
                                        "The network connection has failed. Please try again later",
                                        "error");
                                    $("#status_update_btn_section").addClass('d-block').removeClass('d-none');
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
                                Swal.showValidationMessage('Reject Reason are required');
                            }
                            return remarks;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var Remarks = result.value;
                            $.ajax({
                                url: route,
                                type: "POST",
                                data: {
                                    id: id,
                                    status: status,
                                    remarks: Remarks,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Rejected! ' + response.message,
                                        showConfirmButton: false,
                                        showCloseButton: true,
                                        timer: false
                                    });
                            
                                    setTimeout(function() {
                                        window.location.href = redirect;
                                    }, 1000);
                                } else {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'error',
                                        title: 'Warning! ' + response.message,
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
