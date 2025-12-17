<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\B2B\Entities\B2BVehicleRequests; //updated by Mugesh.B
use Modules\B2B\Entities\B2BReportAccident;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BAdminDeploymentRequestExport;
use App\Exports\B2BAdminDeployedAssetExport;
use Modules\City\Entities\City;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; //updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Illuminate\Support\Str;
use Modules\AssetMaster\Entities\LocationMaster;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\MasterManagement\Entities\FinancingTypeMaster;//updated by Mugesh.B
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\AssetMaster\Entities\VehicleModelMaster; //updated by Mugesh.B
use Modules\MasterManagement\Entities\AssetOwnershipMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsurerNameMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InsuranceTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\HypothecationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RegistrationTypeMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\TelemetricOEMMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\InventoryLocationMaster;//updated by Mugesh.B
use Modules\MasterManagement\Entities\ColorMaster;//updated by Mugesh.B
use Modules\Leads\Entities\leads;
use Modules\LeadSource\Entities\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\MasterManagement\Entities\EvTblAccountabilityType; // updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; //updated by logesh
use Modules\B2B\Entities\B2BRecoveryRequest; //updated by Gowtham.S
use Modules\RecoveryManager\Entities\RecoveryComment; //updated by Gowtham.S
use App\Helpers\RecoveryNotifyHandler; //updated by Gowtham.S
use Modules\Zones\Entities\Zones; // updated by logesh

class DeployedAssetController extends Controller
{
        public function list(Request $request)
    {
       
    if ($request->ajax()) {
        try {
           
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone_id ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $vehicle_make = (array)$request->vehicle_make ?? [];
                $vehicle_model = (array)$request->vehicle_model ?? [];
                $status = (array)$request->status ?? [];
                $customer_id = (array)$request->customer_id ?? [];
            $query = B2BVehicleAssignment::with(['vehicle','vehicle.quality_check' ,'rider.customerlogin.customer_relation','VehicleRequest']);


            if ($request->filled('date_filter')) {
            
                switch ($request->date_filter) {
                    
                    case 'today':
                        $query->whereDate('created_at', now()->toDateString());
                        break;
            
                    case 'week':
                        $query->whereDate('created_at', '>=', now()->startOfWeek())
                              ->whereDate('created_at', '<=', now()->endOfWeek());
                        break;
                    
                    case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
                        
                    case 'month':
                        $query->whereDate('created_at', '>=', now()->startOfMonth())
                              ->whereDate('created_at', '<=', now()->endOfMonth());
                        break;
            
                    case 'year':
                        $query->whereDate('created_at', '>=', now()->startOfYear())
                              ->whereDate('created_at', '<=', now()->endOfYear());
                        break;
            
                    case 'custom':

                        if ($request->filled('from_date') && $request->filled('to_date')) {
                            $query->whereDate('created_at', '>=', $request->from_date)
                                  ->whereDate('created_at', '<=', $request->to_date);
                        }
                        break;
                }
            }
                if (!empty($status) && !in_array('all',$status)) {
                    $query->whereIn('status',$status);
                }
                
                if (!empty($vehicle_type) && !in_array('all',$vehicle_type)) {
                        $query->whereHas('vehicle.quality_check', function($zn) use ($vehicle_type) {
                            $zn->whereIn('vehicle_type', $vehicle_type);
                        });
                    }
                    
                    if (!empty($vehicle_model) && !in_array('all',$vehicle_model)) {
                        $query->whereHas('vehicle.quality_check', function($zn) use ($vehicle_model) {
                            $zn->whereIn('vehicle_model', $vehicle_model);
                        });
                    }
                    
                    if (!empty($vehicle_make) && !in_array('all',$vehicle_make)) {
                        $query->whereHas('vehicle.quality_check.vehicle_model_relation', function($zn) use ($vehicle_make) {
                            $zn->whereIn('make', $vehicle_make);
                        });
                    }
                    
                    if (!empty($city_id) && !in_array('all',$city_id)) {
                        $query->whereHas('VehicleRequest.city', function($ct) use ($city_id) {
                            $ct->whereIn('id', $city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if (!empty($zone_id) && !in_array('all',$zone_id)) {
                        $query->whereHas('VehicleRequest.zone', function($zn) use ($zone_id) {
                            $zn->whereIn('id', $zone_id);
                        });
                    }
                //updated by logesh
                    if (!empty($accountability_type) && !in_array('all',$accountability_type) ) {
                        $query->whereHas('VehicleRequest', function($zn) use ($accountability_type) {
                            $zn->whereIn('account_ability_type', $accountability_type);
                        });
                    }
                    if (!empty($customer_id) && !in_array('all',$customer_id)) {
                        $query->whereHas('rider.customerlogin.customer_relation', function($zn) use ($customer_id) {
                            $zn->whereIn('id', $customer_id);
                        });
                    }
                    
            // 🔹 Search across related fields
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', function ($v) use ($search) {
                        $v->where('permanent_reg_number', 'like', "%{$search}%")
                          ->orWhere('chassis_number', 'like', "%{$search}%")
                          ->orWhere('vehicle_type', 'like', "%{$search}%")
                          ->orWhere('model', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider.customerlogin.customer_relation', function ($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });
                     $q->whereHas('VehicleRequest.accountAbilityRelation', function($zn) use ($search) {
                            $zn->where('name', 'like', "%{$search}%");
                        });
                });
            }
            
            

            $allIds = $query->pluck('asset_vehicle_id')->unique();
            $totalRecords = $allIds->count();


            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();
            $uniqueDatas = $datas->unique('asset_vehicle_id')->values()->take($length);

            $formattedData = $uniqueDatas->map(function ($item) {
                $vehicle = $item->vehicle;
                $rider   = $item->rider;
                $vehicleRequest = $item->VehicleRequest;
                $contract_end_date = $item->rider->customerlogin->customer_relation->end_date ?? '';
                $contract_end_date_format = 'N/A';
                
                if (!empty($contract_end_date)) {
                    $contract_end_date_format = \Carbon\Carbon::parse($contract_end_date)->format('d M Y');
                }
                
                $statusBadge = '';

                    if ($item->status === 'running') {
                        $statusBadge = '<span class="badge-status badge-running">
                                            <i class="bi bi-check-circle"></i> Running
                                        </span>';
                    } elseif ($item->status === 'accident') {
                        $statusBadge = '<span class="badge-status badge-accident">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_1839_1951)">
                                                    <path d="M5.93766 15.8335C5.93766 16.708 5.22878 17.4169 4.35433 17.4169C3.47988 17.4169 2.771 16.708 2.771 15.8335M5.93766 15.8335C5.93766 14.9591 5.22878 14.2502 4.35433 14.2502C3.47988 14.2502 2.771 14.9591 2.771 15.8335M5.93766 15.8335H7.521C7.95823 15.8335 8.31266 15.4791 8.31266 15.0419V12.6783C8.31266 12.4227 8.18916 12.1828 7.98111 12.0342L5.54183 10.2919M2.771 15.8335H1.5835M5.54183 10.2919H1.5835M5.54183 10.2919L3.0074 6.67118C2.85924 6.45952 2.61712 6.33347 2.35876 6.3335L1.5835 6.33357" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M13.0625 15.8335C13.0625 16.708 13.7714 17.4169 14.6458 17.4169C15.5203 17.4169 16.2292 16.708 16.2292 15.8335M13.0625 15.8335C13.0625 14.9591 13.7714 14.2502 14.6458 14.2502C15.5203 14.2502 16.2292 14.9591 16.2292 15.8335M13.0625 15.8335H11.4792C11.0419 15.8335 10.6875 15.4791 10.6875 15.0419V12.6783C10.6875 12.4227 10.811 12.1828 11.0191 12.0342L13.4583 10.2919M16.2292 15.8335H17.4167M13.4583 10.2919L15.9928 6.67118C16.1409 6.45952 16.3831 6.33347 16.6414 6.3335L17.4167 6.33357M13.4583 10.2919H17.4167" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M7.52067 7.91678L5.5415 5.89446L7.12484 5.54178L6.01075 2.38045L8.70817 3.56262L9.8583 1.5835L10.6873 4.75012L13.4582 3.9453L11.9022 7.91686" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.89583 7.91683L9.5 6.3335" stroke="#DC2626" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_1839_1951">
                                                        <rect width="19" height="19" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            Accident
                                        </span>';
                    }elseif ($item->status === 'under_maintenance') { 
                                            $statusBadge = '<span class="badge-status badge-ticket" style="background-color:#dbeafe; color:#1d4ed8; border-color:#1d4ed8;">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6022 6.05671C15.7267 5.9323 15.9411 5.91962 16.0671 6.05715C16.8055 6.86252 17.2141 7.45886 17.3487 8.11926C17.4262 8.49926 17.4372 8.88599 17.3813 9.26219C17.2302 10.2788 16.4065 11.1026 14.759 12.75L12.7495 14.7595C11.1021 16.407 10.2784 17.2307 9.2617 17.3818C8.8855 17.4377 8.49877 17.4266 8.11877 17.3492C7.45844 17.2146 6.86216 16.8061 6.05693 16.0678C5.91923 15.9416 5.93196 15.727 6.05651 15.6025C6.75016 14.9088 6.71719 13.7513 5.9829 13.0169C5.2486 12.2826 4.09102 12.2497 3.39737 12.9434C3.27283 13.0679 3.05821 13.0806 2.93197 12.9429C2.19375 12.1377 1.78517 11.5414 1.65061 10.8811C1.57318 10.5011 1.56217 10.1143 1.61807 9.73814C1.76915 8.72148 2.59286 7.89773 4.24029 6.25031L6.24982 4.24078C7.89724 2.59335 8.72099 1.76964 9.73765 1.61855C10.1138 1.56266 10.5006 1.57367 10.8806 1.6511C11.541 1.78567 12.1373 2.19432 12.9427 2.93271C13.0802 3.05881 13.0676 3.27317 12.9431 3.39758C12.2494 4.09122 12.2825 5.24879 13.0167 5.9831C13.751 6.7174 14.9087 6.75036 15.6022 6.05671Z" stroke="#2563EB" stroke-width="1.1875" stroke-linejoin="round"/>
                        <path d="M15.0417 11.8752L7.125 3.9585" stroke="#2563EB" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> Under Maintenance
                      </span>';   
                        
                    }
                    elseif ($item->status === 'recovery_request') { 
                        
                        $recoveryStatus = $data->recovery_Request->created_by_type ?? null;
                           
             
                        $status_Text = 'Client Recovery Initiated';
                    
                    
                        // Conditional override
                        if ($recoveryStatus === 'b2b-admin-dashboard') {
                            $status_Text = 'GDM Recovery Initiated';
                            
                        }
                $statusBadge = '<span class="badge-status badge-gdm-init '.$recoveryStatus.'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>'.$status_Text.'</span>';   
                        
                    }
                    
                    elseif ($item->status === 'return_request') { 
                    $statusBadge = '
                        <span class="badge-status d-inline-flex align-items-center px-2 py-1" 
                              style="background-color:#EEE9CA; font-size:14px; font-weight:500; gap:6px; line-height:1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 23 23" fill="none">
                                <rect width="24" height="24" rx="8" fill="#EEE9CA"/>
                                <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                                <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                                <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Return Request
                        </span>';
  
                        
                    }
                    
                    elseif ($item->status === 'recovered') { 
                    $statusBadge = '<span class="badge-status badge-gdm-init">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>
                         Recovered</span>';   
                        
                    }
                    
                    else {
                        $statusBadge = '<span class="badge-status badge-default">Unknown</span>';
                    }
                    
                $RRcolor = ($item->status === 'recovery_request') ? '#F87171' : '#A1DBD0';
                $Reactcolor = ($item->status === 'recovery_request') ? '#f4f8f7ff' : '#14A388';
                $RR_Text = ($item->status === 'recovery_request') ? 'You have already recovery requested' : 'Create Recovery Request';
                $RR_route = ($item->status === 'recovery_request') ? 'javascript:void(0);' : route('b2b.admin.deployed_asset.recovery_request', encrypt($item->id));
                return [
                    '<div class="form-check">
                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                               name="is_select[]" type="checkbox" value="'.$item->id.'">
                    </div>',
                    e($vehicleRequest->req_id ?? 'N/A'),// Updated By Gowtham
                    e($vehicleRequest->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                    e($vehicle->permanent_reg_number ?? 'N/A'),
                    e($vehicle->chassis_number ?? 'N/A'),
                    e($vehicle->vehicle_type_relation->name ?? 'N/A'),
                    e($vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'),
                    e($vehicle->vehicle_model_relation->make ?? 'N/A'),
                    e($rider->name ?? 'N/A'),
                    e($rider->mobile_no ?? 'N/A'),
                    e($rider->customerlogin->customer_relation->trade_name ?? 'N/A'),
                    e($vehicleRequest->city->city_name ?? 'N/A'),
                    e($vehicleRequest->zone->name ?? 'N/A'),
                    $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') : 'N/A',
                    $statusBadge,
                    $contract_end_date_format,
                    '<div class="d-flex justify-content-between align-items-center gap-2">
                        <a href="'.route('b2b.admin.deployed_asset.deployed_asset_view', encrypt($item->id)).'"
                            class="d-flex align-items-center justify-content-center border-0" title="View"
                            style="background-color:#CAEDE7;color:#0F5847;border-radius:8px;width:33px;height:33px;">
                            <i class="bi bi-eye fs-5"></i>
                        </a>
                        <a href="'.$RR_route.'" class="cursor-pointer" title="'.$RR_Text.'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 27 27" fill="none">
                                <rect width="27" height="27" rx="8" fill="'.$RRcolor.'"></rect>
                                <path d="M8.68754 13.5C8.68754 13.7317 8.70404 13.9641 8.73636 14.1916L7.37511 14.3862C7.33305 14.0927 7.31214 13.7965
                                    7.31254 13.5C7.31254 10.0879 10.0887 7.3125 13.5 7.3125C14.9046 7.3125 16.2803 7.7965 17.3741 8.67444L16.5127 9.74694C15.6605
                                    9.05723 14.5963 8.68299 13.5 8.6875C10.8463 8.6875 8.68754 10.8463 8.68754 13.5ZM8.00004 16.25C8.00004 16.4323 8.07248 16.6072
                                    8.20141 16.7361C8.33034 16.8651 8.50521 16.9375 8.68754 16.9375C8.86988 16.9375 9.04475 16.8651 9.17368 16.7361C9.30261 16.6072
                                    9.37504 16.4323 9.37504 16.25C9.37504 16.0677 9.30261 15.8928 9.17368 15.7639C9.04475 15.6349 8.86988 15.5625 8.68754
                                    15.5625C8.50521 15.5625 8.33034 15.6349 8.20141 15.7639C8.07248 15.8928 8.00004 16.0677 8.00004 16.25ZM13.5
                                    5.25C18.0492 5.25 21.75 8.95081 21.75 13.5H23.125C23.125 8.1925 18.8075 3.875 13.5 3.875C12.3705 3.875 11.2636
                                    4.06888 10.2104 4.45181L10.6806 5.74431C11.5844 5.41651 12.5386 5.24923 13.5 5.25ZM17.625 10.75C17.625 10.9323
                                    17.6975 11.1072 17.8264 11.2361C17.9553 11.3651 18.1302 11.4375 18.3125 11.4375C18.4949 11.4375 18.6697 11.3651
                                    18.7987 11.2361C18.9276 11.1072 19 10.9323 19 10.75C19 10.5677 18.9276 10.3928 18.7987 10.2639C18.6697 10.1349 
                                    18.4949 10.0625 18.3125 10.0625C18.1302 10.0625 17.9553 10.1349 17.8264 10.2639C17.6975 10.3928 17.625 10.5677
                                    17.625 10.75ZM8.68754 6.625C8.86988 6.625 9.04475 6.55257 9.17368 6.42364C9.30261 6.2947 9.37504 6.11984 9.37504
                                    5.9375C9.37504 5.75516 9.30261 5.5803 9.17368 5.45136C9.04475 5.32243 8.86988 5.25 8.68754 5.25C8.50521 5.25 
                                    8.33034 5.32243 8.20141 5.45136C8.07248 5.5803 8.00004 5.75516 8.00004 5.9375C8.00004 6.11984 8.07248 6.2947
                                    8.20141 6.42364C8.33034 6.55257 8.50521 6.625 8.68754 6.625ZM5.25004 13.5C5.25004 11.2966 6.10804 9.22444
                                    7.66661 7.66656L6.69379 6.69375C5.79699 7.58531 5.08606 8.646 4.6022 9.81434C4.11834 10.9827 3.87118 12.2354 
                                    3.87504 13.5C3.87504 18.8075 8.19254 23.125 13.5 23.125V21.75C8.95086 21.75 5.25004 18.0492 5.25004
                                    13.5ZM22.0938 20.0312C22.0938 21.1684 21.1684 22.0938 20.0313 22.0938C18.8942 22.0938 17.9688 21.1684
                                    17.9688 20.0312C17.9688 19.7136 18.0472 19.4166 18.175 19.1478L14.3835 15.3556C14.1154 15.4841 13.8177
                                    15.5625 13.5 15.5625C12.3629 15.5625 11.4375 14.6371 11.4375 13.5C11.4375 12.3629 12.3629 11.4375 13.5
                                    11.4375C14.6372 11.4375 15.5625 12.3629 15.5625 13.5C15.5625 13.8176 15.4849 14.1146 15.3563
                                    14.3834L19.1479 18.1757C19.416 18.0471 19.7137 17.9688 20.0313 17.9688C21.1684 17.9688 22.0938 
                                    18.8941 22.0938 20.0312ZM13.5 14.1875C13.8789 14.1875 14.1875 13.8788 14.1875 13.5C14.1875 13.1212
                                    13.8789 12.8125 13.5 12.8125C13.1212 12.8125 12.8125 13.1212 12.8125 13.5C12.8125 13.8788 13.1212
                                    14.1875 13.5 14.1875ZM20.7188 20.0312C20.7187 19.8488 20.6461 19.6739 20.5171 19.545C20.388 19.416
                                    20.213 19.3437 20.0306 19.3438C19.8482 19.3438 19.6733 19.4164 19.5443 19.5455C19.4154 19.6745
                                    19.343 19.8495 19.3431 20.0319C19.3432 20.2144 19.4158 20.3893 19.5448 20.5182C19.6739 20.6471
                                    19.8489 20.7195 20.0313 20.7194C20.2137 20.7193 20.3886 20.6468 20.5176 20.5177C20.6465 20.3887
                                    20.7189 20.2137 20.7188 20.0312Z" fill="'.$Reactcolor.'">
                                </path>
                            </svg>
                        </a>
                    </div>
                    '
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Assigned Vehicle List Error: '.$e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    $cities = City::where('status',1)->get();
    $accountability_types = EvTblAccountabilityType::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
    $vehicle_types = VehicleType::where('is_active', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    $vehicle_models = VehicleModelMaster::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
    $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
    return view('b2badmin::deployed_asset.list', compact('cities','accountability_types','customers' , 'vehicle_types' , 'vehicle_models','vehicle_makes'));
}
    
    
    public function deployed_asset_view(Request $request,$id)
    {
        $request_id = decrypt($id);
        $data = B2BVehicleAssignment::where('id',$request_id)->with(['vehicle', 'rider.customerlogin.customer_relation','VehicleRequest'])->orderBy('id', 'desc')->first();
        
        $current_status = AssetVehicleInventory::with('inventory_location')
            ->where('asset_vehicle_id', $data->vehicle->id)
            ->where('asset_vehicle_status', 'accepted')
            ->first()->inventory_location->name ?? null;
            
        
        return view('b2badmin::deployed_asset.view',compact('data' ,'current_status'));
    }

    public function deployed_asset_recovery_request(Request $request , $id)//updated by Gowtham.S
    {
        
        $decrypt_id = decrypt($id);
        $data = B2BVehicleAssignment::with('vehicle' ,'vehicleRequest','rider.customerLogin.customer_relation') 
            ->where('id', $decrypt_id)
            ->first();
        if($data->status == 'recovery_request'){
            return back()->with('warning','You have already recovery requested');
        }
            
        return view('b2badmin::deployed_asset.vh_create_recovery_request',compact('data'));
        
    }
    
    public function uploadFile($file, $directory) //updated by Gowtham.S
    {
        $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName;
    }
    
    public function store_recovery_request(Request $request) //updated by Gowtham.S
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Unauthenticated!',
            ], 401);
        }
        

            
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
           
            // 'datetime'             => 'required|date',
            'city_id'              =>'required|integer',
            'zone_id'              =>'required|integer',
            'reason_for_recovery'  => 'required|string',
            'vehicle_number'       => 'required|string|max:255',
            'chassis_number'       => 'nullable|string|max:255',
            'rider_id'             => 'nullable|string|max:255',
            'rider_name'           => 'nullable|string|max:255',
            'client_business_name' => 'nullable|string|max:255',
            'contact_person_name'  => 'nullable|string|max:255',
            'contact_no'           => 'nullable|string|max:20',
            'contact_email'        => 'nullable|email|max:255',
            'description'          => 'nullable|string',
            'terms_condition'      => 'accepted',
            'reason_for_recovery_txt'=>'required', //updated by Gowtham.S
            // 'files.*'              => 'nullable|mimes:jpg,jpeg,png,pdf,mp4,mov,avi|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        

         try {
                DB::beginTransaction();
        
                $uploadedFiles = [];
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $uploadedFiles[] = $this->uploadFile($file, 'b2b/recovery_request');
                    }
                }
        
                $recovery = new B2BRecoveryRequest();
                $recovery->assign_id          = $request->id;
                $recovery->city_id            = $request->city_id;
                $recovery->zone_id            = $request->zone_id; 
                $recovery->reason             = $request->reason_for_recovery;
                $recovery->vehicle_number     = $request->vehicle_number;
                $recovery->chassis_number     = $request->chassis_number;
                $recovery->rider_id           = $request->rider_id;
                $recovery->rider_name         = $request->rider_name;
                $recovery->client_name        = $request->client_business_name;
                $recovery->rider_mobile_no    = $request->rider_mobile_no;
                $recovery->contact_no         = $request->contact_no;
                $recovery->contact_email      = $request->contact_email;
                $recovery->description        = $request->description;
                $recovery->terms_condition    = $request->has('terms_condition') ? 1 : 0;
                $recovery->created_by         = $user->id;
                $recovery->created_by_type    = 'b2b-admin-dashboard';
                // $recovery->accident_photos    = json_encode($uploadedFiles);

                $recovery->save();
        
                $assignment = B2BVehicleAssignment::find($request->id);
                
                if ($assignment) {
                    
                    $assignment->update(['status' => 'recovery_request']);
                    
                    
                   $inventory = AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)->first();


                    $from_location_source = $inventory ? $inventory->transfer_status : null; 
                    
                                   
                    AssetVehicleInventory::where('asset_vehicle_id', $assignment->asset_vehicle_id)
                            ->update(['transfer_status' => 28]);
                            
                                        
                    $remarks = "Inventory status updated to 'Recovery Pending' due to GDM recovery request.";
                
                    // // Log this inventory action
                    VehicleTransferChassisLog::create([
                        'chassis_number' => $assignment->vehicle->chassis_number,
                        'from_location_source' => $from_location_source,
                        'to_location_destination' => 28,
                        'vehicle_id'     => $assignment->vehicle->id,
                        'status'         => 'updated',
                        'remarks'        => $remarks,
                        'created_by'     => $user->id,
                        'type'           => 'b2b-web-dashboard'
                    ]);
        
                    // $vehicle_request = B2BVehicleRequests::where('req_id', $assignment->req_id)
                    //     // ->where('is_active', 1)
                    //     ->first();
        
                    // if ($vehicle_request) {
                    //     $vehicle_request->update(['is_active' => 0]);
                    // }
                }
        
                B2BVehicleAssignmentLog::create([
                    'assignment_id'   => $request->id,
                    'status'          => 'opened',
                    'remarks'         => "Vehicle {$request->vehicle_number} has been requested for recovery",
                    'action_by'       => $user->id,
                    'type'            => 'b2b-admin-dashboard',
                    'request_type'    => 'recovery_request',
                    'request_type_id' => $recovery->id,
                ]);
                
                RecoveryComment::create([
                    'req_id'    => $recovery->id,
                    'status'    => 'opened',
                    'comments'  => "Vehicle {$request->vehicle_number} has been requested for recovery",
                    'user_id'   => $user->id ?? null,
                    'user_type' => 'b2b-admin-dashboard',
                ]);
        
                DB::commit();
                
                $requestID = $assignment->req_id;
                $rider_id = $assignment->rider_id;
                $vehicle_id = $assignment->asset_vehicle_id;
                $tc_create_type = 'b2b-admin-dashboard';
                $recoveryInfo = [
                    'recovery_reason' => $request->reason_for_recovery_txt,
                    'recovery_description' => $request->description
                ];
                
                if(!empty($requestID)){
                    RecoveryNotifyHandler::AutoSendRecoveryRequestEmail($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type);
                    // RecoveryNotifyHandler::AutoSendRecoveryRequestWhatsApp($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type);
                    \App\Jobs\SendRecoveryWhatsAppJob::dispatch(
                        $requestID,
                        $rider_id,
                        $vehicle_id,
                        $recoveryInfo,
                        $tc_create_type
                    );
                }
                
                $user     = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $cityName = $assignment->vehicleRequest->city->city_name ?? "Unknown City";
                $zoneName = $assignment->vehicleRequest->zone->name ?? "Unknown Zone";
                $reason   = $recovery->recovery_reason_relation->label_name;
                $vno      = $request->vehicle_number;
        
                $shortDescription = "Recovery Request #{$requestID} Created for Vehicle {$vno}";
                

                $longDescription =
                    "A recovery request (#{$requestID}) has been created for vehicle {$vno} "
                    . "in {$cityName} / {$zoneName}. "
                    . "Reason: {$reason}. "
                    . "Created by {$user->name} ({$roleName}).";
        
                audit_log_after_commit([
                    'module_id'         => 5,
                    'short_description' => $shortDescription,
                    'long_description'  => $longDescription,
                    'role'              => $roleName,
                    'user_id'           => $user->id,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'b2badmin.recovery_request_create',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
       
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Recovery Request submitted successfully!',
                    'data'    => $recovery,
                ], 200);
        
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Recovery Request Error: '.$e->getMessage());
        
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Something went wrong while submitting the recovery request.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
    }
    
    
    public function deployment_list(Request $request)
    {
        if ($request->ajax()) {
            try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');
            
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone_id ?? [];
                $city_id = (array)$request->city_id ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $status = (array)$request->status ?? [];
                $customer_id = (array)$request->customer_id ?? [];
                
            $date_filter     = $request->date_filter ?? null;
           
            $query = B2BVehicleRequests::with('rider','zone','city');
        

            if (!empty($date_filter)) {
                switch ($date_filter) {
            
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
            
                    case 'week':
                        $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;
                     case 'last_15_days':
                        $query->whereMonth('created_at', now()->subDays(14)->startOfDay())
                              ->whereYear('created_at', now()->endOfDay());
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
            
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }
            
            if (!empty($status) && !in_array('all',$status)) {
                    $query->whereIn('status',$status);
                }
            
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            
            if (!empty($vehicle_type) && !in_array('all',$vehicle_type)) {
                $query->whereIn('vehicle_type', $vehicle_type);
            }

        
            if (!empty($city_id) && !in_array('all',$city_id)) {
                $query->whereIn('city_id', $city_id);
            }
            
            if (!empty($zone_id) && !in_array('all',$zone_id)) {
                $query->whereIn('zone_id', $zone_id);
            }
            
            //updated by logesh
             if (!empty($accountability_type) && !in_array('all',$accountability_type)) {
                       
                $query->whereIn('account_ability_type', $accountability_type);
            }
            //updated by logesh
            if (!empty($customer_id) && !in_array('all',$customer_id)) {
                        $query->whereHas('rider.customerlogin.customer_relation', function($zn) use ($customer_id) {
                            $zn->whereIn('id', $customer_id);
                        });
                    }
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    // Request ID, status, dates
                    $q->where('req_id', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereDate('created_at', $search)
                      ->orWhereDate('updated_at', $search);
            
                    // Rider fields
                    $q->orWhereHas('rider', function($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            
                    // Client
                    $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });
            
                    // City
                    $q->orWhereHas('city', function($c) use ($search) {
                        $c->where('city_name', 'like', "%{$search}%");
                    });
            
                    // Zone
                    $q->orWhereHas('zone', function($z) use ($search) {
                        $z->where('name', 'like', "%{$search}%");
                    });
                    //updateb by logesh
                    $q->orWhereHas('accountAbilityRelation', function($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
                });
            }

            $totalRecords = $query->count();

            if ($length == -1) {
                $length = $totalRecords;
            }

            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();


            $formattedData = $datas->map(function ($item) {
                $statusColumn = '';
                if ($item->status === 'pending') {
                    $statusColumn = '
                        <span style="background-color:#CAEDCE; color:#155724;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-x-circle me-1"></i> Opened
                        </span>';
                } elseif ($item->status === 'completed') {
                    $statusColumn = '
                        <span style="background-color:#EECACB; color:#721c24;" class="px-2 py-1 rounded-pill">
                            <i class="bi bi-check-circle me-1"></i> Closed
                        </span>';
                }


                if ($item->status === 'completed' && $item->completed_at) {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $completed = \Carbon\Carbon::parse($item->completed_at);
                    $diffInDays = $created->diffInDays($completed);
                    $diffInHours = $created->diffInHours($completed);
                    $diffInMinutes = $created->diffInMinutes($completed);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                } else {
                    $created   = \Carbon\Carbon::parse($item->created_at);
                    $now       = now();
                    $diffInDays = $created->diffInDays($now);
                    $diffInHours = $created->diffInHours($now);
                    $diffInMinutes = $created->diffInMinutes($now);
                
                    if ($diffInDays > 0) {
                        $aging = $diffInDays . ' days';
                    } elseif ($diffInHours > 0) {
                        $aging = $diffInHours . ' hours';
                    } else {
                        $aging = $diffInMinutes . ' mins';
                    }
                }

                
                
                $rider = $item->rider;
                $requestId = $item->req_id;
                $idEncode = encrypt($item->id); // for route link

                return [
                    '<div class="form-check">
                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="'.$item->id.'">
                    </div>',
                    $requestId,
                    e($item->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                    e($rider->name ?? ''),
                    e($rider->mobile_no ?? ''),
                    e($item->rider->customerlogin->customer_relation->trade_name ?? 'N/A'), 
                    e($item->city->city_name ?? ''),
                    e($item->zone->name ?? ''),
                    \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A'),
                    \Carbon\Carbon::parse($item->completed_at)->format('d M Y, h:i A'),
                     $aging, 
                    $statusColumn,
                    '<a href="'.route('b2b.admin.deployment_request.deployment_view', $idEncode).'"
                        class="d-flex align-items-center justify-content-center border-0" title="view"
                        style="background-color:#CAEDCE;color:#155724;border-radius:8px;width:35px;height:31px;">
                        <i class="bi bi-eye fs-5"></i>
                    </a>'
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $formattedData
            ]);
            } catch (\Exception $e) {
                \Log::error('Vehicle Request List Error: '.$e->getMessage());
    
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
        $cities = City::where('status',1)->get();
        $accountability_types = EvTblAccountabilityType::where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
        $customers = CustomerMaster::select('id','trade_name')->where('status', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
            $vehicle_types = VehicleType::where('is_active', 1) //updated by logesh
        ->orderBy('id', 'desc')
        ->get();
        
        return view('b2badmin::deployed_asset.deployed_list' , compact('cities','accountability_types','customers' , 'vehicle_types'));
    }
    
    
    public function deployment_view(Request $request ,$id)
    {
        $request_id = decrypt($id);
       
        $data = B2BVehicleRequests::with('assignment','rider','agent')->where('id', $request_id)
                ->first();
                
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        
    
        $locations = City::where('status',1)->get();
        $passed_chassis_numbers = AssetMasterVehicle::where('qc_status','pass')->get();
        $vehicle_models = VehicleModelMaster::where('status', 1)->get();
        
        $financing_types = FinancingTypeMaster::where('status',1)->get();
        $asset_ownerships = AssetOwnershipMaster::where('status',1)->get();
        $insurer_names = InsurerNameMaster::where('status',1)->get();
        $insurance_types = InsuranceTypeMaster::where('status',1)->get();
        $hypothecations = HypothecationMaster::where('status',1)->get();
        $registration_types = RegistrationTypeMaster::where('status',1)->get();
        $inventory_locations = InventoryLocationMaster::where('status',1)->get();
        $telematics = TelemetricOEMMaster::where('status',1)->get();
        $colors = ColorMaster::where('status',1)->get();
        
        $current_status = null;
        
        if ($data && $data->assignment && $data->assignment->vehicle) {
            $inventory = AssetVehicleInventory::with('inventory_location')
                ->where('asset_vehicle_id', $data->assignment->vehicle->id)
                ->where('asset_vehicle_status', 'accepted')
                ->first();
        
            if ($inventory) {
                $current_status = $inventory->transfer_status;
            }
        }

        
            
        return view('b2badmin::deployed_asset.deployed_view' , compact('data' , 'current_status' ,'vehicle_types','locations','passed_chassis_numbers' ,'financing_types' ,'asset_ownerships' ,'insurer_names' ,'insurance_types' ,'hypothecations' ,'registration_types' ,'inventory_locations', 'vehicle_models' ,'telematics' ,'colors'));
    }
    
    
    public function accident_view(Request $request , $id)
    {
        $accident_id = decrypt($id);
       
        $data = B2BReportAccident::with('rider','logs')->where('id', $accident_id)
                ->first();
            
        return view('b2badmin::deployed_asset.accident_view',compact('data','id'));
    }
    
         public function accident_list(Request $request, $id)
{
    if ($request->ajax()) {
        try {
            $start  = $request->input('start', 0);
            $length = $request->input('length', 25);
            $search = $request->input('search.value');

            // Base query
            $query = B2BReportAccident::where('assign_id', $id)->with([
                'rider',
                'assignment',
                'assignment.vehicle',
                'assignment.VehicleRequest',
                'assignment.VehicleRequest.city',
                'assignment.VehicleRequest.zone',
                'rider.customerlogin.customer_relation'
            ]);

            // Total count without filters
            $totalRecords = $query->count();

            // Apply filters
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            if ($request->filled('city_id')) {
                $query->whereHas('assignment.VehicleRequest.city', function ($ct) use ($request) {
                    $ct->where('id', $request->city_id);
                });
            }

            if ($request->filled('zone_id')) {
                $query->whereHas('assignment.VehicleRequest.zone', function ($zn) use ($request) {
                    $zn->where('id', $request->zone_id);
                });
            }
            
            //updated by logesh
             if ($request->filled('accountability_type')) {
                        $query->whereHas('VehicleRequest', function($zn) use ($request) {
                            $zn->where('account_ability_type', $request->accountability_type);
                        });
                    }
                    
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhereDate('created_at', $search)
                      ->orWhereDate('updated_at', $search);

                    $q->orWhereHas('assignment.VehicleRequest', function ($vr) use ($search) {
                        $vr->where('req_id', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                        $v->where('permanent_reg_number', 'like', "%{$search}%")
                          ->orWhere('chassis_number', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider', function ($r) use ($search) {
                        $r->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile_no', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('rider.customerlogin.customer_relation', function ($c) use ($search) {
                        $c->where('trade_name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.VehicleRequest.city', function ($ct) use ($search) {
                        $ct->where('city_name', 'like', "%{$search}%");
                    });

                    $q->orWhereHas('assignment.VehicleRequest.zone', function ($zn) use ($search) {
                        $zn->where('name', 'like', "%{$search}%");
                    });
                    $q->orWhereHas('assignment.VehicleRequest.accountAbilityRelation', function ($zn) use ($search) {
                        $zn->where('name', 'like', "%{$search}%");
                    });
                });
            }

            // Count after filters
            $filteredRecords = $query->count();

            if ($length == -1) {
                $length = $filteredRecords;
            }

            // Pagination
            $datas = $query->orderBy('id', 'desc')
                           ->skip($start)
                           ->take($length)
                           ->get();

            // Format
            $formattedData = $datas->map(function ($item, $key) use ($start) {
                $statuses = [
                    'claim_initiated' => ['label' => 'Claim Initiated', 'colors' => ['#EDCACA', '#580F0F']],
                    'insurer_visit_confirmed' => ['label' => 'Insurer Visit Confirmed', 'colors' => ['#EDE0CA', '#58490F']],
                    'inspection_completed' => ['label' => 'Inspection Completed', 'colors' => ['#DEEDCA', '#56580F']],
                    'approval_pending' => ['label' => 'Approval Pending', 'colors' => ['#CAEDCE', '#1E580F']],
                    'repair_started' => ['label' => 'Repair Started', 'colors' => ['#CAEDE7', '#0F5847']],
                    'repair_completed' => ['label' => 'Repair Completed', 'colors' => ['#CAE7ED', '#0F4858']],
                    'invoice_submitted' => ['label' => 'Invoice Submitted', 'colors' => ['#CAD2ED', '#1A0F58']],
                    'payment_approved' => ['label' => 'Payment Approved', 'colors' => ['#EDCAE3', '#580F4B']],
                    'claim_closed' => ['label' => 'Claim Closed', 'colors' => ['#EDE9CA', '#584F0F']],
                ];

                $status = $item->status ?? 'N/A';
                $label  = $statuses[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status));
                $colors = $statuses[$status]['colors'] ?? ['#ddd', '#333'];

                $statusColumn = '<span style="background-color:'.$colors[0].'; color:'.$colors[1].'; border:'.$colors[1].' 1px solid" class="px-2 py-1 rounded-pill">'
                                .e($label).'</span>';

                $requestId    = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                $description  = data_get($item, 'description', 'N/A');
                $accidentType = data_get($item, 'accident_type', 'N/A');
                $createdAt    = $item->created_at ? $item->created_at->format('d M Y, h:i A') : '';
                $idEncode     = encrypt($item->id);

                $actions = '
                    <div class="d-flex align-items-center gap-2">
                        <a title="View Ticket Details" href="'.route('b2b.admin.deployed_asset.accident_view', $idEncode).'"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:40px; height:40px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>
                ';

                return [
                    $start + $key + 1,
                     e($requestId),
                     e($item->assignment->VehicleRequest->accountAbilityRelation->name ?? 'N/A'), //updated by logesh
                    e($description),
                    e($accidentType),
                    e($item->assignment->VehicleRequest->city->city_name ?? ''),
                    e($item->assignment->VehicleRequest->zone->name ?? ''),
                    $createdAt,
                    $statusColumn,
                    $actions,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Accident List Error: '.$e->getMessage().' on line '.$e->getLine());

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Something went wrong: '.$e->getMessage()
            ], 500);
        }
    }

    $cities = City::where('status', 1)->get();
    return view('b2badmin::accident.list', compact('cities'));
}
    
    public function export_deploymet_request(Request $request)
{
    // Multi-value filters
    $fields    = $request->input('fields', []);  
    $from_date = $request->input('from_date');
    $to_date   = $request->input('to_date');
    $zone      = $request->input('zone', []);               // ARRAY
    $status    = $request->input('status', []);             // ARRAY
    $city      = $request->input('city', []);               // ARRAY
    $accountability_type = $request->input('accountability_type', []); // ARRAY
    $customer_id = $request->input('customer_id', []);      // ARRAY
    $vehicle_type = $request->input('vehicle_type', []);    // ARRAY
    $datefilter = $request->input('datefilter') ?? null;
    $selectedIds = $request->input('selected_ids', []);

    if (empty($fields)) {
        return back()->with('error', 'Please select at least one field to export.');
    }

    // -------------------------------
    // FORMAT SELECTED FIELDS
    // -------------------------------
    $formattedFields = [];

    foreach ($fields as $item) {

        $name = is_array($item)
            ? ($item['name'] ?? $item['field'] ?? reset($item))
            : $item;

        if (!is_string($name) || trim($name) === '') continue;

        $clean = ucwords(strtolower(str_replace('_', ' ', $name)));

        // manual mapping
        $manual = [
            'Date Time' => 'Date & Time',
            'Qc Checklist' => 'QC Checklist',
            'Id' => 'ID',
        ];

        if (isset($manual[$clean])) {
            $clean = $manual[$clean]; 
        }

        $formattedFields[] = $clean;
    }

    $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // -------------------------------
    // FRIENDLY NAMES FOR MULTIPLE VALUES
    // -------------------------------

    $zoneNames = Zones::whereIn('id', (array)$zone)->pluck('name')->toArray();
    $cityNames = City::whereIn('id', (array)$city)->pluck('city_name')->toArray();
    $customerNames = CustomerMaster::whereIn('id', (array)$customer_id)->pluck('trade_name')->toArray();
    $accountabilityNames = EvTblAccountabilityType::whereIn('id', (array)$accountability_type)->pluck('name')->toArray();
    $vehicleTypeNames = VehicleType::whereIn('id', (array)$vehicle_type)->pluck('name')->toArray();

    // -------------------------------
    // CREATE FILTER SUMMARY
    // -------------------------------

    $appliedFilters = [];

    if (!empty($status)) $appliedFilters[] = 'Status: ' . implode(', ', $status);
    if (!empty($from_date)) $appliedFilters[] = 'From: ' . $from_date;
    if (!empty($to_date)) $appliedFilters[] = 'To: ' . $to_date;

    if (!empty($zoneNames)) $appliedFilters[] = 'Zone: ' . implode(', ', $zoneNames);
    if (!empty($cityNames)) $appliedFilters[] = 'City: ' . implode(', ', $cityNames);
    if (!empty($accountabilityNames)) $appliedFilters[] = 'Accountability Type: ' . implode(', ', $accountabilityNames);
    if (!empty($customerNames)) $appliedFilters[] = 'Customer: ' . implode(', ', $customerNames);
    if (!empty($vehicleTypeNames)) $appliedFilters[] = 'Vehicle Type: ' . implode(', ', $vehicleTypeNames);

    if (!empty($datefilter)) {
        $appliedFilters[] = 'Date Range: ' . $datefilter;
    }

    $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);

    $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', $selectedIds);

    // -------------------------------
    // AUDIT LOG
    // -------------------------------
    $fileName = 'Deployment-request-list-' . date('d-m-Y') . '.csv';
    $user = Auth::user();
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

    audit_log_after_commit([
        'module_id'         => 5,
        'short_description' => 'B2B Admin Deployment Request Export Initiated',
        'long_description'  => "User exported Deployment Request. File: {$fileName}. Fields: {$fieldsText} | Filters: {$filtersText} | Selected IDs: {$selectedIdsText}.",
        'role'              => $roleName,
        'user_id'           => Auth::id(),
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'deployment_request.export',
        'ip_address'        => $request->ip(),
        'user_device'       => $request->userAgent()
    ]);

    // -------------------------------
    // EXPORT EXCEL
    // -------------------------------
    return Excel::download(
        new B2BAdminDeploymentRequestExport(
            $from_date,
            $to_date,
            $selectedIds,
            $fields,
            $city,            // ARRAY
            $zone,            // ARRAY
            $status,          // ARRAY
            $accountability_type,  // ARRAY
            $customer_id,     // ARRAY
            $vehicle_type,    // ARRAY
            $datefilter
        ),
        $fileName
    );
}
        
        // public function export_deploymet_request(Request $request)
        //     {
            
        //     $filters = $request->all();
    
        //     return Excel::download(
        //         new B2BAdminDeploymentRequestExport($filters),
        //         'deployment_requests.xlsx'
        //     );
        //     }
        
    public function export_deployed_list(Request $request)
    {
    
        $user     = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        // Ensure ALL multiple filters are arrays
        $fields              = (array) $request->input('fields', []);
        $from_date           = $request->input('from_date');
        $to_date             = $request->input('to_date');
        $zone                = (array) $request->input('zone_id', []);
        $status              = (array) $request->input('status', []);
        $city                = (array) $request->input('city_id', []);
        $customer_id         = (array) $request->input('customer_id', []);
        $accountability_type = (array) $request->input('accountability_type', []);
        $vehicle_type        = (array) $request->input('vehicle_type', []);
        $vehicle_model       = (array) $request->input('vehicle_model', []);
        $vehicle_make        = (array) $request->input('vehicle_make', []);
        $selectedIds         = (array) $request->input('selected_ids', []);
        $date_filter         = $request->input('date_filter') ?? null;
    
        // Validation
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
    
        // -----------------------------------------
        // GET FRIENDLY NAMES FOR FILTER VALUES
        // -----------------------------------------
        $zoneNames = Zones::whereIn('id', $zone)->pluck('name')->toArray();
        $cityNames = City::whereIn('id', $city)->pluck('city_name')->toArray();
        $customerNames = CustomerMaster::whereIn('id', $customer_id)->pluck('trade_name')->toArray();
        $accountabilityNames = EvTblAccountabilityType::whereIn('id', $accountability_type)->pluck('name')->toArray();
        $vehicleTypeNames = VehicleType::whereIn('id', $vehicle_type)->pluck('name')->toArray();
        $vehicleModelNames = VehicleModelMaster::whereIn('id', $vehicle_model)->pluck('vehicle_model')->toArray();
        $vehicleMakeNames  = VehicleModelMaster::whereIn('make', $vehicle_make)->pluck('make')->toArray();
    
        // -----------------------------------------
        // BUILD FILTER SUMMARY (HUMAN READABLE)
        // -----------------------------------------
        $appliedFilters = [];
    
        if ($from_date) $appliedFilters[] = "From Date: $from_date";
        if ($to_date)   $appliedFilters[] = "To Date: $to_date";
    
        if (!empty($status))        $appliedFilters[] = "Status: " . implode(", ", $status);
        if (!empty($cityNames))     $appliedFilters[] = "City: " . implode(", ", $cityNames);
        if (!empty($zoneNames))     $appliedFilters[] = "Zone: " . implode(", ", $zoneNames);
        if (!empty($customerNames)) $appliedFilters[] = "Customer: " . implode(", ", $customerNames);
        if (!empty($accountabilityNames)) $appliedFilters[] = "Accountability Type: " . implode(", ", $accountabilityNames);
        if (!empty($vehicleTypeNames))  $appliedFilters[] = "Vehicle Type: " . implode(", ", $vehicleTypeNames);
        if (!empty($vehicleModelNames)) $appliedFilters[] = "Vehicle Model: " . implode(", ", $vehicleModelNames);
        if (!empty($vehicleMakeNames))  $appliedFilters[] = "Vehicle Make: " . implode(", ", $vehicleMakeNames);
    
        if (!empty($date_filter)) $appliedFilters[] = "Date Range: $date_filter";
    
        $filtersList = empty($appliedFilters) ? "None" : implode("; ", $appliedFilters);
    
        // -----------------------------------------
        // CLEAN FIELD LIST
        // -----------------------------------------
        $cleanFields = array_map(function ($f) {
            return ucwords(str_replace('_', ' ', $f));
        }, $fields);
    
        $fieldsList = implode(', ', $cleanFields);
    
        // -----------------------------------------
        // AUDIT LOG ENTRY
        // -----------------------------------------
        $longDescription = "{$user->name} ({$roleName}) exported the deployed asset list. "
            . "Fields: {$fieldsList}. "
            . "Filters: {$filtersList}.";
    
        audit_log_after_commit([
            'module_id'         => 5,
            'short_description' => "Exported deployed asset list",
            'long_description'  => $longDescription,
            'role'              => $roleName,
            'user_id'           => $user->id,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'b2badmin.export_deployed_asset_list',
            'ip_address'        => $request->ip(),
            'user_device'       => $request->userAgent()
        ]);
    
        // -----------------------------------------
        // DOWNLOAD EXCEL
        // -----------------------------------------
        return Excel::download(
            new B2BAdminDeployedAssetExport(
                $from_date,
                $to_date,
                $selectedIds,
                $fields,
                $city,
                $zone,
                $status,
                $accountability_type,
                $customer_id,
                $vehicle_type,
                $vehicle_model,
                $vehicle_make,
                $date_filter
            ),
            'Deployed-asset-list-' . date('d-m-Y') . '.csv'
        );
    }


    //  public function export_deploymet_request(Request $request)
    // {
        

    //     $fields    = $request->input('fields', []);  
    //     $from_date = $request->input('from_date');
    //     $to_date   = $request->input('to_date');
    //     $zone = $request->input('zone')?? null;
    //     $status = $request->input('status')?? null;
    //     $city = $request->input('city')?? null;
    //     $accountability_type = $request->input('accountability_type')?? null;
    //     $customer_id = $request->input('customer_id')?? null;
    //     $vehicle_type = $request->input('vehicle_type')?? null;
    //     $datefilter = $request->input('datefilter')?? null;
    //      $selectedIds = $request->input('selected_ids', []);

        
    //     if (empty($fields)) {
    //         return back()->with('error', 'Please select at least one field to export.');
    //     }
        
    //     $formattedFields = [];
    // if (is_array($fields)) {
    //     foreach ($fields as $item) {
    //         $name = null;

    //         // plain string
    //         if (is_string($item) && trim($item) !== '') {
    //             $name = $item;
    //         }
    //         // associative array like ['name' => 'vehicle_type', 'value' => 'on']
    //         elseif (is_array($item)) {
    //             if (!empty($item['name']) && is_string($item['name'])) {
    //                 $name = $item['name'];
    //             } elseif (!empty($item['field']) && is_string($item['field'])) {
    //                 $name = $item['field'];
    //             } else {
    //                 // fallback: take first scalar value
    //                 $first = reset($item);
    //                 if (is_string($first) && trim($first) !== '') {
    //                     $name = $first;
    //                 }
    //             }
    //         }

    //         if (empty($name) || !is_string($name)) {
    //             continue;
    //         }

    //         $clean = str_replace('_', ' ', $name);
    //         $clean = ucwords(strtolower($clean));

    //         // optional manual mappings
    //         $manual = [
    //             'Date Time' => 'Date & Time',
    //             'Qc Checklist' => 'QC Checklist',
    //             'Id' => 'ID',
    //         ];
    //         if (isset($manual[$clean])) {
    //             $clean = $manual[$clean];
    //         }

    //         $formattedFields[] = $clean;
    //     }
    // }
    // $fieldsText = empty($formattedFields) ? 'ALL' : implode(', ', $formattedFields);

    // // Resolve friendly names for zone/city/customer if possible
    // $zoneName = null;
    // $cityName = null;
    // $customerName = null;
    // if (!empty($zone)) {
    //     $zoneName = optional(Zones::find($zone))->name ?? $zone;
    // }
    // if (!empty($city)) {
    //     $cityName = optional(City::find($city))->city_name ?? $city;
    // }
    // if (!empty($customer_id)) {
    //     // adjust model if your customer model differs
    //     $customerName = optional(CustomerMaster::find($customer_id))->name 
    //                     ?? optional(CustomerMaster::find($customer_id))->name 
    //                     ?? 'Unknown Customer';
    // }
    // $accountability_name = null;
    // if (!empty($accountability_type)) {
    //     // adjust model if your customer model differs
    //     $accountability_name = optional(EvTblAccountabilityType::find($accountability_type))->name 
    //                     ?? optional(EvTblAccountabilityType::find($accountability_type))->name 
    //                     ?? 'Unknown Accountability';
    // }
    

    // // Prepare audit log
    // $fileName = 'Deployment-request-list-' . date('d-m-Y') . '.xlsx';
    // $user = Auth::user();
    // $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    // $Vehicle_type_name = VehicleType::where('id', $vehicle_type)->first();

    // $appliedFilters = [];
    // if (!is_null($status) && $status !== '') $appliedFilters[] = 'Status: ' . $status;
    // if (!is_null($from_date) && $from_date !== '') $appliedFilters[] = 'From: ' . $from_date;
    // if (!is_null($to_date) && $to_date !== '') $appliedFilters[] = 'To: ' . $to_date;
    // if (!is_null($zoneName) && $zoneName !== '') $appliedFilters[] = 'Zone: ' . $zoneName;
    // if (!is_null($cityName) && $cityName !== '') $appliedFilters[] = 'City: ' . $cityName;
    // if (!is_null($accountability_name) && $accountability_name !== '') $appliedFilters[] = 'Accountability Type: ' . $accountability_name;
    // if (!is_null($customerName) && $customerName !== '') $appliedFilters[] = 'Customer: ' . $customerName;
    // if (!is_null($vehicle_type) && $vehicle_type !== '') $appliedFilters[] = 'Vehicle Type: ' . $Vehicle_type_name;
    //  if (!is_null($datefilter) && $datefilter !== '') $appliedFilters[] = 'Date Range: ' . $datefilter;

    // $filtersText = empty($appliedFilters) ? 'No filters applied' : implode('; ', $appliedFilters);
    // $selectedIdsText = empty($selectedIds) ? 'ALL' : implode(', ', array_map('strval', $selectedIds));

    // $longDesc = "User initiated B2B Deployment Request export. File: {$fileName}. Selected Fields: {$fieldsText} | Filters: {$filtersText} | Selected IDs: {$selectedIdsText}.";

    // audit_log_after_commit([
    //     'module_id'         => 5,
    //     'short_description' => 'B2B Admin Deployment Request Export Initiated',
    //     'long_description'  => $longDesc,
    //     'role'              => $roleName,
    //     'user_id'           => Auth::id(),
    //     'user_type'         => 'gdc_admin_dashboard',
    //     'dashboard_type'    => 'web',
    //     'page_name'         => 'deployment_request.export',
    //     'ip_address'        => $request->ip(),
    //     'user_device'       => $request->userAgent()
    // ]);
    //     return Excel::download(
    //         new B2BAdminDeploymentRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone ,$status,$accountability_type,$customer_id , $vehicle_type , $datefilter),
    //         'Deployment-request-list-' . date('d-m-Y') . '.xlsx'
    //     );
    // }
     
     //updated by logesh
//       public function export_deployed_list(Request $request)
// {
//     $user     = Auth::user();
//     $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

//     // Ensure ALL multiple filters are handled as arrays
//     $fields              = (array) $request->input('fields', []);
//     $from_date           = $request->input('from_date');
//     $to_date             = $request->input('to_date');
//     $zone                = (array) $request->input('zone_id', []);
//     $status              = (array) $request->input('status', []);
//     $city                = (array) $request->input('city_id', []);
//     $customer_id         = (array) $request->input('customer_id', []);
//     $accountability_type = (array) $request->input('accountability_type', []);
//     $vehicle_type        = (array) $request->input('vehicle_type', []);
//     $vehicle_model       = (array) $request->input('vehicle_model', []);
//     $vehicle_make        = (array) $request->input('vehicle_make', []);
//     $selectedIds         = (array) $request->input('selected_ids', []);
//     $date_filter         = $request->input('date_filter') ?? null;

//     // Validation
//     if (empty($fields)) {
//         return back()->with('error', 'Please select at least one field to export.');
//     }

//     // ------------------------------------
//     // Build filter summary
//     // ------------------------------------
//     $filterNames = [];

//     if ($from_date)            $filterNames[] = "From Date";
//     if ($to_date)              $filterNames[] = "To Date";
//     if (!empty($status))       $filterNames[] = "Status";
//     if (!empty($city))         $filterNames[] = "City";
//     if (!empty($zone))         $filterNames[] = "Zone";
//     if (!empty($customer_id))  $filterNames[] = "Customer";
//     if (!empty($accountability_type)) $filterNames[] = "Accountability Type";
//     if (!empty($vehicle_type)) $filterNames[] = "Vehicle Type";
//     if (!empty($vehicle_model)) $filterNames[] = "Vehicle Model";
//     if (!empty($vehicle_make))  $filterNames[] = "Vehicle Make";
//     if ($date_filter)           $filterNames[] = "Date Range";

//     $filtersList = !empty($filterNames) ? implode(', ', $filterNames) : "None";
//     $fieldsList  = implode(', ', $fields);

//     // Audit log messages
//     $shortDescription = "Exported deployed asset list";
//     $longDescription = "{$user->name} ({$roleName}) exported the deployed asset list. "
//         . "Fields exported: {$fieldsList}. Filters applied: {$filtersList}.";

//     // Save Audit Log
//     audit_log_after_commit([
//         'module_id'         => 5,
//         'short_description' => $shortDescription,
//         'long_description'  => $longDescription,
//         'role'              => $roleName,
//         'user_id'           => $user->id,
//         'user_type'         => 'gdc_admin_dashboard',
//         'dashboard_type'    => 'web',
//         'page_name'         => 'b2badmin.export_deployed_asset_list',
//         'ip_address'        => $request->ip(),
//         'user_device'       => $request->userAgent()
//     ]);

//     // ------------------------------------
//     // EXPORT
//     // ------------------------------------
//     return Excel::download(
//         new B2BAdminDeployedAssetExport(
//             $from_date,
//             $to_date,
//             $selectedIds,
//             $fields,
//             $city,
//             $zone,
//             $status,
//             $accountability_type,
//             $customer_id,
//             $vehicle_type,
//             $vehicle_model,
//             $vehicle_make,
//             $date_filter
//         ),
//         'Deployed-asset-list-' . date('d-m-Y') . '.xlsx'
//     );
// }
  



    public function load_more_servicedata(Request $request)
    {
        $statuses = [
            'unassigned'  => 'Unassigned',
            'inprogress'  => 'Inprogress',
            'closed'      => 'Closed',
        ];
    
        $colors = ['#03a9f4', '#7cb342', '#f32f10'];
    
        $limit     = (int) ($request->limit  ?? 3);
        $offset    = (int) ($request->offset ?? 0);
        $statusReq = $request->status;       
        $assign_id = $request->assign_id;
        $append    = filter_var($request->append, FILTER_VALIDATE_BOOLEAN); 
        
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;
    
    
        // Helper: render single item HTML
        $renderItem = function($val, $color) {
            $reqId = $val->assignment->VehicleRequest->req_id ?? '';
            $vehicleNo = $val->assignment->vehicle->permanent_reg_number ?? '';
            $ticketID = $val->ticket_id ?? '';
            $createdAt = \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i A');
    
            return '<div class="kanban-items m-1" id="item' . $val->id . '" data-item_id="' . $val->id . '" draggable="true">
                <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid ' . $color . '">
                    <div class="card-body">
                        <p class="mb-0 small-para fw-medium" style="color:' . $color . ';"><span class="lead-heading">Request ID : </span>' . $reqId . '</p>
                         <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Ticket ID:</span> ' . $ticketID . '</p>
                        <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Vehicle No :</span> ' . $vehicleNo . '</p>
                        <p class="mb-0 small-para fw-medium"><span class="lead-heading">Created Date & Time :</span> ' . $createdAt . '</p>
                    </div>
                </div>
            </div>';
        };
    
        // If append (load more) and status provided -> return only new items for that status
        if ($append && $statusReq) {
            $items = B2BServiceRequest::with('assignment')
                        ->where('status', $statusReq)
                        ->where('assign_id', $assign_id)
                    ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [
                            \Carbon\Carbon::parse($fromDate)->startOfDay(),
                            \Carbon\Carbon::parse($toDate)->endOfDay()
                        ]);
                    })
                        ->orderBy('id', 'desc')
                        ->skip($offset)
                        ->take($limit)
                        ->get();
    
            $itemsHtml = '';
            $colorIndex = array_search($statusReq, array_keys($statuses));
            // fallback color index by mapping statuses to colors consistently:
            $statusKeys = array_keys($statuses);
            $colorIndex = array_search($statusReq, $statusKeys);
            if ($colorIndex === false) $colorIndex = 0;
            $color = $colors[$colorIndex % count($colors)];
    
            foreach ($items as $val) {
                $itemsHtml .= $renderItem($val, $color);
            }
    
            // determine if more items remain
            $fetched = $items->count();
            $has_more = $fetched >= $limit;
    
            return response()->json([
                'items_html'  => $itemsHtml,           // only the new item cards
                'has_more'    => $has_more,
                'next_offset' => $offset + $fetched,
            ]);
        }
    
        // --- Initial load (or full refresh): render entire columns with header + initial items ---
        $html_data = '';
        $colorIndex = 0;
    
        foreach ($statuses as $key => $label) {
            // total count for header
            $count = B2BServiceRequest::where('status', $key)
                        ->where('assign_id', $assign_id)
                        ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('created_at', [
                                \Carbon\Carbon::parse($fromDate)->startOfDay(),
                                \Carbon\Carbon::parse($toDate)->endOfDay()
                            ]);
                        })
                        ->count();
    
            $list = B2BServiceRequest::with('assignment')
                        ->where('status', $key)
                        ->where('assign_id', $assign_id)
                     ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                            $q->whereBetween('created_at', [
                                \Carbon\Carbon::parse($fromDate)->startOfDay(),
                                \Carbon\Carbon::parse($toDate)->endOfDay()
                            ]);
                        })
                        ->orderBy('id', 'desc')
                        ->take($limit)
                        ->get();
    
            $color = $colors[$colorIndex % count($colors)];
    
            $html_data .= '<div class="col kanban-column card" id="' . $key . '">
                <p class="card-header" style="background-color: ' . $color . '">' . $label . ' - ' . $count . ' Services</p>
                <div class="card-body p-0">
                    <div class="kanban-cards kanban-cards-' . $key . '">';
    
            if ($list->isEmpty()) {
                $html_data .= '<div class="text-center mt-5 card-inside" id="no-lead-' . $key . '">
                        <h4><i class="bi bi-opencollective"></i></h4>
                        <h4>No Service Found</h4>
                    </div>';
            } else {
                foreach ($list as $val) {
                    $html_data .= $renderItem($val, $color);
                    $last_id = $val->id;
                }
    
                // include Lead More button with next offset
                $html_data .= '<div class="text-center card-inside" id="lead-more-' . $key . '">
                    <button class="btn btn-primary w-100 lead-more-btn" data-status="' . $key . '" data-offset="' . $limit . '">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        Lead More
                    </button>
                </div>';
            }
    
            $html_data .= '</div></div></div>';
    
            $colorIndex++;
        }
    
        return response()->json(['html_data' => $html_data]);
    }

    public function autoload_activity_logs(Request $request , $id){
        
    // Get all assignment IDs for the vehicle
    $assignmentIds = B2BVehicleAssignment::where('asset_vehicle_id', $id)
                        ->pluck('id'); // <-- only get the IDs

    // Get all logs for these assignments
    $logs = B2BVehicleAssignmentLog::whereIn('assignment_id', $assignmentIds)->get();
    
        
        return view('b2badmin::deployed_asset.activity_logs', compact('logs'))->render();
    }
    
}
