@extends('layouts.b2b')

@section('css')
<style>
.report-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- First Row -->
    <div class="row g-2 g-md-3 mb-3 "> <!-- gutter = 8px on small, 16px on md+ -->
    
        <!--<div class="col-md-4">-->
        <!--    <div class="d-flex align-items-center p-3" style="border-radius:8px; background-color:#FFFFFF;">-->
        <!--        <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">-->
        <!--            <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">-->
        <!--        </div>-->
        <!--        <div class="d-flex flex-column justify-content-center">-->
                    <!--<span style="font-size:16px; font-weight:500;"><a href="{{route('b2b.reports.vehicle_usage')}}" style="color: black; text-decoration: none;">Vehicle Usage</a></span>-->
        <!--            <span style="font-size:16px; font-weight:500;">Vehicle Usage</span>-->
        <!--            <span class="text-muted" style="font-size:13px;">Compare daily vehicle usage details</span>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <div class="col-md-4">
            <a href="{{route('b2b.reports.deployment_report')}}" style="color: black; text-decoration: none;">
            <div class="d-flex align-items-center p-3 report-card" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Deployment Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily deployments</span>
                </div>
            </div>
            </a>
        </div>
        

        <div class="col-md-4">
            <a href="{{route('b2b.reports.service_report')}}" style="color: black; text-decoration: none;">
            <div class="d-flex align-items-center p-3 report-card" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Service Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily service requests details</span>
                </div>
            </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <a href="{{route('b2b.reports.accident_report')}}" style="color: black; text-decoration: none;">
            <div class="d-flex align-items-center p-3 report-card" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Accident Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily accident vehicle details</span>
                </div>
            </div>
            </a>
        </div>
        
        
    </div>
    <div class="row g-2 g-md-3"> <!-- gutter = 8px on small, 16px on md+ -->
 
 

        
        
        <div class="col-md-4">
            <a href="{{route('b2b.reports.recovery_report')}}" style="color: black; text-decoration: none;">
            <div class="d-flex align-items-center p-3 report-card" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Recovery Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily recovery details</span>
                </div>
            </div>
             </a>
        </div>

        <div class="col-md-4">
            <a href="{{route('b2b.reports.return_report')}}" style="color: black; text-decoration: none;">
            <div class="d-flex align-items-center p-3 report-card" style="border-radius:8px; background-color:#FFFFFF;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Return Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily return vehicle details</span>
                </div>
            </div>
            </a>
        </div>
    

    </div>
</div>


@endsection

@section('js')
@endsection