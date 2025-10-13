<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetMasterVehicleImport; //updated by Gowtham.s

use Modules\AssetMaster\Entities\AmsLocationMaster; 
use Modules\AssetMaster\Entities\AssetInsuranceDetails;
use Modules\AssetMaster\Entities\AssetMasterBattery;
use Modules\AssetMaster\Entities\AssetMasterCharger;
use Modules\AssetMaster\Entities\AssetMasterVehicle;
use Modules\AssetMaster\Entities\ManufacturerMaster;
use Modules\AssetMaster\Entities\ModalMasterVechile;
use Modules\AssetMaster\Entities\ModelMasterBattery;
use Modules\AssetMaster\Entities\ModelMasterCharger;
use Modules\AssetMaster\Entities\AssetStatus;//updated by Gowtham.s
use Modules\AssetMaster\Entities\PoTable;
use Modules\Deliveryman\Entities\Deliveryman;
use App\Exports\VehicleModelMasterExport;//updated by Mugesh.B
use Modules\VehicleManagement\Entities\VehicleType;//updated by Mugesh.B

//dataTable
use Modules\AssetMaster\DataTables\ModalMasterVechileDataTable;
use Modules\AssetMaster\DataTables\ModalMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\ModalMasterChargerDataTable;
use Modules\AssetMaster\DataTables\ManufactureMasterDataTable;
use Modules\AssetMaster\DataTables\PotableDataTable;
use Modules\AssetMaster\DataTables\AmsLocationMasterDataTable;
use Modules\AssetMaster\DataTables\AssetInsuranceDataTable;
use Modules\AssetMaster\DataTables\AssetMasterBatteryDataTable;
use Modules\AssetMaster\DataTables\AssetMasterChargerDataTable;
use Modules\AssetMaster\DataTables\AssetMasterVechileDataTables;
use Modules\AssetMaster\DataTables\AssetStatusDataTable;

class VehicleModelMasterController extends Controller
{



   public function vehicle_model_mater_list(Request $request){
       
     $status = $request->status ?? 'all';
    $from_date = $request->from_date ?? '';
    $to_date = $request->to_date ?? '';

    $query = DB::table('ev_tbl_vehicle_models')
        ->join('ev_tbl_brands', 'ev_tbl_vehicle_models.brand', '=', 'ev_tbl_brands.id')
        ->select('ev_tbl_vehicle_models.*', 'ev_tbl_brands.brand_name');

    // Apply status filter if it's 1 or 0
    if (in_array($status, ['1', '0'])) {
        $query->where('ev_tbl_vehicle_models.status', $status);
    }

    // Apply date filters
    if (!empty($from_date)) {
        $query->whereDate('ev_tbl_vehicle_models.created_at', '>=', $from_date);
    }

    if (!empty($to_date)) {
        $query->whereDate('ev_tbl_vehicle_models.created_at', '<=', $to_date);
    }

    $vehicles = $query->orderBy('ev_tbl_vehicle_models.id', 'desc')->get();



        return view('assetmaster::vehicle_model_master.vehicle_model_master_list' , compact('vehicles' , 'status' , 'to_date' , 'from_date' ));
    }
    
    
    public function create_vehicle_model_master(Request $request){
        $brands = DB::table('ev_tbl_brands')->where('status', 1)->get();
        $vehicle_types = VehicleType::where('is_active', 1)->get();
        
    
        return view('assetmaster::vehicle_model_master.create_vehicle_model_master' , compact('brands' , 'vehicle_types'));
    }
   
    
      public function update_vehicle_model_master(Request $request , $id){
          
          $vehicle = DB::table('ev_tbl_vehicle_models')->where('id', $id)->first();
          $brands = DB::table('ev_tbl_brands')->where('status', 1)->get();
          $vehicle_types = VehicleType::where('is_active', 1)->get();
 
        return view('assetmaster::vehicle_model_master.update_vehicle_model_master' , compact('vehicle' , 'brands'  , 'vehicle_types'));
    }
    
        public function store(Request $request){
       
         $validator = Validator::make($request->all(), [
            'vehicle_model' => 'required|string|max:255',
            'vehicle_type' => 'required',
            'brand_model' => 'required'
        ]);
        
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
        
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $existingVehicleModel = DB::table('ev_tbl_vehicle_models')
        ->where('vehicle_model', $request->vehicle_model)
        ->first();

            if ($existingVehicleModel) {
                return redirect()->back()->withErrors([
                    'vehicle_model' => 'This vehicle model already exists.'
                ])->withInput();
            }
        
        
        
            // Insert the new brand
            DB::table('ev_tbl_vehicle_models')->insert([
                'vehicle_model' => $request->vehicle_model ?? '',
                'brand' => $request->brand_model ?? '',
                'vehicle_type' => $request->vehicle_type ?? '',
                'make' => $request->make ?? '',
                'variant' => $request->variant ?? '',
                'color' => $request->color ?? '',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            
        
            // return redirect()->route('admin.asset_management.vehicle_model_master.list')->with('success', 'Vehicle model created successfully.');
          if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Vehicle model created successfully.'
        ]);
    }

    // Otherwise normal redirect
    return redirect()->route('admin.asset_management.vehicle_model_master.list')
                     ->with('success', 'Vehicle model created successfully.');
        
    }
    
    
       public function export_vehicle_model_master(Request $request)
    {
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
       
        
          return Excel::download(new VehicleModelMasterExport($status,$from_date,$to_date, $selectedIds), 'vehicle-model-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
    
    
                public function update_data(Request $request)
        {
            
            $request->validate([
            'vehicle_model' => 'required|string|max:255' , 
            'vehicle_type' => 'required' ,
            'brand_model' => 'required'
            ]);
        
            $vehicleId = $request->vehicle_id;
        
           
            $existingVehiclemodel = DB::table('ev_tbl_vehicle_models')
                ->where('vehicle_model', $request->vehicle_model)
                ->where('id', '!=', $vehicleId)
                ->first();
                
        
            if ($existingVehiclemodel) {
                return redirect()->back()->withErrors([
                    'vehicle_model' => 'This vehicle model already exists.'
                ])->withInput();
            }
        
        
        
            DB::table('ev_tbl_vehicle_models')
                ->where('id', $vehicleId)
                ->update([
                    'vehicle_model' => $request->vehicle_model ?? '',
                    'vehicle_type'=> $request->vehicle_type ?? '',
                    'brand'=> $request->brand_model ?? '',
                    'make' => $request->make ?? '',
                    'variant' => $request->variant ?? '',
                    'color' => $request->color ?? '',
                    'updated_at' => now()
                ]);
        
        
            return redirect()->route('admin.asset_management.vehicle_model_master.list')
                ->with('success', 'Vehicle model updated successfully.');
        }
        
             public function update_status(Request $request)
        {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required'
            ]);
            
          
            
        
            $updated = DB::table('ev_tbl_vehicle_models')
                ->where('id', $request->id)
                ->update(['status' => $request->status]);
        
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status.'
                ]);
            }
        }
    
    
   
    
    
}
