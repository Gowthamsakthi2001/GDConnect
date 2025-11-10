<?php
        $countData = DB::table('vehicle_qc_check_lists as qc')
        ->join('ev_tbl_asset_master_vehicles as vh', 'qc.id', '=', 'vh.qc_id')
        ->leftJoin('asset_vehicle_inventories as inv', 'inv.asset_vehicle_id', '=', 'vh.id')
        ->when($location_id, fn($q) => $q->where('qc.location', $location_id))
        ->when($vehicle_model, fn($q) => $q->where('qc.vehicle_model', $vehicle_model))
        ->when($vehicle_type, fn($q) => $q->where('qc.vehicle_type', $vehicle_type))
        ->when($timeline, function ($q) use ($timeline) {
            switch ($timeline) {
                case 'today': $q->whereDate('qc.created_at', today()); break;
                case 'this_week': $q->whereBetween('qc.created_at', [now()->startOfWeek(), now()->endOfWeek()]); break;
                case 'this_month': $q->whereBetween('qc.created_at', [now()->startOfMonth(), now()->endOfMonth()]); break;
                case 'this_year': $q->whereBetween('qc.created_at', [now()->startOfYear(), now()->endOfYear()]); break;
            }
        })
        ->where('vh.delete_status', 0)
        ->selectRaw("
            SUM(CASE WHEN inv.transfer_status = 1 THEN 1 ELSE 0 END) as onRoad,
            SUM(CASE WHEN inv.transfer_status <> 1 OR inv.transfer_status IS NULL THEN 1 ELSE 0 END) as offRoad,
            SUM(CASE WHEN inv.transfer_status = 2 THEN 1 ELSE 0 END) as underMaintenance,
            SUM(CASE WHEN inv.transfer_status = 6 THEN 1 ELSE 0 END) as accidentCase
        ")
        ->first();

      $onRoad_asset_count = $countData->onRoad ?? 0;
$offRoad_asset_count = $countData->offRoad ?? 0;
$undermaintance_asset_count = $countData->underMaintenance ?? 0;
$accident_asset_count = $countData->accidentCase ?? 0;

$total_asset_count = $onRoad_asset_count + $offRoad_asset_count;

$onRoad_percentage = $total_asset_count > 0 
    ? round(($onRoad_asset_count / $total_asset_count) * 100, 2) 
    : 0;

$offRoad_percentage = $total_asset_count > 0 
    ? round(($offRoad_asset_count / $total_asset_count) * 100, 2) 
    : 0;

$undermaintanance_percentage = $total_asset_count > 0 
    ? round(($undermaintance_asset_count / $total_asset_count) * 100, 2) 
    : 0;

$accidentcase_percentage = $total_asset_count > 0 
    ? round(($accident_asset_count / $total_asset_count) * 100, 2) 
    : 0;

?>

<div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Total Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" data-target="{{ 100 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="totalAssets" data-target="{{ $total_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad2.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
   
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">On Road Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="onRoadPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="onRoadAssets" data-target="{{ $onRoad_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad2.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Off Road Assets</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="offRoadPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="offRoadAssets" data-target="{{ $offRoad_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad3.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Under Maintainance</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="underMaintenancePercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="underMaintenanceAssets" data-target="{{ $undermaintance_asset_count ?? 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad4.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Accident Case</span> </h6>
                                <small style="color: #26C360; font-weight: 500;" class="count-animation" id="accidentPercentage" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                     <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" id="accidentAssets" data-target="{{ 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad5.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-xl-3 col-lg-3 col-md-4 mb-4 summary-card">
            <a class="text-dark" href="javascript:void(0);">
                <div class="card border-0 equal-card shadow-secondary">
                    <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                        <div class="">
                            <div class="d-flex justify-content-between"> 
                                   <h6 class="mb-3 text-muted"><span style="color:#a3a7af;">Total KM Driven</span> </h6>
                                   <small style="color: #26C360; font-weight: 500;" class="count-animation" data-target="{{ 0 }}">+0%</small>
                            </div>
                             <div class="d-flex justify-content-between align-items-center">
                                    <h3 style="color: #4b5563;" class="pe-2 fw-bold count-animation" data-target="{{ 0 }}"> 0 </h3> 
                                    <img src="{{ asset('public/admin-assets/icons/custom/ad6.png') }}" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

       <div id="noResultsMessage" class="text-center text-muted my-4" style="display: none;">
           
            <div class="col-12">
                    <div class="card border-0 equal-card shadow-secondary">
                        <div class="bg-white rounded-lg p-3 p-md-3 p-lg-3 p-xl-4">
                            <div>
                                <div class="d-flex justify-content-center align-items-center my-3">
                                    <i class="bi bi-emoji-frown fs-1 me-2" style="color:#4b5563;"></i>
                                    <h5 class="ps-2 mb-0" style="color:#4b5563;">No results found.</h5>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        
        <script>
            document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll('.count-animation');
    const speed = 200; // bigger = slower

    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const hasPercent = counter.textContent.includes("%");

        const updateCount = () => {
            let count = +counter.innerText.replace(/[^0-9]/g, "");
            const inc = Math.max(target / speed, 1); // ensure at least +1 step

            if (count < target) {
                count = Math.min(count + inc, target);
                counter.innerText = hasPercent 
                    ? Math.floor(count).toLocaleString() + "%" 
                    : Math.floor(count).toLocaleString();

                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = hasPercent 
                    ? target.toLocaleString() + "%" 
                    : target.toLocaleString();
            }
        };

        updateCount();
    });
});
        </script>