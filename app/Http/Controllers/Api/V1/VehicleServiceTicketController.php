<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\Log;
use Modules\City\Entities\City; //updated by Mugesh.B
use Modules\City\Entities\Area;
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\MasterManagement\Entities\RepairTypeMaster; //updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Mail;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use App\Helpers\CustomHandler;

class VehicleServiceTicketController extends Controller
{
    public function ticket_create(Request $request){

        $validator = Validator::make($request->all(), [
            // 'ticket_id'        => 'required|unique:vehicle_service_tickets,ticket_id',
            'vehicle_no'       => 'required|string|max:100|regex:/^[A-Z0-9\- ]+$/i',
            'chassis_number'       => 'required|string|max:100',
            'city_id'          => 'required|integer|exists:ev_tbl_city,id',
            'area_id'          => 'required|integer|exists:zones,id',
            'vehicle_type'     => 'required|string|max:50',
            'poc_name'         => 'required|string|max:255',
            'poc_contact_no'   => 'required|string|max:20',
            'issue_remarks'    => 'required|string',
            'repair_type'      => 'required|integer|exists:ev_tbl_repair_types,id',
            'address'          => 'required|string',
            'lat'              => 'required|string|max:100',
            'long'             => 'required|string|max:100',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg|max:1024',//1MB Accept
            'dm_id'            => 'required|integer|exists:ev_tbl_delivery_men,id',
            'created_datetime' =>'required|date'
          ],
          [
            'vehicle_no.required'      => 'Vehicle number is required.',
            'vehicle_no.regex'         => 'Vehicle number may only contain letters, numbers, spaces, and hyphens.',
            
            'city_id.required'         => 'City selection is required.',
            'city_id.exists'           => 'Selected city is not valid.',
            
            'area_id.required'         => 'Zone selection is required.',
            'area_id.exists'           => 'Selected zone is not valid.',
            'poc_name.required'        => 'POC name is required.',
            'poc_contact_no.required'  => 'POC contact number is required.',
            
            
            'repair_type.required'     => 'Repair type is required.',
            'repair_type.exists'           => 'Invalid repair type selected.',
            
            'address.required'         => 'Address is required.',
            
            'lat.required'             => 'Latitude is required.',
            'long.required'            => 'Longitude is required.',
            
            'image.image'              => 'Uploaded file must be an image.',
            'image.mimes'              => 'Image must be a JPG or PNG file.',
            'image.max'                => 'Image size must be less than 1MB.',
            
            'created_datetime.required'=> 'Created date and time is required.',
            'created_datetime.date'    => 'Created date must be a valid datetime format.',
          ]
        );
        
        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 422);
        }
        
        // $development = true; //updated by Gowtham.s

        // if ($development) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'This feature is currently under development. Please check back later.'
        //     ]); 
        // }

        
        
         DB::beginTransaction();
    
        try {
            
             $ticket_id = CustomHandler::GenerateTicketId($request->city_id);
           
            if ($ticket_id == "" || $ticket_id == null) {
                return response()->json(['success' => false,'message'  =>'Ticket ID creation failed']);
            }
        
             $imagePath = null;
             $imageUrl = null;
            
            if ($request->hasFile('image')) {
                $imagePath = CustomHandler::uploadFileImage($request->file('image'), 'EV/images/vehicle_ticket_images');
                
                 $imageUrl = asset('EV/images/vehicle_ticket_images/' . $imagePath);
            }
            
           $vehicle = AssetMasterVehicle::where('chassis_number', $request->chassis_number)
                ->first();
        
            // Store record
            $ticket = VehicleTicket::create([
                'ticket_id'         => $ticket_id,
                'vehicle_no'        => $request->vehicle_no,
                'city_id'           => $request->city_id,
                'area_id'           => $request->area_id,
                'vehicle_type'      => $request->vehicle_type,
                'poc_name'          => null,
                'poc_contact_no'    => null,
                'driver_name'       => $request->poc_name,
                'driver_number'     => $request->poc_contact_no ,
                'issue_remarks'     => $request->issue_remarks,
                'repair_type'       => $request->repair_type,
                'address'           => $request->address,
                'gps_pin_address'   => $request->gps_pin_address,
                'lat'               => $request->lat,
                'long'              => $request->long,
                'image'             => $imagePath,
                'created_datetime'  => $request->created_datetime,
                'created_by'        => $request->created_by,
                'created_role'      => $request->created_role,
                'dm_id'             => $request->dm_id,
                'web_portal_status' => 0,
                'platform'          => 'rider-app',
                'ticket_status'     => 0,
            ]);
            
            $city = City::find($request->city_id);
            
            
            $state_name = $city && $city->state ? $city->state->state_name : '';
            
            $repair_type =  RepairTypeMaster::find($request->repair_type);

            
            
             $createdDatetime = isset($request->created_datetime) 
                ? Carbon::parse($request->created_datetime)->utc() 
                : Carbon::now()->utc();
                
            $vehicle_type = optional(VehicleType::find($request->vehicle_type))->name;
        
            $customerLongitude = ($request->long === "" || $request->long === null)
                ? null
                : $request->long;
            
            $customerLatitude = ($request->lat === "" || $request->lat === null)
                ? null
                : $request->lat;
                
             $ticketData = [
                "vehicle_number" => $request->vehicle_no,
                "updatedAt" => $createdDatetime,
                "ticket_status" => "unassigned",
                "chassis_number" => $vehicle->chassis_number ?? null,
                "telematics" =>  $vehicle->telematics_imei_number ?? null,
                "battery" =>  $vehicle->battery_serial_no ?? null,
                "vehicle_type" => $vehicle_type,
                "state" => $city->state->state_name ?? '',
                "priority" => 'High',
                "point_of_contact_info" => $request->poc_name.' - '. $request->poc_contact_no,
                "job_type" => $repair_type->name ?? '',
                "issue_description" => $request->issue_remarks,
                'image' => $imagePath ? [$imagePath] : [],
                "address" => $request->address,
                "greendrive_ticketid" => $ticket_id,
                'driver_name'   => $request->poc_name ?? '',
                'driver_number'   => $request->poc_contact_no ?? '',
                "customer_number" => '',
                "customer_name" => '',
                'customer_email' => '',
               'customer_location' => [
                    $customerLongitude,
                    $customerLatitude
                ], 
                "current_status" => 'open',
                "createdAt" => $createdDatetime,
                "city" => $city->city_name ?? null,
            ];

            $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                'type'       => 'rider-app',
            ]));
            
            
            FieldProxyLog::create([
                'fp_id'      => $fieldProxyTicket->id,  
                'status'     => 'unassigned',      
                "current_status" => 'open',
                'remarks'    => "Ticket raised for vehicle {$request->vehicle_no}",
                'type'       => 'rider-app',
            ]);
            
            $apiTicketData = $ticketData;
            $apiTicketData['image'] = $imageUrl ? [$imageUrl] : [];
            $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
            
            
            $apiData = [
                "sheetId" => "tickets",
                "tableData" => $apiTicketData
            ];
    
            $fieldproxy_base_url = BusinessSetting::where('key_name', 'fieldproxy_base_url')->value('value');
            $fieldproxy_create_endpoint = BusinessSetting::where('key_name', 'fieldproxy_create_enpoint')->value('value');
            
            $apiUrl = $fieldproxy_base_url . $fieldproxy_create_endpoint;
            $apiKey = env('FIELDPROXY_API_KEY', null); // set in .env
    
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
    
    
            DB::commit();
        
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully.',
                // 'data' => $ticket,
                'ticket_id'=>$ticket->ticket_id
            ], 200);
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ticket creation exception', 
            [                
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'input'   => $request->all()
                
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ticket creation failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function get_rider_tickets(Request $request,$dm_id){
        
        $rider = Deliveryman::where('id', $dm_id)->where('delete_status',0)->first();
        if(!$rider){
            return response()->json(['success' => false,'message' => 'rider not found'], 404);
        }
        $status = $request->status;
        
        
        $get_dm_tickets = VehicleTicket::with('field_proxy_relation')->where("dm_id", $dm_id)
        ->where("ticket_status", 0)
        ->when($status == 'opened', function ($query) {
            $query->whereHas('field_proxy_relation', function ($q) {
                $q->where('current_status', '!=', 'closed');
            });
        })
        ->when($status == 'closed', function ($query) {
            $query->whereHas('field_proxy_relation', function ($q) {
                $q->where('current_status', '=', 'closed');
            });
        })
        ->select([
            'id', 'ticket_id', 'vehicle_no', 'city_id', 'area_id', 'vehicle_type',
            'poc_name', 'poc_contact_no', 'issue_remarks', 'repair_type', 'address',
            'lat', 'long', 'image', 'created_datetime', 'dm_id', 'platform', 'created_at', 'updated_at', 'created_by'
        ])
        ->with('user:id,first_name,last_name') // eager load user details
        ->get()
        ->map(function ($data) {
            return [
                'id' => $data->id,
                'ticket_id' => $data->ticket_id,
                'vehicle_no' => $data->vehicle_no,
                'city_id' => $data->city_id,
                'area_id' => $data->area_id,
                'vehicle_type' => $data->vehicle_type,
                'poc_name' => $data->poc_name,
                'poc_contact_no' => $data->poc_contact_no,
                'issue_remarks' => $data->issue_remarks,
                'repair_type' => $data->repair_type,
                'address' => $data->address,
                'lat' => $data->lat,
                'long' => $data->long,
                'image' => $data->image,
                'created_datetime' => date('Y-m-d h:i:s',strtotime($data->created_datetime)),
                'dm_id' => $data->dm_id,
                'platform' => $data->platform,
                'created_at' => date('Y-m-d h:i:s',strtotime($data->created_at)),
                'updated_at' => date('Y-m-d h:i:s',strtotime($data->updated_at)),
                'created_by' => $data->user
                    ? $data->user->first_name . ' ' . $data->user->last_name
                    : null,
            ];
        });

        return response()->json(['success' => true,'message' => 'tickets fetched successfully','data'=>$get_dm_tickets], 200);
        
    }
    
    
    public function get_repair_types(Request $request){
        
         try {
             
            $repair_types = RepairTypeMaster::where('status', 1)
            ->select('id', 'name')
            ->get();


            return response()->json([
                'success' => true,
                'message' => 'Repair types fetched successfully.',
                'data' => $repair_types
            ], 200);
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch repair types.',
                    'error' => $e->getMessage()
                ], 500);
            }
    }
    
    
}