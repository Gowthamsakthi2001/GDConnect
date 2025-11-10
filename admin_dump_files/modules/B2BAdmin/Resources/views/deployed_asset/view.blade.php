<x-app-layout>
    
<style>
    /* Custom tab styles */
        table thead th{
        background: white !important;
        color: #4b5563 !important;
    }
    .custom-tabs .nav-link {
        border: none;
        color: #6c757d; /* default gray */
        font-weight: 500;
    }

    .custom-tabs .nav-link.active {
        color: #28a745; /* green */
        border-bottom: 2px solid #28a745; /* green underline */
        background-color: transparent;
    }

    .custom-tabs .nav-link:hover {
        color: #28a745;
    }
    
        .file-preview-container {
            border-radius:8px;
            margin-top:8px;
            border: 2px dotted #ccc;
            padding: 0;
            height: 220px;
            width: 100%;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
                .vehicle-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            padding: 12px 16px;
        }

        .vehicle-info img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        .vehicle-details h6 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .vehicle-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .vehicle-meta span {
            font-size: 14px;
            color: #1a1a1a;
        }

        .dot-separator {
            width: 5px;
            height: 5px;
            background: #1a1a1a;
            border-radius: 50%;
        }

        .back-btn {
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
        }

        .nav-tabs {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            padding: 12px 16px 0;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #1a1a1a;
            font-weight: 600;
            font-size: 16px;
            padding: 10px 10px 12px 0;
            margin-right: 24px;
            background: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #12ae3a;
            border-bottom: 1px solid #12ae3a;
        }

        .activity-logs-btn {
            background: rgba(18,174,58,0.1);
            color: #12ae3a;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .filter-section {
            padding: 16px;
            background: white;
        }

        .filter-section h5 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .date-picker {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 16px;
            width: 200px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            color: #1a1a1a;
        }

        .status-columns {
            display: flex;
            gap: 16px;
            padding: 16px;
            overflow-x: auto;
            min-height: 650px;
        }

        .status-column {
            min-width: 376px;
            background: white;
            border-right: 1px solid rgba(0,0,0,0.07);
        }

        .status-header {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-header.pending {
            background: #ffbebe;
            color: #a61d1d;
            border: 0.8px solid #a61d1d;
        }

        .status-header.assigned {
            background: #d8e4fe;
            color: #2563eb;
            border: 0.8px solid #2563eb;
        }

        .status-header.in-progress {
            background: #f0d8fe;
            color: #7e25eb;
            border: 0.8px solid #7e25eb;
        }

        .status-header.hold {
            background: #fef5d8;
            color: #947b14;
            border: 0.8px solid #947b14;
        }

        .status-header.closed {
            background: #d4efdf;
            color: #005d27;
            border: 0.8px solid #005d27;
        }

        .count-badge {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 5px;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: black;
        }

        .cards-container {
            padding: 24px 16px;
            height: 524px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .service-card {
            background: white;
            border: 1px solid rgba(26,26,26,0.5);
            border-radius: 8px;
            padding: 12px 16px;
            width: 344px;
        }

        .card-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #1a1a1a;
        }

        .card-row:last-child {
            margin-bottom: 0;
        }

        .card-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .scrollable-content {
            max-height: 100vh;
            overflow: auto;
        }

        /* Custom scrollbar */
        .cards-container::-webkit-scrollbar {
            width: 6px;
        }

        .cards-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .cards-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .status-columns::-webkit-scrollbar {
            height: 6px;
        }

        .status-columns::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .status-columns::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .status-columns::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
            
.timeline-wrapper {
    position: relative;
    margin-left: 40px;
    padding-left: 30px;
}
.timeline-wrapper::before {
    content: "";
    position: absolute;
    top: 0;
    left: 10px;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #4cafef, #28a745, #ff9800, #f44336);
    border-radius: 2px;
    animation: growLine 1.5s ease-in-out forwards;
}
@keyframes growLine {
    from { height: 0; }
    to { height: 100%; }
}

.timeline-step {
    position: relative;
    margin-bottom: 40px;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.8s forwards;
}
@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.timeline-icon {
    position: absolute;
    left: -40px;
    top: 0;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 20px;
    color: #fff;
    animation: bounceIn 1s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
@keyframes bounceIn {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.1); opacity: 1; }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); }
}

.timeline-content {
    background: #fff;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.timeline-content:hover {
    transform: translateX(10px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    border-left: 4px solid #28a745;
}

/* Progressive colors */
.step-0 { background: #4cafef; }
.step-1 { background: #28a745; }
.step-2 { background: #ff9800; }
.step-3 { background: #f44336; }


        
            /* ---------- Pagination container: force single-line flex layout ---------- */
    .dataTables_wrapper .dataTables_paginate {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-end; /* change to center if you want it centered */
      flex-wrap: nowrap;
      white-space: nowrap;
      margin-top: 12px;
    }
    
    /* Style Previous / Next only */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 100px;   /* ðŸ”¹ make both equal width */
      height: 40px;
      padding: 8px 16px;  /* ðŸ”¹ more padding for balanced look */
      border-radius: 6px;
      border: none;
      color: #fff !important;
      background-color: #0d6efd;
      cursor: pointer;
      font-weight: 500;
    }
    
    /* Hover */
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
      background-color: #0b5ed7;
    }
    
    /* Disabled */
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
      background-color: #e9ecef;
      color: #6c757d !important;
      cursor: not-allowed;
    }
</style>

    <link rel="stylesheet" href="{{asset('public/EV/css/service_page.css')}}"/>

    <div class="main-content">
        
    <div id="AssetSection">
       
        <div class="card my-2">
            <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-10 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>
                                
                            

                                
                        <img src="{{asset('admin-assets/img/default_bike_img.png')}}" alt="Profile" width="80" height="80" style=" border-radius: 50%; object-fit: cover; border: 2px solid #f0f0f0; padding: 2px; ">
                            
                            </div>
                            <div class="px-3">
                                <div class="h4 fw-bold mt-2" style=" color: black; ">{{ $data->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A' }}</div>
                               <div class="small text-secondary d-flex flex-wrap align-items-center">
                                  <span class="me-2">Chassis No: {{$data->vehicle->chassis_number ??''}}</span>
                                  <span class="me-2">• Vehicle Type: {{$data->vehicle->vehicle_type_relation->name ??''}}</span>
                                  <span class="me-2">• Vehicle No: {{$data->vehicle->permanent_reg_number ??''}}</span>
                                  <span class="me-2">• Color: {{$data->vehicle->vehicle_model_relation->color ??''}}</span>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">

                            <a href="{{route('b2b.admin.deployed_asset.list')}}" class="btn btn-dark  px-5">Back</a>
                            

                        </div>
                    </div>

                </div>
                
                        <!-- Tabs -->
                <ul class="nav nav-tabs custom-tabs mt-3 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#vehicle-info">Vehicle Info</a>
                    </li>
                    
                @if(isset($data) && !in_array($data->status, ['returned','return_request']))
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#client-info">Client Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rider-info">Rider Info</a>
                    </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#service-records">Service Records</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#accident-records" data-accident-url="{{ isset($data) ? route('b2b.admin.deployed_asset.accident_list', $data->id) : '' }}">Accident Records</a>
                    </li>
                    <li class="nav-item ms-auto">
                        <a class="btn btn-success btn-sm px-3" href="#activity-logs">Activity Logs</a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="tab-content">
            <div class="tab-pane fade show active" id="vehicle-info">
    <div class="card">
        <div class="card-body">
            <form id="StoreAssetMasterVehicleForm" action="javascript:void(0);" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group"> 
                            <label class="input-label mb-2 ms-1" for="chassis_number">Chassis Number (VIN)</label>
                            <input type="text" class="form-control bg-white" name="chassis_number" id="chassis_number" value="{{ $data->vehicle?->chassis_number ?? null }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_category">Vehicle Category</label>
                            <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="vehicle_category" id="vehicle_category" value="{{ ($data->vehicle?->vehicle_category == 'regular_vehicle')?'Regular Vehicle':(($data->vehicle?->vehicle_category == 'low_speed_vehicle')?'Low Speed Vehicle':'') }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_type">Vehicle Type</label>
                            <input type="text" class="form-control bg-white" name="vehicle_type" id="vehicle_type" value="{{ $data->vehicle?->vehicle_type_relation->name ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="model">Model</label>
                            <input type="text" class="form-control bg-white" name="model" id="model" value="{{ $data->vehicle?->vehicle_model_relation->vehicle_model ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="make">Make</label>
                            <input type="text" class="form-control bg-white" style="padding: 12px 20px;" name="make" id="make" value="{{ $data->vehicle?->vehicle_model_relation->make ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="variant">Variant</label>
                            <input type="text" class="form-control bg-white" name="variant" id="variant" value="{{ $data->vehicle?->vehicle_model_relation->variant ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="color">Color</label>
                            <input type="text" class="form-control bg-white" name="color" id="color" value="{{ $data->vehicle?->vehicle_model_relation->color ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="motor_number">Engine Number/Motor Number</label>
                            <input type="text" class="form-control bg-white" name="motor_number" id="motor_number" value="{{ $data->vehicle->motor_number??''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_id">Vehicle ID</label>
                            <input type="text" class="form-control bg-white" name="vehicle_id" id="vehicle_id" value="{{ $data->vehicle->vehicle_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="tax_invoice_number">Tax Invoice Number</label>
                            <input type="text" class="form-control bg-white" name="tax_invoice_number" id="tax_invoice_number" value="{{ $data->vehicle->tax_invoice_number ?? '' }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="tax_invoice_date">Tax Invoice Date</label>
                            <input type="date" class="form-control bg-white" name="tax_invoice_date" id="tax_invoice_date" value="{{ $data->vehicle?->tax_invoice_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="tax_invoice_value">Invoice Value/Purchase Price</label>
                            <input type="text" class="form-control bg-white" name="tax_invoice_value" id="tax_invoice_value" value="{{ $data->vehicle->tax_invoice_value ?? '' }}" readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="tax_invoice_attachment">Tax Invoice Attachment</label>-->
                    <!--        <input type="file" class="form-control bg-white" name="tax_invoice_attachment" id="tax_invoice_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'tax_invoice_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    
                         <?php
                                $TaxInvoiceattachment = $data->vehicle->tax_invoice_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $TaxInvoicefilePath = !empty($TaxInvoiceattachment)
                                    ? asset("EV/asset_master/tax_invoice_attachments/{$TaxInvoiceattachment}")
                                    : '';
                                   
                                    
                            
                                $isTaxInvoicePDF = $TaxInvoicefilePath && \Illuminate\Support\Str::endsWith($TaxInvoiceattachment, '.pdf');
                                $TaxInvoiceimageSrc = (!$TaxInvoicefilePath || $isTaxInvoicePDF) ? $defaultImage : $TaxInvoicefilePath;


                            ?>
                        <div class="col-md-12 mb-4 my-4">
                            <label class="input-label mb-2 ms-1" for="tax_invoice_attachment">Tax Invoice Attachment</label>
                         <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                            
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
                            <input type="text" class="form-control bg-white" name="location" id="location" value=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="city_code">City Code</label>
                            <input type="text" class="form-control bg-white" name="city_code" id="city_code" value=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="gd_hub_id">GD Hub ID Allocated</label>
                            <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id" id="gd_hub_id" value=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="gd_hub_id_exiting">GD Hub ID Existing</label>
                            <input type="text" class="form-control bg-white" style="padding:12px 20px;" name="gd_hub_id_exiting" id="gd_hub_id_exiting" value=""  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="financing_type">Financing Type</label>
                            <input type="text" class="form-control bg-white" name="financing_type" id="financing_type" value="{{ $data->vehicle?->financing_type_relation->name ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="asset_ownership">Asset Ownership</label>
                            <input type="text" class="form-control bg-white" name="asset_ownership" id="asset_ownership" value="{{ $data->vehicle?->asset_ownership_relation->name ?? ''}}"  readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-12 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="master_lease_agreement">Master Lease Agreement</label>-->
                    <!--        <input type="file" class="form-control bg-white" name="master_lease_agreement" id="master_lease_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'master_lease_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                        <?php
                        // $image_src = !empty($data->vehicle->master_lease_agreement)
                        //     ? asset("EV/asset_master/master_lease_agreements/{$data->vehicle->master_lease_agreement}")
                        //     : asset("admin-assets/img/defualt_upload_img.jpg");
                        
                        
                                $MasterLeaseattachment = $data->vehicle->master_lease_agreement ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $MasterLeasefilePath = !empty($MasterLeaseattachment)
                                    ? asset("EV/asset_master/master_lease_agreements/{$MasterLeaseattachment}")
                                    : '';
                                   
                                    
                            
                                $isMasterLeasePDF = $MasterLeasefilePath && \Illuminate\Support\Str::endsWith($MasterLeaseattachment, '.pdf');
                                $MasterLeaseimageSrc = (!$MasterLeasefilePath || $isMasterLeasePDF) ? $defaultImage : $MasterLeasefilePath;
                        ?>

                        
                        <div class="col-md-12 mb-4 my-4">
                            <label class="input-label mb-2 ms-1" for="master_lease_agreement">Master Lease Agreement</label>
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
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
                            <input type="date" class="form-control bg-white" name="lease_start_date" id="lease_start_date" value="{{ $data->vehicle?->lease_start_date ?? '' }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="lease_end_date">Lease End Date</label>
                            <input type="date" class="form-control bg-white" name="lease_end_date" id="lease_end_date" value="{{ $data->vehicle?->lease_end_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="emi_lease_amount">EMI/Lease Amount</label>
                            <input type="text" class="form-control bg-white" name="emi_lease_amount" id="emi_lease_amount" value="{{ $data->vehicle?->emi_lease_amount ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="hypothecation">Hypothecation</label>
                            <input type="text" class="form-control bg-white" name="hypothecation" id="hypothecation" value="{{ $data->vehicle?->hypothecation ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="hypothecation_to">Hypothecated To</label>
                            <input type="text" class="form-control bg-white" name="hypothecation_to" id="hypothecation_to" value=""  readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-12 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="hypothecation_document">Hypothecation Document</label>-->
                    <!--        <input type="file" class="form-control bg-white" name="hypothecation_document" id="hypothecation_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hypothecation_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                        <?php
                            //  $image_hypo = !empty($data->vehicle->hypothecation_document)
                            // ? asset("EV/asset_master/hypothecation_documents/{$data->vehicle->hypothecation_document}")
                            // : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                             $Hypothecationattachment = $data->vehicle->hypothecation_document ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $HypothecationfilePath = !empty($Hypothecationattachment)
                                    ? asset("EV/asset_master/hypothecation_documents/{$Hypothecationattachment}")
                                    : '';
                                   
                                    
                            
                                $isHypothecationPDF = $HypothecationfilePath && \Illuminate\Support\Str::endsWith($Hypothecationattachment, '.pdf');
                                $HypothecationimageSrc = (!$HypothecationfilePath || $isHypothecationPDF) ? $defaultImage : $HypothecationfilePath;
                            
                            
                        ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                             <label class="input-label mb-2 ms-1" for="hypothecation_document">Hypothecation Document</label>
                     <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                            
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
                            <input type="text" class="form-control bg-white" name="insurance_type" id="insurance_type" value="{{ $data->vehicle?->insurance_type ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="insurer_name">Insurer Name</label>
                            <input type="text" class="form-control bg-white" name="insurer_name" id="insurer_name" value="{{ $data->vehicle?->insurer_name ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="insurance_number">Insurance Number</label>
                            <input type="text" class="form-control bg-white" name="insurance_number" id="insurance_number" value="{{ $data->vehicle?->insurance_number ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="insurance_start_date">Insurance Start Date</label>
                            <input type="date" class="form-control bg-white" name="insurance_start_date" id="insurance_start_date" value="{{ $data->vehicle?->insurance_start_date ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="insurance_expiry_date">Insurance Expiry Date</label>
                            <input type="date" class="form-control bg-white" name="insurance_expiry_date" id="insurance_expiry_date" value="{{ $data->vehicle?->insurance_expiry_date ?? ''}}" readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="insurance_attachment">Insurance Attachment</label>-->
                    <!--        <input type="file" class="form-control bg-white" name="insurance_attachment" id="insurance_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'insurance_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                           <?php
                            // $image_src1 = !empty($data->vehicle->insurance_attachment)
                            //     ? asset("EV/asset_master/insurance_attachments/{$data->vehicle->insurance_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $insuranceAttachment = $data->vehicle->insurance_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $insuranceFilePath = !empty($insuranceAttachment)
                                    ? asset("EV/asset_master/insurance_attachments/{$insuranceAttachment}")
                                    : '';
                            
                                $isInsurancePDF = $insuranceFilePath && \Illuminate\Support\Str::endsWith($insuranceAttachment, '.pdf');
                                $insuranceImageSrc = (!$insuranceFilePath || $isInsurancePDF) ? $defaultImage : $insuranceFilePath;
                            ?>
                        
                         <div class="col-md-12 mb-4 my-4">
                             <label class="input-label mb-2 ms-1" for="insurance_attachment">Insurance Attachment</label>
                        <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
                            <img id="insurance_Image"
                                 src="{{ $insuranceImageSrc }}"
                                 alt="Insurance Attachment"
                                 class="img-fluid rounded shadow border"
                                 style="max-height: 300px; object-fit: cover; {{ $isInsurancePDF ? 'display: none;' : '' }}"
                                  onclick="OpenImageModal('{{ $insuranceImageSrc }}')">
                    
                            <iframe id="insurance_PDF"
                                    src="{{ $isInsurancePDF ? $insuranceFilePath : '' }}"
                                    style="width: 100%; height: 100%; {{ !$isInsurancePDF ? 'display: none;' : '' }} border: none;"
                                    frameborder="0"></iframe>
                            </div>
                        </div>
                    
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="registration_type">Registration Type</label>
                            <input type="text" class="form-control bg-white" name="registration_type" id="registration_type" value="{{ $data->vehicle?->registration_type_relation->name ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" hidden>
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="registration_status">Registration Status</label>
                            <input type="text" class="form-control bg-white" name="registration_status" id="registration_status" value="{{ $data->vehicle?->registration_status ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="temporary_registration_number">Temporary Registration Number</label>
                            <input type="text" class="form-control bg-white" name="temporary_registration_number" id="temporary_registration_number" value="{{ $data->vehicle?->temporary_registration_number ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="temporary_registration_date">Temporary Registration Date</label>
                            <input type="date" class="form-control bg-white" name="temporary_registration_date" id="temporary_registration_date" value="{{ $data->vehicle?->temporary_registration_date ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="temporary_registration_expiry_date">Temporary Registration Expiry Date</label>
                            <input type="date" class="form-control bg-white" name="temporary_registration_expiry_date" id="temporary_registration_expiry_date" value="{{ $data->vehicle?->temporary_registration_expiry_date ?? ''}}" readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="temporary_certificate_attachment">Temporary Registration Certificate Attachment</label>-->
                    <!--        <input type="File" class="form-control bg-white" name="temporary_certificate_attachment" id="temporary_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'temporary_certificate_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                           <?php
                            // $image_temproary = !empty($data->vehicle->temproary_reg_attachment)
                            //     ? asset("EV/asset_master/temporary_certificate_attachments/{$data->vehicle->temproary_reg_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                                $temporaryAttachment = $data->vehicle->temproary_reg_attachment ?? '';
                                $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                                $temporaryFilePath = !empty($temporaryAttachment)
                                    ? asset("EV/asset_master/temporary_certificate_attachments/{$temporaryAttachment}")
                                    : '';
                            
                                $isTemporaryPDF = $temporaryFilePath && \Illuminate\Support\Str::endsWith($temporaryAttachment, '.pdf');
                                $temporaryImageSrc = (!$temporaryFilePath || $isTemporaryPDF) ? $defaultImage : $temporaryFilePath;
                            ?>
          
                        <div class="col-md-12 mb-4 my-4">
                            <label class="input-label mb-2 ms-1" for="temporary_certificate_attachment">Temporary Registration Certificate Attachment</label>
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
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
                            <input type="text" class="form-control bg-white" name="permanent_reg_number" id="permanent_reg_number" value="{{ $data->vehicle?->permanent_reg_number ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="permanent_reg_date">Permanent Registration Date</label>
                            <input type="date" class="form-control bg-white" name="permanent_reg_date" id="permanent_reg_date" value="{{ $data->vehicle?->permanent_reg_number ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="reg_certificate_expiry_date">Registration Certificate Expiry Date</label>
                            <input type="date" class="form-control bg-white" name="reg_certificate_expiry_date" id="reg_certificate_expiry_date" value="{{ $data->vehicle?->permanent_reg_number ?? ''}}" readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="hsrp_certificate_attachment">HSRP Copy Attachment</label>-->
                    <!--        <input type="File" class="form-control bg-white" name="hsrp_certificate_attachment" id="hsrp_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'hsrp_certificate_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                                 <?php
                            // $image_hsrp = !empty($data->vehicle->hsrp_copy_attachment)
                            //     ? asset("EV/asset_master/hsrp_certificate_attachments/{$data->vehicle->hsrp_copy_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            $hsrpAttachment = $data->vehicle->hsrp_copy_attachment ?? '';
                            $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                            $hsrpFilePath = !empty($hsrpAttachment)
                                ? asset("EV/asset_master/hsrp_certificate_attachments/{$hsrpAttachment}")
                                : '';
                        
                            $isHsrpPDF = $hsrpFilePath && \Illuminate\Support\Str::endsWith($hsrpAttachment, '.pdf');
                            $hsrpImageSrc = (!$hsrpFilePath || $isHsrpPDF) ? $defaultImage : $hsrpFilePath;
                            ?>
                        <div class="col-md-12 mb-4 my-4">
                            <label class="input-label mb-2 ms-1" for="hsrp_certificate_attachment">HSRP Copy Attachment</label>
                              <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                              
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
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="reg_certificate_attachment">Registration Certificate Attachment</label>-->
                    <!--        <input type="File" class="form-control bg-white" name="reg_certificate_attachment" id="reg_certificate_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'reg_certificate_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                        <?php
                            // $image_src2 = !empty($data->vehicle->reg_certificate_attachment)
                            //     ? asset("EV/asset_master/reg_certificate_attachments/{$data->vehicle->reg_certificate_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                                
                                
                        $regAttachment = $data->vehicle->reg_certificate_attachment ?? '';
                        $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                        $regFilePath = !empty($regAttachment)
                            ? asset("EV/asset_master/reg_certificate_attachments/{$regAttachment}")
                            : '';
                    
                        $isRegPDF = $regFilePath && \Illuminate\Support\Str::endsWith($regAttachment, '.pdf');
                        $regImageSrc = (!$regFilePath || $isRegPDF) ? $defaultImage : $regFilePath;
                                                ?>
          
                        <div class="col-md-12 mb-4 my-4">
                             <label class="input-label mb-2 ms-1" for="reg_certificate_attachment">Registration Certificate Attachment</label>
                            <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                               
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
                            <input type="date" class="form-control bg-white" name="fc_expiry_date" value="{{ $data->vehicle?->permanent_reg_number ?? ''}}" id="fc_expiry_date" readonly>
                        </div>
                    </div>
                    <!--<div class="col-md-6 mb-3">-->
                    <!--    <div class="form-group">-->
                    <!--        <label class="input-label mb-2 ms-1" for="fc_attachment">Fitness Certificate Attachment</label>-->
                    <!--        <input type="File" class="form-control bg-white" name="fc_attachment" id="fc_attachment_img" accept="image/png,image/jpeg,image/jpg,application/pdf" onchange="showImagePreview(this,'fc_attachment_Image')" readonly disabled style="display: none;">-->
                    <!--        <input type="text" class="form-control bg-white" value="File upload disabled (read-only view)" readonly>-->
                    <!--    </div>-->
                    <!--</div>-->
                        <?php
                            // $image_src3 = !empty($data->vehicle->fc_attachment)
                            //     ? asset("EV/asset_master/fc_attachments/{$data->vehicle->fc_attachment}")
                            //     : asset("admin-assets/img/defualt_upload_img.jpg");
                            
                            
                            
                    $fcAttachment = $data->vehicle->fc_attachment ?? '';
                    $defaultImage = asset('admin-assets/img/defualt_upload_img.jpg');
                    $fcFilePath = !empty($fcAttachment)
                        ? asset("EV/asset_master/fc_attachments/{$fcAttachment}")
                        : '';
                
                    $isFcPDF = $fcFilePath && \Illuminate\Support\Str::endsWith($fcAttachment, '.pdf');
                    $fcImageSrc = (!$fcFilePath || $isFcPDF) ? $defaultImage : $fcFilePath;
                            ?>
                         
                        <div class="col-md-12 mb-4 my-4">
                            <label class="input-label mb-2 ms-1" for="fc_attachment">Fitness Certificate Attachment</label>
                             <div class="preview-container border rounded shadow overflow-hidden position-relative" style="height: 300px; display: flex; justify-content: center; align-items: center;">
                                
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
                            <input type="text" class="form-control bg-white" name="servicing_dates" id="servicing_dates" value="{{ $data->vehicle?->servicing_dates ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1 d-block">Road Tax Applicable</label>
                            <input type="text" class="form-control bg-white" name="road_tax_applicable" id="road_tax_applicable" value="{{ $data->vehicle?->road_tax_applicable ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="road_tax_amount">Road Tax Amount</label>
                            <input type="text" class="form-control bg-white" name="road_tax_amount" id="road_tax_amount" value="{{ $data->vehicle?->road_tax_amount ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="road_tax_renewal_frequency">Road Tax Renewal Frequency</label>
                            <input type="text" class="form-control bg-white" name="road_tax_renewal_frequency" id="road_tax_renewal_frequency" value="{{ $data->vehicle?->road_tax_renewal_frequency ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="next_renewal_date">If Yes Road Tax Next Renewal Date</label>
                            <input type="date" class="form-control bg-white" name="next_renewal_date" id="next_renewal_date" value="{{ $data->vehicle?->next_renewal_date ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_type">Battery Type</label>
                            <input type="text" class="form-control bg-white" name="battery_type" id="battery_type" value="{{ $data->vehicle?->battery_type == 1 ? 'Self-Charging' : ($data->vehicle?->battery_type == 2 ? 'Portable' : '') }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" hidden>
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_variant_name">Battery Variant Name</label>
                            <input type="text" class="form-control bg-white" name="battery_variant_name" id="battery_variant_name" value="{{ $data->vehicle?->battery_variant_name ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no">Battery Serial Number - Original</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no" id="battery_serial_no" value="{{ $data->vehicle?->battery_serial_no ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement1">Battery Serial Number - Replacement 1</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no_replacement1" id="battery_serial_no_replacement1" value="{{ $data->vehicle?->battery_serial_no_replacement1 ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement2">Battery Serial Number - Replacement 2</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no_replacement2" id="battery_serial_no_replacement2" value="{{ $data->vehicle?->battery_serial_no_replacement2 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement3">Battery Serial Number - Replacement 3</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no_replacement3" id="battery_serial_no_replacement3" value="{{ $data->vehicle?->battery_serial_no_replacement3 ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement4">Battery Serial Number - Replacement 4</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no_replacement4" id="battery_serial_no_replacement4" value="{{ $data->vehicle?->battery_serial_no_replacement4 ?? ''}}"  readonly>
                        </div>
                    </div>
                    

                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="battery_serial_no_replacement5">Battery Serial Number - Replacement 5</label>
                            <input type="text" class="form-control bg-white" name="battery_serial_no_replacement5" id="battery_serial_no_replacement5" value="{{ $data->vehicle?->battery_serial_no_replacement5 ?? ''}}" readonly>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_variant_name">Charger Variant Name</label>
                            <input type="text" class="form-control bg-white" name="charger_variant_name" id="charger_variant_name" value="{{ $data->vehicle?->charger_variant_name ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no">Charger Serial Number - Original</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no" id="charger_serial_no" value="{{ $data->vehicle?->charger_serial_no ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement1">Charger Serial Number - Replacement 1</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no_replacement1" id="charger_serial_no_replacement1" value="{{ $data->vehicle?->charger_serial_no_replacement1 ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement2">Charger Serial Number - Replacement 2</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no_replacement2" id="charger_serial_no_replacement2" value="{{ $data->vehicle?->charger_serial_no_replacement2 ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement3">Charger Serial Number - Replacement 3</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no_replacement3" id="charger_serial_no_replacement3" value="{{ $data->vehicle?->charger_serial_no_replacement3 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement4">Charger Serial Number - Replacement 4</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no_replacement4" id="charger_serial_no_replacement4" value="{{ $data->vehicle?->charger_serial_no_replacement4 ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="charger_serial_no_replacement5">Charger Serial Number - Replacement 5</label>
                            <input type="text" class="form-control bg-white" name="charger_serial_no_replacement5" id="charger_serial_no_replacement5" value="{{ $data->vehicle?->charger_serial_no_replacement5 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_variant_name">Telematics Variant Name</label>
                            <input type="text" class="form-control bg-white" name="telematics_variant_name" style="padding:12px 20px;" id="telematics_variant_name" value="{{ $data->vehicle?->telematics_variant_name ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_oem">Telematics OEM</label>
                            <input type="text" class="form-control bg-white" name="telematics_oem" id="telematics_oem" value="{{ $data->vehicle?->telematics_oem ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no"> Telematics Serial Number - Original</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no" id="telematics_serial_no" value="{{ $data->vehicle?->telematics_serial_no ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_imei_no"> Telematics IMEI Number</label>
                            <input type="text" class="form-control bg-white" name="telematics_imei_no" id="telematics_imei_no" value="{{ $data->vehicle?->telematics_imei_no ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement1"> Telematics Serial Number - Replacement 1</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement1" id="telematics_serial_no_replacement1" value="{{ $data->vehicle?->telematics_serial_no_replacement1 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement2"> Telematics Serial Number - Replacement 2</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement2" id="telematics_serial_no_replacement2" value="{{ $data->vehicle?->telematics_serial_no_replacement2 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement3"> Telematics Serial Number - Replacement 3</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement3" id="telematics_serial_no_replacement3" value="{{ $data->vehicle?->telematics_serial_no_replacement3 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement4"> Telematics Serial Number - Replacement 4</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement4" id="telematics_serial_no_replacement4" value="{{ $data->vehicle?->telematics_serial_no_replacement4 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="telematics_serial_no_replacement5"> Telematics Serial Number - Replacement 5</label>
                            <input type="text" class="form-control bg-white" name="telematics_serial_no_replacement5" id="telematics_serial_no_replacement5" value="{{ $data->vehicle?->telematics_serial_no_replacement5 ?? ''}}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" >
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="client">Client Name</label>
                            <input type="text" class="form-control bg-white" name="client" id="client" value="{{ $data->vehicle?->quality_check->customer_relation->trade_name ?? '' }}"  readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" >
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_delivery_date">Vehicle Delivery Date</label>
                            <input type="date" class="form-control bg-white" name="vehicle_delivery_date" id="vehicle_delivery_date" value="{{ $data->vehicle?->vehicle_delivery_date ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="input-label mb-2 ms-1" for="vehicle_status">Vehicle Status</label>
                            <input type="text" class="form-control bg-white" name="vehicle_status" id="vehicle_status" value="{{ $current_status ?? ''}}" readonly>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
            <div class="tab-pane fade" id="client-info">
                <div class="card">
                 <div class="card-body">
                     <div class="row">
                         <div class="col-md-12 mb-3 d-flex justify-content-center align-items-center ">
                            @php
                                $customer_image = $data->rider->customerlogin->customer_relation->profile_img 
                                                  ? asset('EV/vehicle_transfer/profile_images/' . $data->rider->customerlogin->customer_relation->profile_img) 
                                                  : null;
                                $default_img = asset('admin-assets/img/person.png');
                            @endphp
                            <img src="{{ $customer_image ?? $default_img }}" width=200 height=150 style="border-radius:5px;">
                        </div>
                         
                     </div>
                      <div class="row">
                          
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="client_name">Client Name</label>
                                <input type="text" class="form-control bg-white" name="client_name" id="client_name"  value="{{$data->rider->customerlogin->customer_relation->trade_name ?? '' }}" >
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="contact_no">Client Contact No</label>
                                <input type="text" class="form-control bg-white" name="contact_no" id="contact_no"  value="{{$data->rider->customerlogin->customer_relation->phone ?? ''}}" >
                            </div>
                        </div>
                        
                        
                                                 <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="email_id">Client Email ID</label>
                                <input type="text" class="form-control bg-white" name="email_id" id="email_id"  value="{{$data->rider->customerlogin->customer_relation->email ?? ''}}" >
                            </div>
                        </div>
                        
                        </div>
                        </div>
                </div>
            </div>
            <div class="tab-pane fade" id="rider-info">
                <div class="card">
                    <div class="card-body">
                      <div class="row">
                          
                         <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Name</label>
                                <input type="text" class="form-control bg-white" name="name" id="name"  value="{{$data->rider->name??''}}" >
                            </div>
                        </div>
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="mobile_no">Mobile No</label>
                                <input type="text" class="form-control bg-white" name="mobile_no" id="mobile_no"  value="{{$data->rider->mobile_no??''}}" >
                            </div>
                        </div>
                        
                        
                    <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="email_id">Email ID</label>
                                <input type="text" class="form-control bg-white" name="email_id" id="email_id"  value="{{$data->rider->email??''}}" >
                            </div>
                        </div>
                        
                        </div>
                        
                        
                     <div class="row g-3">
                    <!-- Aadhaar Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Aadhaar Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->adhar_front))
                                @php
                                    $frontExtension = pathinfo($data->rider->adhar_front, PATHINFO_EXTENSION);
                                    $frontPath = asset('b2b/aadhar_images/'.$data->rider->adhar_front);
                                @endphp
                               
                
                                @if(in_array(strtolower($frontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $frontPath }}"
                                         class="img-fluid rounded"
                                         alt="Aadhaar Front"
                                         onclick="OpenImageModal('{{ $frontPath }}')">
                                @elseif(strtolower($frontExtension) === 'pdf')
                                <iframe src="{{ $frontPath }}"
                                        class="file-preview border rounded"
                                        style="width:100%; height:200px;"
                                        frameborder="0"></iframe>
                                           
                                           
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="Aadhaar Front"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                                 
                        </div>
                    </div>
                
                    <!-- Aadhaar Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Aadhaar Card Back</label>
                        <div class="file-preview-container">
                        @if(!empty($data->rider->adhar_back))
                            @php
                                $backExtension = pathinfo($data->rider->adhar_back, PATHINFO_EXTENSION);
                                $backPath = asset('b2b/aadhar_images/'.$data->rider->adhar_back);
                            @endphp
            
                            @if(in_array(strtolower($backExtension), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $backPath }}"
                                     class="img-fluid rounded"
                                     alt="Aadhaar Back"
                                     onclick="OpenImageModal('{{ $backPath }}')">
                            @elseif(strtolower($backExtension) === 'pdf')
                                       
                                 <iframe src="{{ $backPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                            @endif
                        @else
                            <img src="{{ asset('b2b/img/default_image.png') }}"
                                 class="img-fluid rounded"
                                 alt="Aadhaar Back"
                                 onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                        @endif
                        </div>
                    </div>
                </div>
                
                
            <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="adhar">Adhar Card Number</label>
                                <input type="text" class="form-control bg-white" name="adhar_number" id="adhar_number" value="{{$data->rider->adhar_number ?? ''}}" readonly>
                            </div>
                        </div>
                </div>
                
                
                <div class="row g-3">
                    <!-- Pan Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Front</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->pan_front))
                                @php
                                    $panFrontExtension = pathinfo($data->rider->pan_front, PATHINFO_EXTENSION);
                                    $panFrontPath = asset('b2b/pan_images/'.$data->rider->pan_front);
                                @endphp
                
                                @if(in_array(strtolower($panFrontExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panFrontPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Front"
                                         onclick="OpenImageModal('{{ $panFrontPath }}')">
                                @elseif(strtolower($panFrontExtension) === 'pdf')
                                           
                                <iframe src="{{ $panFrontPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="PAN Front"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                        </div>
                    </div>
                
                    <!-- Pan Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pan Card Back</label>
                        <div class="file-preview-container">
                            @if(!empty($data->rider->pan_back))
                                @php
                                    $panBackExtension = pathinfo($data->rider->pan_back, PATHINFO_EXTENSION);
                                    $panBackPath = asset('b2b/pan_images/'.$data->rider->pan_back);
                                @endphp
                
                                @if(in_array(strtolower($panBackExtension), ['jpg', 'jpeg', 'png']))
                                    <img src="{{ $panBackPath }}"
                                         class="img-fluid rounded"
                                         alt="PAN Back"
                                         onclick="OpenImageModal('{{ $panBackPath }}')">
                                @elseif(strtolower($panBackExtension) === 'pdf')
                                <iframe src="{{ $panBackPath }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                                @endif
                            @else
                                <img src="{{ asset('b2b/img/default_image.png') }}"
                                     class="img-fluid rounded"
                                     alt="PAN Back"
                                     onclick="OpenImageModal('{{ asset('b2b/img/default_image.png') }}')">
                            @endif
                        </div>
                    </div>
                </div>
                
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="pan">Pan Card Number</label>
                                <input type="text" class="form-control bg-white" name="pan_number" id="pan_number" value="{{$data->rider->pan_number ?? ''}}"  readonly>
                            </div>
                        </div>

                </div>
                
                
                                @if(!empty($data->rider->driving_license_number))
                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Front</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->driving_license_front ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                        @if(!empty($file))
                        @if(pathinfo($data->rider->driving_license_front, PATHINFO_EXTENSION) === 'pdf')
                                 
                    <iframe src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                            
                        @else
                            <img src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}"
                                 class="img-fluid rounded"
                                 alt="Driving License Front"
                                 onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_front) }}')">
                        @endif
                            @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License">
                        @endif
                        </div>
                    </div>
                
                    <!-- Driving License Back -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Driving License Back</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->driving_license_back ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($data->rider->driving_license_back, PATHINFO_EXTENSION) === 'pdf')
                                       
                                <iframe src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                            @else
                                <img src="{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}"
                                     class="img-fluid rounded"
                                     alt="Driving License Back"
                                     onclick="OpenImageModal('{{ asset('b2b/driving_license_images/'.$data->rider->driving_license_back) }}')">
                            @endif
                                @else
                            {{-- Show default image when no file --}}
                            <img src="{{ $defaultImage }}"
                                 class="img-fluid rounded"
                                 alt="Default Driving License">
                        @endif
                        </div>
                    </div>
                </div>
                
                
                 <div class="row">
                      <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Number</label>
                                <input type="text" class="form-control bg-white" name="license_number" id="license_number" value="{{$data->rider->driving_license_number ?? ''}}" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">Driving License Expiry Date</label>
                                <input type="text" class="form-control bg-white"  value="{{$data->rider->dl_expiry_date ?? ''}}" readonly>
                            </div>
                        </div>
                </div>
            
            @elseif(!empty($data->rider->llr_number))

                <div class="row g-3">
                    <!-- Driving License Front -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">LLR</label>
                        <div class="file-preview-container">
                        @php
                            $file = $data->rider->llr_image ?? null;
                            $defaultImage = asset('b2b/img/default_image.png');
                        @endphp
                          @if(!empty($file))
                            @if(pathinfo($data->rider->llr_image, PATHINFO_EXTENSION) === 'pdf')

                                       
                         <iframe src="{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}"
                                class="file-preview border rounded"
                                style="width:100%; height:200px;"
                                frameborder="0"></iframe>
                            @else
                                <img src="{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}"
                                     class="img-fluid rounded"
                                     alt="LLR Image"
                                     onclick="OpenImageModal('{{ asset('b2b/llr_images/'.$data->rider->llr_image) }}')">
                            @endif
                            
                                @else
                                {{-- Show default image when no file --}}
                                <img src="{{ $defaultImage }}"
                                     class="img-fluid rounded"
                                     alt="Default Driving License">
                            @endif
                        </div>
                    </div>
                
                </div>
                
                
                
            
                 <div class="row">
                      <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="input-label mb-2 ms-1" for="name">LLR Number</label>
                                <input type="text" class="form-control bg-white" name="llr_number" id="llr_number" value="{{ $data->rider->llr_number ?? '' }}"  readonly>
                            </div>
                        </div>
                </div>

                
                
            @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body p-4 text-center">
                    
                                @if(!empty($data->rider->terms_condition) && $data->rider->terms_condition == 1)
                                    <!-- Checkbox (Accepted) -->
                                    <div class="form-check d-flex align-items-center mb-3">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input me-2" 
                                            checked 
                                            disabled
                                            id="termsAccepted"
                                        >
                                        <label class="form-check-label fw-semibold" for="termsAccepted">
                                            Terms & Conditions Accepted
                                        </label>
                                    </div>
                    
                                    <!-- Terms & Conditions Content -->
                                    <div class="card-body p-3 text-start">
                                        <h4 class="custom-dark mb-3">
                                            Terms and Conditions for Rider Responsibility
                                        </h4>
                    
                                        <p>
                                            By registering as a rider on our platform, I acknowledge and confirm the following:
                                        </p>
                    
                                        <ul class="ms-3">
                                            <li>I do <strong>not</strong> possess a valid driving license or Learner's License (LLR).</li>
                                            <li>I understand that operating a vehicle without a valid license is against the law.</li>
                                            <li>I take <strong>full responsibility</strong> for any accidents, damages, or legal consequences that may arise while performing deliveries.</li>
                                            <li>I agree to indemnify and hold harmless the company, its employees, and partners from any claims, losses, or liabilities resulting from my actions.</li>
                                            <li>I confirm that I will follow all safety guidelines and traffic rules to the best of my ability.</li>
                                        </ul>
                    
                                        <p class="mt-3">
                                            By continuing, I acknowledge that I have read and understood these terms and accept the responsibility
                                            for all outcomes arising from my participation on the platform.
                                        </p>
                                    </div>
                                @else
                                    <!-- If not accepted -->
                                    <p class="mt-3 mb-0 text-muted">
                                        The rider has not accepted the terms and conditions yet.
                                    </p>
                                @endif
                    
                            </div>
                        </div>
                    </div>
            @endif
                
                
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="service-records">
                <div class="card">
                                <!-- Filter Section -->
                <div class="filter-section d-flex justify-content-between align-items-center">
                    <h5>Overall List</h5>
                    <div class="d-flex gap-3">
                    <!-- From Date -->
                    <div>
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date">
                    </div>
                    
                    <!-- To Date -->
                    <div>
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date">
                    </div>

                <div>
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" id="filter-btn" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
                    </div>
                </div>
                
                   <!-- kanban view -->
                <div class="kanban-view" id="kanban-view">
                    <div class="container-kanban">
                        <div class="row mt-3 d-flex flex-wrap justify-content-between align-items-center">
            
                            
                            <div class="col-md-6 col-12 d-flex flex-wrap justify-content-end align-items-center gap-2">
                                  <!-- New Lead Button -->
                                  <a href="javascript:void(0);" id="refresh-btn" class="btn custom-btn btn-round btn-sm px-3" >
                                      <i class="bi bi-arrow-clockwise"></i> Refresh
                                  </a>
            
                              </div>
            
                        </div>
            
                        @php
                            $statuses = [
                                'unassigned' => 'Unassigned',
                                'inprogress' => 'Inprogress',
                                'closed' => 'Closed',
                            ];
            
                            $colors = ['#03a9f4', '#7cb342', '#f32f10'];
                            $colorIndex = 0;
                            $count = 0;
                        @endphp
            
                        <div class="row kanban-board mt-3" id="autoload_lead_data">
                          
                        </div>
            
                    </div>
                </div>
                </div>
            </div>
            <div class="tab-pane fade" id="accident-records">
                <div class="card p-4" >
                    <div class="table-responsive" >
    
                    <table id="accidentList" class="table text-left table-striped table-bordered table-hover" style="overflow-x: auto; white-space: nowrap; width: 100%;">
                            
                            <thead class="bg-white rounded" style="background:white !important; color:black !important;">
                            <tr>
                           
                              <th scope="col" class="custom-dark">Sl No</th>
                              <th scope="col" class="custom-dark">Request ID</th>
                              <th scope="col" class="custom-dark">Subject</th>
                              <th scope="col" class="custom-dark">Accident Type </th>
                              <th scope="col" class="custom-dark">City</th>
                              <th scope="col" class="custom-dark">Zone</th>
                              <th scope="col" class="custom-dark">Date and Time</th>
                              <th scope="col" class="custom-dark">Status</th>
                              <th scope="col" class="custom-dark">Action</th>
                            </tr>
                          </thead>
                            
                            <tbody class="border border-white"></tbody>
                          



                        </table>
                     </div>
                </div>
            </div>
            <div class="tab-pane fade" id="activity-logs">
                <div class="card">
                <p>Activity Logs content goes here...</p>
                </div>
            </div>
        </div>
        
        
        </div>
        

            <div id="ActivityLogSection" style="display:none;">
                <div class="card my-2">
                <div class="card-header">
                
                <div class="row g-3 d-flex justify-content-between align-items-center">
                    
                    <div class="col-md-10 col-12 mb-2">
                        <div class="d-flex justify-content-start align-items-center">
                            <div>
                            
                            
                            </div>
                            <div class="px-3">
                                <div class="h4 fw-bold mt-2" style=" color: black; ">Activity Logs</div>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-12 mb-2">
                        <div class="d-flex justify-content-end align-items-center gap-2">

                            <a href="javascript:void(0);" class="btn btn-dark  px-5">Back</a>

                        </div>
                    </div>

                </div>
                
          
            </div>
            </div>
            <div class="card p-4" id="autoload_log_section">
                
            </div>
            
        </div>


    </div>
    
    
             <!--  Image View Modal -->
                    <div class="modal fade" id="attachment_view_modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4" style="overflow:hidden;"> <!-- overflow hidden added -->
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
                                    <button class="btn btn-sm btn-dark" onclick="downloadImage()">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-dark" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="modal-body d-flex justify-content-center align-items-center" 
                                     style="overflow:auto; max-height:80vh; background:#f9f9f9;">
                                    <img src="" id="modal_preview_image" 
                                         style="max-width:100%; max-height:75vh; transition:transform 0.3s ease;">
                                </div>
                            </div>
                        </div>
                    </div>
   
@section('script_js')

 <script>
        // Initialize tabs
        document.addEventListener('DOMContentLoaded', function() {
            // Set active tab
            const serviceRecordsTab = document.getElementById('service-records-tab');
            serviceRecordsTab.classList.add('active');
            
            // Add click handlers for date pickers
            window.openDatePicker = function(type) {
                // In a real implementation, this would open a date picker
                alert(Opening ${type} date picker);
            };

            // Add click handlers for buttons
            document.querySelector('.back-btn').addEventListener('click', function() {
                alert('Going back...');
            });

            document.querySelector('.activity-logs-btn').addEventListener('click', function() {
                alert('Opening activity logs...');
            });

            // Add card click handlers
            const cards = document.querySelectorAll('.service-card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const requestId = this.querySelector('.card-row span').textContent;
                    alert(Opening details for ${requestId});
                });
                
                // Add hover effect
                card.style.cursor = 'pointer';
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                    this.style.transition = 'all 0.2s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });

            // Smooth scrolling for horizontal scroll
            const statusColumns = document.querySelector('.status-columns');
            let isDown = false;
            let startX;
            let scrollLeft;

            statusColumns.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - statusColumns.offsetLeft;
                scrollLeft = statusColumns.scrollLeft;
            });

            statusColumns.addEventListener('mouseleave', () => {
                isDown = false;
            });

            statusColumns.addEventListener('mouseup', () => {
                isDown = false;
            });

            statusColumns.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - statusColumns.offsetLeft;
                const walk = (x - startX) * 2;
                statusColumns.scrollLeft = scrollLeft - walk;
            });
        });

        // Tab switching functionality
        function switchTab(tabId) {
            // Remove active class from all tabs
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Hide all tab content
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show selected tab
            document.getElementById(tabId + '-tab').classList.add('active');
            document.getElementById(tabId).classList.add('show', 'active');
        }

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                const statusColumns = document.querySelector('.status-columns');
                const scrollAmount = 200;
                
                if (e.key === 'ArrowLeft') {
                    statusColumns.scrollLeft -= scrollAmount;
                } else {
                    statusColumns.scrollLeft += scrollAmount;
                }
            }
        });
    </script>

<script>
    let scale = 1;
    let rotation = 0;
    let currentImageUrl = ''; 
    
    function OpenImageModal(img_url) {
        scale = 1;
        rotation = 0;
        currentImageUrl = img_url;; 
        updateImageTransform();
        document.getElementById("modal_preview_image").src = img_url;
        $("#attachment_view_modal").modal('show');
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
        const img = document.getElementById("modal_preview_image");
        img.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
    }
    
    async function downloadImage() {
        console.log('started');
    if (!currentImageUrl) return;

        try {
            const response = await fetch(currentImageUrl, { mode: 'no-cors' });
            const blob = await response.blob();
            const blobUrl = window.URL.createObjectURL(blob);
    
            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = currentImageUrl.split('/').pop() || 'image.jpg';
            document.body.appendChild(link);
            link.click();
    
            document.body.removeChild(link);
            window.URL.revokeObjectURL(blobUrl);
        } catch (error) {
            console.error('Error downloading image:', error);
            alert('Unable to download image. Please try opening it in a new tab and saving manually.');
        }
    }
    </script>

<script>
$(document).ready(function () {
    // ✅ Select All Checkbox
    $('#CSelectAllBtn').on('change', function () {
        $('.sr_checkbox').prop('checked', this.checked);
    });

    $(document).on('change', '.sr_checkbox', function () {
        if (!this.checked) {
            $('#CSelectAllBtn').prop('checked', false);
        } else if ($('.sr_checkbox:checked').length === $('.sr_checkbox').length) {
            $('#CSelectAllBtn').prop('checked', true);
        }
    });

    // ✅ Initialize DataTable
    var accidentTable = $('#accidentList').DataTable({
        pagingType: "simple",
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('b2b.admin.deployed_asset.accident_list', $data->id) }}",
            type: "GET",
            beforeSend: function () {
                $('#accidentList tbody').html(`
                  <tr>
                    <td colspan="9" class="text-center p-4">
                      <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                `);
            },
            error: function () {
                $('#accidentList tbody').html(`
                  <tr>
                    <td colspan="9" class="text-center text-danger p-4">
                      <i class="bi bi-exclamation-triangle"></i> 
                      Failed to load data. Please try again.
                    </td>
                  </tr>
                `);
            }
        },
        columns: [
            { data: 0, className: 'text-center' },
            { data: 1, className: 'text-center', orderable: false, searchable: false },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, className: 'text-center', orderable: false, searchable: false }
            
        ],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1, 5] }
        ],
        lengthMenu: [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
        responsive: true,
       
    });

    // ✅ Apply filter button
    window.applyRiderFilter = function() {
        riderTable.ajax.reload();
    }

    // ✅ Clear filter button
    window.clearRiderFilter = function() {
        $('#FromDate').val('');
        $('#ToDate').val('');
        $('#city_id').val('').trigger('change');
        $('#zone_id').val('').trigger('change');
        riderTable.ajax.reload();
    }
});

$(document).ready(function() {
    var limit = 3;
    var assign_id = "{{ $data->id }}";

    // Track current offset per status
    var offsets = {
        'unassigned': 0,
        'inprogress': 0,
        'closed': 0
    };

    // Initial load (no append)
    loadLeadData(null, 0, limit, false);

    $(document).on('click', '#refresh-btn', function() {
        // Show creative loading while refreshing
        $("#autoload_lead_data").html(`
            <div class="w-100 text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p class="mt-3 fw-bold text-primary">Refreshing service data...</p>
            </div>
        `);

        // Reset offsets
        offsets = {
            'unassigned': 0,
            'inprogress': 0,
            'closed': 0
        };

        // Reload data
        loadLeadData(null, 0, limit, false);
    });
    
        $(document).on('click', '#filter-btn', function () {
        var fromDate = $('#from_date').val();
        var toDate   = $('#to_date').val();
    
    
            // Both required
        if (!fromDate || !toDate) {
            toastr.error("Please select both From Date and To Date.");
            return;
        }
    
        // Validation
        if (fromDate && !toDate) {
            toastr.error("Please select To Date.");
            return;
        }
        if (toDate && !fromDate) {
            toastr.error("Please select From Date.");
            return;
        }
        if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
            toastr.error("To Date must be same or after From Date.");
            return;
        }
    
        // Show creative loading
        $("#autoload_lead_data").html(`
            <div class="w-100 text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p class="mt-3 fw-bold text-primary">Filtering service data...</p>
            </div>
        `);
    
        // Reset offsets
        offsets = { 'unassigned': 0, 'inprogress': 0, 'closed': 0 };
    
        // Reload with filter
        loadLeadData(null, 0, limit, false, fromDate, toDate);
    });



    // Click "Load More" handler
    $(document).on('click', '.lead-more-btn', function() {
        var button = $(this);
        var status = button.data('status');
        var offset = offsets[status] || 0;

        // show spinner
        button.find('.spinner-border').removeClass('d-none');

        // request append for this status
        $.ajax({
            url: "{{ route('b2b.admin.deployed_asset.auload_service_data') }}",
            method: "GET",
            data: {
                status: status,
                offset: offset,
                limit: limit,
                assign_id: assign_id,
                append: true
            },
            success: function(resp) {
                // hide spinner
                button.find('.spinner-border').addClass('d-none');

                var itemsHtml = resp.items_html || '';
                var hasMore = resp.has_more;
                var nextOffset = resp.next_offset !== undefined ? resp.next_offset : (offset + limit);

                if (itemsHtml.trim() === '') {
                    // no new items — remove the button
                    button.remove();
                    return;
                }

                // Remove "No Service Found" placeholder for this column if present
                $("#no-lead-" + status).remove();

                // Append new items ABOVE the button so the button stays at the end
                var cardContainer = $("#" + status + " .kanban-cards");
                button.closest('.card-inside').before(itemsHtml);
                
                // If no new items and first load, show "No Service Found"
                if(itemsHtml.trim() === '' && cardContainer.children().length <= 1){
                    cardContainer.html('<div class="text-center mt-5 card-inside" id="no-lead-' + status + '"><h4><i class="bi bi-opencollective"></i></h4><h4>No Service Found</h4></div>');
                    button.remove();
                }

                // Update offset
                offsets[status] = nextOffset;

                if (!hasMore) {
                    // No more items -> remove button
                    button.remove();
                } else {
                    // Update button's data-offset (optional)
                    button.data('offset', nextOffset);
                }
            },
            error: function() {
                // hide spinner and optionally show error state
                button.find('.spinner-border').addClass('d-none');
            }
        });
    });

    // Function for initial load
    function loadLeadData(status, offset, limit, append ,  fromDate = null, toDate = null) {
        $.ajax({
            url: "{{ route('b2b.admin.deployed_asset.auload_service_data') }}",
            method: "GET",
            data: {
                status: status,
                offset: offset,
                limit: limit,
                assign_id: assign_id,
                append: false, 
                from_date: fromDate,
                to_date: toDate
            },
            success: function(data) {
                // initial full HTML
                $("#autoload_lead_data").html(data.html_data);

                // initialize offsets to limit for each status that has a Lead More button
                $('.lead-more-btn').each(function() {
                    var st = $(this).data('status');
                    var off = $(this).data('offset') || limit;
                    offsets[st] = off;
                });
            }
        });
    }
});


$(document).ready(function () {
    // Show Activity Logs section
    $(document).on('click', 'a[href="#activity-logs"]', function (e) {
        e.preventDefault();
        $("#AssetSection").hide(); 
        $("#ActivityLogSection").show();
        
                $.ajax({
            url: "{{ route('b2b.admin.deployed_asset.activity_logs', $data->vehicle->id) }}", 
            type: "GET",
            beforeSend: function () {
                $("#autoload_log_section").html('<div class="text-center py-5">Loading...</div>');
            },
            success: function (response) {
                $("#autoload_log_section").html(response);
            },
            error: function () {
                $("#autoload_log_section").html('<div class="alert alert-danger">Failed to load activity logs.</div>');
            }
        });
        
        
    });

    // Back button inside Activity Logs
    $(document).on('click', '#ActivityLogSection .btn-dark', function (e) {
        e.preventDefault();
        $("#ActivityLogSection").hide();
        $("#AssetSection").show();
    });
});

</script>



@endsection
</x-app-layout>
