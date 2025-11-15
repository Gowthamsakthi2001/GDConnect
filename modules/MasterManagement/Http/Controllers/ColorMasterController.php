<?php

namespace Modules\MasterManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\EVState;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\City\Entities\City;
use Modules\MasterManagement\Entities\ColorMaster;
use Modules\MasterManagement\Entities\CustomerMaster;
use Modules\MasterManagement\Entities\CustomerPOCDetail;
use Modules\MasterManagement\Entities\CustomerOperationalHub;
use Modules\MasterManagement\Entities\BusinessConstitutionType;
use App\Exports\CustomerMasterExport;
use App\Exports\ColorMasterExport;
use App\Helpers\CustomHandler;
use Illuminate\Support\Facades\Auth;

class ColorMasterController extends Controller
{
    
    public function index(Request $request)
    {
        $query = ColorMaster::query();
    
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

        return view('mastermanagement::color_master.index' , compact('data' ,'from_date' ,'to_date'));
    }
    
   public function store(Request $request)
    {
        $validated = $request->validate([
            'color_name' => 'required|string|max:255|unique:ev_tbl_color_master,name',
            'status' => 'required', // adjust as per your allowed values
        ]);
    
        // ✅ Save to DB
        ColorMaster::create([
            'name' => $validated['color_name'],
            'status' => $validated['status'],
        ]);
        
        $user = Auth::user();
            $roleName = optional(\Modules\Role\Entities\Role::find($user->role))->name ?? 'Unknown';
            $statusText = $color->status == 1 ? 'Active' : 'Inactive';
            audit_log_after_commit([
                'module_id'         => 1, // adjust if ColorMaster has a different module id
                'short_description' => 'Color Created',
                'long_description'  => "Color '{$color->name}' created (ID: {$color->id}). Status: {$statusText}.",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'color_master.store',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'New color created successfully.',
        ]);
        
        
    }

        public function update_status(Request $request)
    {
        $request->validate([
            'status' => 'required',
        ]);
    
        $record = ColorMaster::find($request->id);
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
                    'short_description' => 'Color Status Updated',
                    'long_description'  => "Color '{$record->name}' (ID: {$record->id}) status changed: {$oldText} → {$newText}.",
                    'role'              => $roleName,
                    'user_id'           => $user->id ?? null,
                    'user_type'         => 'gdc_admin_dashboard',
                    'dashboard_type'    => 'web',
                    'page_name'         => 'color_master.update_status',
                    'ip_address'        => request()->ip(),
                    'user_device'       => request()->userAgent()
                ]);
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
        ]);
    }
    
    
        public function update(Request $request)
    {
     
        
        
        $request->validate([
            'color_name' => 'required|string|max:255|unique:ev_tbl_color_master,name,' . $request->id,
            'status' => 'required',
        ]);
    
        $model = ColorMaster::find($request->id);
        $old = $model->getAttributes();
        $model->update([
            'name' => $request->color_name,
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
                'short_description' => 'Color Updated',
                'long_description'  => "Color '{$model->name}' (ID: {$model->id}) updated. Changes: {$changesText}",
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'color_master.update',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
    
        return response()->json(['success' => true, 'message' => 'Color Master updated successfully.']);
    }
    
        public function export_color_master(Request $request)
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
            "Color Master export initiated. Filters → Status: %s | From: %s | To: %s | Selected IDs: %s%s",
            $status ?? '-',
            $from_date ?? '-',
            $to_date ?? '-',
            $idsSample,
            $more
        );
        
            audit_log_after_commit([
                'module_id'         => 1,
                'short_description' => 'Color Master Export Triggered',
                'long_description'  => $longDescription,
                'role'              => $roleName,
                'user_id'           => $user->id ?? null,
                'user_type'         => 'gdc_admin_dashboard',
                'dashboard_type'    => 'web',
                'page_name'         => 'color_master.export',
                'ip_address'        => request()->ip(),
                'user_device'       => request()->userAgent()
            ]);
        
          return Excel::download(new ColorMasterExport($status,$from_date,$to_date , $selectedIds), 'color-master-' . date('d-m-Y') . '.xlsx');
       
    }
    
}