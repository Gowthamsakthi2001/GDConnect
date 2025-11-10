<?php

namespace Modules\B2B\Http\Controllers\Api\V1\B2BAgent;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\City\Entities\City;
use Modules\Zones\Entities\Zones;
use Modules\B2B\Entities\B2BReturnRequest;
use Modules\B2B\Entities\B2BAgent;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BServiceRequest;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\B2B\Entities\B2BRider;//updated by Mugesh.B
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\B2B\Entities\B2BAgentsNotification;
use Carbon\Carbon;
use App\Helpers\CustomHandler;//updated by Gowtham.s
use App\Services\FirebaseNotificationService; //updated by Gowtham.s

use Modules\B2B\Entities\B2BRidersNotification;

class B2BAgentController extends Controller
{

    public function request_list(Request $request)
{
    try {
        $user = $request->user('agent');

        
        // Start from vehicle requests
        $query = B2BVehicleRequests::with([
            'rider.customerLogin.customer_relation'
        ]);
        
        if ($user->login_type == 1) { //updated by Gowtham.S
            // Only city 
            $query->where('city_id', $user->city_id);
        } elseif ($user->login_type == 2) {
            // city base zone
            $query->where('city_id', $user->city_id)->where('zone_id', $user->zone_id);
        }
    
        
        $status = strtolower($request->query('status', 'pending'));
        if ($status === 'open') {
            $query->where('status', 'pending');
        } elseif ($status === 'closed') {
            $query->where('status', 'completed');
        } else {
            $query->where('status', 'pending');
        }

        // Sorting
        $sort = strtolower($request->query('sort', 'newest'));
        $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

        // Fetch requests
        $requests = $query->get();

        // Format clean response
        $formatted = $requests->map(function ($req) {
            return [
                'request_details' => [
                    'id'         => $req->id,
                    'req_id'     => $req->req_id,
                    'status'     => $req->status,
                    'city_name' => $req->city->city_name ?? 'N/A',
                    'zone_name' => $req->zone->name ?? 'N/A',
                    'created_at' => $req->created_at
                        ? $req->created_at->format('d M Y h:i A')
                        : null,
                    'updated_at' => $req->updated_at
                        ? $req->updated_at->format('d M Y h:i A')
                        : null,
                    'completed_at' => $req->completed_at
                        ? $req->updated_at->format('d M Y h:i A')
                        : null,
                ],
                'rider' => $req->rider
                    ? [
                        'id'        => $req->rider->id,
                        'name'      => $req->rider->name,
                        'mobile_no' => $req->rider->mobile_no,
                        'email'     => $req->rider->email,
                    ]
                    : null,
                'customer' => $req->rider &&
                              $req->rider->customerLogin &&
                              $req->rider->customerLogin->customer_relation
                    ? [
                        'name'       => $req->rider->customerLogin->customer_relation->name,
                        'trade_name' => $req->rider->customerLogin->customer_relation->trade_name,
                    ]
                    : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Requests fetched successfully.',
            'data'    => $formatted,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Failed to fetch requests.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
    
//     public function request_view(Request $request, $id)
// {
//     try {
        
//         $user = $request->user('agent');
        
//         // Load vehicle request with rider + customer
//         $req = B2BVehicleRequests::with(['rider.customerLogin.customer_relation'])->where('req_id' ,$id)->first();
        
//         if ($user->login_type == 1) {
//             // Only city check
//             $query->whereHas('rider.customerLogin', function ($q) use ($user) {
//                 $q->where('city_id', $user->city_id);
//             });
//             } elseif ($user->login_type == 2) {
//                     // City + Zone check
//                     $query->whereHas('rider.customerLogin', function ($q) use ($user) {
//                         $q->where('city_id', $user->city_id)
//                           ->where('zone_id', $user->zone_id);
//                 });
//             }
    
//         if (!$req) {
//             return response()->json([
//                 'status'  => false,
//                 'message' => 'Request not found',
//                 'data'    => null,
//             ], 404);
//         }
//                 $imageUrl = function ($folder, $file) {
//                 return !empty($file) ? "/b2b/{$folder}/{$file}" : null;
//                 };
//         // Format into clean structure
//         $data = [
//             'request_details' => [
//                 'id'                    => $req->id,
//                 'req_id'                => $req->req_id,
                
//                 'start_date'            => $req->start_date,
//                 'end_date'              => $req->end_date,
//                 'vehicle_type'          => $req->vehicle_type,
//                 'battery_type'          => $req->battery_type,
//                 'status'                => $req->status,
//                 'qrcode_image'          => $req->qrcode_image
//                     ? asset("b2b/qrcodes/{$req->qrcode_image}")
//                     : null,
//                 'created_at'            => $req->created_at
//                     ? $req->created_at->format('d M Y h:i A')
//                     : null,
//                 'updated_at'            => $req->updated_at
//                     ? $req->updated_at->format('d M Y h:i A')
//                     : null,
               
//             ],
//             'rider' => $req->rider ? [
//                 'id'        => $req->rider->id,
//                 'name'      => $req->rider->name,
//                 'mobile_no' => $req->rider->mobile_no,
//                 'email'     => $req->rider->email,
//                 'dob'                   => $req->rider->dob,
//                 'adhar_front'           => $imageUrl('aadhar_images', $req->rider->adhar_front),
//                 'adhar_back'            => $imageUrl('aadhar_images', $req->rider->adhar_back),
//                 'adhar_number'          => $req->rider->adhar_number,
//                 'pan_front'             => $imageUrl('pan_images', $req->rider->pan_front),
//                 'pan_back'              => $imageUrl('pan_images', $req->rider->pan_back),
//                 'pan_number'            => $req->rider->pan_number,
//                 'driving_license_front' => $imageUrl('driving_license_images', $req->rider->driving_license_front),
//                 'driving_license_back'  => $imageUrl('driving_license_images', $req->rider->driving_license_back),
//                 'driving_license_number'=> $req->rider->driving_license_number,
//                 'llr_image'             => $imageUrl('llr_images', $req->rider->llr_image),
//                 'llr_number'            => $req->rider->llr_number,
//                 'terms_condition'       => $req->rider->terms_condition,
//                 'status'                => $req->rider->status,
//                 'adhar_verified'          => $req->rider->adhar_verified,
//                 'pan_verified'          => $req->rider->pan_verified,
//                 'dl_verified'          => $req->rider->dl_verified,
//                 'llr_verified'          => $req->rider->llr_verified,
//                 'created_by'            => $req->rider->created_by,
//                 'created_at'            => $req->rider->created_at->format('d M Y h:i A'),
//                 'updated_at'            => $req->rider->updated_at->format('d M Y h:i A'),
//             ] : null,
//             'customer' => $req->rider &&
//                           $req->rider->customerLogin &&
//                           $req->rider->customerLogin->customer_relation
//                 ? [
//                     'id'         => $req->rider->customerLogin->customer_relation->id,
//                     'name'       => $req->rider->customerLogin->customer_relation->name,
//                     'trade_name' => $req->rider->customerLogin->customer_relation->trade_name,
//                 ]
//                 : null,
//         ];

//         return response()->json([
//             'status'  => true,
//             'message' => 'Request details fetched successfully',
//             'data'    => $data,
//         ], 200);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status'  => false,
//             'message' => 'Something went wrong',
//             'error'   => $e->getMessage(),
//         ], 500);
//     }
// }

public function fcm_token_update(Request $request){
    $user = $request->user('agent');
    
    if(!$user){
        return response()->json(['status'=>false, 'message'=>'Agent not found'],404);
    }
    
    $validator = Validator::make($request->all(), [
        'fcm_token'     => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status"  => false,
            "message" => "Validation Error Occurred",
            "errors"  => $validator->errors()
        ], 422);
    }
    
    $user->mb_fcm_token = $request->fcm_token;
    $user->save();
    
    return response()->json(['status'=>true, 'message'=>'FCM Token has been updated'],200);
}
 
 public function request_view(Request $request, $id)
{
    try {
        $user = $request->user('agent');

        // Start building query
        $query = B2BVehicleRequests::with(['rider.customerLogin.customer_relation'])
            ->where('req_id', $id);

        if ($user->login_type == 1) { //updated by Gowtham.S
            // Only city 
            $query->where('city_id', $user->city_id);
        } elseif ($user->login_type == 2) {
            // city base zone
            $query->where('city_id', $user->city_id)->where('zone_id', $user->zone_id);
        }
        
        // Fetch the filtered request
        $req = $query->first();

        if (!$req) {
            return response()->json([
                'status'  => false,
                'message' => 'Request not found or not accessible',
                'data'    => [],
            ], 404);
        }

        // Helper for image URLs
        $imageUrl = function ($folder, $file) {
            return !empty($file) ? "/b2b/{$folder}/{$file}" : null;
        };

        // Format into clean structure
        $data = [
            'request_details' => [
                'id'           => $req->id,
                'req_id'       => $req->req_id,
                'start_date'   => $req->start_date,
                'end_date'     => $req->end_date,
                'vehicle_type' => $req->vehicle_type,
                'battery_type' => $req->battery_type,
                'status'       => $req->status,
                'qrcode_image' => $req->qrcode_image
                    ? asset("b2b/qrcodes/{$req->qrcode_image}")
                    : null,
                'created_at'   => $req->created_at
                    ? $req->created_at->format('d M Y h:i A')
                    : null,
                'updated_at'   => $req->updated_at
                    ? $req->updated_at->format('d M Y h:i A')
                    : null,
            ],
            'rider' => $req->rider ? [
                'id'        => $req->rider->id,
                'name'      => $req->rider->name,
                'mobile_no' => $req->rider->mobile_no,
                'email'     => $req->rider->email,
                'dob'                   => $req->rider->dob,
                'adhar_front'           => $imageUrl('aadhar_images', $req->rider->adhar_front),
                'adhar_back'            => $imageUrl('aadhar_images', $req->rider->adhar_back),
                'adhar_number'          => $req->rider->adhar_number,
                'pan_front'             => $imageUrl('pan_images', $req->rider->pan_front),
                'pan_back'              => $imageUrl('pan_images', $req->rider->pan_back),
                'pan_number'            => $req->rider->pan_number,
                'driving_license_front' => $imageUrl('driving_license_images', $req->rider->driving_license_front),
                'driving_license_back'  => $imageUrl('driving_license_images', $req->rider->driving_license_back),
                'driving_license_number'=> $req->rider->driving_license_number,
                'llr_image'             => $imageUrl('llr_images', $req->rider->llr_image),
                'llr_number'            => $req->rider->llr_number,
                'terms_condition'       => $req->rider->terms_condition,
                'status'                => $req->rider->status,
                'adhar_verified'        => $req->rider->adhar_verified,
                'pan_verified'          => $req->rider->pan_verified,
                'dl_verified'           => $req->rider->dl_verified,
                'llr_verified'          => $req->rider->llr_verified,
                'created_by'            => $req->rider->created_by,
                'city_name'             => $req->city->city_name ?? 'N/A', //updated by Gowtham.s
                'zone_name'             => $req->zone->name ?? 'N/A',
                'created_at'            => $req->rider->created_at->format('d M Y h:i A'),
                'updated_at'            => $req->rider->updated_at->format('d M Y h:i A'),
            ] : null,
            'customer' => $req->rider &&
                          $req->rider->customerLogin &&
                          $req->rider->customerLogin->customer_relation
                ? [
                    'id'         => $req->rider->customerLogin->customer_relation->id,
                    'name'       => $req->rider->customerLogin->customer_relation->name,
                    'trade_name' => $req->rider->customerLogin->customer_relation->trade_name,
                ]
                : null,
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Request details fetched successfully',
            'data'    => $data,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}
  
    
public function update_request(Request $request)
{
    try {
       
        $rider = B2BRider::find($request->rider_id);

        if (!$rider) {
            return response()->json([
                'status'  => false,
                'message' => 'Rider not found for the given ID: ' . $request->rider_id,
            ], 404);
        }
        
        

        // File upload helper
        $uploadFile = function ($file, $folder, $oldFile = null) {
            $directory = public_path('b2b/' . $folder);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            if (!empty($oldFile) && file_exists($directory . '/' . $oldFile)) {
                unlink($directory . '/' . $oldFile);
            }

            if ($file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                return $filename;
            }

            return $oldFile;
        };

        $fileFields = [
            'adhar_front'           => 'aadhar_images',
            'adhar_back'            => 'aadhar_images',
            'pan_front'             => 'pan_images',
            'pan_back'              => 'pan_images',
            'driving_license_front' => 'driving_license_images',
            'driving_license_back'  => 'driving_license_images',
            'llr_image'             => 'llr_images',
        ];

        // Update file fields only if provided
        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $rider->$field = $uploadFile($request->file($field), $folder, $rider->$field);
            }
        }

        // Update only provided non-file fields (using has() so only if actually present in request)
        foreach ($request->all() as $field => $value) {
            if ($field !== 'id' && $field !== 'rider_id' && $field !== 'start_date' && $field !== 'end_date' && !array_key_exists($field, $fileFields)) {
                if ($request->has($field)) {
                    $rider->$field = $value;
                }
            }
        }
        
        // Handle verified_by
        if ($request->has('verified')) {
            $user = $request->user('agent');
            $rider->verified_by = $user->id;
            $rider->verified = 1;
        }

        $rider->save();

        // Basic required fields (always needed)
        $baseRequired = [
            'name',
            'email',
            'mobile_no',
            'dob',
            'adhar_number',
            'adhar_front',
            'adhar_back',
            'pan_front',
            'pan_back',
            'pan_number',
        ];
        
        // Step 1: Check all base required fields
        $isComplete = true;
        foreach ($baseRequired as $field) {
            if (empty($rider->$field)) {
                $isComplete = false;
                break;
            }
        }
        
        // Step 2: If base fields are fine, check conditional groups
        if ($isComplete) {
            // Driving License + verification
            $hasDrivingLicense = !empty($rider->driving_license_front)
                && !empty($rider->driving_license_back)
                && !empty($rider->driving_license_number)
                && $rider->dl_verified == 1;
        
            // LLR + verification
            $hasLLR = !empty($rider->llr_image)
                && !empty($rider->llr_number)
                && $rider->llr_verified == 1;
        
            // Terms + verification
            $hasTerms = ($rider->terms_condition == 1);
        
            // Mandatory verification flags
            $allVerified = $rider->adhar_verified == 1
                && $rider->pan_verified == 1;
        
            // Step 3: At least one group (DL, LLR, Terms) must be satisfied
            if (!($hasDrivingLicense || $hasLLR || $hasTerms)) {
                $isComplete = false;
            }
        
            // Step 4: All verification flags must be valid
            if (!$allVerified) {
                $isComplete = false;
            }
        }
        // Prepare response with file paths
        $responseData = $rider->toArray();
        foreach ($fileFields as $field => $folder) {
            $responseData[$field] = !empty($rider->$field)
                ? "b2b/{$folder}/{$rider->$field}"
                : null;
        }

        return response()->json([
            'status'  => true,
            'message' => 'Rider request updated successfully.',
            'completion_status' => $isComplete ? 'complete' : 'incomplete',
            'data'    => $responseData,
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Update Request Error: ' . $e->getMessage());

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong while updating.',
            'completion_status' =>'incomplete',
            'error'   => $e->getMessage(),
        ], 500);
    }
}



    public function get_vehicle_list(Request $request)
    {
        $user = $request->user('agent');

        $query = AssetVehicleInventory::where('asset_vehicle_inventories.transfer_status', 3)
            ->join('ev_tbl_asset_master_vehicles as amv', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'amv.id')
            ->leftJoin('vehicle_qc_check_lists as qc', 'amv.qc_id', '=', 'qc.id')
            ->leftJoin('vehicle_types as vt', 'amv.vehicle_type', '=', 'vt.id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'amv.model', '=', 'vm.id')
            ->leftJoin('ev_tbl_brands as vb', 'vm.brand', '=', 'vb.id')
            ->leftJoin('ev_tbl_color_master as vc', 'amv.color', '=', 'vc.id')
            ->leftJoin('ev_tbl_financing_type_master as ftm', 'amv.financing_type', '=', 'ftm.id')
            ->leftJoin('ev_tbl_asset_ownership_master as aom', 'amv.asset_ownership', '=', 'aom.id')
            ->leftJoin('ev_tbl_registration_types as rt', 'amv.registration_type', '=', 'rt.id')
            ->leftJoin('ev_tbl_insurer_name_master as inm', 'amv.insurer_name', '=', 'inm.id')
            ->leftJoin('ev_tbl_insurance_types as it', 'amv.insurance_type', '=', 'it.id')
            ->leftJoin('ev_tbl_city as ct', 'qc.location', '=', 'ct.id')
            ->leftJoin('zones as zones', 'qc.zone_id', '=', 'zones.id')
            ->select(
                'amv.*',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vc.name as vehicle_color',
                'vb.brand_name as vehicle_brand',
                'ftm.name as financing_type_name',
                'aom.name as asset_ownership_name',
                'rt.name as registration_type_name',
                'inm.name as insurer_type_name',
                'it.name as insurance_type_name',
                'ct.city_name as city_name',
                'zones.name as zone_name',
                DB::raw("CASE 
                WHEN amv.battery_type = 1 THEN 'Self-Charging' 
                WHEN amv.battery_type = 2 THEN 'Portable' 
                ELSE 'Unknown' 
            END as battery_type_name")
            );
            
        // Apply login_type conditions
        if ($user->login_type == 1) {
            $query->where('qc.location', $user->city_id);
        } elseif ($user->login_type == 2) {
            $query->where('qc.location', $user->city_id)
                  ->where('qc.zone_id', $user->zone_id);
        }
    
        if ($request->filled('search')) {
            $s = mb_strtolower(trim($request->search), 'UTF-8');
    
            $query->where(function ($q) use ($s) {
                $q->whereRaw("LOWER(COALESCE(amv.permanent_reg_number, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(amv.chassis_number, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(amv.vehicle_category, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(amv.make, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(amv.variant, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(vb.brand_name, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(vm.vehicle_model, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(vt.name, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("LOWER(COALESCE(vc.name, '')) LIKE ?", ["%{$s}%"])
                  ->orWhereRaw("CAST(amv.id AS CHAR) LIKE ?", ["%{$s}%"]);
            });
        }
    
        $vehicles = $query->paginate(20)->appends($request->only('search', 'page'));
    
        return response()->json([
            'status'   => true,
            'message'  => "Vehicle List Fetched Successfully",
            'vehicles' => $vehicles
        ]);
    }

    
    public function assign_vehicle(Request $request)
    {
    
        try {
           
            $user = $request->user('agent');
            // Find Rider
            $rider = B2BRider::with('customerLogin')->find($request->rider_id);
            if (!$rider) {
                return response()->json([
                    'status'  => false,
                    'message' => "Rider not found for the given ID: " . $request->rider_id,
                ], 404);
            }
            
         
            // Define file upload helper
            $uploadFile = function ($file, $folder, $oldFile = null) {
                $directory = public_path('b2b/' . $folder);
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
    
                // Delete old file if exists
                if (!empty($oldFile) && file_exists($directory . '/' . $oldFile)) {
                    unlink($directory . '/' . $oldFile);
                }
    
                if ($file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($directory, $filename);
                    return $filename;
                }
    
                return $oldFile;
            };
    
            // Fields that accept files
            $fileFields = [
                // 'kilometer_image' => 'kilometer_images',
                'odometer_image'  => 'odometer_images',
                'vehicle_front'   => 'vehicle_front',
                'vehicle_back'    => 'vehicle_back',
                'vehicle_top'     => 'vehicle_top',
                'vehicle_bottom'  => 'vehicle_bottom',
                'vehicle_left'    => 'vehicle_left',
                'vehicle_right'   => 'vehicle_right',
                'vehicle_battery' => 'vehicle_battery',
                'vehicle_charger' => 'vehicle_charger',
            ];
            $assign_vehicle_check = B2BVehicleAssignment::where('req_id',$request->request_id)->where('rider_id',$request->rider_id)->first();
            if($assign_vehicle_check){
                 return response()->json([
                    'status'  => false,
                    'message' => 'Already Vehicle has been Assigned',
                ]);   
            }
            // Assuming you have an AssignVehicle model/table
            $assignVehicle = new B2BVehicleAssignment();
            $assignVehicle->rider_id   = $request->rider_id;
            $assignVehicle->req_id = $request->request_id;
            $assignVehicle->kilometer_value =  0;
            $assignVehicle->odometer_value  = $request->odometer_value ?? 0;
            $assignVehicle->handover_type  = 'vehicle';
            $assignVehicle->status  = 'running';
            $assignVehicle->assigned_agent_id  = $user->id;
            $assignVehicle->asset_vehicle_id  = $request->asset_vehicle_id ?? null;
    
            // Process file uploads
            foreach ($fileFields as $field => $folder) {
                if ($request->hasFile($field)) {
                    $assignVehicle->$field = $uploadFile($request->file($field), $folder, $assignVehicle->$field ?? null);
                }
            }
    
            $assignVehicle->save();
            
            $vehicle_number = AssetMasterVehicle::select('permanent_reg_number')->where('id',$request->asset_vehicle_id)->first();
            B2BVehicleAssignmentLog::create([
                'assignment_id' => $assignVehicle->id,
                'status'        => 'running',
                'remarks'       => "Vehicle {$vehicle_number->permanent_reg_number} assigned to rider {$rider->name} successfully",
                'action_by'     => $user->id ?? null,
                'type'          => 'agent',
            ]);
            
            
            AssetVehicleInventory::where('asset_vehicle_id', $request->asset_vehicle_id)
                        ->update(['transfer_status' => 1]);
                
            $vehicle = AssetMasterVehicle::where('id',$request->asset_vehicle_id)->first();
                
            $customer_name = $rider->customerLogin->customer_relation->trade_name;
                
            $customer_id = $rider->customerLogin->customer_relation->id;
            
            // $vehicle->update(['client' => $customer_id]);
            
            $vehicle->update([
            'client'                => $customer_id,
            'vehicle_delivery_date' => Carbon::now()->format('Y-m-d'), 
            ]);
        
        
            
            $remarks = "Vehicle has been successfully assigned to {$customer_name}; inventory status updated accordingly.";
            
            // Log this inventory action
            VehicleTransferChassisLog::create([
                    'chassis_number' => $vehicle->chassis_number,
                    'vehicle_id'     => $vehicle->id,
                    'status'         => 'updated',
                    'remarks'        => $remarks,
                    'created_by'     => $user->id,
                    'type'           => 'b2b-agent-app'
                ]);
                
                
            $vehicle_request = B2BVehicleRequests::where('req_id',$assignVehicle->req_id)->first();
            if ($vehicle_request) {
                    $vehicle_request->is_active = 1;
                    $vehicle_request->status = "completed";
                    $vehicle_request->closed_by = $user->id;
                    $vehicle_request->completed_at = now();
                    $vehicle_request->save();
                }
                
            $vehicle_no = $vehicle_number->permanent_reg_number; //updated by Gowtham
            $rider_name = $rider->name ?? 'Rider';
            $this->pushRiderVehicleStatusNotification($rider, $request->request_id,$vehicle_no,'rider_vehicle_assign_notify');
            $this->pushAgentVehicleStatusNotification($user,$request->request_id,$vehicle_no,'agent_vehicle_assign_notify',$rider_name);
            $this->AutoSendAssignVehicleEmail($user, $request->request_id, $request->rider_id, $request->asset_vehicle_id, 'agent_vehicle_assign_email_notify');
            $this->AutoSendAssignVehicleWhatsApp($user,$request->request_id,$request->rider_id,$request->asset_vehicle_id,'agent_vehicle_assign_notify');
                
            return response()->json([
                'status'  => true,
                'message' => 'Vehicle assigned successfully',
                'data'    => $assignVehicle
            ], 200);
    
        } catch (\Exception $e) {
            Log::info("catch error for Assign Vehicle".$e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Vehicle Assign Failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    public function AutoSendAssignVehicleEmail($agent, $vehicleRequestId, $rider_id, $vehicle_id, $forward_type)
    {
        
        $rider = B2BRider::with('customerLogin.customer_relation')
            ->where('id', $rider_id)
            ->first();

        if (!$rider || !$rider->mobile_no) {
            Log::info('Email Notify : Rider or mobile number not found');
            return false;
        }
    
        // Rider details
        $riderName    = $rider->name ?? 'Rider';
        $riderPhone   = $rider->mobile_no;
        $riderEmail   = $rider->email;
        $requestId    = $vehicleRequestId ?? '';
        $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
        $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
        $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
        $customerPhone = $rider->customerLogin->customer_relation->phone ?? 'N/A';
        $customerLoginEmail= $rider->customerLogin->email ?? 'N/A';
 
    
        $vehicleData = AssetMasterVehicle::with(['quality_check'])
            ->where('id', $vehicle_id)
            ->first();

        // Vehicle details
        $AssetvehicleId = $vehicleData->id ?? 'N/A';
        $vehicleNo      = $vehicleData->permanent_reg_number ?? 'N/A';
        $vehicleType    = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A';
        $vehicleModel   = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A';
    
        $cityData = City::select('city_name')->where('id', $agent->city_id)->first();
        $zoneData = Zones::select('name')->where('id', $agent->zone_id)->first();
    
        // Agent details
        $assignBy_agentName  = $agent->name;
        $assignBy_agentPhone = $agent->phone;
        $assignBy_agentEmail = $agent->email;
        $assignBy_agentCity  = $cityData->city_name ?? 'N/A';
        $assignBy_agentZone  = $zoneData->name ?? 'N/A';
    
        $footerText = \App\Models\BusinessSetting::where('key_name', 'email_footer')->value('value');
        $footerContentText = $footerText ?? "For any assistance, please reach out to Admin Support.<br>Email: support@greendrivemobility.com<br>Thank you,<br>GreenDriveConnect Team";
        
        $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.
                <br>Email: {$customerEmail}<br>Thank you,<br>{$customerName}";
    
        if ($forward_type == 'agent_vehicle_assign_email_notify') {
    
            // Rider email
            if (!empty($riderEmail)) {
                $subject = "Vehicle Assigned - Request #{$requestId}";
                $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin: 0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin: 0; font-size: 22px;'>Vehicle Assigned</h2>
                                </td>
                            </tr>
                    
                            <!-- Body -->
                            <tr>
                                <td style='padding: 25px 20px;'>
                                    <p style='font-size: 16px; margin-bottom: 15px;'>Hello <strong>{$riderName}</strong>,</p>
                                    <p style='font-size: 15px; margin-bottom: 20px;'>Your vehicle has been successfully assigned. Please find the details below:</p>
                    
                                    <!-- Vehicle Details Table -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th style='text-align: left; border-bottom: 1px solid #ddd;'>Request ID</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$requestId}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle ID</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; border-bottom: 1px solid #ddd;'>Vehicle No</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle Type</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left;'>Vehicle Model</th>
                                            <td>{$vehicleModel}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Footer -->
                                    <p style='margin-top: 20px; font-size: 14px; line-height: 1.5;'>
                                        {$CustomerfooterContentText}
                                    </p>
                                </td>
                            </tr>
                    
                            <!-- Footer Text -->
                            <tr>
                                <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                    &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

                CustomHandler::sendEmail([$riderEmail], $subject, $body);
            }
    
            // Customer email
            $toCustomerEmails = array_filter([$customerLoginEmail, $customerEmail]);
            if (!empty($toCustomerEmails)) {
                $subject = "Rider Vehicle Assigned - Request #{$requestId}";
               $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin:0; font-size: 22px;'>Vehicle Assigned</h2>
                                </td>
                            </tr>
                    
                            <!-- Body -->
                            <tr>
                                <td style='padding: 25px 20px;'>
                                    <p style='font-size:16px; margin-bottom:15px;'>Hello <strong>{$customerName}</strong>,</p>
                                    <p style='font-size:15px; margin-bottom:20px;'>A vehicle has been assigned for your rider. Please find the details below:</p>
                    
                                    <!-- Rider Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Rider Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderName}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$riderPhone}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderEmail}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Vehicle Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Vehicle ID</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle No</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Type</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9;'>Model</td>
                                            <td style='background-color: #f9f9f9;'>{$vehicleModel}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Agent Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Agent Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$assignBy_agentName}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$assignBy_agentPhone}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$assignBy_agentEmail}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9;'>Location</td>
                                            <td style='background-color: #f9f9f9;'>{$assignBy_agentZone}, {$assignBy_agentCity}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Footer -->
                                    <p style='margin-top: 20px; font-size:14px; line-height:1.5;'>{$footerContentText}</p>
                                </td>
                            </tr>
                    
                            <!-- Footer Text -->
                            <tr>
                                <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                    &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

                CustomHandler::sendEmail($toCustomerEmails, $subject, $body);
            }
     
            // Agent email
            $toAgentEmails = User::where('role', 17)
                ->where('city_id', $agent->city_id)
                ->where('zone_id', $agent->zone_id)
                ->where('status', 'Active')
                ->pluck('email')
                ->filter()
                ->toArray();
    
            if (!empty($toAgentEmails)) {
                $subject = "Confirmation - Vehicle Assigned (Request #{$requestId})";
               $body = "
                    <html>
                      <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
                          
                          <!-- Header -->
                          <tr>
                            <td style='background: linear-gradient(135deg, #4caf50, #2e7d32); padding: 25px; text-align: center; color: #ffffff;'>
                              <h2 style='margin: 0; font-size: 22px;'>Vehicle Assigned Notification</h2>
                            </td>
                          </tr>
                          
                          <!-- Content -->
                          <tr>
                            <td style='padding: 25px; line-height: 1.6;'>
                              <p style='font-size: 15px; margin: 0 0 15px;'>Hello <strong>{$assignBy_agentName}</strong>,</p>
                              <p style='font-size: 15px; margin: 0 0 20px;'>You have successfully assigned a vehicle. Please find the details below:</p>
                    
                              <!-- Rider Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                                </tr>
                                <tr><td width='40%'><strong>Name:</strong></td><td>{$riderName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                              </table>
                    
                              <!-- Vehicle Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'> Vehicle Details</td>
                                </tr>
                                <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                              </table>
                    
                              <!-- Customer Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                                </tr>
                                <tr><td><strong>Name:</strong></td><td>{$customerName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>ID:</strong></td><td>{$customerID}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                              </table>
                    
                              <!-- Assigned By -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Assigned By</td>
                                </tr>
                                <tr><td><strong>Name:</strong></td><td>{$assignBy_agentName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Email:</strong></td><td>{$assignBy_agentEmail}</td></tr>
                                <tr><td><strong>Location:</strong></td><td>{$assignBy_agentZone}, {$assignBy_agentCity}</td></tr>
                              </table>
                    
                              <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                            </td>
                          </tr>
                    
                          <!-- Footer -->
                          <tr>
                            <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                              &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                          </tr>
                    
                        </table>
                      </body>
                    </html>
                    ";

                CustomHandler::sendEmail($toAgentEmails, $subject, $body);
            }
           
            // Admin email
            $adminEmails = DB::table('roles')
                ->leftJoin('users', 'roles.id', '=', 'users.role')
                ->whereIn('users.role', [1, 13]) //Admins
                ->where('users.status','Active')
                ->pluck('users.email')
                ->filter()
                ->toArray();
    
            if (!empty($adminEmails)) {
                $subject = "Admin Notification - Vehicle Assigned (Request #{$requestId})";
         
                $body = "
                <html>
                  <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);'>
                
                      <!-- Header -->
                      <tr>
                        <td style='background: linear-gradient(135deg, #2e7d32, #4caf50); padding: 25px; text-align: center; color: #ffffff;'>
                          <h2 style='margin: 0; font-size: 22px;'>Vehicle Assigned Notification</h2>
                        </td>
                      </tr>
                
                      <!-- Body Content -->
                      <tr>
                        <td style='padding: 25px; line-height: 1.6; font-size: 15px;'>
                          <p style='margin: 0 0 15px;'>Dear Admin,</p>
                          <p style='margin: 0 0 20px;'>A new vehicle has been assigned. Please review the details below:</p>
                
                          <!-- Customer Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                            </tr>
                            <tr><td width='40%'><strong>Name:</strong></td><td>{$customerName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>ID:</strong></td><td>{$customerID}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                          </table>
                
                          <!-- Rider Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                            </tr>
                            <tr><td><strong>Name:</strong></td><td>{$riderName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                          </table>
                
                          <!-- Vehicle Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Vehicle Details</td>
                            </tr>
                            <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                          </table>
                
                          <!-- Assigned By -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Assigned By</td>
                            </tr>
                            <tr><td><strong>Name:</strong></td><td>{$assignBy_agentName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$assignBy_agentPhone}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$assignBy_agentEmail}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Location:</strong></td><td>{$assignBy_agentZone}, {$assignBy_agentCity}</td></tr>
                          </table>
                
                          <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                        </td>
                      </tr>
                
                      <!-- Footer -->
                      <tr>
                        <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                          &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                      </tr>
                
                    </table>
                  </body>
                </html>
                ";

                CustomHandler::sendEmail($adminEmails, $subject, $body);
            }
            
        }
        
        
        if( $forward_type == 'agent_vehicle_return_email_notify'){
            // Rider email
            if (!empty($riderEmail)) {
                $subject = "Vehicle Returned - Request #{$requestId}";
                $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin: 0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin: 0; font-size: 22px;'>Vehicle Returned</h2>
                                </td>
                            </tr>
                    
                            <!-- Body -->
                            <tr>
                                <td style='padding: 25px 20px;'>
                                    <p style='font-size: 16px; margin-bottom: 15px;'>Hello <strong>{$riderName}</strong>,</p>
                                    <p style='font-size: 15px; margin-bottom: 20px;'>Your vehicle has been successfully returned. Please find the details below:</p>
                    
                                    <!-- Vehicle Details Table -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th style='text-align: left; border-bottom: 1px solid #ddd;'>Request ID</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$requestId}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle ID</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; border-bottom: 1px solid #ddd;'>Vehicle No</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle Type</th>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                        </tr>
                                        <tr>
                                            <th style='text-align: left;'>Vehicle Model</th>
                                            <td>{$vehicleModel}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Footer -->
                                    <p style='margin-top: 20px; font-size: 14px; line-height: 1.5;'>
                                        {$CustomerfooterContentText}
                                    </p>
                                </td>
                            </tr>
                    
                            <!-- Footer Text -->
                            <tr>
                                <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                    &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

                CustomHandler::sendEmail([$riderEmail], $subject, $body);
            }
           
            // Customer email
            $toCustomerEmails = array_filter([$customerLoginEmail, $customerEmail]);
            if (!empty($toCustomerEmails)) {
                $subject = "Rider Vehicle Returned - Request #{$requestId}";
               $body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px; margin:0; color: #544e54;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden;'>
                            
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #4a90e2; color: #ffffff; text-align: center; padding: 25px 20px;'>
                                    <h2 style='margin:0; font-size: 22px;'>Vehicle Returned</h2>
                                </td>
                            </tr>
                    
                            <!-- Body -->
                            <tr>
                                <td style='padding: 25px 20px;'>
                                    <p style='font-size:16px; margin-bottom:15px;'>Hello <strong>{$customerName}</strong>,</p>
                                    <p style='font-size:15px; margin-bottom:20px;'>A vehicle has been returned for your rider. Please find the details below:</p>
                    
                                    <!-- Rider Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Rider Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderName}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$riderPhone}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$riderEmail}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Vehicle Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Vehicle Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Vehicle ID</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$AssetvehicleId}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Vehicle No</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$vehicleNo}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Type</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$vehicleType}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9;'>Model</td>
                                            <td style='background-color: #f9f9f9;'>{$vehicleModel}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Agent Details -->
                                    <table cellpadding='10' cellspacing='0' style='width: 100%; margin-top:20px; border-collapse: collapse; border: 1px solid #ddd; border-radius: 5px;'>
                                        <tr style='background-color: #f2f2f2;'>
                                            <th colspan='2' style='text-align:center; font-size:16px; padding:10px;'>Agent Details</th>
                                        </tr>
                                        <tr>
                                            <td style='width: 40%; font-weight:bold; border-bottom: 1px solid #ddd;'>Name</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$assignBy_agentName}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>Phone</td>
                                            <td style='background-color: #f9f9f9; border-bottom: 1px solid #ddd;'>{$assignBy_agentPhone}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; border-bottom: 1px solid #ddd;'>Email</td>
                                            <td style='border-bottom: 1px solid #ddd;'>{$assignBy_agentEmail}</td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight:bold; background-color: #f9f9f9;'>Location</td>
                                            <td style='background-color: #f9f9f9;'>{$assignBy_agentZone}, {$assignBy_agentCity}</td>
                                        </tr>
                                    </table>
                    
                                    <!-- Footer -->
                                    <p style='margin-top: 20px; font-size:14px; line-height:1.5;'>{$footerContentText}</p>
                                </td>
                            </tr>
                    
                            <!-- Footer Text -->
                            <tr>
                                <td style='background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 12px; color: #777;'>
                                    &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    ";

                CustomHandler::sendEmail($toCustomerEmails, $subject, $body);
            }
            
            // Agent email
            $toAgentEmails = User::where('role', 17)
                ->where('city_id', $agent->city_id)
                ->where('zone_id', $agent->zone_id)
                ->where('status', 'Active')
                ->pluck('email')
                ->filter()
                ->toArray();
    
            if (!empty($toAgentEmails)) {
                $subject = "Confirmation - Vehicle Returned (Request #{$requestId})";
               $body = "
                    <html>
                      <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
                          
                          <!-- Header -->
                          <tr>
                            <td style='background: linear-gradient(135deg, #4caf50, #2e7d32); padding: 25px; text-align: center; color: #ffffff;'>
                              <h2 style='margin: 0; font-size: 22px;'>Vehicle Returned Notification</h2>
                            </td>
                          </tr>
                          
                          <!-- Content -->
                          <tr>
                            <td style='padding: 25px; line-height: 1.6;'>
                              <p style='font-size: 15px; margin: 0 0 15px;'>Hello <strong>{$assignBy_agentName}</strong>,</p>
                              <p style='font-size: 15px; margin: 0 0 20px;'>You have successfully returned a vehicle. Please find the details below:</p>
                    
                              <!-- Rider Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                                </tr>
                                <tr><td width='40%'><strong>Name:</strong></td><td>{$riderName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                              </table>
                    
                              <!-- Vehicle Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'> Vehicle Details</td>
                                </tr>
                                <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                                <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                              </table>
                    
                              <!-- Customer Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                                </tr>
                                <tr><td><strong>Name:</strong></td><td>{$customerName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>ID:</strong></td><td>{$customerID}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                              </table>
                    
                              <!-- Returned By Agent Details -->
                              <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                                <tr style='background-color: #f7f7f7;'>
                                  <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Agent Details</td>
                                </tr>
                                <tr><td><strong>Name:</strong></td><td>{$assignBy_agentName}</td></tr>
                                <tr style='background-color:#fafafa;'><td><strong>Email:</strong></td><td>{$assignBy_agentEmail}</td></tr>
                                <tr><td><strong>Location:</strong></td><td>{$assignBy_agentZone}, {$assignBy_agentCity}</td></tr>
                              </table>
                    
                              <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                            </td>
                          </tr>
                    
                          <!-- Footer -->
                          <tr>
                            <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                              &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                            </td>
                          </tr>
                    
                        </table>
                      </body>
                    </html>
                    ";

                CustomHandler::sendEmail($toAgentEmails, $subject, $body);
            }
      
            // Admin email
            $adminEmails = DB::table('roles')
                ->leftJoin('users', 'roles.id', '=', 'users.role')
                ->whereIn('users.role', [1, 13]) //Admins
                ->where('users.status','Active')
                ->pluck('users.email')
                ->filter()
                ->toArray();
    
            if (!empty($adminEmails)) {
                $subject = "Admin Notification - Vehicle Returned (Request #{$requestId})";
         
                $body = "
                <html>
                  <body style='font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px; color: #333;'>
                    <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 650px; margin: auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08);'>
                
                      <!-- Header -->
                      <tr>
                        <td style='background: linear-gradient(135deg, #2e7d32, #4caf50); padding: 25px; text-align: center; color: #ffffff;'>
                          <h2 style='margin: 0; font-size: 22px;'>Vehicle Returned Notification</h2>
                        </td>
                      </tr>
                
                      <!-- Body Content -->
                      <tr>
                        <td style='padding: 25px; line-height: 1.6; font-size: 15px;'>
                          <p style='margin: 0 0 15px;'>Dear Admin,</p>
                          <p style='margin: 0 0 20px;'>A new vehicle has been returned. Please review the details below:</p>
                
                          <!-- Customer Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Customer Details</td>
                            </tr>
                            <tr><td width='40%'><strong>Name:</strong></td><td>{$customerName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>ID:</strong></td><td>{$customerID}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$customerEmail}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$customerPhone}</td></tr>
                          </table>
                
                          <!-- Rider Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Rider Details</td>
                            </tr>
                            <tr><td><strong>Name:</strong></td><td>{$riderName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$riderPhone}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$riderEmail}</td></tr>
                          </table>
                
                          <!-- Vehicle Details -->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Vehicle Details</td>
                            </tr>
                            <tr><td><strong>Vehicle ID:</strong></td><td>{$AssetvehicleId}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Vehicle No:</strong></td><td>{$vehicleNo}</td></tr>
                            <tr><td><strong>Type:</strong></td><td>{$vehicleType}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Model:</strong></td><td>{$vehicleModel}</td></tr>
                          </table>
                
                          <!-- Returned By Agent Details-->
                          <table cellpadding='8' cellspacing='0' width='100%' style='border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;'>
                            <tr style='background-color: #f7f7f7;'>
                              <td colspan='2' style='text-align:center; font-weight:bold; font-size: 14px;'>Agent Details</td>
                            </tr>
                            <tr><td><strong>Name:</strong></td><td>{$assignBy_agentName}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Phone:</strong></td><td>{$assignBy_agentPhone}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>{$assignBy_agentEmail}</td></tr>
                            <tr style='background-color:#fafafa;'><td><strong>Location:</strong></td><td>{$assignBy_agentZone}, {$assignBy_agentCity}</td></tr>
                          </table>
                
                          <p style='margin-top: 20px; font-size: 14px; color: #555;'>{$footerContentText}</p>
                        </td>
                      </tr>
                
                      <!-- Footer -->
                      <tr>
                        <td style='background-color: #f7f7f7; text-align: center; padding: 15px; font-size: 12px; color: #666;'>
                          &copy; " . date('Y') . " GreenDriveConnect Team. All rights reserved.
                        </td>
                      </tr>
                
                    </table>
                  </body>
                </html>
                ";
                CustomHandler::sendEmail($adminEmails, $subject, $body);
            }
        }
        return false;
    }
    
    public function pushRiderVehicleStatusNotification($rider, $requestId, $vehicle_no, $forward_type)
    {
        $svc   = new FirebaseNotificationService();
        $image = null;
        $icon  = null;
        $riderId = $rider->id;
        $token   = $rider->fcm_token;
    
        try {
            if ($forward_type === 'rider_vehicle_assign_notify') {
                $title = 'Vehicle Assigned Successfully!';
                $body  = "Hello {$rider->name},\n\n"
                       . "A vehicle has been assigned to you.\n\n"
                       . "Request ID: {$requestId}\n"
                       . "Vehicle No: {$vehicle_no}";
            } elseif ($forward_type === 'rider_vehicle_retuned_push_notify') {
                $title = 'Vehicle Returned Successfully!';
                $body  = "Hello {$rider->name},\n\n"
                       . "A vehicle has been returned.\n\n"
                       . "Request ID: {$requestId}\n"
                       . "Vehicle No: {$vehicle_no}";
            } else {
                throw new \Exception("Invalid forward_type: {$forward_type}");
            }

            $data = [
                'request_id'   => $requestId,
                'vehicle_no'   => $vehicle_no,
                'forward_type' => $forward_type,
            ];
    

            // Store in DB
            $createModel = new B2BRidersNotification();
            $createModel->title       = $title;
            $createModel->description = $body;
            $createModel->image       = $image;
            $createModel->status      = 1;
            $createModel->rider_id    = $riderId;
            $createModel->save();
            
            // Send FCM
            if ($token) {
                $res = $svc->sendToToken($token, $title, $body, $data, $image, $icon, $riderId);

            }
    
           
            return true;
    
        } catch (\Exception $e) {
            \Log::error("pushRiderVehicleStatusNotification failed: " . $e->getMessage());
            return false;
        }
    }





    public function AutoSendAssignVehicleWhatsApp($agent,$vehicleRequestId,$rider_id,$vehicle_id,$forward_type)
    {

            $rider = B2BRider::with('customerLogin.customer_relation')
                ->where('id', $rider_id)
                ->first();
        
            if (!$rider || !$rider->mobile_no) {
                Log::info('QR File : Rider or mobile number not found');
                return false;
            }

            // WhatsApp API
            $api_key = BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
            $url = 'https://whatshub.in/api/whatsapp/send';
        
            $riderName    = $rider->name ?? 'Rider';
            $riderPhone   = $rider->mobile_no;
            $requestId    = $vehicleRequestId ?? '';
            $customerID   = $rider->customerLogin->customer_relation->id ?? 'N/A';
            $customerName = $rider->customerLogin->customer_relation->name ?? 'N/A';
            $customerEmail= $rider->customerLogin->customer_relation->email ?? 'N/A';
            $customerPhone= $rider->customerLogin->customer_relation->phone ?? '';
            $vehicleData = AssetMasterVehicle::with(['quality_check'])->where('id',$vehicle_id)->first();
            //vehicle details
            $AssetvehicleId    = $vehicleData->id; 
            $vehicleNo    = $vehicleData->permanent_reg_number ?? 'N/A'; 
            $vehicleType  = $vehicleData->quality_check->vehicle_type_relation->name ?? 'N/A'; 
            $vehicleModel  = $vehicleData->quality_check->vehicle_model_relation->vehicle_model ?? 'N/A'; 
            $cityData = City::select('city_name')->where('id',$agent->city_id)->first();
            $zoneData = Zones::select('name')->where('id',$agent->zone_id)->first();
            //agent details
            $assignBy_agentName    = $agent->name;
            $assignBy_agentPhone   = $agent->phone;
            $assignBy_agentCity = 'N/A';
            $assignBy_agentZone = 'N/A';
            if($cityData){
                $assignBy_agentCity = $cityData->city_name;
            }
            if($zoneData){
                $assignBy_agentZone = $zoneData->name;
            }
            //   dd($vehicle_id,$AssetvehicleId,$vehicleNo,$vehicleType,$vehicleModel,$riderName,$riderPhone,$requestId,$customerID,$customerName,$customerEmail,$customerPhone,$assignBy_agentName,$assignBy_agentPhone,$assignBy_agentCity,$assignBy_agentZone);

            $footerText = \App\Models\BusinessSetting::where('key_name', 'whatsapp_notify_footer')->value('value');
            $footerContentText = $footerText ??
                "For any assistance, please reach out to Admin Support.\nEmail: support@greendrivemobility.com\nThank you,\nGreenDriveConnect Team";
            
             $CustomerfooterContentText = "For any assistance, please reach out to Admin Support.\n" .
                 "Email: {$customerEmail}\n" .
                 "Thank you,\n" .
                 "{$customerName}";

            if($forward_type == 'agent_vehicle_assign_notify'){
                $rider_message = 
                    "Hello {$riderName},\n\n" .
                    "Your vehicle has been successfully assigned for your request.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "{$CustomerfooterContentText}";
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "A vehicle has been assigned for your rider.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                    "*Rider Information:*\n" .
                    "• Name: {$riderName}\n" .
                    "• Phone: {$riderPhone}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Assigned By:* {$assignBy_agentName}\n" .
                    "📍 *Assigned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                    "{$footerContentText}";

                $agent_message = 
                "Hello {$assignBy_agentName},\n\n" .
                "You have successfully assigned a vehicle.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Rider Information:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "📍 *Assigned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A new vehicle has been assigned for a rider request.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "*Rider Information:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Assigned By:* {$assignBy_agentName}\n" .
                "📍 *Assigned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                "{$footerContentText}";
            }
            
            if($forward_type == 'agent_vehicle_returned_whatsapp_notify'){
                $rider_message = 
                    "Hello {$riderName},\n\n" .
                    "Your vehicle has been successfully returned for your request.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "{$CustomerfooterContentText}";
                    
                $customer_message = 
                    "Hello {$customerName},\n\n" .
                    "A vehicle has been returned for your rider.\n\n" .
                    "📌 *Request Details:*\n" .
                    "• Request ID: {$requestId}\n\n" .
                    "*Rider Information:*\n" .
                    "• Name: {$riderName}\n" .
                    "• Phone: {$riderPhone}\n\n" .
                    "*Vehicle Information:*\n" .
                    "• Vehicle ID: {$AssetvehicleId}\n" .
                    "• Vehicle No: {$vehicleNo}\n" .
                    "• Vehicle Type: {$vehicleType}\n" .
                    "• Vehicle Model: {$vehicleModel}\n\n" .
                    "*Returned By:* {$assignBy_agentName}\n" .
                    "📍 *Returned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                    "{$footerContentText}";

                $agent_message = 
                "Hello {$assignBy_agentName},\n\n" .
                "You have successfully returned a vehicle.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Rider Information:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "📍 *Returned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                "{$footerContentText}";
                
                $admin_message = 
                "Dear Admin,\n\n" .
                "A new vehicle has been returned for a rider request.\n\n" .
                "📌 *Request Details:*\n" .
                "• Request ID: {$requestId}\n\n" .
                "*Customer Information:*\n" .
                "• Customer Name: {$customerName}\n" .
                "• Customer ID: {$customerID}\n\n" .
                "*Rider Information:*\n" .
                "• Name: {$riderName}\n" .
                "• Phone: {$riderPhone}\n\n" .
                "*Vehicle Information:*\n" .
                "• Vehicle ID: {$AssetvehicleId}\n" .
                "• Vehicle No: {$vehicleNo}\n" .
                "• Vehicle Type: {$vehicleType}\n" .
                "• Vehicle Model: {$vehicleModel}\n\n" .
                "*Returned By:* {$assignBy_agentName}\n" .
                "📍 *Returned Zone:* {$assignBy_agentZone}, {$assignBy_agentCity}\n\n" .
                "{$footerContentText}";
            }
            
            
            // Rider message
            if (!empty($riderPhone)) {
                CustomHandler::user_whatsapp_message($riderPhone, $rider_message);
            }
        

            // Customer message
            if (!empty($customerPhone)) {
                CustomHandler::user_whatsapp_message($customerPhone, $customer_message);
            }
            // Agent message
            if (!empty($assignBy_agentPhone)) {
                CustomHandler::user_whatsapp_message($assignBy_agentPhone, $agent_message);

            }
            
            $adminPhone = BusinessSetting::where('key_name', 'admin_whatsapp_no')->value('value');
            if (!empty($adminPhone)) {

                CustomHandler::admin_whatsapp_message($admin_message);
            }
           
    
        }
        
    // public function pushAgentVehicleStatusNotification($user, $requestId, $vehicle_no, $forward_type, $rider_name)
    // {
    //     $agent_Arr = User::where('role', 14)
    //         ->where('city_id', $user->city_id)
    //         ->where('zone_id', $user->zone_id)
    //         ->where('status', 'Active')
    //         ->get(['id', 'phone', 'mb_fcm_token', 'email', 'name']);
                    
    //     $svc = new FirebaseNotificationService();
    //     $title = 'Vehicle Assigned!';
    //     $image = null;
    //     $notifications = [];
        
    //      if ($forward_type == 'agent_vehicle_assign_notify') {
    //         $title = 'Vehicle Assigned Successfully!';
    //       $body = "Hello {$agent->name},\n"
    //                   . "A vehicle has been assigned to {$rider_name}.\n"
    //                   . "Request ID: {$requestId}\n"
    //                   . "Vehicle No: {$vehicle_no}";
    //     } elseif ($forward_type === 'rider_vehicle_retuned_push_notify') {
    //         $title = 'Vehicle Returned Successfully!';
    //       $body = "Hello {$agent->name},\n"
    //                   . "A vehicle has been returned to {$rider_name}.\n"
    //                   . "Request ID: {$requestId}\n"
    //                   . "Vehicle No: {$vehicle_no}";
    //     } else {
    //         throw new \Exception("Invalid forward_type: {$forward_type}");
    //     }

    //         foreach ($agent_Arr as $agent) {
    //             $agentId = $agent->id;
    //             $token   = $agent->mb_fcm_token;

    
    //             $data = [];
    //             $icon = null;
    
    //             if ($token) {
    //                 $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
    //             }
    
    //             $notifications[] = [
    //                 'title'       => $title,
    //                 'description' => $body,
    //                 'image'       => $image,
    //                 'status'      => 1,
    //                 'agent_id'    => $agentId,
    //                 'created_at'  => now(),
    //                 'updated_at'  => now(),
    //             ];
            
    //     }
    
    //     if (!empty($notifications)) {
    //         \DB::table('b2b_tbl_agent_notifications')->insert($notifications);
    //     }
    // }
    
    public function pushAgentVehicleStatusNotification($user, $requestId, $vehicle_no, $forward_type, $rider_name)
    {
        $agent_Arr = User::where('role', 17)
            ->where('city_id', $user->city_id)
            ->where('zone_id', $user->zone_id)
            ->where('status', 'Active')
            ->get(['id', 'phone', 'mb_fcm_token', 'email', 'name']);

        $svc = new FirebaseNotificationService();
        $image = null;
        $notifications = [];
    
        foreach ($agent_Arr as $agent) {
            $agentId = $agent->id;
            $token   = $agent->mb_fcm_token;
    
            if ($forward_type === 'agent_vehicle_assign_notify') {
                $title = 'Vehicle Assigned Successfully!';
                $body  = "Hello {$agent->name},\n"
                       . "A vehicle has been assigned to {$rider_name}.\n"
                       . "Request ID: {$requestId}\n"
                       . "Vehicle No: {$vehicle_no}";
            } elseif ($forward_type === 'agent_vehicle_return_push_notify') {
                $title = 'Vehicle Returned Successfully!';
                $body  = "Hello {$agent->name},\n"
                       . "A vehicle has been returned by {$rider_name}.\n"
                       . "Request ID: {$requestId}\n"
                       . "Vehicle No: {$vehicle_no}";
            } else {
                throw new \Exception("Invalid forward_type: {$forward_type}");
            }
    
            $data = [];
            $icon = null;
    
            if ($token) {
                $svc->sendToToken($token, $title, $body, $data, $image, $icon, $agentId);
            }
    
            $notifications[] = [
                'title'       => $title,
                'description' => $body,
                'image'       => $image,
                'status'      => 1,
                'agent_id'    => $agentId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
    
        if (!empty($notifications)) {
            \DB::table('b2b_tbl_agent_notifications')->insert($notifications);
        }
    }

    



    public function get_return_request_list(Request $request)
    {
        $user = $request->user('agent');
        
        $search = $request->input('search');
        $status = $request->input('filter', 'opened');
        $sort = $request->input('sort', 'newest');
    
        $query = B2BReturnRequest::with([
                'rider.customerLogin.customer_relation',
                'assignment'
        ])->where('status', $status);
        
        if ($user->login_type == 1) {
                // Only city check
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($user) {
                    $q->where('city_id', $user->city_id);
                });
        } elseif ($user->login_type == 2) {
                // City + Zone check
                $query->whereHas('assignment.VehicleRequest', function ($q) use ($user) {
                    $q->where('city_id', $user->city_id)
                      ->where('zone_id', $user->zone_id);
            });
        }   
      
        // if ($user->login_type == 1) {
        //         // Only city check
        //         $query->whereHas('rider.customerLogin', function ($q) use ($user) {
        //             $q->where('city_id', $user->city_id);
        //         });
        // } elseif ($user->login_type == 2) {
        //         // City + Zone check
        //         $query->whereHas('rider.customerLogin', function ($q) use ($user) {
        //             $q->where('city_id', $user->city_id)
        //               ->where('zone_id', $user->zone_id);
        //     });
        // }
        
            
            
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('return_reason', 'like', "%{$search}%")
                  ->orWhere('register_number', 'like', "%{$search}%")
                  ->orWhere('chassis_number', 'like', "%{$search}%")
                  ->orWhere('rider_name', 'like', "%{$search}%")
                  ->orWhere('rider_mobile_no', 'like', "%{$search}%")
                  ->orWhere('client_business_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc'); // default newest
        }
        $requests = $query->paginate(10);
    
        // Format created_at & add aging field
        $requests->getCollection()->transform(function ($item) {
            $item->rider->city_name = $item->rider->city->city_name ?? 'N/A';
            $item->rider->zone_name = $item->rider->zone->name ?? 'N/A';
            return $item;
        });
        
        
        return response()->json([
            'status'  => true,
            'message' => 'Return requests fetched successfully.',
            'data'    => $requests,
        ]);
    }

public function update_return_request(Request $request)
{
            
    \Log::info("Update Return Request".json_encode($request->all()));
    DB::beginTransaction();
    try {
        $validator = Validator::make($request->all(), [
            'id'              => 'required|exists:b2b_tbl_return_request,id',
            // 'kilometer_value' => 'required',//updated by Gowtham.s
            // 'kilometer_image' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'odometer_value'  => 'required',//updated by Gowtham.s
            'odometer_image'  => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_front'   => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_back'    => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_top'     => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_bottom'  => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_left'    => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_right'   => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_battery' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'vehicle_charger' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'agent_remarks' => 'nullable|max:255',
        ]);
        


        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation Error Occurred",
                "errors"  => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $user = $request->user('agent');
    
        // Define file upload helper
        $uploadFile = function ($file, $folder, $oldFile = null) {
            $directory = public_path('b2b/' . $folder);
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            // Delete old file if exists
            if (!empty($oldFile) && file_exists($directory . '/' . $oldFile)) {
                unlink($directory . '/' . $oldFile);
            }

            if ($file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                return $filename;
            }

            return $oldFile;
        };

        // Fields that accept files
        $fileFields = [
            // 'kilometer_image' => 'kilometer_images',
            'odometer_image'  => 'odometer_images',
            'vehicle_front'   => 'vehicle_front',
            'vehicle_back'    => 'vehicle_back',
            'vehicle_top'     => 'vehicle_top',
            'vehicle_bottom'  => 'vehicle_bottom',
            'vehicle_left'    => 'vehicle_left',
            'vehicle_right'   => 'vehicle_right',
            'vehicle_battery' => 'vehicle_battery',
            'vehicle_charger' => 'vehicle_charger',
        ];
  
        $returnRequest = B2BReturnRequest::findOrFail($validated['id']);
        // Update values
        if($returnRequest->status == 'closed'){
            return response()->json([
                'status'  => false,
                'message' => 'Vehicle has been already returned',
            ]);
        }
        $returnRequest->kilometer_value =  0;
        $returnRequest->odometer_value  = $request->kilometer_value ?? 0;
        $returnRequest->agent_remarks  = $request->remarks ?? '';
        $returnRequest->status          = 'closed'; 
        $returnRequest->closed_by       = $user->id??null;
        $returnRequest->closed_at       = now();

        // Process file uploads
        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                $returnRequest->$field = $uploadFile($request->file($field), $folder, $returnRequest->$field ?? null);
            }
        }
        
        

        $returnRequest->save();
        
        $rider_id = $returnRequest->rider_id;
        
        // Get all assignment IDs for this rider
        $assignIds = B2BVehicleAssignment::where('rider_id', $rider_id)->pluck('id');
        
        // Check if there is any active (not closed) service request for these assignments
        $service = B2BServiceRequest::whereIn('assign_id', $assignIds)
            ->where('status', '!=', 'closed')
            ->exists();
            
        
        $rider = B2BRider::with('customerLogin')->find($rider_id);
        
        $vehicle = AssetMasterVehicle::where('chassis_number', $returnRequest->chassis_number)->first();
        
        $vehicle->update(['client' => null , 'vehicle_delivery_date' => null]);
        
        $customer_name = $rider->customerLogin->customer_relation->trade_name;

        if ($service) {
        
            AssetVehicleInventory::where('asset_vehicle_id', $vehicle->id)
                ->update(['transfer_status' => 2]);
    
            $remarks = "Vehicle returned by {$customer_name} while under service. Inventory updated to 'Under Maintenance'.";
            
            VehicleTransferChassisLog::create([
                'chassis_number' => $vehicle->chassis_number,
                'vehicle_id'     => $vehicle->id,
                'status'         => 'updated',
                'remarks'        => $remarks,
                'created_by'     => $user->id ?? null,
                'type'           => 'b2b-agent-app'
            ]);
            
        } else {
            
            AssetVehicleInventory::where('asset_vehicle_id', $vehicle->id)
                ->update(['transfer_status' => 5]);
        
            $remarks = "Vehicle returned by {$customer_name}. Inventory updated to 'Returned - Pending QC'.";
            
            VehicleTransferChassisLog::create([
                'chassis_number' => $vehicle->chassis_number,
                'vehicle_id'     => $vehicle->id,
                'status'         => 'updated',
                'remarks'        => $remarks,
                'created_by'     => $user->id ?? null,
                'type'           => 'b2b-agent-app'
            ]);
            
        }

        $assignment = B2BVehicleAssignment::with('vehicle')->find($returnRequest->assign_id);
        if ($assignment) {
            
                $assignment->update(['status' => 'returned']);
                
                $vehicle_request = B2BVehicleRequests::where('req_id',$assignment->req_id)->where('is_active',1)->first();
                
                if ($vehicle_request) {
                        $vehicle_request->is_active = 0;
                        $vehicle_request->save();
                    }
                
            }
            
        // Add log
        B2BVehicleAssignmentLog::create([
            'assignment_id' => $returnRequest->assign_id,
            'status'        => 'closed',
            'remarks'       => "Vehicle {$returnRequest->register_number} returned & request closed successfully",
            'action_by'     => $user->id ?? null,
            'type'          => 'agent',
            'request_type'  => 'return_request',
            'request_type_id' => $returnRequest->id
        ]);
        
          DB::commit();
        
        $rider_name = $rider->name ?? 'Rider';
        $vehicle_no = $vehicle->permanent_reg_number; //updated by Gowtham

        $this->pushRiderVehicleStatusNotification($rider, $assignment->req_id,$vehicle_no,'rider_vehicle_retuned_push_notify');//rider push notify 
        $this->pushAgentVehicleStatusNotification($user,$assignment->req_id,$vehicle_no,'agent_vehicle_return_push_notify',$rider_name); //agent push notify 
        $this->AutoSendAssignVehicleWhatsApp($user,$assignment->req_id,$rider->id,$vehicle->id,'agent_vehicle_returned_whatsapp_notify'); // whatsapp 
        $this->AutoSendAssignVehicleEmail($user, $assignment->req_id, $returnRequest->rider_id, $vehicle->id, 'agent_vehicle_return_email_notify');//email 

        return response()->json([
            'status'  => true,
            'message' => 'Return request updated & closed successfully',
            'data'    => $returnRequest
        ], 200);

    } 
    catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Vehicle return process failed', [
            'error'   => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
            'request' => $request->all(),
            'user_id' => $request->user('agent')->id ?? null
        ]);

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

// public function get_dashboard_data(Request $request)
// {
//     $user = $request->user('agent');
    
//     $today = now();
//     $last30Days = $today->copy()->subDays(30);
//     $prev30Days = $last30Days->copy()->subDays(30);

//     // === Deployment Requests ===
//     $totalDeploymentCurrent = B2BVehicleRequests::whereBetween('created_at', [$last30Days, $today])->count();
//     $totalDeploymentPrev = B2BVehicleRequests::whereBetween('created_at', [$prev30Days, $last30Days])->count();

//     $openDeploymentCurrent = B2BVehicleRequests::where('status', 'pending')->whereBetween('created_at', [$last30Days, $today])->count();
//     $openDeploymentPrev = B2BVehicleRequests::where('status', 'pending')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

//     $closedDeploymentCurrent = B2BVehicleRequests::where('status', 'completed')->whereBetween('created_at', [$last30Days, $today])->count();
//     $closedDeploymentPrev = B2BVehicleRequests::where('status', 'completed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

//     // === Return Requests ===
//     $totalReturnCurrent = B2BReturnRequest::whereBetween('created_at', [$last30Days, $today])->count();
//     $totalReturnPrev = B2BReturnRequest::whereBetween('created_at', [$prev30Days, $last30Days])->count();

//     $openReturnCurrent = B2BReturnRequest::where('status', 'opened')->whereBetween('created_at', [$last30Days, $today])->count();
//     $openReturnPrev = B2BReturnRequest::where('status', 'opened')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

//     $closedReturnCurrent = B2BReturnRequest::where('status', 'closed')->whereBetween('created_at', [$last30Days, $today])->count();
//     $closedReturnPrev = B2BReturnRequest::where('status', 'closed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();
    

//     // === Prepare response ===
//     return response()->json([
//         'status' => true,
//         'message' => 'Dashboard data retrieved successfully',
//         'data' => [
//         'user_name'=>$user->name,
//         'deployment_requests' => [
//             'total' => [
//                 'current' => $totalDeploymentCurrent,
//                 'previous' => $totalDeploymentPrev,
//                 'change_percent' => $this->calculatePercentageChange($totalDeploymentPrev, $totalDeploymentCurrent),
//             ],
//             'open' => [
//                 'current' => $openDeploymentCurrent,
//                 'previous' => $openDeploymentPrev,
//                 'change_percent' => $this->calculatePercentageChange($openDeploymentPrev, $openDeploymentCurrent),
//             ],
//             'closed' => [
//                 'current' => $closedDeploymentCurrent,
//                 'previous' => $closedDeploymentPrev,
//                 'change_percent' => $this->calculatePercentageChange($closedDeploymentPrev, $closedDeploymentCurrent),
//             ],
//         ],
//         'return_requests' => [
//             'total' => [
//                 'current' => $totalReturnCurrent,
//                 'previous' => $totalReturnPrev,
//                 'change_percent' => $this->calculatePercentageChange($totalReturnPrev, $totalReturnCurrent),
//             ],
//             'open' => [
//                 'current' => $openReturnCurrent,
//                 'previous' => $openReturnPrev,
//                 'change_percent' => $this->calculatePercentageChange($openReturnPrev, $openReturnCurrent),
//             ],
//             'closed' => [
//                 'current' => $closedReturnCurrent,
//                 'previous' => $closedReturnPrev,
//                 'change_percent' => $this->calculatePercentageChange($closedReturnPrev, $closedReturnCurrent),
//             ],
//         ],
//         ]
//     ]);
// }

   public function get_notification_data(Request $request)
    {
        $user = $request->user('agent');;
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }
        
        $perPage = $request->query('per_page', 20);
    
        $notifications = B2BAgentsNotification::where('agent_id', $user->id)
            ->orderBy('read_status', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
       $notification_unread_count = B2BAgentsNotification::where('agent_id', $user->id)->where('read_status',0)->get()->count();
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
    
     public function notification_status_update(Request $request, $notification_id, $read_status)
    {
        $user = $request->user('agent');
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }
    
        $notification_data = B2BAgentsNotification::where('id', $notification_id)
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


public function get_dashboard_data(Request $request)
{
    $user = $request->user('agent'); 

    $cityData = City::where('id',$user->city_id)->first();
    $zoneData = Zones::where('id',$user->zone_id)->first();
    $cityName = 'N/A';
    $zoneName = 'N/A';
    if($cityData){
        $cityName = $cityData->city_name;
    }
    if($zoneData){
        $zoneName = $zoneData->name;
    }
    
    $today = now();
    $last30Days = $today->copy()->subDays(30);
    $prev30Days = $last30Days->copy()->subDays(30);

    // Helper function to apply city/zone filters
    $applyUserFilter = function ($query) use ($user) {

        if ($user->login_type == 1) { //updated by Gowtham.S
            // Only city 
            $query->where('city_id', $user->city_id);
        } elseif ($user->login_type == 2) {
            // city base zone
            $query->where('city_id', $user->city_id)->where('zone_id', $user->zone_id);
        }
        
        return $query;
    };
    
    // === Deployment Requests ===
    $deploymentQuery = $applyUserFilter(B2BVehicleRequests::query());
    
    $totalDeploymentCurrent = (clone $deploymentQuery)->whereBetween('created_at', [$last30Days, $today])->count();
    $totalDeploymentPrev = (clone $deploymentQuery)->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $openDeploymentCurrent = (clone $deploymentQuery)->where('status', 'pending')->whereBetween('created_at', [$last30Days, $today])->count();
    $openDeploymentPrev = (clone $deploymentQuery)->where('status', 'pending')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $closedDeploymentCurrent = (clone $deploymentQuery)->where('status', 'completed')->whereBetween('created_at', [$last30Days, $today])->count();
    $closedDeploymentPrev = (clone $deploymentQuery)->where('status', 'completed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();
    
    
    $applyUserFilterReturn = function ($query) use ($user) {
        
            if ($user->login_type == 1) {
                    // Only city check
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user) {
                        $q->where('city_id', $user->city_id);
                    });
            } elseif ($user->login_type == 2) {
                    // City + Zone check
                    $query->whereHas('assignment.VehicleRequest', function ($q) use ($user) {
                        $q->where('city_id', $user->city_id)
                          ->where('zone_id', $user->zone_id);
                });
            } 
     return $query;
    };

    // === Return Requests ===
    $returnQuery = $applyUserFilterReturn(B2BReturnRequest::query());

    $totalReturnCurrent = (clone $returnQuery)->whereBetween('created_at', [$last30Days, $today])->count();
    $totalReturnPrev = (clone $returnQuery)->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $openReturnCurrent = (clone $returnQuery)->where('status', 'opened')->whereBetween('created_at', [$last30Days, $today])->count();
    $openReturnPrev = (clone $returnQuery)->where('status', 'opened')->whereBetween('created_at', [$prev30Days, $last30Days])->count();

    $closedReturnCurrent = (clone $returnQuery)->where('status', 'closed')->whereBetween('created_at', [$last30Days, $today])->count();
    $closedReturnPrev = (clone $returnQuery)->where('status', 'closed')->whereBetween('created_at', [$prev30Days, $last30Days])->count();


    // === Prepare response ===
    return response()->json([
        'status' => true,
        'message' => 'Dashboard data retrieved successfully',
        'data' => [
            'user_name' => $user->name,
            'city_name' =>$cityName,
            'zone_name' =>$zoneName,
            'deployment_requests' => [
                'total' => [
                    'current' => $totalDeploymentCurrent,
                    'previous' => $totalDeploymentPrev,
                    'change_percent' => $this->calculatePercentageChange($totalDeploymentPrev, $totalDeploymentCurrent),
                ],
                'open' => [
                    'current' => $openDeploymentCurrent,
                    'previous' => $openDeploymentPrev,
                    'change_percent' => $this->calculatePercentageChange($openDeploymentPrev, $openDeploymentCurrent),
                ],
                'closed' => [
                    'current' => $closedDeploymentCurrent,
                    'previous' => $closedDeploymentPrev,
                    'change_percent' => $this->calculatePercentageChange($closedDeploymentPrev, $closedDeploymentCurrent),
                ],
            ],
            'return_requests' => [
                'total' => [
                    'current' => $totalReturnCurrent,
                    'previous' => $totalReturnPrev,
                    'change_percent' => $this->calculatePercentageChange($totalReturnPrev, $totalReturnCurrent),
                ],/////'/'
                'open' => [
                    'current' => $openReturnCurrent,
                    'previous' => $openReturnPrev,
                    'change_percent' => $this->calculatePercentageChange($openReturnPrev, $openReturnCurrent),
                ],
                'closed' => [
                    'current' => $closedReturnCurrent,
                    'previous' => $closedReturnPrev,
                    'change_percent' => $this->calculatePercentageChange($closedReturnPrev, $closedReturnCurrent),
                ],
            ],
        ]
    ]);
}

/**
 * Helper: Calculate percentage change
 */
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



}