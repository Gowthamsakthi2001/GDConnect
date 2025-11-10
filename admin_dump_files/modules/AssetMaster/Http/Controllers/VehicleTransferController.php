<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s
use App\Exports\VehicleTransferExport;

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;

use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\AssetMaster\Entities\VehicleTransfer;
use Modules\AssetMaster\Entities\VehicleTransferDetail;
use Modules\AssetMaster\Entities\AssetMasterVehicle; //updated by Gowtham.s
use Modules\AssetMaster\Entities\VehicleTransferLog;
use Modules\AssetMaster\Entities\VehicleTransferChassisLog; //updated by Mugesh.B

use Modules\AssetMaster\Entities\AssetVehicleInventory; 
use Modules\VehicleManagement\Entities\VehicleType;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\MasterManagement\Entities\InventoryLocationMaster;
use Modules\MasterManagement\Entities\VehicleTransferType;


class VehicleTransferController extends Controller
{

    public function vehicle_transfer_show(Request $request)
    {  
        Log::info("Current Page Vehicle Transfer Page ".now());
        $vehicle_types = VehicleType::where('is_active',1)->get();
        $customers = CustomerMaster::where('status',1)->get();
        $passed_chassis_nos = AssetVehicleInventory::leftJoin('ev_tbl_asset_master_vehicles as b', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'b.id')
                ->where('transfer_status',3)
                ->select('b.id', 'b.chassis_number')
                ->get();

        $vehicle_transfer_status = InventoryLocationMaster::where('status',1)->get();
        $deliverymans = Deliveryman::where('work_type','deliveryman')->where('delete_status',0)->whereNotNull('emp_id')->select('id','emp_id','first_name','last_name','work_type')->get();
        $transfer_types = VehicleTransferType::where('status',1)->get();
        $transfer_ids = VehicleTransfer::where('return_status',0)->get();
        return view('assetmaster::vehicle_transfer.vehicle_transfer_show',compact('customers','vehicle_types','transfer_types','passed_chassis_nos','vehicle_transfer_status','deliverymans','transfer_ids'));
    }
    
    
    public function vehicle_transfer_initiate_form(Request $request){

        $rules = [
            'transfer_type' => 'required|in:1,2,3',
            'transfer_date' => 'required|date',
            'from_location' => 'required',
            'to_location' => 'required',
            'select_chessis_number' => 'required|array',
        ];


        if ($request->transfer_type != 1) {
            $rules['customer_id'] = 'required|exists:ev_tbl_customer_master,id';
            $rules['customer_name'] = 'required';
        }
        
        if ($request->transfer_type == 3) {
            $rules['rider_id'] = 'required|array';
        }

    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        
       $chassisNumbers_Arr = $request->input('select_chessis_number', []);

        if (count($chassisNumbers_Arr) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one chassis number.',
            ]);
        }

        // dd($chassisNumbers_Arr,$request->get_chassis_numbers);
        
        $lastCode = DB::table('ev_tbl_vehicle_transfers')
            ->where('id', 'LIKE', 'GDMVT%')
            ->orderByRaw("CAST(SUBSTRING(id, 6) AS UNSIGNED) DESC")
            ->value('id');
        
        if ($lastCode && preg_match('/GDMVT(\d+)/', $lastCode, $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $ACode = 'GDMVT' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);  
        
        try {
            DB::beginTransaction();

            $transfer = VehicleTransfer::create([
                'id' => $ACode, 
                'transfer_type' => $request->transfer_type,
                'transfer_date' => $request->transfer_date,
                'custom_master_id' => $request->transfer_type != 1 ? $request->customer_id : null,
                'from_location_source' => $request->from_location,
                'to_location_destination' => $request->to_location,
                'remarks' => $request->remarks ?? null,
                'status' => 1,
                'created_by' => auth()->id() ?? null, 
            ]);
            $InfoErrors = [];
            $storedChassisnumbers = [];
            foreach ($request->select_chessis_number as $index => $chassisNumber) {
                if (!$chassisNumber) continue;
                     $inventory = AssetVehicleInventory::with(['assetVehicle'])->where('asset_vehicle_id',$chassisNumber)->first();
                    
                    $asset = AssetMasterVehicle::where('id',$chassisNumber)->first();
                    
                    if(!$inventory) {
                        $InfoErrors[$chassisNumber] = 'In this Chassis Number '.$asset->chassis_number.' Inventory not found';
                        continue;
                    }


                    VehicleTransferDetail::create([
                        'transfer_id' => $ACode,
                        'inventory_id' => $inventory->id ?? null, 
                        'vehicle_id' => $inventory->asset_vehicle_id ?? null, 
                        'chassis_number' => $inventory->assetVehicle->chassis_number ?? null,
                        'dm_id' => $request->transfer_type == 3 ? ($request->rider_id[$index] ?? null) : null,
                        'from_location_source' => $request->from_location,
                        'to_location_destination' => $request->to_location,
                        'initial_status'=> 1 
                    ]);
                    $storedChassisnumbers[] = $inventory->assetVehicle->chassis_number;
                    if($inventory){
                        
                        if (!empty($request->customer_id)) {
                            AssetMasterVehicle::where('id', $chassisNumber)
                                ->update(['client' => $request->customer_id]);
                                
                                
                        }
                        
    
                        $inventory->transfer_status = $request->to_location;
                        $inventory->save();
                    }
  
            }
            
            
            $getChassisNumbers = $request->get_chassis_numbers; 
            
            $remarks = "Transfer ID {$ACode} - ";
    
            if (!empty($storedChassisnumbers)) {
                $remarks .= "The following vehicle(s) have been transferred: " . implode(', ', $storedChassisnumbers) . ". ";
            }
            
            
            $chassis_remark = "";
            
            foreach ($request->select_chessis_number as $index => $chassisNumber) {
                if (!$chassisNumber) continue;
                
    
    
                $inventory_data = AssetVehicleInventory::with(['assetVehicle'])->where('asset_vehicle_id',$chassisNumber)->first();
                if($inventory_data){
                    $chassis_remark = "Transfer ID {$ACode} - Vehicle has been transferred: {$inventory_data->assetVehicle->chassis_number}. ";
                    VehicleTransferChassisLog::create([
                        'transfer_id' => $ACode,
                        'transfer_type' => $request->transfer_type,
                        'transfer_date' => $request->transfer_date,
                        'vehicle_id' => $inventory_data->asset_vehicle_id ?? null,
                        'chassis_number' => $inventory_data->assetVehicle->chassis_number ?? null,
                        'dm_id' => $request->transfer_type == 3 ? ($request->rider_id[$index] ?? null) : null,
                        'from_location_source' => $request->from_location,
                        'to_location_destination' => $request->to_location,
                        'status' => 'initial',
                        'created_by' => auth()->id() ?? null, 
                        'remarks'=>$chassis_remark
                    ]);
                }
                 
            }
            
            
            VehicleTransferLog::create([
                    'transfer_id' => $ACode,
                    'transfer_type' => $request->transfer_type,
                    'transfer_date' => $request->transfer_date ?? null,
                    'chassis_numbers' => implode(', ', $storedChassisnumbers) ?? null,
                    'from_location_source' => $request->from_location,
                    'to_location_destination' => $request->to_location,
                    'is_status' => 'initial',
                    'created_by' => auth()->id() ?? null, 
                    'remarks'=>$remarks
                ]);
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Vehicle transfer stored successfully. Your Transfer ID '.$ACode,
                'info_errors' =>$InfoErrors,
                'transfer_id'=>$ACode
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }

    }
    
        
    public function vehicle_transfer_return_form(Request $request){

        $rules = [
            'transfer_id' => 'required',
            'to_location' => 'required',
            'detail_ids' => 'required',
            'return_transfer_date'=>'required|date',
            'return_remarks'=>'nullable'
        ];

    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $transfer_details_ids = explode(',', $request->detail_ids);
        
        if(count($transfer_details_ids) < 1){
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one chassis number.',
            ]);
        }

        
         try {
            DB::beginTransaction();
    
            $vehicle_transfer = VehicleTransfer::where('id',$request->transfer_id)->first();
            // dd($vehicle_transfer);
            
            foreach ($transfer_details_ids as $index => $detail_id) {
                if (!$detail_id) continue;
                $detail = VehicleTransferDetail::where('id',$detail_id)->first();
                $inventory = AssetVehicleInventory::where('id',$detail->inventory_id)->first();
                if($detail){
                    $detail->return_location = $request->to_location;
                    $detail->return_status = 1 ;
                    $detail->return_remarks = $request->return_remarks ?? null;
                    $detail->return_transfer_date = $request->return_transfer_date ?? null;
                    $detail->save();
                }
                if($inventory){
                    $inventory->transfer_status = $request->to_location;
                    $inventory->save();
                }
            }
    
            $getChassisNumbers = $request->get_chassis_numbers; 
            $pendingChassisNumbers = $request->pending_chassis_numbers;
            
            $remarks = "Transfer ID {$request->transfer_id} - ";
            $return_type = "";
            if (!empty($getChassisNumbers) && empty($pendingChassisNumbers)) {
                $remarks .= "All listed vehicle(s) have been successfully returned: {$getChassisNumbers}.";
                $return_type = "Full Returned";
            } else {
                $return_type = "Partial Returned";
                if (!empty($getChassisNumbers)) {
                    $remarks .= "The following vehicle(s) have been returned: {$getChassisNumbers}. ";
                }
            
                if (!empty($pendingChassisNumbers)) {
                    $remarks .= "The following vehicle(s) are still running: {$pendingChassisNumbers}.";
                }
            }
            // dd($getChassisNumbers,$pendingChassisNumbers,$remarks,$return_type);
            // foreach ($transfer_details_ids as $index => $detail_id) {
            //     if (!$detail_id) continue;
            //     $detail = VehicleTransferDetail::where('id',$detail_id)->first();
            
           $chassisNumbers = explode(',', $request->get_chassis_numbers ?? '');

            $base_remark = "";
            
            foreach ($chassisNumbers as $index => $chassisNumber) {
                if (!$chassisNumber) continue;

             $vehicle =  AssetMasterVehicle::where('chassis_number', $chassisNumber)->first();

                VehicleTransferChassisLog::create([
                    'transfer_id' => $vehicle_transfer->id,
                    'transfer_type' => $vehicle_transfer->transfer_type,
                    'transfer_date' => $request->return_transfer_date,
                    'vehicle_id' =>$vehicle->id ?? null,
                    'chassis_number' => $chassisNumber ?? null,
                    'from_location_source' => $vehicle_transfer->to_location_destination,
                    'to_location_destination' => $request->to_location,
                    'status' => 'returned',
                    'created_by' => auth()->id(),
                      'remarks' => "Transfer ID {$request->transfer_id} - Vehicle has been returned : {$chassisNumber}."
                ]);
              }
              
              // 2. Pending Vehicles Loop
            $pendingChassis = explode(',', $request->pending_chassis_numbers ?? '');
            
            foreach ($pendingChassis as $chassisNumber) {
                if (!$chassisNumber) continue;
            
                $vehicle = AssetMasterVehicle::where('chassis_number', $chassisNumber)->first();
            
                VehicleTransferChassisLog::create([
                    'transfer_id' => $vehicle_transfer->id,
                    'transfer_type' => $vehicle_transfer->transfer_type,
                    'transfer_date' => $request->return_transfer_date,
                    'vehicle_id' => $vehicle->id ?? null,
                    'chassis_number' => $chassisNumber,
                    'from_location_source' => $vehicle_transfer->to_location_destination,
                    'to_location_destination' => $request->to_location,
                    'status' => 'running',
                    'created_by' => auth()->id(),
                    'remarks' => "Transfer ID {$request->transfer_id} - Vehicle is still running: {$chassisNumber}."
                ]);
            }
           



                VehicleTransferLog::create([
                    'transfer_id' => $vehicle_transfer->id,
                    'transfer_type' => $vehicle_transfer->transfer_type,
                    'transfer_date' => $request->return_transfer_date ?? null,
                    'chassis_numbers' => $getChassisNumbers ?? null,
                    'from_location_source' => $vehicle_transfer->to_location_destination,
                    'to_location_destination' => $request->to_location,
                    'is_status' => 'returned',
                    'created_by' => auth()->id() ?? null, 
                    'remarks'=>$remarks
                ]);
            // }
            
            
            $return_vehicle_count = VehicleTransferDetail::where('initial_status',1)->where('return_status',0)->count();
            
            $total_vehicles = VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->count();
            $return_vehicles = VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->where('initial_status',1)->where('return_status',1)->count();
            
            if($total_vehicles == $return_vehicles){
                $vehicle_transfer->return_status = 1;
                $vehicle_transfer->save();
            }
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Vehicle Return transfer successfully',
                'return_type' => $return_type,
                'remarks'=>$remarks,
                 'redirect_url' => route('admin.asset_management.vehicle_transfer.log_preview', [
                        'transfer_id' => $vehicle_transfer->id
                    ])
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ], 500);
        }

        
    }
    
    public function get_chassis_details(Request $request){
         
        $vehicle = AssetMasterVehicle::where('id',$request->vehicle_id)->first();
    
        if (!$vehicle) {
            return response()->json(['success' => false,'message' => 'Vehicle Not Found']);
        }
        
        $vehicle_type = $vehicle->vehicle_type_relation->name ?? '';
        $vehicle_model = $vehicle->vehicle_model_relation->vehicle_model ?? '';

        return response()->json([
            'success' => true,
            'message' => 'Vehicle Data fetched successfully!',
            'vehicle_type'=>$vehicle_type,
            'vehicle_model'=>$vehicle_model,
            'data' => $vehicle
        ]);
    
     }
     
     public function get_rider_details(Request $request){

        $dm = Deliveryman::where('id',$request->dm_id)->where('delete_status',0)->first();
    
        if (!$dm) {
            return response()->json(['success' => false,'message' => 'Deliveryman Not Found']);
        }
        
        $dm_name = $dm->first_name.' '.$dm->last_name  ?? '';

        return response()->json([
            'success' => true,
            'message' => 'Deliveryman Data fetched successfully!',
            'dm_name'=>$dm_name,
        ]);
    
     }
     
    public function get_bulk_details(Request $request)
    {
        $request->validate([
            'transfer_type' => 'required|in:1,2,3',
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);
    
        $data = Excel::toArray([], $request->file('excel_file'));
    
        $rows = $data[0]; 
    
        $results = [];
        $warningArr = [];
        $item = [];
 
        foreach ($rows as $index => $row) {
            if ($index === 0) continue;
        
            $chassis_number = $row[0] ?? null;
            $rider_id = $row[1] ?? null;
        
            if (!$chassis_number) continue;
        
            $vehicle = AssetVehicleInventory::with([
                    'assetVehicle',
                    'assetVehicle.vehicle_model_relation',
                    'assetVehicle.vehicle_type_relation',
                ])
                ->whereHas('assetVehicle', function($query) use($chassis_number){
                    $query->where('chassis_number', $chassis_number)->where('delete_status',0);
                })
                ->where('transfer_status', 3)
                ->first();
        
            if ($vehicle) {
                $item = [
                    'vehicle_id' => $vehicle->asset_vehicle_id,
                    'chassis_number' => $vehicle->assetVehicle->chassis_number,
                    'vehicle_type' => $vehicle->assetVehicle->vehicle_type_relation->name ?? '',
                    'vehicle_model' => $vehicle->assetVehicle->vehicle_model_relation->vehicle_model ?? '',
                ];
        
                if ($request->transfer_type == "3" && $rider_id) {
                    $dm = Deliveryman::where('emp_id', $rider_id)->where('delete_status', 0)->first();
                    if ($dm) {
                        $item['rider_id'] = $dm->id ?? '';
                        $item['emp_id'] = $dm->emp_id ?? '';
                        $item['rider_name'] = trim($dm->first_name . ' ' . $dm->last_name) ?? '';
                    }
                }
        
                // ✅ only push when item exists
                $results[] = $item;
        
            } else {
                $warningArr[] = $chassis_number;
            }
        }

        
        $warnings_message = '';
        if (!empty($warningArr)) {
            $warnings_message = 'The chassis numbers ' . implode(', ', $warningArr) . ' do not exist in inventory, and were not added.';
        }
                
        return response()->json([
            'success' => true,
            'message' => 'Vehicle Data fetched successfully!',
            'transfer_type'=>$request->transfer_type,
            'data' => $results,
            'warnings_message'=>$warnings_message
        ]);
    }
    
    public function return_transfer_vehicle_view(Request $request){
        $vehicle_transfer = VehicleTransfer::where('id',$request->transfer_id)->where('return_status',0)->first();
        
        if(!$vehicle_transfer){
           return back()->with('error','Vehicle Transfer Not Found'); 
        }
        $vehicle_types = VehicleType::where('is_active',1)->get();
        $customers = CustomerMaster::where('status',1)->get();
        $passed_chassis_nos = AssetVehicleInventory::leftJoin('ev_tbl_asset_master_vehicles as b', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'b.id')
                ->select('b.id', 'b.chassis_number')
                ->get();
        $vehicle_transfer_status = InventoryLocationMaster::where('status',1)->get();
        $deliverymans = Deliveryman::where('work_type','deliveryman')->where('delete_status',0)->whereNotNull('emp_id')->select('id','emp_id','first_name','last_name','work_type')->get();
        $transfer_types = VehicleTransferType::where('status',1)->get();
        
        
        return view('assetmaster::vehicle_transfer.return_transfer_view',compact('vehicle_transfer','customers','vehicle_types','transfer_types','passed_chassis_nos','vehicle_transfer_status','deliverymans'));
    }
    
    
    public function log_preview(Request $request){
        
        $vehicle_transfer = VehicleTransfer::where('id',$request->transfer_id)->first();
        if(!$vehicle_transfer){
           return back()->with('error','Vehicle Transfer Not Found'); 
        }
        
        $vehicle_types = VehicleType::where('is_active',1)->get();
        $vehicle_transfer_status = InventoryLocationMaster::where('status',1)->get();
        $transfer_types = VehicleTransferType::where('status',1)->get();

        return view('assetmaster::vehicle_transfer.vehicle_transfer_log_preview',compact('vehicle_transfer','vehicle_types','transfer_types','vehicle_transfer_status'));
    }
    
    //  public function log_and_history_view(Request $request){
         
    //     $vehicle_transfers = VehicleTransfer::orderBy('id','desc')->get();
        
        
    //     $status = $request->status ?? 'all';
    //     $timeline = $request->timeline ?? '';
    //     $from_date = $request->from_date ?? '';
    //     $to_date = $request->to_date ?? '';
    //     $chassis_number = $request->chassis_number ?? '';

    //     $query = VehicleTransfer::with('transfer_details');
    //     if (!empty($status) && $status != "all") {
    //         $is_status =  $status == "closed" ? 1 : 0;
    //         $query->where('return_status', $is_status);
    //     }
        
        
    //             // Chassis number filter (from related transfer_details table)
    //     if (!empty($chassis_number)) {
    //         $query->whereHas('transfer_details', function ($q) use ($chassis_number) {
    //             $q->where('chassis_number', $chassis_number);
    //         });
    //     }


    //     if ($timeline) {
    //         switch ($timeline) {
    //             case 'today':
    //                 $query->whereDate('created_at', today());
    //                 break;
        
    //             case 'this_week':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfWeek(), now()->endOfWeek()
    //                 ]);
    //                 break;
        
    //             case 'this_month':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfMonth(), now()->endOfMonth()
    //                 ]);
    //                 break;
        
    //             case 'this_year':
    //                 $query->whereBetween('created_at', [
    //                     now()->startOfYear(), now()->endOfYear()
    //                 ]);
    //                 break;
    //         }
        
    //         $from_date = null;
    //         $to_date = null;
    //     } else {
    //         if ($from_date) {
    //             $query->whereDate('created_at', '>=', $from_date);
    //         }
        
    //         if ($to_date) {
    //             $query->whereDate('created_at', '<=', $to_date);
    //         }
    //     }
        
    //     // dd($query->toSql());

    //     $lists =  $query->orderBy('id', 'desc')->get();
        
    //   $passed_chassis_nos = AssetVehicleInventory::leftJoin('ev_tbl_asset_master_vehicles as b', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'b.id')
    //             ->select('b.id', 'b.chassis_number')
    //             ->get();
    
    //     return view('assetmaster::vehicle_transfer.vehicle_transfer_log', compact('lists','status','from_date','to_date','timeline' ,'passed_chassis_nos' ,'chassis_number'));
    // }
    
    public function log_and_history_view(Request $request)
{
    $status = $request->status ?? 'all';
    $timeline = $request->timeline ?? '';
    $from_date = $request->from_date ?? '';
    $to_date = $request->to_date ?? '';
    $chassis_number = $request->chassis_number ?? '';
    $searchValue = $request->search['value'] ?? ''; // DataTables search value
    
    // Eager load with necessary relationships
    $query = VehicleTransfer::with([
        'transferType',
        'transfer_details' => function($q) {
            $q->select('id', 'transfer_id', 'initial_status', 'return_status', 'return_transfer_date');
        }
    ]);
    
    // Apply search filter if provided
    if (!empty($searchValue)) {
        $query->where(function($q) use ($searchValue) {
            $q->where('id', 'like', '%' . $searchValue . '%')
              ->orWhereHas('transferType', function($q) use ($searchValue) {
                  $q->where('name', 'like', '%' . $searchValue . '%');
              })
              ->orWhereHas('transfer_details', function($q) use ($searchValue) {
                  $q->where('chassis_number', 'like', '%' . $searchValue . '%');
              });
        });
    }
    
    if (!empty($status) && $status != "all") {
        $is_status = $status == "closed" ? 1 : 0;
        $query->where('return_status', $is_status);
    }
    
    if (!empty($chassis_number)) {
        $query->whereHas('transfer_details', function ($q) use ($chassis_number) {
            $q->where('chassis_number', $chassis_number);
        });
    }
    
    // Use transfer_date for filtering if that's what you display
    $dateField = 'created_at';
    
    if ($timeline) {
        switch ($timeline) {
            case 'today':
                $query->whereDate($dateField, today());
                break;
            case 'this_week':
                $query->whereBetween($dateField, [
                    now()->startOfWeek(), now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereBetween($dateField, [
                    now()->startOfMonth(), now()->endOfMonth()
                ]);
                break;
            case 'this_year':
                $query->whereBetween($dateField, [
                    now()->startOfYear(), now()->endOfYear()
                ]);
                break;
        }
    } else {
        if ($from_date) {
            $query->whereDate($dateField, '>=', $from_date);
        }
        
        if ($to_date) {
            $query->whereDate($dateField, '<=', $to_date);
        }
    }
    
    // Get total records count before pagination
    $recordsTotal = VehicleTransfer::count();
   
    $recordsFiltered = $query->count();
    
    // Apply ordering
    $orderColumn = $request->order[0]['column'] ?? 1;
    $orderDirection = $request->order[0]['dir'] ?? 'desc';
    
    $orderColumns = [
        1 => 'id',
        2 => 'transfer_type_id', // Assuming this is the relationship column
        3 => 'transfer_date',
        // Add more columns as needed
    ];
    
    if (isset($orderColumns[$orderColumn])) {
        $query->orderBy($orderColumns[$orderColumn], $orderDirection);
    } else {
        $query->orderBy('id', 'desc');
    }
    
    // Apply pagination
    if ($request->has('start') && $request->has('length')) {
        $query->skip($request->start)->take($request->length);
    }
    
    $lists = $query->get();
    
    // If AJAX request from DataTables
    if ($request->ajax() && $request->has('draw')) {
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $lists->map(function ($val) {
                // Use eager loaded relationships
                $total_vehicles = $val->transfer_details->count();
                $return_vehicles = $val->transfer_details->where('initial_status', 1)->where('return_status', 1)->count();
                $running_vehicles = $val->transfer_details->where('initial_status', 1)->where('return_status', 0)->count();
                $last_vehicle = $val->transfer_details->where('initial_status', 1)->where('return_status', 1)->sortByDesc('id')->first();

                // Status logic
                $statusHtml = '';
                if ($val->return_status == 1) {
                    $statusHtml = '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Closed';
                } elseif ($val->return_status == 0) {
                    $statusHtml = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Active';
                } else {
                    $statusHtml = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> -';
                }

                // Action buttons
                $actionsHtml = '<div class="d-flex gap-2">';
                if ($val->return_status == 0) {
                    $returnUrl = route('admin.asset_management.vehicle_transfer.return_vehicle_view', ['transfer_id' => $val->id]);
                    $actionsHtml .= '<a href="' . $returnUrl . '" title="Return" class="dropdown-item d-flex align-items-center justify-content-center">';
                    $actionsHtml .= '<i class="bi bi-arrow-left-right me-2 fs-5"></i></a>';
                }
                $viewUrl = route('admin.asset_management.vehicle_transfer.log_preview', ['transfer_id' => $val->id]);
                $actionsHtml .= '<a href="' . $viewUrl . '" title="View" class="dropdown-item d-flex align-items-center justify-content-center">';
                $actionsHtml .= '<i class="bi bi-eye me-2 fs-5"></i></a>';
                $actionsHtml .= '</div>';

                return [
                    'checkbox' => '<div class="form-check"><input class="form-check-input sr_checkbox" style="width:25px; height:25px;" name="is_select[]" type="checkbox" value="' . $val->id . '"></div>',
                    'id' => $val->id,
                    'transfer_type' => $val->transferType->name ?? '',
                    'total_vehicles' => $total_vehicles,
                    'return_vehicles' => $return_vehicles,
                    'running_vehicles' => $running_vehicles,
                    'transfer_date' => '<div>' . ($val->transfer_date ? \Carbon\Carbon::parse($val->transfer_date)->format('d M Y') : '') . '</div>',
                    'return_date' => '<div>' . (($last_vehicle && $last_vehicle->return_transfer_date) 
                        ? \Carbon\Carbon::parse($last_vehicle->return_transfer_date)->format('d M Y') 
                        : '-') . '</div>',
                    'status' => $statusHtml,
                    'action' => $actionsHtml
                ];
            })->values()
        ]);
    }
    
    // For initial page load
    $passed_chassis_nos = AssetVehicleInventory::leftJoin('ev_tbl_asset_master_vehicles as b', 'asset_vehicle_inventories.asset_vehicle_id', '=', 'b.id')
        ->select('b.id', 'b.chassis_number')
        ->get();
    
    return view('assetmaster::vehicle_transfer.vehicle_transfer_log', compact('lists', 'status', 'from_date', 'to_date', 'timeline', 'passed_chassis_nos', 'chassis_number','recordsTotal'));
}
    
    public function export_detail(Request $request){
       
        
        $status = $request->status ?? 'all';
        $timeline = $request->timeline ?? '';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
        $get_ids = $request->get('get_ids', []);
        $get_labels = array_filter($request->get('get_export_labels', []), function ($label) {
            return !is_null($label) && trim($label) !== '';
        });
        
        $chassis_number = $request->chassis_number ?? '';

 
        $export = new VehicleTransferExport(
            $request->status,
            $request->from_date,
            $request->to_date,
            $request->timeline,
            $request->get_export_labels ??[] ,
            $request->get_ids ?? [] ,
            $chassis_number
            
        );
        return Excel::download($export, 'Vehicle_Transfers-'.date('d-m-Y').'.xlsx');
    }
    
    public function getInterTransferTable($id)
    {
        $vehicle_transfer = VehicleTransfer::with([
            'transfer_details.asset_vehicle.vehicle_type_relation',
            'transfer_details.asset_vehicle.vehicle_model_relation',
            'transfer_details.deliveryman'
        ])->findOrFail($id);
    
        $transferType = $vehicle_transfer->transfer_type;
        $transfer_vehicles = $vehicle_transfer->transfer_details ?? [];
        $Table_body = '';
    
        if ($transferType == 1 || $transferType == 2) {
            if (isset($transfer_vehicles) && count($transfer_vehicles) > 0) {
                $Table_body .= '
                    <tr>
                        <th colspan="4" class="bg-light text-center fw-medium" style="padding:15px;">Vehicles Pending for Return</th>
                    </tr>
                ';
    
                foreach ($transfer_vehicles as $transfer) {
                    if ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                        $Table_body .= '
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input sr_checkbox" style="width:25px; height:25px;" 
                                               name="is_select[]" type="checkbox" 
                                               data-chassis_number="' . ($transfer->asset_vehicle->chassis_number ?? '') . '" 
                                               value="' . $transfer->id . '" checked>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="select_chessis_number[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->chassis_number ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_type[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_model[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '" readonly>
                                </td>
                            </tr>
                        ';
                    }
                }
    
                $hasReturnedVehicles = false;
                $sno = 1;
                $total_vehicles = 0;
    
                $Table_body .= '
                    <tr>
                        <th colspan="4" class="bg-light text-center fw-medium" style="padding:15px;">Returned vehicles</th>
                    </tr>
                ';
    
                foreach ($transfer_vehicles as $transfer) {
                    if ($transfer->return_status == 1) {
                        $hasReturnedVehicles = true;
                        $total_vehicles++;
    
                        $Table_body .= '
                            <tr>
                                <td class="text-center">' . $sno++ . '</td>
                                <td>
                                    <input type="text" class="form-control" name="select_chessis_number[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->chassis_number ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_type[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_model[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '" readonly>
                                </td>
                            </tr>
                        ';
                    }
                }
    
                if (!$hasReturnedVehicles) {
                    $Table_body .= '
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding:15px;">
                                No vehicles have been returned yet 🏍️
                            </td>
                        </tr>
                    ';
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'table_body' => $Table_body
        ]);
    }
    
    public function getInterTransferTablelist($id)
    {
        $vehicle_transfer = VehicleTransfer::with([
            'transfer_details.asset_vehicle.vehicle_type_relation',
            'transfer_details.asset_vehicle.vehicle_model_relation',
            'transfer_details.deliveryman'
        ])->findOrFail($id);
        
        $total_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->count();
        $return_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)
                        ->where('initial_status',1)->where('return_status',1)->count();
        $transferType = $vehicle_transfer->transfer_type;
        $transfer_vehicles = $vehicle_transfer->transfer_details ?? [];
        $Table_body = '';
        $sno = 1;
        if ($transferType == 1 || $transferType == 2) {
            if (!empty($transfer_vehicles)) {
                foreach ($transfer_vehicles as $transfer) {

                        if ($total_vehicles == $return_vehicles) {
                            $status = '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Closed';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 1) {
                            $status = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Active';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                            $status = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Active';
                        } else {
                            $status = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> -';
                        }

                        if ($total_vehicles == $return_vehicles) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#12ae3a;"></i> Return';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 1) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Return';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Running';
                        } else {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> -';
                        }

                        $Table_body .= '
                            <tr>
                                <td class="text-center">
                                    <div>' . ($sno++) . '</div>
                                </td>
                                <td class="text-center">
                                    <div>' . ($transfer->asset_vehicle->chassis_number ?? '') . '</div>
                                </td>
                                <td>
                                    <div>' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '</div>
                                </td>
                                <td>
                                    <div>' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '</div>
                                </td>
                                <td>' . $status . '</td>
                                <td>' . $returnCol . '</td>
                            </tr>
                        ';
                    
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'table_body' => $Table_body
        ]);
    }
    
    
     public function getLogRiderTransferTablelist($id)
    {
        $vehicle_transfer = VehicleTransfer::with([
            'transfer_details.asset_vehicle.vehicle_type_relation',
            'transfer_details.asset_vehicle.vehicle_model_relation',
            'transfer_details.deliveryman'
        ])->findOrFail($id);
        $total_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)->count();
        $return_vehicles = \Modules\AssetMaster\Entities\VehicleTransferDetail::where('transfer_id',$vehicle_transfer->id)
                        ->where('initial_status',1)->where('return_status',1)->count();
        $transferType = $vehicle_transfer->transfer_type;
        $transfer_vehicles = $vehicle_transfer->transfer_details ?? [];
        $Table_body = '';
        $sno = 1;
        if ($transferType == 3) {
            if (!empty($transfer_vehicles)) {
                foreach ($transfer_vehicles as $transfer) {

                        if ($total_vehicles == $return_vehicles) {
                            $status = '<i class="bi bi-circle-fill" style="color:#ff2c2c;"></i> Closed';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 1) {
                            $status = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Active';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                            $status = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Active';
                        } else {
                            $status = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> -';
                        }

                        if ($total_vehicles == $return_vehicles) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#12ae3a;"></i> Return';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 1) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#72cf72;"></i> Return';
                        } elseif ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> Running';
                        } else {
                            $returnCol = '<i class="bi bi-circle-fill" style="color:#ffd52c;"></i> -';
                        }

                        $Table_body .= '
                            <tr>
                                <td class="text-center">
                                    <div>' . ($sno++) . '</div>
                                </td>
                                <td class="text-center">
                                    <div>' . ($transfer->asset_vehicle->chassis_number ?? '') . '</div>
                                </td>
                                <td>
                                    <div>' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '</div>
                                </td>
                                <td>
                                    <div>' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '</div>
                                </td>
                                <td>
                                    <div>' . ($transfer->deliveryman->emp_id ?? 'N/A') . '</div>
                                </td>
                                <td>
                                    <div>' . ( $transfer->deliveryman ? $transfer->deliveryman->first_name . ' ' . $transfer->deliveryman->last_name : 'N/A' ) . '</div>
                                </td>
                                <td>' . $status . '</td>
                                <td>' . $returnCol . '</td>
                            </tr>
                        ';
                    
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'table_body' => $Table_body,
        ]);
    }

    
    public function getRiderTransferTable($id)
    {
        $vehicle_transfer = VehicleTransfer::with([
            'transfer_details.asset_vehicle.vehicle_type_relation',
            'transfer_details.asset_vehicle.vehicle_model_relation',
            'transfer_details.deliveryman'
        ])->findOrFail($id);
    
        $transferType = $vehicle_transfer->transfer_type;
        $transfer_vehicles = $vehicle_transfer->transfer_details ?? [];
        $Table_body = '';
    
        if ($transferType == 3) {
            if (isset($transfer_vehicles) && count($transfer_vehicles) > 0) {
                $Table_body .= '
                    <tr>
                        <th colspan="6" class="bg-light text-center fw-medium" style="padding:15px;">Vehicles Pending for Return</th>
                    </tr>
                ';
    
                foreach ($transfer_vehicles as $transfer) {
                    if ($transfer->initial_status == 1 && $transfer->return_status == 0) {
                        $Table_body .= '
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input sr_checkbox sr_checkbox_custom" style="width:25px; height:25px;" 
                                               name="is_select[]" type="checkbox" 
                                               data-chassis_number="' . ($transfer->asset_vehicle->chassis_number ?? '') . '" 
                                               value="' . $transfer->id . '" checked>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="select_chessis_number[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->chassis_number ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_type[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_model[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="deliveryman_id[]" placeholder="Auto filled" value="'.($transfer->deliveryman->emp_id ?? 'N/A') . '" readonly>
                                </td>
                                <td> 
                                    <input type="text" class="form-control" 
                                    name="rider_name[]" 
                                    placeholder="Auto filled" 
                                    value="'.( $transfer->deliveryman ? $transfer->deliveryman->first_name . ' ' . $transfer->deliveryman->last_name : 'N/A' ).'" 
                                    readonly>
                                </td>
                            </tr>
                        ';
                    }
                }
    
                $hasReturnedVehicles = false;
                $sno = 1;
                $total_vehicles = 0;
    
                $Table_body .= '
                    <tr>
                        <th colspan="6" class="bg-light text-center fw-medium" style="padding:15px;">Returned vehicles</th>
                    </tr>
                ';
    
                foreach ($transfer_vehicles as $transfer) {
                    if ($transfer->return_status == 1) {
                        $hasReturnedVehicles = true;
                        $total_vehicles++;
    
                        $Table_body .= '
                            <tr>
                                <td class="text-center">' . $sno++ . '</td>
                                <td>
                                    <input type="text" class="form-control" name="select_chessis_number[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->chassis_number ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_type[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_type_relation->name ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="vehicle_model[]" 
                                           placeholder="Auto filled" 
                                           value="' . ($transfer->asset_vehicle->vehicle_model_relation->vehicle_model ?? 'N/A') . '" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="deliveryman_id[]" placeholder="Auto filled" value="'.($transfer->deliveryman->emp_id ?? 'N/A') . '" readonly>
                                </td>
                                <td> 
                                    <input type="text" class="form-control" 
                                    name="rider_name[]" 
                                    placeholder="Auto filled" 
                                    value="'.( $transfer->deliveryman ? $transfer->deliveryman->first_name . ' ' . $transfer->deliveryman->last_name : 'N/A' ).'" 
                                    readonly>
                                </td>
                            </tr>
                        ';
                    }
                }
    
                if (!$hasReturnedVehicles) {
                    $Table_body .= '
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding:15px;">
                                No vehicles have been returned yet 🏍️
                            </td>
                        </tr>
                    ';
                }
            }
        }
    
        return response()->json([
            'status' => true,
            'rider_table_body' => $Table_body
        ]);
    }

}