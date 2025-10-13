<x-app-layout>
@section('style_css')
@endsection

<div class="container-fluid">
    <!-- First Row -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">
                        Vehicle Usage
                    </span>
                    <span class="text-muted" style="font-size:13px;">Compare daily vehicle usage details</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Rider Performance</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily Rider Performance details</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Ticket Requests</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily ticket request details</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Deployment Request</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily deployment request details</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Return Requests</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily return request details</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Recovery Requests</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily recovery request details</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Third Row -->
    <div class="row g-2 g-md-3">
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Client Statistics</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily client statistic details</span>
                </div>
            </div>
        </div>
    </div>
</div>



@section('script_js')
@endsection
</x-app-layout>