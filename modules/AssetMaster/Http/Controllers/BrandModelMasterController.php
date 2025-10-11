<?php

namespace Modules\AssetMaster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Illuminate\Support\Str;
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
use App\Exports\BrandModelMasterExport;//updated by Mugesh.B

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

class BrandModelMasterController extends Controller
{



   public function brand_model_mater_list(Request $request){
       
        $status = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        // Start query
        $query = DB::table('ev_tbl_brands');
    
        // Filter by status if 0 or 1
        if (in_array($status, ['0', '1'])) {
            $query->where('status', $status);
        }
    
        // Filter by date range
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        // Get results
        
        $brands = $query->orderBy('id', 'desc')->get();
        
        
        
        return view('assetmaster::brand_model_master.brand_model_master_list' , compact('brands', 'from_date', 'to_date' , 'status'));
    }
    
   
   public function export_brand_model_master(Request $request)
    {
        
        
        $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        
          return Excel::download(new BrandModelMasterExport($status ,$from_date,$to_date , $selectedIds), 'brand-model-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
    
       public function create_brand_model_master(Request $request){
       
        return view('assetmaster::brand_model_master.create_brand_model_master');
    }
    
     public function update_brand_model_master(Request $request , $id){
       
        $brand = DB::table('ev_tbl_brands')->where('id', $id)->first();
        return view('assetmaster::brand_model_master.edit_brand_model_master' , compact('brand'));
    }
    
    
        public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'brand_model' => 'required|string|max:255',
            ]);
        
            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
        
                return redirect()->back()->withErrors($validator)->withInput();
            }
        
            $existingBrand = DB::table('ev_tbl_brands')
                ->where('brand_name', $request->brand_model)
                ->first();
        
            if ($existingBrand) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This brand model already exists.'
                    ], 409);
                }
        
                return redirect()->back()->withErrors([
                    'brand_model' => 'This brand model already exists.'
                ])->withInput();
            }
        
            DB::table('ev_tbl_brands')->insert([
                'brand_name' => $request->brand_model,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brand model created successfully.'
                ]);
            }
        
            return redirect()->route('admin.asset_management.brand_model_master.list')
                ->with('success', 'Brand model created successfully.');
        }

    
            public function update_data(Request $request)
        {
            $request->validate([
                'brand_model' => 'required|string|max:255'
            ]);
        
            $brandId = $request->brand_id;
        
            // Check if the brand name already exists (excluding current record)
            $existingBrand = DB::table('ev_tbl_brands')
                ->where('brand_name', $request->brand_model)
                ->where('id', '!=', $brandId)
                ->first();
                
        
            if ($existingBrand) {
                return redirect()->back()->withErrors([
                    'brand_model' => 'This brand model already exists.'
                ])->withInput();
            }
        
            // Update the brand name
            DB::table('ev_tbl_brands')
                ->where('id', $brandId)
                ->update([
                    'brand_name' => $request->brand_model,
                    'updated_at' => now()
                ]);
        
            return redirect()->route('admin.asset_management.brand_model_master.list')
                ->with('success', 'Brand model updated successfully.');
        }
        
        
        
        public function update_status(Request $request)
        {
            $request->validate([
                'id' => 'required|integer',
                'status' => 'required'
            ]);
        
            $updated = DB::table('ev_tbl_brands')
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
