<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Modules\B2B\Entities\B2BRecoveryRequest;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;
use Modules\B2B\Entities\B2BRecoveryAgentNotification;
use Illuminate\Support\Facades\Mail;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\RecoveryManager\Entities\RecoveryComment;
use App\Models\EvMobitraApiSetting;
use App\Models\MobitraApiLog;
use App\Models\User;
use App\Helpers\CustomHandler;
use Modules\Role\Entities\Role; //updated by logesh
use Modules\MasterManagement\Entities\CustomerMaster; 
use App\Jobs\SendEmailJob;
use Modules\MasterManagement\Entities\RecoveryUpdatesMaster;
use App\Helpers\RecoveryNotifyHandler;


class RecoveryAgentContoller extends Controller
{

public function getDashboardData(Request $request)
{
    $user_id = $request->user_id;
    
    $user = Deliveryman::with('current_city','zone')->where('id',$user_id)->first();
    if(!$user){
        return response()->json([
            'success'=>false,
            'message' => 'User not found'],404);
    }
    
    $today = now();
    $last30Days = $today->copy()->subDays(30);
    $prev30Days = $last30Days->copy()->subDays(30);

    // === Return Requests ===
    $recoveryQuery = B2BRecoveryRequest::query();

    $totalRecoveryCurrent = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->whereBetween('created_at', [$last30Days, $today])->count();
    $totalRecoveryPrev = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $openRecoveryCurrent = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->where('status','!=','closed')->whereBetween('created_at', [$last30Days, $today])->count();
    $openRecoveryPrev = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->where('status','!=','closed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $closedRecoveryCurrent = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->where('status', 'closed')->whereBetween('created_at', [$last30Days, $today])->count();
    $closeRecoveryPrev = (clone $recoveryQuery)->where('recovery_agent_id',$user_id)->where('status', 'closed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();


    // === Prepare response ===
    return response()->json([
        'status' => true,
        'message' => 'Dashboard data retrieved successfully',
        'data' => [
            'user_name' => $user->first_name . $user->last_name,
            'city_name' =>$user->current_city->city_name,
            'zone_name' =>$user->zone->name,
            'recovery_requests' => [
                'total' => [
                    'current' => $totalRecoveryCurrent,
                    'previous' => $totalRecoveryPrev,
                    'change_percent' => $this->calculatePercentageChange($totalRecoveryPrev, $totalRecoveryCurrent),
                ],
                'open' => [
                    'current' => $openRecoveryCurrent,
                    'previous' => $openRecoveryPrev,
                    'change_percent' => $this->calculatePercentageChange($openRecoveryPrev, $openRecoveryCurrent),
                ],
                'closed' => [
                    'current' => $closedRecoveryCurrent,
                    'previous' => $closeRecoveryPrev,
                    'change_percent' => $this->calculatePercentageChange($closeRecoveryPrev, $closedRecoveryCurrent),
                ],
            ],
        ]
    ]);
}

private function calculatePercentageChange($previous, $current)
{
    if ($previous == 0) {
        $result = $current > 0 ? 100 : 0;
    } else {
        $result = round((($current - $previous) / $previous) * 100, 2);
    }

    // Add + or - prefix
    return ($result > 0 ? '+' : '') . $result;
}

public function getRequestData(Request $request)
{
    $user_id = $request->user_id;

    $user = Deliveryman::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $search = $request->input('search');
    $status = $request->input('filter', '');
    $sort   = $request->input('sort', 'newest');

    // Base query with only required columns
    $query = B2BRecoveryRequest::select([
        'id',
        'assign_id',
        'reason',
        'vehicle_number',
        'chassis_number',
        'rider_id',
        'rider_name',
        'status',
        'agent_status',
        \DB::raw('ROW_NUMBER() OVER (ORDER BY created_at ASC) AS priority')
    ])
    ->with([
        'rider:id,name,mobile_no',
        'assignment:id,rider_id,asset_vehicle_id,req_id,vehicle_front,vehicle_right',
        'assignment.vehicle:id,model,make,variant,color,vehicle_type',
        'assignment.vehicle.vehicle_model_relation:id,vehicle_model,make,variant,color',
        'assignment.vehicle.vehicle_type_relation:id,name'
    ])->where('recovery_agent_id',$user_id);

    // Filter by status
    if (!empty($status)) {
        if($status == 'opened'){
            $query->where('status','!=', 'closed');
        }else{
            $query->where('status', $status);
        }
        
    }

    // Filter by zone/city from user
    $query->whereHas('assignment.vehicleRequest', function ($q) use ($user) {
        $q->where('zone_id', $user->zone_id)
          ->where('city_id', $user->current_city_id);
    });

    // Optional search filter
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('reason', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%")
              ->orWhere('chassis_number', 'like', "%{$search}%")
              ->orWhere('rider_name', 'like', "%{$search}%");
        });
    }

    // Sorting
    $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

    // Paginate results
    $requests = $query->paginate(10);
    // print_r($requests);exit;
    // Transform response
    $requests->getCollection()->transform(function ($item) {
        $vehicleImage = null;

        if (!empty($item->assignment->vehicle_front)) {
            $vehicleImage = 'b2b/vehicle_front/' . $item->assignment->vehicle_front;
        } elseif (!empty($item->assignment->vehicle_right)) {
            $vehicleImage = 'b2b/vehicle_right/' . $item->assignment->vehicle_right;
        }

        return [
            'id' => $item->id,
            'req_id'=>$item->assignment->req_id ?? '',
            'reason' => $item->reason,
            'vehicle_number' => $item->vehicle_number,
            'chassis_number' => $item->chassis_number,
            'rider_name' => $item->rider->name ?? 'N/A',
            'rider_mobile_no' => $item->rider->mobile_no ?? 'N/A',
            'vehicle_model' => $item->assignment->vehicle->vehicle_model_relation->vehicle_model ?? 'N/A',
            'vehicle_make' => $item->assignment->vehicle->vehicle_model_relation->make ?? 'N/A',
            'vehicle_variant' => $item->assignment->vehicle->vehicle_model_relation->variant ?? 'N/A',
            'vehicle_color' => $item->assignment->vehicle->vehicle_model_relation->color ?? 'N/A',
            'vehicle_type' => $item->assignment->vehicle->vehicle_type_relation->name ?? 'N/A' ,
            'status' => $item->status,
            'agent_status' => $item->agent_status,
            'vehicle_image' => $vehicleImage,
            'priority' => (int) $item->priority, 
        ];
    });

    return response()->json([
        'status'  => true,
        'message' => 'Recovery requests fetched successfully.',
        'data'    => $requests,
    ]);
}

public function getRecoveryData(Request $request)
{
    $user_id = $request->user_id;
    $req_id  = $request->req_id;

    $user = Deliveryman::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $requestData = B2BRecoveryRequest::with([
        'rider.city', 
        'rider.zone', 
        'assignment.vehicle.vehicle_model_relation'
    ])->find($req_id);

    if (!$requestData) {
        return response()->json([
            'success' => false,
            'message' => 'Recovery Request not found'
        ], 404);
    }

    // Rider info
    $rider = $requestData->rider;
    if ($rider) {
        $rider->city_name = $rider->city->city_name ?? 'N/A';
        $rider->zone_name = $rider->zone->name ?? 'N/A';
        
        // $rider->terms_condition_content = '';
        $rider->terms_condition_content = BusinessSetting::where('key_name', 'rider_terms_condition')->value('value') ?? '';  
        if($rider->terms_condition){
          $rider->terms_condition_content = BusinessSetting::where('key_name', 'rider_terms_condition')->value('value') ?? '';  
        }
        // Rider-related documents
        $riderDocs = [
            'adhar_front' => 'aadhar_images',
            'adhar_back'  => 'aadhar_images',
            'pan_front'   => 'pan_images',
            'pan_back'    => 'pan_images',
            'driving_license_front' => 'driving_license_images',
            'driving_license_back'  => 'driving_license_images',
            'llr_image'   => 'llr_images',
        ];

        foreach ($riderDocs as $field => $folder) {
            $rider->$field = !empty($rider->$field)
                ? asset("b2b/{$folder}/" . $rider->$field)
                : null;
        }
    }

    // Assignment-related info and images
    $assignment = $requestData->assignment;
    if ($assignment) {
        $imageFields = [
            'odometer_image'   => 'odometer_images',
            'vehicle_front'    => 'vehicle_front',
            'vehicle_back'     => 'vehicle_back',
            'vehicle_top'      => 'vehicle_top',
            'vehicle_bottom'   => 'vehicle_bottom',
            'vehicle_left'     => 'vehicle_left',
            'vehicle_right'    => 'vehicle_right',
            'vehicle_battery'  => 'vehicle_battery',
            'vehicle_charger'  => 'vehicle_charger',
        ];

        foreach ($imageFields as $field => $folder) {
            $assignment->$field = !empty($assignment->$field)
                ? asset("b2b/{$folder}/" . $assignment->$field)
                : null;
        }

        // Vehicle model details
        $vehicle = $assignment->vehicle;
        if ($vehicle && $vehicle->vehicle_model_relation) {
            $requestData->vehicle_model   = $vehicle->vehicle_model_relation->vehicle_model ?? 'N/A';
            $requestData->vehicle_make    = $vehicle->vehicle_model_relation->make ?? 'N/A';
            $requestData->vehicle_variant = $vehicle->vehicle_model_relation->variant ?? 'N/A';
            $requestData->vehicle_color   = $vehicle->vehicle_model_relation->color ?? 'N/A';
            $requestData->vehicle_type   = $vehicle->vehicle_type_relation->name ?? 'N/A';
        } else {
            $requestData->vehicle_model   = 'N/A';
            $requestData->vehicle_make    = 'N/A';
            $requestData->vehicle_variant = 'N/A';
            $requestData->vehicle_color   = 'N/A';
            $requestData->vehicle_type   = 'N/A';
        }
    }

    return response()->json([
        'status'  => true,
        'message' => 'Recovery request fetched successfully.',
        'data'    => $requestData,
    ]);
}

public function getStatusMaster(Request $request)
{
    $statusList = [
        ['key' => 'in_progress',     'label' => 'In Progress'],
        ['key' => 'rider_contacted','label' => 'Follow-up Call'],
        ['key' => 'reached_location','label' => 'Location Reached'],
        ['key' => 'revisit_location','label' => 'Location Revisit'],
        ['key' => 'recovered',       'label' => 'Recovered'],
        ['key' => 'not_recovered',   'label' => 'Not Recovered'],
        ['key' => 'hold',            'label' => 'Hold'],
        ['key' => 'closed',            'label' => 'Closed'],
    ];

    return response()->json([
        'status'  => true,
        'message' => 'Status master fetched successfully.',
        'data'    => [
            'status_master' => $statusList
        ],
    ]);
}

public function getCallAttemptMessages(Request $request)
{
    $messageList = RecoveryUpdatesMaster::where('status', 1)
    ->orderBy('id', 'asc')
    ->get(['id', 'label_name'])
    ->map(function ($item) {
        return [
            'key'   => $item->id,
            'label' => $item->label_name,
        ];
    })
    ->toArray();
    // $messageList = [
    //     ['key' => 1, 'label' => 'Rider didn’t answer the call'],
    //     ['key' => 2, 'label' => 'Rider’s phone unreachable / switched off'],
    //     ['key' => 3, 'label' => 'Rider disconnected the call'],
    //     ['key' => 4, 'label' => 'Rider agreed to return the vehicle'],
    //     ['key' => 5, 'label' => 'Rider agreed but asked for more time'],
    //     ['key' => 6, 'label' => 'Rider refused to return the vehicle'],
    //     ['key' => 7, 'label' => 'Rider not co-operative on call'],
    //     ['key' => 8, 'label' => 'Multiple calls made, no response'],
    //     ['key' => 9, 'label' => 'Rider promised to call back'],
    //     ['key' => 10, 'label' => 'Invalid / wrong number'],
    // ];

    return response()->json([
        'status'  => true,
        'message' => 'Call attempt messages fetched successfully.',
        'data'    => [
            'call_attempt_messages' => $messageList
        ],
    ]);
}

public function getLogData(Request $request)
{
    $user_id = $request->user_id;
    $req_id  = $request->req_id;

    $user = Deliveryman::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $recovery = B2BRecoveryRequest::find($req_id);
    if (!$recovery) {
        return response()->json([
            'success' => false,
            'message' => 'Recovery Request not found'
        ], 404);
    }
    $roles = Role::All();
    $customers = CustomerMaster::All();
    $logs = RecoveryComment::where('req_id', $req_id)
        ->orderBy('id', 'desc')
        ->paginate(10);
    
    $logs->getCollection()->transform(function ($log) use ($roles,$customers) {

        // Default values
        $profilePhoto = '';
        $name = '';
        $userRole='Unknown';
        
        if ($log->recovery_user) {
        // Handle various user types
            if ($log->user_type === 'recovery-manager-dashboard' || $log->user_type === 'b2b-admin-dashboard') {
                $name = $log->recovery_user->name ?? 'Unknown';
                $userRole = $roles->firstWhere('id', $log->recovery_user->role)->name ?? 'Unknown Role';
                
                $profilePhoto = $log->recovery_user->profile_photo_path ? 'uploads/users/' . $log->recovery_user->profile_photo_path :'';

            } 
            elseif ($log->user_type === 'recovery-agent') {
                $name = trim(($log->recovery_user->first_name ?? '') . ' ' . ($log->recovery_user->last_name ?? '')) ?: 'Unknown';
                $userRole = 'Agent';
                $profilePhoto = $log->recovery_user->photo ? 'EV/images/photos/' . $log->recovery_user->photo : '';
                 
            } 
            elseif (in_array($log->user_type, ['b2b-customer', 'b2b-web-dashboard'])) {
                $name = $customers->firstWhere('id', $log->recovery_user->customer_id)->trade_name ?? 'Customer';
                $userRole = 'Customer';
                $profilePhoto = '';
            }
        }
        return [
            'name'        => $name,
            'formatted_date'    => \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A'),
            'profile_photo_path' => $profilePhoto,
            'comments'     => $log->comments ?? '',
            'user_role' => $userRole,
        ];
    });

    return response()->json([
        'status'  => true,
        'message' => 'Logs fetched successfully.',
        'data'    => $logs,
    ]);
}


public function addComment(Request $request)
{
    Log::info('Incoming recovery request started:'.now());
    
    $max = 10 * 1024 * 1024; // 10MB
    $contentLength = (int) $request->header('Content-Length');

    if ($contentLength > $max) {
        return response()->json([
            'success' => false,
            'message' => 'Video cannot be more than 10MB'
        ], 413);
    }
    
    Log::info('Incoming recovery request after validation:'.now());
    
    $request->validate([
        'user_id'  => 'required|integer',
        'lat'  => 'required|numeric',
        'long' => 'required|numeric',
        'req_id'   => 'required|string|max:100',
        'comments' => 'required|string',
        'status'   => 'nullable|string|max:100',
        'faq_id'   =>'nullable|integer',
        'images'   => 'nullable|array',
        'images.*' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        'video'    => 'nullable|file|mimes:mp4,mov,avi,3gp|max:10240', // 10MB limit
    ]);
    
    Log::info('Incoming recovery request validation overed:'.now());
    
    DB::beginTransaction(); // Start Transaction

    try {
        
    $user = Deliveryman::find($request->user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $recovery = B2BRecoveryRequest::with('rider','assignment' , 'assignment.vehicle')->find($request->req_id);
    if (!$recovery) {
        return response()->json([
            'success' => false,
            'message' => 'Recovery Request not found'
        ], 404);
    }

    if ($recovery->status == 'closed') {
        return response()->json([
            'success' => false,
            'message' => 'Recovery Request already closed'
        ], 400);
    }
    if(!empty($request->status)){
      if ($recovery->agent_status == $request->status) {
        return response()->json([
            'success' => false,
            'message' => 'Status already updated'
        ], 400);
    } 
    }
    
    $admins = User::whereIn('role', [1,13])
            ->where('status', 'Active')
            ->pluck('email')
            ->toArray();
            
    $manager = '';
    $managerName ='';
    $managerEmail='';
    if($recovery->city_manager_id){
        $manager =  User::select('email','name')->where('id',$recovery->city_manager_id)
            ->first();
        $managerName = $manager->name ?? 'Manager';
        $managerEmail = $manager->email ?? '';
    }
    
    $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
    $footerContent = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
    $user_type = 'recovery-agent';

    // Prepare upload directory
    $uploadPath = public_path('b2b/recovery_comments');
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    // Process multiple images
    $imagePaths = [];
    if ($request->hasFile('images')) {
        Log::info('Images is uploaded');
        foreach ($request->file('images') as $image) {
            $filename = uniqid('img_') . '.' . $image->getClientOriginalExtension();
            Log::info('Images inside the foreach loop ',['filename' =>$filename]);
            $image->move($uploadPath, $filename);
            // $imagePaths[] = url('b2b/recovery_comments/' . $filename);
            $imagePaths[] = $filename;
        }
    }

    // Process video
    $videoPath = null;
    if ($request->hasFile('video')) {
        $video = $request->file('video');
        $filename = uniqid('vid_') . '.' . $video->getClientOriginalExtension();
        $video->move($uploadPath, $filename);
        $videoPath = $filename;
    }

    // Create assignment log
    if(!empty($request->status)){
        
        $name = ucwords($user->first_name . ' ' . $user->last_name);
        
        $remarks = [
            'in_progress' => "$name has initiated the recovery process.",
            'rider_contacted' => "$name has contacted the rider.",
            'reached_location' => "$name has reached the vehicle’s last known location.",
            'revisit_location' => "$name has revisited the recovery location for further inspection.",
            'recovered' => "Vehicle has been successfully recovered by $name.",
            'not_recovered' => "$name could not recover the vehicle after all attempts. Case closed as not recovered.",
            'hold' => "Recovery process handled by $name has been temporarily placed on hold pending further instructions.",
            'closed' => "Recovery request handled by $name has been closed after completing all necessary actions.",
        ];

      B2BVehicleAssignmentLog::create([
        'assignment_id'   => $recovery->assign_id,
        'status'          => $request->status ?? null,
        'remarks'         => $remarks[$request->status] ?? null,
        'action_by'       => $request->user_id ?? null,
        'type'            => $user_type ?? null,
        'request_type'    => 'recovery_request',
        'request_type_id' => $recovery->id,
        'location_lat'    => $request->lat ?? null,
        'location_lng'    => $request->long ?? null,
        'updates_id'         =>$faq_id ?? null
    ]); 
    }
    
    // Create recovery comment
    $remark = RecoveryComment::create([
        'req_id'    => $recovery->id,
        'status'    => $request->status ?? null,
        'comments'  => $request->comments ?? null,
        'user_id'   => $request->user_id ?? null,
        'user_type' => $user_type,
        'location_lat'    => $request->lat ?? null,
        'location_lng'    => $request->long ?? null,
        'updates_id'         =>$faq_id ?? null
    ]);
    
    if (!empty($request->status)) {
        $recovery->agent_status = $request->status;
    }
    if(!empty($request->status) && $request->status == 'closed'){
        $recovery->status = $request->status;
        $recovery->closed_by = $user->id ?? null;
        $recovery->closed_by_type = 'recovery-agent';
        $recovery->closed_at = now();
        if ($recovery->assignment) {
                $recovery->assignment->status = 'recovered';
                $recovery->assignment->save();
                }
    }
    if (!empty($imagePaths)) {
    $recovery->images = json_encode($imagePaths);
    }
    if (!empty($videoPath)) {
        $recovery->video = $videoPath;
    }
    if (!empty($request->faq_id)) {
        $recovery->faq_id = $request->faq_id;
    }
    
    $recovery->save();
 
        if (!empty($request->status) && $request->status == 'closed') {
        
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
        
                    $agentName = ucwords($user->first_name . ' ' . $user->last_name);
                    $remarks = "Vehicle {$vehicle->permanent_reg_number} recovered by {$agentName} and inventory updated from '{$from_status_name}' to 'Recovered - Pending QC'.";
        
                    VehicleTransferChassisLog::create([
                        'chassis_number' => $vehicle->chassis_number ?? null,
                        'vehicle_id' => $vehicle->id,
                        'from_location_source' => $fromStatus, // previous inventory status
                        'to_location_destination' => $toStatus,
                        'status' => 'updated',
                        'remarks' => $remarks,
                        'created_by' => $request->user_id ?? null,
                        'type' => 'gdm-rider-app'
                    ]);
                }
            }
        }

       
    
    $statusRecipients = [
            [
                'to'  => $manager->email ? [$manager->email] : '',
                'cc'  => [], 
                'bcc' => $admins,
            ]
        ];
        if($recovery->status == 'closed'){
            $customerEmail =$recovery->rider->customerLogin->customer_relation->email ?? '';
            
                $statusRecipients = [
                [
                    'to'  => $manager->email ? [$manager->email] : '',
                    'cc'  => [$customerEmail],
                    'bcc' => $admins
                ]
            ];
        $reasonList = [
            1 => 'Breakdown',
            2 => 'Battery Drain',
            3 => 'Accident',
            4 => 'Rider Unavailable',
            5 => 'Other',
        ];
        $reasonText = $reasonList[$recovery->reason ?? 0] ?? 'Unknown';
         $recoveryData = [
            'recovery_reason'=>$reasonText,
            'recovery_description'=>$recovery->description
            ];
        
            RecoveryNotifyHandler::AutoSendRecoveryRequestClosedWhatsApp($recovery->assignment->req_id, $recovery->rider->id, $recovery->assignment->asset_vehicle_id, $recoveryData, $recovery->created_by_type,$recovery);
        }
        // $statusRecipients = [
        //     [
        //         'to'  => 'logeshmudaliyar2802@gmail.com',
        //         'cc'  => ['mudaliyarlogesh@gmail.com'],
        //         'bcc' => ['gowtham@alabtechnology.com']
        //     ]
        // ];
    $status = $request->status; 
    $statusMessages = [
        'in_progress' => [
            'subject' => "Recovery Update – Recovery In Progress (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The recovery agent has started working on the recovery process."
        ],
        'rider_contacted' => [
            'subject' => "Recovery Update – Rider Contacted (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The agent has contacted the rider and is following up on the recovery."
        ],
        'reached_location' => [
            'subject' => "Recovery Update – Agent Reached Rider Location (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The agent has reached the rider’s location and is verifying recovery details."
        ],
        'revisit_location' => [
            'subject' => "Recovery Update – Revisiting Rider Location (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The agent is revisiting the rider’s location for further action."
        ],
        'recovered' => [
            'subject' => "Recovery Completed – Vehicle Recovered (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The vehicle has been successfully recovered and verified by the recovery team."
        ],
        'not_recovered' => [
            'subject' => "Recovery Update – Vehicle Not Recovered (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The recovery attempt was unsuccessful. The vehicle remains unrecovered."
        ],
        'hold' => [
            'subject' => "Recovery Update – Request On Hold (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The recovery request has been temporarily put on hold pending further action."
        ],
        'closed' => [
            'subject' => "Recovery Request Closed (Vehicle No: {$recovery->vehicle_number})",
            'body'    => "The recovery request has been closed and no further action is required."
        ],
    ];

    // ✅ Default fallback message if an unknown status is passed
    $message = $statusMessages[$status] ?? [
        'subject' => "Recovery Update – Status Changed (Vehicle No: {$recovery->vehicle_number})",
        'body'    => "The recovery status has been updated by the agent."
    ];

    // ✅ Build the email body (HTML template)
    $statusBody = '
    <html>
    <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
        <table width="100%" cellpadding="0" cellspacing="0" 
               style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
            <tr>
                <td style="padding:20px; text-align:center; background:#4CAF50; color:#fff;">
                    <h2>Vehicle Recovery Update</h2>
                </td>
            </tr>
            <tr>
                <td style="padding:20px;">
                    <p>Dear <strong>'.$managerName.'</strong>,</p>
                    <p>'.$message['body'].'</p>

                    <p><strong>Recovery Details</strong></p>
                    <table cellpadding="8" cellspacing="0" border="1" 
                           style="border-collapse: collapse; width: 100%; max-width: 600px;">
                        <tr style="background:#f2f2f2;">
                            <td><strong>Recovery ID</strong></td>
                            <td>'.($recovery->assignment->req_id ?? 'N/A').'</td>
                        </tr>
                        <tr>
                            <td><strong>Vehicle Number</strong></td>
                            <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
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
                            <td><strong>Current Status</strong></td>
                            <td>'.ucwords(str_replace('_', ' ', $status)).'</td>
                        </tr>
                    </table>

                    <p style="margin-top:20px;">This is an automated update regarding the ongoing recovery process.</p>
                    <p>'.$footerContent.'</p>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    // ✅ Send the email
    $this->sendDynamicEmailNotify($statusRecipients, $message['subject'], $statusBody, false);

    
        if(!empty($request->faq_id) && $request->faq_id == 4){
               
        $recipients = [
            [
                'to'  => $recovery->rider->customerLogin->customer_relation->email ?? '',
                'cc'  => $managerEmail ? [$managerEmail] : [], 
                'bcc' => $admins,
            ]
        ];
        
        $adminrecipients = [
            [
                'to'  => $admins,
                'cc'  =>[], 
                'bcc' => [],
            ]
        ];
        
        //   $adminrecipients = [
        //     [
        //         'to'  => 'logeshmudaliyar2802@gmail.com',
        //         'cc'  =>[], 
        //         'bcc' => [],
        //     ]
        // ];
        
        // $recipients = [
        //     [
        //         'to'  => 'logeshmudaliyar2802@gmail.com',
        //         'cc'  => ['mudaliyarlogesh@gmail.com'],
        //         'bcc' => array_merge(['pratheesh@alabtechnology.com','saran@alabtechnology.com'],['gowtham@alabtechnology.com'])
        //     ]
        // ];
        
        $recovery_id = encrypt($recovery->id);
        $faqBody = '
            <html>
            <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                    <tr>
                        <td style="padding:20px; text-align:center; background:#4CAF50; color:#fff;">
                            <h2>Vehicle Recovery Update</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;">
                            <p>Dear <strong>'.$recovery->client_name.'</strong>,</p>
                            <p>The rider has <strong>agreed to return your vehicle</strong>. Our recovery team is actively coordinating to ensure a smooth and timely return.</p>
                            
                            <p><strong>Recovery Details</strong></p>
                            <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 600px;">
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Recovery ID</strong></td>
                                    <td>'.($recovery->assignment->req_id ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Vehicle Number</strong></td>
                                    <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
                                </tr>
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Rider Name</strong></td>
                                    <td>'.($recovery->rider_name ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Rider Contact</strong></td>
                                    <td>'.($recovery->rider_mobile_no ?? 'N/A').'</td>
                                </tr>
                            </table>
            
                            <p style="margin-top:20px;">Once the vehicle has been received and verified, you may close this recovery request.</p>
            
                            <div style="text-align:center; margin:30px 0;">
                                <a href="'.url('customer/recovery-request?recovery_id='.$recovery_id).'" 
                                   style="background-color:#f44336; color:#fff; padding:12px 20px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block;">
                                   Close Request
                                </a>
                            </div>
            
                            <p style="color:#f44336; font-size:14px; text-align:center;">
                                *Note: Once you click “Close Request,” this recovery case will be marked as closed and cannot be reopened.*
                            </p>
                            <p>'.$footerContent.'</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
        
        $adminBody = '
            <html>
            <body style="font-family: Arial, sans-serif; background-color:#f7f7f7; padding:20px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:auto; background:#fff; border-radius:8px;">
                    <tr>
                        <td style="padding:20px; text-align:center; background:#4CAF50; color:#fff;">
                            <h2>Vehicle Recovery Update</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;">
                            <p>Dear <strong>'.$manager->name.'</strong>,</p>
                            <p>The rider has <strong>agreed to return the vehicle</strong>.</p>
                            
                            <p><strong>Recovery Details</strong></p>
                            <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 600px;">
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Recovery ID</strong></td>
                                    <td>'.($recovery->assignment->req_id ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Vehicle Number</strong></td>
                                    <td>'.($recovery->vehicle_number ?? 'N/A').'</td>
                                </tr>
                                <tr style="background:#f2f2f2;">
                                    <td><strong>Rider Name</strong></td>
                                    <td>'.($recovery->rider_name ?? 'N/A').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Rider Contact</strong></td>
                                    <td>'.($recovery->rider_mobile_no ?? 'N/A').'</td>
                                </tr>
                            </table>

                            <p>'.$footerContent.'</p>
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
    $faqSubject = "Vehicle Recovery Update – Rider Agreed to Return Vehicle (Vehicle No : {$recovery->vehicle_number})";
    
    $this->sendDynamicEmailNotify($recipients,$faqSubject,$faqBody,false);
    $this->sendDynamicEmailNotify($adminrecipients,$faqSubject,$adminBody,false);
    }
         
    DB::commit();
        
        Log::info('Incoming recovery request completed:'.now());
    
        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
        ]);
        
    } catch (\Throwable $e) {
        DB::rollBack(); 
        Log::error('Error adding recovery comment:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong: ' . $e->getMessage()
        ], 500);
    }
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

public function getTrackingData(Request $request)
{
    $user_id = $request->user_id;
    $req_id = $request->req_id;
    
    $user = Deliveryman::find($user_id);
    if(!$user){
        return response()->json([
            'success'=>false,
            'message' => 'User not found'],404);
    }

    $query = B2BRecoveryRequest::with([
            'assignment.vehicle'
    ])->find($req_id);
    
    // $imei = $query->assignment->vehicle->telematics_imei_number ?? '100000000200001';
    $imei = '100000000200001';
    $roleId = 42;
    $response = $this->getVehicleStatusDataJson($request,$imei,$roleId);
    return response()->json([
        'status'  => true,
        'message' => 'tracking data fetched successfully.',
        'data'    => $response['nodes'][0],
        'imei'    =>$imei
    ]);
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

public function getHistoryData(Request $request)
{
    $user_id = $request->user_id;
    $req_id = $request->req_id;
    
    $user = Deliveryman::find($user_id);
    if(!$user){
        return response()->json([
            'success'=>false,
            'message' => 'User not found'],404);
    }
    
    $response = [
        'day_1' => [
            'first' => [
                'lat' => 12.9716,
                'long' => 77.5946,
                'date_time' => '23-Oct-2025 09:15 AM',
                'address' => 'MG Road, Bengaluru, Karnataka',
            ],
            'last' => [
                'lat' => 12.9721,
                'long' => 77.5982,
                'date_time' => '23-Oct-2025 06:45 PM',
                'address' => 'Brigade Road, Bengaluru, Karnataka',
            ],
            'frequently_visited' => [
                [
                    'lat' => 12.9719,
                    'long' => 77.5964,
                    'date_time' => '23-Oct-2025 11:30 AM',
                    'address' => 'Church Street, Bengaluru, Karnataka',
                ],
                [
                    'lat' => 12.9725,
                    'long' => 77.5952,
                    'date_time' => '23-Oct-2025 03:00 PM',
                    'address' => 'Cubbon Park, Bengaluru, Karnataka',
                ],
            ],
        ],
        'day_2' => [
            'first' => [
                'lat' => 13.0352,
                'long' => 77.5895,
                'date_time' => '22-Oct-2025 08:50 AM',
                'address' => 'Hebbal, Bengaluru, Karnataka',
            ],
            'last' => [
                'lat' => 13.0378,
                'long' => 77.5843,
                'date_time' => '22-Oct-2025 07:10 PM',
                'address' => 'Manyata Tech Park, Bengaluru, Karnataka',
            ],
            'frequently_visited' => [
                [
                    'lat' => 13.0360,
                    'long' => 77.5865,
                    'date_time' => '22-Oct-2025 10:45 AM',
                    'address' => 'Nagawara Junction, Bengaluru, Karnataka',
                ],
                [
                    'lat' => 13.0372,
                    'long' => 77.5851,
                    'date_time' => '22-Oct-2025 02:15 PM',
                    'address' => 'Elements Mall, Bengaluru, Karnataka',
                ],
            ],
        ],
    ];

    return response()->json([
        'success' => true,
        'message' => 'History data fetched successfully',
        'data' => $response,
    ]);
}



   public function get_notification_data(Request $request)
    {
        
        $user_id = $request->user_id;
        
        $user = Deliveryman::find($user_id);
        if(!$user){
            return response()->json([
                'success'=>false,
                'message' => 'User not found'],404);
        }
    
        $perPage = $request->query('per_page', 20);
    
        $notifications = B2BRecoveryAgentNotification::where('agent_id', $user->id)
            ->orderBy('read_status', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
       $notification_unread_count = B2BRecoveryAgentNotification::where('agent_id', $user->id)->where('read_status',0)->get()->count();
        $notifications->getCollection()->transform(function ($data) {
            return [
                'id'          => $data->id,
                'title'       => $data->title,
                'description' => $data->description,
                'read_status' => $data->read_status,
                'created_at'  => $data->created_at ? $data->created_at->format('d-m-Y h:i:s A') : null,
                'updated_at'  => $data->updated_at ? $data->updated_at->format('d-m-Y h:i:s A') : null,
            ];
        });
    
    
        return response()->json([
            'status'  => true,
            'message' => 'Notification data fetched successfully',
            'notification_unread_count'=>$notification_unread_count,
            'data'    => $notifications->items(),   
            'paginate_info'    => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'per_page'     => $notifications->perPage(),
                'total'        => $notifications->total(),
            ]
        ]);
    }
    
         public function notification_status_update(Request $request)
    {
        
        $user_id = $request->user_id;
        
        $user = Deliveryman::find($user_id);
        if(!$user){
            return response()->json([
                'success'=>false,
                'message' => 'User not found'],404);
        }
    
        $notification_id = $request->notification_id;
        $read_status   = $request->status;
    
        $notification_data = B2BRecoveryAgentNotification::where('id', $notification_id)
            ->where('agent_id', $user->id)
            ->first();
    
        if (!$notification_data) {
            return response()->json([
                'status'  => false,
                'message' => 'Notification not found.',
            ], 404);
        }
    
        // Update read status
        $notification_data->read_status = $read_status;
        $notification_data->save();
    
        // Prepare response
        $notification = [
            'id'          => $notification_data->id,
            'title'       => $notification_data->title,
            'description' => $notification_data->description,
            'read_status' => intval($notification_data->read_status),
            'created_at'  => $notification_data->created_at 
                                ? $notification_data->created_at->format('d-m-Y h:i:s A') 
                                : null,
            'updated_at'  => $notification_data->updated_at 
                                ? $notification_data->updated_at->format('d-m-Y h:i:s A') 
                                : null,
        ];
    
        return response()->json([
            'status'  => true,
            'message' => 'Notification data updated successfully',
            'data'    => $notification,   
        ]);
    }
    

}