<x-app-layout>
@section('style_css')
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

<div class="container-fluid mt-3">
    <!-- First Row -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100 report-card" data-url="{{ route('b2b.admin.report.deployment_report') }}" style="border-radius:8px; background-color:#FFFFFF; cursor:pointer;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">
                        Deployment Report
                    </span>
                    <span class="text-muted" style="font-size:13px;">Compare daily deployment details</span>
                </div>
            </div>

        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100 report-card" data-url="{{ route('b2b.admin.report.service_report') }}" style="border-radius:8px; background-color:#FFFFFF; cursor:pointer;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Service Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily service details</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100 report-card" data-url="{{ route('b2b.admin.report.accident_report') }}" style="border-radius:8px; background-color:#FFFFFF; cursor:pointer;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon1.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Accident Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily accident details</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100 report-card" data-url="{{ route('b2b.admin.report.recovery_report') }}" style="border-radius:8px; background-color:#FFFFFF;cursor:pointer;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Recovery Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily recovery details</span>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex">
            <div class="d-flex align-items-center p-3 w-100 h-100 report-card" data-url="{{ route('b2b.admin.report.return_report') }}" style="border-radius:8px; background-color:#FFFFFF;cursor:pointer;">
                <div class="d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px;">
                    <img src="{{ asset('b2b/img/report_icon2.svg') }}" width="20" height="20" alt="icon">
                </div>
                <div class="d-flex flex-column justify-content-center">
                    <span style="font-size:16px; font-weight:500;">Return Report</span>
                    <span class="text-muted" style="font-size:13px;">Compare daily return details</span>
                </div>
            </div>
        </div>
        
        
    </div>
    
</div>



@section('script_js')
<script>
document.querySelectorAll('.report-card').forEach(card => {
    card.addEventListener('click', () => {
        window.location.href = card.dataset.url;
    });
});
</script>
@endsection
</x-app-layout>