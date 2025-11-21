<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Modules\AssetMaster\Entities\AssetMasterVehicle; 
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\QualityCheckMaster;
use Modules\AssetMaster\Entities\QualityCheck; 
use Modules\AssetMaster\Entities\QualityCheckReinitiate;
use Modules\VehicleManagement\Entities\VehicleType;//updated by Mugesh.B
use Modules\VehicleManagement\Entities\VehicleModelMaster;//updated by Mugesh.B
use Modules\AssetMaster\Entities\LocationMaster; //updated by Mugesh.B

class AssetManagementContoller extends Controller
{
    
     public function qc_lists(Request $request,$dm_id)
    {
        
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pass,fail,qc_pending',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'timeline' => 'nullable|in:today,this_week,this_month,this_year',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }


    try {

        

        // ✅ 2. Build base query
        $query = QualityCheck::with('location_relation');
    
        // ✅ 3. Apply filters
        $status = $request->query('status', 'all');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $timeline = $request->query('timeline');
        
        $search_name = $request->query('search_name');
    
        if (!empty($status)) {
            if (in_array($status, ['pass', 'fail' ,'qc_pending'])) {
                $query->where('status', $status);
            }
        }
        
        //  $query->whereIn('status', ['pass', 'fail']);
        
        

    // if (!empty($search_name)) {
    //     $query->where(function ($q) use ($search_name) {
    //         $q->where('chassis_number', 'like', $search_name . '%')
    //           ->orWhere('id', 'like', $search_name . '%')
    //           ->orWhere('battery_number', 'like', $search_name . '%')
    //           ->orWhere('motor_number', 'like', $search_name . '%');
    //     });
    // }
    
            if (!empty($search_name)) {
            $query->where(function ($q) use ($search_name) {
                $q->where('chassis_number', 'like', '%' . $search_name . '%')
                  ->orWhere('id', 'like', '%' . $search_name . '%')
                  ->orWhere('battery_number', 'like', '%' . $search_name . '%')
                  ->orWhere('motor_number', 'like', '%' . $search_name . '%');
            });
        }




    
        if ($timeline) {
            switch ($timeline) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
                case 'this_year':
                    $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                    break;
            }
            
            $from_date = null;
            $to_date = null;
            
        } else {
           if ($from_date) {
                $query->where('created_at', '>=', $from_date);
            }
            
            if ($to_date) {
                $query->where('created_at', '<=', $to_date);
            }
            
        }
    
        // ✅ 4. Paginate results
        $datas = $query->orderBy('id', 'desc')->paginate(10);
   
    
        // ✅ 5. Format for mobile response
        $formatted = $datas->map(function ($item) {
            return [
                'qc_id' => $item->id,
                'datetime' => date('d M Y h:i A' , strtotime($item->datetime)) ?? null,
                'location_name' => optional($item->location_relation)->name,
                'chassis_number' => $item->chassis_number ?? null ,
                'status' => $item->status,
            ];
        });
        
        
  
    
        return response()->json([
            'success' => true,
            'message' => 'Quality checks retrieved successfully.',
            'data' => $formatted,
            'pagination' => [
                'current_page' => $datas->currentPage(),
                'last_page' => $datas->lastPage(),
                'total' => $datas->total(),
            ]
        ], 200);
        
        
    } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
     
         
    }
    
    public function getCheckBoxLists(Request $request, $vehicleType)
    {
        $checklists = QualityCheckMaster::where('status', 1)->where('vehicle_type_id', $vehicleType)->get()->map(function ($box) {
                return [
                    'id' => $box->id,
                    'label_name' => $box->label_name,
                    'created_at' => Carbon::parse($box->created_at)->format('d-m-Y H:i:s'),
                    'updated_at' => Carbon::parse($box->updated_at)->format('d-m-Y H:i:s'),
                ];
            });
        return response()->json([
            'success' => true,
            'message' => $checklists->isNotEmpty() ? 'Checkboxes fetched successfully.' : 'No checkboxes found.',
            'data' => $checklists,
        ], 200);
    }
    
    
    // public function assetmanager(Request $request,$id){
    //     try {
    //         $AssetMasterVehicle = AssetMasterVehicle::where('dm_id',$id)->get(); 
    //         $AssetMasterCharger = AssetMasterCharger::where('dm_id',$id)->get();
    //         $AssetMasterBattery = AssetMasterBattery::where('dm_id',$id)->get();
    //         $AssetInsuranceDetails = AssetInsuranceDetails::where('dm_id',$id)->get();
            
            
    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'insurance' => $AssetInsuranceDetails,
    //                 'vehicles' => $AssetMasterVehicle,
    //                 'chargers' => $AssetMasterCharger,
    //                 'batteries' => $AssetMasterBattery
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to retrieve data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function assetmanager(Request $request, $Chassis_Serial_No)
    {
        
        try {
            // Fetch data from the database
            $AssetMasterVehicle = AssetMasterVehicle::where('Chassis_Serial_No', $Chassis_Serial_No)->get(); 
            $AssetMasterCharger = AssetMasterCharger::where('Chassis_Serial_No', $Chassis_Serial_No)->get();
            $AssetMasterBattery = AssetMasterBattery::where('Chassis_Serial_No', $Chassis_Serial_No)->get();
            $AssetInsuranceDetails = AssetInsuranceDetails::where('Chassis_Serial_No', $Chassis_Serial_No)->get();
            // dd($AssetInsuranceDetails);
            // exit;
            
            // Validation: Check if any of the datasets are empty
            if ($AssetMasterVehicle->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No vehicles found for the given Chassis Serial No.'
                ], 404);
            }
    
            if ($AssetMasterCharger->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No chargers found for the given Chassis Serial No.'
                ], 404);
            }
    
            if ($AssetMasterBattery->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No batteries found for the given Chassis Serial No.'
                ], 404);
            }
    
            if ($AssetInsuranceDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No insurance details found for the given Chassis Serial No.'
                ], 404);
            }
    
            // Optional: Check for invalid Chassis_Serial_No in vehicles
            foreach ($AssetMasterVehicle as $vehicle) {
                if (empty($vehicle->Chassis_Serial_No)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vehicle not assigned. Missing Chassis Serial Number.'
                    ], 400);
                }
            }
    
            // If everything is fine, return the data
            return response()->json([
                'success' => true,
                'data' => [
                    'insurance' => $AssetInsuranceDetails,
                    'vehicles' => $AssetMasterVehicle,
                    'chargers' => $AssetMasterCharger,
                    'batteries' => $AssetMasterBattery
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
     public function get_vehicle_types(Request $request){
         
             try {
                $vehicle_types = VehicleType::where('is_active', 1)
                 ->select('id', 'name', 'created_at', 'updated_at')
                ->get();
        
                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle types fetched successfully.',
                    'data' => $vehicle_types
                ], 200);
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch vehicle types.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
         
         
         
     }
     
          public function get_vehicle_models(Request $request){
        
         
             try {
                $vehicle_models = DB::table('ev_tbl_vehicle_models')
                 ->select('id', 'vehicle_model', 'created_at', 'updated_at')
                ->where('status',1)->get();
        
        
                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle models fetched successfully.',
                    'data' => $vehicle_models
                ], 200);
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch vehicle models.',
                    'error' => $e->getMessage()
                ], 500);
            }
        
         
     }
    
    
    public function get_location_data(Request $request){
        
         
             try {
                    $location_values = LocationMaster::where('status', 1)
                     ->select('id', 'name', 'created_at', 'updated_at')
                    ->get();
        
        
                return response()->json([
                    'success' => true,
                    'message' => 'Location fetched successfully.',
                    'data' => $location_values
                ], 200);
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch location.',
                    'error' => $e->getMessage()
                ], 500);
            }
        
         
     }
     
     
        public function view_quality_check(Request $request, $id)
        {
            try {
                // Fetch the QualityCheck record
              $data = QualityCheck::with('technician:id,name,profile_photo_path','delivery_man:id,first_name,last_name,photo' ,'vehicle_type_relation:id,name' ,'vehicle_model_relation:id,vehicle_model' ,'location_relation:id,city_name')->where('id', $id)->first(); // ✅ Correct

        
                // If no record found, return 404
                if (!$data) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quality check not found.',
                    ], 404);
                }
                
             

                
                
             $logs = QualityCheckReinitiate::with(
                'technician_reinitiator:id,name,profile_photo_path',
                'deliveryman_relation:id,first_name,last_name,photo'
            )
                ->where('qc_id', $id)
                ->where('status', '!=', 'qc_pending')
                ->orderBy('id', 'desc')
                ->get();

        
        
                    
                    
                    
                    // ✅ Technician or Delivery Man Logic
                    $technician = $data->getRelation('technician');
                    $dm = $data->getRelation('delivery_man');
                    
                   if ($technician) {
                        $technicianData = [
                            'id' => $technician->id ?? null,
                            'name' => $technician->name ?? null,
                            'profile_photo_url' => $technician->profile_photo_path
                                ? asset('uploads/users/' . $technician->profile_photo_path)
                                : asset('public/admin-assets/img/person.png'),
                        ];
                    } elseif ($dm) {
                        $technicianData = [
                            'id' => $dm->id ?? null,
                            'name' => trim(($dm->first_name ?? '') . ' ' . ($dm->last_name ?? '')) ?: null,
                            'profile_photo_url' => $dm->photo
                                ? asset('EV/images/photos/' . $dm->photo)
                                : asset('public/admin-assets/img/person.png'),
                        ];
                    } else {
                        $technicianData = [
                            'id' => null,
                            'name' => null,
                            'profile_photo_url' => null,
                        ];
                    }
            
                                // First decode
                    $checklistData = json_decode($data->check_lists, true);
                    
                    // If still a string (double encoded), decode again
                    if (is_string($checklistData)) {
                        $checklistData = json_decode($checklistData, true);
                    }
                    
                    if (!is_array($checklistData)) {
                        $checklistData = [];
                    }

                    $checklistFormatted = [];
            
                    if (!empty($checklistData)) {
                        $checklistNames = DB::table('ev_tbl_qc_list_master')
                            ->whereIn('id', array_keys($checklistData))
                            ->pluck('label_name', 'id')
                            ->toArray();
            
                        foreach ($checklistData as $id => $status) {
                            $checklistFormatted[] = [
                                'name' => $checklistNames[$id] ?? 'Unknown',
                                'status' => $status
                            ];
                        }
                    }



                // ✅ Prepare logs with technician/deliveryman photo & name
                  $formattedLogs = $logs->map(function ($log) {
                      
                $technician = $log->getRelation('technician_reinitiator');
                $dm = $log->getRelation('deliveryman_relation');
    
                if ($technician) {
                    $reinitiator = [
                        'id' => $technician->id ?? null,
                        'name' => $technician->name ?? null,
                    ];
                } elseif ($dm) {
                    $reinitiator = [
                        'id' => $dm->id ?? null,
                        'name' => trim(($dm->first_name ?? '') . ' ' . ($dm->last_name ?? '')) ?: null,
                    ];
                } else {
                    $reinitiator = [
                        'id' => null,
                        'name' => null,
                    ];
                }
    
                return [
                    'id' => $log->id,
                    'qc_id'=>$log->qc_id,
                    'remarks' => $log->remarks,
                    'status' => $log->status,
                    'created_at' => date('d M Y h:i A' , strtotime($log->created_at)) ?? null,
                    'updated_at' => date('d M Y h:i A' , strtotime($log->updated_at)) ?? null,
                    'reinitiator' => $reinitiator,
                ];
            });


                // Return successful response
                return response()->json([
                    'success' => true,
                    'message' => 'Quality check data retrieved successfully.',
                    'data' => [
                    'id' => $data->id ?? null,
                    'technician' => $technicianData ?? null,
                    'vehicle_type' => $data->vehicle_type ?? null,
                    'vehicle_model' => $data->vehicle_model ?? null,
                    'location' => $data->location_relation->city_name ?? "Location",//updated by mugesh
                    'chassis_number' => $data->chassis_number ?? null,
                    'battery_number' => $data->battery_number ?? null,
                    'telematics_number' => $data->telematics_number ?? null,
                    'motor_number' => $data->motor_number ?? null,
                    'check_lists' => $checklistFormatted,
                    'datetime' => date('d M Y h:i A' , strtotime($data->datetime)) ?? null,
                    'status' => $data->status ?? null,
                    'image' => $data->image ? 'https://evms.greendrivemobility.com/EV/images/quality_check/' . $data->image : null,
                    'remarks' => $data->remarks ?? null,
                    'created_at' => $data->created_at ?? null,
                    'updated_at' => $data->updated_at ?? null,
                    'vehicle_type_relation' => $data->vehicle_type_relation,
                    'vehicle_model_relation' => $data->vehicle_model_relation,
                    'location_relation' => $data->location_relation,
                ],
                    'logs' => $formattedLogs,
                ], 200);
        
        
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch quality check.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }

    
      public function create(Request $request){
          
          
        //   Log::info("Api called Create 123" .json_encode($request->all()));
            $checklists = QualityCheckMaster::where('status', 1)
                ->where('vehicle_type_id', $request->vehicle_type)
                ->get();
            
            $rules = [
                'vehicle_type'       => 'required',
                'vehicle_model'      => 'required',
                'location'           => 'required',
                'chassis_number'     => 'required|unique:vehicle_qc_check_lists,chassis_number',
                'battery_number'     => 'required',
                'telematics_number'  => 'required|unique:vehicle_qc_check_lists,telematics_number',
                'motor_number'       => 'required',
                'result'             => 'required',
                'remarks'            => 'max:255',
                'file'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:1024'//1MB Accept
            ];
            
            if ($checklists->count() > 0) {
                $rules['qc'] = 'required';
            }
            
            $messages = [
                'qc.required' => 'QC Checklist is required.',
            ];
            
            $validator = Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors'  => $validator->errors()
                ], 422);
            }

        
        
        
             DB::beginTransaction(); // ✅ Start transaction

               try {
                
                
                 $imageName = null; // Declare it here to use later
                  if ($request->hasFile('file')) {
                        $imageName  = $this->uploadFile($request->file('file'), 'EV/images/quality_check');
                    }
            
                    $lastCode = QualityCheck::orderBy('id', 'desc')->value('id');
                
                    if ($lastCode && preg_match('/QC(\d+)/', $lastCode, $matches)) {
                        $lastNumber = (int)$matches[1];
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1001; // Start from QC1001
                    }
                
                    // ✅ Generate new QC code
                    $qcCode = 'QC' . $newNumber;
                    
                    
                    
                    
                    
                
                    QualityCheck::create([
                        'id' => $qcCode,
                        'vehicle_type' => $request->vehicle_type ?? '',
                        'vehicle_model' => $request->vehicle_model,
                        'location' => $request->location ?? '',
                        'chassis_number' => $request->chassis_number ?? '',
                        'battery_number' => $request->battery_number ?? '',
                        'telematics_number' => $request->telematics_number ?? '',
                        'motor_number' => $request->motor_number ?? '',
                        'datetime' =>  now(),
                        'status' => $request->result ?? '',
                        'remarks' => $request->remarks ?? '',
                        'dm_id' => $request->user_id ?? '' ,
                         'check_lists' => json_encode($request->qc ?? []),
                         'image' => $imageName, 
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    
                    if($request->result == 'pass'){
                        
                         $lastCode = DB::table('ev_tbl_asset_master_vehicles')
                        ->where('id', 'LIKE', 'VH%')
                        ->orderByRaw("CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC")
                        ->value('id');
                    
                    if ($lastCode && preg_match('/VH(\d+)/', $lastCode, $matches)) {
                        $lastNumber = (int)$matches[1];
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1001;
                    }
                    
                    $ACode = 'VH' . $newNumber;
                    
                    
                         AssetMasterVehicle::create([
                        'id'=>$ACode ,
                        'qc_id' => $qcCode,
                        'chassis_number' => $request->chassis_number ,
                         'vehicle_type'=> $request->vehicle_type ?? '' ,
                        'motor_number' => $request->motor_number ?? '' ,
                        'location' => $request->location ?? '',
                        'battery_serial_no'=> $request->battery_number ?? '',
                        'telematics_serial_no' => $request->telematics_number ?? '' ,
                        'model' => $request->vehicle_model ?? '' ,
                        'qc_status' => 'pass',
                        'is_status' => 'pending',
                       
                    ]);
                    
                    }
        
        
        
        
                   QualityCheckReinitiate::insert([
                        'qc_id'=>$qcCode ?? '' ,
                        'status' => $request->result ?? '',
                        'remarks' => $request->remarks ?? '',
                        'dm_id' => $request->user_id ?? '' ,
                        'created_at'=>now() ,
                        'updated_at' => now()
                    ]);
        
                    
                  DB::commit(); // ✅ Commit transaction
        
               return response()->json([
                    'success' => true,
                    'message' => 'Quality check created successfully.',
                    'qc_id' => $qcCode
                ], 200);

        
            } catch (\Exception $e) {
                DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage() // (optional: only for debugging)
            ], 500);
            }
                    
        
        
     
        }
        
        
        
        public function reinitiate_quality_check(Request $request){
            
          $id = $request->qc_id;
          
          
            $checklists = QualityCheckMaster::where('status', 1)
                ->where('vehicle_type_id', $request->vehicle_type)
                ->get();
            
            $rules = [
                'vehicle_type'       => 'required',
                'vehicle_model'      => 'required',
                'location'           => 'required',
                'chassis_number'     => 'required|unique:vehicle_qc_check_lists,chassis_number,' . $id . ',id',
                'battery_number'     => 'required',
                'telematics_number'  => 'required|unique:vehicle_qc_check_lists,telematics_number,' . $id . ',id',
                'motor_number'       => 'required',
                'result'             => 'required',
                'remarks'            => 'max:255',
                'file'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',//1MB Accept
                'qc_id' => 'required'
                
            ];
            
            // Conditionally make 'qc' required
            if ($checklists->count() > 0) {
                $rules['qc'] = 'required';
            }
            
            $validator = Validator::make($request->all(), $rules, [
                'qc.required' => 'QC Checklist is required.',
            ]);

            
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // dd($request->all());
            
            
            
              DB::beginTransaction(); // ✅ Start transaction

      try {
           $existing = QualityCheck::find($id);
           
           
        $changes = [];

        // Compare and track changes
        if ($existing->vehicle_type != $request->vehicle_type) {
            $changes[] = 'Vehicle Type';
        }
        if ($existing->vehicle_model != $request->vehicle_model) {
            $changes[] = 'Vehicle Model';
        }
        if ($existing->location != $request->location) {
            $changes[] = 'Location';
        }
        if ($existing->chassis_number != $request->chassis_number) {
            $changes[] = 'Chassis Number';
        }
        if ($existing->battery_number != $request->battery_number) {
            $changes[] = 'Battery Number';
        }
        if ($existing->telematics_number != $request->telematics_number) {
            $changes[] = 'Telematics Number';
        }
        if ($existing->motor_number != $request->motor_number) {
            $changes[] = 'Motor Number';
        }
        // if ($existing->datetime != $request->datetime) {
        //     $changes[] = 'Datetime';
        // }
        if ($existing->status != $request->result) {
            $changes[] = 'Status';
        }
        
        
        
        
        $oldChecklist = json_decode($existing->check_lists ?? '[]', true);
        $newChecklist = $request->qc ?? [];


        $oldDecoded = is_string($oldChecklist) ? json_decode($oldChecklist, true) : $oldChecklist;
        $newDecoded = is_string($newChecklist) ? json_decode($newChecklist, true) : $newChecklist;
        
        if ($oldDecoded !== $newDecoded) {
            $changes[] = 'Checklist';
        }
        
            
            $imageName = null;
            if ($request->hasFile('file')) {
                
                $changes[] = 'Image';
                if ($existing && $existing->image) {
                    $oldPath = public_path('EV/images/quality_check/' . $existing->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath); // ❌ Delete previous file
                    }
                }
                
            
            
                $imageName = $this->uploadFile($request->file('file'), 'EV/images/quality_check');
            }
    

            $userRemark = trim($request->remarks ?? '');
            $changeRemark = $changes ? 'Updated Fields: ' . implode(', ', $changes) : 'No major field updates.';
            $finalRemark = $userRemark
                ? "1) $userRemark\n2) $changeRemark"
                : $changeRemark;
                

          QualityCheckReinitiate::insert([
                'qc_id'=>$request->qc_id ?? '' ,
                'status' => $request->result ?? '',
                'remarks' => $finalRemark ?? '',
                'dm_id' => $request->user_id ?? '' ,
                'created_at'=>now() ,
                'updated_at' => now()
            ]);
            
            
            
        
                $updateData = [
                'vehicle_type'       => $request->vehicle_type ?? '',
                'vehicle_model'      => $request->vehicle_model ?? '',
                'location'           => $request->location ?? '',
                'chassis_number'     => $request->chassis_number ?? '',
                'battery_number'     => $request->battery_number ?? '',
                'telematics_number'  => $request->telematics_number ?? '',
                'motor_number'       => $request->motor_number ?? '',
                'datetime'           => now(),
                'status'             => $request->result ?? '',
                'remarks'            => $request->remarks ?? '',
                'check_lists' => json_encode($request->qc ?? []),
                'updated_at'         => now()
            ];
            
            
    
            

            // Only add image field if a file is uploaded
            if ($imageName) {
                $updateData['image'] = $imageName;
            }
            
             QualityCheck::where('id', $request->qc_id)->update($updateData);


            if($request->result == 'pass'){
                
                 $lastCode = DB::table('ev_tbl_asset_master_vehicles')
                        ->where('id', 'LIKE', 'VH%')
                        ->orderByRaw("CAST(SUBSTRING(id, 3) AS UNSIGNED) DESC")
                        ->value('id');
                    
                    if ($lastCode && preg_match('/VH(\d+)/', $lastCode, $matches)) {
                        $lastNumber = (int)$matches[1];
                        $newNumber = $lastNumber + 1;
                    } else {
                        $newNumber = 1001;
                    }
                    
                    $Acode = 'VH' . $newNumber;
                   
                    
                    
                        
                        
                     AssetMasterVehicle::create([
                        'id'=>$Acode ,
                        'qc_id' => $id,
                         'chassis_number' => $request->chassis_number ,
                         'vehicle_type'=> $request->vehicle_type ?? '' ,
                        'motor_number' => $request->motor_number ?? '' ,
                        'location' => $request->location ?? '',
                        'battery_serial_no'=> $request->battery_number ?? '',
                        'telematics_serial_no' => $request->telematics_number ?? '' ,
                        'model' => $request->vehicle_model ?? '' ,
                        'qc_status' => 'pass',
                        'is_status' => 'pending',
                       
                    ]);
            }
            
            
            
        DB::commit(); // ✅ Commit transaction

               return response()->json([
                    'success' => true,
                    'message' => 'Quality check reinitiated successfully.',
                    'qc_id' => $id
                ], 200);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage() // (optional: only for debugging)
            ], 500);
        }
            
     
            
            
        }
    
        public function uploadFile($file, $directory)
    {
        $imageName = Str::uuid(). '.' . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $imageName);
        return $imageName; // Return the name of the uploaded file
    }
    
}