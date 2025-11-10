    @extends('layouts.b2b')
@section('css')

<style>
.attachment-preview {
    border: 1px dashed #ccc;
    border-radius: 8px;
    height: 300px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    position: relative;
    background-color: #fdfdfd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.attachment-preview:hover {
    border-color: #007bff;
    background-color: #f9f9f9;
}

.preview-image {
    width: 80%;
    height: 80%;
    /*object-fit: contain;*/
    cursor: pointer;
    transition: transform 0.2s;
    border-radius: 4px;
}

.preview-image:hover {
    transform: scale(1.02);
}

.preview-pdf {
    width: 100%;
    height: 100%;
    border: none;
}

.d-none {
    display: none !important;
}

</style>
@endsection

@section('content')
<div class="main-content">


        <!-- Header Section -->
        <div class="mb-4">
            <div class="p-3 rounded" style="background:#fbfbfb;">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <!-- Title -->
                    <h5 class="m-0 text-truncate custom-dark">
                        View Vehicle Details
                    </h5>
                    
        
                    <!-- Back Button -->
                    <a href="{{ route('b2b.vehiclelist') }}" 
                       class="btn btn-dark btn-md mt-2 mt-md-0">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

            
            <div class="card">
               <!--<div class="card-header" style="background:#eef2ff;">-->
               <!--      <h5 style="color:#1e3a8a;" class="fw-bold">Asset details</h5>-->
               <!--      <p class="mb-0" style="color:#1e3a8a;">Asset in details</p>-->
               <!--  </div>-->
                <div class="card-body">
                    <div class="row">
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                                <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id" value="{{$data->vehicle->vehicle_id ?? ''}}" placeholder="Vehicle ID" readonly>
                            </div>
                        </div>
                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_number">Vehicle No</label>
                                <input type="text" class="form-control bg-white" name="vehicle_number" id="vehicle_number" value="{{$data->vehicle->permanent_reg_number ?? ''}}"  placeholder="Vehicle NO" readonly>
                            </div>
                        </div>



                          <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number</label>
                                <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{$data->vehicle->chassis_number ?? ''}}" placeholder="Chassis Number" readonly>
                            </div>
                        </div>
                        
                        
                       <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="imei_number">IMEI Number</label>
                                <input type="text" class="form-control bg-white" name="imei_number" id="imei_number" value="{{$data->vehicle->telematics_imei_number ?? ''}}" placeholder="IMEI Number" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                                <input type="text" class="form-control bg-white" name="motor_number" id="motor_number" value="{{$data->vehicle->motor_number ?? ''}}"  placeholder="Engine Number/Motor Number" readonly>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="city">City</label>
                                <!--<input type="text" class="form-control bg-white" name="city" id="city"  placeholder="City">-->
                             <select class="form-select custom-select2-field form-control-sm" id="city" name="city" disabled>
                                    <option value="">Select</option>
                                    @if(isset($locations))
                                       @foreach($locations as $location)
                                          <option value="{{$location->id}}" {{ isset($data->vehicle->quality_check->location) && $data->vehicle->quality_check->location == $location->id ? 'selected' : '' }}>{{$location->city_name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                                
                            </div>
                        </div>
                        
                        
                        <!--<div class="col-md-6 mb-3">-->
                        <!--        <div class="form-group">-->
                        <!--            <label class="input-label mb-2 ms-1" for="hub">HUB</label>-->
                        <!--            <input type="text" class="form-control bg-white" name="hub" id="hub" style="padding:12px 20px;"  placeholder="HUB">-->
                        <!--        </div>-->
                        <!--</div>-->
                        
                        
                                                
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                                <select class="form-select custom-select2-field form-control-sm" id="vehicle_type" name="vehicle_type" disabled>
                                    <option value="">Select</option>
                                    @if(isset($vehicle_types))
                                       @foreach($vehicle_types as $type)
                                          <option value="{{$type->id}}" {{ isset($data->vehicle->vehicle_type) && $data->vehicle->vehicle_type == $type->id ? 'selected' : '' }}>{{$type->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="make">Make</label>
                                    <input type="text" class="form-control bg-white" name="make" id="make" value="{{$data->vehicle->vehicle_model_relation->make ?? ''}}" placeholder="Make" readonly>
                                </div>
                        </div>
                        
                        
                      <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="model">Model</label>
                                    <input type="text" class="form-control bg-white" name="model" id="model" value="{{$data->vehicle->vehicle_model_relation->vehicle_model ?? ''}}" placeholder="Model" readonly>
                                </div>
                        </div>
                        
                     <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="color">Color</label>
                                    <input type="text" class="form-control bg-white" name="color" id="color"  placeholder="Color" value="{{$data->vehicle->color_relation->name ?? ''}}" readonly>
                                </div>
                        </div>
                        
                        
                                <?php
                        
                                $RCAttachment = $data->vehicle->reg_certificate_attachment ?? '';
                                $defaultImage = asset('b2b/img/default_image.png');
                                $RCFilePath = !empty($RCAttachment)
                                    ? asset("EV/asset_master/reg_certificate_attachments/{$RCAttachment}")
                                    : '';
                            
                                $isRCPDF = $RCFilePath && \Illuminate\Support\Str::endsWith($RCAttachment, '.pdf');
                                $RCImageSrc = (!$RCFilePath || $isRCPDF) ? $defaultImage : $RCFilePath;
                            ?>
                        
                        
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Registration Certificate Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center">
                                        <!-- Image Preview -->
                                        <img id="Rc_Image"
                                             src="{{ $RCImageSrc }}"
                                             alt="Registration Certificate Attachment"
                                             class="preview-image {{ $isRCPDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $RCImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="Rc_PDF"
                                                src="{{ $isRCPDF ? $RCFilePath : '' }}"
                                                class="preview-pdf {{ !$isRCPDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                                
                                      @if($isRCPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $RCFilePath }}')">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                            
                            
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="insurance_type">Insurance Type</label>
                                    <input type="text" class="form-control bg-white" name="insurance_type" id="insurance_type"  placeholder="Insurance Type" value="{{$data->vehicle->insurer_type_relation->name ?? ''}}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="insurer_name">Insurer Name</label>
                                    <input type="text" class="form-control bg-white" name="insurer_name" id="insurer_name"  placeholder="Insurer Name" value="{{$data->vehicle->insurer_name_relation->name ?? ''}}" readonly>
                                </div>
                            </div>
                            
                            
                             <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="insurance_number">Insurance Number</label>
                                    <input type="text" class="form-control bg-white" name="insurance_number" id="insurance_number"  placeholder="Insurance Number" value="{{$data->vehicle->insurance_number ?? ''}}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="insurance_start_date">Insurance Start Date</label>
                                    <input type="date" class="form-control bg-white" name="insurance_start_date" id="insurance_start_date"  value="{{$data->vehicle->insurance_start_date ?? ''}}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="insurance_end_date">Insurance Expiry Date</label>
                                    <input type="date" class="form-control bg-white" name="insurance_end_date" id="insurance_end_date"  value="{{$data->vehicle->insurance_expiry_date ?? ''}}" readonly>
                                </div>
                            </div>
                            
                        <?php
                        
                                $insuranceAttachment = $data->vehicle->insurance_attachment ?? '';
                                $defaultImage = asset('b2b/img/default_image.png');
                                $insuranceFilePath = !empty($insuranceAttachment)
                                    ? asset("EV/asset_master/insurance_attachments/{$insuranceAttachment}")
                                    : '';
                            
                                $isInsurancePDF = $insuranceFilePath && \Illuminate\Support\Str::endsWith($insuranceAttachment, '.pdf');
                                $insuranceImageSrc = (!$insuranceFilePath || $isInsurancePDF) ? $defaultImage : $insuranceFilePath;
                            ?>
                        
                        
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Insurance Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center">
                                        <!-- Image Preview -->
                                        <img id="insurance_Image"
                                             src="{{ $insuranceImageSrc }}"
                                             alt="Insurance Attachment"
                                             class="preview-image {{ $isInsurancePDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $insuranceImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="insurance_PDF"
                                                src="{{ $isInsurancePDF ? $insuranceFilePath : '' }}"
                                                class="preview-pdf {{ !$isInsurancePDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                      @if($isInsurancePDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $insuranceFilePath }}')">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        
                        
                        @php
                            $hsrpAttachment = $data->vehicle->hsrp_copy_attachment ?? '';
                            $defaultImage = asset('b2b/img/default_image.png');
                            $hsrpFilePath = $hsrpAttachment
                                ? asset("EV/asset_master/hsrp_certificate_attachments/{$hsrpAttachment}")
                                : '';
                            $isHsrpPDF = $hsrpAttachment && \Illuminate\Support\Str::endsWith($hsrpAttachment, '.pdf');
                            $hsrpImageSrc = (!$hsrpFilePath || $isHsrpPDF) ? $defaultImage : $hsrpFilePath;
                        @endphp
                        
                        <div class="col-md-12 mb-4 my-4">
                            <label class="mb-2">HSRP Copy Attachment</label>
                            <div class="attachment-preview">
                                <div class="col-12 text-center">
                                    <!-- Image Preview -->
                                    <img id="hsrp_certificate_Image"
                                         src="{{ $hsrpImageSrc }}"
                                         alt="HSRP Certificate Attachment"
                                         class="preview-image {{ $isHsrpPDF ? 'd-none' : '' }}"
                                         onclick="OpenImageModal('{{ $hsrpImageSrc }}')">
                        
                                    <!-- PDF Preview -->
                                    <iframe id="hsrp_certificate_PDF"
                                            src="{{ $isHsrpPDF ? $hsrpFilePath : '' }}"
                                            class="preview-pdf {{ !$isHsrpPDF ? 'd-none' : '' }}"
                                            frameborder="0"></iframe>
                                            
                                      @if($isHsrpPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $hsrpFilePath }}')">
                                            </div>
                                        @endif
                                </div>
                            </div>

                        </div>

                        
                        <?php
                            
                        $fcAttachment = $data->vehicle->fc_attachment ?? '';
                        $defaultImage = asset('b2b/img/default_image.png');
                        $fcFilePath = !empty($fcAttachment)
                            ? asset("EV/asset_master/fc_attachments/{$fcAttachment}")
                            : '';
                    
                        $isFcPDF = $fcFilePath && \Illuminate\Support\Str::endsWith($fcAttachment, '.pdf');
                        $fcImageSrc = (!$fcFilePath || $isFcPDF) ? $defaultImage : $fcFilePath;
                            ?>
                         
                            <div class="col-md-12 mb-4 my-4">
                                <label class="mb-2">Fitness Certificate (FC) Attachment</label>
                                <div class="attachment-preview">
                                    <div class="col-12 text-center">
                                        <!-- Image Preview -->
                                        <img id="fc_attachment_Image"
                                             src="{{ $fcImageSrc }}"
                                             alt="Fitness Certificate Attachment"
                                             class="preview-image {{ $isFcPDF ? 'd-none' : '' }}"
                                             onclick="OpenImageModal('{{ $fcImageSrc }}')">
                            
                                        <!-- PDF Preview -->
                                        <iframe id="fc_attachment_PDF"
                                                src="{{ $isFcPDF ? $fcFilePath : '' }}"
                                                class="preview-pdf {{ !$isFcPDF ? 'd-none' : '' }}"
                                                frameborder="0"></iframe>
                                                
                                                
                                      @if($isFcPDF)
                                            <!-- Only for PDF -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                 style="cursor: pointer; background: transparent;"
                                                 onclick="OpenImageModal('{{ $fcFilePath }}')">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        
                        
                        <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="client_name">Client Name</label>
                                    <input type="text" class="form-control bg-white" name="client_name" id="client_name" value="{{$data->vehicle->quality_check->customer_relation->trade_name ?? ''}}"  placeholder="Client Name" readonly>
                                </div>
                        </div>
                        
                        
                                                
                        <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                                    <input type="date" class="form-control bg-white" name="vehicle_delivery_date" value="{{$data->vehicle->vehicle_delivery_date ?? ''}}" id="vehicle_delivery_date" readonly>
                                </div>
                        </div>
                        <?php 
                        // dd($inventory_data);
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                                <!--<input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status"  value="" placeholder="Enter Vehicle Status">-->
                                <select class="form-select custom-select2-field form-control-sm" name="vehicle_status" id="vehicle_status" disabled>
                                    <option value="">Select</option>
                                    @if(isset($inventory_locations))
                                       @foreach($inventory_locations as $val)
                                          <option value="{{$val->id}}" {{ isset($inventory_data->transfer_status) && $inventory_data->transfer_status == $val->id ? 'selected' : '' }}>{{$val->name}}</option>
                                       @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        
                    </div>
                    
            </div>
            </div>
            
    @include('b2b::action_popup_modal') 
               




</div>

    

@endsection

@section('js')

    <script>
let scale = 1;
let rotation = 0;
let currentFileUrl = '';
let currentType = ''; 

function OpenImageModal(fileUrl) {
    currentFileUrl = fileUrl;
    const isPDF = fileUrl.toLowerCase().endsWith('.pdf');

    scale = 1;
    rotation = 0;
    updateImageTransform();

    if (isPDF) {
        $("#kyc_image").hide();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").hide(); // Hide image tools for PDF
        $("#kyc_pdf").attr("src", fileUrl).show();
        currentType = 'pdf';
    } else {
        $("#kyc_pdf").hide();
        $("#kyc_image").attr("src", fileUrl).show();
        $("#rotateBtn, #zoomInBtn, #zoomOutBtn").show();
        currentType = 'image';
    }

    $("#downloadBtn").off("click").on("click", function () {
        const link = document.createElement("a");
        link.href = currentFileUrl;
        link.download = currentFileUrl.split('/').pop();
        link.click();
    });

    $("#BKYC_Verify_view_modal").modal("show");
}

function zoomIn() {
    if (currentType !== 'image') return;
    scale += 0.1;
    updateImageTransform();
}

function zoomOut() {
    if (currentType !== 'image') return;
    if (scale > 0.2) {
        scale -= 0.1;
        updateImageTransform();
    }
}

function rotateImage() {
    if (currentType !== 'image') return;
    rotation = (rotation + 90) % 360;
    updateImageTransform();
}

function updateImageTransform() {
    const img = document.getElementById("kyc_image");
    if (img) {
        img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
    }
}
    </script>




@endsection
