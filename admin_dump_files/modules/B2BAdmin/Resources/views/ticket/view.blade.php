<x-app-layout>
 
 @section('style_css')   
<style>
    /* Custom tab styles */
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
</style>
@endsection

    <div class="main-content">
        <div class="">
        <div class="p-3 rounded" style="background:#fbfbfb;">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <!-- Title -->
                <h5 class="m-0 text-truncate custom-dark" style="font-size: clamp(1rem, 2vw, 1rem);">
                    Ticket In detail View
                </h5>

                <!-- Back Button -->
                <a href="{{ route('b2b.admin.agent.list') }}" 
                   class="btn btn-dark btn-md mt-2 mt-md-0">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
            
        </div>
    </div>
     
            <div class="" id="agent-info">
    <div class="card">
        <div class="card-body">
            <div class="row mt-1 mb-3">
                <div class="col-md-6" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border : #A61D1D 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#A61D1D">
                                Opened
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                                28.08.2025, 1:30 Pm
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="card shadow-sm border-0">
                        <div class="p-3" style="border-radius:8px;border:#005D27 1px solid ">
                            <div class="mb-1" style="font-weight:500; font-size:14px;color:#005D27;">
                                Closed  
                            </div>
                            <div style="font-weight:400; font-size:14px;">
                                29.08.2025, 2:30 Pm
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row ">
                <!-- Subject Field (12 cols) -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="name">Subject <span style="color:red;">*</span></label>
                        <input type="text" class="form-control bg-white" name="name" id="name" value="abc" placeholder="Deployment Issue Ticket">
                    </div>
                </div>
            
                <!-- Categories (Radio Buttons, also 12 cols full row) -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1">Categories <span style="color:red;">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="dashboard" value="Dashboard Issue">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="dashboard">Dashboard Issue</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="deployment" value="Deployment Request Issues">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="deployment">Deployment Request Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="vehicle" value="Vehicle List Issues">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="vehicle">Vehicle List Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="tracking" value="Live Tracking Issues">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="tracking">Live Tracking Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="reports" value="Reports Issues">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="reports">Reports Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="account" value="Account/Login">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="account">Account/Login</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="others" value="Others">
                                <label class="form-check-label" style="font-weight:400;font-size:14px;" for="others">Others</label>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- SubCategory Field (6 cols) -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="subcategories">Subcategory <span style="color:red;">*</span></label>
                        <input type="text" class="form-control bg-white" name="subcategories" id="subcategories" placeholder="Enter SubCategory">
                    </div>
                </div>
            
                <!-- Description Field (6 cols) -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="input-label mb-2 ms-1" for="description">Description <span style="color:red;">*</span></label>
                        <textarea class="form-control bg-white" name="description" id="description" rows="8" placeholder="Enter Description"></textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
            
        </div>
        

    
    
             
   
@section('script_js')

@endsection
</x-app-layout>
