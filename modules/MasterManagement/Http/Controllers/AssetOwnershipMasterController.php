<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; //updated by Mugesh.B
use Illuminate\Support\Facades\Auth; //updated by Logesh
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
        
        $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $model->status == 1 ? 'Active' : 'Inactive';

            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Asset Ownership Created',
                'long_description'  => "Asset ownership '{$model->name}' created (ID: {$model->id}). Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_ownership_master.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
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
        $old = $model->getAttributes();
        $model->update([
            'name' => $request->asset_owner_ship_name,
            'status' => $request->status,
            'updated_at'=> now() 
        ]);
        $new = $model->getAttributes();
        
        $changes = [];
        if (($old['name'] ?? null) != ($new['name'] ?? null)) {
            $changes[] = "Name: " . ($old['name'] ?? 'N/A') . " → " . ($new['name'] ?? 'N/A');
        }
        if ((string)($old['status'] ?? '') !== (string)($new['status'] ?? '')) {
            $oldStatusText = isset($old['status']) ? (($old['status'] == 1) ? 'Active' : 'Inactive') : 'N/A';
            $newStatusText = ($new['status'] == 1) ? 'Active' : 'Inactive';
            $changes[] = "Status: {$oldStatusText} → {$newStatusText}";
        }
        $changesText = empty($changes) ? 'No visible changes detected.' : implode('; ', $changes);

        $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Asset Ownership Updated',
                'long_description'  => "Asset ownership '{$model->name}' (ID: {$model->id}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_ownership_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
                
            ]);
        return response()->json(['success' => true, 'message' => 'Asset Ownership updated successfully.']);
  
        
    }
    
    
        public function status_update(Request $request)
    {
        
        
        $request->validate([
            'status' => 'required',
        ]);
    
        $record = AssetOwnershipMaster::find($request->id);
        $oldStatus = (int) $record->status;
        $newStatus = (int) $request->status;
        $record->status = $request->status;
        $record->save();
        
        $user = Auth::user();
                $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
                $oldText = $oldStatus == 1 ? 'Active' : 'Inactive';
                $newText = $newStatus == 1 ? 'Active' : 'Inactive';

                audit_log_after_commit([
                    'module_id'         => 1,
                    'short_description' => 'Asset Ownership Status Updated',
                    'long_description'  => "Asset ownership '{$record->name}' (ID: {$record->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'asset_ownership_master.status_update',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
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
        
         $selectedCount = is_array($selectedIds) ? count($selectedIds) : 0;
        $idsSample = $selectedCount > 0 ? implode(',', array_slice($selectedIds, 0, 5)) : '-';
        $more = $selectedCount > 5 ? ' (+' . ($selectedCount - 5) . ' more)' : '';

        $user = Auth::user();
        $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';

        $longDescription = sprintf(
            "Asset Ownership export initiated. Filters → Status: %s | From: %s | To: %s | Selected IDs: %s%s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $idsSample,
            $more
        );
         audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Asset Ownership Export Triggered',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'asset_ownership_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
                
            ]);
        
          return Excel::download(new AssetOwnershipMasterExport($status,$from_date,$to_date , $selectedIds), 'asset-ownership-master-' . date('d-m-Y') . '.xlsx');
       
    }

    
}
