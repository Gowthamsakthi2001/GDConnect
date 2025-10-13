<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Illuminate\Support\Facades\Log;
use Modules\City\Entities\City; //updated by Mugesh.B
use Modules\City\Entities\Area;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Mail;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use App\Helpers\CustomHandler;

class VehicleServiceTicketController extends Controller
{
    public function ticket_create(Request $request){
        
        $validator = Validator::make($request->all(), [
            // 'ticket_id'        => 'required|unique:vehicle_service_tickets,ticket_id',
            'vehicle_no' => 'required|string|max:100|regex:/^[A-Z0-9\- ]+$/i',
            'city_id'          => 'required|integer|exists:ev_tbl_city,id',
            'area_id'          => 'required|integer|exists:ev_tbl_area,id',
            'vehicle_type'     => 'required|string|max:50',
            'poc_name'         => 'required|string|max:255',
            'poc_contact_no'   => 'required|string|max:20',
            'issue_remarks'    => 'required|string',
            'repair_type'      => 'required|integer|in:1,2,3,4,5',
            'address'          => 'required|string',
            'lat'              => 'required|string|max:100',
            'long'             => 'required|string|max:100',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg|max:1024',//1MB Accept
            'dm_id'            => 'required|integer|exists:ev_tbl_delivery_men,id',
            'created_datetime' =>'required|date'
          ],
          [
            //   'ticket_id.unique' =>'The ticket has already been created. Please create a new ticket.'
          ]
        );
        
        if ($validator->fails()) {
            return response()->json(['success' => false,'errors' => $validator->errors()], 422);
        }
        
        $development = true; //updated by Gowtham.s

        if ($development) {
            return response()->json([
                'success' => false,
                'message' => 'This feature is currently under development. Please check back later.'
            ]); 
        }

        
        
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
        
            // Store record
            $ticket = VehicleTicket::create([
                'ticket_id'         => $ticket_id,
                'vehicle_no'        => $request->vehicle_no,
                'city_id'           => $request->city_id,
                'area_id'           => $request->area_id,
                'vehicle_type'      => $request->vehicle_type,
                'poc_name'          => $request->poc_name,
                'poc_contact_no'    => $request->poc_contact_no,
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
            
            
            $repairTypeMap = [
                1 => "Breakdown Repair",
                2 => "Running Repair",
                3 => "PMS (Preventive Maintenance Service)", 
                4 => "PDI (Pre Delivery Inspection)",   
                5 => "Scheduled Service",             
            ];


            $repair_type = $repairTypeMap[$request->repair_type] ?? null;
            
            $area = Area::where('id', $request->area_id)->first();
            $area_name = $area ? $area->Area_name : '';
            
            // Ensure $request->created_datetime exists, or use now()
             $createdDatetime = isset($request->created_datetime) 
                ? Carbon::parse($request->created_datetime)->utc() 
                : Carbon::now()->utc();
        
            // 6️⃣ Prepare data for FieldProxy API
             $ticketData = [
                "vehicle_type" => $request->vehicle_type,
                "vehicle_number" => $request->vehicle_no,
                "vehicle_name" => null,
                "vehicle_id" => null,
                "updatedat" => $createdDatetime,
                "ticket_status" => "pending",
                "telematics" => null,
                "technician_notes" => null,
                "task_performed" => null,
                "sync" => true,
                "state" => $area_name,
                "started_location" => null,
                "started_at" => null,
                "service_type" => null,
                "service_charges" => 0,
                "role" => null,
                "repair_type" => $repair_type,
                "priority" => 'High',
                "point_of_contact_info" => null,
                "odometer" => 0,
                "observation" => null,
                "location" => null,
                "lastsync" => null,
                "labour_description" => null,
                "job_type" => $repair_type,
                "issue_description" => $request->issue_remarks,
                'image' => $imageUrl ? [$imageUrl] : [],
                "greendrive_ticketid" => $ticket_id,
                "final_image" => null,
                "ended_location" => null,
                "ended_at" => null,
                "deletedat" => null,
                "delete" => false,
                "customer_number" => $request->poc_contact_no,
                "customer_name" => $request->poc_name,
                "current_status" => 'open',
                "createdat" => $createdDatetime,
                "contact_details" => "",
                "city" => $city->city_name ?? null,
                "chassis_number" => "",
                "category" => "",
                "battery" => null,
                "assignment_info" => null,
                "assigned_technician_id" => null,
                "assigned_by" => null,
                "assigned_at" => null,
                "address" => $request->address,
                "final_technician_notes" => null
            ];
            
            
    
            $apiData = [
                "tableName" => "tickets",
                "data" => [$ticketData]
            ];
    
            $apiUrl = 'https://api-india-1.fieldproxy.ai/api/insert';
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
            if ($curlError) {
                Log::error('FieldProxy cURL error', ['ticket_id' => $ticket_id, 'error' => $curlError]);
            } elseif ($httpCode >= 400) {
                Log::error('FieldProxy returned HTTP error', [
                    'ticket_id' => $ticket_id,
                    'http_code' => $httpCode,
                    'body' => $responseBody
                ]);
            } else {
                $decoded = json_decode($responseBody, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('FieldProxy returned non-JSON response', [
                        'ticket_id' => $ticket_id,
                        'http_code' => $httpCode,
                        'body' => $responseBody
                    ]);
                } else {
                    $fieldproxyResult = $decoded;
                    Log::info('FieldProxy response', ['ticket_id' => $ticket_id, 'response' => $fieldproxyResult]);
                }
            }
    
    
            DB::commit();
        
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully.',
                // 'data' => $ticket,
                'ticket_id'=>$ticket->ticket_id
            ], 200);
        
        } catch (\Exception $e) {
            DB::rollBack();
     Log::error('Ticket creation exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        // $get_dm_tickets = VehicleTicket::where("dm_id", $dm_id)
        // ->where("ticket_status", 0)
        // ->select([
        //     'id', 'ticket_id', 'vehicle_no', 'city_id', 'area_id', 'vehicle_type',
        //     'poc_name', 'poc_contact_no', 'issue_remarks', 'repair_type', 'address',
        //     'lat', 'long', 'image', 'created_datetime', 'dm_id', 'platform','created_at', 'updated_at'
        // ])
        // ->get();
        $get_dm_tickets = VehicleTicket::where("dm_id", $dm_id)
        ->where("ticket_status", 0)
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
}