<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MasterManagement\Entities\AssetOwnershipMaster; //updated by Mugesh.B
use App\Exports\AssetOwnershipMasterExport;//updated by Mugesh.B


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

class AssetOwnershipMasterController extends Controller
{



   public function asset_ownership_master_list(Request $request){
       
        $query = AssetOwnershipMaster::query();
    
        $status = $request->status ?? 'all';
        $from_date = $request->from_date ?? '';
        $to_date = $request->to_date ?? '';
    
        // Only apply filter if valid
        if (in_array($status, ['1', '0'])) {
            $query->where('status', $status);
        }
    
        if ($from_date) {
            $query->whereDate('created_at', '>=', $from_date);
        }
    
        if ($to_date) {
            $query->whereDate('created_at', '<=', $to_date);
        }
    
        $data = $query->orderBy('id', 'desc')->get();
        
       
        return view('mastermanagement::asset_ownership_master.asset_ownership_master_list' , compact('data' , 'status' , 'from_date' , 'to_date'));
    }
    
   

    public function store(Request $request)
    {
        
        
        // ✅ Validate input
        $validated = $request->validate([
            'asset_ownership_name' => 'required|string|max:255|unique:ev_tbl_asset_ownership_master,name',
            'status' => 'required', // adjust as per your allowed values
        ]);
    
        // ✅ Save to DB
        AssetOwnershipMaster::create([
            'name' => $validated['asset_ownership_name'],
            'status' => $validated['status'],
        ]);
        
    
        return response()->json([
            'success' => true,
            'message' => 'Asset ownership master created successfully.',
        ]);
    }
    
     public function get_data($id)
    {
        $data = AssetOwnershipMaster::find($id);
    
        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Not found']);
        }
    
        return response()->json(['success' => true, 'data' => $data]);
    }


  public function update(Request $request)
    {
     
             
        $request->validate([
            'asset_owner_ship_name' => 'required|string|max:255|unique:ev_tbl_asset_ownership_master,name,' . $request->id,
            'status' => 'required',
        ]);
    
        $model = AssetOwnershipMaster::find($request->id);
        $model->update([
            'name' => $request->asset_owner_ship_name,
            'status' => $request->status,
            'updated_at'=> now() 
        ]);
    
    
        return response()->json(['success' => true, 'message' => 'Asset Ownership updated successfully.']);
  
        
    }
    
    
        public function status_update(Request $request)
    {
        
        
        $request->validate([
            'status' => 'required',
        ]);
    
        $record = AssetOwnershipMaster::find($request->id);
        $record->status = $request->status;
        $record->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
       
    }
    
            public function export_asset_ownership_master(Request $request)
    {
       
        
        $status = $request->status;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
         $selectedIds = json_decode($request->query('selected_ids', '[]'), true);
        
        
          return Excel::download(new AssetOwnershipMasterExport($status,$from_date,$to_date , $selectedIds), 'asset-ownership-master-' . date('d-m-Y') . '.xlsx');
       
    }

    
}
