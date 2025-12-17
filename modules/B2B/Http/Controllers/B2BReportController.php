<?php

namespace Modules\B2B\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\Zones\Entities\Zones; //updated by Mugesh.B
use Illuminate\Support\Facades\Auth;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
use Modules\B2B\Entities\B2BServiceRequest;//updated by Mugesh.B
use Modules\B2B\Entities\B2BReturnRequest;//updated by Mugesh.B
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\MasterManagement\Entities\CustomerLogin;
use App\Exports\B2BDeploymentReportExport;//updated by Mugesh.B
use App\Exports\B2BServiceReportExport;//updated by Mugesh.B
use App\Exports\B2BReturnReportExport;//updated by Mugesh.B
use App\Exports\B2BClientAccidentReportExport;//updated by Mugesh.B
use Modules\MasterManagement\Entities\EvTblAccountabilityType; //updated by Mugesh.B
use App\Exports\B2BRecoveryReportExport;//updated by Mugesh.B
use Modules\B2B\Entities\B2BReportAccident;//updated by Mugesh.B
use Modules\B2B\Entities\B2BRecoveryRequest;//updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleModelMaster;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
class B2BReportController extends Controller
{

    public function index()
    {
        return view('b2b::reports.index');
    }
    
    public function vehicle_usage()
    {
        return view('b2b::reports.vehicle_usage');
    }
    
    
        public function deployment_report(Request $request)
        {
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            $user->load(['city', 'zone', 'customer_relation']);
        
            if (!$user) {
                return back()->with('error', 'Auth user not found');
            }
        
            if ($user->type == 'master' && empty($user->city_id)) {
                return back()->with('error', 'City not assigned for master');
            }
            
            $accountability_Types = $user->customer_relation->accountability_type_id;

            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }
            
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $vehicle_make = (array)$request->vehicle_make ?? [];
                $vehicle_model = (array)$request->vehicle_model ?? [];
                $status = (array)$request->status ?? [];
                
            // Handle Ajax DataTables request
            if ($request->ajax()) {
                try {
                   
                    
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                        ->where('city_id', $user->city_id)
                        ->pluck('id');
        
                    $query = B2BVehicleAssignment::with([
                        'rider',
                        'agent_relation',
                        'vehicle.quality_check.vehicle_type_relation',
                        'vehicle.quality_check.vehicle_model_relation',
                        'vehicle.quality_check',
                        'vehicle.quality_check.customer_relation',
                        'zone',
                        'VehicleRequest',
                        'VehicleRequest.accountAbilityRelation',
                        'recovery_Request',
                    ]);
        
                    // Core filters
                    $query->whereHas('VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds, $request,$accountability_type,$zone_id) {
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                        
                        
                        if (!empty(array_filter($accountability_type))) {
                            $q->whereIn('account_ability_type', $accountability_type);
                        }
        
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                            
                            if (!empty(array_filter($zone_id))) {
                                $q->whereIn('zone_id', $zone_id);
                            }
                        } elseif ($guard === 'zone') {
                            // $zoneId = $request->filled('zone') ? $request->zone : $user->zone_id;
                            
                            $q->where('city_id', $user->city_id)
                              ->where('zone_id', $user->zone_id);
                        }
        
                    });
        
                    // Vehicle type filter
                    if (!empty(array_filter($vehicle_type))) {

                        $query->whereHas('vehicle', function ($v) use ($vehicle_type) {
                                  $v->whereIn('vehicle_type', $vehicle_type);
                              });
                    }
                    
                    if (!empty(array_filter($vehicle_make))) {
                        $query->whereHas('vehicle.quality_check.vehicle_model_relation', function ($v) use ($vehicle_make) {
                                  $v->whereIn('make', $vehicle_make);
                              });
                    }
        
                    // Vehicle no filter
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no; // Ensure it's an array
                    
                        $query->whereHas('vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
                    
                    if (!empty(array_filter($vehicle_model))) {

                    $query->whereHas('vehicle', function ($v) use ($vehicle_model) {
                              $v->whereIn('model', $vehicle_model);
                          });
                }
                
                    
        
                if(!empty(array_filter($status))){
                  $query->whereIn('status' , $status);
                }
                    
                    
                    // Date filters
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
                    
                    $dateRange = $request->get('date_range', 'today');
                        $from = $request->get('from_date');
                        $to   = $request->get('to_date');
            
                        switch ($dateRange) {
                            case 'yesterday':
                                $from = $to = now()->subDay()->toDateString();
                                break;
                            case 'last7':
                                $from = now()->subDays(6)->toDateString();
                                $to   = now()->toDateString();
                                break;
                            case 'last30':
                                $from = now()->subDays(29)->toDateString();
                                $to   = now()->toDateString();
                                break;
                            case 'custom':
                                // already handled by from_date and to_date from frontend
                                break;
                            default: // today
                                $from = $to = now()->toDateString();
                                break;
                        }
            
                        if ($from && $to) {
                            $query->whereDate('created_at', '>=', $from)
                                  ->whereDate('created_at', '<=', $to);
                        }
        
                    // Search filter
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhereHas('vehicle', function ($v) use ($search) {
                                    $v->where('permanent_reg_number', 'like', "%{$search}%")
                                      ->orWhere('chassis_number', 'like', "%{$search}%");
                                })
                                ->orWhereHas('rider', function ($r) use ($search) {
                                    $r->where('name', 'like', "%{$search}%")
                                      ->orWhere('mobile_no', 'like', "%{$search}%");
                                })
                                ->orWhereHas('vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('make', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                ->orWhereHas('agent_relation', function ($a) use ($search) {
                                      $a->where('name', 'like', "%{$search}%");
                                  })
                                ->orWhereHas('VehicleRequest.customerLogin.customer_relation', function ($c) use ($search) {
                                    $c->where('trade_name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('zone', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
        
                    // $query->whereNotIn('status', ['returned']);
        
                    $totalRecords = $query->count();
        
                    if ($length == -1) $length = $totalRecords;
        
                    $datas = $query->orderBy('id', 'desc')
                                   ->skip($start)
                                   ->take($length)
                                   ->get();
        
                    $formattedData = $datas->map(function ($data, $key) use ($start) {
                        
                        
                    $statusBadge = '';

                    if ($data->status === 'running') {
                        $statusBadge = '<span class="badge-status badge-running">
                                            <i class="bi bi-check-circle"></i> Running
                                        </span>';
                    } elseif ($data->status === 'accident') {
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
                    }elseif ($data->status === 'under_maintenance') { 
                                            $statusBadge = '<span class="badge-status badge-ticket" style="background-color:#dbeafe; color:#1d4ed8; border-color:#1d4ed8;">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.6022 6.05671C15.7267 5.9323 15.9411 5.91962 16.0671 6.05715C16.8055 6.86252 17.2141 7.45886 17.3487 8.11926C17.4262 8.49926 17.4372 8.88599 17.3813 9.26219C17.2302 10.2788 16.4065 11.1026 14.759 12.75L12.7495 14.7595C11.1021 16.407 10.2784 17.2307 9.2617 17.3818C8.8855 17.4377 8.49877 17.4266 8.11877 17.3492C7.45844 17.2146 6.86216 16.8061 6.05693 16.0678C5.91923 15.9416 5.93196 15.727 6.05651 15.6025C6.75016 14.9088 6.71719 13.7513 5.9829 13.0169C5.2486 12.2826 4.09102 12.2497 3.39737 12.9434C3.27283 13.0679 3.05821 13.0806 2.93197 12.9429C2.19375 12.1377 1.78517 11.5414 1.65061 10.8811C1.57318 10.5011 1.56217 10.1143 1.61807 9.73814C1.76915 8.72148 2.59286 7.89773 4.24029 6.25031L6.24982 4.24078C7.89724 2.59335 8.72099 1.76964 9.73765 1.61855C10.1138 1.56266 10.5006 1.57367 10.8806 1.6511C11.541 1.78567 12.1373 2.19432 12.9427 2.93271C13.0802 3.05881 13.0676 3.27317 12.9431 3.39758C12.2494 4.09122 12.2825 5.24879 13.0167 5.9831C13.751 6.7174 14.9087 6.75036 15.6022 6.05671Z" stroke="#2563EB" stroke-width="1.1875" stroke-linejoin="round"/>
                        <path d="M15.0417 11.8752L7.125 3.9585" stroke="#2563EB" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> Under Maintenance
                      </span>';   
                        
                    }
                    elseif ($data->status === 'recovery_request') { 
                        
                       $recoveryStatus = $data->recovery_Request->created_by_type ?? null;

                        // Default: Client Recovery Initiated
                        $status_Text = 'Client Recovery Initiated';
                    
                        // Conditional override
                        if ($recoveryStatus === 'b2b-admin-dashboard') {
                            $status_Text = 'GDM Recovery Initiated';
                        }
                        
                    $statusBadge = '<span class="badge-status badge-gdm-init">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>'.$status_Text.'</span>';   
                        
                    }
                    
                     elseif ($data->status === 'recovered') { 
                    $statusBadge = '<span class="badge-status badge-gdm-init">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path d="M13.4601 16.6252C14.3343 16.6252 15.0429 15.9163 15.0429 15.0418C15.0429 14.1674 14.3343 13.4585 13.4601 13.4585C12.5861 13.4585 11.8774 14.1674 11.8774 15.0418C11.8774 15.9163 12.5861 16.6252 13.4601 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M5.54658 16.6252C6.42069 16.6252 7.12929 15.9163 7.12929 15.0418C7.12929 14.1674 6.42069 13.4585 5.54658 13.4585C4.67247 13.4585 3.96387 14.1674 3.96387 15.0418C3.96387 15.9163 4.67247 16.6252 5.54658 16.6252Z" stroke="#A6661D" stroke-width="1.1875"/>
                        <path d="M9.50293 9.5L4.75479 2.375M4.75479 2.375L6.3375 10.2917M4.75479 2.375H3.03819C2.88854 2.375 2.7623 2.50244 2.74376 2.67225L2.47985 5.08928C2.97146 5.08928 3.37 5.545 3.37 6.10715C3.37 6.66929 2.97146 7.125 2.47985 7.125C2.09227 7.125 1.71156 6.84177 1.58936 6.44643M15.0425 15.0417C17.1659 15.0417 17.4165 14.307 17.4165 12.2807C17.4165 11.3109 17.4165 10.826 17.2265 10.4166C17.0281 9.98909 16.6706 9.72277 15.9187 9.17787C15.1719 8.63669 14.6409 8.02829 14.135 7.19023C13.4134 5.995 13.0527 5.39739 12.5116 5.0737C11.9706 4.75 11.3324 4.75 10.0561 4.75H9.50293V10.2917" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.96365 15.0384C3.96365 15.0384 3.04573 15.0465 2.76084 15.0099C2.52343 14.9149 2.23495 14.692 2.04862 14.5681C1.47885 14.1893 1.5928 14.3449 1.5928 14.0029C1.5928 13.4682 1.58958 11.0882 1.58958 11.0882V10.3282C1.58958 10.2807 1.54075 10.2908 1.90612 10.2965H17.0052M7.12918 15.0431H11.8773" stroke="#A6661D" stroke-width="1.1875" stroke-linecap="round"/>
                        </svg>
                         Recovered</span>';   
                        
                    }
                    
                    elseif ($data->status === 'return_request') { 
                    $statusBadge = '
                        <span class="badge-status d-inline-flex align-items-center px-2 py-1" 
                              style="background-color:#EEE9CA; border-radius:6px; font-size:14px; font-weight:500; gap:6px; line-height:1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                                <rect width="24" height="24" rx="8" fill="#EEE9CA"/>
                                <path d="M4.3335 9.8335V20.8335C4.3335 21.846 5.15431 22.6668 6.16683 22.6668H20.8335C21.846 22.6668 22.6668 21.846 22.6668 20.8335V9.8335" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6.57677 5.34694L4.3335 9.8335H22.6668L20.4236 5.34694C20.113 4.72584 19.4782 4.3335 18.7837 4.3335H8.21656C7.52214 4.3335 6.88733 4.72583 6.57677 5.34694Z" stroke="#58490F" stroke-width="1.375" stroke-linejoin="round"/>
                                <path d="M13.5 9.8335V4.3335" stroke="#58490F" stroke-width="1.375"/>
                                <path d="M10.2918 15.3333H15.3335C16.346 15.3333 17.1668 16.1541 17.1668 17.1667C17.1668 18.1792 16.346 19 15.3335 19H14.4168M11.6668 13.5L9.8335 15.3333L11.6668 17.1667" stroke="#58490F" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Return Request
                        </span>';
  
                        
                    }elseif ($data->status === 'returned') {
                        
                        $statusBadge = '<span class="badge-status d-inline-flex align-items-center px-2 py-1"
                            style="background-color:#DCFCE7; border-radius:6px; font-size:14px; font-weight:500; gap:6px; color:#166534; border:1px solid #16A34A; line-height:1;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16A34A" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12l4-4m0 0l4 4m-4-4v12m8-4a4 4 0 100-8 4 4 0 000 8z"/>
                            </svg> Returned
                        </span>';
                    } 

                     else {
                        $statusBadge = '<span class="badge-status badge-default">Unknown</span>';
                    }
                    
                        return [
                            $start + $key + 1,
                            $data->req_id ?? '-',
                            $data->VehicleRequest->accountAbilityRelation->name ?? '-',
                            $data->vehicle->permanent_reg_number ?? '-',
                            $data->vehicle->chassis_number ?? '-',
                            $data->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                            $data->vehicle->quality_check->vehicle_model_relation->make ?? '-',
                            $data->vehicle->quality_check->vehicle_type_relation->name ?? '-',
                            $data->vehicle->quality_check->location_relation->city_name ?? '-',
                            $data->vehicle->quality_check->zone->name ?? '-',
                            $data->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
                            $data->rider->name ?? '-',
                            $data->agent_relation->name ?? '-',
                            optional($data->VehicleRequest->created_at)->format('d M Y h:i A'),
                            optional($data->created_at)->format('d M Y h:i A'),
                             $statusBadge
                        ];
                    });
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Deployment Report Error: ' . $e->getMessage());
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => $e->getMessage(),
                    ], 500);
                }
            }
        
          $accountability_types = EvTblAccountabilityType::where('status', 1)
            ->whereIn('id',$accountability_Types)
            ->orderBy('id', 'desc')
            ->get();
        
            $zone_id = $user->zone_id ?? null;
            $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
            $vehicle_types = VehicleType::where('is_active', 1)->get();
            $city_id = $user->city_id ?? null;
            $vehicle_models = VehicleModelMaster::where('status', 1)->get();
            $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
        
            return view('b2b::reports.deployment_report', compact('vehicle_types', 'zone_id', 'zones', 'city_id', 'guard' , 'accountability_types','vehicle_models','vehicle_makes'));
        }


        public function export_deployment_report (Request $request)
        {
            $date_range = $request->date_range ?? null;
            $from_date = $request->from_date ?? null;
            $to_date = $request->to_date ?? null;
            $vehicle_type = $request->vehicle_type ?? [];
            $zone = $request->zone_id ?? [];
            $city = $request->city_id ?? [];
            $vehicle_no = $request->vehicle_no ?? [];
            $status = (array) $request->status ?? []; 
            $vehicle_model        = $request->input('vehicle_model', []);        
            $vehicle_make         = $request->input('vehicle_make', []);  
            $accountability_type = $request->accountability_type ?? []; 
            
            
            return Excel::download(
                new B2BDeploymentReportExport($date_range , $from_date , $to_date , $vehicle_type , $city , $zone , $vehicle_no , $status , $accountability_type,$vehicle_model,$vehicle_make),
                'deployment-report-' . date('d-m-Y') . '.csv'
            );
        }
   
   
        public function service_report(Request $request)
        {
            try {
              
                $guard = Auth::guard('master')->check() ? 'master' : 'zone';
                $user = Auth::guard($guard)->user();
        
                if (!$user) {
                    return back()->with('error', 'Authenticated user not found');
                }
        
                $user->load(['city', 'zone', 'customer_relation']);
        
                if ($user->type === 'master' && empty($user->city_id)) {
                    return back()->with('error', 'City not assigned for master');
                }
                
                $accountability_Types = $user->customer_relation->accountability_type_id;
                
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $vehicle_make = (array)$request->vehicle_make ?? [];
                $vehicle_model = (array)$request->vehicle_model ?? [];
                $status = (array)$request->status ?? [];
                
                // Make sure it's an array (sometimes could be stored as string or null)
                if (!is_array($accountability_Types)) {
                    $accountability_Types = json_decode($accountability_Types, true) ?? [];
                }
                // -------------------------------
                // AJAX request (DataTable)
                // -------------------------------
                if ($request->ajax()) {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    // Get all customer logins linked to this master
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                        ->where('city_id', $user->city_id)
                        ->pluck('id');
        
                    $query = B2BServiceRequest::with([
                        'assignment.rider',
                        'assignment.vehicle.quality_check.vehicle_type_relation',
                        'assignment.vehicle.quality_check.vehicle_model_relation',
                        'assignment.vehicle.quality_check.customer_relation',
                        'assignment.vehicle.quality_check.location_relation',
                        'assignment.vehicle.quality_check.zone',
                        'assignment.zone',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.accountAbilityRelation'
                    ]);
        
        
                    // -------------------------------
                    // Core filters
                    // -------------------------------
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds, $request,$accountability_type,$zone_id) {
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
        
                        if (!empty(array_filter($accountability_type))) {
                            $q->whereIn('account_ability_type', $accountability_type);
                        }
                        
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                            
                            if (!empty(array_filter($zone_id))) {
                                $q->whereIn('zone_id', $zone_id);
                            }
                        } elseif ($guard === 'zone') {
                            $zoneId = $request->filled('zone') ? $request->zone : $user->zone_id;
                            $q->where('city_id', $user->city_id)
                                ->where('zone_id', $zoneId);
                        }
                    });
        
                    // Vehicle type filter
                    if (!empty(array_filter($vehicle_type))) {

                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_type) {
                                  $v->whereIn('vehicle_type', $vehicle_type);
                              });
                    }
                    
                    if (!empty(array_filter($vehicle_make))) {
                        $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($vehicle_make) {
                                  $v->whereIn('make', $vehicle_make);
                              });
                    }
        
                    // Vehicle no filter
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no; // Ensure it's an array
                    
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
                    
                    if (!empty(array_filter($vehicle_model))) {

                    $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_model) {
                              $v->whereIn('model', $vehicle_model);
                          });
                }
                
                    
        
                if(!empty(array_filter($status))){
                  $query->whereIn('status' , $status);
                }
        
                    // -------------------------------
                    // Vehicle number filter (supports multi-select)
                    // -------------------------------
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no;
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
        
                    // -------------------------------
                    // Date range filter
                    // -------------------------------
                    $dateRange = $request->get('date_range', 'today');
                    $from = $request->get('from_date');
                    $to   = $request->get('to_date');
        
                    switch ($dateRange) {
                        case 'yesterday':
                            $from = $to = now()->subDay()->toDateString();
                            break;
                        case 'last7':
                            $from = now()->subDays(6)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'last30':
                            $from = now()->subDays(29)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'custom':
                            // already set from frontend
                            break;
                        default:
                            $from = $to = now()->toDateString();
                            break;
                    }
        
                    if ($from && $to) {
                        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                    }
                    
                    // if (!empty($request->status)) {
                    //     $query->where('status' , $request->status);
                    // }
        
                    // -------------------------------
                    // Search filter
                    // -------------------------------
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                                    $v->where('permanent_reg_number', 'like', "%{$search}%")
                                        ->orWhere('chassis_number', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.rider', function ($r) use ($search) {
                                    $r->where('name', 'like', "%{$search}%")
                                        ->orWhere('mobile_no', 'like', "%{$search}%");
                                })
                                
                                ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function ($v) use ($search) {
                                      $v->where('name', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('make', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                  
                                ->orWhereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($c) use ($search) {
                                    $c->where('trade_name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.VehicleRequest.zone', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
        
                    // -------------------------------
                    // Pagination & Ordering
                    // -------------------------------
                    $totalRecords = $query->count();
                    if ($length == -1) $length = $totalRecords;
        
                    $datas = $query->orderByDesc('id')
                        ->skip($start)
                        ->take($length)
                        ->get();
        
                    // -------------------------------
                    // Format Data for DataTables
                    // -------------------------------
                    $formattedData = $datas->map(function ($data, $key) use ($start) {
                        
                        $statusConfig = [
                            'open' => ['label' => 'Open', 'color' => '#DC2626'], // Red
                            'assigned' => ['label' => 'Assigned', 'color' => '#2563EB'], // Blue
                            'work_in_progress' => ['label' => 'Work In Progress', 'color' => '#0EA5E9'], // Sky blue
                            'spare_requested' => ['label' => 'Spare Requested', 'color' => '#F59E0B'], // Amber
                            'spare_approved' => ['label' => 'Spare Approved', 'color' => '#10B981'], // Green
                            'spare_collected' => ['label' => 'Spare Collected', 'color' => '#059669'], // Teal
                            'estimate_requested' => ['label' => 'Estimate Requested', 'color' => '#8B5CF6'], // Purple
                            'estimate_approved' => ['label' => 'Estimate Approved', 'color' => '#22C55E'], // Bright green
                            'closed' => ['label' => 'Closed', 'color' => '#6B7280'], // Gray
                            // Add more if needed
                        ];
                    
                        // Determine badge for current_status
                        $currentStatusKey = $data->current_status ?? '';
                        $currentStatus = $statusConfig[$currentStatusKey] ?? ['label' => ucfirst($currentStatusKey), 'color' => '#6B7280'];
                    
                        $currentStatusHtml = '<span style="background-color:'.$currentStatus['color'].'; color:#fff; padding:4px 8px; border-radius:4px; font-weight:500; font-size:13px;">'
                                                . $currentStatus['label'] .
                                             '</span>';
        
                        $statusConfig = [
                            'unassigned' => ['label' => 'Unassigned', 'color' => '#F87171'], // Red
                            'inprogress' => ['label' => 'In Progress', 'color' => '#3B82F6'], // Blue
                            'closed' => ['label' => 'Closed', 'color' => '#6B7280'], // Gray
                        ];
                        
                        
                        $statusKey = $data->status ?? '';
                        $status = $statusConfig[$statusKey] ?? ['label' => ucfirst($statusKey), 'color' => '#6B7280'];
                        $statusHtml = '<span style="background-color:'.$status['color'].'; color:#fff; padding:4px 8px; border-radius:4px; font-weight:500; font-size:13px;">'
                                            . $status['label'] .
                                      '</span>';
                        return [
                            $start + $key + 1,
                            $data->ticket_id ?? '-',
                            $data->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
                            $data->assignment->vehicle->permanent_reg_number ?? '-',
                            $data->assignment->vehicle->chassis_number ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->make ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_type_relation->name ?? '-',
                            $data->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
                            $data->assignment->vehicle->quality_check->zone->name ?? '-',
                            $data->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
                            $data->assignment->rider->name ?? '-',
                            $data->assignment->rider->mobile_no ?? '-',
                            optional($data->created_at)->format('d M Y h:i A'),
                            $currentStatusHtml,
                            $statusHtml,
                        ];
                    });
        
                    // -------------------------------
                    // Response
                    // -------------------------------
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData,
                    ]);
                }
        
                // -------------------------------
                // Normal Page Load (Non-AJAX)
                // -------------------------------
                $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
                $vehicle_types = VehicleType::where('is_active', 1)->get();
                $accountability_types = EvTblAccountabilityType::where('status', 1)
                    ->whereIn('id',$accountability_Types)
                    ->orderBy('id', 'desc')
                    ->get();
                
                $vehicle_models = VehicleModelMaster::where('status', 1)->get();
                $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
            
                return view('b2b::reports.service_report', [
                    'vehicle_types' => $vehicle_types,
                    'zone_id' => $user->zone_id ?? null,
                    'zones' => $zones,
                    'city_id' => $user->city_id ?? null,
                    'guard' => $guard,
                    'accountability_types' => $accountability_types,
                    'vehicle_models' => $vehicle_models,
                    'vehicle_makes' => $vehicle_makes
                ]);
            } catch (\Exception $e) {
                \Log::error('Service Report Error: ' . $e->getMessage());
                return back()->with('error', 'Something went wrong. Please try again.');
            }
        }
        
        

        public function export_service_report (Request $request)
        {
            
            $date_range = $request->date_range ?? null;
            $from_date = $request->from_date ?? null;
            $to_date = $request->to_date ?? null;
            $vehicle_type = $request->vehicle_type ?? [];
            $vehicle_model        = $request->input('vehicle_model', []);        
            $vehicle_make         = $request->input('vehicle_make', []);  
            $zone = $request->zone ?? [];
            $city = $request->city ?? null;
            $vehicle_no = $request->vehicle_no ?? [];
            $accountability_type = $request->accountability_type ?? [];
            $status = $request->status ?? [];
            
            
            return Excel::download(
                new B2BServiceReportExport($date_range , $from_date , $to_date , $vehicle_type ,$vehicle_model,$vehicle_make, $city , $zone , $vehicle_no , $accountability_type , $status),
                'service-report-' . date('d-m-Y') . '.csv'
            );
        }
        
        
        public function return_report(Request $request){
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
              $user->load(['city', 'zone', 'customer_relation']);
        
            if (!$user) {
                return back()->with('error', 'Auth user not found');
            }
        
            if ($user->type == 'master' && empty($user->city_id)) {
                return back()->with('error', 'City not assigned for master');
            }
            
            $accountability_Types = $user->customer_relation->accountability_type_id;
            
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $vehicle_make = (array)$request->vehicle_make ?? [];
                $vehicle_model = (array)$request->vehicle_model ?? [];
                $status = (array)$request->status ?? [];
                
            // Make sure it's an array (sometimes could be stored as string or null)
            if (!is_array($accountability_Types)) {
                $accountability_Types = json_decode($accountability_Types, true) ?? [];
            }
            
            // -------------------------------
                // AJAX request (DataTable)
                // -------------------------------
                if ($request->ajax()) {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    // Get all customer logins linked to this master
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                        ->where('city_id', $user->city_id)
                        ->pluck('id');
        
                    $query = B2BReturnRequest::with([
                        'assignment.rider',
                        'assignment.vehicle.quality_check.vehicle_type_relation',
                        'assignment.vehicle.quality_check.vehicle_model_relation',
                        'assignment.vehicle.quality_check.customer_relation',
                        'assignment.vehicle.quality_check.location_relation',
                        'assignment.vehicle.quality_check.zone',
                        'assignment.zone',
                        'assignment.VehicleRequest',
                        'agent',
                        'assignment.VehicleRequest.accountAbilityRelation'
                        
                    ]);
        
                    // -------------------------------
                    // Core filters
                    // -------------------------------
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds, $request,$accountability_type,$zone_id) {
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
        
                         if (!empty(array_filter($accountability_type))) {
                            $q->whereIn('account_ability_type', $accountability_type);
                        }
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                             if (!empty(array_filter($zone_id))) {
                                $q->whereIn('zone_id', $zone_id);
                            }
                        } elseif ($guard === 'zone') {
                            $zoneId = $request->filled('zone') ? $request->zone : $user->zone_id;
                            $q->where('city_id', $user->city_id)
                                ->where('zone_id', $zoneId);
                        }
                    });
        
                    // Vehicle type filter
                    if (!empty(array_filter($vehicle_type))) {

                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_type) {
                                  $v->whereIn('vehicle_type', $vehicle_type);
                              });
                    }
                    
                    if (!empty(array_filter($vehicle_make))) {
                        $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($vehicle_make) {
                                  $v->whereIn('make', $vehicle_make);
                              });
                    }
        
                    // Vehicle no filter
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no; // Ensure it's an array
                    
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
                    
                    if (!empty(array_filter($vehicle_model))) {

                    $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_model) {
                              $v->whereIn('model', $vehicle_model);
                          });
                }
        
                    // -------------------------------
                    // Vehicle number filter (supports multi-select)
                    // -------------------------------
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no;
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
        
                    // -------------------------------
                    // Date range filter
                    // -------------------------------
                    $dateRange = $request->get('date_range', 'today');
                    $from = $request->get('from_date');
                    $to   = $request->get('to_date');
        
                    switch ($dateRange) {
                        case 'yesterday':
                            $from = $to = now()->subDay()->toDateString();
                            break;
                        case 'last7':
                            $from = now()->subDays(6)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'last30':
                            $from = now()->subDays(29)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'custom':
                            // already set from frontend
                            break;
                        default:
                            $from = $to = now()->toDateString();
                            break;
                    }
        
                    if ($from && $to) {
                        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                    }
                    
                    if(!empty(array_filter($status))){
                          $query->whereIn('status' , $status);
                        }
                    // $query->where('status' , 'closed');
                    
        
                    // -------------------------------
                    // Search filter
                    // -------------------------------
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                                    $v->where('permanent_reg_number', 'like', "%{$search}%")
                                        ->orWhere('chassis_number', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.rider', function ($r) use ($search) {
                                    $r->where('name', 'like', "%{$search}%")
                                        ->orWhere('mobile_no', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function ($v) use ($search) {
                                      $v->where('name', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('make', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                ->orWhereHas('agent', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.VehicleRequest.zone', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
        
                    // -------------------------------
                    // Pagination & Ordering
                    // -------------------------------
                    $totalRecords = $query->count();
                    if ($length == -1) $length = $totalRecords;
        
                    $datas = $query->orderByDesc('id')
                        ->skip($start)
                        ->take($length)
                        ->get();
        
                    // -------------------------------
                    // Format Data for DataTables
                    // -------------------------------
                    $formattedData = $datas->map(function ($data, $key) use ($start) {
                        
                        $statusConfig = [
                            'opened' => ['label' => 'Opened', 'color' => '#F87171'], // Red
                            'closed' => ['label' => 'Closed', 'color' => '#6B7280'], // Gray
                        ];
                        
                        $statusKey = $data->status ?? '';
                        $status = $statusConfig[$statusKey] ?? ['label' => ucfirst($statusKey), 'color' => '#6B7280'];
                        $statusHtml = '<span style="background-color:'.$status['color'].'; color:#fff; padding:4px 8px; border-radius:4px; font-weight:500; font-size:13px;">'
                                            . $status['label'] .
                                      '</span>';
                        
                        $createdAt = $data->created_at ? Carbon::parse($data->created_at)->format('d M Y h:i A') : '-';
                        $closedAt  = $data->closed_at ? Carbon::parse($data->closed_at)->format('d M Y h:i A') : '-';
                        return [
                            $start + $key + 1,
                            $data->assignment->VehicleRequest->req_id ?? '-',
                            $data->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
                            $data->assignment->vehicle->permanent_reg_number ?? '-',
                            $data->assignment->vehicle->chassis_number ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->make ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_type_relation->name ?? '-',
                            $data->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
                            $data->assignment->vehicle->quality_check->zone->name ?? '-',
                            $data->assignment->rider->name ?? '-',
                            $data->assignment->rider->mobile_no ?? '-',
                            $data->agent->name ?? '-',
                            $statusHtml,
                            $createdAt ,
                            $closedAt
                        ];
                    });
        
                    // -------------------------------
                    // Response
                    // -------------------------------
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData,
                    ]);
                }
            
            $zone_id = $user->zone_id ?? null;
            $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
            $vehicle_types = VehicleType::where('is_active', 1)->get();
            $city_id = $user->city_id ?? null;
            $accountability_types = EvTblAccountabilityType::where('status', 1)
                    ->whereIn('id',$accountability_Types)
                    ->orderBy('id', 'desc')
                    ->get();
            $vehicle_models = VehicleModelMaster::where('status', 1)->get();
            $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
            return view('b2b::reports.return_report', compact('vehicle_types', 'zone_id', 'zones', 'city_id', 'guard' , 'accountability_types','vehicle_models','vehicle_makes'));
            
        }
        
        public function export_return_report(Request $request){
            
            
            $date_range = $request->date_range ?? null;
            $from_date = $request->from_date ?? null;
            $to_date = $request->to_date ?? null;
            $vehicle_type = $request->vehicle_type ?? [];
            $zone = $request->zone ?? [];
            $city = $request->city ?? [];
            $status = $request->status ?? [];
            $vehicle_no = $request->vehicle_no ?? [];
            $accountability_type = $request->accountability_type ?? [];
            $vehicle_model     = $request->vehicle_model ?? [];
            $vehicle_make      = $request->vehicle_make ?? [];
            return Excel::download(
                new B2BReturnReportExport($date_range , $from_date , $to_date , $vehicle_type , $city , $zone , $vehicle_no , $accountability_type,$status,$vehicle_model,$vehicle_make),
                'return-report-' . date('d-m-Y') . '.csv'
            );
        }
        
        
        public function accident_report(Request $request){
            
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            $user->load(['city', 'zone', 'customer_relation']);
        
            if (!$user) {
                return back()->with('error', 'Auth user not found');
            }
        
            if ($user->type == 'master' && empty($user->city_id)) {
                return back()->with('error', 'City not assigned for master');
            }
            $accountability_Types = $user->customer_relation->accountability_type_id;
            
                // Make sure it's an array (sometimes could be stored as string or null)
                if (!is_array($accountability_Types)) {
                    $accountability_Types = json_decode($accountability_Types, true) ?? [];
                }
                
                $accountability_type = (array)$request->accountability_type ?? [];
                $zone_id = (array)$request->zone ?? [];
                $vehicle_type = (array)$request->vehicle_type ?? [];
                $vehicle_make = (array)$request->vehicle_make ?? [];
                $vehicle_model = (array)$request->vehicle_model ?? [];
                $status = (array)$request->status ?? [];
                
                // -------------------------------
                // AJAX request (DataTable)
                // -------------------------------
                if ($request->ajax()) {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    // Get all customer logins linked to this master
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                        ->where('city_id', $user->city_id)
                        ->pluck('id');
        
                    $query = B2BReportAccident::with([
                        'assignment.rider',
                        'assignment.vehicle.quality_check.vehicle_type_relation',
                        'assignment.vehicle.quality_check.vehicle_model_relation',
                        'assignment.vehicle.quality_check.customer_relation',
                        'assignment.vehicle.quality_check.location_relation',
                        'assignment.vehicle.quality_check.zone',
                        'assignment.zone',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.accountAbilityRelation'
                    ]);
        
                    // -------------------------------
                    // Core filters
                    // -------------------------------
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds, $request,$accountability_type,$zone_id) {
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                        if (!empty(array_filter($accountability_type))) {
                            $q->whereIn('account_ability_type', $accountability_type);
                        }
                        
        
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                            if (!empty(array_filter($zone_id))) {
                                $q->whereIn('zone_id', $zone_id);
                            }
                        } elseif ($guard === 'zone') {
                            $zoneId = $request->filled('zone') ? $request->zone : $user->zone_id;
                            $q->where('city_id', $user->city_id)
                                ->where('zone_id', $zoneId);
                        }
                    });
        
                    // Vehicle type filter
                    if (!empty(array_filter($vehicle_type))) {

                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_type) {
                                  $v->whereIn('vehicle_type', $vehicle_type);
                              });
                    }
                    
                    if (!empty(array_filter($vehicle_make))) {
                        $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($vehicle_make) {
                                  $v->whereIn('make', $vehicle_make);
                              });
                    }
        
                    // Vehicle no filter
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no; // Ensure it's an array
                    
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
                    
                    if (!empty(array_filter($vehicle_model))) {

                    $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_model) {
                              $v->whereIn('model', $vehicle_model);
                          });
                    }
        
                    // -------------------------------
                    // Vehicle number filter (supports multi-select)
                    // -------------------------------
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no;
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
        
                    // -------------------------------
                    // Date range filter
                    // -------------------------------
                    $dateRange = $request->get('date_range', 'today');
                    $from = $request->get('from_date');
                    $to   = $request->get('to_date');
        
                    switch ($dateRange) {
                        case 'yesterday':
                            $from = $to = now()->subDay()->toDateString();
                            break;
                        case 'last7':
                            $from = now()->subDays(6)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'last30':
                            $from = now()->subDays(29)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'custom':
                            // already set from frontend
                            break;
                        default:
                            $from = $to = now()->toDateString();
                            break;
                    }
        
                    if ($from && $to) {
                        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                    }
                    if(!empty(array_filter($status))){
                          $query->whereIn('status' , $status);
                        }
                    
                    // -------------------------------
                    // Search filter
                    // -------------------------------
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                                    $v->where('permanent_reg_number', 'like', "%{$search}%")
                                        ->orWhere('chassis_number', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.rider', function ($r) use ($search) {
                                    $r->where('name', 'like', "%{$search}%")
                                        ->orWhere('mobile_no', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function ($v) use ($search) {
                                      $v->where('name', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('make', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                ->orWhereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($c) use ($search) {
                                    $c->where('trade_name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('agent', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.VehicleRequest.zone', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
        
                    // -------------------------------
                    // Pagination & Ordering
                    // -------------------------------
                    $totalRecords = $query->count();
                    if ($length == -1) $length = $totalRecords;
        
                    $datas = $query->orderByDesc('id')
                        ->skip($start)
                        ->take($length)
                        ->get();
        
                    // -------------------------------
                    // Format Data for DataTables
                    // -------------------------------
                    $formattedData = $datas->map(function ($data, $key) use ($start) {
                        
                        
                        $createdAt = $data->created_at ? Carbon::parse($data->created_at)->format('d M Y h:i A') : '-';
                        
                        $statusBadgeMap = [
                            'claimed_initiated' => ['text' => 'Claimed Initiated', 'class' => 'badge bg-primary'],
                            'insurer_visit_confirmed' => ['text' => 'Insurer Visit Confirmed', 'class' => 'badge bg-info'],
                            'inspection_completed' => ['text' => 'Inspection Completed', 'class' => 'badge bg-secondary'],
                            'approval_pending' => ['text' => 'Approval Pending', 'class' => 'badge bg-warning'],
                            'repair_started' => ['text' => 'Repair Started', 'class' => 'badge bg-info'],
                            'repair_completed' => ['text' => 'Repair Completed', 'class' => 'badge bg-success'],
                            'invoice_submitted' => ['text' => 'Invoice Submitted', 'class' => 'badge bg-primary'],
                            'payment_approved' => ['text' => 'Payment Approved', 'class' => 'badge bg-success'],
                            'claim_closed' => ['text' => 'Claim Closed (Settled)', 'class' => 'badge bg-dark'],
                        ];
                        
                        $status = '-';
                        if (!empty($data->status)) {
                            if (isset($statusBadgeMap[$data->status])) {
                                $status = '<span class="'.$statusBadgeMap[$data->status]['class'].'">'
                                          .$statusBadgeMap[$data->status]['text'].
                                          '</span>';
                            } else {
                                // fallback for unknown status
                                $status = '<span class="badge bg-secondary">'.ucfirst(str_replace('_', ' ', $data->status)).'</span>';
                            }
                        }

                        
                        
                        return [
                            $start + $key + 1,
                            $data->assignment->VehicleRequest->req_id ?? '-',
                            $data->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
                            $data->assignment->vehicle->permanent_reg_number ?? '-',
                            $data->assignment->vehicle->chassis_number ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->make ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_type_relation->name ?? '-',
                            $data->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
                            $data->assignment->vehicle->quality_check->zone->name ?? '-',
                            $data->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
                            $data->assignment->rider->name ?? '-',
                            $data->assignment->rider->mobile_no ?? '-',
                            $createdAt ,
                            $status
                        ];
                    });
        
        
                    // -------------------------------
                    // Response
                    // -------------------------------
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData,
                    ]);
                }
            
            $zone_id = $user->zone_id ?? null;
            $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
            $vehicle_types = VehicleType::where('is_active', 1)->get();
            $city_id = $user->city_id ?? null;
            
            $accountability_types = EvTblAccountabilityType::where('status', 1)
                    ->whereIn('id',$accountability_Types)
                    ->orderBy('id', 'desc')
                    ->get();
            $vehicle_models = VehicleModelMaster::where('status', 1)->get();
            $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');        
                    
            return view('b2b::reports.accident_report', compact('vehicle_types', 'zone_id', 'zones', 'city_id', 'guard' , 'accountability_types','vehicle_models','vehicle_makes'));
            
        }
        
       public function export_accident_report(Request $request)
        {
            if (!empty($request->status) && is_string($request->status)) {
                        $status = explode(',', $request->status);
                    } else {
                        $status = (array) $request->status;
                    }
              
            return Excel::download(
                new B2BClientAccidentReportExport(
                    $request->date_range ?? null,
                    $request->from_date ?? null,
                    $request->to_date ?? null,
                    $request->vehicle_type ?? [],
                    $request->city ?? [],
                    $request->zone ?? [],
                    $request->vehicle_no ?? [],
                    $status,
                    $request->accountability_type ?? [],
                    $request->vehicle_model ?? [],
                    $request->vehicle_make ?? []
                ),
                'accident-report-' . date('d-m-Y') . '.csv'
            );
        }

        
        public function recovery_report(Request $request){
           
            $guard = Auth::guard('master')->check() ? 'master' : 'zone';
            $user = Auth::guard($guard)->user();
            $user->load(['city', 'zone', 'customer_relation']);
        
            if (!$user) {
                return back()->with('error', 'Auth user not found');
            }
        
            if ($user->type == 'master' && empty($user->city_id)) {
                return back()->with('error', 'City not assigned for master');
            }
               $accountability_Types = $user->customer_relation->accountability_type_id;
    
                // Make sure it's an array (sometimes could be stored as string or null)
                if (!is_array($accountability_Types)) {
                    $accountability_Types = json_decode($accountability_Types, true) ?? [];
                }

            // -------------------------------
                // AJAX request (DataTable)
                // -------------------------------
                if ($request->ajax()) {
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    // Get all customer logins linked to this master
                    $customerId = CustomerLogin::where('id', $user->id)->value('customer_id');
                    $customerLoginIds = CustomerLogin::where('customer_id', $customerId)
                        ->where('city_id', $user->city_id)
                        ->pluck('id');
                    
                    $accountability_type = (array)$request->accountability_type ?? [];
                    $zone_id = (array)$request->zone ?? [];
                    $vehicle_type = (array)$request->vehicle_type ?? [];
                    $vehicle_make = (array)$request->vehicle_make ?? [];
                    $vehicle_model = (array)$request->vehicle_model ?? [];
                    $status = (array)$request->status ?? [];
                
                    $query = B2BRecoveryRequest::with([
                        'assignment.rider',
                        'assignment.vehicle.quality_check.vehicle_type_relation',
                        'assignment.vehicle.quality_check.vehicle_model_relation',
                        'assignment.vehicle.quality_check.customer_relation',
                        'assignment.vehicle.quality_check.location_relation',
                        'assignment.vehicle.quality_check.zone',
                        'assignment.zone',
                        'assignment.VehicleRequest',
                        'recovery_agent',
                        'assignment.VehicleRequest.accountAbilityRelation'
                    ]);
        
                    // -------------------------------
                    // Core filters
                    // -------------------------------
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user, $guard, $customerLoginIds, $request,$accountability_type,$zone_id) {
                        if ($customerLoginIds->isNotEmpty()) {
                            $q->whereIn('created_by', $customerLoginIds);
                        }
                        
                           if (!empty(array_filter($accountability_type))) {
                            $q->whereIn('account_ability_type', $accountability_type);
                        }
        
                        if ($guard === 'master') {
                            $q->where('city_id', $user->city_id);
                             if (!empty(array_filter($zone_id))) {
                                $q->whereIn('zone_id', $zone_id);
                            }
                        } elseif ($guard === 'zone') {
                            $zoneId = $request->filled('zone') ? $request->zone : $user->zone_id;
                            $q->where('city_id', $user->city_id)
                                ->where('zone_id', $zoneId);
                        }
                    });
        
                    if (!empty(array_filter($vehicle_type))) {

                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_type) {
                                  $v->whereIn('vehicle_type', $vehicle_type);
                              });
                    }
                    
                    if (!empty(array_filter($vehicle_make))) {
                        $query->whereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($vehicle_make) {
                                  $v->whereIn('make', $vehicle_make);
                              });
                    }
        
                    // Vehicle no filter
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no; // Ensure it's an array
                    
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
                    
                    if (!empty(array_filter($vehicle_model))) {

                    $query->whereHas('assignment.vehicle', function ($v) use ($vehicle_model) {
                              $v->whereIn('model', $vehicle_model);
                          });
                    }
        
                    // -------------------------------
                    // Vehicle number filter (supports multi-select)
                    // -------------------------------
                    if ($request->filled('vehicle_no')) {
                        $vehicleNos = (array) $request->vehicle_no;
                        $query->whereHas('assignment.vehicle', function ($v) use ($vehicleNos) {
                            $v->whereIn('id', $vehicleNos);
                        });
                    }
        
                    // -------------------------------
                    // Date range filter
                    // -------------------------------
                    $dateRange = $request->get('date_range', 'today');
                    $from = $request->get('from_date');
                    $to   = $request->get('to_date');
        
                    switch ($dateRange) {
                        case 'yesterday':
                            $from = $to = now()->subDay()->toDateString();
                            break;
                        case 'last7':
                            $from = now()->subDays(6)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'last30':
                            $from = now()->subDays(29)->toDateString();
                            $to   = now()->toDateString();
                            break;
                        case 'custom':
                            // already set from frontend
                            break;
                        default:
                            $from = $to = now()->toDateString();
                            break;
                    }
        
                    if ($from && $to) {
                        $query->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
                    }
                    
                            
                    if(!empty(array_filter($status))){
                          $query->whereIn('status' , $status);
                        }
                    
        
                    // -------------------------------
                    // Search filter
                    // -------------------------------
                    if (!empty($search)) {
                        $query->where(function ($q) use ($search) {
                            $q->where('id', 'like', "%{$search}%")
                                ->orWhereHas('assignment.vehicle', function ($v) use ($search) {
                                    $v->where('permanent_reg_number', 'like', "%{$search}%")
                                        ->orWhere('chassis_number', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.rider', function ($r) use ($search) {
                                    $r->where('name', 'like', "%{$search}%")
                                        ->orWhere('mobile_no', 'like', "%{$search}%");
                                })
                                
                                ->orWhereHas('assignment.vehicle.quality_check.vehicle_type_relation', function ($v) use ($search) {
                                      $v->where('name', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('make', 'like', "%{$search}%");
                                  })
                                  ->orWhereHas('assignment.vehicle.quality_check.vehicle_model_relation', function ($v) use ($search) {
                                      $v->where('vehicle_model', 'like', "%{$search}%");
                                  })
                                  
                                ->orWhereHas('assignment.VehicleRequest.customerLogin.customer_relation', function ($c) use ($search) {
                                    $c->where('trade_name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('agent', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('assignment.VehicleRequest.zone', function ($z) use ($search) {
                                    $z->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
        
                    // -------------------------------
                    // Pagination & Ordering
                    // -------------------------------
                    $totalRecords = $query->count();
                    if ($length == -1) $length = $totalRecords;
        
                    $datas = $query->orderByDesc('id')
                        ->skip($start)
                        ->take($length)
                        ->get();
        
                    // -------------------------------
                    // Format Data for DataTables
                    // -------------------------------
                    $formattedData = $datas->map(function ($data, $key) use ($start) {
                        
                        
                        $createdAt = $data->created_at ? Carbon::parse($data->created_at)->format('d M Y h:i A') : '-';
                        
                          $status = $data->status ?? '-';
                            switch ($status) {
                            case 'opened':
                                $statusTag = '<span class="badge bg-primary">Opened</span>';
                                break;
                            case 'closed':
                                $statusTag = '<span class="badge bg-success">Closed</span>';
                                break;
                            case 'agent_assigned':
                                $statusTag = '<span class="badge bg-warning text-dark">Agent Assigned</span>';
                                break;
                            case 'not_recovered':
                                $statusTag = '<span class="badge bg-danger">Not Recovered</span>';
                                break;
                            default:
                                $statusTag = '<span class="badge bg-secondary">-</span>';
                        }
                        $created_by = "Unknown";
                        if($data->created_by_type == 'b2b-web-dashboard'){
                            $created_by = 'Customer';
                        }elseif($data->created_by_type == 'b2b-admin-dashboard'){
                            $created_by = 'GDM';
                        }
    
                        return [
                            $start + $key + 1,
                            $data->assignment->VehicleRequest->req_id ?? '-',
                            $data->assignment->VehicleRequest->accountAbilityRelation->name ?? '-',
                            $data->assignment->vehicle->permanent_reg_number ?? '-',
                            $data->assignment->vehicle->chassis_number ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->vehicle_model ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_model_relation->make ?? '-',
                            $data->assignment->vehicle->quality_check->vehicle_type_relation->name ?? '-',
                            $data->assignment->vehicle->quality_check->location_relation->city_name ?? '-',
                            $data->assignment->vehicle->quality_check->zone->name ?? '-',
                            $data->assignment->VehicleRequest->customerLogin->customer_relation->trade_name ?? '-',
                            $data->assignment->rider->name ?? '-',
                            $data->assignment->rider->mobile_no ?? '-',
                            $data->recovery_agent 
                            ? trim(($data->recovery_agent->first_name ?? '') . ' ' . ($data->recovery_agent->last_name ?? ''))
                            : '-',
                            $created_by ,
                            $createdAt ,
                            $statusTag
                            
                        ];
                    });
        
                    // -------------------------------
                    // Response
                    // -------------------------------
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData,
                    ]);
                }
                
            $zone_id = $user->zone_id ?? null;
            $zones = Zones::where('city_id', $user->city_id)->where('status', 1)->get();
            $vehicle_types = VehicleType::where('is_active', 1)->get();
            $city_id = $user->city_id ?? null;
            $accountability_types = EvTblAccountabilityType::where('status', 1)
                    ->whereIn('id',$accountability_Types)
                    ->orderBy('id', 'desc')
                    ->get();
            $vehicle_models = VehicleModelMaster::where('status', 1)->get();
            $vehicle_makes = VehicleModelMaster::where('status', 1)->distinct()->pluck('make');
            
            return view('b2b::reports.recovery_report', compact('vehicle_types', 'zone_id', 'zones', 'city_id', 'guard' , 'accountability_types','vehicle_models','vehicle_makes'));
            
        }
        
        
        public function export_recovery_report(Request $request)
        {
            $date_range        = $request->date_range ?? null;
            $from_date         = $request->from_date ?? null;
            $to_date           = $request->to_date ?? null;
            $vehicle_type      = $request->vehicle_type ?? [];
            $zone              = $request->zone ?? [];
            $city              = $request->city ?? [];
            $vehicle_no        = $request->vehicle_no ?? [];
            $status            = $request->status ?? [];
            $accountability_type = $request->accountability_type ?? [];
            $vehicle_model     = $request->vehicle_model ?? [];
            $vehicle_make      = $request->vehicle_make ?? [];
        
            return Excel::download(
                new B2BRecoveryReportExport(
                    $date_range,
                    $from_date,
                    $to_date,
                    $vehicle_type,
                    $city,
                    $zone,
                    $vehicle_no,
                    $status,
                    $accountability_type,
                    $vehicle_model,
                    $vehicle_make
                ),
                'recovery-report-' . date('d-m-Y') . '.csv'
            );
        }

}
