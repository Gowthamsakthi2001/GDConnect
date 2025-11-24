<?php

namespace Modules\RecoveryManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Carbon\Carbon;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\RecoveryManager\Entities\RecoveryComment;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BusinessSetting;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\B2BRecoveryAgentExport;
use App\Exports\B2BRecoveryManagerRequestExport;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use App\Helpers\CustomHandler;
use App\Services\FirebaseNotificationService; //updated by Mugesh.B
use App\Models\EvMobitraApiSetting;
use App\Models\MobitraApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Role\Entities\Role;
use Illuminate\Support\Facades\DB;
use Modules\MasterManagement\Entities\CustomerMaster;
use App\Jobs\SendEmailJob;
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster; //updated by logesh

class RecoveryManagerController extends Controller
{
    
        public function list(Request $request,$type)
        {
            if ($request->ajax()) {
                try {
                    $validTypes = ['all', 'pending','agent-assigned', 'not-recovered','closed'];
                    if (!in_array($type, $validTypes)) {
                        return response()->json(['error' => 'Invalid agent status'], 422);
                    }
                    $start  = $request->input('start', 0);
                    $length = $request->input('length', 25);
                    $search = $request->input('search.value');
        
                    $user = User::find(Auth::id());
                
                    $query = B2BRecoveryRequest::with([
                        'rider',
                        'assignment',
                        'assignment.vehicle',
                        'assignment.VehicleRequest',
                        'assignment.VehicleRequest.city',
                        'assignment.VehicleRequest.zone',
                        'rider.customerlogin.customer_relation'
                    ]);
                    $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
                    
                    if ($type == 'pending') {
                        $query->where('status', 'opened');
                    } elseif ($type == 'not-recovered') {
                        $query->where('status', 'not_recovered');
                    } elseif ($type == 'closed') {
                        $query->where('status', 'closed');
                    } elseif ($type == 'agent-assigned') {
                        $query->where('status', 'agent_assigned');
                    }
                    
                    // Filter by status
                    if ($request->filled('status') && $request->status !== 'all') {
                        $query->where('status', $request->status);
                    }
        
                    // Filter by date range
                    if ($request->filled('from_date') && $request->filled('to_date')) {
                        $query->whereDate('created_at', '>=', $request->from_date)
                              ->whereDate('created_at', '<=', $request->to_date);
                    }
        
        
                    if ($request->filled('city_id')) {
                        $query->whereHas('assignment.VehicleRequest.city', function($ct) use ($request) {
                            $ct->where('id', $request->city_id);
                        });
                    }
                    
                    // Filter by zone_id
                    if ($request->filled('zone_id')) {
                        $query->whereHas('assignment.VehicleRequest.zone', function($zn) use ($request) {
                            $zn->where('id', $request->zone_id);
                        });
                    }
                    

                    // Search filters
                    if (!empty($search)) {
                        $query->where(function($q) use ($search) {
                            // Status, dates
                            $q->where('status', 'like', "%{$search}%")
                              ->orWhereDate('created_at', $search)
                              ->orWhereDate('updated_at', $search);
                    
                            // Vehicle Request (req_id)
                            $q->orWhereHas('assignment.VehicleRequest', function($vr) use ($search) {
                                $vr->where('req_id', 'like', "%{$search}%");
                            });
                    
                            // Vehicle details
                            $q->orWhereHas('assignment.vehicle', function($v) use ($search) {
                                $v->where('permanent_reg_number', 'like', "%{$search}%")
                                  ->orWhere('chassis_number', 'like', "%{$search}%");
                            });
                    
                            // Rider details
                            $q->orWhereHas('rider', function($r) use ($search) {
                                $r->where('name', 'like', "%{$search}%")
                                  ->orWhere('mobile_no', 'like', "%{$search}%");
                            });
                    
                            // Client details
                            $q->orWhereHas('rider.customerlogin.customer_relation', function($c) use ($search) {
                                $c->where('trade_name', 'like', "%{$search}%");
                            });
                            
                            $q->orWhereHas('assignment.VehicleRequest.city', function($ct) use ($search) {
                                $ct->where('city_name', 'like', "%{$search}%");
                            });
                    
                            // Zone
                            $q->orWhereHas('assignment.VehicleRequest.zone', function($zn) use ($search) {
                                $zn->where('name', 'like', "%{$search}%");
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
                        // Status display
                        
                        $idEncode = encrypt($item->id);
                         
                        $statusColumn = '';
                        if ($item->status === 'opened') {
                            $statusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->status === 'closed') {
                            $statusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        } elseif ($item->status === 'agent_assigned') {
                            $statusColumn = '
                                <span style="background-color:#FFF3CD; color:#856404;border:1px solid #856404;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-person-check me-1"></i> Agent Assigned
                                </span>';
                        } elseif ($item->status === 'not_recovered') {
                            $statusColumn = '
                                <span style="background-color:#E2E3E5; color:#383D41;border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Not Recovered
                                </span>';
                        }
                        $comments = '<a href="javascript:void(0);"
                                   data-bs-toggle="modal"
                                   data-bs-target="#commentModal"
                                   data-agent_id="'.$item->recovery_agent_id.'"
                                   data-id="'.$item->id.'"
                                   data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                                   data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                                   title="Agent Comment"
                                   class="view-comments d-flex align-items-center justify-content-center border-0"
                                   style="border-radius:8px;cursor:pointer;">
                                   <i class="bi bi-eye fs-5"></i>
                                </a>';
                            
                        $agentStatusColumn = '';
                        if ($item->agent_status === 'opened') {
                            $agentStatusColumn = '
                                <span style="background-color:#CAEDCE; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-x-circle me-1"></i> Opened
                                </span>';
                        } elseif ($item->agent_status === 'in_progress') {
                            $agentStatusColumn = '
                                <span style="background-color:#FFF3CD; color:#856404;border:1px solid #856404;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-arrow-repeat me-1"></i> In Progress
                                </span>';
                        } elseif ($item->agent_status === 'reached_location') {
                            $agentStatusColumn = '
                                <span style="background-color:#CCE5FF; color:#004085;border:1px solid #004085;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-geo-alt me-1"></i> Reached Location
                                </span>';
                        } elseif ($item->agent_status === 'revisit_location') {
                            $agentStatusColumn = '
                                <span style="background-color:#D6D8D9; color:#383D41;border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Revisited Location
                                </span>';
                        } elseif ($item->agent_status === 'recovered') {
                            $agentStatusColumn = '
                                <span style="background-color:#D4EDDA; color:#155724;border:1px solid #155724;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Vehicle Found
                                </span>';
                        } elseif ($item->agent_status === 'closed') {
                            $agentStatusColumn = '
                                <span style="background-color:#EECACB; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-check-circle me-1"></i> Closed
                                </span>';
                        } elseif ($item->agent_status === 'not_recovered') {
                            $agentStatusColumn = '
                                <span style="background-color:#F8D7DA; color:#721c24;border:1px solid #721c24;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Vehicle Not Found
                                </span>';
                        } elseif ($item->agent_status === 'hold') {
                            $agentStatusColumn = '
                                <span style="background-color:#D1ECF1; color:#0C5460;border:1px solid #0C5460;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-pause-circle me-1"></i> Hold
                                </span>';
                        }elseif ($item->agent_status === 'rider_contacted') {
                                $agentStatusColumn = '
                                    <span style="background-color:#E2EAFD; color:#1A237E; border:1px solid #1A237E;" class="px-2 py-1 rounded-pill">
                                        <i class="bi bi-telephone-forward me-1"></i> Follow-up Call
                                    </span>';
                            }
                        else{
                            $agentStatusColumn = '<span style="background-color:#E2E3E5; color:#383D41; border:1px solid #383D41;" class="px-2 py-1 rounded-pill">
                                    <i class="bi bi-info-circle me-1"></i> Not Assigned
                                </span>';
                        }
                        
                        $action ='<div class="d-flex align-items-center gap-2">
                            <a href="'.route('admin.recovery_management.view', $idEncode).'"
                                class="d-flex align-items-center justify-content-center border-0" title="View"
                                style="background-color:#CAEDCE;color:#155724;border-radius:8px;width:35px;height:35px;">
                                <i class="bi bi-eye fs-5"></i>
                            </a>
                        
                            <!-- Add Comment -->
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#addCommentModal"
                               data-agent_id="'.$item->recovery_agent_id.'"
                               data-id="'.$item->id.'"
                               data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                               data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                               title="Add Comment"
                               class="view-comments d-flex align-items-center justify-content-center border-0"
                               style="background-color:#D1ECF1; color:#0C5460; border-radius:8px; width:35px; height:35px;">
                                <i class="bi bi-chat-left-text fs-5"></i>
                            </a>
                        
                            <!-- View Logs -->
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#showLogModal"
                               data-agent_id="'.$item->recovery_agent_id.'"
                               data-id="'.$item->id.'"
                               data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                               data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                               title="Logs"
                               class="view-comments d-flex align-items-center justify-content-center border-0"
                               style="background-color:#E2E3E5; color:#383D41; border-radius:8px; width:35px; height:35px;">
                                <i class="bi bi-clock-history fs-5"></i>
                            </a>
                                ';
                        $agent_id = '';
                        
                        if($item->status != "closed"){
                        if($item->is_agent_assigned){
                            $agent_id =$item->recovery_agent_id;
                            $action .=' 
                                <!-- Update Status -->
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#updateStatusModal"
                               data-agent_id="'.$item->recovery_agent_id.'"
                               data-id="'.$item->id.'"
                               data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                               data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                               title="Update Status"
                               class="d-flex align-items-center justify-content-center border-0"
                               style="background-color:#FFF3CD; color:#856404; border-radius:8px; width:35px; height:35px;">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#vehicleRequestModal"
                               data-agent_id="'.$agent_id.'" data-id="'.$item->id.'" data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'" data-get_city_id="'.$item->assignment->vehicleRequest->city_id  
                               
                               .'" title="Re-Assign Agent"
                               class="d-flex align-items-center justify-content-center border-0"
                               style="border-radius:8px;cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 27 27" fill="none">
                                <rect width="27" height="27" rx="8" fill="#EECACB"/>
                                <path d="M13.5003 8.00016C14.5128 8.00016 15.3337 7.17935 15.3337 6.16683C15.3337 5.15431 14.5128 4.3335 13.5003 4.3335C12.4878 4.3335 11.667 5.15431 11.667 6.16683C11.667 7.17935 12.4878 8.00016 13.5003 8.00016Z" stroke="#721c24" stroke-width="1.375"/>
                                <path d="M11.6667 6.1665H8" stroke="#721c24" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.9997 6.1665H15.333" stroke="#721c24" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.75 20.8335C9.53313 20.801 8.82533 20.676 8.38662 20.196C7.7935 19.547 7.97186 18.555 8.3286 16.571L8.88907 13.4541C9.11388 12.2037 9.22629 11.5786 9.55325 11.1042C9.87486 10.6376 10.3408 10.2718 10.8902 10.0544C11.4487 9.8335 12.1325 9.8335 13.5 9.8335C14.8675 9.8335 15.5513 9.8335 16.1098 10.0544C16.6592 10.2718 17.1251 10.6376 17.4468 11.1042C17.7737 11.5786 17.8862 12.2037 18.1109 13.4541L18.6714 16.571C19.0281 18.555 19.2065 19.547 18.6134 20.196C18.1767 20.6738 17.4733 20.8001 16.2665 20.8335" stroke="#721c24" stroke-width="1.375" stroke-linecap="round"/>
                                <path d="M13.5 19V22.6667" stroke="#721c24" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            </div>'; 
                            
                           
                        } else{
                           $action .=' 
                               <!-- Update Status -->
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#updateStatusModal"
                               data-agent_id="'.$item->recovery_agent_id.'"
                               data-id="'.$item->id.'"
                               data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'"
                               data-get_city_id="'.$item->assignment->vehicleRequest->city_id.'"
                               title="Update Status"
                               class="d-flex align-items-center justify-content-center border-0"
                               style="background-color:#FFF3CD; color:#856404; border-radius:8px; width:35px; height:35px;">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </a>
                            <a href="javascript:void(0);"
                               data-bs-toggle="modal"
                               data-bs-target="#vehicleRequestModal"
                               data-agent_id="'.$agent_id.'" data-id="'.$item->id.'" data-get_zone_id="'.$item->assignment->vehicleRequest->zone_id.'" data-get_city_id="'.$item->assignment->vehicleRequest->city_id
                               
                               .'" title="Assign Agent"
                               class="d-flex align-items-center justify-content-center border-0"
                               style="border-radius:8px;cursor:pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 27 27" fill="none">
                                <rect width="27" height="27" rx="8" fill="#D0ACFF"/>
                                <path d="M13.5003 8.00016C14.5128 8.00016 15.3337 7.17935 15.3337 6.16683C15.3337 5.15431 14.5128 4.3335 13.5003 4.3335C12.4878 4.3335 11.667 5.15431 11.667 6.16683C11.667 7.17935 12.4878 8.00016 13.5003 8.00016Z" stroke="#9747FF" stroke-width="1.375"/>
                                <path d="M11.6667 6.1665H8" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.9997 6.1665H15.333" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.75 20.8335C9.53313 20.801 8.82533 20.676 8.38662 20.196C7.7935 19.547 7.97186 18.555 8.3286 16.571L8.88907 13.4541C9.11388 12.2037 9.22629 11.5786 9.55325 11.1042C9.87486 10.6376 10.3408 10.2718 10.8902 10.0544C11.4487 9.8335 12.1325 9.8335 13.5 9.8335C14.8675 9.8335 15.5513 9.8335 16.1098 10.0544C16.6592 10.2718 17.1251 10.6376 17.4468 11.1042C17.7737 11.5786 17.8862 12.2037 18.1109 13.4541L18.6714 16.571C19.0281 18.555 19.2065 19.547 18.6134 20.196C18.1767 20.6738 17.4733 20.8001 16.2665 20.8335" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round"/>
                                <path d="M13.5 19V22.6667" stroke="#9747FF" stroke-width="1.375" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            </div>';
                        }   
                        }
                        
                        $created_by = 'Unknown';
                        if($item->created_by_type == 'b2b-web-dashboard'){
                            $created_by = 'Customer';
                        }elseif($item->created_by_type == 'b2b-admin-dashboard'){
                            $created_by = 'Admin';
                        }
                        // Aging
                        if ($item->status === 'closed' && $item->closed_at) {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(\Carbon\Carbon::parse($item->closed_at), true);
                        } else {
                            $aging = \Carbon\Carbon::parse($item->created_at)
                                        ->diffForHumans(now(), true);
                        }
        
                        // NULL-safe values using data_get
                        $requestId  = data_get($item, 'assignment.VehicleRequest.req_id', 'N/A');
                        $regNumber  = data_get($item, 'assignment.vehicle.permanent_reg_number', '');
                        $chassis    = data_get($item, 'assignment.vehicle.chassis_number', '');
                        $riderName  = data_get($item, 'rider.name', '');
                        $riderPhone = data_get($item, 'rider.mobile_no', '');
                        $clientName = data_get($item, 'rider.customerlogin.customer_relation.trade_name', '');
                        $cityName = data_get($item, 'assignment.VehicleRequest.city.city_name', 'N/A');
                        $zoneName = data_get($item, 'assignment.VehicleRequest.zone.name', 'N/A');
        
                        $createdAt  = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') : '';
                        $updatedAt  = $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y, h:i A') : '';
        
                       
        
                        return [
                            '<div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                    name="is_select[]" type="checkbox" value="'.$item->id.'">
                            </div>',
                            e($requestId),
                            e($regNumber),
                            e($chassis),
                            e($riderName),
                            e($riderPhone),
                            e($clientName),
                            e($cityName),
                            e($zoneName),
                            $created_by,
                            $createdAt,
                            $updatedAt,
                            // $comments,
                            $agentStatusColumn,
                            $aging,
                            $statusColumn,
                            $action
                            
                        ];
                    });
                    
                 
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Recovery Request List Error: '.$e->getMessage().' on line '.$e->getLine());
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'Something went wrong: '.$e->getMessage()
                    ], 500);
                }
            }
            
            $user = User::find(Auth::id());
        
            if(in_array($user->role, [1, 13])){
                    $cities = City::where('status', 1)->get();
                    $zones = '';
                    $agents =Deliveryman::where('work_type','in-house')->where('team_type',22)->where('delete_status', 0)->get();
                }else{
                    $cities = City::where('id',$user->city_id )->where('status', 1)->get();
                    $zones = Zones::where('city_id',$user->city_id )->where('status', 1)->get();
                    $agents =Deliveryman::where('current_city_id',$user->city_id)->where('work_type','in-house')->where('team_type',22)->where('delete_status', 0)->get();
                }
            
            return view('recoverymanager::recovery.list' , compact('cities','agents','zones','type'));
        }
        
        public function getAgent(Request $request)
        {
            $zoneId = $request->zone_id;
            $cityId = $request->city_id;
            if (!$zoneId) {
                return response()->json(['success' => false, 'message' => 'Zone ID missing']);
            }
            
            if (!$cityId) {
                return response()->json(['success' => false, 'message' => 'City ID missing']);
            }
            
            $agents = Deliveryman::where('current_city_id', $cityId) // static for now, can make dynamic
                ->where('work_type', 'in-house')
                ->where('team_type', 22)
                ->where('zone_id', $zoneId)
                ->where('delete_status', 0)
                ->select('id', 'first_name', 'last_name')
                ->get();
        
            return response()->json([
                'success' => true,
                'agents' => $agents
            ]);
        }

        public function view(Request $request , $id)
    {
       $recovery_id = decrypt($id);
       
       $data = B2BRecoveryRequest::with('rider','assignment','recovery_agent')->find($recovery_id);
                
        
        return view('recoverymanager::recovery.view' , compact('data'));
    }

     public function export(Request $request)
        {

            $fields    = $request->input('fields', []);  
            $from_date = $request->input('from_date');
            $to_date   = $request->input('to_date');
            $zone = $request->input('zone_id')?? null;
            $city = $request->input('city_id')?? null;
            $status = $request->input('status')?? null;
            $selectedIds = $request->input('selected_ids', []);
    
        
            if (empty($fields)) {
                return back()->with('error', 'Please select at least one field to export.');
            }
            
            $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
               $formattedFields = array_map(function($f) {
                return ucwords(str_replace('_', ' ', $f));
            }, $fields);
            
            $fieldsText = implode(', ', $formattedFields ?: ['ALL']);
            $zoneName = $zone ? Zones::where('id', $zone)->value('name') : 'N/A';
            $cityName = $city ? City::where('id', $city)->value('city_name') : 'N/A';
            $statusText = ucwords($status)??'All';
            $longDesc = "Requested recovery request export. From: {$from_date}, To: {$to_date}, Fields: {$fieldsText}, Selected IDs: " . (empty($selectedIds) ? 'ALL' : implode(',', $selectedIds)) . ", City: " . ($cityName ?? 'N/A') . ", Zone: " . ($zoneName ?? 'N/A') . ", Status: " . ($statusText ?? 'N/A');

    audit_log_after_commit([
        'module_id'         => 7,
        'short_description' => 'B2B Recovery Request Exported',
        'long_description'  => $longDesc,
        'role'              => $roleName,
        'user_id'           => $user->id ?? null,
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_recovery_request.export',
        'ip_address'        => request()->ip(),
        'user_device'       => request()->userAgent()
    ]);
    
            return Excel::download(
                new B2BRecoveryManagerRequestExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status),
                'recovery-request-list-' . date('d-m-Y') . '.xlsx'
            );
        }
        
        
        public function assignAgent(Request $request)
    {
        
        // Validate input
        $request->validate([
            'request_id' => 'required|integer',
            'agent_id' => 'required|integer',
            'remarks' => 'nullable|string|max:2000'
        ]);
    
        // Fetch recovery request with rider
        $recovery = B2BRecoveryRequest::with('rider.customerLogin.customer_relation','recovery_agent','rider' ,'assignment.vehicle' ,'assignment.VehicleRequest' )
            ->find($request->request_id);
    
        if (!$recovery) {
            return response()->json(['success' => false, 'message' => 'Request not found.']);
        }
    
        if ($recovery->status == 'closed' || $recovery->agent_status == 'recovered') {
            return response()->json(['success' => false, 'message' => 'Request has been closed already.']);
        }
        
        // $isReassigned = $recovery->is_agent_assigned == 1;
        // $recovery_agent_name = '';
        // if($isReassigned && $recovery->recovery_agent_id && $recovery->agent){
        //     $recovery_agent_name = $recovery->agent->first_name . $recovery->agent->last_name ;
        // }
        // // Update recovery request
        $recovery->recovery_agent_id = $request->agent_id;
        $recovery->status = 'agent_assigned';
        $recovery->agent_status = 'opened';
        $recovery->city_manager_id = Auth::id();
        $recovery->is_agent_assigned = 1;
        $recovery->save();
        
        $agent = Deliveryman::find($request->agent_id);
        // // Log assignment
        B2BVehicleAssignmentLog::create([
            'assignment_id' => $recovery->assign_id,
            'status'        => 'agent_assigned',
            'remarks'       => $agent->first_name . ' ' . $agent->last_name . '(Recovery Agent) has been assigned to this request.',
            'action_by'     => Auth::id(),
            'type'          => 'recovery-manager-dashboard',
            'request_type'  => 'recovery_request',
            'request_type_id'=> $recovery->id
        ]);
    
        RecoveryComment::create([
            'req_id'    => $recovery->id,
            'status'    => 'agent_assigned',
            'comments'  => $request->remarks,
            'user_id'   => Auth::id(),
            'user_type' => 'recovery-manager-dashboard',
        ]);
    
        // =======================
        // Send Email Notification
        // =======================

        $manager = Auth::user();
    
        // Admins and Superadmins
        $admins = User::whereIn('role', [1,13])
            ->where('status', 'Active')
            ->pluck('email')
            ->toArray();
    
        // Customer email
        $customerEmail = $recovery->rider->customerLogin->customer_relation->email ?? [];
    
        // Recipients array
        $recipients = [
            [
                'to'  => $agent->email,
                'cc'  => [$manager->email],
                'bcc' => array_merge($admins, (array)$customerEmail)
            ]
        ];
        
        // $recipients = [
        //     [
        //         'to'  => 'logeshmudaliyar2802@gmail.com',
        //         'cc'  => ['mudaliyarlogesh@gmail.com'],
        //         'bcc' => array_merge(['pratheesh@alabtechnology.com','saran@alabtechnology.com'],['gowtham@alabtechnology.com'])
        //     ]
        // ];
        
        // Footer content
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
    
        // Email body
        $body = '
            <html>
            <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                    <tr>
                        <td style="padding:20px; text-align:center; background:#2196F3; color:#fff;">
                            <h2>New Recovery Request Assigned</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;">
                            <p>Hello <strong>'.$agent->first_name.' '.$agent->last_name.'</strong>,</p>
                            <p>A new recovery request <strong>#'.$recovery->assignment->req_id.'</strong> has been assigned to you.</p>
                            <p>Status: <strong>Opened</strong></p>';
            
            // Add remarks if present
            if (!empty($request->remarks)) {
                $body .= '<p>Remarks: '.$request->remarks.'</p>';
            }
            
            // Add recovery details table
            $body .= '
                            <p><strong>Recovery Details</strong></p>
                            <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 600px;">
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Vehicle Number</strong></td>
                                    <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Chassis Number</strong></td>
                                    <td>'.($recovery->chassis_number ?? 'N/A').'</td>
                                </tr>
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Rider Name</strong></td>
                                    <td>'.($recovery->rider_name ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Rider Contact</strong></td>
                                    <td>'.($recovery->rider_mobile_no ?? 'N/A').'</td>
                                </tr>
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Customer</strong></td>
                                    <td>'.($recovery->client_name ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer Contact</strong></td>
                                    <td>'.($recovery->contact_no ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Previous Agent</strong></td>
                                    <td>'.($recovery_agent_name ?? '').'</td>
                                </tr>
                            </table>
            
                            <p>'.$footerContent.'</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
        
        $subject = "New Recovery Request Assigned: #{$recovery->assign_id}";
        // if($isReassigned){
        //     $subject = "Reassigned Recovery Request: #{$recovery->assign_id}";
        // }
        
        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    
        $agentName = $agent ? ($agent->first_name . ' ' . $agent->last_name) : 'N/A';
        $remarksShort = \Str::limit($request->remarks, 300);
        audit_log_after_commit([
            'module_id'         => 7,
            'short_description' => 'Recovery Agent Assigned',
            'long_description'  => "Request ID: {$recovery->id}. Agent assigned: {$agentName} (ID: {$request->agent_id}). Remarks: " . ($remarksShort ?: 'N/A'),
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'b2b_recovery_request.assignAgent',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
    
        // Send the email
        $this->sendDynamicEmailNotify($recipients, $subject, $body, false);
        $this->AutoSendWhatsAppMessage($recovery,'agent_assign_notify',null);
        $this->pushAgentNotificationSent($agent,$recovery);
    
        return response()->json([
            'success' => true,
            'message' => 'Agent assigned successfully!'
        ]);
    }
    

   public function AutoSendWhatsAppMessage($recovery,$forward_type,$status='null')
    {
            // $recovery = B2BRecoveryRequest::with('rider.customerLogin.customer_relation','recovery_agent','rider' 
            // ,'assignment.vehicle' ,'assignment.VehicleRequest' )->find($request_id); 
            
            if (!$recovery) {
                Log::info('Assign Recovery Agent : Recovery Request not found');
                return false;
            }
            
            if($recovery->recovery_agent_id){
                $agent = Deliveryman::find($recovery->recovery_agent_id);
                $agentName    = $agent->first_name .' '. $agent->first_name  ?? 'Agent';
                $agentPhone   = $agent->mobile_number ;
                
                if (!$agent || !$agent->mobile_number) {
                Log::info('Assign Recovery Agent : Agent or mobile number not found');
                return false;
                }
            }
            
            $manager = Auth::user();

            if (!$manager || !$manager->phone) {
                Log::info('Assign Recovery Agent : Manager or mobile number not found');
                return false;
            }
            

            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            // $url = 'https://whatshub.in/api/whatsapp/send';
            BusinessSetting::where('key_name', 'whatshub_api_url')->value('value');
        
            
            $requestId    = $recovery->assignment->req_id ?? '';
            $customerID   = $recovery->rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName = $recovery->rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail= $recovery->rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone= $recovery->rider->customerLogin->customer_relation->phone ?? '';
            //vehicle details
            $AssetvehicleId    = $recovery->assignment->asset_vehicle_id ?? 'N/A'; 
            $vehicleNo    = $recovery->assignment->vehicle->permanent_reg_number ?? 'N/A'; 
            $vehicleType  = $recovery->assignment->vehicle->vehicle_type_relation->name ?? 'N/A'; 
            $vehicleModel  = $recovery->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'; 
            $cityData = City::select('city_name')->where('id',$manager->city_id)->first();
            $zoneData = Zones::select('name')->where('id',$manager->zone_id)->first();
            //agent details
            $assignBy_managerName    = $manager->name;
            $assignBy_managerPhone   = $manager->phone;
            $assignBy_managerCity = 'N/A';
            $assignBy_managerZone = 'N/A';

            $reasonMap = [
                1 => 'Breakdown',
                2 => 'Battery Drain',
                3 => 'Accident',
                4 => 'Rider Unavailable',
                5 => 'Other',
            ];

            $reason = $reasonMap[$recovery->reason] ?? 'Unknown';
            if($cityData){
                $assignBy_managerCity = $cityData->city_name;
            }
            if($zoneData){
                $assignBy_managerZone = $zoneData->name;
            }
            //   dd($vehicle_id,$AssetvehicleId,$vehicleNo,$vehicleType,$vehicleModel,$agentName,$agentPhone,$requestId,$customerID,$customerName,$customerEmail,$customerPhone,$assignBy_managerName,$assignBy_managerPhone,$assignBy_managerCity,$assignBy_managerZone);

            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";

            if($forward_type == 'agent_assign_notify'){
                $agent_message = 
                    "Hello {$agentName},\n\n" .
                    "Recovery Manager has been successfully assigned a recovery request to you.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n" .
                    "• Recovery Reason: {$reason}\n" .
                    "• Recovery Description: {$recovery->description}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "{$footerContentText}";
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "A Recovery Request Id :#{requestId} has been assigned to agent.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n" .
                    "• Recovery Reason: {$reason}\n" .
                    "• Recovery Description: {$recovery->description}\n\n" .
                    "*Agent Information:*\n" .
                    "• Name: {$agentName}\n" .
                    "• Phone: {$agentPhone}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Assigned By:* {$assignBy_managerName}\n" .
                    "📍 *Assigned Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                    "{$footerContentText}";

                $manager_message = 
                "Hello {$assignBy_managerName},\n\n" .
                "You have successfully assigned a Recovery Request.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Rider Information:*\n" .
                "• Name: {$agentName}\n" .
                "• Phone: {$agentPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "📍 *Assigned Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A new recovery request has been assigned to agent.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "*Agent Information:*\n" .
                "• Name: {$agentName}\n" .
                "• Phone: {$agentPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Assigned By:* {$assignBy_managerName}\n" .
                "📍 *Assigned Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
            }
            
            if($forward_type == 'manager_status_update_whatsapp_notify'){
                if(!$status || !in_array($status, ['closed', 'not_recovered'])){
                   Log::info('Recovery Status Update by Manager : Status not available or it is invalid');
                    return false; 
                }
                    $statusLabel = [
                        "closed" => "Closed",
                        "not_recovered" => "Not Recovered"
                    ];
                    $statusText = $statusLabel[$status] ?? ucfirst($status);
                $agent_content = '';
                if($recovery->recovery_agent_id){
                    $agent_content = 
                        "*Agent Information:*\n" .
                        "• Name: {$agentName}\n" .
                        "• Phone: {$agentPhone}\n\n";
                    
                    $agent_message = 
                        "Hello {$agentName},\n\n" .
                        "Your Recovery Request Id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                        "📌 *Request Details:*\n" .
                        "• Request ID: {$requestId}\n\n" .
                        "*Vehicle Information:*\n" .
                        "• Vehicle ID: {$AssetvehicleId}\n" .
                        "• Vehicle No: {$vehicleNo}\n" .
                        "• Vehicle Type: {$vehicleType}\n" .
                        "• Vehicle Model: {$vehicleModel}\n\n" .
                        "{$footerContentText}";
                }
                    
                
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "A recovery request id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                     "{$agent_content}".
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Managed By:* {$assignBy_managerName}\n" .
                    "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                    "{$footerContentText}";

                $manager_message = 
                "Hello {$assignBy_managerName},\n\n" .
                "You have successfully updated the status of recovery request id :#{$requestId} to {$statusText}.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "{$agent_content}".
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A recovery request id :#{$requestId} status has been changed to {$statusText}.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "{$agent_content}".
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Managed By:* {$assignBy_managerName}\n" .
                "📍 *Manager Zone:* {$assignBy_managerZone}, {$assignBy_managerCity}\n\n" .
                "{$footerContentText}";
            }
            
            
            // Rider message
            if($recovery->recovery_agent_id){
                if (!empty($agentPhone)) {
                    // CustomHandler::user_whatsapp_message('+917812880655', $agent_message);
                    CustomHandler::user_whatsapp_message($agentPhone, $agent_message);
                }
            }

            // Customer message
            if (!empty($customerPhone)) {
                // CustomHandler::user_whatsapp_message('+917812880655', $customer_message);
                CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
            }
            // Agent message
            if (!empty($assignBy_managerPhone)) {
                // CustomHandler::user_whatsapp_message('+917812880655', $manager_message);
                CustomHandler::user_whatsapp_message($assignBy_managerPhone, $manager_message);

            }
            
            $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
            if (!empty($adminPhone)) {

                CustomHandler::admin_whatsapp_message($admin_message);
            }
           
    
        }

    public function pushAgentNotificationSent($agent_Arr,$recovery)
    {
        $svc = new FirebaseNotificationService();
        $title = 'New Recovery Request Assigned!';
        $image = null;
        $notifications = [];
        
        
        $vehicleNumber  = $recovery->assignment->vehicle->permanent_reg_number ?? '-';
        $chassisNumber  = $recovery->assignment->vehicle->chassis_number ?? '-';
        $make           = $recovery->assignment->vehicle->vehicle_model_relation->make ?? '-';
        $riderName      = $recovery->rider->name ?? '-';
        $riderNumber    = $recovery->rider->mobile_no ?? '-';
        $agentName      = trim($agent_Arr->first_name . ' ' . $agent_Arr->last_name);
        $request_id     = $recovery->assignment->VehicleRequest->req_id;
        $bodyTemplate = "Dear {$agentName},\n\n".
                        "A new Recovery Request (Request ID: {$request_id}) has been assigned to you.\n\n".
                        "Vehicle Details:\n".
                        "Vehicle Number: {$vehicleNumber}\n".
                        "Chassis Number: {$chassisNumber}\n".
                        "Make: {$make}\n\n".
                        "Rider Details:\n".
                        "Name: {$riderName}\n".
                        "Contact Number: {$riderNumber}";
                        

        
            $agentId    = $agent_Arr->id;
            $token      = $agent_Arr->fcm_token;
            $body       = $bodyTemplate;
            $data       = [];
            $icon       = null; 
            
            // if ($token) {
            //     $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
            // }
    
             $notifications[] = [
                'title'    => $title,
                'description' => $bodyTemplate,
                'image'    => $image,
                'status'   => 1,
                'agent_id' => $agentId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        
        
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_recovery_agent_notifications')->insert($notifications);
        }

    }


         public function updateStatus(Request $request)
    {
      
        $request->validate([
            'request_id' => 'required|integer',
            'agent_id' => 'nullable|integer',
            'update_status'   =>'required|string',
            'remarks' => 'nullable|string|max:2000'
        ]);

        DB::beginTransaction();
    
        try {
        
        $recovery = B2BRecoveryRequest::with('rider','assignment','recovery_agent')->find($request->request_id);

        if (!$recovery) {
            return response()->json(['success' => false, 'message' => 'Request not found.']);
        }
        if($recovery->status == 'closed'){
            return response()->json(['success' => false, 'message' => 'Request already been closed.']);
        }
        $oldStatus = $recovery->status;
        $recovery->status = $request->update_status;
        
        if($request->update_status == 'closed' && $request->agent_id){
           $recovery->agent_status = 'closed';  
           $recovery->closed_by = Auth::id() ?? null; 
           $recovery->closed_by_type = 'recovery-manager-dashboard'; 
           $recovery->closed_at = now(); 
           if ($recovery->assignment) {
                $recovery->assignment->status = 'recovered';
                $recovery->assignment->save();
                }
        }
        elseif($request->update_status == 'not_recovered' && $request->agent_id){
           $recovery->agent_status = 'not_recovered';  
        }
        else{
           $recovery->status = $request->update_status;  
        }
        $recovery->city_manager_id = Auth::id() ?? null;
        $recovery->save(); 

        if($request->update_status == 'closed'){
          B2BVehicleAssignmentLog::create([
            'assignment_id' => $recovery->assign_id,
            'status'        => 'closed',
            'remarks'       => 'Recovery request has been closed By Recovery Manager after completing all necessary actions.',
            'action_by'     => Auth::id() ?? null,
            'type'          => 'recovery-manager-dashboard',
            'request_type'  => 'recovery_request',
            'request_type_id'=>$recovery->id??null
        ]); 
        
        RecoveryComment::create([
            'req_id' => $recovery->id,
            'status'        => 'closed',
            'comments'       => $request->remarks,
            'user_id'     => Auth::id() ?? null,
            'user_type' => 'recovery-manager-dashboard',
        ]);
        
        }
        elseif($request->update_status == 'not_recovered'){
          B2BVehicleAssignmentLog::create([
            'assignment_id' => $recovery->assign_id,
            'status'        => 'not_recovered',
            'remarks'       => 'We could not recover the vehicle after all attempts. Case closed as not recovered.',
            'action_by'     => Auth::id() ?? null,
            'type'          => 'recovery-manager-dashboard',
            'request_type'  => 'recovery_request',
            'request_type_id'=>$recovery->id??null
        ]); 
        RecoveryComment::create([
            'req_id' => $recovery->id,
            'status'        => 'not_recovered',
            'comments'       => $request->remarks,
            'user_id'     => Auth::id() ?? null,
            'user_type'  => 'recovery-manager-dashboard',
        ]);
        }
        
        
           if($request->update_status == 'closed'){
               
            $vehicleID = $recovery->assignment->asset_vehicle_id ?? null;
        
            if ($vehicleID) {
                
                $vehicle = AssetMasterVehicle::find($vehicleID);
        
                if ($vehicle) {
                    
                    $inventory = AssetVehicleInventory::where('asset_vehicle_id', $vehicle->id)->first();
        
                    $fromStatus = $inventory->transfer_status ?? null; // Save old status
                    $toStatus = 24; // Recovered - Pending QC
        
                    $from_status_name = $inventory->inventory_location->name ?? null;
                    $vehicle->update([
                        'client' => null,
                        'vehicle_delivery_date' => null,
                    ]);
        
                    AssetVehicleInventory::where('asset_vehicle_id', $vehicle->id)
                        ->update(['transfer_status' => $toStatus]);
        
                    $user = User::find(Auth::id());
                    $RoleName = ucwords($user->get_role->name ?? 'Unknown Role');
                    $ManagerName = ucwords($user->name);
                    $remarks = "Vehicle {$vehicle->permanent_reg_number} recovered by {$ManagerName} ({$RoleName}) and inventory updated from '{$from_status_name}' to 'Recovered - Pending QC'.";
        
                    VehicleTransferChassisLog::create([
                        'chassis_number' => $vehicle->chassis_number ?? null,
                        'vehicle_id' => $vehicle->id,
                        'from_location_source' => $fromStatus, // previous inventory status
                        'to_location_destination' => $toStatus,
                        'status' => 'updated',
                        'remarks' => $remarks,
                        'created_by' => Auth::id() ?? null,
                        'type' => 'gdm-dashboard'
                    ]);
                    
                    
                     // FIELDPROXY TICKET RAISE SECTION START
            
                        $ticket_id = CustomHandler::GenerateTicketId($vehicle->quality_check->location);
                           
                            if ($ticket_id == "" || $ticket_id == null) {
                                
                                   Log::error('TICKET ID creation failed', [
                                        'ticket_id' => $ticket_id
                                    ]);

                                return response()->json(['success' => false,'message'  =>'Ticket ID creation failed']);
                            }
                            
                         $customer = optional(optional($vehicle)->quality_check)->accountability_type == 2
                            ? optional(optional($vehicle)->quality_check)->customer_relation
                            : optional($vehicle)->customer_relation;
            
                        
                            $ticket = VehicleTicket::create([
                            'ticket_id'         => $ticket_id,
                            'vehicle_no'        => $vehicle->permanent_reg_number ?? '',
                            'city_id'           => $vehicle->quality_check->location ?? '',
                            'area_id'           => $vehicle->quality_check->zone_id ?? '',
                            'vehicle_type'      => $vehicle->vehicle_type ?? '',
                            'poc_name'          => $customer->trade_name ?? '',
                            'poc_contact_no'    => $customer->phone ?? '',
                            'issue_remarks'     => 'Recovery request has been closed by the Recovery Manager. A service ticket has been generated for inspection and corrective action.',
                            'repair_type'       => 6,
                            'address'           => '',
                            'gps_pin_address'   => '',
                            'lat'               => '',
                            'long'              => '',
                            'driver_name'       =>  $recovery->assignment->rider->name ?? '',
                            'driver_number'     =>  $recovery->assignment->rider->mobile_no ?? '',
                            'image'             => '',
                            'created_datetime'  => now(),                                                                                                          
                            'created_by'        => $user->id,
                            'created_role'      => '',
                            'customer_id'             => '',
                            'web_portal_status' => 0,
                            'platform'          => 'recovery-manager-dashboard',
                            'ticket_status'     => 0,
                        ]);
                    
                        $city = City::find($vehicle->quality_check->location);
            
                        $createdDatetime = Carbon::now()->utc();
                        
                        $customerLongitude = '';
                        $customerLatitude  = '';
                            
                         $ticketData = [
                            "vehicle_number" => $vehicle->permanent_reg_number ?? '',
                            "updatedAt" => $createdDatetime,
                            "ticket_status" => "unassigned",
                            "chassis_number" => $vehicle->chassis_number ?? null,
                            "telematics" => $vehicle->telematics_imei_number ?? null,
                            "battery" => $vehicle->battery_serial_no ?? null,
                            "vehicle_type" => $vehicle->vehicle_type_relation->name ?? null,
                            "state" => $city->state->state_name ?? '',
                            "priority" => 'High',
                            "point_of_contact_info" => $customer->phone.' - '. $customer->trade_name,
                            "job_type" => 'Vehicle audit',
                            "issue_description" => 'A service ticket has been generated for inspection and corrective action.',
                            'image' => [],
                            'address'   => '',
                            "greendrive_ticketid" => $ticket_id,
                            'driver_name'   => $recovery->assignment->rider->name ?? '',
                            'driver_number'   => $recovery->assignment->rider->mobile_no ?? '',
                            "customer_number" => $customer->phone ?? '',
                            "customer_name" => $customer->trade_name ?? '',
                            'customer_email' => $customer->email ?? '',
                            'customer_location' => [null, null],
                            "current_status" => 'open',
                            "createdAt" => $createdDatetime,
                            "city" => $city->city_name ?? null,
                        ];
                        
                        
                        $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                            'created_by' => $user->id,
                            'type'       => 'recovery-manager-dashboard',
                        ]));
                        
                        
                        FieldProxyLog::create([
                            'fp_id'      => $fieldProxyTicket->id,   
                            'status'     => 'unassigned',  
                            "current_status" => 'open',
                            'remarks'    => 'Recovery request has been closed by the Recovery Manager. A service ticket has been generated for inspection and corrective action.',
                            'created_by' => $user->id,
                            'type'       => 'recovery-manager-dashboard',
                        ]);
                        
                        $apiTicketData = $ticketData;
                        $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
                        $apiTicketData['customer_number'] = preg_replace('/^\+91/', '', $ticketData['customer_number']);
                        
                        
                        
                        $fieldproxy_base_url = BusinessSetting::where('key_name', 'fieldproxy_base_url')->value('value');
                        $fieldproxy_create_endpoint = BusinessSetting::where('key_name', 'fieldproxy_create_enpoint')->value('value');
                        
                        $apiData = [
                            "sheetId" => "tickets",
                            "tableData" => $apiTicketData
                        ];
                        
                        $apiUrl = $fieldproxy_base_url . $fieldproxy_create_endpoint;
                        
                        $apiKey = env('FIELDPROXY_API_KEY', null); 
                
                        $ch = curl_init($apiUrl);
                        $payload = json_encode($apiData);
                
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "x-api-key: {$apiKey}",
                            "Content-Type: application/json",
                            "Accept: application/json"
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                
                        $responseBody = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curlError = curl_error($ch);
                        curl_close($ch);
                
                        $fieldproxyResult = null;
                        
                        // ========== THROW ERROR TO TRIGGER ROLLBACK ==========
                        if ($curlError) {
                            throw new \Exception("FieldProxy cURL Error: {$curlError}");
                        }
                
                        if ($httpCode >= 400) {
                            throw new \Exception("FieldProxy HTTP {$httpCode} Error: {$responseBody}");
                        }
                
                        $decoded = json_decode($responseBody, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new \Exception("FieldProxy returned invalid JSON");
                        }
                        // ======================================================
                        
                     // FIELDPROXY TICKET RAISE SECTION END
                    
                }
            }
           }
           
        
        
        
            $statusLabel = [
                        "closed" => "Closed",
                        "not_recovered" => "Not Recovered"
                    ];
            $statusText = $statusLabel[$recovery->status] ?? ucwords(str_replace('_', ' ', $recovery->status));
            $manager = User::find($recovery->city_manager_id);
            $admins = User::whereIn('role', [1,13])
            ->where('status', 'Active')
            ->pluck('email')
            ->toArray();
            $agentName = 'Agent'; 
            $agentPhone = 'N\A'; 
            $agentEmail = '';
            if($recovery->is_agent_assigned){
                $agentName = $recovery->recovery_agent->first_name .' '. $recovery->recovery_agent->last_name ?? 'Agent'; 
            $agentPhone = $recovery->recovery_agent->mobile_number ?? 'N\A'; 
            $agentEmail = $recovery->recovery_agent->email ?? '';
            }
            
            $requestId    = $recovery->assignment->req_id ?? '';
            $customerID   = $recovery->rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName = $recovery->rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail= $recovery->rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone= $recovery->rider->customerLogin->customer_relation->phone ?? '';
            //vehicle details
            $AssetvehicleId    = $recovery->assignment->asset_vehicle_id ?? 'N/A'; 
            $vehicleNo    = $recovery->assignment->vehicle->permanent_reg_number ?? 'N/A'; 
            $vehicleType  = $recovery->assignment->vehicle->vehicle_type_relation->name ?? 'N/A'; 
            $vehicleModel  = $recovery->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A'; 
            $cityData = City::select('city_name')->where('id',$manager->city_id)->first();
            $zoneData = Zones::select('name')->where('id',$manager->zone_id)->first();
            //agent details
            $assignBy_managerName    = $manager->name;
            $assignBy_managerPhone   = $manager->phone;
            $assignBy_managerEmail   = $manager->email ?? '';
            $assignBy_managerCity = 'N/A';
            $assignBy_managerZone = 'N/A';

            $reasonMap = [
                1 => 'Breakdown',
                2 => 'Battery Drain',
                3 => 'Accident',
                4 => 'Rider Unavailable',
                5 => 'Other',
            ];

            $reason = $reasonMap[$recovery->reason] ?? 'Unknown';
            if($cityData){
                $assignBy_managerCity = $cityData->city_name;
            }
            if($zoneData){
                $assignBy_managerZone = $zoneData->name;
            }
            //   dd($vehicle_id,$AssetvehicleId,$vehicleNo,$vehicleType,$vehicleModel,$agentName,$agentPhone,$requestId,$customerID,$customerName,$customerEmail,$customerPhone,$assignBy_managerName,$assignBy_managerPhone,$assignBy_managerCity,$assignBy_managerZone);

            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContent = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";
            
                    $adminRecipients = [
                            [
                                'to'  => $admins,
                                'cc'  => [],
                                'bcc' => []
                            ]
                        ];
                        $agentRecipients = [
                            [
                                'to'  => $agentEmail,
                                'cc'  => [],
                                'bcc' => []
                            ]
                        ];
                        $managerRecipients = [
                            [
                                'to'  => $assignBy_managerEmail,
                                'cc'  => [],
                                'bcc' => []
                            ]
                        ];
                        $customerRecipients = [
                            [
                                'to'  => $customerEmail,
                                'cc'  => [],
                                'bcc' => []
                            ]
                        ];
        
        // =======================
        // Agent Email
        // =======================
        $agentSubject = "Recovery Request #{$requestId} Status Updated to {$statusText}";
        $agentContent = '';
        if($recovery->recovery_agent_id){
            $agentContent = '
              <p><strong>Agent Information</strong></p>
              <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                <tr><td><strong>Name</strong></td><td>'.$agentName.'</td></tr>
                <tr><td><strong>Phone</strong></td><td>'.$agentPhone.'</td></tr>
                </table>';
            
        }
        $agentBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#007BFF; color:#fff;">
                        <h2>Recovery Request Update</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Hello <strong>'.$agentName.'</strong>,</p>
                        <p>Your recovery request <strong>#'.$requestId.'</strong> status has been updated to <strong>'.$statusText.'</strong>.</p>
        
                        <p><strong>Request Details</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                            <tr><td><strong>Request ID</strong></td><td>'.$requestId.'</td></tr>
                            <tr><td><strong>Vehicle ID</strong></td><td>'.$AssetvehicleId.'</td></tr>
                            <tr><td><strong>Vehicle No</strong></td><td>'.$vehicleNo.'</td></tr>
                            <tr><td><strong>Vehicle Type</strong></td><td>'.$vehicleType.'</td></tr>
                            <tr><td><strong>Vehicle Model</strong></td><td>'.$vehicleModel.'</td></tr>
                        </table>
        
                        <p style="margin-top:20px;">'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';


        // =======================
        // Customer Email
        // =======================
        $customerSubject = "Recovery Request #{$requestId} – Status Updated to {$statusText}";
        $customerBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#28A745; color:#fff;">
                        <h2>Recovery Request Update</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Hello <strong>'.$customerName.'</strong>,</p>
                        <p>Your recovery request <strong>#'.$requestId.'</strong> status has been updated to <strong>'.$statusText.'</strong>.</p>
                        '.$agentContent.'
        
                        <p style="margin-top:15px;"><strong>Vehicle Information</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                            <tr><td><strong>Vehicle ID</strong></td><td>'.$AssetvehicleId.'</td></tr>
                            <tr><td><strong>Vehicle No</strong></td><td>'.$vehicleNo.'</td></tr>
                            <tr><td><strong>Vehicle Type</strong></td><td>'.$vehicleType.'</td></tr>
                            <tr><td><strong>Vehicle Model</strong></td><td>'.$vehicleModel.'</td></tr>
                        </table>
        
                        <p style="margin-top:15px;">
                            <strong>Managed By:</strong> '.$assignBy_managerName.'<br>
                            <strong>Manager Zone:</strong> '.$assignBy_managerZone.', '.$assignBy_managerCity.'
                        </p>
        
                        <p style="margin-top:20px;">'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';


        // =======================
        // Manager Email
        // =======================
        $managerSubject = "Recovery Request #{$requestId} Updated to {$statusText}";
        $managerBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#17A2B8; color:#fff;">
                        <h2>Recovery Request Update</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Hello <strong>'.$assignBy_managerName.'</strong>,</p>
                        <p>The recovery request <strong>#'.$requestId.'</strong> status has been updated to <strong>'.$statusText.'</strong>.</p>
                        '.$agentContent.'
                        <p style="margin-top:15px;"><strong>Vehicle Information</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                            <tr><td><strong>Vehicle ID</strong></td><td>'.$AssetvehicleId.'</td></tr>
                            <tr><td><strong>Vehicle No</strong></td><td>'.$vehicleNo.'</td></tr>
                            <tr><td><strong>Vehicle Type</strong></td><td>'.$vehicleType.'</td></tr>
                            <tr><td><strong>Vehicle Model</strong></td><td>'.$vehicleModel.'</td></tr>
                        </table>
        
                        <p style="margin-top:15px;">
                            <strong>Manager Zone:</strong> '.$assignBy_managerZone.', '.$assignBy_managerCity.'
                        </p>
        
                        <p style="margin-top:20px;">'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';


        // =======================
        // Admin Email
        // =======================
        $adminSubject = "Recovery Request #{$requestId} Updated to {$statusText}";
        $adminBody = '
        <html>
        <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                <tr>
                    <td style="padding:20px; text-align:center; background:#6C757D; color:#fff;">
                        <h2>Recovery Request Update</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p>Dear Admin,</p>
                        <p>The recovery request <strong>#'.$requestId.'</strong> has been updated to <strong>'.$statusText.'</strong>.</p>
        
                        <p><strong>Customer Information</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                            <tr><td><strong>Name</strong></td><td>'.$customerName.'</td></tr>
                            <tr><td><strong>Customer ID</strong></td><td>'.$customerID.'</td></tr>
                        </table>

                        '.$agentContent.'
        
                        <p style="margin-top:15px;"><strong>Vehicle Information</strong></p>
                        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
                            <tr><td><strong>Vehicle ID</strong></td><td>'.$AssetvehicleId.'</td></tr>
                            <tr><td><strong>Vehicle No</strong></td><td>'.$vehicleNo.'</td></tr>
                            <tr><td><strong>Vehicle Type</strong></td><td>'.$vehicleType.'</td></tr>
                            <tr><td><strong>Vehicle Model</strong></td><td>'.$vehicleModel.'</td></tr>
                        </table>
        
                        <p style="margin-top:15px;">
                            <strong>Managed By:</strong> '.$assignBy_managerName.'<br>
                            <strong>Manager Zone:</strong> '.$assignBy_managerZone.', '.$assignBy_managerCity.'
                        </p>
        
                        <p style="margin-top:20px;">'.$footerContent.'</p>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        
        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $changeDetails = "Request ID: {$recovery->id}. Status: {$oldStatus} → {$recovery->status}.";
        if (!empty($request->agent_id)) {
            $changeDetails .= " Agent ID: {$request->agent_id}.";
        }
        if (!empty($request->remarks)) {
            $shortRemarks = \Str::limit($request->remarks, 300);
            $changeDetails .= " Remarks: \"{$shortRemarks}\".";
        }

        // If close caused inventory update, add a note
        $vehicleID = $recovery->assignment->asset_vehicle_id ?? null;
        if ($request->update_status == 'closed' && $vehicleID) {
            $changeDetails .= " Vehicle ID {$vehicleID} marked Recovered - Pending QC and transfer_status updated.";
        }

        audit_log_after_commit([
            'module_id'         => 7,
            'short_description' => 'Recovery Request Status Updated',
            'long_description'  => $changeDetails,
            'role'              => $roleName,
            'user_id'           => $user->id ?? null,
            'user_type'         => 'gdc_admin_dashboard',
            'dashboard_type'    => 'web',
            'page_name'         => 'b2b_recovery_request.updateStatus',
            'ip_address'        => request()->ip(),
            'user_device'       => request()->userAgent()
        ]);
        
        $this->AutoSendWhatsAppMessage($recovery,'manager_status_update_whatsapp_notify',$request->update_status);
        if($recovery->is_agent_assigned){
            $this->sendDynamicEmailNotify($agentRecipients, $agentSubject, $agentBody, false);
        }
        
        $this->sendDynamicEmailNotify($customerRecipients, $customerSubject, $customerBody, false);
        $this->sendDynamicEmailNotify($managerRecipients, $managerSubject, $managerBody, false);
        $this->sendDynamicEmailNotify($adminRecipients, $adminSubject, $adminBody, false);
        if(!empty($request->agent_id)){
            
            $agent = Deliveryman::find($request->agent_id);
            
            if(!empty($agent)){
                $this->pushUpdateStatusNotificationSent($agent,$recovery);
                
            }
         
        }
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Recovery update failed', [
                'error_message' => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'trace'         => $e->getTraceAsString(),
                'input'         => $request->all()
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating recovery status.',
                'error' => $e->getMessage()
            ], 500);
    }
    }
    
   


     public function pushUpdateStatusNotificationSent($agent_Arr, $recovery)
    {
        $svc = new FirebaseNotificationService();
        $notifications = [];
    
        $vehicleNumber  = $recovery->assignment->vehicle->permanent_reg_number ?? '-';
        $chassisNumber  = $recovery->assignment->vehicle->chassis_number ?? '-';
        $make           = $recovery->assignment->vehicle->vehicle_model_relation->make ?? '-';
        $riderName      = $recovery->rider->name ?? '-';
        $riderNumber    = $recovery->rider->mobile_no ?? '-';
        $agentName      = trim($agent_Arr->first_name . ' ' . $agent_Arr->last_name);
        $status         = ucfirst(str_replace('_', ' ', $recovery->status ?? ''));
        $requestId      = $recovery->assignment->VehicleRequest->req_id ?? '-';
    
        $agentId  = $agent_Arr->id;
        $token    = $agent_Arr->fcm_token;
        $image    = null;
        $icon     = null;
    
        // Prepare standard message based on recovery status
        switch (strtolower($recovery->status)) {
            case 'closed':
                $title = 'Recovery Request Closed';
                $bodyTemplate = "Dear {$agentName},\n\n".
                    "The recovery request (ID: {$requestId}) has been successfully closed.\n\n".
                    "Vehicle Details:\n".
                    "Vehicle Number: {$vehicleNumber}\n".
                    "Chassis Number: {$chassisNumber}\n".
                    "Make: {$make}\n\n".
                    "Rider Details:\n".
                    "Name: {$riderName}\n".
                    "Contact Number: {$riderNumber}\n\n".
                    "Status: {$status}";
                break;
    
            case 'not_recovered':
                $title = 'Recovery Request Not Recovered';
                $bodyTemplate = "Dear {$agentName},\n\n".
                    "The recovery request (ID: {$requestId}) has been marked as *Not Recovered*.\n\n".
                    "Vehicle Details:\n".
                    "Vehicle Number: {$vehicleNumber}\n".
                    "Chassis Number: {$chassisNumber}\n".
                    "Make: {$make}\n\n".
                    "Rider Details:\n".
                    "Name: {$riderName}\n".
                    "Contact Number: {$riderNumber}\n\n".
                    "Status: {$status}";
                break;
    
            default:
                $title = 'Recovery Status Updated';
                $bodyTemplate = "Dear {$agentName},\n\n".
                    "The status of recovery request (ID: {$requestId}) has been updated.\n\n".
                    "Vehicle Details:\n".
                    "Vehicle Number: {$vehicleNumber}\n".
                    "Chassis Number: {$chassisNumber}\n".
                    "Make: {$make}\n\n".
                    "Rider Details:\n".
                    "Name: {$riderName}\n".
                    "Contact Number: {$riderNumber}\n\n".
                    "Status: {$status}";
                break;
        }
    
        $body = $bodyTemplate;
        $data = [];
    
        // Send FCM notification (uncomment when needed)
        // if ($token) {
        //     $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
        // }
    
        // Log notification to database
        $notifications[] = [
            'title'       => $title,
            'description' => $body,
            'image'       => $image,
            'status'      => 1,
            'agent_id'    => $agentId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_recovery_agent_notifications')->insert($notifications);
        }
    }
    

    
     public function agentList(Request $request, $type) //to be work
    {
        if ($request->ajax()) {
            try {
                $validTypes = ['all', 'active', 'inactive'];
                    if (!in_array($type, $validTypes)) {
                        return response()->json(['error' => 'Invalid agent status'], 422);
                    }
                $start  = $request->input('start', 0);
                $length = $request->input('length', 10);
                $search = $request->input('search.value');
                $from   = $request->input('from_date');
                $to     = $request->input('to_date');
                $city   = $request->input('city_id');
                $zone   = $request->input('zone_id');
                $user = User::find(Auth::id());
                $query = Deliveryman::where('work_type', 'in-house')
                // ->where('current_city_id', $user->city_id)
                ->where('team_type', 22)
                ->where('delete_status', 0)
                ->withCount([
                        'openedRequest as opened_request_count',
                        'closedRequest as closed_request_count'
                    ]);
                $query->when(!in_array($user->role, [1, 13]), function ($q) use ($user) {
                        $q->where('current_city_id', $user->city_id);
                    });
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                          ->orWhere('mobile_number', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                    });
                }

                if ($from) $query->whereDate('created_at', '>=', $from);
                if ($to)   $query->whereDate('created_at', '<=', $to);
                if ($city) $query->where('current_city_id', $city);
                if ($zone) $query->where('zone_id', $zone);
                if ($type == 'active') $query->where('rider_status', 1);
                if ($type == 'inactive') $query->where('rider_status', 0);

                $totalRecords = $query->count();
                if ($length == -1) $length = $totalRecords;

                $agents = $query->orderBy('id', 'desc')
                                ->skip($start)
                                ->take($length)
                                ->get();

                $data = $agents->map(function($agent, $index) use ($start) {
                    $idEncode = encrypt($agent->id);
                    $profileImage = $agent->photo 
                        ? asset('EV/images/photos/' . $agent->photo) 
                        : asset('b2b/img/default_profile_img.png');

                    $action = '<div class="d-flex align-items-center gap-1">
                        <a href="'.route('admin.recovery_management.agent_view',$idEncode).'" title="View Agent Details"
                           class="d-flex align-items-center justify-content-center border-0"
                           style="background-color:#CAEDCE; color:#155724; border-radius:8px; width:35px; height:35px;">
                           <i class="bi bi-eye fs-5"></i>
                        </a>
                    </div>';

                    return [
                        '<input class="form-check-input sr_checkbox" type="checkbox" style="width:25px;height:25px;" value="'.$agent->id.'">',
                        '<img src="'.$profileImage.'" class="rounded-circle" style="width:40px; height:40px; object-fit:cover;">',
                        $agent->emp_id ?? '-',
                        // $agent->reg_application_id ?? '-',
                        $agent->first_name . ' ' . $agent->last_name ?? '-',
                        $agent->mobile_number ?? '-',
                        $agent->email ?? '-',
                        $agent->current_city->city_name ?? 'NA',
                        $agent->zone->name ?? 'NA',
                        $agent->opened_request_count ?? 0,
                        $agent->closed_request_count ?? 0,
                        '<div class="form-check form-switch">
                            <input class="form-check-input custom-switch" type="checkbox" data-id="'.$agent->id.'" '.(($agent->rider_status==1) ? 'checked' : '').' disabled>
                        </div>',
                        $action
                    ];
                });

                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalRecords,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                \Log::error('Agent List Error: '.$e->getMessage());
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        }
         $user = User::find(Auth::id());
        
            if(in_array($user->role, [1, 13])){
                    $cities = City::where('status', 1)->get();
                    $zones = '';
                    $agents =Deliveryman::where('work_type','in-house')->where('team_type',22)->where('delete_status', 0)->get();
                }else{
                    $cities = City::where('id',$user->city_id )->where('status', 1)->get();
                    $zones = Zones::where('city_id',$user->city_id )->where('status', 1)->get();
                    $agents =Deliveryman::where('current_city_id',$user->city_id )->where('work_type','in-house')->where('team_type',22)->where('delete_status', 0)->get();
                }
        return view('recoverymanager::recovery.agent_list', compact('cities','zones','agents','type'));
    }
    
    // public function getAgentComments($id)
    // {
    //     $logs = RecoveryComment::with('user')->where('req_id', $id)
    //                 ->orderBy('created_at', 'asc')
    //                 ->get();
    //     $manager = User::find(Auth::id());
    //     $html = view('recoverymanager::recovery.agent_comments', compact('logs','manager'))->render();
    
    //     return response()->json(['success' => true, 'html' => $html]);
    // }

    public function getAgentComments($id)
    {
        $logs = RecoveryComment::with('user')->where('req_id', $id)
                    ->orderBy('created_at', 'asc')
                    ->get();
        $roles = Role::All();
        $customers = CustomerMaster::All();
        $manager = User::find(Auth::id());
        $updates = RecoveryUpdatesMaster::where('status',1)->get();
        $html = view('recoverymanager::recovery.logs', compact('logs','manager','roles','customers','updates'))->render();
    
        return response()->json(['success' => true, 'html' => $html]);
    }
    
    
       public function addComment(Request $request)
{
    
    $request->validate([
        'req_id' => 'required|string|max:100',
        'comments' => 'required|string',
        // 'status' => 'nullable|string|max:100',
    ]);
    // print_r($request->all());exit;
    $user = User::find(Auth::id());
    $user_type = 'recovery-manager-dashboard';  
    
    $remark = RecoveryComment::create([
        'req_id' => $request->req_id,
        'user_type' => $user_type,
        'user_id' => $user->id,
        'comments' => $request->comments,
        'status' => $request->status ?? '',
    ]);
    
    // $statusColors = [
    //             'in_progress' => '#ffc107',
    //             'pickup_reached' => '#17a2b8',
    //             'recovered' => '#28a745',
    //             'not_recovered' => '#dc3545',
    //             'vehicle_handovered' => '#6f42c1'
    //         ];
    // $statusColor = $statusColors[$remark->status] ?? '';
    
    $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
    $shortComment = \Str::limit($remark->comments, 300);
    audit_log_after_commit([
        'module_id'         => 7,
        'short_description' => 'Recovery Comment Added',
        'long_description'  => "Comment added for Request ID {$remark->req_id}. Comment: \"{$shortComment}\". Status: " . ($remark->status ?: 'N/A'),
        'role'              => $roleName,
        'user_id'           => $user->id ?? null,
        'user_type'         => 'gdc_admin_dashboard',
        'dashboard_type'    => 'web',
        'page_name'         => 'b2b_recovery_request.addComment',
        'ip_address'        => request()->ip(),
        'user_device'       => request()->userAgent()
    ]);
    return response()->json([
        'success' => true,
        'message' =>"Comments added successfully",
        'comment' => [
            'comments' => $remark->comments,
            'created_at' => $remark->created_at->format('d M Y, h:i A'),
            // 'status' => $remark->status,
            // 'status_color' =>$statusColor
        ],
        
        
    ]);
}


     public function agentView(Request $request, $id)
        {
            $id = decrypt($id);
        
            $agent = Deliveryman::where('id', $id)->where('delete_status', 0)
                ->withCount([
                    'openedRequest as opened_request_count' ,
                    'closedRequest as closed_request_count'
                ])
                ->firstOrFail();
        
            return view('recoverymanager::recovery.agent_view', compact('agent'));
        }

     public function agentExport(Request $request)
    {
        
        $fields    = $request->input('fields', []);  
        $from_date = $request->input('from_date');
        $to_date   = $request->input('to_date');
        $zone = $request->input('zone_id')?? null;
        $city = $request->input('city_id')?? null;
        $status = $request->input('status')?? null;
        $selectedIds = $request->input('selected_ids', []);

    
        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to export.');
        }
        
         $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $formattedFields = array_map(function($f) {
                return ucwords(str_replace('_', ' ', $f));
            }, $fields);
            
            $fieldsText = implode(', ', $formattedFields ?: ['ALL']);
            $zoneName = $zone ? Zones::where('id', $zone)->value('name') : 'N/A';
            $cityName = $city ? City::where('id', $city)->value('city_name') : 'N/A';
            $statusText = ucwords($status)?? 'All';
            $longDesc = "Requested agent export. From: {$from_date}, To: {$to_date}, Fields: {$fieldsText}, Selected IDs: " . (empty($selectedIds) ? 'ALL' : implode(',', $selectedIds)) . ", City: " . ($cityName ?? 'N/A') . ", Zone: " . ($zoneName ?? 'N/A') . ", Status: " . ($statusText ?? 'N/A');
        
            audit_log_after_commit([
                'module_id'         => 7,
                'short_description' => 'B2B Recovery Agent Exported',
                'long_description'  => $longDesc,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'b2b_recovery_agent.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        return Excel::download(
            new B2BRecoveryAgentExport($from_date, $to_date, $selectedIds, $fields,$city,$zone,$status),
            'recovery_agent_list-' . date('d-m-Y') . '.xlsx'
        );
    }
    
        public function sendDynamicEmailNotify(array $recipients, string $subject, string $body, bool $footer = false)
        {
            // Add footer content dynamically if needed
            if ($footer) {
                $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
                $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
                $body .= "<p style='margin-top:20px;'>{$footerContent}</p>";
            }
        
            foreach ($recipients as $recipient) {
                $to  = $recipient['to'] ?? null;
                $cc  = (array) ($recipient['cc'] ?? []);
                $bcc = (array) ($recipient['bcc'] ?? []);
        
                if (!empty($to)) {
                    // CustomHandler::updatedSendEmail($to, $subject, $body, $cc, $bcc);
                    SendEmailJob::dispatch($to, $subject, $body, $cc, $bcc);
                }
            }
        
            return true;
        }
    
    public function getVehicleStatusDataJson(Request $request,$imei = '100000000200001',$roleId = 42)
{

    try {
        $imeiNumbers[] = $imei;
        $roleIds[] = $roleId;
        
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'FLEET_TRACKING_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // $deviceResponse = $this->getRoleBasedImeiData($request);
        // if (!isset($deviceResponse['status']) || $deviceResponse['status'] != 200) {
            
        //     return response()->json([
        //         'status' => $deviceResponse['status'] ?? 500,
        //         'message' => 'Failed to get IMEI data',
        //         'errors' => $deviceResponse['errors'] ?? null
        //     ]);
        // }

        // foreach ($deviceResponse['results'] as $result) {
        //     if (isset($result['data']['payload'])) {
        //         foreach ($result['data']['payload'] as $device) {
        //             // if (!empty($device['imei'])) $imeiNumbers[] = $device['imei'];
        //             if (!empty($device['roleId']) && !in_array($device['roleId'], $roleIds)) {
        //                 $roleIds[] = $device['roleId'];
        //             }
        //         }
        //     }
        // }
        
        $params = [
            'accountId' => $request->input('accountId', 11),
            'limit' => $request->input('limit',50),
            'offset' => $request->input('offset', 1),
            'startDate' => $request->input('startDate', strtotime('-1 day')),
            'endDate' => $request->input('endDate', time()),
            'status' => $request->input('status', '')
        ];
        // print_r($params);exit;
        $payload = [
            'operationName' => 'VehicleStatusAndSinceUpdated',
            'variables' => array_merge($params, [
                'roleIds' => $roleIds,
                'IMEINumbers' => $imeiNumbers
            ]),
            'query' => 'query VehicleStatusAndSinceUpdated(
                $accountId: Int!, 
                $roleIds: [Int!]!, 
                $status: String, 
                $limit: Int!, 
                $offset: Int!, 
                $startDate: Int!, 
                $endDate: Int!, 
                $IMEINumbers: [String]
            ) {
                vehicleStatusAndSinceUpdated(
                    accountId: $accountId
                    roleIds: $roleIds
                    IMEINumbers: $IMEINumbers
                    status: $status
                    limit: $limit
                    offset: $offset
                    startDate: $startDate
                    endDate: $endDate
                ) {
                    totalCount
                    count {
                        running
                        stopped
                        offline
                    }
                    nodes {
                        vehicleNumber
                        distanceTravelled
                        lastIgnition
                        lastSpeed
                        latitude
                        longitude
                        lastDbTime
                        lastContactedTime
                        gsmNetwork
                        gpsNetwork
                        battery
                        charging
                        vehicleType
                        deviceType
                        IMEINumber
                        vehicleStatus
                        vehicleSince
                        favourite
                        roleId
                        address
                        redDotFlag
                        deviceSubscriptionExpiryDate
                        deviceEnableStatus
                        roleName
                        prRoleName
                        driverName
                        userId
                        deviceId
                        displayNumber
                    }
                }
            }'
        ];

        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['FLEET_TRACKING_ENDPOINT'], '/');
       

        $response = Http::timeout(120)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN'],
                'Content-Type' => 'application/json'
            ])
            ->post($url, $payload);

    

        if ($response->failed()) {
            $logData['error_message'] = $response->body();
           
            return response()->json([
                'status' => $response->status(),
                'message' => 'GraphQL request failed',
                'errors' => $response->json() ?? $response->body(),
            ]);
        }

        $responseData = $response->json();
        $vehicleData = $responseData['data']['vehicleStatusAndSinceUpdated'] ?? [];
        return $vehicleData; // updated by logesh


    } catch (\Exception $e) {
       

        Log::error('Vehicle Status API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ]);
    }
}

public function getUserDevicesJson(Request $request)
{
    // Initialize log data with default values
    // $logData = [
    //     'user_id' => auth()->id(),
    //     'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
    //     'api_user_id' => null, // Will be populated from API response
    //     'api_endpoint' => null,
    //     'status_code' => null,
    //     'status_type' => null,
    // ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'GET_USER_LIST_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // Get pagination parameters from request
        $page = $request->input('page', 0);
        $pageSize = $request->input('pageSize', 10);
        
        $data = $this->authenticate($request);
        $userId = $data['user_id'];
        $logData['api_user_id'] = $userId;

        if (empty($userId)) {
            throw new \Exception("User ID is required");
        }

        // Build endpoint URL with parameters
        $endpoint = preg_replace([
            '/\{(\$)?userId\}/',
            '/\{(\$)?page\}/',
            '/\{(\$)?pageSize\}/'
        ], [
            $userId,
            $page,
            $pageSize
        ], $settings['GET_USER_LIST_ENDPOINT']);
        
        $vehicle_data = '';
        if($request->input('vehicle_number')){
            $vehicle_data = '&vehicleNumber=' . $request->input('vehicle_number');
        }
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/') . $vehicle_data;
        $logData['api_endpoint'] = $url;
        // print_r($url);exit;
        // Make API request
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN']
            ])
            ->get($url);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle non-successful responses
        if ($response->failed()) {
            $errorResponse = $response->json() ?? $response->body();
            $logData['error_message'] = 'API request failed: ' . ($errorResponse['message'] ?? $response->body());
            // MobitraApiLog::create($logData);

            return response()->json([
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ], $response->status());
        }

        $responseData = $response->json();
        // print_r($responseData);exit;
        // MobitraApiLog::create($logData);

        return response()->json([
            'status' => $response->status(),
            'page' => $page,
            'pageSize' => $pageSize,
            'data' => $responseData
        ]);

    } catch (\Illuminate\Http\Client\RequestException $e) {
        $logData['status_code'] = 503;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 503,
            'message' => 'Service unavailable',
            'error' => 'Could not connect to API service'
        ], 503);

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function authenticate(Request $request)
{
    // Initialize log data with default values
    // $logData = [
    //     'user_id' => auth()->id(),
    //     'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
    //     'api_user_id' => null, // Will be populated from API response
    //     'api_endpoint' => null,
    //     'status_code' => null,
    //     'status_type' => null,
    // ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'AUTHENTICATE_ENDPOINT', 'USER_NAME', 'PASSWORD'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // Build request URL
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['AUTHENTICATE_ENDPOINT'], '/');
        $logData['api_endpoint'] = $url;

        // Prepare request data
        $data = ($request->user_name && $request->password)
            ? ['username' => $request->user_name, 'password' => $request->password]
            : ['username' => $settings['USER_NAME'], 'password' => $settings['PASSWORD']];

        // Make API request
        $response = Http::timeout(30)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($url, $data);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Process response
        $responseData = $response->json();
        
        if ($response->failed()) {
            Log::warning('API Authentication Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \Exception('API request failed: ' . ($responseData['message'] ?? $response->body()));
        }

        // Validate response structure
        if (!isset($responseData['token']) || !isset($responseData['userId'])) {
            throw new \Exception('Token or user_id missing in API response');
        }

        // Update log with API user ID from response
        $logData['api_user_id'] = $responseData['userId'];
        // MobitraApiLog::create($logData);

        return [
            'token' => $responseData['token'],
            'user_id' => $responseData['userId']
        ];

    } catch (\Exception $e) {
        // Ensure error log contains all available information
        $logData['status_code'] = $logData['status_code'] ?? 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage(); // Additional error info
        
        // Save error log
        // MobitraApiLog::create($logData);

        Log::error('API Connection Error', [
            'error' => $e->getMessage(),
            'url' => $logData['api_endpoint'] ?? null,
            'trace' => $e->getTraceAsString()
        ]);

        throw new \Exception('Authentication failed: ' . $e->getMessage());
    }
}
         
 
    
}
