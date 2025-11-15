<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Illuminate\Support\Facades\Auth; //updated by Logesh
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
       
         $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
         $validator = Validator::make($request->all(), [
            'vehicle_model' => 'required|string|max:255',
            'vehicle_type' => 'required',
            'brand_model' => 'required'
        ]);
        
        if ($validator->fails()) {
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Vehicle Model Create Failed (Validation)',
                'long_description'  => 'Validation errors: ' . implode(', ', $validator->errors()->all()),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'vehicle_model_master.store',
                'ip_address'        => $request->ip(),
                'user_device'       => $request->userAgent()
            ]);
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
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Vehicle Model Create Failed (Duplicate)',
                    'long_description'  => 'Attempted to create duplicate vehicle model: ' . $request->vehicle_model,
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'vehicle_model_master.store',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
                ]);
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
            
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Vehicle Model Created',
                    'long_description'  => sprintf(
                        'Vehicle model "%s" created (Brand: %s, Type: %s, Make: %s, Variant: %s, Color: %s).',
                        $request->vehicle_model ?? '-',
                        $request->brand_model ?? '-',
                        $request->vehicle_type ?? '-',
                        $request->make ?? '-',
                        $request->variant ?? '-',
                        $request->color ?? '-'
                    ),
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'vehicle_model_master.store',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent()
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
        
        $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';

            // ✅ Audit: export triggered
            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Vehicle Model Master Exported',
                'long_description'  => sprintf(
                    'Vehicle Model Master export triggered. Filters -> Status: %s, From: %s, To: %s, Selected IDs: %d',
                    $status ?: 'all',
                    $from_date ?: '-',
                    $to_date ?: '-',
                    is_array($selectedIds) ? count($selectedIds) : 0
                ),
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'vehicle_model_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent(),
            ]);
        
          return Excel::download(new VehicleModelMasterExport($status,$from_date,$to_date, $selectedIds), 'vehicle-model-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
    
    
                public function update_data(Request $request)
        {
             $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
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
            
            $old = DB::table('ev_tbl_vehicle_models')->where('id', $vehicleId)->first();
            $old = $old ? (array) $old : [];
        
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
                
            $new = [
                'vehicle_model' => $request->vehicle_model ?? '',
                'vehicle_type'  => $request->vehicle_type ?? '',
                'brand'         => $request->brand_model ?? '',
                'make'          => $request->make ?? '',
                'variant'       => $request->variant ?? '',
                'color'         => $request->color ?? '',
            ];
            $changes = [];

            foreach ($new as $field => $newValue) {
                $oldValue = $old[$field] ?? null;
            
                if ((string)$oldValue !== (string)$newValue) {
                    $changes[$field] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
            if (!empty($changes)) {
                $changeTextList = [];
            
                foreach ($changes as $field => $values) {
                    $old = $values['old'] === null || $values['old'] === '' ? '-' : $values['old'];
                    $new = $values['new'] === null || $values['new'] === '' ? '-' : $values['new'];
            
                    $changeTextList[] = "{$field}: {$old} → {$new}";
                }
            
                $longDescription = "Vehicle Model (ID: {$vehicleId}) updated. Changes: " . implode("; ", $changeTextList) . ".";
            } else {
                $longDescription = "Vehicle Model (ID: {$vehicleId}) updated. No fields changed.";
            }


            audit_log_after_commit([
                'module_id'         => 4,
                'short_description' => 'Vehicle Model Updated',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => Auth::id(),
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'vehicle_model_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent(),
            ]);
            return redirect()->route('admin.asset_management.vehicle_model_master.list')
                ->with('success', 'Vehicle model updated successfully.');
        }
        
             public function update_status(Request $request)
        {
            $roleName = optional(\Modules\Role\Entities\Role::find(optional(Auth::user())->role))->name ?? 'Unknown';
            
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required'
            ]);
            
         
            $updated = DB::table('ev_tbl_vehicle_models')
                ->where('id', $request->id)
                ->update(['status' => $request->status]);
                
            $vehicleModel = DB::table('ev_tbl_vehicle_models')
                ->where('id', $request->id)->first();
            if ($updated) {
                $statusText = $request->status == 1 ? "Active" : "Inactive";

                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Vehicle Model Status Updated',
                    'long_description'  => "Vehicle Model '{$vehicleModel->vehicle_model}' (ID: {$request->id}) status changed to {$statusText}.",
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'vehicle_model_master.update_status',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent(),
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully.'
                ]);
            } else {
                audit_log_after_commit([
                    'module_id'         => 4,
                    'short_description' => 'Vehicle Model Status Update Failed (Not Found)',
                    'long_description'  => 'Attempted to update status for missing Model ID: ' . $request->id,
                    'role'              => $roleName,
                    'user_id'           => Auth::id(),
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'vehicle_model_master.update_status',
                    'ip_address'        => $request->ip(),
                    'user_device'       => $request->userAgent(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status.'
                ]);
            }
        }
    
    
   
    
    
}
