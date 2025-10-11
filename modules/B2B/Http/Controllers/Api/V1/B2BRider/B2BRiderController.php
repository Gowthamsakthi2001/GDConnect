<?php

namespace Modules\B2B\Http\Controllers\Api\V1\B2BRider;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Log;
use Modules\MasterManagement\Entities\CustomerLogin;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Illuminate\Support\Facades\DB; 
use Modules\B2B\Entities\B2BRider;
use Modules\B2B\Entities\B2BTicketCategory;
use Modules\B2B\Entities\B2BRiderTicket;
use Modules\B2B\Entities\B2BRiderTicketLog;
use App\Models\EvMobitraApiSetting;
use Modules\B2B\Entities\B2BVehicleAssignment;
use Modules\B2B\Entities\B2BVehicleRequests;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Modules\B2B\Entities\B2BRidersNotification;
use Modules\B2B\Entities\B2BServiceRequest;
use App\Models\MobitraApiLog;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Helpers\CustomHandler;
use App\Helpers\ServiceTicketHandler;


class B2BRiderController extends Controller
{
    public function rider_profile(Request $request)
{
    // $existingUser = B2BRider::where('mobile_no', "+919790860187")->first();
    $existingUser = $request->user('rider'); 
    if (!$existingUser) {
        return response()->json([
            "status"  => false,
            "message" => "Rider Not Found"
        ], 404);
    }

    $getFullUrl = function ($file, $folder) {
        return $file ? "b2b/{$folder}/" . $file : null;
    };

    $riderData = [
        "id"                     => $existingUser->id,
        "name"                   => $existingUser->name,
        "mobile_no"              => $existingUser->mobile_no,
        "email"                  => $existingUser->email,
        "dob"                    => $existingUser->dob,
        "adhar_number"           => $existingUser->adhar_number,
        "pan_number"             => $existingUser->pan_number,
        "driving_license_number" => $existingUser->driving_license_number,
        "llr_number"             => $existingUser->llr_number,
        "terms_condition"        => $existingUser->terms_condition,
        "status"                 => $existingUser->status,
        "created_by"             => $existingUser->created_by,
        "adhar_front"            => $getFullUrl($existingUser->adhar_front, "aadhar_images"),
        "adhar_back"             => $getFullUrl($existingUser->adhar_back, "aadhar_images"),
        "pan_front"              => $getFullUrl($existingUser->pan_front, "pan_images"),
        "pan_back"               => $getFullUrl($existingUser->pan_back, "pan_images"),
        "driving_license_front"  => $getFullUrl($existingUser->driving_license_front, "driving_license_images"),
        "driving_license_back"   => $getFullUrl($existingUser->driving_license_back, "driving_license_images"),
        "llr_image"              => $getFullUrl($existingUser->llr_image, "llr_images"),
        "profile_image"              => $getFullUrl($existingUser->profile_image, "profile_images"),
        "show_edit_option"       => true
    ];

    return response()->json([
        "status" => true,
        "message" => "Rider Profile Fetched Successfully",
        "data" => $riderData
    ], 200);
}

 public function update_rider_profile(Request $request)
{
    // ✅ Validation
    // $validator = Validator::make($request->all(), [
    //     'name'      => 'nullable|string',
    //     'email'     => 'nullable|email'
    // ]);

    // if ($validator->fails()) {
    //     return response()->json([
    //         "status"  => false,
    //         "errors"  => $validator->errors()
    //     ], 422);
    // }

    // $rider = B2BRider::where('mobile_no', "+919790860187")->first();
    $rider = $request->user('rider'); 
    if (!$rider) {
        return response()->json([
            "status"  => false,
            "message" => "Rider Not Found"
        ], 404);
    }
    if(isset($request->name) && !empty($request->name)){
        $rider->name       = $request->name ;  
    }
    
    if(isset($request->email) && !empty($request->email)){
        $rider->email      = $request->email;
    }
    
    $rider->save();

    return response()->json([
        "status"  => true,
        "message" => "Profile updated successfully",
        "data"    => [
            "id"         => $rider->id,
            "name"       => $rider->name,
            "mobile_no"  => $rider->mobile_no,
            "email"      => $rider->email,
        ]
    ], 200);
}



private function uploadFile($file, $folder)
{
    
    if ($file) {
        $directory = public_path('b2b/' . $folder);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);
        return $filename; // ✅ store only filename in DB
    }
    return null;
}

public function update_rider_kyc(Request $request)
{
    $validator = Validator::make($request->all(), [
            'aadhaar_back'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'aadhaar_front'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'pan_front'             => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'pan_back'              => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'driving_front' => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'driving_back'  => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
            'llr_image'             => 'nullable|mimes:jpg,jpeg,png,pdf|max:1024',
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status"  => false,
            "message" =>"Validation Error Occurred",
            "errors"  => $validator->errors()
        ], 422);
    }
    
    // $rider = B2BRider::where('mobile_no', "+919790860187")->first();
    $rider = $request->user('rider'); 

    if (!$rider) {
        return response()->json([
            "status" => false,
            "message" => "Rider Not Found"
        ], 404);
    }

    $rider->adhar_front = $request->hasFile('aadhaar_front') 
        ? $this->uploadFile($request->file('aadhaar_front'), 'aadhar_images') 
        : $rider->adhar_front;

    $rider->adhar_back = $request->hasFile('aadhaar_back') 
        ? $this->uploadFile($request->file('aadhaar_back'), 'aadhar_images') 
        : $rider->adhar_back;

    $rider->pan_front = $request->hasFile('pan_front') 
        ? $this->uploadFile($request->file('pan_front'), 'pan_images') 
        : $rider->pan_front;

    $rider->pan_back = $request->hasFile('pan_back') 
        ? $this->uploadFile($request->file('pan_back'), 'pan_images') 
        : $rider->pan_back;

    $rider->driving_license_front = $request->hasFile('driving_license_front') 
        ? $this->uploadFile($request->file('driving_license_front'), 'driving_license_images') 
        : $rider->driving_license_front;

    $rider->driving_license_back = $request->hasFile('driving_license_back') 
        ? $this->uploadFile($request->file('driving_license_back'), 'driving_license_images') 
        : $rider->driving_license_back;

    $rider->llr_image = $request->hasFile('llr_image') 
        ? $this->uploadFile($request->file('llr_image'), 'llr_images') 
        : $rider->llr_image;

    $rider->save();

    return response()->json([
        "status" => true,
        "message" => "KYC updated successfully",
        "data" => [
            "aadhaar_front" => $rider->adhar_front ? asset("b2b/aadhar_images/" . $rider->adhar_front) : null,
            "aadhaar_back"  => $rider->adhar_back ? asset("b2b/aadhar_images/" . $rider->adhar_back) : null,
            "pan_front"     => $rider->pan_front ? asset("b2b/pan_images/" . $rider->pan_front) : null,
            "pan_back"      => $rider->pan_back ? asset("b2b/pan_images/" . $rider->pan_back) : null,
            "driving_license_front" => $rider->driving_license_front ? asset("b2b/driving_license_images/" . $rider->driving_license_front) : null,
            "driving_license_back"  => $rider->driving_license_back ? asset("b2b/driving_license_images/" . $rider->driving_license_back) : null,
            "llr_image"     => $rider->llr_image ? asset("b2b/llr_images/" . $rider->llr_image) : null,
        ]
        
    ], 200);
}

public function update_profile_image(Request $request)
{
    $validator = Validator::make($request->all(), [
        'profile_image'          => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status"  => false,
            "message" =>"Validation Error Occurred",
            "errors"  => $validator->errors()
        ], 422);
    }
    
    // $rider = B2BRider::where('mobile_no', "+919790860187")->first();
    $rider = $request->user('rider'); 

    if (!$rider) {
        return response()->json([
            "status" => false,
            "message" => "Rider Not Found"
        ], 404);
    }

    $rider->profile_image = $request->hasFile('profile_image') 
        ? $this->uploadFile($request->file('profile_image'), 'profile_images') 
        : $rider->profile_image;
    $rider->save();
    return response()->json([
        "status" => true,
        "message" => "Profile Image updated successfully",
        "data" => [
            "profile_image" => $rider->profile_image ? asset("b2b/profile_images/" . $rider->profile_image) : null
        ]
    ], 200);
}



public function get_rider_qr(Request $request)
{
    // $rider = B2BRider::where('mobile_no', "+919790860187")->first();
    $rider = $request->user('rider'); 
 
    if (!$rider) {
        return response()->json([
            "status" => false,
            "message" => "Rider Not Found"
        ], 404);
    }
    
    return response()->json([
        "status" => true,
        "message" => "QR code fetched successfully",
        "data" => [
            "qrcode_image" => $rider->qrcode_image ? asset("b2b/qr/" . $rider->qrcode_image) : null
        ]
    ], 200);
}

public function get_categories($id = null)
{
    // If no id passed, return all main categories
    if (is_null($id)) {
        $categories = B2BTicketCategory::Where('parent_id', 0)->where('status',1)->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories
        ]);
    }

    // If id is passed, check if it's a main category
    $category = B2BTicketCategory::where('id', $id)->where('parent_id',0)->where('status',1)->first();

    if (!$category) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid category ID or not a main category.'
        ], 404);
    }

    // Fetch subcategories for this category
    $subcategories = B2BTicketCategory::where('parent_id', $id)->where('status',1)->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Subcategories fetched Successfully',
        'parent_category' => $category->name,
        'data' => $subcategories
    ]);
}


    public function store_ticket(Request $request)
    {
        // \Log::info("Ticket Create Api ".json_encode($request->all()));

        $user = $request->user('rider'); 
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);  
        }
    
        $validated = $request->validate([
            'category_id' => 'required|exists:b2b_tbl_ticket_categories,id',
            'description' => 'nullable|string',
            'address'     => 'nullable|string',
            'latitude'     => 'nullable|string',
            'longitude'     => 'nullable|string',
        ]);
    

        
             $ticket_status = B2BVehicleRequests::from('b2b_tbl_vehicle_requests as vr')
            ->leftJoin('b2b_tbl_vehicle_assignments as vs', 'vs.req_id', '=', 'vr.req_id')
            ->leftJoin('b2b_tbl_service_request as sr', 'sr.assign_id', '=', 'vs.id')
            ->select(
                'vr.req_id as tbl_vehicle_request_id',
                'vs.req_id as tbl_vehicle_assignment_id',
                'sr.assign_id as service_assign_id',
                'vr.rider_id',
                'vs.id as assign_id',
                'vr.status as vehicle_request_status',
                'vs.status as assignment_status',
                'sr.status as service_status'
            )
            ->where('vr.rider_id', $user->id)
            ->where('vr.status', 'completed')     
            ->whereIn('vs.status', ['under_maintenance','return_request','returned'])   
            //  ->where('vs.status','!=', 'return_request')   
            ->where(function ($q) {              
                $q->whereIn('sr.status', ['unassigned', 'inprogress'])
                  ->orWhereNull('sr.status');
            })
            ->orderBy('vr.id', 'desc')
            ->first();

        if ($ticket_status) {
            $message = 'You cannot create a new ticket because there is already a pending ticket.';
        
            if ($ticket_status->assignment_status === 'return_request') {
                $message = 'You have already raised a return request, so you cannot raise a new ticket.';
            } elseif ($ticket_status->assignment_status === 'returned') {
                $message = 'The vehicle has already been returned, so you cannot raise a new ticket.';
            } elseif ($ticket_status->service_status && $ticket_status->service_status != 'closed') {
                $message = 'You cannot create a new ticket because there is already a pending ticket.';
            }
        
            return response()->json([
                'status' => false,
                'message' => $message
            ], 409);
        }
        
        
        
        DB::beginTransaction();
        try {
            
            $rider = B2BRider::with('city' ,'zone')->where('id', $user->id)->first();
                        
            // Generate ticket ID
            $ticket_id = CustomHandler::GenerateTicketId($rider->city->id);

            if (empty($ticket_id)) {
                throw new \Exception('Ticket ID creation failed');
            }
    
            // Get active request

            $requestRow = B2BVehicleRequests::where('rider_id', $user->id)
                ->where('is_active', 1)
                ->first();
                

            
    
            if (!$requestRow) {
                throw new \Exception('No active vehicle request found for this rider');
            }
    
            // Get assignment
            $assign = B2BVehicleAssignment::with('vehicle')
                ->where('req_id', $requestRow->req_id)
                ->first();
    
            if (!$assign) {
                throw new \Exception('No vehicle assignment found for this request');
            }
    
            $category = B2BTicketCategory::find($validated['category_id']);
    
    
                // Get Customer
            $userid = B2BRider::where('id', $user->id)->value('created_by');
            $customer = CustomerLogin::with('customer_relation')->find($userid);
            
            
            // Create Service Request
            $service = B2BServiceRequest::create([
                'assign_id'      => $assign->id,
                'vehicle_number' => $assign->vehicle->permanent_reg_number ?? '',
                'ticket_id'      => $ticket_id,
                'created_by'     => $user->id,
                'type'           => 'rider',
                'gps_pin_address'   => $validated['address'] ?? '',
                'latitude'               => $validated['latitude'] ?? '',
                'longitude'              => $validated['longitude'] ?? '',
                'poc_name'          => $customer->customer_relation->trade_name ?? '',
                'poc_number'    => $customer->customer_relation->phone ?? '',
                'driver_name'   => $rider->name ?? '',
                'driver_number'   => $rider->mobile_no ?? '',
                'repair_type'    => $validated['category_id'] ?? '',
                'description'    => $validated['description'],
                'current_status'   => 'open',
                'status'          => 'unassigned',
                'city'           => $rider->city->id,
                'zone_id'           => $rider->zone->id,
            ]);
    
    
            $assign->update(['status' => 'under_maintenance']);
    
            // Update inventory status
            AssetVehicleInventory::where('asset_vehicle_id', $assign->asset_vehicle_id)
                ->update(['transfer_status' => 7]);
            
            // Remarks for inventory log only
            $remarks = "Inventory status updated to 'On Rent - Ticket Raised' due to rider service request.";
            
            // Log inventory action
            VehicleTransferChassisLog::create([
                'chassis_number' => $assign->vehicle->chassis_number,
                'vehicle_id'     => $assign->vehicle->id,
                'status'         => 'updated',
                'remarks'        => $remarks,
                'created_by'     => $user->id,
                'type'           => 'b2b-rider-app'
            ]);
            
            

            

    
            // Create assignment log
            B2BVehicleAssignmentLog::create([
                'assignment_id'   => $assign->id,
                'status'          => 'unassigned',
                'current_status' => 'open',
                'remarks'         => "Service request raised for vehicle {$assign->vehicle->permanent_reg_number}",
                'action_by'       => $user->id,
                'type'            => 'b2b-rider-app',
                'request_type'    => 'service_request',
                'request_type_id' => $service->id
            ]);
    
            // Create Vehicle Ticket
            $ticket = VehicleTicket::create([
                'ticket_id'        => $ticket_id,
                'vehicle_no'       => $assign->vehicle->permanent_reg_number,
                'city_id'          => $rider->city->id ?? '',
                'area_id'          => $rider->zone->id ?? '',
                'vehicle_type'     => $assign->vehicle->vehicle_type ?? '',
                'poc_name'         => $customer->customer_relation->trade_name ?? '',
                'poc_contact_no'   => $customer->customer_relation->phone ?? '',
                'driver_name'   => $rider->name ?? '',
                'driver_number'   => $rider->mobile_no ?? '',
                'issue_remarks'    => $validated['description'],
                'repair_type'      => $validated['category_id'] ?? null,
                'address'          => '',
                'gps_pin_address'  => $validated['address'] ?? '',
                'lat'              => $validated['latitude'] ?? '',
                'long'             => $validated['longitude'] ?? '',
                'image'            => '',
                'created_datetime' => now(),
                'created_by'       => '',
                'created_role'     => 'rider',
                'customer_id'      => $user->id,
                'web_portal_status'=> 0,
                'platform'         => 'b2b-rider-app',
                'ticket_status'    => 0,
            ]);
    
            $customerLongitude = $validated['longitude'] ?? '';
            $customerLatitude  = $validated['latitude'] ?? '';
            
            // Prepare data for FieldProxy API
            $createdDatetime = Carbon::now()->utc();
            $ticketData = [
                "vehicle_number"    => $assign->vehicle->permanent_reg_number ?? '',
                "updatedAt"         => $createdDatetime,
                "ticket_status"     => "unassigned",
                "chassis_number" => $assign->vehicle->chassis_number ?? null,
                "telematics" => $assign->vehicle->telematics_imei_number ?? null,
                "battery" => $assign->vehicle->battery_serial_no ?? null,
                "vehicle_type" => $assign->vehicle->vehicle_type_relation->name ?? null,
                "state"             => $rider->city->state->state_name ?? '',
                "priority"          => 'High',
                "point_of_contact_info" => $customer->customer_relation->trade_name .' - '.$customer->customer_relation->phone,
                "job_type"          => $category->name ?? null,
                "issue_description" => $validated['description'] ?? '',
                'image'             => [],
                "greendrive_ticketid"=> $ticket_id,
                "customer_number"   => $customer->customer_relation->phone ?? '',
                "customer_name"     => $customer->customer_relation->trade_name ?? '',
                'customer_email' =>  $customer->customer_relation->email ?? '',
                'driver_name'   => $rider->name ?? '',
                'driver_number'   => $rider->mobile_no ?? '',
                "current_status"    => 'open',
                'customer_location' => [
                    $customerLongitude,
                    $customerLatitude
                ], 
                "createdAt"         => $createdDatetime,
                "city"              => $rider->city->city_name ?? '',
            ];
            
            
            $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                'created_by' => $user->id,
                'type'       => 'b2b-rider-app',
            ]));
            
            
            FieldProxyLog::create([
                'fp_id'      => $fieldProxyTicket->id,  
                'status'     => 'unassigned',  // ticket status
                "current_status" => 'open',          
                'remarks'    => "Service request raised for vehicle {$assign->vehicle->permanent_reg_number}",
                'created_by' => $user->id,
                'type'       => 'b2b-rider-app',
            ]);

            $apiTicketData = $ticketData;
            $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
            $apiTicketData['customer_number'] = preg_replace('/^\+91/', '', $ticketData['customer_number']);


            $apiData = [
                "sheetId" => "tickets",
                "tableData" => $apiTicketData
            ];
    
            
            $apiUrl = 'https://webapi.fieldproxy.com/v3/zapier/sheetsRow';
            $apiKey = env('FIELDPROXY_API_KEY'); 
    
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: {$apiKey}",
                "Content-Type: application/json",
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
    
            if ($curlError) {
                Log::error('FieldProxy cURL error', ['ticket_id' => $ticket_id, 'error' => $curlError]);
            } elseif ($httpCode >= 400) {
                Log::error('FieldProxy HTTP error', ['ticket_id' => $ticket_id, 'http_code' => $httpCode, 'body' => $responseBody]);
            } else {
                $decoded = json_decode($responseBody, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('FieldProxy returned non-JSON', ['ticket_id' => $ticket_id, 'body' => $responseBody]);
                } else {
                    Log::info('FieldProxy success', ['ticket_id' => $ticket_id, 'response' => $decoded]);
                }
            }
            DB::commit();
            
            $customerName = $customer->customer_relation->name ?? 'Customer';  //updated by Gowtham.s
            $riderID = $rider->id;
            $riderData = $rider;
            $vehicleId = $assign->asset_vehicle_id;
            
            $issue_description = $request->description;
            $address = $request->address;
            $lat = $request->latitude;
            $long = $request->longitude;
            $repairInfo = [
              'issue_description'=>$issue_description,
              'address'=>$address,
              'latitude'=>$lat,
              'longitude'=>$long
            ];
            ServiceTicketHandler::pushRiderServiceTicketNotification($riderData, $ticket_id, $repairInfo,'create_by_rider', $customerName);//push notification 
            ServiceTicketHandler::AutoSendServiceRequestEmail($ticket_id,$riderID,$vehicleId,$repairInfo,'rider_create_ticket','create_by_rider'); //email 
            ServiceTicketHandler::AutoSendServiceRequestWhatsApp($ticket_id,$riderID,$vehicleId,$repairInfo,'rider_create_ticket','create_by_rider');//whatsapp
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Ticket created successfully.',
                'data'    => $ticket,
            ], 201);
    
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            Log::error('Ticket creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => false,
                'message' => 'Failed to create ticket: '.$e->getMessage(),
            ], 500);
        }
    }



    public function get_ticket_history(Request $request)
    {
        $user = $request->user('rider');
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Fetch tickets with pagination (10 per page)
        $tickets = B2BServiceRequest::where('created_by', $user->id)->where('type','rider')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
            

        // Transform data to include time difference
        $tickets->getCollection()->transform(function ($ticket) {
    $createdAt = Carbon::parse($ticket->created_at);
    $now = Carbon::now();

    $diffInHours = $createdAt->diffInHours($now);

    if ($diffInHours < 24) {
        $aging = $diffInHours . ' hrs';
    } else {
        $diffInDays = $createdAt->diffInDays($now);
        $remainingHours = $diffInHours % 24;

        if ($remainingHours > 0) {
            $aging = $diffInDays . ' days ' . $remainingHours . ' hrs';
        } else {
            $aging = $diffInDays . ' days';
        }
    }

    return [
        'assign_id'        => $ticket->assign_id,
        'vehicle_number'   => $ticket->vehicle_number,
        'ticket_id'        => $ticket->ticket_id ?? null,
        'city'             => $ticket->city ?? null,
        'zone_id'          => $ticket->zone_id,
        'state'            => $ticket->state,
        'vehicle_type'     => $ticket->vehicle_type,
        'poc_number'       => $ticket->poc_number,
        'contact_number'   => $ticket->contact_number,
        'description'      => $ticket->description,
        'address'          => $ticket->address,
        'repair_type'      => $ticket->repair_type,
        'latitude'         => $ticket->latitude,
        'longitude'        => $ticket->longitude,
        'gps_pin_address'  => $ticket->gps_pin_address,
        'image'            => $ticket->image,
        'status'           => $ticket->status,
        'created_by'       => $ticket->created_by,
        'type'             => $ticket->type,
        'created_at'       => $ticket->created_at ? $ticket->created_at->format('d M Y h:i A'):null,
        'aging'            => $aging,
    ];
});

        return response()->json([
            'status' => true,
            'message'=>"Tickets fetched successfully",
            'tickets'   => $tickets
        ]);
}

public function authenticate(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

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
    
public function getUserData(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();
        
        // Validate API mode
        // if (!isset($settings['API_CLUB_MODE']) || $settings['API_CLUB_MODE'] != 1) {
        //     throw new \Exception('API is not in production mode');
        // }

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'GET_USER_BY_ID_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }
        
        $data = $this->authenticate($request);
        $user_id = $data['user_id'];
        $logData['api_user_id'] = $user_id;
        
        $endpoint = preg_replace('/\{(\$)?user_id\}/', $user_id, $settings['GET_USER_BY_ID_ENDPOINT']);
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/');
        $logData['api_endpoint'] = $url;

        $token = $data['token'];

        // Make API request with timeout
        $response = Http::timeout(30)
            ->retry(3, 100) // Retry 3 times with 100ms delay
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => "Bearer {$token}"
            ])
            ->get($url);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle non-successful responses
        if ($response->failed()) {
            $errorResponse = $response->json() ?? $response->body();
            // $logData['error_message'] = 'API request failed: ' . ($errorResponse['message'] ?? $response->body());
            // MobitraApiLog::create($logData);

            Log::warning('User Data API Request Failed', [
                'status' => $response->status(),
                'response' => $errorResponse,
                'user_id' => $user_id
            ]);
            
            return response()->json([
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ], $response->status());
        }

        $responseData = $response->json();
        // MobitraApiLog::create($logData);

        return response()->json([
            'status' => $response->status(),
            'data' => $responseData
        ]);

    } catch (\Illuminate\Http\Client\RequestException $e) {
        $logData['status_code'] = 503;
        $logData['status_type'] = 'Error';
        // MobitraApiLog::create($logData);

        Log::error('User Data API Connection Error', [
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
        // MobitraApiLog::create($logData);

        Log::error('User Data API Error', [
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

public function getUserDevices(Request $request,$vehicle_number='')
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null, // Will be populated from API response
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
    ];

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
        
        $vehicle_data = '&vehicleNumber=' . $vehicle_number;
        
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($endpoint, '/') . $vehicle_data;
        $logData['api_endpoint'] = $url;

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

            return [
                'status' => $response->status(),
                'message' => 'API request failed',
                'errors' => $errorResponse
            ];
        }

        $responseData = $response->json();
        
        // print_r($responseData);exit;
        // MobitraApiLog::create($logData);

        return [
            'status' => $response->status(),
            'page' => $page,
            'pageSize' => $pageSize,
            'data' => $responseData
        ];

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

        return [
            'status' => 503,
            'message' => 'Service unavailable',
            'error' => 'Could not connect to API service'
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('User Devices API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return[
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}

public function getRoleBasedImeiData(Request $request)
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null,
        'api_endpoint' => null, // Will be set to actual endpoint URL
        'status_code' => null,
        'status_type' => null
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'VW_ROLE_BASED_IMEI_ENDPOINT', 'GET_USER_LIST_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // First get the device list
        $deviceResponse = $this->getUserDevices($request);
        // print_r($deviceResponse);exit;
        if (!($deviceResponse['status'] == 200)) {
            $logData['status_code'] = $deviceResponse['status'];
            $logData['status_type'] = 'Error';
            $logData['error_message'] = 'Failed to get device list';
            // MobitraApiLog::create($logData);

            return [
                'status' => $deviceResponse['status'],
                'message' => 'Failed to get device list',
                'errors' => $deviceResponse['errors'] ?? null
            ];
        }

        $devices = $deviceResponse['data']['payload']['deviceList'] ?? [];
        
        // Extract unique accountId and roleId combinations
        $accountRoleMap = [];
        foreach ($devices as $device) {
            $accountId = $device['accountId'] ?? null;
            $roleId = $device['role']['id'] ?? null;
            
            if ($accountId && $roleId) {
                if (!isset($accountRoleMap[$accountId])) {
                    $accountRoleMap[$accountId] = [];
                }
                if (!in_array($roleId, $accountRoleMap[$accountId])) {
                    $accountRoleMap[$accountId][] = $roleId;
                }
            }
        }

        $baseUrl = rtrim($settings['BASE_URL'], '/');
        $token = $settings['API_TOKEN'];
        $results = [];
        $successCount = 0;
        $failureCount = 0;
        
        // Process each accountId and roleId combination
        foreach ($accountRoleMap as $accountId => $roleIds) {
            foreach ($roleIds as $roleId) {
                // Construct endpoint URL with parameters
                $endpoint = preg_replace([
                    '/\{(\$)?accountId\}/',
                    '/\{(\$)?roleIds\}/'
                ], [
                    $accountId,
                    $roleId
                ], $settings['VW_ROLE_BASED_IMEI_ENDPOINT']);

                $url = $baseUrl . '/' . ltrim($endpoint, '/');
                
                // Create log entry for this specific API call
                $callLogData = [
                    'user_id' => $logData['user_id'],
                    'api_username' => $logData['api_username'],
                    'api_user_id' => $accountId,
                    'api_endpoint' => $url, // Actual endpoint URL being called
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $response = Http::timeout(30)
                    ->retry(2, 100)
                    ->withHeaders([
                        'accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $token
                    ])
                    ->get($url);

                $callLogData['status_code'] = $response->status();
                $callLogData['status_type'] = $response->successful() ? 'Success' : 'Error';

                if ($response->successful()) {
                    $responseData = $response->json();
                    $successCount++;
                    
                    $results[] = [
                        'accountId' => $accountId,
                        'roleId' => $roleId,
                        'data' => $responseData,
                        'status' => 'success'
                    ];
                } else {
                    $failureCount++;
                    $callLogData['error_message'] = $response->body();
                    
                    $results[] = [
                        'accountId' => $accountId,
                        'roleId' => $roleId,
                        'error' => $response->json() ?? $response->body(),
                        'status' => 'failed',
                        'statusCode' => $response->status()
                    ];
                }

                // MobitraApiLog::create($callLogData);
            }
        }

        // Create summary log entry with the main endpoint pattern
        $logData['api_endpoint'] = $settings['VW_ROLE_BASED_IMEI_ENDPOINT']; // The endpoint pattern
        $logData['status_code'] = 200;
        $logData['status_type'] = 'Success';
        $logData['additional_info'] = json_encode([
            'total_devices_processed' => count($devices),
            'unique_account_role_pairs' => count($results),
            'successful_calls' => $successCount,
            'failed_calls' => $failureCount
        ]);
        // MobitraApiLog::create($logData);

        return [
            'status' => 200,
            'message' => 'Process completed',
            'totalDevicesProcessed' => count($devices),
            'uniqueAccountRolePairs' => count($results),
            'successfulCalls' => $successCount,
            'failedCalls' => $failureCount,
            'results' => $results
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        // MobitraApiLog::create($logData);

        Log::error('Role Based IMEI Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}

public function getVehicleStatusData(Request $request,$imei='')
{
    // Initialize log data with default values
    $logData = [
        'user_id' => auth()->id(),
        'api_username' => $request->user_name ?? EvMobitraApiSetting::where('key_name', 'USER_NAME')->value('value'),
        'api_user_id' => null,
        'api_endpoint' => null,
        'status_code' => null,
        'status_type' => null,
        'created_at' => now(),
        'updated_at' => now()
    ];

    try {
        // Get API settings
        $settings = EvMobitraApiSetting::pluck('value', 'key_name')->toArray();

        // Validate required settings
        $requiredSettings = ['BASE_URL', 'API_TOKEN', 'FLEET_TRACKING_ENDPOINT'];
        foreach ($requiredSettings as $setting) {
            if (empty($settings[$setting])) {
                throw new \Exception("Missing required API setting: $setting");
            }
        }

        // First get the IMEI and role data from role-based endpoint
        $deviceResponse = $this->getRoleBasedImeiData($request);
        
        if (!isset($deviceResponse['status']) || $deviceResponse['status'] != 200) {
            $logData['status_code'] = $deviceResponse['status'] ?? 500;
            $logData['status_type'] = 'Error';
            $logData['error_message'] = 'Failed to get IMEI data';
            MobitraApiLog::create($logData);

            return [
                'status' => $deviceResponse['status'] ?? 500,
                'message' => 'Failed to get IMEI data',
                'errors' => $deviceResponse['errors'] ?? null
            ];
        }

        // Extract all IMEI numbers and roleIds from the response
        $imeiNumbers = [];
        $roleIds = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['roleId']) && !in_array($device['roleId'], $roleIds)) {
                        $roleIds[] = $device['roleId'];
                    }
                }
            }
        }
        
        $imeiNumbers = [$imei];
       
        // Get parameters from request or use defaults
        $params = [
            'accountId' => $request->input('accountId', 11),
            'limit' => $request->input('limit', 50),
            'offset' => $request->input('offset', 1),
            'startDate' => $request->input('startDate', strtotime('-1 day')),
            'endDate' => $request->input('endDate', time()),
            'status' => $request->input('status', '')
        ];

        // GraphQL query payload
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

        // Make GraphQL request
        $url = rtrim($settings['BASE_URL'], '/') . '/' . ltrim($settings['FLEET_TRACKING_ENDPOINT'], '/');
        $logData['api_endpoint'] = $url;

        $response = Http::timeout(120)
            ->retry(3, 100)
            ->withHeaders([
                'accept' => 'application/json',
                'Authorization' => 'Bearer ' . $settings['API_TOKEN'],
                'Content-Type' => 'application/json'
            ])
            ->post($url, $payload);

        // Set response status info
        $logData['status_code'] = $response->status();
        $logData['status_type'] = $response->successful() ? 'Success' : 'Error';

        // Handle response
        if ($response->failed()) {
            $logData['error_message'] = $response->body();
            MobitraApiLog::create($logData);

            Log::warning('Vehicle Status API Request Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $payload
            ]);
            
            return [
                'status' => $response->status(),
                'message' => 'GraphQL request failed',
                'errors' => $response->json() ?? $response->body(),
                'imei_count' => count($imeiNumbers),
                'roleIds_count' => count($roleIds)
            ];
        }

        $responseData = $response->json();
        // print_r($responseData);exit;
        $vehicleData = $responseData['data']['vehicleStatusAndSinceUpdated'] ?? null;

        // Create IMEI mapping
        $imeiMapping = [];
        foreach ($deviceResponse['results'] as $result) {
            if (isset($result['data']['payload'])) {
                foreach ($result['data']['payload'] as $device) {
                    if (!empty($device['imei'])) {
                        $imeiMapping[$device['imei']] = [
                            'roleId' => $device['roleId'],
                            'accountId' => $device['accountId'],
                            'vehicleNumber' => $device['vehicleNumber']
                        ];
                    }
                }
            }
        }

        // Enhance vehicle data
        $enhancedVehicles = [];
        if (isset($vehicleData['nodes'])) {
            foreach ($vehicleData['nodes'] as $vehicle) {
                $imei = $vehicle['IMEINumber'] ?? null;
                if ($imei && isset($imeiMapping[$imei])) {
                    $enhancedVehicles[] = array_merge($vehicle, [
                        'originalRoleId' => $imeiMapping[$imei]['roleId'],
                        'originalAccountId' => $imeiMapping[$imei]['accountId'],
                        'registeredVehicleNumber' => $imeiMapping[$imei]['vehicleNumber']
                    ]);
                } else {
                    $enhancedVehicles[] = $vehicle;
                }
            }
        }

        // Prepare response data
        $transformedData = [
            'summary' => [
                'total' => $vehicleData['totalCount'] ?? 0,
                'running' => $vehicleData['count']['running'] ?? 0,
                'stopped' => $vehicleData['count']['stopped'] ?? 0,
                'offline' => $vehicleData['count']['offline'] ?? 0,
                'imeiCount' => count($imeiNumbers),
                'roleIdsCount' => count($roleIds),
                'matchedVehicles' => count($enhancedVehicles)
            ],
            'vehicles' => $enhancedVehicles
        ];

        MobitraApiLog::create($logData);
        
        return [
            'status' => 200,
            'message' => 'Vehicle status data retrieved',
            'data' => $transformedData,
            'requestDetails' => [
                'roleIdsUsed' => $roleIds,
                'imeiNumbersUsed' => $imeiNumbers
            ]
        ];

    } catch (\Exception $e) {
        $logData['status_code'] = 500;
        $logData['status_type'] = 'Error';
        $logData['error_message'] = $e->getMessage();
        MobitraApiLog::create($logData);

        Log::error('Vehicle Status API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'status' => 500,
            'message' => 'Internal server error',
            'error' => $e->getMessage()
        ];
    }
}

     public function get_notification_list(Request $request)
    {
        $user = $request->user('rider');
        
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }
        
        $perPage = $request->query('per_page', 20);
    
        $notification_unread_count = B2BRidersNotification::where('rider_id', $user->id)->where('read_status',0)->get()->count();
        
        $notifications = B2BRidersNotification::where('rider_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    
        $notifications->getCollection()->transform(function ($data) {
            return [
                'id'          => $data->id,
                'title'       => $data->title,
                'description' => $data->description,
                'read_status'      => intval($data->read_status),
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
        $user = $request->user('rider');
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }
    
        $notification_data = B2BRidersNotification::where('id', $notification_id)
            ->where('rider_id', $user->id)
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




    public function get_vehicle_details(Request $request)
    {
        $user = $request->user('rider');
        
        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.',
            ], 404);
        }
        
    
        $vehicle_request = B2BVehicleRequests::where('rider_id', $user->id)
            ->where('status', 'completed')
            ->where('is_active', 1)
            ->first();
    
            $hubName = $user->zone->name ?? 'N/A';
            $hubLat  = $user->zone->lat !== null ? floatval($user->zone->lat) : null;
            $hubLong = $user->zone->long !== null ? floatval($user->zone->long) : null;
            
            
    
            if($vehicle_request){
                
            $vehicle = B2BVehicleAssignment::select('asset_vehicle_id','vehicle_front')
                ->where('rider_id', $user->id)
                ->where('req_id',$vehicle_request->req_id)
                ->first();
                
            
            if($vehicle){
                 $vehicle_no = AssetMasterVehicle::select('permanent_reg_number','fc_attachment','hsrp_copy_attachment','insurance_attachment')
                ->where('id', $vehicle->asset_vehicle_id)
                ->first();
                //  $vehicle_no = AssetMasterVehicle::where('id', $vehicle->asset_vehicle_id)
                // ->first();
                
               
           
            $getFullUrl = function ($file, $folder) {
                    return $file ? "EV/asset_master/{$folder}/" . $file : null;
                };
    
            // Call user devices API
            // $response = $this->getUserDevices($request, $vehicle_no->permanent_reg_number);
            //  $response = null;
            // $device = $this->getDummyUserDevice($user->id,$vehicle->asset_vehicle_id);
    
            // $imei = $device['imei'] ?? null;
    
            // if ($imei) {
    
            //     $vehicleResponse = $this->getVehicleStatusData($request, $imei);
            //     $vehicleData = !empty($vehicleResponse['data']['vehicles'])
            //         ? $vehicleResponse['data']['vehicles'][0]
            //         : $this->getDummyVehicleData();
            // } else {
    
            //     $vehicleData = $this->getDummyVehicleData();
            // }
            
            // print_r($vehicle->asset_vehicle_id);exit;
            $device = $this->getDummyUserDevice($user->id,$vehicle->asset_vehicle_id);//customized by Gowtham.S
            $vehicleData = $this->getDummyVehicleData();
            $vehicleData['IMEINumber'] = isset($vehicleData['IMEINumber']) && $vehicleData['IMEINumber']
                ? $vehicleData['IMEINumber']
                : $device['imei'];
            $device['vehicleDeliveryDate'] = $device['additionalInfo']['vehicle_delivery_date'] ?? null;
            
            return response()->json([
                'status' => true,
                'message' => 'Vehicle data retrieved',
                'data'=>[
                'hub' =>null,
                'request_id' =>null,
                'vehicle_details' => $device,
                'vehicle_status'  => $vehicleData,
                'images' =>[
                   'vehicle_image' => $vehicle->vehicle_front ? 'b2b/vehicle_front/' . $vehicle->vehicle_front : null,
                   'fc_image' =>$getFullUrl($vehicle_no->fc_attachment, "fc_attachments"),
                   'hsrp_image' =>$getFullUrl($vehicle_no->	hsrp_copy_attachment, "hsrp_certificate_attachments"),
                   'insurance_image' =>$getFullUrl($vehicle_no->insurance_attachment, "insurance_attachments"),
                    ]
                ]
            ]);
            } else {
                
            $hub = [
                "name"      => $hubName, //updated by Gowtham.S
                "latitude"  => $hubLat,
                "longitude" => $hubLong,
            ];
    
            return response()->json([
                'status' => true,
                'message' => 'Vehicle data retrieved',
                'data'=>[
                "hub"        => $hub,
                "request_id" => $vehicle_request->req_id??null,
                'vehicle_details' => null,
                'vehicle_status'  => null,
                'images' =>null,
                    ]
                
            ]);
        }
        } else { 
        
            $hub = [
                "name"      => $hubName, //updated by Gowtham.S
                "latitude"  => $hubLat,
                "longitude" => $hubLong
            ];
            
            $vehicle_request1 = B2BVehicleRequests::where('rider_id', $user->id)
            ->where('status', 'pending')
            ->where('is_active', 0)
            ->first();
            
            return response()->json([
                'status' => true,
                'message' => 'Vehicle data retrieved',
                'data' =>[
                "hub"        => $hub,
                "request_id" => $vehicle_request1->req_id??null,
                'vehicle_details' => null,
                'vehicle_status'  => null,
                'images' => null,
                    ]
                
            ]);
        }
    }



    private function getDummyUserDevice($riderId,$vehicleId)
    {
        
        $vehicle = AssetVehicleInventory::
            join('ev_tbl_asset_master_vehicles as amv', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'amv.id')
            ->leftJoin('ev_tbl_inventory_location_master as ilm', 'asset_vehicle_inventories.transfer_status', '=', 'ilm.id')
            ->leftJoin('vehicle_types as vt', 'amv.vehicle_type', '=', 'vt.id')
            ->leftJoin('ev_tbl_vehicle_models as vm', 'amv.model', '=', 'vm.id')
            ->leftJoin('ev_tbl_brands as vb', 'vm.brand', '=', 'vb.id')
            ->leftJoin('ev_tbl_color_master as vc', 'amv.color', '=', 'vc.id')
            ->leftJoin('ev_tbl_financing_type_master as ftm', 'amv.financing_type', '=', 'ftm.id')
            ->leftJoin('ev_tbl_asset_ownership_master as aom', 'amv.asset_ownership', '=', 'aom.id')
            ->leftJoin('ev_tbl_registration_types as rt', 'amv.registration_type', '=', 'rt.id')
            ->leftJoin('ev_tbl_insurer_name_master as inm', 'amv.insurer_name', '=', 'inm.id')
            ->leftJoin('ev_tbl_insurance_types as it', 'amv.insurance_type', '=', 'it.id')
            ->select(
                'amv.*',
                'ilm.name as vehicleStatus',
                'vt.name as vehicle_type_name',
                'vm.vehicle_model',
                'vc.name as vehicle_color',
                'vb.brand_name as vehicle_brand',
                'ftm.name as financing_type_name',
                'aom.name as asset_ownership_name',
                'rt.name as registration_type_name',
                'inm.name as insurer_type_name',
                'it.name as insurance_type_name',
                DB::raw("CASE 
                    WHEN amv.battery_type = 1 THEN 'Self-Charging' 
                    WHEN amv.battery_type = 2 THEN 'Portable' 
                    ELSE 'Unknown' 
                END as battery_type_name")
            )->where('amv.id',$vehicleId)
            ->first();
    
      $userid = B2BRider::where('id', $riderId)->value('created_by');
    $customer = CustomerLogin::with('customer_relation')->find($userid);
        // Dummy structure
       $dummy = [
        "id" => 0,
        "deviceId" => "",
        "userId" => "",
        "accountId" => 0,
        "role" => "",
        "imei" => "",
        "simNumber" => "",
        "vehicleType" => "",
        "vehicleNumber" => "",
        "driverName" => "",
        "driverMobile" => "",
        "device" => "",
        "deviceType" => "",
        "vehicleMake" => "",
        "model" => "",
        "fuelType" => "",
        "expectedMileage" => 0.00,
        "chassisNumber" => "",
        "displayNumber" => "",
        "status" => 0,
        "additionalInfo" => [
            "color" => "",
            "variant" => "",
            "customer" => "",
            "reg_type" => "",
            "ownership" => "",
            "reg_status" => "",
            "battery_type" => "",
            "insurer_name" => "",
            "lease_amount" => 0.00,
            "engine_number" => "",
            "hypothecation" => "",
            "financing_type" => "",
            "insurance_type" => "",
            "lease_end_date" => "",
            "fit_cert_exp_dt" => "",
            "hypothecated_to" => "",
            "reg_cert_exp_dt" => "",
            "insurance_end_dt" => "",
            "insurance_number" => "",
            "inventory_status" => "",
            "lease_start_date" => "",
            "permanent_reg_num" => "",
            "charger_serial_num" => "",
            "insurance_start_dt" => "",
            "permanent_reg_date" => "",
            "battery_variant_name" => "",
            "charger_variant_name" => "",
            "battery_serial_number" => "",
            "vehicle_delivery_date" => "",
            "master_lease_agreement" => ""
        ],
        "referenceId" => "",
        "favourite" => false,
        "createdBy" => "",
        "createdDt" => "",
        "lastUpdatedBy" => "",
        "lastUpdatedDt" => "",
        "prole" => "",
        "vehicleDeliveryDate" =>"",
        "clientName"=>"",
        "vehicleStatus"=>""
    ];
    
        // If no vehicle, return dummy
        if (!$vehicle) {
            return $dummy;
        }
    
        // Map DB values into dummy structure
        $dummy["id"] = $vehicle->id;
        $dummy["vehicleNumber"] = $vehicle->permanent_reg_number;
        $dummy["vehicleType"] = $vehicle->vehicle_type_name;
        $dummy["vehicleMake"] = $vehicle->vehicle_brand;
        $dummy["model"] = $vehicle->vehicle_model;
        $dummy["chassisNumber"] = $vehicle->chassis_number;
        $dummy["status"] = $vehicle->status;
        $dummy["createdBy"] = $vehicle->created_by;
        $dummy["createdDt"] = $vehicle->created_at;
        $dummy["lastUpdatedDt"] = $vehicle->updated_at;
         $dummy["clientName"] = $customer->customer_relation->name;
         $dummy["vehicleStatus"] = $vehicle->vehicleStatus;
         $dummy["vehicleDeliveryDate"] = $vehicle->vehicle_delivery_date;
        $dummy["imei"] = $vehicle->telematics_imei_number;
        
        // Additional Info
        $dummy["additionalInfo"]["color"] = $vehicle->vehicle_color;
        $dummy["additionalInfo"]["reg_type"] = $vehicle->registration_type_name;
        $dummy["additionalInfo"]["ownership"] = $vehicle->asset_ownership_name;
        $dummy["additionalInfo"]["battery_type"] = $vehicle->battery_type_name;
        $dummy["additionalInfo"]["insurer_name"] = $vehicle->insurer_type_name;
        $dummy["additionalInfo"]["financing_type"] = $vehicle->financing_type_name;
        $dummy["additionalInfo"]["insurance_type"] = $vehicle->insurance_type_name;
        $dummy["additionalInfo"]["insurance_number"] = $vehicle->insurance_number;
        $dummy["additionalInfo"]["insurance_start_dt"] = $vehicle->insurance_start_dt;
        $dummy["additionalInfo"]["insurance_end_dt"] = $vehicle->insurance_end_dt;
        $dummy["additionalInfo"]["permanent_reg_num"] = $vehicle->permanent_reg_num;
        $dummy["additionalInfo"]["permanent_reg_date"] = $vehicle->permanent_reg_date;
        $dummy["additionalInfo"]["reg_cert_exp_dt"] = $vehicle->reg_cert_exp_dt;
        $dummy["additionalInfo"]["fit_cert_exp_dt"] = $vehicle->fit_cert_exp_dt;
        $dummy["additionalInfo"]["battery_variant_name"] = $vehicle->battery_variant_name;
        $dummy["additionalInfo"]["battery_serial_number"] = $vehicle->battery_serial_number;
        $dummy["additionalInfo"]["charger_variant_name"] = $vehicle->charger_variant_name;
        $dummy["additionalInfo"]["charger_serial_num"] = $vehicle->charger_serial_num;
        $dummy["additionalInfo"]["vehicle_delivery_date"] = $vehicle->vehicle_delivery_date;
        $dummy["additionalInfo"]["master_lease_agreement"] = $vehicle->master_lease_agreement;
        $dummy["additionalInfo"]["engine_number"] = $vehicle->motor_number;
        return $dummy;
    }

    private function getDummyVehicleData()
    {
            return [
                "vehicleNumber" => "",
                "distanceTravelled" => 0.00,
                "lastIgnition" => "",
                "lastSpeed" => 0.00,
                "latitude" => 0.00,
                "longitude" => 0.00,
                "lastDbTime" => "",
                "lastContactedTime" => "",
                "gsmNetwork" => "",
                "gpsNetwork" => "",
                "battery" => 0,
                "charging" => 0,
                "vehicleType" => "",
                "deviceType" => "",
                "IMEINumber" => "",
                "vehicleStatus" => "",
                "vehicleSince" => "",
                "favourite" => false,
                "roleId" => 0,
                "address" => "",
                "redDotFlag" => false,
                "deviceSubscriptionExpiryDate" => "",
                "deviceEnableStatus" => false,
                "roleName" => "",
                "prRoleName" => "",
                "driverName" => "",
                "userId" => "",
                "deviceId" => "",
                "displayNumber" => "",
                "originalRoleId" => 0,
                "originalAccountId" => 0,
                "registeredVehicleNumber" => ""
            ];
    
    }
    
    public function get_ticket_status()
{
    $statuses = [
        'unassigned' => [
            'label' => 'Unassigned',
            'color' => '#EECACB' // Red
        ],
        'inprogress' => [
            'label' => 'In Progress',
            'color' => '#CAD8EE' // Orange
        ],
        'closed' => [
            'label' => 'Closed',
            'color' => '#CAEDCE' // Green
        ],
    ];

    // Return as JSON response
    return response()->json([
        'success' => true,
        'data' => $statuses
    ]);
}
}