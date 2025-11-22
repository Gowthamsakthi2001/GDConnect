<?php

namespace Modules\VehicleServiceTicket\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\VehicleServiceTicket\Entities\FieldProxyTicket;//updated by Mugesh.B
use Modules\VehicleServiceTicket\Entities\FieldProxyLog;//updated by Mugesh.B
use Modules\VehicleManagement\Entities\VehicleType; //updated by Mugesh.B
use Modules\AssetMaster\Entities\VehicleTransferChassisLog;//updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetVehicleInventory;
use Modules\B2B\Entities\B2BVehicleAssignment;//updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\B2B\Entities\B2BVehicleAssignmentLog;//updated by Mugesh.B
use Modules\MasterManagement\Entities\RepairTypeMaster;
use Modules\B2B\Entities\B2BServiceRequest;
use Illuminate\Support\Facades\Log;
use Modules\City\Entities\Area;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketExport;//updated by Mugesh.B
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Mail;
use Modules\VehicleServiceTicket\Entities\VehicleTicket;
use App\Helpers\CustomHandler;
use Modules\City\Entities\City;

class VehicleServiceTicketController extends Controller
{
    
    public function create_web_ticket(Request $request)
    {
        $cities = City::where('status',1)->get();
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        $apiKey = BusinessSetting::where('key_name', 'google_map_api_key')->value('value');
        $repair_types = RepairTypeMaster::where('status',1)->get();
        return view('vehicleserviceticket::web_ticket_create_form',compact('cities' ,'vehicle_types' ,'apiKey' , 'repair_types'));
    }
    
    public function create_user_ticket(Request $request)
    {
        $cities = City::where('status',1)->get();
        return view('vehicleserviceticket::user_ticket_create_form',compact('cities'));
    }
    
    public function form_subimit_welcome(Request $request)
    {
       $encodedTicketId = $request->query('token'); // get from query
        $ticket_id = null;
    
        if ($encodedTicketId) {
            $decoded = base64_decode($encodedTicketId, true);
            // Validate decoded data
            if ($decoded !== false) {
                $ticket_id = $decoded;
            }
        }
        return view('vehicleserviceticket::form_submit_welcome',compact('ticket_id'));
    }
    
    public function new_ticket_create(Request $request){
     
     
            $validator = Validator::make($request->all(), [
                'vehicle_no'        => 'required|string|max:100|regex:/^[A-Z0-9\- ]+$/i',
                'city_id'           => 'required|exists:ev_tbl_city,id',
                'area_id'           => 'required|exists:ev_tbl_area,id',
                'vehicle_type'      => 'required|string|max:50',
                'poc_name'          => 'required|string|max:255',
                'poc_contact_no'    => 'required|string|max:20',
                'issue_remarks'     => 'required|string',
                // 'chassis_number'     => 'required|string',
                // 'battery_number'     => 'required|string',
                // 'telematics_number'     => 'required|string',
                'repairType'        => 'required|exists:ev_tbl_repair_types,id',
                // 'address'           => 'required|string',
                'latitude'          => 'nullable|string|max:100',
                'longitude'         => 'nullable|string|max:100',
                'image'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'dm_id'             => 'nullable|integer|exists:ev_tbl_delivery_men,id',
                'created_datetime'  => 'required|date',
                
                'form_type'         => 'required|in:web_portal_form,user_form'
            ], [
                'area_id.required' => 'The state field is required'
            ]);
            
            // $validator->sometimes('dm_id', 'required|integer|exists:ev_tbl_delivery_men,id', function ($input) {
            //     return $input->form_type === 'user_form';
            // });
          
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }
            
            if($request->form_type == "user_form"){
                $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user();
                if(!$customer){
                    return response()->json(['success' => false,'message'  =>'Created by not found']);
                }
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
        
        $platform = '';
        if($request->form_type == "web_portal_form"){
            $platform = 'web-portal-user';
        }
        
        $created_by = null;
        if($request->form_type == "user_form"){
            $platform = 'web-portal-customer';
            $created_by = $customer->id;
        }
        
        
        
          // Store record
            $ticket = VehicleTicket::create([
                'ticket_id'         => $ticket_id,
                'vehicle_no'        => $request->vehicle_no,
                'city_id'           => $request->city_id,
                'area_id'           => $request->area_id,
                'vehicle_type'      => $request->vehicle_type,
                'driver_name'          => $request->poc_name,
                'driver_number'    => $request->poc_contact_no,
                'issue_remarks'     => $request->issue_remarks,
                'repair_type'       => $request->repairType,
                'address'           => $request->address,
                'gps_pin_address'   => $request->gps_pin_address,
                'lat'               => $request->latitude,
                'long'              => $request->longitude,
                'image'             => $imagePath,
                'created_datetime'  => $request->created_datetime,
                'created_by'        => $created_by,
                'created_role'      => $request->created_role,
                'dm_id'             => $request->dm_id,
                'web_portal_status' => 0,
                'platform'          => $platform,
                'ticket_status'     => 0,
            ]);
        
           $city = City::with('state')->find($request->city_id);
          

            $state_name = $city && $city->state ? $city->state->state_name : '';
            
            

            $createdDatetime = isset($request->created_datetime) 
            ? Carbon::parse($request->created_datetime)->utc()   
            : Carbon::now()->utc();
                
    
            $customerLongitude = ($request->longitude === "" || $request->longitude === null)
                ? null
                : $request->longitude;
            
            $customerLatitude = ($request->latitude === "" || $request->latitude === null)
                ? null
                : $request->latitude;
            
             $vehicle = AssetMasterVehicle::where('permanent_reg_number' , $request->vehicle_no)->first();
             
             $repair_type =  RepairTypeMaster::find($request->repairType);

             $ticketData = [
                "vehicle_number" => $request->vehicle_no,
                "updatedAt" => $createdDatetime,
                "ticket_status" => "unassigned",
                "chassis_number" => $vehicle->chassis_number ?? null,
                "telematics" => $vehicle->telematics_imei_number ?? null,
                "battery" => $vehicle->battery_serial_no ?? null,
                "vehicle_type" => $vehicle->vehicle_type_relation->name ?? null,
                "state" => $state_name,
                "priority" => 'High',
                "point_of_contact_info" => $request->poc_name." - ".$request->poc_contact_no,
                "job_type" => $repair_type->name ?? '-',
                "issue_description" => $request->issue_remarks,
                'image' => $imagePath ? [$imagePath] : [],
                "greendrive_ticketid" => $ticket_id,
                "customer_number" => null,
                "customer_name" => null,
                'driver_name'   => $request->poc_name ?? '',
                'driver_number'   => $request->poc_contact_no ?? '',
                'customer_email' =>'',
                'customer_location' => [
                    $customerLongitude,
                    $customerLatitude
                ], 
                "current_status" => 'open',
                "createdAt" => $createdDatetime,
                "city" => $city->city_name ?? null
            ];
            
            $fieldProxyTicket = FieldProxyTicket::create(array_merge($ticketData, [
                'type'       => 'web-portal-user',
            ]));
            
            
            FieldProxyLog::create([
                'fp_id'      => $fieldProxyTicket->id,  
                'status'     => 'unassigned',      
                "current_status" => 'open',
                'remarks'    => "Ticket raised for vehicle {$request->vehicle_no}",
                'type'       => 'web-portal-user',
            ]);
            
            $apiTicketData = $ticketData;
            $apiTicketData['image'] = $imageUrl ? [$imageUrl] : [];
            $apiTicketData['driver_number'] = preg_replace('/^\+91/', '', $ticketData['driver_number']);
            $apiTicketData['customer_number'] = preg_replace('/^\+91/', '', $ticketData['customer_number']);
            
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
    

    
    
    
            public function ticket_list(Request $request, $type)
        {
            if ($request->ajax()) {
                try {
                    // Validate the type parameter
                    $validTypes = ['all', 'pending', 'assigned', 'work_in_progress', 'hold', 'closed'];
                    if (!in_array($type, $validTypes)) {
                        return response()->json(['error' => 'Invalid ticket type'], 422);
                    }
                    
                    $timeline = $request->input('timeline');
                    $from_date = $request->input('from_date');
                    $to_date = $request->input('to_date');
                    
        
                    // Pagination parameters
                    $length = $request->input('length', 25);
                    $start = $request->input('start', 0);
        
                    $localTickets = VehicleTicket::query();
                
                    if (!empty($timeline)) {
                        $now = now();
                        switch ($timeline) {
                            case 'today':
                                $localTickets->whereDate('created_at', $now->toDateString());
                                break;
                            case 'this_week':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfWeek()->toDateTimeString(),
                                    $now->endOfWeek()->toDateTimeString()
                                ]);
                                break;
                            case 'this_month':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfMonth()->toDateTimeString(),
                                    $now->endOfMonth()->toDateTimeString()
                                ]);
                                break;
                            case 'this_year':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfYear()->toDateTimeString(),
                                    $now->endOfYear()->toDateTimeString()
                                ]);
                                break;
                        }
                    } elseif (!empty($from_date) || !empty($to_date)) {
                        // Date range filter
                        if (!empty($from_date)) {
                            $localTickets->where('created_at', '>=', Carbon::parse($from_date)->startOfDay());
                        }
                        if (!empty($to_date)) {
                            $localTickets->where('created_at', '<=', Carbon::parse($to_date)->endOfDay());
                        }
                    }
                
                    // Fetch the filtered tickets
                    $localTickets = $localTickets->get();
                    
     
                    

                    // Initialize query conditions
                    $conditions = [];
        
                    // Ticket status filter
                    if ($type !== 'all') {
                        $conditions['ticket_status'] = $type;
                    }
            
            
                    // API request to FieldProxy
                    $apiResponse = Http::withHeaders([
                        'x-api-key' => env('FIELDPROXY_API_KEY')
                    ])->post('https://api-india-1.fieldproxy.ai/api/read', [
                        'tableName' => 'tickets',
                        'condition' => $conditions,
                        'limit' => $length,
                        'offset' => $start
                    ]);
        
                    if (!$apiResponse->successful()) {
                        Log::error('FieldProxy API Error: ' . $apiResponse->body());
                        return response()->json([
                            'draw' => intval($request->input('draw')),
                            'recordsTotal' => 0,
                            'recordsFiltered' => 0,
                            'data' => [],
                            'error' => 'Failed to fetch tickets from API.'
                        ], 500);
                    }
        
                    // $data = collect($apiResponse->json('data') ?? []);
                    
                    $apiTickets = collect($apiResponse->json('data') ?? []);


                    $mergedTickets = $apiTickets->filter(function ($apiTicket) use ($localTickets) {
                        return $localTickets->pluck('ticket_id')->contains($apiTicket['greendrive_ticketid']);
                    });
            
            
        
                    $totalRecords = $mergedTickets->count();
        
                    // Format data for DataTable
                    $formattedData = $mergedTickets->map(function ($item) {
                        $rawStatus = $item['ticket_status'] ?? null;
                        $normalizedStatus = strtolower($rawStatus);
                        $colorClass = match ($normalizedStatus) {
                            'pending'           => 'text-warning',      // yellow / needs attention
                            'assigned'          => 'text-primary',      // blue / assigned
                            'work_in_progress'  => 'text-info',         // light blue / in progress
                            'hold'              => 'text-secondary',    // grey / on hold
                            'closed'            => 'text-success',      // green / completed
                            default             => 'text-dark',         // fallback
                        };
                        
                        $displayStatus = match ($normalizedStatus) {
                            'pending'           => 'Pending',
                            'assigned'          => 'Assigned',
                            'work_in_progress'  => 'Work In Progress',
                            'hold'              => 'Hold',
                            'closed'            => 'Closed',
                            default             => ucfirst($normalizedStatus ?: 'Pending'),
                        };


                        // Format createdat in IST
                        $createdAt = '-';
                        if (!empty($item['createdat'])) {
                            try {
                                $createdAt = Carbon::parse($item['createdat'])
                                                   ->setTimezone('Asia/Kolkata')
                                                   ->format('d M Y h:i A');
                            } catch (\Exception $e) {
                                $createdAt = $item['createdat'] ?? '-';
                            }
                        }

        
                        $id_encode = encrypt($item['id']);
        
                        return [
                            'checkbox' => '<div class="form-check">
                                <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" type="checkbox" value="'.$item['id'].'">
                            </div>',
                            'ticket_id' => $item['greendrive_ticketid'] ?? '-',
                            'vehicle_type' => $item['vehicle_type'] ?? '-',
                            'vehicle_number' => $item['vehicle_number'] ?? '-',
                            'city' => $item['city'] ?? '-',
                            'createdat' => $createdAt ?? '-',
                            'status' => '<div class="d-flex align-items-center gap-2">
                                <i class="bi bi-circle-fill '.$colorClass.'"></i><span>'.$displayStatus.'</span>
                            </div>',
                            'action' => '<div class="dropdown">
                                <button type="button" class="btn btn-sm dropdown-toggle custom-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end text-center p-1">
                                    <li>
                                        <a href="'.route('admin.ticket_management.view',['id'=>$id_encode]).'" class="dropdown-item d-flex align-items-center justify-content-center">
                                            <i class="bi bi-eye me-2 fs-5"></i> View
                                        </a>
                                    </li>
                                </ul>
                            </div>'
                        ];
                    });
        
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => $totalRecords,
                        'recordsFiltered' => $totalRecords,
                        'data' => $formattedData
                    ]);
        
                } catch (\Exception $e) {
                    Log::error('Ticket List Error: ' . $e->getMessage());
                    return response()->json([
                        'draw' => intval($request->input('draw')),
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                        'error' => 'An error occurred while processing your request.'
                    ], 500);
                }
            }
        
            return view('vehicleserviceticket::ticket_list', compact('type'));
        }
    
    
    
    
     public function view_ticket(Request $request, $id)
        {
        try {
            //  Decrypt the ticket ID
            $ticket_id = decrypt($id);
            if (!$ticket_id) {
                return redirect()->back()->withErrors('Invalid ticket ID.');
            }
    
            //  Prepare API request
            $apiResponse = Http::withHeaders([
                'x-api-key' => env('FIELDPROXY_API_KEY')
            ])->post('https://api-india-1.fieldproxy.ai/api/read', [
                'tableName' => 'tickets',
                'condition' => [
                    'id' => $ticket_id
                ],
                'limit' => 1,
                'offset' => 0
            ]);
    
            //  Check API response
            if (!$apiResponse->successful()) {
                \Log::error('FieldProxy API Error (View Ticket): ' . $apiResponse->body());
                return redirect()->back()->withErrors('Failed to fetch ticket details.');
            }
    
            $datas = collect($apiResponse->json('data'))->first(); // Get single ticket data
    
            if (!$datas) {
                return redirect()->back()->withErrors('Ticket not found.');
            }
    
            // Pass data to the view
            return view('vehicleserviceticket::ticket_view', compact('datas'));
    
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            \Log::error('Decrypt Error (View Ticket): ' . $e->getMessage());
            return redirect()->back()->withErrors('Invalid ticket ID.');
        } catch (\Exception $e) {
            \Log::error('View Ticket Error: ' . $e->getMessage());
            return redirect()->back()->withErrors('An error occurred while fetching ticket details.');
        }
    }


        public function export_ticket(Request $request)
        {
            
            
            // Step 1: Validate request
            $validator = Validator::make($request->all(), [
                'type'   => 'required|string|in:all,pending,assigned,work_in_progress,hold,closed',
                'fields' => 'required|json', // JSON array of selected fields
            ]);
        
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Invalid request',
                    'messages' => $validator->errors()
                ], 422);
            }
        
        
            $timeline = $request->timeline;
            $form_date = $request->from_date;
            $to_date = $request->to_date;
            
            
            $type = $request->type;
            $selectedFields = json_decode($request->fields, true);
        
            if (empty($selectedFields) || !is_array($selectedFields)) {
                return response()->json(['error' => 'No fields selected'], 422);
            }
        
            $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
            // Step 2: Get local tickets
            $localTickets = VehicleTicket::query();
            
                    if (!empty($timeline)) {
                        $now = now();
                        switch ($timeline) {
                            case 'today':
                                $localTickets->whereDate('created_at', $now->toDateString());
                                break;
                            case 'this_week':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfWeek()->toDateTimeString(),
                                    $now->endOfWeek()->toDateTimeString()
                                ]);
                                break;
                            case 'this_month':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfMonth()->toDateTimeString(),
                                    $now->endOfMonth()->toDateTimeString()
                                ]);
                                break;
                            case 'this_year':
                                $localTickets->whereBetween('created_at', [
                                    $now->startOfYear()->toDateTimeString(),
                                    $now->endOfYear()->toDateTimeString()
                                ]);
                                break;
                        }
                    } elseif (!empty($from_date) || !empty($to_date)) {
                        // Date range filter
                        if (!empty($from_date)) {
                            $localTickets->where('created_at', '>=', Carbon::parse($from_date)->startOfDay());
                        }
                        if (!empty($to_date)) {
                            $localTickets->where('created_at', '<=', Carbon::parse($to_date)->endOfDay());
                        }
                    }
                    
             $localTickets = $localTickets->get();
             

        
            // Step 3: Fetch API tickets
            $apiResponse = Http::withHeaders([
                'x-api-key' => env('FIELDPROXY_API_KEY')
            ])->post('https://api-india-1.fieldproxy.ai/api/read', [
                'tableName' => 'tickets',
                'condition' => $type !== 'all' ? ['ticket_status' => $type] : [],
            ]);
        
            if (!$apiResponse->successful()) {
                Log::error('FieldProxy API Error: ' . $apiResponse->body());
                return response()->json([
                    'error' => 'Failed to fetch tickets from API',
                    'message' => $apiResponse->body()
                ], 500);
            }
        
            $apiTickets = collect($apiResponse->json('data') ?? []);
        
            // Step 4: Merge API tickets with local DB tickets
            $mergedTickets = $apiTickets->filter(function ($apiTicket) use ($localTickets) {
                return $localTickets->pluck('ticket_id')->contains($apiTicket['greendrive_ticketid']);
            })->values();
        
        
            // Step 5: Filter by selected IDs if provided
            if (!empty($selectedIds)) {
                $mergedTickets = $mergedTickets->filter(function ($ticket) use ($selectedIds) {
                    return in_array($ticket['id'], $selectedIds) || in_array($ticket['id'] ?? null, $selectedIds);
                })->values();
        
                if ($mergedTickets->isEmpty()) {
                    return response()->json(['error' => 'No matching tickets found for selected IDs'], 404);
                }
            }
        
            // Step 6: Return Excel download
            return Excel::download(
                new TicketExport($type, $selectedFields, $mergedTickets),
                'ticket-management-' . now()->format('d-m-Y') . '.xlsx'
            );
        }
    
    //  public function uploadFile($file, $directory)
    // {
    //     $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
    //     $file->move(public_path($directory), $imageName);
    //     return $imageName; // Return the name of the uploaded file
    // }
    
    
    
    


    public function updateStatus(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'greendrive_ticketid' => 'required|string',
            'ticket_status'       => 'nullable|string',
            'current_status'      => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }
    
        try {
            DB::beginTransaction();

        
            // Standard status mappings
            $ticket_statuses = [
                'unassigned' => 'Unassigned',
                'inprogress' => 'In Progress',
                'closed'     => 'Closed',
            ];
    
            $current_statuses = [
                'open'             => 'Open',
                'assigned'         => 'Assigned',
                'work_in_progress' => 'Work In Progress',
                'spare_requested'  => 'Spare Requested',
                'spare_approved'   => 'Spare Approved',
                'hold'             => 'Hold' ,
                'spare_collected'  => 'Spare Collected',
                'closed'           => 'Closed',
            ];
    
            $ticket_id          = $request->greendrive_ticketid;
            $new_ticket_status  = $request->ticket_status ? strtolower($request->ticket_status) : null;
            $new_current_status = $request->current_status ? strtolower($request->current_status) : null;
    
            $fieldproxy = FieldProxyTicket::where('greendrive_ticketid', $ticket_id)->first();
    
            if (!$fieldproxy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }
    
            // Get last log for comparison
            $lastLog = FieldProxyLog::where('fp_id', $fieldproxy->id)
                        ->latest('created_at')
                        ->first();
    
            $changes = [];
            $logData = [];
    
        // Ticket Status Change
        if ($new_ticket_status && (!$lastLog || $new_ticket_status !== strtolower($lastLog->status))) {
            $old = $lastLog ? $lastLog->status : (isset($fieldproxy->ticket_status) ? $fieldproxy->ticket_status : 'N/A');
            $fieldproxy->ticket_status = $new_ticket_status;
            $logData['ticket_status'] = $new_ticket_status;
            $changes[] = "Ticket Status changed from '" . (isset($ticket_statuses[strtolower($old)]) ? $ticket_statuses[strtolower($old)] : $old) . "' to '{$ticket_statuses[$new_ticket_status]}'";
        }
        
        // Current Status Change
        if ($new_current_status && (!$lastLog || $new_current_status !== strtolower($lastLog->current_status))) {
            $old = $lastLog ? $lastLog->current_status : (isset($fieldproxy->current_status) ? $fieldproxy->current_status : 'N/A');
            $fieldproxy->current_status = $new_current_status;
            $logData['current_status'] = $new_current_status;
            $changes[] = "Current Status changed from '" . (isset($current_statuses[strtolower($old)]) ? $current_statuses[strtolower($old)] : $old) . "' to '{$current_statuses[$new_current_status]}'";
        }


    
            // Save main FieldProxyTicket table if changed
            if (!empty($logData)) {
                $fieldproxy->save();
    
                // Create log for FieldProxy
                FieldProxyLog::create([
                    'fp_id'          => $fieldproxy->id,
                    'status'         => $fieldproxy->ticket_status,
                    'current_status' => $fieldproxy->current_status,
                    'remarks'        => "FieldProxy has updated: " . implode(" and ", $changes),
                    'type'           => 'fieldproxy',
                    'created_by'     => 'system-sync',
                    'changed_at'     => now(),
                ]);
            }
    
            // Update linked Service Request table if exists
            $service = B2BServiceRequest::where('ticket_id', $ticket_id)->first();
            if (!empty($service)) {
                $service_changes = [];
    
                if ($new_ticket_status && $service->status !== $new_ticket_status) {
                    $old_status = $ticket_statuses[$service->status] ?? $service->status ?? 'N/A';
                    $service->status = $new_ticket_status;
                    $service_changes[] = "Ticket Status changed from '{$old_status}' to '{$ticket_statuses[$new_ticket_status]}'";
                }
    
                if ($new_current_status && $service->current_status !== $new_current_status) {
                    $old_current = $current_statuses[$service->current_status] ?? $service->current_status ?? 'N/A';
                    $service->current_status = $new_current_status;
                    $service_changes[] = "Current Status changed from '{$old_current}' to '{$current_statuses[$new_current_status]}'";
                }
    
                if (!empty($service_changes)) {
                    $service->save();
                    
                    // Update assignment log if linked
                    $assignment = B2BVehicleAssignment::where('id', $service->assign_id)->first();
                    
                    if (isset($assignment) && !in_array($assignment->status, ['returned', 'return_request'])) {
                        
                        if($new_ticket_status == "closed"){
                             $assignment->status = "running";
                             $assignment->save();
                        }
                        
                    }
                    
                    if ($assignment) {
                        B2BVehicleAssignmentLog::create([
                            'assignment_id'     => $assignment->id,
                            'status'            => $service->status,
                            'current_status'    => $service->current_status,
                            'remarks'           => "FieldProxy updated: " . implode(" and ", $service_changes),
                            'type'              => 'fieldproxy',
                            'request_type'      => 'service_request',
                            'request_type_id'   => $service->id,
                        ]);
                    }
                }
            }


            
             if ($new_ticket_status === 'closed') {
                $vehicleNumber = $fieldproxy->vehicle_number ?? null;
                $chassis_number = $fieldproxy->chassis_number ?? null;
                
                if(!empty($chassis_number)){
                    
                $vehicle = AssetMasterVehicle::where('chassis_number', $chassis_number)->first();
            
                if ($vehicle) {
                    $inventory = AssetVehicleInventory::where('asset_vehicle_id', $vehicle->id)
                        ->where('asset_vehicle_status', 'accepted')
                        ->first();
            
                    if ($inventory) {
                         $from_transfer_status = $inventory->transfer_status;
                         
                         
                        //  SCENARIO CHECK
                        $activeAssignment = B2BVehicleAssignment::where('asset_vehicle_id', $vehicle->id)
                            // ->whereNotIn('status', ['returned', 'return_request'])
                            ->latest('created_at')
                            ->first();
            
                        if ($service) {
                            // ---- SCENARIO 1: Ticket belongs to a Service Request ----
                            if ($activeAssignment && in_array($activeAssignment->status, ['returned', 'return_request'])) {
                                // Vehicle was returned/requested → mark as RFD
                                $inventory->transfer_status = 3; // Ready for Deployment
                                $remarks = "Vehicle status changed to Ready for Deployment after service completion, due to FieldProxy ticket closure.";
                            } else {
                                // Vehicle still actively assigned → keep On Rent
                                $inventory->transfer_status = 1; // On Rent
                                $remarks = "Vehicle remains On Rent after service completion, due to FieldProxy ticket closure.";
                            }
                        } else {
                            // ---- SCENARIO 2: Ticket only exists in FieldProxy (no service request) ----
                            $inventory->transfer_status = 1; // On Rent
                            $remarks = "Vehicle remains On Rent after ticket closure, due to FieldProxy ticket closure.";
                        }
            
                        $inventory->save();
                        
                        $to_transfer_status = $inventory->transfer_status;
            
                        VehicleTransferChassisLog::create([
                            'chassis_number' => $vehicle->chassis_number,
                            'from_location_source'    => $from_transfer_status,   // previous transfer status
                            'to_location_destination' => $to_transfer_status,     // new transfer status
                            'vehicle_id'     => $vehicle->id,
                            'remarks'        => $remarks,
                            'status'           => 'updated', 
                            'type'           => 'fieldproxy'
                        ]);
                    }
                }
                }
            }


            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully',
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating the ticket',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vehicleserviceticket::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicleserviceticket::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('vehicleserviceticket::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('vehicleserviceticket::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
